<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Email Verification OTP</title></head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:32px 16px;">
  <tr><td align="center">
    <table width="520" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.08);">

      <!-- Header -->
      <tr><td style="background:linear-gradient(135deg,#7c3aed,#5b21b6);padding:28px 32px;text-align:center;">
        <p style="margin:0;font-size:12px;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:1.5px;font-weight:700;">BT Guru</p>
        <h1 style="margin:8px 0 0;font-size:22px;font-weight:800;color:#fff;">Verify Your Email</h1>
      </td></tr>

      <!-- Body -->
      <tr><td style="padding:32px;">
        <p style="margin:0 0 6px;font-size:15px;color:#374151;">Hi <strong>{{ $name }}</strong>,</p>
        <p style="margin:0 0 24px;font-size:14px;color:#6b7280;line-height:1.6;">
          You're registering <strong>{{ $coachingName }}</strong> on BT Guru.<br>
          Use the OTP below to verify your email address.
        </p>

        <!-- OTP Box -->
        <table cellpadding="0" cellspacing="0" style="margin:0 auto 28px;border:2px solid #e9d5ff;border-radius:16px;background:#faf5ff;">
          <tr><td style="padding:20px 40px;text-align:center;">
            <p style="margin:0 0 4px;font-size:11px;font-weight:700;color:#7c3aed;text-transform:uppercase;letter-spacing:1.5px;">Your OTP</p>
            <p style="margin:0;font-size:42px;font-weight:900;color:#5b21b6;letter-spacing:12px;">{{ $otp }}</p>
          </td></tr>
        </table>

        <p style="margin:0 0 8px;font-size:13px;color:#6b7280;text-align:center;">
          ⏰ This OTP is valid for <strong>15 minutes</strong>.
        </p>
        <p style="margin:0;font-size:13px;color:#9ca3af;text-align:center;">
          If you didn't request this, please ignore this email.
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
