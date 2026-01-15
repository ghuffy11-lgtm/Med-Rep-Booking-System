@extends('layouts.app')

@section('title', 'All Bookings')
@section('page-title', 'All Bookings')

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
    <a href="{{ route('admin.statistics.index') }}" class="nav-link">
        <i class="bi bi-graph-up"></i> Statistics
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
        <a href="{{ route('2fa.setup') }}" class="nav-link">
            <i class="bi bi-shield-lock"></i> 2FA Security
        </a>
    @endif
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-calendar-check"></i> All Bookings</h5>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="{{ route('admin.bookings.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-2">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="department_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From Date">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To Date">
                </div>
                <div class="col-md-2">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search...">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>
        
        @if($bookings->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Representative</th>
                        <th>Mobile Number</th>
                        <th>Civil ID</th>
                        <th>Department</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td><code>#{{ $booking->id }}</code></td>
                        <td>
                            <strong>{{ $booking->user->name }}</strong><br>
                            <small class="text-muted">{{ $booking->user->company }}</small>
                        </td>
                        <td>
                            @if($booking->user->mobile_number)
                                <i class="bi bi-phone"></i> {{ $booking->user->mobile_number }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($booking->user->civil_id)
                                <code>{{ $booking->user->civil_id }}</code>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
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
                        <td>
                            @if($booking->status === 'approved' && $booking->booking_date >= now()->toDateString())
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $booking->id }}">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </button>
                                
                                <!-- Cancel Modal -->
                                <div class="modal fade" id="cancelModal{{ $booking->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Cancel Approved Booking</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        <i class="bi bi-exclamation-triangle"></i>
                                                        Cancelling this approved booking will remove the representative's cooldown.
                                                    </div>
                                                    
                                                    <p><strong>Representative:</strong> {{ $booking->user->name }}</p>
                                                    <p><strong>Department:</strong> {{ $booking->department->name }}</p>
                                                    <p><strong>Date:</strong> {{ $booking->formatted_date }}</p>
                                                    
                                                    <div class="mb-3">
                                                        <label for="cancellation_reason{{ $booking->id }}" class="form-label">Cancellation Reason *</label>
                                                        <textarea 
                                                            class="form-control" 
                                                            id="cancellation_reason{{ $booking->id }}" 
                                                            name="cancellation_reason" 
                                                            rows="3" 
                                                            required
                                                        ></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-danger">Cancel Booking</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted small">No actions</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-3">
            {{ $bookings->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-muted">No Bookings Found</h5>
            <p class="text-muted">Try adjusting your filters.</p>
        </div>
        @endif
    </div>
</div>
@endsection
