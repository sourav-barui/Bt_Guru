<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:32px 16px;">
  <tr>
    <td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.08);">

        <!-- Header -->
        <tr>
          <td style="background:linear-gradient(135deg,#7c3aed 0%,#5b21b6 100%);padding:28px 32px;text-align:center;">
            <p style="margin:0;font-size:13px;color:rgba(255,255,255,0.8);letter-spacing:1px;text-transform:uppercase;font-weight:600;">{{ $coachingName }}</p>
            <h1 style="margin:8px 0 0;font-size:22px;font-weight:700;color:#fff;">{{ $title }}</h1>
          </td>
        </tr>

        <!-- Body -->
        <tr>
          <td style="padding:32px;">
            <p style="margin:0 0 8px;font-size:15px;color:#374151;">Hi <strong>{{ $user->name }}</strong>,</p>
            @if($body)
            <p style="margin:0 0 24px;font-size:15px;color:#6b7280;line-height:1.6;">{{ $body }}</p>
            @endif

            @if($url)
            <table cellpadding="0" cellspacing="0" style="margin:0 auto 24px;">
              <tr>
                <td style="background:linear-gradient(135deg,#7c3aed,#5b21b6);border-radius:10px;">
                  <a href="{{ $url }}" style="display:block;padding:14px 32px;font-size:14px;font-weight:600;color:#fff;text-decoration:none;">View Details &rarr;</a>
                </td>
              </tr>
            </table>
            @endif

            <p style="margin:0;font-size:13px;color:#9ca3af;">If you have any questions, reply to this email or contact your coaching centre.</p>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background:#f9fafb;padding:16px 32px;text-align:center;border-top:1px solid #e5e7eb;">
            <p style="margin:0;font-size:12px;color:#9ca3af;">&copy; {{ date('Y') }} {{ $coachingName }}. All rights reserved.</p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
