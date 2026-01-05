@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

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
<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-subtitle mb-2">Pending Bookings</h6>
                        <h2 class="mb-0">{{ $stats['pending_bookings'] }}</h2>
                        <small>Awaiting approval</small>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="bi bi-hourglass-split" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-subtitle mb-2">Today's Bookings</h6>
                        <h2 class="mb-0">{{ $stats['today_bookings'] }}</h2>
                        <small>Appointments today</small>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="bi bi-calendar-day" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-subtitle mb-2">Active Reps</h6>
                        <h2 class="mb-0">{{ $stats['active_reps'] }}</h2>
                        <small>Total representatives</small>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="bi bi-people" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-subtitle mb-2">Departments</h6>
                        <h2 class="mb-0">{{ $stats['departments'] }}</h2>
                        <small>Total departments</small>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="bi bi-hospital" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cooldown Statistics -->
<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-3">Cooldown Statistics</h6>
                <div class="d-flex justify-content-between mb-3">
                    <span>In Cooldown:</span>
                    <strong class="text-danger">{{ $cooldownStats['in_cooldown'] ?? 0 }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Available to Book:</span>
                    <strong class="text-success">{{ $cooldownStats['available_to_book'] ?? 0 }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Never Booked:</span>
                    <strong class="text-info">{{ $cooldownStats['never_booked'] ?? 0 }}</strong>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8 mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Pending Approvals</h6>
                <a href="{{ route('admin.bookings.pending') }}" class="btn btn-sm btn-outline-primary">
                    View All <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($pendingBookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Representative</th>
                                <th>Department</th>
                                <th>Date & Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingBookings as $booking)
                            <tr>
                                <td>{{ $booking->user->name }}</td>
                                <td>{{ $booking->department->name }}</td>
                                <td>
                                    {{ $booking->formatted_date }}<br>
                                    <small class="text-muted">{{ $booking->formatted_time_slot }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('admin.bookings.pending') }}" class="btn btn-sm btn-outline-primary">
                                        Review
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 mt-2">No pending bookings</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Bookings</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Representative</th>
                        <th>Department</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBookings as $booking)
                    <tr>
                        <td><code>#{{ $booking->id }}</code></td>
                        <td>{{ $booking->user->name }}</td>
                        <td>{{ $booking->department->name }}</td>
                        <td>
                            {{ $booking->formatted_date }}<br>
                            <small class="text-muted">{{ $booking->formatted_time_slot }}</small>
                        </td>
                        <td>
			@if($booking->status === 'pending')
                                <span class="badge bg-warning text-dark">
                            @elseif($booking->status === 'approved')
                                <span class="badge bg-success">
                            @elseif($booking->status === 'rejected')
                                <span class="badge bg-danger">
                            @else
                                <span class="badge bg-secondary">
                            @endif
                                {{ $booking->status_text }}
                            </span>
                        </td>
                        <td>{{ $booking->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No recent bookings
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
