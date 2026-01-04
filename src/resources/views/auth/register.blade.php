@extends('layouts.guest')

@section('title', 'Register')
@section('header-title', 'Create Account')
@section('header-subtitle', 'Register as a Representative')

@push('styles')
<style>
    /* Integrated Theme Colors */
    :root {
        --primary-color: #6c8cef;
        --primary-dark: #224abe;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }

    /* Password Toggle Styling */
    .password-wrapper {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        top: 50%;
        right: 15px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        z-index: 10;
    }

    .toggle-password:hover {
        color: var(--primary-color);
    }
</style>
@endpush

@push('scripts')
<script src="https://js.hcaptcha.com/1/api.js" async defer></script>
@endpush

@section('content')
<form action="{{ route('register') }}" method="POST" id="registerForm">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">
            <i class="bi bi-person"></i> Full Name
        </label>
        <input
            type="text"
            class="form-control @error('name') is-invalid @enderror"
            id="name"
            name="name"
            value="{{ old('name') }}"
            placeholder="John Doe"
            required
            autofocus
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
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
            inputmode="email"
            required
        >
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="company" class="form-label">
            <i class="bi bi-building"></i> Company Name
        </label>
        <input
            type="text"
            class="form-control @error('company') is-invalid @enderror"
            id="company"
            name="company"
            value="{{ old('company') }}"
            placeholder="PharmaCo Kuwait"
            required
        >
        @error('company')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="civil_id" class="form-label">
            <i class="bi bi-card-text"></i> Civil ID (12 digits)
        </label>
        <input
            type="text"
            class="form-control @error('civil_id') is-invalid @enderror"
            id="civil_id"
            name="civil_id"
            value="{{ old('civil_id') }}"
            placeholder="298765432109"
            inputmode="numeric"
            maxlength="12"
            pattern="[0-9]{12}"
            required
        >
        <small class="form-text text-muted">Enter exactly 12 digits</small>
        @error('civil_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">
            <i class="bi bi-lock"></i> Password
        </label>
        <div class="password-wrapper">
            <input
                type="password"
                class="form-control @error('password') is-invalid @enderror"
                id="password"
                name="password"
                placeholder="Minimum 8 characters"
                style="padding-right: 45px;"
                required
            >
            <i class="bi bi-eye-slash toggle-password" id="togglePassword"></i>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-4">
        <label for="password_confirmation" class="form-label">
            <i class="bi bi-lock-fill"></i> Confirm Password
        </label>
        <div class="password-wrapper">
            <input
                type="password"
                class="form-control"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="Re-enter your password"
                style="padding-right: 45px;"
                required
            >
            <i class="bi bi-eye-slash toggle-password" id="toggleConfirmPassword"></i>
        </div>
    </div>

    <div class="mb-4">
        <div class="h-captcha"
             data-sitekey="{{ config('services.hcaptcha.site_key') }}">
        </div>

        @error('h-captcha-response')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary w-100" id="submitBtn">
        <span id="btnText"><i class="bi bi-person-plus"></i> Create Account</span>
        <span id="btnLoading" class="spinner-border spinner-border-sm" style="display: none;" role="status" aria-hidden="true"></span>
    </button>
</form>

<script>
    // Password Toggle Logic
    function setupPasswordToggle(toggleId, inputId) {
        const toggle = document.getElementById(toggleId);
        const input = document.getElementById(inputId);
        
        if (toggle && input) {
            toggle.addEventListener('click', function() {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        setupPasswordToggle('togglePassword', 'password');
        setupPasswordToggle('toggleConfirmPassword', 'password_confirmation');

        // Form Loading State
        const form = document.getElementById('registerForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoading = document.getElementById('btnLoading');

        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline-block';
        });
    });
</script>
@endsection

@section('footer')
    <p class="mb-0">
        Already have an account?
        <a href="{{ route('login') }}" style="color: var(--primary-color); font-weight: 600;">Sign in here</a>
    </p>
@endsection
