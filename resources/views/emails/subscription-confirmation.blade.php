<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Subscription Activated</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; margin: 0; padding: 40px 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; }
        .header { background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%); padding: 40px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 800; }
        .content { padding: 40px; }
        .details-box { background-color: #fff5f7; border: 1px solid #fce7f3; border-radius: 16px; padding: 24px; margin-bottom: 30px; }
        .details-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; }
        .details-row:last-child { margin-bottom: 0; padding-top: 12px; border-top: 1px dashed #fce7f3; }
        .label { font-weight: 700; color: #64748b; text-transform: uppercase; font-size: 11px; }
        .value { font-weight: 600; color: #0f172a; }
        .btn { display: inline-block; background: #ec4899; color: #ffffff !important; text-decoration: none; padding: 14px 30px; border-radius: 12px; font-weight: 700; font-size: 14px; text-align: center; box-shadow: 0 10px 15px -3px rgba(236,72,153,0.2); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Subscription Activated!</h1>
            <p>Welcome to the GlowGym Platform</p>
        </div>
        <div class="content">
            <p>Hi {{ $user->name }},</p>
            <p>Your subscription is active and your Gym Enterprise has been successfully provisioned. Here are your account details:</p>
            
            <div class="details-box">
                <div class="details-row"><span class="label">Gym / Studio</span><span class="value">{{ $gym->name }}</span></div>
                <div class="details-row"><span class="label">Plan Tier</span><span class="value">{{ $gym->subscription_plan }}</span></div>
                <div class="details-row"><span class="label">Expiry Date</span><span class="value">{{ \Carbon\Carbon::parse($gym->subscription_expires_at)->format('F d, Y') }}</span></div>
                <div class="details-row"><span class="label">Transaction ID</span><span class="value">{{ $gym->subscription_transaction_id }}</span></div>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/dashboard') }}" class="btn">Enter Admin Portal</a>
            </div>
        </div>
    </div>
</body>
</html>
