<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Models\Tenant;
use App\Models\User;
use App\Models\SystemSetting;

class LoginController extends Controller
{
    public function showAdminLogin()
    {
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if user is super admin
            if (!$user->isSuperAdmin()) {
                Auth::logout();
                return back()->with('error', 'Unauthorized access. Super Admin only.');
            }

            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        return back()->with('error', 'Invalid credentials.')->withInput();
    }

    public function showTenantLogin()
    {
        $currentTenant = app('current_tenant');
        return view('auth.tenant-login', compact('currentTenant'));
    }

    public function tenantLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $currentTenant = app('current_tenant');
        
        if (!$currentTenant) {
            return back()->with('error', 'Tenant not found.')->withInput();
        }

        // Find user by email and tenant_id
        $user = \App\Models\User::where('email', $request->email)
            ->where('tenant_id', $currentTenant->id)
            ->first();

        if (!$user) {
            return back()->with('error', 'Invalid credentials.')->withInput();
        }

        // Attempt login with user ID and password
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'tenant_id' => $currentTenant->id])) {
            $user = Auth::user();

            if ($user->isStudent()) {
                Auth::logout();
                return back()->with('error', 'Students must use the student login portal.');
            }

            $request->session()->regenerate();

            if ($user->isTenantAdmin()) {
                return redirect()->intended('/dashboard');
            } elseif ($user->isTeacher()) {
                return redirect()->intended('/teacher/dashboard');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->with('error', 'Invalid credentials.')->withInput();
    }

    public function showStudentLogin()
    {
        $currentTenant = app('current_tenant');
        return view('auth.student-login', compact('currentTenant'));
    }

    public function studentLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $currentTenant = app('current_tenant');
        
        if (!$currentTenant) {
            return back()->with('error', 'Tenant not found.')->withInput();
        }

        $user = \App\Models\User::where('email', $request->email)
            ->where('tenant_id', $currentTenant->id)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Invalid credentials.')->withInput();
        }

        if (!$user->isStudent()) {
            return back()->with('error', 'Access denied. Students only.');
        }

        // Check if user already has an active session
        if ($user->hasActiveSession()) {
            // Store user info in session for the conflict resolution page
            session(['login_conflict_user' => $user->id]);
            return redirect()->route('student.login.conflict');
        }

        // Clear any old sessions and login
        $user->logoutFromAllDevices();
        Auth::login($user);

        $request->session()->regenerate();

        // Update session info using direct DB query (bypass any model caching)
        \DB::table('users')
            ->where('id', $user->id)
            ->update([
                'current_session_id' => session()->getId(),
                'last_login_ip' => $request->ip(),
                'last_login_at' => now(),
            ]);

        return redirect()->intended('/student/dashboard');
    }

    public function showSessionConflict()
    {
        $userId = session('login_conflict_user');
        if (!$userId) {
            return redirect()->route('student.login');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('student.login');
        }

        return view('auth.session-conflict', compact('user'));
    }

    public function sendPasswordResetLink(Request $request)
    {
        $userId = session('login_conflict_user');
        if (!$userId) {
            return redirect()->route('student.login');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('student.login');
        }

        // Generate a unique token
        $token = Str::random(64);
        
        // Store token in password_resets table
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Send email with reset link
        $resetUrl = route('student.password.reset', ['token' => $token, 'email' => $user->email]);
        
        // For now, just show the link (in production, send actual email)
        return back()->with('success', 'Password reset link has been sent to your registered email: ' . $user->email);
    }

    public function showResetPasswordForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $currentTenant = app('current_tenant');
        
        // Find user
        $user = User::where('email', $request->email)
            ->where('tenant_id', $currentTenant?->id)
            ->first();

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        // Verify token
        $record = \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->with('error', 'Invalid or expired token.');
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($record->created_at) > 60) {
            return back()->with('error', 'Token has expired. Please request a new one.');
        }

        // Update password and logout from all devices
        $user->password = Hash::make($request->password);
        $user->invalidateOldSessions();
        $user->save();

        // Delete the token
        \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('student.login')
            ->with('success', 'Password changed successfully. Please login with your new password. All other devices have been logged out.');
    }

    // ==================== OTP BASED FORGOT PASSWORD ====================

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendPasswordResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $currentTenant = app('current_tenant');

        if (!$currentTenant) {
            return back()->with('error', 'Tenant not found.');
        }

        // Find student in this tenant
        $user = User::where('email', $request->email)
            ->where('tenant_id', $currentTenant->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->first();

        if (!$user) {
            return back()->with('error', 'No student found with this email address.');
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in session (expires in 10 minutes)
        session([
            'password_reset_otp' => $otp,
            'password_reset_email' => $user->email,
            'password_reset_user_id' => $user->id,
            'password_reset_otp_expires' => now()->addMinutes(10),
        ]);

        // Send OTP via email using tenant configuration
        try {
            $this->sendOtpEmail($currentTenant, $user, $otp);
            return redirect()->route('student.password.otp.verify')
                ->with('success', 'OTP has been sent to your email address.');
        } catch (\Throwable $e) {
            \Log::error('Failed to send OTP email: ' . $e->getMessage());
            return back()->with('error', 'Failed to send OTP. Please try again later.');
        }
    }

    private function sendOtpEmail(Tenant $tenant, User $user, string $otp): void
    {
        $settings = $tenant->settings ?? [];

        // Check if tenant has email configured
        if (empty($settings['mail_host']) || empty($settings['mail_username'])) {
            throw new \Exception('Email not configured for this tenant');
        }

        // Configure mail with tenant settings
        Config::set('mail.default', $settings['mail_driver'] ?? 'smtp');
        Config::set('mail.mailers.smtp.host', $settings['mail_host']);
        Config::set('mail.mailers.smtp.port', $settings['mail_port'] ?? 587);
        Config::set('mail.mailers.smtp.username', $settings['mail_username']);
        Config::set('mail.mailers.smtp.password', $settings['mail_password'] ?? '');
        Config::set('mail.mailers.smtp.encryption', $settings['mail_encryption'] ?? 'tls');
        Config::set('mail.from.address', $settings['mail_from_address'] ?? $settings['mail_username']);
        Config::set('mail.from.name', $settings['mail_from_name'] ?? $tenant->coaching_name);

        Mail::send([], [], function ($message) use ($user, $otp, $tenant, $settings) {
            $fromAddress = $settings['mail_from_address'] ?? $settings['mail_username'];
            $fromName = $settings['mail_from_name'] ?? $tenant->coaching_name;

            $message->to($user->email, $user->name)
                ->from($fromAddress, $fromName)
                ->subject('Password Reset OTP - ' . $tenant->coaching_name)
                ->html(view('emails.otp-password-reset', [
                    'user' => $user,
                    'otp' => $otp,
                    'tenant' => $tenant,
                ])->render());
        });
    }

    public function showVerifyOtpForm()
    {
        // Check if OTP session exists
        if (!session('password_reset_otp')) {
            return redirect()->route('student.password.request')
                ->with('error', 'Please request a new OTP.');
        }

        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $sessionOtp = session('password_reset_otp');
        $expiresAt = session('password_reset_otp_expires');

        if (!$sessionOtp) {
            return redirect()->route('student.password.request')
                ->with('error', 'OTP expired. Please request a new one.');
        }

        if (now()->greaterThan($expiresAt)) {
            session()->forget(['password_reset_otp', 'password_reset_email', 'password_reset_user_id', 'password_reset_otp_expires']);
            return redirect()->route('student.password.request')
                ->with('error', 'OTP has expired. Please request a new one.');
        }

        if ($request->otp !== $sessionOtp) {
            return back()->with('error', 'Invalid OTP. Please try again.');
        }

        // OTP verified, allow password reset
        session(['password_reset_verified' => true]);

        return redirect()->route('student.password.reset.otp');
    }

    public function showResetPasswordFormOtp()
    {
        // Check if OTP was verified
        if (!session('password_reset_verified')) {
            return redirect()->route('student.password.request')
                ->with('error', 'Please verify OTP first.');
        }

        return view('auth.reset-password-otp');
    }

    public function resetPasswordOtp(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!session('password_reset_verified')) {
            return redirect()->route('student.password.request')
                ->with('error', 'Please verify OTP first.');
        }

        $userId = session('password_reset_user_id');
        $email = session('password_reset_email');

        $user = User::find($userId);

        if (!$user || $user->email !== $email) {
            return redirect()->route('student.password.request')
                ->with('error', 'Invalid request. Please start again.');
        }

        // Update password and logout from all devices
        $user->password = Hash::make($request->password);
        $user->invalidateOldSessions();
        $user->save();

        // Clear all password reset session data
        session()->forget([
            'password_reset_otp',
            'password_reset_email',
            'password_reset_user_id',
            'password_reset_otp_expires',
            'password_reset_verified',
        ]);

        return redirect()->route('student.login')
            ->with('success', 'Password changed successfully. Please login with your new password.');
    }

    // ==================== TENANT FORGOT PASSWORD (Uses Superadmin Email Config) ====================

    public function showTenantForgotPasswordForm()
    {
        return view('auth.tenant-forgot-password');
    }

    public function sendTenantPasswordResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $currentTenant = app('current_tenant');

        if (!$currentTenant) {
            return back()->with('error', 'Tenant not found.');
        }

        // Find tenant admin or teacher in this tenant
        $user = User::where('email', $request->email)
            ->where('tenant_id', $currentTenant->id)
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['tenant_admin', 'teacher']))
            ->first();

        if (!$user) {
            return back()->with('error', 'No account found with this email address.');
        }

        // Check if Superadmin email is configured
        if (!SystemSetting::isMailConfigured()) {
            return back()->with('error', 'Email service is not configured. Please contact support.');
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in session (expires in 10 minutes)
        session([
            'tenant_password_reset_otp' => $otp,
            'tenant_password_reset_email' => $user->email,
            'tenant_password_reset_user_id' => $user->id,
            'tenant_password_reset_otp_expires' => now()->addMinutes(10),
        ]);

        // Send OTP via email using Superadmin configuration
        try {
            $this->sendTenantOtpEmail($currentTenant, $user, $otp);
            return redirect()->route('tenant.password.otp.verify')
                ->with('success', 'OTP has been sent to your email address.');
        } catch (\Throwable $e) {
            \Log::error('Failed to send tenant OTP email: ' . $e->getMessage());
            return back()->with('error', 'Failed to send OTP. Please try again later.');
        }
    }

    private function sendTenantOtpEmail(Tenant $tenant, User $user, string $otp): void
    {
        // Use Superadmin email configuration from SystemSetting
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

        Mail::send([], [], function ($message) use ($user, $otp, $tenant, $cfg) {
            $message->to($user->email, $user->name)
                ->from($cfg['from_address'], $cfg['from_name'])
                ->subject('Password Reset OTP - ' . $tenant->coaching_name)
                ->html(view('emails.tenant-otp-password-reset', [
                    'user' => $user,
                    'otp' => $otp,
                    'tenant' => $tenant,
                ])->render());
        });
    }

    public function showTenantVerifyOtpForm()
    {
        // Check if OTP session exists
        if (!session('tenant_password_reset_otp')) {
            return redirect()->route('tenant.password.request')
                ->with('error', 'Please request a new OTP.');
        }

        return view('auth.tenant-verify-otp');
    }

    public function verifyTenantOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $sessionOtp = session('tenant_password_reset_otp');
        $expiresAt = session('tenant_password_reset_otp_expires');

        if (!$sessionOtp) {
            return redirect()->route('tenant.password.request')
                ->with('error', 'OTP expired. Please request a new one.');
        }

        if (now()->greaterThan($expiresAt)) {
            session()->forget(['tenant_password_reset_otp', 'tenant_password_reset_email', 'tenant_password_reset_user_id', 'tenant_password_reset_otp_expires']);
            return redirect()->route('tenant.password.request')
                ->with('error', 'OTP has expired. Please request a new one.');
        }

        if ($request->otp !== $sessionOtp) {
            return back()->with('error', 'Invalid OTP. Please try again.');
        }

        // OTP verified, allow password reset
        session(['tenant_password_reset_verified' => true]);

        return redirect()->route('tenant.password.reset.otp');
    }

    public function showTenantResetPasswordFormOtp()
    {
        // Check if OTP was verified
        if (!session('tenant_password_reset_verified')) {
            return redirect()->route('tenant.password.request')
                ->with('error', 'Please verify OTP first.');
        }

        return view('auth.tenant-reset-password-otp');
    }

    public function resetTenantPasswordOtp(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!session('tenant_password_reset_verified')) {
            return redirect()->route('tenant.password.request')
                ->with('error', 'Please verify OTP first.');
        }

        $userId = session('tenant_password_reset_user_id');
        $email = session('tenant_password_reset_email');

        $user = User::find($userId);

        if (!$user || $user->email !== $email) {
            return redirect()->route('tenant.password.request')
                ->with('error', 'Invalid request. Please start again.');
        }

        // Update password and logout from all devices
        $user->password = Hash::make($request->password);
        $user->invalidateOldSessions();
        $user->save();

        // Clear all password reset session data
        session()->forget([
            'tenant_password_reset_otp',
            'tenant_password_reset_email',
            'tenant_password_reset_user_id',
            'tenant_password_reset_otp_expires',
            'tenant_password_reset_verified',
        ]);

        return redirect()->route('tenant.login')
            ->with('success', 'Password changed successfully. Please login with your new password.');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Clear current session tracking
        if ($user) {
            $user->update(['current_session_id' => null]);
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
