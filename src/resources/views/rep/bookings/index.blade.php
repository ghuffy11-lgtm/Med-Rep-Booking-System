<!-- Rejection Reason Modal -->
<div class="modal fade" id="reasonModal{{ $booking->id }}" tabindex="-1" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Rejection Reason</h5>
		<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="closeModal{{ $booking->id }}()"></button>
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
                <button type="button" class="btn btn-secondary btn-lg w-100" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Reason Modal -->
<div class="modal fade" id="cancelReasonModal{{ $booking->id }}" tabindex="-1" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">Cancellation Reason</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <button type="button" class="btn btn-secondary btn-lg w-100" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
// Force modal dismissal on mobile
document.addEventListener('DOMContentLoaded', function() {
    // When any modal is shown
    document.querySelectorAll('.modal').forEach(function(modalEl) {
        modalEl.addEventListener('shown.bs.modal', function() {
            // Add click handler to backdrop
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.addEventListener('click', function() {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                    }
                });
            }
        });
    });
    
    // ESC key handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.show').forEach(function(modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }
            });
        }
    });
});
</script>
@endpush
