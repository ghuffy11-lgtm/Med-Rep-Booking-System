@extends('layouts.guest')

@section('title', 'Login')
@section('header-title', 'Welcome Back')
@section('header-subtitle', 'Sign in to your account')

@section('content')
<form action="{{ route('login') }}" method="POST">
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
    </div>
    
    <div class="mb-4">
        <label for="password" class="form-label">
            <i class="bi bi-lock"></i> Password
        </label>
        <input 
            type="password" 
            class="form-control @error('password') is-invalid @enderror" 
            id="password" 
            name="password" 
            placeholder="Enter your password"
            required
        >
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div class="form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember">
        <label class="form-check-label" for="remember">Remember me</label>
    </div>
    <a href="{{ route('password.request') }}" class="text-decoration-none" style="color: #667eea; font-weight: 600;">
        Forgot Password?
    </a>
</div>
    
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-box-arrow-in-right"></i> Sign In
    </button>
</form>
@endsection

@section('footer')
    <p class="mb-0">
        Don't have an account? 
        <a href="{{ route('register') }}">Register here</a>
    </p>
@endsection
