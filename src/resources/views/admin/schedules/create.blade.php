@extends('layouts.app')

@section('title', 'Create Schedule')
@section('page-title', 'Create Schedule/Closure')

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
                <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Create Schedule/Closure</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.schedules.store') }}" method="POST" id="scheduleForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="department_id" class="form-label">Department *</label>
                        <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
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
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}" min="{{ date('Y-m-d') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label for="end_date" class="form-label">End Date *</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}" min="{{ date('Y-m-d') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Schedule Type *</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="is_closed" id="closure" value="1" {{ old('is_closed', '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="closure">
                                <strong>Department Closure</strong>
                                <small class="d-block text-muted">Department will be completely closed during this period</small>
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="is_closed" id="override" value="0" {{ old('is_closed', '0') == '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="override">
                                <strong>Schedule Override</strong>
                                <small class="d-block text-muted">Specify custom booking days for this period</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-4" id="overrideDaysSection">
                        <label class="form-label">Override Days (Select days when booking is allowed)</label>
                        @foreach($daysOfWeek as $day)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="override_days[]" value="{{ $day }}" id="day{{ $day }}" {{ in_array($day, old('override_days', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="day{{ $day }}">{{ $day }}</label>
                        </div>
                        @endforeach
                        <small class="text-muted">Leave unchecked to use global allowed days</small>
                    </div>
                    
                    <div class="mb-4" id="overrideTimesSection">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-clock"></i> Custom Working Hours (Optional)
                        </h6>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            If specified, these custom hours will be used instead of the global config times during this schedule period.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="override_start_time" class="form-label">
                                    Override Start Time
                                    <small class="text-muted d-block">Leave blank to use global config</small>
                                </label>
                                <input type="time" 
                                       class="form-control @error('override_start_time') is-invalid @enderror" 
                                       id="override_start_time" 
                                       name="override_start_time"
                                       value="{{ old('override_start_time') }}">
                                @error('override_start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="override_end_time" class="form-label">
                                    Override End Time
                                    <small class="text-muted d-block">Leave blank to use global config</small>
                                </label>
                                <input type="time" 
                                       class="form-control @error('override_end_time') is-invalid @enderror" 
                                       id="override_end_time" 
                                       name="override_end_time"
                                       value="{{ old('override_end_time') }}">
                                @error('override_end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="e.g., Renovation, Holiday, etc.">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active (takes effect immediately)</label>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create Schedule
                        </button>
                        <a href="{{ route('admin.schedules.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Schedule Information</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2"><strong>Department Closure:</strong></p>
                <ul class="small mb-3">
                    <li>Blocks ALL bookings during the period</li>
                    <li>Used for maintenance, holidays, etc.</li>
                    <li>Override days are ignored</li>
                </ul>
                
                <p class="small mb-2"><strong>Schedule Override:</strong></p>
                <ul class="small mb-0">
                    <li>Specify custom allowed days</li>
                    <li>Overrides global settings temporarily</li>
                    <li>Useful for special schedules</li>
                </ul>
                <p class="small mb-2"><strong>Custom Working Hours:</strong></p>
                <ul class="small mb-0">
                    <li>Optional custom start/end times</li>
                    <li>Example: Ramadan hours 8 AM - 2 PM</li>
                    <li>Overrides global working hours</li>
                </ul>
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
            $('#overrideTimesSection').hide();
        } else {
            $('#overrideDaysSection').show();
            $('#overrideTimesSection').show();
        }
    }
    
    $('input[name="is_closed"]').on('change', toggleOverrideDays);
    toggleOverrideDays();
});
</script>
@endpush
