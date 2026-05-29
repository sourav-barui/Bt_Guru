<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use App\Models\SystemSetting;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->pluck('value', 'key');
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'mail_driver'       => 'required|in:smtp,sendmail,mailgun,ses,log',
            'mail_host'         => 'required_unless:mail_driver,log|nullable|string|max:255',
            'mail_port'         => 'required_unless:mail_driver,log|nullable|integer|min:1|max:65535',
            'mail_username'     => 'required_unless:mail_driver,log|nullable|string|max:255',
            'mail_password'     => 'nullable|string|max:255',
            'mail_encryption'   => 'nullable|in:tls,ssl,starttls,',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name'    => 'required|string|max:255',
        ], [
            'mail_host.required_unless'     => 'SMTP host is required.',
            'mail_port.required_unless'     => 'SMTP port is required.',
            'mail_username.required_unless' => 'SMTP username is required.',
        ]);

        $data = $request->only([
            'mail_driver', 'mail_host', 'mail_port', 'mail_username',
            'mail_encryption', 'mail_from_address', 'mail_from_name',
        ]);

        // Only overwrite password if a new one is provided
        if ($request->filled('mail_password')) {
            $data['mail_password'] = $request->mail_password;
        }

        SystemSetting::setMany($data);

        return back()->with('success', 'Email settings saved successfully.');
    }

    public function testMail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        if (!SystemSetting::isMailConfigured()) {
            return back()->with('error', 'Please save your SMTP settings before sending a test email.');
        }

        $cfg = SystemSetting::mailConfig();

        // Apply runtime config
        Config::set('mail.default', $cfg['driver']);
        Config::set('mail.mailers.smtp.host', $cfg['host']);
        Config::set('mail.mailers.smtp.port', $cfg['port']);
        Config::set('mail.mailers.smtp.username', $cfg['username']);
        Config::set('mail.mailers.smtp.password', $cfg['password']);
        Config::set('mail.mailers.smtp.encryption', $cfg['encryption'] ?: null);
        Config::set('mail.from.address', $cfg['from_address']);
        Config::set('mail.from.name', $cfg['from_name']);

        try {
            Mail::send([], [], function (Message $msg) use ($request, $cfg) {
                $msg->to($request->test_email)
                    ->from($cfg['from_address'], $cfg['from_name'])
                    ->subject('BT Guru – SMTP Test Email')
                    ->html(
                        '<div style="font-family:sans-serif;padding:24px;max-width:480px;">'
                        . '<h2 style="color:#7c3aed;">✓ SMTP Test Successful</h2>'
                        . '<p>This is a test email sent from <strong>BT Guru Super Admin</strong> to verify your email configuration.</p>'
                        . '<p style="color:#6b7280;font-size:13px;">Sent via: ' . $cfg['host'] . ':' . $cfg['port'] . ' (' . $cfg['encryption'] . ')</p>'
                        . '<hr style="border:none;border-top:1px solid #e5e7eb;margin:16px 0;">'
                        . '<p style="font-size:12px;color:#9ca3af;">&copy; ' . date('Y') . ' BT Guru</p>'
                        . '</div>'
                    );
            });

            return back()->with('success', 'Test email sent successfully to ' . $request->test_email);
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Raw SMTP connection + auth diagnostic.
     * Returns JSON with step-by-step results so the admin knows exactly what failed.
     */
    public function diagnoseMail(Request $request)
    {
        $request->validate([
            'host'       => 'required|string',
            'port'       => 'required|integer',
            'username'   => 'required|string',
            'password'   => 'nullable|string',
            'encryption' => 'nullable|string',
        ]);

        $host       = trim($request->host);
        $port       = (int) $request->port;
        $username   = trim($request->username);
        $password   = $request->password ?? SystemSetting::get('mail_password', '');
        $encryption = strtolower(trim($request->encryption ?? ''));
        $steps      = [];
        $timeout    = 10;

        // ── Step 1: DNS resolution ─────────────────────────────────
        $ip = gethostbyname($host);
        if ($ip === $host && !filter_var($host, FILTER_VALIDATE_IP)) {
            return response()->json([
                'ok'    => false,
                'steps' => array_merge($steps, [['label' => 'DNS Lookup', 'ok' => false, 'detail' => "Cannot resolve hostname \"$host\". Check the SMTP host spelling."]]),
            ]);
        }
        $steps[] = ['label' => 'DNS Lookup', 'ok' => true, 'detail' => "$host → $ip"];

        // ── Step 2: TCP connection ─────────────────────────────────
        $errno = 0; $errstr = '';
        $scheme = ($encryption === 'ssl') ? 'ssl://' : '';
        $sock = @fsockopen($scheme . $host, $port, $errno, $errstr, $timeout);

        if (!$sock) {
            $hint = match(true) {
                $port === 465  => 'Port 465 requires SSL encryption. Make sure "SSL" is selected.',
                $port === 587  => 'Port 587 uses TLS/STARTTLS. Try switching encryption to TLS.',
                $port === 25   => 'Port 25 is often blocked by ISPs and hosting. Try port 587 or 465.',
                default        => 'The port may be blocked by a firewall or the host is unreachable.',
            };
            return response()->json([
                'ok'    => false,
                'steps' => array_merge($steps, [['label' => "TCP Connect ($host:$port)", 'ok' => false, 'detail' => "Connection failed: $errstr ($errno). $hint"]]),
            ]);
        }
        stream_set_timeout($sock, $timeout);
        $steps[] = ['label' => "TCP Connect ($host:$port)", 'ok' => true, 'detail' => 'Connected successfully'];

        // ── Step 3: Read server banner ─────────────────────────────
        $banner = fgets($sock, 512);
        if (!$banner || !str_starts_with(trim($banner), '220')) {
            fclose($sock);
            return response()->json([
                'ok'    => false,
                'steps' => array_merge($steps, [['label' => 'Server Banner', 'ok' => false, 'detail' => 'Unexpected server response: ' . trim((string)$banner)]]),
            ]);
        }
        $steps[] = ['label' => 'Server Banner', 'ok' => true, 'detail' => trim($banner)];

        // ── Step 4: EHLO ───────────────────────────────────────────
        fwrite($sock, "EHLO btguru.test\r\n");
        $ehlo = '';
        while ($line = fgets($sock, 512)) {
            $ehlo .= $line;
            if (substr($line, 3, 1) === ' ') break;
        }
        $supportsAuth = stripos($ehlo, 'AUTH') !== false;
        $supportsStartTls = stripos($ehlo, 'STARTTLS') !== false;
        $steps[] = ['label' => 'EHLO Handshake', 'ok' => true, 'detail' => 'AUTH advertised: ' . ($supportsAuth ? 'Yes' : 'No') . ' | STARTTLS: ' . ($supportsStartTls ? 'Yes' : 'No')];

        // ── Step 5: STARTTLS upgrade if needed ────────────────────
        if (in_array($encryption, ['tls', 'starttls']) && $supportsStartTls) {
            fwrite($sock, "STARTTLS\r\n");
            $stlsResp = fgets($sock, 512);
            if (!str_starts_with(trim((string)$stlsResp), '220')) {
                fclose($sock);
                return response()->json([
                    'ok'    => false,
                    'steps' => array_merge($steps, [['label' => 'STARTTLS Upgrade', 'ok' => false, 'detail' => 'Server rejected STARTTLS: ' . trim((string)$stlsResp)]]),
                ]);
            }
            // Upgrade to TLS stream
            stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            fwrite($sock, "EHLO btguru.test\r\n");
            while ($line = fgets($sock, 512)) {
                if (substr($line, 3, 1) === ' ') break;
            }
            $steps[] = ['label' => 'STARTTLS Upgrade', 'ok' => true, 'detail' => 'TLS layer established'];
        }

        // ── Step 6: AUTH LOGIN ─────────────────────────────────────
        if (empty($password)) {
            fclose($sock);
            return response()->json([
                'ok'    => false,
                'steps' => array_merge($steps, [['label' => 'AUTH LOGIN', 'ok' => false, 'detail' => 'No password saved. Please enter and save the SMTP password first.']]),
            ]);
        }

        fwrite($sock, "AUTH LOGIN\r\n");
        $r1 = trim((string)fgets($sock, 512));
        if (!str_starts_with($r1, '334')) {
            fclose($sock);
            return response()->json([
                'ok'    => false,
                'steps' => array_merge($steps, [['label' => 'AUTH LOGIN', 'ok' => false, 'detail' => "Server refused AUTH LOGIN: $r1. The server may not support LOGIN authenticator."]]),
            ]);
        }

        fwrite($sock, base64_encode($username) . "\r\n");
        $r2 = trim((string)fgets($sock, 512));

        fwrite($sock, base64_encode($password) . "\r\n");
        $r3 = trim((string)fgets($sock, 512));
        fclose($sock);

        if (str_starts_with($r3, '235')) {
            $steps[] = ['label' => 'AUTH LOGIN', 'ok' => true, 'detail' => 'Authentication successful! Credentials are correct.'];
            return response()->json(['ok' => true, 'steps' => $steps]);
        }

        // Parse the 535 reason
        $hint = '';
        if (str_contains($r3, '535')) {
            $hint = match(true) {
                str_contains(strtolower($r3), 'authentication failed') =>
                    'Wrong password. For Gmail/Workspace use an App Password (not your login password). For cPanel check the exact mailbox password.',
                str_contains(strtolower($r3), 'invalid credentials') =>
                    'Username or password is incorrect.',
                default => 'The server rejected the credentials. Double-check username and password.',
            };
        }

        $steps[] = ['label' => 'AUTH LOGIN', 'ok' => false, 'detail' => "$r3. $hint"];
        return response()->json(['ok' => false, 'steps' => $steps]);
    }
}
