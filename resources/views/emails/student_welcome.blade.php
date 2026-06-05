<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Welcome to {{ $tenant->coaching_name }}</title></head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:32px 16px;">
  <tr><td align="center">
    <table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.08);">

      <!-- Header -->
      <tr><td style="background:linear-gradient(135deg,#7c3aed,#5b21b6);padding:32px;text-align:center;">
        @if($tenant->logo)
        <img src="{{ Storage::url($tenant->logo) }}" alt="{{ $tenant->coaching_name }}" style="height:50px;margin-bottom:16px;background:white;padding:8px;border-radius:8px;">
        @endif
        <h1 style="margin:0;font-size:24px;font-weight:800;color:#fff;">🎉 Welcome {{ $student->name }}!</h1>
        <p style="margin:8px 0 0;font-size:14px;color:rgba(255,255,255,.8);">Your learning journey starts now</p>
      </td></tr>

      <!-- Body -->
      <tr><td style="padding:32px;">
        <p style="margin:0 0 16px;font-size:15px;color:#374151;">Hi <strong>{{ $student->name }}</strong>,</p>
        <p style="margin:0 0 24px;font-size:14px;color:#6b7280;line-height:1.7;">
          You have been successfully enrolled at <strong>{{ $tenant->coaching_name }}</strong>.
          Access your courses, exams, live classes, and study materials anytime, anywhere.
        </p>

        <!-- Login Details Card -->
        <table cellpadding="0" cellspacing="0" width="100%" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;margin-bottom:24px;">
          <tr><td style="padding:20px;">
            <p style="margin:0 0 12px;font-size:11px;font-weight:700;color:#7c3aed;text-transform:uppercase;letter-spacing:.8px;">Your Login Details</p>
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr><td style="padding:5px 0;font-size:13px;color:#6b7280;width:150px;">Portal URL</td><td style="padding:5px 0;font-size:13px;font-weight:700;color:#7c3aed;">{{ $tenant->subdomain }}.{{ config('app.central_domain') }}</td></tr>
              <tr><td style="padding:5px 0;font-size:13px;color:#6b7280;">Email</td><td style="padding:5px 0;font-size:13px;font-weight:700;color:#111827;">{{ $student->email }}</td></tr>
              <tr><td style="padding:5px 0;font-size:13px;color:#6b7280;">Password</td><td style="padding:5px 0;font-size:13px;font-weight:700;color:#111827;">{{ $password ?? 'As provided by admin' }}</td></tr>
            </table>
          </td></tr>
        </table>

        <!-- App Download Section -->
        <table cellpadding="0" cellspacing="0" width="100%" style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);border:1px solid #a7f3d0;border-radius:12px;margin-bottom:24px;">
          <tr><td style="padding:20px;">
            <p style="margin:0 0 12px;font-size:11px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:.8px;">Get the Mobile App</p>
            <p style="margin:0 0 16px;font-size:13px;color:#065f46;">
              Download our app for the best learning experience on your phone:
            </p>

            <!-- APK Download Button -->
            <a href="{{ $downloadUrl ?? 'https://' . $tenant->subdomain . '.' . config('app.central_domain') . '/downloads/' . $tenant->subdomain . '/student.apk' }}" style="display:inline-block;background:#10b981;color:#fff;padding:14px 24px;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px;margin-bottom:12px;">
              <span style="display:flex;align-items:center;gap:8px;">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.5511 0 .9993.4482.9993.9993.0001.5511-.4482.9997-.9993.9997m-11.046 0c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.5511 0 .9993.4482.9993.9993 0 .5511-.4482.9997-.9993.9997m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0225 3.503C15.5902 8.4794 13.8538 8.138 12 8.138c-1.8538 0-3.5902.3414-5.1368.9489L4.8407 5.5837a.416.416 0 00-.5676-.1521.416.416 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589.3432 18.6617h23.3136c0-4.0028-2.3457-7.475-5.7754-9.3403"/></svg>
                Download {{ $tenant->coaching_name }} Android App
              </span>
            </a>

            <p style="margin:8px 0 0;font-size:12px;color:#047857;">
              <strong>iPhone/iPad users:</strong> Visit your portal in Safari and tap "Share → Add to Home Screen"
            </p>
          </td></tr>
        </table>

        <!-- Login Button -->
        <a href="{{ $loginUrl ?? 'https://' . $tenant->subdomain . '.' . config('app.central_domain') . '/login' }}" style="display:block;width:100%;background:#7c3aed;color:#fff;padding:16px;border-radius:8px;text-align:center;text-decoration:none;font-weight:600;font-size:15px;margin-bottom:24px;">
          Login to Student Portal
        </a>

        <p style="margin:0 0 8px;font-size:14px;color:#374151;font-weight:700;">What you can do:</p>
        <ul style="margin:0 0 24px;padding-left:20px;color:#6b7280;font-size:13px;line-height:2;">
          <li>Access all your enrolled courses</li>
          <li>Watch live classes and recordings</li>
          <li>Attempt practice tests and exams</li>
          <li>Download study notes and PDFs</li>
          <li>Get notifications for upcoming classes</li>
        </ul>

        <p style="margin:0;font-size:13px;color:#9ca3af;">
          Questions? Contact {{ $tenant->coaching_name }} at {{ $tenant->email ?? 'support@' . $tenant->subdomain . '.' . config('app.central_domain') }}
        </p>
      </td></tr>

      <!-- Footer -->
      <tr><td style="background:#f9fafb;padding:16px 32px;text-align:center;border-top:1px solid #e5e7eb;">
        <p style="margin:0;font-size:12px;color:#9ca3af;">&copy; {{ date('Y') }} {{ $tenant->coaching_name }}. All rights reserved.</p>
        <p style="margin:8px 0 0;font-size:11px;color:#d1d5db;">Powered by BT Guru</p>
      </td></tr>

    </table>
  </td></tr>
</table>
</body>
</html>
