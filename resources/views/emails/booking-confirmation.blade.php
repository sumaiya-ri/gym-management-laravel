<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Confirmed</title>
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
            font-size: 24px;
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
        }
        .details-box {
            background-color: #f5f3ff;
            border: 1px solid #e0d7ff;
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
            border-top: 1px dashed #c7b8ff;
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
            color: #7c3aed;
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
            <h1>Workout Confirmed!</h1>
            <p>Get ready for your upcoming workout at {{ $booking->gym->name ?? $booking->timeslot->gym->name }}</p>
        </div>
        
        <div class="content">
            <p>Hi {{ $booking->user->name }},</p>
            <p>Your payment was successful and your spot has been secured. Here are your booking and payment details:</p>
            
            <div class="details-box">
                <div class="details-row">
                    <span class="label">Class</span>
                    <span class="value">{{ $booking->timeslot->service->name }}</span>
                </div>
                <div class="details-row">
                    <span class="label">Trainer</span>
                    <span class="value">{{ $booking->timeslot->trainer->name }}</span>
                </div>
                <div class="details-row">
                    <span class="label">Date</span>
                    <span class="value">{{ \Carbon\Carbon::parse($booking->timeslot->date)->format('l, F d, Y') }}</span>
                </div>
                <div class="details-row">
                    <span class="label">Time</span>
                    <span class="value">
                        {{ \Carbon\Carbon::parse($booking->timeslot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->timeslot->end_time)->format('h:i A') }}
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
            
            <div style="text-align: center;">
                <a href="{{ route('member.bookings') }}" class="btn">View My Bookings</a>
            </div>

            <p style="font-size: 13px; color: #6b7280; text-align: center; margin-top: 20px;">
                If you need to reschedule or cancel your class, please do so at least 24 hours prior to start time.
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $booking->gym->name ?? $booking->timeslot->gym->name }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
