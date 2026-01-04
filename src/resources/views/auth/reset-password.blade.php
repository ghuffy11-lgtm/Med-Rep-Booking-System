@extends('layouts.guest')

@section('title', 'Reset Password')
@section('header-title', 'Reset Your Password')
@section('header-subtitle', 'Enter your new password below')

@section('content')
<form action="{{ route('password.update') }}" method="POST">
    @csrf
    
    <input type="hidden" name="token" value="{{ $token }}">
    
    <div class="mb-4">
        <label for="email" class="form-label">
            <i class="bi bi-envelope"></i> Email Address
        </label>
        <input 
            type="email" 
            class="form-control @error('email') is-invalid @enderror" 
            id="email" 
            name="email" 
            value="{{ old('email', $email) }}" 
            placeholder="your.email@example.com"
            required 
            readonly
            style="background-color: #f8f9fa; cursor: not-allowed;"
        >
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-4">
        <label for="password" class="form-label">
            <i class="bi bi-lock"></i> New Password
        </label>
        <input 
            type="password" 
            class="form-control @error('password') is-invalid @enderror" 
            id="password" 
            name="password" 
            placeholder="Enter new password (min 8 characters)"
            required
            autofocus
        >
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
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
            placeholder="Confirm new password"
            required
        >
    </div>
    
    <button type="submit" class="btn btn-primary mb-3">
        <i class="bi bi-check-circle"></i> Reset Password
    </button>
    
    <div class="text-center">
        <a href="{{ route('login') }}" class="text-decoration-none" style="color: #667eea;">
            <i class="bi bi-arrow-left"></i> Back to Login
        </a>
    </div>
</form>
@endsection

@section('footer')
    <p class="mb-0">
        Don't have an account? 
        <a href="{{ route('register') }}">Register here</a>
    </p>
@endsection
