@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('sidebar-menu')
    <a href="{{ route('rep.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('rep.bookings.index') }}" class="nav-link">
        <i class="bi bi-calendar-check"></i> My Bookings
    </a>
    <a href="{{ route('rep.bookings.create') }}" class="nav-link">
        <i class="bi bi-plus-circle"></i> New Booking
    </a>
    <a href="{{ route('rep.bookings.history') }}" class="nav-link">
        <i class="bi bi-clock-history"></i> History
    </a>
    <a href="{{ route('rep.profile.edit') }}" class="nav-link active">
        <i class="bi bi-person-circle"></i> My Profile
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-circle"></i> Update Profile Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('rep.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="name" class="form-label">
                            <i class="bi bi-person"></i> Full Name *
                        </label>
                        <input 
                            type="text" 
                            class="form-control @error('name') is-invalid @enderror" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $user->name) }}" 
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope"></i> Email Address *
                        </label>
                        <input 
                            type="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            id="email" 
                            name="email" 
                            value="{{ old('email', $user->email) }}" 
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="company" class="form-label">
                            <i class="bi bi-building"></i> Company Name *
                        </label>
                        <input 
                            type="text" 
                            class="form-control @error('company') is-invalid @enderror" 
                            id="company" 
                            name="company" 
                            value="{{ old('company', $user->company) }}" 
                            required
                        >
                        @error('company')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="mb-3">Change Password (Optional)</h6>
                    <p class="text-muted small">Leave blank if you don't want to change your password.</p>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i> New Password
                        </label>
                        <input 
                            type="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            id="password" 
                            name="password" 
                            placeholder="Leave blank to keep current password"
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Minimum 8 characters</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">
                            <i class="bi bi-lock-fill"></i> Confirm New Password
                        </label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            placeholder="Re-enter new password"
                        >
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Profile Info Card -->
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Account Information</h6>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Civil ID:</strong><br>
                    <code>{{ $user->civil_id }}</code>
                </p>
                <p class="mb-2">
                    <strong>Role:</strong><br>
                    <span class="badge bg-secondary">Representative</span>
                </p>
                <p class="mb-2">
                    <strong>Status:</strong><br>
                    @if($user->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </p>
                <p class="mb-0">
                    <strong>Member Since:</strong><br>
                    {{ $user->created_at->format('F j, Y') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
