<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to GymGlow</title>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f9fafb;
            color: #1f2937;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #f3f4f6;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            padding: 40px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.025em;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
            font-weight: 500;
        }
        .content {
            padding: 40px;
            line-height: 1.6;
        }
        .welcome-box {
            background-color: #f5f3ff;
            border: 1px solid #e0d7ff;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 30px;
            text-align: center;
        }
        .welcome-title {
            font-size: 18px;
            font-weight: 700;
            color: #7c3aed;
            margin-bottom: 8px;
        }
        .welcome-text {
            font-size: 14px;
            color: #4b5563;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            padding: 0 40px 40px;
        }
        .btn {
            display: inline-block;
            background: #7c3aed;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            text-align: center;
            margin: 10px auto 30px;
            box-shadow: 0 10px 15px -3px rgba(124, 58, 237, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to GymGlow!</h1>
            <p>Your fitness and wellness journey starts here</p>
        </div>
        
        <div class="content">
            <p>Hi {{ $user->name }},</p>
            <p>Thank you for joining <strong>GymGlow</strong>, the premium system for fitness and wellness. We are thrilled to have you as a member!</p>
            
            <div class="welcome-box">
                <div class="welcome-title">Your Member Account is Active</div>
                <p class="welcome-text">You can now book fitness classes, choose your trainers, and track your wellness journey from your dashboard.</p>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ url('/dashboard') }}" class="btn">Go to Dashboard</a>
            </div>

            <p style="font-size: 13px; color: #6b7280; text-align: center; margin-top: 20px;">
                If you have any questions or need assistance, feel free to contact your gym admin.
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} GymGlow. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
