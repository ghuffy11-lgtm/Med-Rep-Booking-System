@extends('layouts.app')

@section('title', 'System Configuration')
@section('page-title', 'System Configuration')

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
        <a href="{{ route('super-admin.config.edit') }}" class="nav-link active">
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
        <h5 class="mb-0"><i class="bi bi-gear"></i> System Configuration</h5>
    </div>

    <div class="card-body">
        <form action="{{ route('super-admin.config.update') }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Time Slots Configuration --}}
            <div class="mb-4">
                <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-clock"></i> Time Slots Configuration</h6>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    Configure the working hours for pharmacy and non-pharmacy departments. Time slots will be generated based on these settings.
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="non_pharmacy_start_time" class="form-label">
                            Non-Pharmacy Start Time *
                            <small class="text-muted d-block">When non-pharmacy department bookings start</small>
                        </label>
                        <input type="time" 
                               class="form-control @error('non_pharmacy_start_time') is-invalid @enderror"
                               id="non_pharmacy_start_time"
                               name="non_pharmacy_start_time"
                               value="{{ old('non_pharmacy_start_time', substr($config->non_pharmacy_start_time ?? '13:00:00', 0, 5)) }}"
                               required>
                        @error('non_pharmacy_start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="non_pharmacy_end_time" class="form-label">
                            Non-Pharmacy End Time *
                            <small class="text-muted d-block">When non-pharmacy department bookings end</small>
                        </label>
                        <input type="time" 
                               class="form-control @error('non_pharmacy_end_time') is-invalid @enderror"
                               id="non_pharmacy_end_time"
                               name="non_pharmacy_end_time"
                               value="{{ old('non_pharmacy_end_time', substr($config->non_pharmacy_end_time ?? '16:00:00', 0, 5)) }}"
                               required>
                        @error('non_pharmacy_end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="pharmacy_start_time" class="form-label">
                            Pharmacy Start Time *
                            <small class="text-muted d-block">When pharmacy department bookings start</small>
                        </label>
                        <input type="time" 
                               class="form-control @error('pharmacy_start_time') is-invalid @enderror"
                               id="pharmacy_start_time"
                               name="pharmacy_start_time"
                               value="{{ old('pharmacy_start_time', substr($config->pharmacy_start_time ?? '13:00:00', 0, 5)) }}"
                               required>
                        @error('pharmacy_start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="pharmacy_end_time" class="form-label">
                            Pharmacy End Time *
                            <small class="text-muted d-block">When pharmacy department bookings end</small>
                        </label>
                        <input type="time" 
                               class="form-control @error('pharmacy_end_time') is-invalid @enderror"
                               id="pharmacy_end_time"
                               name="pharmacy_end_time"
                               value="{{ old('pharmacy_end_time', substr($config->pharmacy_end_time ?? '17:00:00', 0, 5)) }}"
                               required>
                        @error('pharmacy_end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="slot_duration_minutes" class="form-label">
                            Slot Duration (Minutes) *
                            <small class="text-muted d-block">Duration of each time slot</small>
                        </label>
                        <select class="form-select @error('slot_duration_minutes') is-invalid @enderror"
                                id="slot_duration_minutes"
                                name="slot_duration_minutes"
                                required>
                            <option value="5" {{ old('slot_duration_minutes', $config->slot_duration_minutes ?? 10) == 5 ? 'selected' : '' }}>5 minutes</option>
                            <option value="10" {{ old('slot_duration_minutes', $config->slot_duration_minutes ?? 10) == 10 ? 'selected' : '' }}>10 minutes</option>
                            <option value="15" {{ old('slot_duration_minutes', $config->slot_duration_minutes ?? 10) == 15 ? 'selected' : '' }}>15 minutes</option>
                            <option value="20" {{ old('slot_duration_minutes', $config->slot_duration_minutes ?? 10) == 20 ? 'selected' : '' }}>20 minutes</option>
                            <option value="30" {{ old('slot_duration_minutes', $config->slot_duration_minutes ?? 10) == 30 ? 'selected' : '' }}>30 minutes</option>
                            <option value="60" {{ old('slot_duration_minutes', $config->slot_duration_minutes ?? 10) == 60 ? 'selected' : '' }}>60 minutes</option>
                        </select>
                        @error('slot_duration_minutes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Allowed Booking Days --}}
            <div class="mb-4">
                <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-calendar-week"></i> Allowed Booking Days</h6>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    Select which days of the week representatives can create bookings.
                </div>

                <div class="row">
                    @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                        <div class="col-md-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="allowed_days[]" 
                                       value="{{ $day }}"
                                       id="day_{{ $day }}"
                                       {{ in_array($day, old('allowed_days', $config->allowed_days ?? ['Tuesday', 'Thursday'])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="day_{{ $day }}">
                                    {{ $day }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('allowed_days')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            {{-- Daily Limits --}}
            <div class="mb-4">
                <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-bar-chart"></i> Daily Booking Limits</h6>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="non_pharmacy_daily_limit" class="form-label">
                            Non-Pharmacy Daily Limit *
                            <small class="text-muted d-block">Maximum non-pharmacy bookings per day</small>
                        </label>
                        <input type="number"
                               class="form-control @error('non_pharmacy_daily_limit') is-invalid @enderror"
                               id="non_pharmacy_daily_limit"
                               name="non_pharmacy_daily_limit"
                               value="{{ old('non_pharmacy_daily_limit', $config->non_pharmacy_daily_limit ?? 20) }}"
                               min="1"
                               max="100"
                               required>
                        @error('non_pharmacy_daily_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="pharmacy_daily_limit" class="form-label">
                            Pharmacy Daily Limit *
                            <small class="text-muted d-block">Maximum pharmacy bookings per day</small>
                        </label>
                        <input type="number"
                               class="form-control @error('pharmacy_daily_limit') is-invalid @enderror"
                               id="pharmacy_daily_limit"
                               name="pharmacy_daily_limit"
                               value="{{ old('pharmacy_daily_limit', $config->pharmacy_daily_limit ?? 10) }}"
                               min="1"
                               max="100"
                               required>
                        @error('pharmacy_daily_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Booking Rules --}}
            <div class="mb-4">
                <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-calendar-check"></i> Booking Rules</h6>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="booking_advance_days" class="form-label">
                            Booking Advance Days *
                            <small class="text-muted d-block">How many days in advance can representatives book?</small>
                        </label>
                        <input type="number"
                               class="form-control @error('booking_advance_days') is-invalid @enderror"
                               id="booking_advance_days"
                               name="booking_advance_days"
                               value="{{ old('booking_advance_days', $config->booking_advance_days ?? 7) }}"
                               min="1"
                               max="90"
                               required>
                        @error('booking_advance_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="cooldown_days" class="form-label">
                            Cooldown Period (Days) *
                            <small class="text-muted d-block">Days to wait after approved booking before next booking</small>
                        </label>
                        <input type="number"
                               class="form-control @error('cooldown_days') is-invalid @enderror"
                               id="cooldown_days"
                               name="cooldown_days"
                               value="{{ old('cooldown_days', $config->cooldown_days ?? 14) }}"
                               min="0"
                               max="365"
                               required>
                        @error('cooldown_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Update Configuration
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Current Configuration Preview --}}
<div class="card mt-3">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-info-circle"></i> Current Configuration Summary</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Time Slots</h6>
                <dl class="row mb-0">
                    <dt class="col-sm-6">Non-Pharmacy Hours:</dt>
                    <dd class="col-sm-6">{{ substr($config->non_pharmacy_start_time, 0, 5) }} - {{ substr($config->non_pharmacy_end_time, 0, 5) }}</dd>

                    <dt class="col-sm-6">Pharmacy Hours:</dt>
                    <dd class="col-sm-6">{{ substr($config->pharmacy_start_time, 0, 5) }} - {{ substr($config->pharmacy_end_time, 0, 5) }}</dd>

                    <dt class="col-sm-6">Slot Duration:</dt>
                    <dd class="col-sm-6">{{ $config->slot_duration_minutes }} minutes</dd>
                </dl>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Booking Rules</h6>
                <dl class="row mb-0">
                    <dt class="col-sm-6">Advance Days:</dt>
                    <dd class="col-sm-6">{{ $config->booking_advance_days ?? 7 }} days</dd>

                    <dt class="col-sm-6">Cooldown Period:</dt>
                    <dd class="col-sm-6">{{ $config->cooldown_days ?? 14 }} days</dd>

                    <dt class="col-sm-6">Allowed Days:</dt>
                    <dd class="col-sm-6">
                        @if(is_array($config->allowed_days))
                            {{ implode(', ', $config->allowed_days) }}
                        @else
                            Tuesday, Thursday
                        @endif
                    </dd>
                </dl>
            </div>
        </div>

        <hr class="my-3">

        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Daily Limits</h6>
                <dl class="row mb-0">
                    <dt class="col-sm-6">Non-Pharmacy:</dt>
                    <dd class="col-sm-6">{{ $config->non_pharmacy_daily_limit }} bookings/day</dd>

                    <dt class="col-sm-6">Pharmacy:</dt>
                    <dd class="col-sm-6">{{ $config->pharmacy_daily_limit }} bookings/day</dd>
                </dl>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-3">System Info</h6>
                <dl class="row mb-0">
                    <dt class="col-sm-6">Last Updated:</dt>
                    <dd class="col-sm-6">{{ $config->updated_at->format('M d, Y H:i') }}</dd>

                    <dt class="col-sm-6">Updated By:</dt>
                    <dd class="col-sm-6">{{ $config->updater->name ?? 'System' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
