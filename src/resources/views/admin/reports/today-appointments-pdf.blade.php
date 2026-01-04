<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Today's Appointments - {{ $today->format('Y-m-d') }}</title>
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
        
        .stats {
            width: 100%;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .stats table {
            width: 100%;
            border: none;
        }
        
        .stats td {
            text-align: center;
            padding: 10px;
            border: none;
        }
        
        .stat-number {
            font-size: 24pt;
            font-weight: bold;
            color: #4e73df;
            display: block;
        }
        
        .stat-label {
            font-size: 9pt;
            color: #666;
            text-transform: uppercase;
            display: block;
            margin-top: 5px;
        }
        
        table.appointments {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table.appointments thead {
            background: #4e73df;
            color: white;
        }
        
        table.appointments th {
            padding: 10px 6px;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
            border: 1px solid #4e73df;
        }
        
        table.appointments td {
            padding: 8px 6px;
            border: 1px solid #ddd;
            font-size: 9pt;
        }
        
        table.appointments tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
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
            font-size: 8pt;
            color: #666;
        }
        
        .no-appointments {
            text-align: center;
            padding: 60px 0;
            color: #999;
        }
        
        .no-appointments h3 {
            font-size: 16pt;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Med. Rep. Appointment System</h1>
        <h2>Today's Appointments Report - {{ $today->format('l, F j, Y') }}</h2>
    </div>
    
    <div class="stats">
        <table>
            <tr>
                <td>
                    <span class="stat-number">{{ $stats['total'] }}</span>
                    <span class="stat-label">Total Appointments</span>
                </td>
                <td>
                    <span class="stat-number">{{ $stats['pharmacy'] }}</span>
                    <span class="stat-label">Pharmacy</span>
                </td>
                <td>
                    <span class="stat-number">{{ $stats['non_pharmacy'] }}</span>
                    <span class="stat-label">Non-Pharmacy</span>
                </td>
            </tr>
        </table>
    </div>
    
    @if($appointments->count() > 0)
    <table class="appointments">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 10%;">Time</th>
                <th style="width: 23%;">Representative Name</th>
                <th style="width: 18%;">Company</th>
                <th style="width: 18%;">Department</th>
                <th style="width: 18%;">Contact</th>
                <th style="width: 8%;">Type</th>
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
                <td style="font-size: 8pt;">{{ $appointment->user->email }}</td>
                <td style="text-align: center;">
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
        <h3>No Appointments Today</h3>
        <p>There are no approved appointments scheduled for today.</p>
    </div>
    @endif
    
    <div class="footer">
        <p>Report generated on {{ now()->format('F j, Y g:i A') }} by {{ auth()->user()->name }}</p>
        <p>&copy; {{ date('Y') }} Med. Rep. Appointment System. All rights reserved.</p>
    </div>
</body>
</html>
