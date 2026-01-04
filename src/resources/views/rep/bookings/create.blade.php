@extends('layouts.rep')

@section('title', 'Create Booking')
@section('page-title', 'New Booking')
@php
    $hideBottomNav = true;
@endphp

@push('styles')
<style>
    /* Step indicator - Original Blue/Green Palette */
    .booking-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        padding: 0 10px;
    }

    .booking-step {
        flex: 1;
        text-align: center;
        position: relative;
    }

    .booking-step:not(:last-child):after {
        content: '';
        position: absolute;
        top: 20px;
        left: 50%;
        width: 100%;
        height: 3px;
        background: #dee2e6;
        z-index: -1;
    }

    .booking-step.active:after { background: #0d6efd; }
    .booking-step.completed:after { background: #198754; }

    .step-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #dee2e6;
        color: #6c757d;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        margin-bottom: 8px;
        border: 3px solid white;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .booking-step.active .step-circle { background: #0d6efd; color: white; transform: scale(1.1); }
    .booking-step.completed .step-circle { background: #198754; color: white; }

    .step-label { font-size: 12px; color: #6c757d; display: block; font-weight: 500; }
    .booking-step.active .step-label { color: #0d6efd; font-weight: 700; }

    /* Form Layout */
    .form-section { display: none; animation: fadeIn 0.3s ease; }
    .form-section.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    /* Selectable Cards */
    .department-radio, .time-slot-radio { display: none; }

    .department-label, .time-slot-label {
        display: flex;
        align-items: center;
        padding: 20px;
        border: 3px solid #dee2e6;
        border-radius: 12px;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 12px;
    }

    .department-radio:checked + .department-label,
    .time-slot-radio:checked + .time-slot-label {
        border-color: #0d6efd;
        background: #e7f1ff;
    }

    /* Department Icons Style */
    .department-icon {
        width: 50px;
        height: 50px;
        background: #f8f9fa;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 24px;
        color: #6c757d;
    }

    .department-radio:checked + .department-label .department-icon {
        background: #0d6efd;
        color: white;
    }

    /* TIME SLOT FONT - Bold and Large */
    .slot-text {
        font-size: 1.6rem !important; 
        font-weight: 900 !important;   
        display: block;
        color: #212529;
    }

    .loading-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    .loading-overlay.show { display: flex; }

    .btn-lg-custom {
        height: 56px;
        font-weight: 600;
        border-radius: 10px;
    }
</style>
@endpush

@section('content')
<div class="container py-3">

    @if(isset($cooldownInfo) && $cooldownInfo['in_cooldown'])
        <div class="alert alert-warning rounded-3 border-0 shadow-sm mb-4">
            <i class="bi bi-hourglass-split me-2"></i>
            <strong>Cooldown:</strong> Next booking on <strong>{{ $cooldownInfo['cooldown_end']->format('F j, Y') }}</strong>.
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-calendar-plus me-2"></i>New Booking</h5>
        </div>
        <div class="card-body p-4">

            <div class="booking-steps">
                @foreach(['Dept', 'Date', 'Time', 'Confirm'] as $i => $label)
                <div class="booking-step {{ $i == 0 ? 'active' : '' }}" data-step="{{ $i+1 }}">
                    <div class="step-circle">{{ $i+1 }}</div>
                    <span class="step-label d-none d-md-block">{{ $label }}</span>
                </div>
                @endforeach
            </div>

            <form action="{{ route('rep.bookings.store') }}" method="POST" id="bookingForm">
                @csrf

                <div class="form-section active" id="step1">
                    <h6 class="fw-bold mb-3">Select Department</h6>
                    @forelse($departments as $department)
                        <input type="radio" name="department_id" id="dept_{{ $department->id }}" value="{{ $department->id }}" class="department-radio" required>
                        <label for="dept_{{ $department->id }}" class="department-label">
                            <div class="department-icon">
                                <i class="bi bi-{{ $department->is_pharmacy_department ? 'capsule' : 'hospital' }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold">{{ $department->name }}</h6>
                                <small class="text-muted">{{ $department->is_pharmacy_department ? 'Pharmacy' : 'Clinical' }}</small>
                            </div>
                        </label>
                    @empty
                        <div class="text-center py-5"><p>No Departments Available</p></div>
                    @endforelse
                    <button type="button" class="btn btn-primary btn-lg-custom w-100 mt-3" onclick="nextStep(2)" id="step1Next" disabled>
                        Continue <i class="bi bi-arrow-right"></i>
                    </button>
                </div>

                <div class="form-section" id="step2">
                    <h6 class="fw-bold mb-3">Select Date</h6>
                    <input type="text" name="booking_date" id="booking_date" class="form-control form-control-lg rounded-3 mb-4" placeholder="Pick a date" readonly required>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary btn-lg-custom px-4" onclick="prevStep(1)">Back</button>
                        <button type="button" class="btn btn-primary btn-lg-custom flex-grow-1" onclick="loadTimeSlots()" id="step2Next" disabled>Next</button>
                    </div>
                </div>

                <div class="form-section" id="step3">
                    <h6 class="fw-bold mb-3">Select Time Slot</h6>
                    <div id="timeSlotsLoading" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary"></div>
                    </div>
                 <div class="alert alert-info mb-3" id="slotStatusBox" style="display: none;">
                    <strong>📊 Availability:</strong> 
                    <span id="slotAvailable" class="badge bg-success">0</span> Available | 
                    <span id="slotOccupied" class="badge bg-danger">0</span> Occupied
                    <small class="d-block mt-1">Total: <span id="slotTotal">0</span> slots</small>
                </div>
                    <div id="timeSlotsContainer"></div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="button" class="btn btn-secondary btn-lg-custom px-4" onclick="prevStep(2)">Back</button>
                        <button type="button" class="btn btn-primary btn-lg-custom flex-grow-1" onclick="nextStep(4)" id="step3Next" disabled>Review</button>
                    </div>
                </div>

                <div class="form-section" id="step4">
                    <h6 class="fw-bold mb-3">Review Details</h6>
                    <div class="card bg-light border-0 rounded-3 mb-4">
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted d-block">Department</small>
                                <span id="reviewDepartment" class="fw-bold fs-5">-</span>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Date</small>
                                <span id="reviewDate" class="fw-bold fs-5">-</span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Time</small>
                                <span id="reviewTime" class="fw-bold fs-5">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary btn-lg-custom px-4" onclick="prevStep(3)">Back</button>
                        <button type="submit" class="btn btn-success btn-lg-custom flex-grow-1" id="submitBtn">Confirm Booking</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="loading-overlay" id="loadingOverlay">
    <div class="bg-white p-4 rounded-4 text-center">
        <div class="spinner-border text-primary mb-2"></div>
        <p class="mb-0 fw-bold">Processing...</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentStep = 1;
let selectedDepartment = null;
let selectedDate = null;
let datePicker = null;

document.addEventListener('DOMContentLoaded', function() {
    datePicker = flatpickr("#booking_date", {
        minDate: new Date().fp_incr(1),
        maxDate: new Date().fp_incr({{ $globalConfig->booking_advance_days ?? 7 }}),
        dateFormat: "Y-m-d",
        disable: [function(date) {
            const allowedDays = @json($allowedDays);
            return !allowedDays.includes(date.getDay());
        }],
        onChange: (dates, str) => {
            selectedDate = str;
            document.getElementById('step2Next').disabled = false;
        }
    });

    document.querySelectorAll('.department-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            selectedDepartment = this.value;
            document.getElementById('step1Next').disabled = false;
        // Auto-advance to next step
            setTimeout(() => nextStep(2), 300); // 300ms delay for smooth transition
        });
    });

    document.getElementById('bookingForm').addEventListener('submit', () => {
        document.getElementById('loadingOverlay').classList.add('show');
    });
});

function nextStep(step) {
    document.querySelector('.form-section.active').classList.remove('active');
    document.querySelector('.booking-step.active').classList.add('completed');
    document.querySelector('.booking-step.active').classList.remove('active');
    document.getElementById('step' + step).classList.add('active');
    document.querySelector('.booking-step[data-step="' + step + '"]').classList.add('active');
    currentStep = step;
    if (step === 4) updateReview();
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function prevStep(step) {
    document.querySelector('.form-section.active').classList.remove('active');
    document.querySelector('.booking-step.active').classList.remove('active');
    document.getElementById('step' + step).classList.add('active');
    const prev = document.querySelector('.booking-step[data-step="' + step + '"]');
    prev.classList.add('active');
    prev.classList.remove('completed');
    currentStep = step;
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function loadTimeSlots() {
    document.getElementById('timeSlotsLoading').style.display = 'block';
    document.getElementById('timeSlotsContainer').innerHTML = '';

    fetch(`/api/slots/available?department_id=${selectedDepartment}&date=${selectedDate}`)
        .then(res => res.json())
        .then(data => {
            nextStep(3);  // Move to step 3 AFTER fetch starts
            document.getElementById('timeSlotsLoading').style.display = 'none';

            if (data.success && data.slots.length > 0) {
                // Update status display
                const statusBox = document.getElementById('slotStatusBox');
                if (statusBox) {
                    statusBox.style.display = 'block';
                    document.getElementById('slotAvailable').innerText = data.available_count;
                    document.getElementById('slotOccupied').innerText = data.booked_count;
                    document.getElementById('slotTotal').innerText = data.total_slots;
                }

                let html = '';
                let availableCount = 0;

                data.slots.forEach(slot => {
                    if (slot.is_available) {
                        // Available slot - Green, clickable
                        availableCount++;
                        html += `
                            <div class="time-slot-item">
                                <input type="radio" name="time_slot" id="slot_${slot.time}" value="${slot.time}" class="time-slot-radio" data-display="${slot.formatted}" required>
                                <label for="slot_${slot.time}" class="time-slot-label">
                                    <span>
                                        <strong class="slot-text">${slot.formatted}</strong>
                                        <small class="text-success fw-bold"><i class="bi bi-check-circle"></i> Available</small>
                                    </span>
                                </label>
                            </div>`;
                    } else {
                        // Occupied slot - Red, disabled
                        html += `
                            <div class="time-slot-item">
                                <label class="time-slot-label" style="opacity: 0.6; cursor: not-allowed; border-color: #dc3545;">
                                    <span>
                                        <strong class="slot-text" style="color: #6c757d;">${slot.formatted}</strong>
                                        <small class="text-danger fw-bold"><i class="bi bi-x-circle"></i> Occupied</small>
                                    </span>
                                </label>
                            </div>`;
                    }
                });

                if (availableCount === 0) {
                    html = '<div class="text-center py-4"><p class="text-danger">All slots are occupied for this date.</p></div>';
                }

                document.getElementById('timeSlotsContainer').innerHTML = html;
                document.querySelectorAll('.time-slot-radio').forEach(radio => {
                    radio.addEventListener('change', () => {
                        document.getElementById('step3Next').disabled = false;
                        // Auto-advance to review step
                        setTimeout(() => nextStep(4), 300);
                    });
                });
            } else {
                document.getElementById('timeSlotsContainer').innerHTML = '<div class="text-center py-4"><p>No slots available.</p></div>';
            }
        });
}

function updateReview() {
    const deptRadio = document.querySelector('.department-radio:checked');
    const deptLabel = deptRadio ? (deptRadio.nextElementSibling.querySelector('h6')?.innerText || '-') : '-';
    
    const dateLabel = selectedDate ? new Date(selectedDate).toLocaleDateString('en-US', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    }) : '-';

    const timeRadio = document.querySelector('.time-slot-radio:checked');
    // Using innerText on .slot-text ensures the time is pulled without "Available" or icon text
    const timeLabel = timeRadio ? timeRadio.getAttribute('data-display') : '-';

    document.getElementById('reviewDepartment').innerText = deptLabel;
    document.getElementById('reviewDate').innerText = dateLabel;
    document.getElementById('reviewTime').innerText = timeLabel;
}
</script>
@endpush
