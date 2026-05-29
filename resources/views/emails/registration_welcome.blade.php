<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Welcome to BT Guru</title></head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:32px 16px;">
  <tr><td align="center">
    <table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.08);">

      <!-- Header -->
      <tr><td style="background:linear-gradient(135deg,#7c3aed,#5b21b6);padding:32px;text-align:center;">
        <p style="margin:0 0 4px;font-size:12px;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:1.5px;font-weight:700;">BT Guru</p>
        <h1 style="margin:0 0 4px;font-size:26px;font-weight:800;color:#fff;">🎉 Welcome Aboard!</h1>
        <p style="margin:0;font-size:14px;color:rgba(255,255,255,.8);">{{ $tenant->coaching_name }} is now registered</p>
      </td></tr>

      <!-- Body -->
      <tr><td style="padding:32px;">
        <p style="margin:0 0 16px;font-size:15px;color:#374151;">Hi <strong>{{ $admin->name }}</strong>,</p>
        <p style="margin:0 0 24px;font-size:14px;color:#6b7280;line-height:1.7;">
          Congratulations! <strong>{{ $tenant->coaching_name }}</strong> has been successfully registered on <strong>BT Guru</strong>.
          Your account is currently <strong>under review</strong> by our team. You will be notified once it is activated — usually within 24 hours.
        </p>

        <!-- Details Card -->
        <table cellpadding="0" cellspacing="0" width="100%" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;margin-bottom:24px;">
          <tr><td style="padding:20px;">
            <p style="margin:0 0 12px;font-size:11px;font-weight:700;color:#7c3aed;text-transform:uppercase;letter-spacing:.8px;">Your Registration Details</p>
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr><td style="padding:5px 0;font-size:13px;color:#6b7280;width:150px;">Centre Name</td><td style="padding:5px 0;font-size:13px;font-weight:700;color:#111827;">{{ $tenant->coaching_name }}</td></tr>
              <tr><td style="padding:5px 0;font-size:13px;color:#6b7280;">Your Portal URL</td><td style="padding:5px 0;font-size:13px;font-weight:700;color:#7c3aed;">{{ $tenant->subdomain }}.{{ config('app.central_domain') }}</td></tr>
              <tr><td style="padding:5px 0;font-size:13px;color:#6b7280;">Admin Email</td><td style="padding:5px 0;font-size:13px;font-weight:700;color:#111827;">{{ $admin->email }}</td></tr>
              <tr><td style="padding:5px 0;font-size:13px;color:#6b7280;">Status</td><td style="padding:5px 0;"><span style="background:#fef3c7;color:#92400e;font-size:12px;font-weight:700;padding:3px 10px;border-radius:20px;">Pending Review</span></td></tr>
            </table>
          </td></tr>
        </table>

        <p style="margin:0 0 8px;font-size:14px;color:#374151;font-weight:700;">What happens next?</p>
        <ol style="margin:0 0 24px;padding-left:20px;color:#6b7280;font-size:13px;line-height:2;">
          <li>Our team reviews your registration (24–48 hours)</li>
          <li>Your account gets activated</li>
          <li>You receive a confirmation email with login link</li>
          <li>Start adding courses, teachers and students!</li>
        </ol>

        <p style="margin:0;font-size:13px;color:#9ca3af;">
          Questions? Reply to this email or contact <a href="mailto:support@btguru.in" style="color:#7c3aed;font-weight:600;">support@btguru.in</a>
        </p>
      </td></tr>

      <!-- Footer -->
      <tr><td style="background:#f9fafb;padding:16px 32px;text-align:center;border-top:1px solid #e5e7eb;">
        <p style="margin:0;font-size:12px;color:#9ca3af;">&copy; {{ date('Y') }} BT Guru. All rights reserved.</p>
      </td></tr>

    </table>
  </td></tr>
</table>
</body>
</html>
