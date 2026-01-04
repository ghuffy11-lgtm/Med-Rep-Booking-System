@extends('layouts.rep')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Profile Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('rep.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Name (Read-Only) --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Full Name
                                <i class="bi bi-lock-fill text-muted" data-bs-toggle="tooltip" title="This field cannot be changed"></i>
                            </label>
                            <input type="text" 
                                   class="form-control bg-light" 
                                   id="name" 
                                   value="{{ $user->name }}" 
                                   readonly
                                   disabled>
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> Contact administrator to change your name
                            </small>
                        </div>

                        {{-- Email (Read-Only) --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email Address
                                <i class="bi bi-lock-fill text-muted" data-bs-toggle="tooltip" title="This field cannot be changed"></i>
                            </label>
                            <input type="email" 
                                   class="form-control bg-light" 
                                   id="email" 
                                   value="{{ $user->email }}" 
                                   readonly
                                   disabled>
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> Contact administrator to change your email
                            </small>
                        </div>

                        {{-- Civil ID (Read-Only) --}}
                        <div class="mb-3">
                            <label for="civil_id" class="form-label">
                                Civil ID
                                <i class="bi bi-lock-fill text-muted" data-bs-toggle="tooltip" title="This field cannot be changed"></i>
                            </label>
                            <input type="text" 
                                   class="form-control bg-light" 
                                   id="civil_id" 
                                   value="{{ $user->civil_id }}" 
                                   readonly
                                   disabled>
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> Contact administrator to change your Civil ID
                            </small>
                        </div>

                        <hr class="my-4">

                        {{-- Company (Editable) --}}
                        <div class="mb-3">
                            <label for="company" class="form-label">
                                Company Name *
                                <i class="bi bi-pencil-fill text-primary" data-bs-toggle="tooltip" title="You can edit this field"></i>
                            </label>
                            <input type="text" 
                                   class="form-control @error('company') is-invalid @enderror" 
                                   id="company" 
                                   name="company"
                                   value="{{ old('company', $user->company) }}" 
                                   required>
                            @error('company')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">Change Password (Optional)</h6>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle"></i> Leave blank if you don't want to change your password
                        </p>

                        {{-- New Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                New Password
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password"
                                   placeholder="Enter new password (min 8 characters)">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">
                                Confirm New Password
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation"
                                   placeholder="Confirm new password">
                        </div>

                        <hr class="my-4">

                        {{-- Submit Button --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Profile
                            </button>
                            <a href="{{ route('rep.dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Account Information Card --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Account Information</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Account Role:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-info">Representative</span>
                        </dd>

                        <dt class="col-sm-4">Account Status:</dt>
                        <dd class="col-sm-8">
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Email Verified:</dt>
                        <dd class="col-sm-8">
                            @if($user->hasVerifiedEmail())
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Verified
                                </span>
                            @else
                                <span class="badge bg-warning">
                                    <i class="bi bi-exclamation-triangle"></i> Not Verified
                                </span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Member Since:</dt>
                        <dd class="col-sm-8">{{ $user->created_at->format('F j, Y') }}</dd>

                        <dt class="col-sm-4">Last Updated:</dt>
                        <dd class="col-sm-8">{{ $user->updated_at->format('F j, Y g:i A') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
