@extends('layouts.rep')

@section('title', 'My Bookings')
@section('page-title', 'My Bookings')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-calendar-check"></i> All My Bookings</h5>
        <a href="{{ route('rep.bookings.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Booking
        </a>
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
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td><code>#{{ $booking->id }}</code></td>
                        <td>
                            <i class="bi bi-hospital"></i>
                            {{ $booking->department->name }}
                            @if($booking->department->is_pharmacy_department)
                                <span class="badge bg-info">Pharmacy</span>
                            @endif
                        </td>
                        <td>{{ $booking->formatted_date }}</td>
                        <td>{{ $booking->formatted_time_slot }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'cancelled' => 'secondary'
                                ];
                                $statusTexts = [
                                    'pending' => 'Pending',
                                    'approved' => 'Approved',
                                    'rejected' => 'Rejected',
                                    'cancelled' => 'Cancelled'
                                ];
                                $color = $statusColors[$booking->status] ?? 'secondary';
                                $text = $statusTexts[$booking->status] ?? ucfirst($booking->status);
                            @endphp
                            <span class="badge bg-{{ $color }}">{{ $text }}</span>
                        </td>
                        <td>{{ $booking->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($booking->status === 'pending')
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $booking->id }}">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </button>

                                <!-- Cancel Modal -->
                                <div class="modal fade" id="cancelModal{{ $booking->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Cancel Booking</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('rep.bookings.cancel', $booking) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        <i class="bi bi-exclamation-triangle"></i>
                                                        Are you sure you want to cancel this booking?
                                                    </div>
                                                    <p><strong>Department:</strong> {{ $booking->department->name }}</p>
                                                    <p><strong>Date:</strong> {{ $booking->formatted_date }}</p>
                                                    <p><strong>Time:</strong> {{ $booking->formatted_time_slot }}</p>

                                                    <div class="mb-3">
                                                        <label for="cancellation_reason{{ $booking->id }}" class="form-label">Cancellation Reason *</label>
                                                        <textarea class="form-control" id="cancellation_reason{{ $booking->id }}" name="cancellation_reason" rows="3" required placeholder="Please provide a reason for cancellation..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-x-circle"></i> Cancel Booking
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @elseif($booking->status === 'rejected')
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#reasonModal{{ $booking->id }}">
                                    <i class="bi bi-info-circle"></i> View Reason
                                </button>

                                <!-- Reason Modal -->
                                <div class="modal fade" id="reasonModal{{ $booking->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Rejection Reason</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Department:</strong> {{ $booking->department->name }}</p>
                                                <p><strong>Date:</strong> {{ $booking->formatted_date }}</p>
                                                <p><strong>Time:</strong> {{ $booking->formatted_time_slot }}</p>
                                                <hr>
                                                <p class="mb-0"><strong>Reason:</strong></p>
                                                <p>{{ $booking->rejection_reason ?? 'No reason provided' }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($booking->status === 'cancelled')
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#cancelReasonModal{{ $booking->id }}">
                                    <i class="bi bi-info-circle"></i> View Reason
                                </button>

                                <!-- Cancel Reason Modal -->
                                <div class="modal fade" id="cancelReasonModal{{ $booking->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-secondary text-white">
                                                <h5 class="modal-title">Cancellation Reason</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Department:</strong> {{ $booking->department->name }}</p>
                                                <p><strong>Date:</strong> {{ $booking->formatted_date }}</p>
                                                <p><strong>Time:</strong> {{ $booking->formatted_time_slot }}</p>
                                                <hr>
                                                <p class="mb-0"><strong>Cancellation Reason:</strong></p>
                                                <p>{{ $booking->cancellation_reason ?? 'No reason provided' }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
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
            <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-muted">No Bookings Yet</h5>
            <p class="text-muted">You haven't created any bookings.</p>
            <a href="{{ route('rep.bookings.create') }}" class="btn btn-primary mt-2">
                <i class="bi bi-plus-circle"></i> Create Your First Booking
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get sidebar and overlay elements
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    // Get all modals
    const modals = document.querySelectorAll('.modal');

    // When any modal is about to show
    modals.forEach(function(modalElement) {
        modalElement.addEventListener('show.bs.modal', function() {
            // Close sidebar if open on mobile
            if (sidebar) {
                sidebar.classList.remove('show');
            }
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('show');
            }

            // Prevent body scroll (iOS fix)
            document.body.style.position = 'fixed';
            document.body.style.top = `-${window.scrollY}px`;
            document.body.style.width = '100%';
        });

        // When modal is hidden
        modalElement.addEventListener('hidden.bs.modal', function() {
            // Restore body scroll
            const scrollY = document.body.style.top;
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.width = '';
            window.scrollTo(0, parseInt(scrollY || '0') * -1);
        });

        // iOS-specific touch handling for backdrop
        modalElement.addEventListener('shown.bs.modal', function() {
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                // Add touch event listener for iOS
                backdrop.addEventListener('touchstart', function(e) {
                    e.preventDefault();
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                }, { passive: false });
            }
        });
    });

    // Enhanced close button handler for iOS
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function(closeBtn) {
        closeBtn.addEventListener('touchstart', function(e) {
            e.stopPropagation();
            const modalId = this.closest('.modal').id;
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
        }, { passive: false });
    });
});
</script>
@endpush
