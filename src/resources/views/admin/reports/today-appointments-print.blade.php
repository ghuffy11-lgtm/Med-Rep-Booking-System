<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Appointments - {{ $today->format('Y-m-d') }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
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
            font-size: 24pt;
            color: #4e73df;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14pt;
            color: #666;
            font-weight: normal;
        }
        
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            padding: 15px 0;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .stat-box {
            text-align: center;
        }
        
        .stat-box .number {
            font-size: 28pt;
            font-weight: bold;
            color: #4e73df;
        }
        
        .stat-box .label {
            font-size: 10pt;
            color: #666;
            text-transform: uppercase;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table thead {
            background: #4e73df;
            color: white;
        }
        
        table th {
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 10pt;
        }
        
        table td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10pt;
        }
        
        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        table tbody tr:hover {
            background: #e9ecef;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: 600;
        }
        
        .badge-pharmacy {
            background: #17a2b8;
            color: white;
        }
        
        .badge-clinical {
            background: #28a745;
            color: white;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }
        
        .no-appointments {
            text-align: center;
            padding: 60px 0;
            color: #999;
        }
        
        .no-appointments i {
            font-size: 48pt;
            display: block;
            margin-bottom: 15px;
        }
        
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .no-print {
                display: none;
            }
            
            @page {
                margin: 15mm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Med. Rep. Appointment System</h1>
        <h2>Today's Appointments Report - {{ $today->format('l, F j, Y') }}</h2>
    </div>
    
    <div class="stats">
        <div class="stat-box">
            <div class="number">{{ $stats['total'] }}</div>
            <div class="label">Total Appointments</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ $stats['pharmacy'] }}</div>
            <div class="label">Pharmacy</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ $stats['non_pharmacy'] }}</div>
            <div class="label">Non-Pharmacy</div>
        </div>
    </div>
    
    @if($appointments->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 10%;">Time</th>
                <th style="width: 25%;">Representative Name</th>
                <th style="width: 20%;">Company</th>
                <th style="width: 20%;">Department</th>
                <th style="width: 15%;">Contact</th>
                <th style="width: 5%;">Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $index => $appointment)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ \Carbon\Carbon::parse($appointment->time_slot)->format('g:i A') }}</strong></td>
                <td><strong>{{ $appointment->user->name }}</strong></td>
                <td>{{ $appointment->user->company }}</td>
                <td>{{ $appointment->department->name }}</td>
                <td>{{ $appointment->user->email }}</td>
                <td>
                    @if($appointment->department->is_pharmacy_department)
                        <span class="badge badge-pharmacy">Pharmacy</span>
                    @else
                        <span class="badge badge-clinical">Clinical</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-appointments">
        <div style="font-size: 48pt; color: #ccc; margin-bottom: 15px;">📭</div>
        <h3>No Appointments Today</h3>
        <p>There are no approved appointments scheduled for today.</p>
    </div>
    @endif
    
    <div class="footer">
        <p>Report generated on {{ now()->format('F j, Y g:i A') }} by {{ auth()->user()->name }}</p>
        <p>&copy; {{ date('Y') }} Med. Rep. Appointment System. All rights reserved.</p>
    </div>
    
    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
