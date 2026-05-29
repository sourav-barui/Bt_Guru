<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .otp-box {
            background-color: #f0fdf4;
            border: 2px dashed #10b981;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #059669;
            letter-spacing: 8px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $tenant->coaching_name }}</h1>
        </div>

        <div class="content">
            <h2 style="color: #1f2937; margin-top: 0;">Password Reset Request</h2>

            <p style="color: #4b5563;">Hello {{ $user->name }},</p>

            <p style="color: #4b5563;">
                You have requested to reset your password. Use the following OTP code to verify your identity:
            </p>

            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
                <p style="color: #6b7280; margin-top: 10px; font-size: 14px;">
                    This code will expire in 10 minutes
                </p>
            </div>

            <p style="color: #4b5563;">
                If you didn't request this password reset, please ignore this email or contact your administrator.
            </p>

            <p style="color: #6b7280; font-size: 14px; margin-top: 30px;">
                Best regards,<br>
                <strong>{{ $tenant->coaching_name }} Team</strong>
            </p>
        </div>

        <div class="footer">
            <p>Powered by BT Guru - Complete Coaching Management Solution</p>
            <p style="margin-top: 10px;">&copy; {{ date('Y') }} {{ $tenant->coaching_name }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
