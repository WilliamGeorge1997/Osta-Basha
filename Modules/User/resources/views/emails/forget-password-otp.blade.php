<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background-color: #2c3e50;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .email-body {
            padding: 30px 20px;
        }

        .otp-box {
            background-color: #f8f9fa;
            border: 2px dashed #2c3e50;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }

        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            letter-spacing: 5px;
            font-family: 'Courier New', monospace;
        }

        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }

        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #2c3e50;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }

        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .text-white {
            color: #ffffff;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1 class="text-white">Password Reset Request</h1>
        </div>

        <div class="email-body">
            <h2>Hello {{ $user->first_name ?? 'User' }}!</h2>

            <p>You are receiving this email because we received a password reset request for your account.</p>

            <div class="otp-box">
                <p style="margin: 0 0 10px 0; color: #666;">Your OTP verification code is:</p>
                <div class="otp-code">{{ $otp }}</div>
            </div>

            <p>Please use this code to reset your password</p>
        </div>

        <div class="email-footer">
            <p>This is an automated message, please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
