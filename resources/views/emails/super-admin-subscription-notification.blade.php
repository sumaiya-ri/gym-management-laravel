<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SaaS Subscription Alert</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; margin: 0; padding: 40px 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; }
        .header { background: #0f172a; padding: 40px; text-align: center; color: #ffffff; border-bottom: 4px solid #0ea5e9; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 800; }
        .content { padding: 40px; }
        .details-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; margin-bottom: 30px; }
        .details-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; }
        .details-row:last-child { margin-bottom: 0; padding-top: 12px; border-top: 1px dashed #e2e8f0; }
        .label { font-weight: 700; color: #64748b; text-transform: uppercase; font-size: 11px; }
        .value { font-weight: 600; color: #0f172a; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SaaS Subscription Alert!</h1>
            <p>New Tenant Signed Up</p>
        </div>
        <div class="content">
            <p>Hello Super Admin,</p>
            <p>A new Gym Enterprise has completed their simulated subscription payment. Here are the tenant registration details:</p>
            
            <div class="details-box">
                <div class="details-row"><span class="label">Gym / Studio</span><span class="value">{{ $gym->name }}</span></div>
                <div class="details-row"><span class="label">Contact Email</span><span class="value">{{ $gym->email }}</span></div>
                <div class="details-row"><span class="label">Plan Subscribed</span><span class="value">{{ $gym->subscription_plan }}</span></div>
                <div class="details-row"><span class="label">Transaction ID</span><span class="value">{{ $gym->subscription_transaction_id }}</span></div>
            </div>

            <p style="font-size: 13px; color: #64748b; text-align: center;">
                You can review this gym and all tenant metrics in the Super Admin SaaS portal.
            </p>
        </div>
    </div>
</body>
</html>
