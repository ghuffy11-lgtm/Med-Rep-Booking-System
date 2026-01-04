@extends('layouts.rep')

@section('title', 'Booking History')
@section('page-title', 'Booking History')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Past Appointments</h5>
    </div>
    <div class="card-body">
        @if($bookings->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Department</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Final Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td><code>#{{ $booking->id }}</code></td>
                        <td>
                            <i class="bi bi-hospital"></i>
                            {{ $booking->department->name }}
                        </td>
                        <td>{{ $booking->formatted_date }}</td>
                        <td>{{ $booking->formatted_time_slot }}</td>
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
                            @if($booking->status === 'approved')
                                <span class="text-success">
                                    <i class="bi bi-check-circle"></i> Completed
                                </span>
                            @elseif($booking->status === 'rejected')
                                <span class="text-danger">
                                    <i class="bi bi-x-circle"></i> Rejected
                                </span>
                            @elseif($booking->status === 'cancelled')
                                <span class="text-secondary">
                                    <i class="bi bi-slash-circle"></i> Cancelled
                                </span>
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
            <i class="bi bi-archive text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-muted">No Past Appointments</h5>
            <p class="text-muted">Your booking history will appear here.</p>
        </div>
        @endif
    </div>
</div>
@endsection
