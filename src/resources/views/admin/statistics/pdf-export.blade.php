<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Statistics Report - {{ now()->format('Y-m-d') }}</title>
    <style>
        @page {
            margin: 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #4e73df;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 22pt;
            color: #4e73df;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 13pt;
            color: #666;
            font-weight: normal;
        }

        .overview {
            width: 100%;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .overview h3 {
            font-size: 14pt;
            color: #4e73df;
            margin-bottom: 10px;
        }

        .overview table {
            width: 100%;
            border-collapse: collapse;
        }

        .overview td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .overview td:first-child {
            font-weight: bold;
            width: 60%;
        }

        .overview td:last-child {
            text-align: right;
            color: #4e73df;
            font-weight: bold;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section h3 {
            font-size: 14pt;
            color: #4e73df;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #4e73df;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data-table thead {
            background: #4e73df;
            color: white;
        }

        table.data-table th {
            padding: 8px 6px;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
            border: 1px solid #4e73df;
        }

        table.data-table td {
            padding: 6px;
            border: 1px solid #ddd;
            font-size: 9pt;
        }

        table.data-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: 600;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-warning {
            background: #ffc107;
            color: #333;
        }

        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }

        .month-comparison {
            background: #e7f3ff;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #4e73df;
        }

        .month-comparison h4 {
            font-size: 12pt;
            color: #4e73df;
            margin-bottom: 8px;
        }

        .month-comparison p {
            font-size: 10pt;
            margin-bottom: 4px;
        }

        .trend-icon {
            font-weight: bold;
            font-size: 12pt;
        }

        .trend-up {
            color: #28a745;
        }

        .trend-down {
            color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Med. Rep. Appointment System</h1>
        <h2>Statistics Report</h2>
        @if($isSuperAdmin)
            <p style="font-size: 11pt; margin-top: 5px;">System-Wide Statistics</p>
        @else
            <p style="font-size: 11pt; margin-top: 5px;">{{ $pharmacyName ?? 'Pharmacy' }} Statistics</p>
        @endif
        <p style="font-size: 9pt; color: #999;">Generated on {{ now()->format('F j, Y g:i A') }}</p>
    </div>

    <!-- Overview Section -->
    <div class="overview">
        <h3>Overview Statistics</h3>
        <table>
            <tr>
                <td>Total Bookings</td>
                <td>{{ number_format($overview['total_bookings']) }}</td>
            </tr>
            <tr>
                <td>Bookings This Month</td>
                <td>{{ number_format($overview['bookings_this_month']) }}</td>
            </tr>
            <tr>
                <td>Today's Bookings</td>
                <td>{{ number_format($overview['bookings_today']) }}</td>
            </tr>
            <tr>
                <td>Pending Approvals</td>
                <td>{{ number_format($overview['pending_approvals']) }}</td>
            </tr>
            @if(isset($overview['total_representatives']))
            <tr>
                <td>Active Representatives</td>
                <td>{{ number_format($overview['total_representatives']) }}</td>
            </tr>
            @endif
            @if(isset($overview['active_representatives']))
            <tr>
                <td>Active Representatives</td>
                <td>{{ number_format($overview['active_representatives']) }}</td>
            </tr>
            @endif
            @if(isset($overview['total_pharmacies']))
            <tr>
                <td>Active Pharmacies</td>
                <td>{{ number_format($overview['total_pharmacies']) }}</td>
            </tr>
            @endif
            @if(isset($overview['total_departments']))
            <tr>
                <td>Active Departments</td>
                <td>{{ number_format($overview['total_departments']) }}</td>
            </tr>
            @endif
            @if(isset($overview['active_departments']))
            <tr>
                <td>Active Departments</td>
                <td>{{ number_format($overview['active_departments']) }}</td>
            </tr>
            @endif
            @if(isset($overview['avg_daily_bookings']))
            <tr>
                <td>Avg. Daily Bookings</td>
                <td>{{ number_format($overview['avg_daily_bookings'], 1) }}</td>
            </tr>
            @endif
            <tr>
                <td>Approval Rate</td>
                <td>{{ number_format($overview['approval_rate'], 1) }}%</td>
            </tr>
        </table>
    </div>

    <!-- Month Comparison -->
    <div class="month-comparison">
        <h4>Month-over-Month Comparison</h4>
        <p><strong>This Month:</strong> {{ number_format($monthComparison['this_month']) }} bookings</p>
        <p><strong>Last Month:</strong> {{ number_format($monthComparison['last_month']) }} bookings</p>
        <p>
            <strong>Change:</strong>
            <span class="trend-icon {{ $monthComparison['change_direction'] === 'up' ? 'trend-up' : 'trend-down' }}">
                {{ $monthComparison['change_direction'] === 'up' ? '↑' : '↓' }}
                {{ number_format(abs($monthComparison['change']), 1) }}%
            </span>
        </p>
    </div>

    <!-- Top Departments -->
    @if(isset($topDepartments) && count($topDepartments) > 0)
    <div class="section">
        <h3>Top 10 Departments</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Rank</th>
                    <th style="width: 35%;">Department</th>
                    <th style="width: 15%;">This Month</th>
                    <th style="width: 15%;">Last Month</th>
                    <th style="width: 15%;">Change</th>
                    <th style="width: 10%;">Trend</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topDepartments as $index => $dept)
                <tr>
                    <td style="text-align: center;">#{{ $index + 1 }}</td>
                    <td><strong>{{ $dept['department'] }}</strong></td>
                    <td style="text-align: center;">{{ number_format($dept['this_month']) }}</td>
                    <td style="text-align: center;">{{ number_format($dept['last_month']) }}</td>
                    <td style="text-align: center;">
                        <span class="badge badge-{{ $dept['change_direction'] === 'up' ? 'success' : 'warning' }}">
                            {{ $dept['change_direction'] === 'up' ? '+' : '' }}{{ number_format($dept['change'], 1) }}%
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <span class="trend-icon {{ $dept['change_direction'] === 'up' ? 'trend-up' : 'trend-down' }}">
                            {{ $dept['change_direction'] === 'up' ? '↑' : '↓' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Top Representatives -->
    @if(isset($topRepresentatives) && count($topRepresentatives) > 0)
    <div class="section">
        <h3>Top 10 Representatives</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 8%;">Rank</th>
                    <th style="width: 28%;">Name</th>
                    <th style="width: 26%;">Company</th>
                    <th style="width: 13%;">Total</th>
                    <th style="width: 13%;">Approved</th>
                    <th style="width: 12%;">Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topRepresentatives as $index => $rep)
                <tr>
                    <td style="text-align: center;">
                        @if($index === 0)
                            🏆 #1
                        @else
                            #{{ $index + 1 }}
                        @endif
                    </td>
                    <td><strong>{{ $rep['name'] }}</strong></td>
                    <td>{{ $rep['company'] }}</td>
                    <td style="text-align: center;">{{ number_format($rep['total_bookings']) }}</td>
                    <td style="text-align: center;">{{ number_format($rep['approved_bookings']) }}</td>
                    <td style="text-align: center;">{{ number_format($rep['approval_rate'], 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Report generated on {{ now()->format('F j, Y g:i A') }} by {{ $generatedBy }}</p>
        <p>&copy; {{ date('Y') }} Med. Rep. Appointment System. All rights reserved.</p>
    </div>
</body>
</html>
