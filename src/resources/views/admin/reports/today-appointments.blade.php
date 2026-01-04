@extends('layouts.app')

@section('title', "Today's Appointments")
@section('page-title', "Today's Appointments Report")

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
    <a href="{{ route('admin.reports.today') }}" class="nav-link active">
        <i class="bi bi-file-text"></i> Today's Report
    </a>

    
    @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('admin.departments.index') }}" class="nav-link">
            <i class="bi bi-hospital"></i> Departments
        </a>
        
        <hr style="border-color: rgba(255,255,255,0.2); margin: 1rem 1.5rem;">
        
        <a href="{{ route('super-admin.users.index') }}" class="nav-link">
            <i class="bi bi-people"></i> Manage Users
        </a>
        <a href="{{ route('super-admin.config.edit') }}" class="nav-link">
            <i class="bi bi-gear"></i> System Config
        </a>
    @endif
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">
                <i class="bi bi-calendar-day"></i> Today's Appointments
            </h5>
            <small class="text-muted">{{ $today->format('l, F j, Y') }}</small>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.reports.today.print') }}" target="_blank" class="btn btn-outline-primary">
                <i class="bi bi-printer"></i> Print
            </a>
            <a href="{{ route('admin.reports.today.pdf') }}" class="btn btn-outline-danger">
                <i class="bi bi-file-pdf"></i> Download PDF
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        <small>Total Appointments</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $stats['pharmacy'] }}</h3>
                        <small>Pharmacy</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $stats['non_pharmacy'] }}</h3>
                        <small>Non-Pharmacy</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments Table -->
        @if($appointments->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="table-light">
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
                        <td>
                            <strong>{{ \Carbon\Carbon::parse($appointment->time_slot)->format('g:i A') }}</strong>
                        </td>
                        <td>
                            <strong>{{ $appointment->user->name }}</strong>
                        </td>
                        <td>{{ $appointment->user->company }}</td>
                        <td>
                            <i class="bi bi-hospital"></i> {{ $appointment->department->name }}
                        </td>
                        <td>
                            <small>{{ $appointment->user->email }}</small>
                        </td>
                        <td>
                            @if($appointment->department->is_pharmacy_department)
                                <span class="badge bg-info">Pharmacy</span>
                            @else
                                <span class="badge bg-success">Clinical</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
            <h5 class="mt-3 text-muted">No Appointments Today</h5>
            <p class="text-muted">There are no approved appointments scheduled for today.</p>
        </div>
        @endif
    </div>
    @if($appointments->count() > 0)
    <div class="card-footer text-muted">
        <small>
            <i class="bi bi-info-circle"></i> 
            Report generated on {{ now()->format('F j, Y g:i A') }} by {{ auth()->user()->name }}
        </small>
    </div>
    @endif
</div>
@endsection
