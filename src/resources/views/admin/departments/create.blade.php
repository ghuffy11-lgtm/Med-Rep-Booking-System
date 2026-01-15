@extends('layouts.app')

@section('title', 'Create Department')
@section('page-title', 'Create New Department')

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
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Create New Department</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.departments.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="name" class="form-label">Department Name *</label>
                        <input 
                            type="text" 
                            class="form-control @error('name') is-invalid @enderror" 
                            id="name" 
                            name="name" 
                            value="{{ old('name') }}" 
                            placeholder="e.g., Cardiology"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Department Type *</label>
                        <div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="radio" 
                                name="is_pharmacy_department" 
                                id="nonPharmacy" 
                                value="0" 
                                {{ old('is_pharmacy_department', '0') == '0' ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="nonPharmacy">
                                <strong>Non-Pharmacy Department</strong>
                                <small class="d-block text-muted">20 slots per day, 1:00 PM - 4:00 PM</small>
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input 
                                class="form-check-input" 
                                type="radio" 
                                name="is_pharmacy_department" 
                                id="pharmacy" 
                                value="1"
                                {{ old('is_pharmacy_department') == '1' ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="pharmacy">
                                <strong>Pharmacy Department</strong>
                                <small class="d-block text-muted">10 slots per day, 1:00 PM - 2:40 PM</small>
                            </label>
                        </div>
                        @error('is_pharmacy_department')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Status</label>
                        <div class="form-check form-switch">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="is_active" 
                                name="is_active" 
                                value="1"
                                {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="is_active">
                                Active (users can book appointments)
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create Department
                        </button>
                        <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">
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
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Department Guidelines</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2"><strong>Pharmacy Departments:</strong></p>
                <ul class="small mb-3">
                    <li>Limited to 10 slots per day</li>
                    <li>Time slots: 1:00 PM - 2:40 PM</li>
                    <li>10-minute intervals</li>
                </ul>
                
                <p class="small mb-2"><strong>Non-Pharmacy Departments:</strong></p>
                <ul class="small mb-0">
                    <li>20 slots per day available</li>
                    <li>Time slots: 1:00 PM - 4:00 PM</li>
                    <li>10-minute intervals</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
