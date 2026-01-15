@extends('layouts.app')

@section('title', 'Pharmacy Statistics')
@section('page-title', 'Pharmacy Statistics Dashboard')

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('admin.bookings.pending') }}" class="nav-link">
        <i class="bi bi-hourglass-split"></i> Pending Queue
    </a>
    <a href="{{ route('admin.bookings.index') }}" class="nav-link">
        <i class="bi bi-calendar-check"></i> All Bookings
    </a>
    <a href="{{ route('admin.schedules.index') }}" class="nav-link">
        <i class="bi bi-calendar3"></i> Schedules
    </a>
    <a href="{{ route('admin.reports.today') }}" class="nav-link">
        <i class="bi bi-file-text"></i> Today's Report
    </a>
    <a href="{{ route('admin.statistics.index') }}" class="nav-link active">
        <i class="bi bi-graph-up"></i> Statistics
    </a>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header with Export Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0"><i class="bi bi-graph-up"></i> Pharmacy Statistics</h2>
            <p class="text-muted mb-0">Performance metrics for your pharmacy</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.statistics.export.excel') }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="{{ route('admin.statistics.export.pdf') }}" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            <button class="btn btn-primary" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Month/Year Selection Form -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.statistics.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="month" class="form-label"><strong>Select Month</strong></label>
                    <select name="month" id="month" class="form-select">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="year" class="form-label"><strong>Select Year</strong></label>
                    <select name="year" id="year" class="form-select">
                        @foreach(range(date('Y'), date('Y') - 5) as $y)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> View Statistics
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.statistics.index') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset to Current Month
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Bookings
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($overview['total_bookings']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check-fill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                This Month
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($overview['bookings_this_month']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-month fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Today's Bookings
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($overview['bookings_today']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Approvals
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($overview['pending_approvals']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-hourglass-split fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Active Representatives
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($overview['active_representatives']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people-fill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Departments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($overview['active_departments']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approval Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($overview['approval_rate'], 1) }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle-fill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Avg. Daily Bookings
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($overview['avg_daily_bookings'], 1) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Month Comparison Alert -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-{{ $monthComparison['change_direction'] === 'up' ? 'success' : 'info' }} d-flex align-items-center">
                <i class="bi bi-{{ $monthComparison['change_direction'] === 'up' ? 'arrow-up-circle' : 'arrow-down-circle' }}-fill me-2" style="font-size: 2rem;"></i>
                <div>
                    <h5 class="mb-1">Month-over-Month Comparison</h5>
                    <p class="mb-0">
                        <strong>This Month:</strong> {{ number_format($monthComparison['this_month']) }} bookings |
                        <strong>Last Month:</strong> {{ number_format($monthComparison['last_month']) }} bookings |
                        <strong>Change:</strong>
                        <span class="badge bg-{{ $monthComparison['change_direction'] === 'up' ? 'success' : 'secondary' }}">
                            {{ $monthComparison['change_direction'] === 'up' ? '+' : '' }}{{ number_format($monthComparison['change'], 1) }}%
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Bookings Trend Chart -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-graph-up"></i> 30-Day Bookings Trend
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="bookingsTrendChart" style="height: 320px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Distribution Chart -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-pie-chart"></i> Status Distribution
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="statusDistributionChart" style="height: 320px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Peak Hours Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-bar-chart"></i> Peak Booking Hours
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="peakHoursChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Departments Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-building"></i> Top 10 Departments
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 8%;">Rank</th>
                                    <th style="width: 40%;">Department Name</th>
                                    <th style="width: 15%;">This Month</th>
                                    <th style="width: 15%;">Last Month</th>
                                    <th style="width: 12%;">Change</th>
                                    <th style="width: 10%;">Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topDepartments as $index => $dept)
                                <tr>
                                    <td class="text-center"><span class="badge bg-light text-dark">#{{ $index + 1 }}</span></td>
                                    <td><strong>{{ $dept['department'] }}</strong></td>
                                    <td><span class="badge bg-info">{{ number_format($dept['this_month']) }}</span></td>
                                    <td><span class="badge bg-secondary">{{ number_format($dept['last_month']) }}</span></td>
                                    <td>
                                        <span class="badge bg-{{ $dept['change_direction'] === 'up' ? 'success' : 'warning' }}">
                                            {{ $dept['change_direction'] === 'up' ? '+' : '' }}{{ number_format($dept['change'], 1) }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($dept['change_direction'] === 'up')
                                            <i class="bi bi-arrow-up-circle-fill text-success" style="font-size: 1.5rem;"></i>
                                        @else
                                            <i class="bi bi-arrow-down-circle-fill text-warning" style="font-size: 1.5rem;"></i>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Representatives Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-person-badge"></i> Top 10 Representatives
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 8%;">Rank</th>
                                    <th style="width: 30%;">Representative Name</th>
                                    <th style="width: 25%;">Company</th>
                                    <th style="width: 15%;">Total Bookings</th>
                                    <th style="width: 12%;">Approved</th>
                                    <th style="width: 10%;">Approval Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topRepresentatives as $index => $rep)
                                <tr>
                                    <td class="text-center">
                                        @if($index === 0)
                                            <span class="badge bg-warning">🏆 #1</span>
                                        @else
                                            <span class="badge bg-light text-dark">#{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ $rep['name'] }}</strong></td>
                                    <td>{{ $rep['company'] }}</td>
                                    <td><span class="badge bg-primary">{{ number_format($rep['total_bookings']) }}</span></td>
                                    <td><span class="badge bg-success">{{ number_format($rep['approved_bookings']) }}</span></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                 style="width: {{ $rep['approval_rate'] }}%;"
                                                 aria-valuenow="{{ $rep['approval_rate'] }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                                {{ number_format($rep['approval_rate'], 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
// Bookings Trend Line Chart
const trendCtx = document.getElementById('bookingsTrendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($trend['labels']) !!},
        datasets: [{
            label: 'Bookings',
            data: {!! json_encode($trend['data']) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            title: {
                display: true,
                text: 'Daily Bookings Over Last 30 Days'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Status Distribution Pie Chart
const statusCtx = document.getElementById('statusDistributionChart').getContext('2d');
new Chart(statusCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($statusDistribution['labels']) !!},
        datasets: [{
            data: {!! json_encode($statusDistribution['data']) !!},
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',   // approved - green
                'rgba(255, 193, 7, 0.8)',   // pending - yellow
                'rgba(220, 53, 69, 0.8)',   // rejected - red
                'rgba(108, 117, 125, 0.8)'  // cancelled - gray
            ],
            borderColor: [
                'rgba(40, 167, 69, 1)',
                'rgba(255, 193, 7, 1)',
                'rgba(220, 53, 69, 1)',
                'rgba(108, 117, 125, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            },
            title: {
                display: true,
                text: 'Bookings by Status'
            }
        }
    }
});

// Peak Hours Bar Chart
const peakCtx = document.getElementById('peakHoursChart').getContext('2d');
new Chart(peakCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($peakHours['labels']) !!},
        datasets: [{
            label: 'Number of Bookings',
            data: {!! json_encode($peakHours['data']) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            title: {
                display: true,
                text: 'Booking Distribution by Hour of Day'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-secondary {
    border-left: 0.25rem solid #858796 !important;
}
.text-xs {
    font-size: 0.7rem;
    font-weight: 700;
}
</style>
@endsection
