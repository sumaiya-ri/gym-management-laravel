<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Class Booking Alert</title>
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
            background: #111827;
            padding: 40px;
            text-align: center;
            color: #ffffff;
            border-bottom: 4px solid #7c3aed;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.025em;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 14px;
            color: #9ca3af;
            font-weight: 500;
        }
        .content {
            padding: 40px;
        }
        .details-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 30px;
        }
        .details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }
        .details-row:last-child {
            margin-bottom: 0;
            padding-top: 12px;
            border-top: 1px dashed #e5e7eb;
        }
        .label {
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.05em;
        }
        .value {
            font-weight: 600;
            color: #1f2937;
            text-align: right;
        }
        .value.highlight {
            color: #10b981;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            padding: 0 40px 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Class Booking Alert</h1>
            <p>Gym: {{ $booking->gym->name ?? $booking->timeslot->gym->name }}</p>
        </div>
        
        <div class="content">
            <p>Hello Gym Administrator,</p>
            <p>A new class has been booked and paid for. Below are the details:</p>
            
            <div class="details-box">
                <div class="details-row">
                    <span class="label">Member Name</span>
                    <span class="value">{{ $booking->user->name }}</span>
                </div>
                <div class="details-row">
                    <span class="label">Member Email</span>
                    <span class="value">{{ $booking->user->email }}</span>
                </div>
                <div class="details-row">
                    <span class="label">Class Booked</span>
                    <span class="value">{{ $booking->timeslot->service->name }}</span>
                </div>
                <div class="details-row">
                    <span class="label">Trainer Assigned</span>
                    <span class="value">{{ $booking->timeslot->trainer->name }}</span>
                </div>
                <div class="details-row">
                    <span class="label">Class Schedule</span>
                    <span class="value">
                        {{ \Carbon\Carbon::parse($booking->timeslot->date)->format('F d, Y') }} 
                        ({{ \Carbon\Carbon::parse($booking->timeslot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->timeslot->end_time)->format('h:i A') }})
                    </span>
                </div>
                <div class="details-row">
                    <span class="label">Transaction ID</span>
                    <span class="value">{{ $booking->payment_transaction_id }}</span>
                </div>
                <div class="details-row">
                    <span class="label">Amount Paid</span>
                    <span class="value highlight">${{ number_format($booking->payment_amount, 2) }}</span>
                </div>
            </div>
            
            <p style="font-size: 13px; color: #6b7280; text-align: center;">
                You can view the full class roster in your admin dashboard under the Master Schedule.
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $booking->gym->name ?? $booking->timeslot->gym->name }}. All Rights Reserved.</p>
        </div>
    </div>
</body>
</html>
