<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Approved</title>
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
            background-color: #28a745;
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
            border-left: 4px solid #28a745;
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
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .alert-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .success-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="success-icon">✓</div>
        <h1 style="margin: 0;">Booking Approved</h1>
    </div>

    <div class="content">
        <p>Dear {{ $booking->user->name }},</p>

        <p>Good news! Your pharmacy visit booking has been approved.</p>

        <div class="booking-details">
            <h3 style="margin-top: 0; color: #28a745;">Booking Details</h3>
            
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
                <span class="detail-value" style="color: #28a745; font-weight: bold;">Approved</span>
            </div>
        </div>

        <div class="alert">
            <div class="alert-title">⏳ Important: Cooldown Period</div>
            <p style="margin: 5px 0 0 0;">
                After this visit, you will need to wait <strong>{{ $cooldownDays }} days</strong> before booking your next appointment. 
                This cooldown period starts from your visit date: <strong>{{ $booking->formatted_date }}</strong>.
            </p>
        </div>

        <p><strong>What's Next?</strong></p>
        <ul>
            <li>Please arrive on time for your appointment</li>
            <li>Bring any required documents or materials</li>
            <li>Contact the pharmacy if you need to reschedule</li>
        </ul>

        <p>If you have any questions, please contact the pharmacy administration.</p>

        <p>Thank you!</p>
    </div>

    <div class="footer">
        <p>This is an automated message from the Pharmacy Booking System.</p>
        <p>© {{ date('Y') }} Pharmacy Booking System. All rights reserved.</p>
    </div>
</body>
</html>
