<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>GymGlow Admin Verification Code</title>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 40px 20px;
            color: #1f2937;
        }
        .container {
            max-width: 560px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #f3f4f6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-box {
            display: inline-block;
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            padding: 12px;
            border-radius: 16px;
            margin-bottom: 12px;
        }
        .logo-box svg {
            width: 32px;
            height: 32px;
            color: #ffffff;
            display: block;
        }
        .title {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
            margin: 0;
            letter-spacing: -0.02em;
        }
        .text {
            font-size: 15px;
            line-height: 1.6;
            color: #4b5563;
            margin-bottom: 24px;
            text-align: center;
        }
        .otp-container {
            text-align: center;
            margin: 32px 0;
        }
        .otp-code {
            display: inline-block;
            background-color: #f5f3ff;
            color: #7c3aed;
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 0.15em;
            padding: 16px 36px;
            border-radius: 20px;
            border: 2px dashed #c084fc;
        }
        .warning-text {
            font-size: 13px;
            color: #9ca3af;
            text-align: center;
            margin-top: 30px;
            border-top: 1px solid #f3f4f6;
            padding-top: 20px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-box">
                <!-- Inline SVG of lightning bolt -->
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="width: 32px; height: 32px; color: white;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"></path>
                </svg>
            </div>
            <h1 class="title">Two-Factor Authentication</h1>
        </div>

        <p class="text">
            A sign-in attempt was detected for your administrator account on <strong>GymGlow</strong>. Please enter the verification code below to complete your login.
        </p>

        <div class="otp-container">
            <span class="otp-code">{{ $otp }}</span>
        </div>

        <p class="text" style="font-size: 13px; color: #6b7280;">
            This security code was requested just now and will expire in <strong>5 minutes</strong> (OTP expires at {{ now()->addMinutes(5)->format('H:i') }}).
        </p>

        <div class="warning-text">
            If you did not make this request, please ignore this email or change your account password immediately to protect your account.
        </div>
    </div>
</body>
</html>
