@extends('layouts.app')

@section('title', 'Pending Bookings')
@section('page-title', 'Pending Approval Queue')

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
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-hourglass-split"></i> Pending Bookings
            <span class="badge bg-warning ms-2">{{ $bookings->total() }}</span>
        </h5>
    </div>
    <div class="card-body">
        @if($bookings->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Representative</th>
                        <th>Company</th>
                        <th>Mobile Number</th>
                        <th>Civil ID</th>
                        <th>Department</th>
                        <th>Date & Time</th>
                        <th>Requested</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td><code>#{{ $booking->id }}</code></td>
                        <td>
                            <strong>{{ $booking->user->name }}</strong><br>
                            <small class="text-muted">{{ $booking->user->email }}</small>
                        </td>
                        <td>{{ $booking->user->company }}</td>
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
                        <td>
                            <i class="bi bi-hospital"></i>
                            {{ $booking->department->name }}
                            @if($booking->department->is_pharmacy_department)
                                <span class="badge bg-info">Pharmacy</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $booking->formatted_date }}</strong><br>
                            <small class="text-muted">{{ $booking->formatted_time_slot }}</small>
                        </td>
                        <td>{{ $booking->created_at->diffForHumans() }}</td>
                        <td>
                            <div class="btn-group">
                                <!-- Approve Button -->
                                <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this booking?')">
                                        <i class="bi bi-check-circle"></i> Approve
                                    </button>
                                </form>
                                
                                <!-- Reject Button -->
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $booking->id }}">
                                    <i class="bi bi-x-circle"></i> Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Reject Modal -->
                    <div class="modal fade" id="rejectModal{{ $booking->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Reject Booking</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.bookings.reject', $booking) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            You are about to reject this booking request.
                                        </div>
                                        
                                        <p><strong>Representative:</strong> {{ $booking->user->name }}</p>
                                        <p><strong>Department:</strong> {{ $booking->department->name }}</p>
                                        <p><strong>Date:</strong> {{ $booking->formatted_date }}</p>
                                        <p><strong>Time:</strong> {{ $booking->formatted_time_slot }}</p>
                                        
                                        <hr>
                                        
                                        <div class="mb-3">
                                            <label for="rejection_reason{{ $booking->id }}" class="form-label">
                                                Rejection Reason *
                                            </label>
                                            <textarea 
                                                class="form-control" 
                                                id="rejection_reason{{ $booking->id }}" 
                                                name="rejection_reason" 
                                                rows="4" 
                                                required
                                                placeholder="Please provide a clear reason for rejection..."
                                            ></textarea>
                                            <small class="form-text text-muted">
                                                This reason will be visible to the representative.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-x-circle"></i> Reject Booking
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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
            <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-success">All Caught Up!</h5>
            <p class="text-muted">There are no pending bookings to review.</p>
        </div>
        @endif
    </div>
</div>
@endsection
