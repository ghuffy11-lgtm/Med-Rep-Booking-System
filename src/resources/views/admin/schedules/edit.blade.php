@extends('layouts.app')

@section('title', 'Edit Schedule')
@section('page-title', 'Edit Schedule/Closure')

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
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Schedule/Closure</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.schedules.update', $schedule) }}" method="POST" id="scheduleForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="department_id" class="form-label">Department *</label>
                        <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $schedule->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="start_date" class="form-label">Start Date *</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $schedule->start_date) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label for="end_date" class="form-label">End Date *</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $schedule->end_date) }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Schedule Type *</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_closed" id="closure" value="1" {{ old('is_closed', $schedule->is_closed) == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="closure">
                                <strong>Department Closure</strong>
                                <small class="d-block text-muted">Department will be completely closed</small>
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="is_closed" id="override" value="0" {{ old('is_closed', $schedule->is_closed) == '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="override">
                                <strong>Schedule Override</strong>
                                <small class="d-block text-muted">Custom booking days</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-4" id="overrideDaysSection">
                        <label class="form-label">Override Days</label>
                        @php
                            $selectedDays = old('override_days', json_decode($schedule->override_days, true) ?? []);
                        @endphp
                        @foreach($daysOfWeek as $day)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="override_days[]" value="{{ $day }}" id="day{{ $day }}" {{ in_array($day, $selectedDays) ? 'checked' : '' }}>
                            <label class="form-check-label" for="day{{ $day }}">{{ $day }}</label>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mb-4">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $schedule->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $schedule->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Schedule
                        </button>
                        <a href="{{ route('admin.schedules.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function toggleOverrideDays() {
        if ($('#closure').is(':checked')) {
            $('#overrideDaysSection').hide();
        } else {
            $('#overrideDaysSection').show();
        }
    }
    
    $('input[name="is_closed"]').on('change', toggleOverrideDays);
    toggleOverrideDays();
});
</script>
@endpush
