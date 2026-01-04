<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Update</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
            border-top: none;
        }
        .booking-details {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #dc3545;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #6c757d;
        }
        .detail-value {
            color: #212529;
        }
        .reason-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .reason-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 10px;
        }
        .reason-text {
            color: #212529;
            font-style: italic;
        }
        .info-box {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .status-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="status-icon">✕</div>
        <h1 style="margin: 0;">Booking {{ ucfirst($booking->status) }}</h1>
    </div>

    <div class="content">
        <p>Dear {{ $booking->user->name }},</p>

        <p>We regret to inform you that your pharmacy visit booking has been <strong>{{ $booking->status }}</strong>.</p>

        <div class="booking-details">
            <h3 style="margin-top: 0; color: #dc3545;">Booking Details</h3>
            
            <div class="detail-row">
                <span class="detail-label">Booking ID:</span>
                <span class="detail-value">#{{ $booking->id }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Department:</span>
                <span class="detail-value">{{ $booking->department->name }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ $booking->formatted_date }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Time:</span>
                <span class="detail-value">{{ $booking->formatted_time_slot }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value" style="color: #dc3545; font-weight: bold;">{{ ucfirst($booking->status) }}</span>
            </div>
        </div>

        <div class="reason-box">
            <div class="reason-title">Reason for {{ ucfirst($booking->status) }}:</div>
            <div class="reason-text">{{ $reason }}</div>
        </div>

        <div class="info-box">
            <p style="margin: 0;"><strong>ℹ️ Good News:</strong> No cooldown period applies since this booking was {{ $booking->status }}. You can submit a new booking request immediately.</p>
        </div>

        <p><strong>What You Can Do Next:</strong></p>
        <ul>
            <li>Submit a new booking request with a different date or time</li>
            <li>Contact the administration if you have questions</li>
            <li>Review the reason provided above to help with your next booking</li>
        </ul>

        <p>We apologize for any inconvenience this may cause.</p>

        <p>Thank you for your understanding!</p>
    </div>

    <div class="footer">
        <p>This is an automated message from the Pharmacy Booking System.</p>
        <p>© {{ date('Y') }} Pharmacy Booking System. All rights reserved.</p>
    </div>
</body>
</html>
