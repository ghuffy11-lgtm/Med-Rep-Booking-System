@extends('layouts.app')

@section('title', 'Edit Department')
@section('page-title', 'Edit Department')

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
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Department</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.departments.update', $department) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="name" class="form-label">Department Name *</label>
                        <input 
                            type="text" 
                            class="form-control @error('name') is-invalid @enderror" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $department->name) }}" 
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
                                {{ old('is_pharmacy_department', $department->is_pharmacy_department) == '0' ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="nonPharmacy">
                                <strong>Non-Pharmacy Department</strong>
                                <small class="d-block text-muted">20 slots per day</small>
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input 
                                class="form-check-input" 
                                type="radio" 
                                name="is_pharmacy_department" 
                                id="pharmacy" 
                                value="1"
                                {{ old('is_pharmacy_department', $department->is_pharmacy_department) == '1' ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="pharmacy">
                                <strong>Pharmacy Department</strong>
                                <small class="d-block text-muted">10 slots per day</small>
                            </label>
                        </div>
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
                                {{ old('is_active', $department->is_active) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Department
                        </button>
                        <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
