@extends('layouts.guest')

@section('title', 'Forgot Password')
@section('header-title', 'Forgot Password?')
@section('header-subtitle', 'Enter your email to receive a password reset link')

@section('content')
<form action="{{ route('password.email') }}" method="POST">
    @csrf
    
    <div class="mb-4">
        <label for="email" class="form-label">
            <i class="bi bi-envelope"></i> Email Address
        </label>
        <input 
            type="email" 
            class="form-control @error('email') is-invalid @enderror" 
            id="email" 
            name="email" 
            value="{{ old('email') }}" 
            placeholder="your.email@example.com"
            required 
            autofocus
        >
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted mt-2 d-block">
            We'll send you a link to reset your password.
        </small>
    </div>
    
    <button type="submit" class="btn btn-primary mb-3">
        <i class="bi bi-send"></i> Send Reset Link
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
