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
<div class="card-header">
        <h5 class="mb-3">
            <i class="bi bi-calendar-day"></i> Appointments Report
        </h5>
        
        {{-- Date Filter Form --}}
        <form method="GET" action="{{ route('admin.reports.today') }}" class="row g-3 align-items-end mb-3">
            <div class="col-md-3">
                <label for="from_date" class="form-label">From Date</label>
                <input type="date" 
                       class="form-control" 
                       id="from_date" 
                       name="from_date" 
                       value="{{ request('from_date', $fromDate->format('Y-m-d')) }}">
            </div>
            
            <div class="col-md-3">
                <label for="to_date" class="form-label">To Date</label>
                <input type="date" 
                       class="form-control" 
                       id="to_date" 
                       name="to_date" 
                       value="{{ request('to_date', $toDate->format('Y-m-d')) }}">
            </div>
            
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('admin.reports.today') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </form>

        {{-- Display current date range --}}
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle"></i>
            @if($fromDate->eq($toDate))
                Showing appointments for: <strong>{{ $fromDate->format('l, F j, Y') }}</strong>
            @else
                Showing appointments from: <strong>{{ $fromDate->format('M j, Y') }}</strong> to <strong>{{ $toDate->format('M j, Y') }}</strong>
            @endif
        </div>
        
        {{-- Export buttons --}}
        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('admin.reports.today.print', ['from_date' => request('from_date', $fromDate->format('Y-m-d')), 'to_date' => request('to_date', $toDate->format('Y-m-d'))]) }}" 
               target="_blank" 
               class="btn btn-outline-primary">
                <i class="bi bi-printer"></i> Print
            </a>
            <a href="{{ route('admin.reports.today.pdf', ['from_date' => request('from_date', $fromDate->format('Y-m-d')), 'to_date' => request('to_date', $toDate->format('Y-m-d'))]) }}" 
               class="btn btn-outline-danger">
                <i class="bi bi-file-pdf"></i> Download PDF
            </a>
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
                        <th style="width: 10%;">Date</th>
                        <th style="width: 8%;">Time</th>
                        <th style="width: 20%;">Representative Name</th>
                        <th style="width: 18%;">Company</th>
                        <th style="width: 18%;">Department</th>
                        <th style="width: 21%;">Contact</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $index => $appointment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($appointment->booking_date)->format('M j, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($appointment->time_slot)->format('g:i A') }}</td>
                        <td>{{ $appointment->user->name }}
                        <td>{{ $appointment->user->company }}</td>
                        <td>
                            <i class="bi bi-hospital"></i> {{ $appointment->department->name }}
                        </td>
                        <td>
                            @if($appointment->user->mobile_number)
                                <small><i class="bi bi-phone"></i> {{ $appointment->user->mobile_number }}</small><br>
                            @endif
                            <small><i class="bi bi-envelope"></i> {{ $appointment->user->email }}</small>
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
