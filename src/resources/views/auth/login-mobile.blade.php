<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Pharmacy Booking System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="...">
    <style>
        body {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .login-header i {
            font-size: 64px;
            margin-bottom: 15px;
            display: block;
        }

        .login-header h3 {
            margin: 0 0 8px 0;
            font-size: 28px;
            font-weight: 700;
        }

        .login-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 15px;
        }

        .login-body {
            padding: 35px 25px;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-control {
            height: 58px;
            font-size: 16px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-floating > label {
            padding: 1rem 16px;
            font-size: 15px;
            color: #6c757d;
        }

        .form-check {
            margin-bottom: 20px;
        }

        .form-check-input {
            width: 20px;
            height: 20px;
            margin-top: 2px;
            cursor: pointer;
        }

        .form-check-label {
            font-size: 15px;
            cursor: pointer;
            user-select: none;
        }

        .btn-login {
            width: 100%;
            height: 58px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 12px;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
            color: white;
            margin-top: 10px;
            transition: transform 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e0e0e0;
        }

        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            color: #6c757d;
            font-size: 14px;
        }

        .register-link {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            margin: 0 -25px -35px -25px;
            border-radius: 0 0 20px 20px;
        }

        .register-link a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
        }

        .alert {
            border-radius: 12px;
            padding: 15px 18px;
            margin-bottom: 20px;
            border: none;
            font-size: 15px;
            display: flex;
            align-items: center;
        }

        .alert i {
            font-size: 20px;
            margin-right: 10px;
        }

        .alert-danger {
            background: #fee;
            color: #c33;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .invalid-feedback {
            font-size: 14px;
            margin-top: 6px;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        /* Mobile specific */
        @media (max-width: 767.98px) {
            body {
                padding: 15px;
            }

            .login-container {
                border-radius: 16px;
            }

            .login-header {
                padding: 35px 20px;
            }

            .login-header i {
                font-size: 56px;
            }

            .login-header h3 {
                font-size: 24px;
            }

            .login-body {
                padding: 30px 20px;
            }

            .form-control {
                font-size: 16px; /* Prevents iOS zoom */
            }

            .register-link {
                margin: 0 -20px -30px -20px;
            }
        }

        /* Loading spinner */
        .spinner-border-sm {
            width: 1.2rem;
            height: 1.2rem;
            border-width: 2px;
        }

        /* Password toggle */
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            font-size: 20px;
            z-index: 10;
            user-select: none;
        }

        .password-toggle:hover {
            color: #495057;
        }

        .password-field {
            position: relative;
        }

        .password-field .form-control {
            padding-right: 50px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <i class="bi bi-capsule"></i>
            <h3>Welcome Back</h3>
            <p>Login to access your bookings</p>
        </div>

        <!-- Body -->
        <div class="login-body">
            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Info Message -->
            @if(session('info'))
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    {{ session('info') }}
                </div>
            @endif

            <!-- Error Message -->
            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="POST" id="loginForm">
                @csrf

                <!-- Email -->
                <div class="form-floating">
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           placeholder="Email Address"
                           required
                           autofocus>
                    <label for="email"><i class="bi bi-envelope"></i> Email Address</label>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="password-field">
                    <div class="form-floating">
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Password"
                               required>
                        <label for="password"><i class="bi bi-lock"></i> Password</label>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <i class="bi bi-eye password-toggle" id="togglePassword"></i>
                </div>

		<!-- Remember Me & Forgot Password -->
		<div class="d-flex justify-content-between align-items-center" style="margin-bottom: 20px;">
    <div class="form-check">
        <input class="form-check-input"
               type="checkbox"
               name="remember"
               id="remember"
               {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label" for="remember">
            Remember me
        </label>
    </div>
    <a href="{{ route('password.request') }}" style="color: #667eea; font-weight: 600; text-decoration: none; font-size: 15px;">
        Forgot?
    </a>
</div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-login" id="submitBtn">
                    <span id="btnText">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </span>
                    <span id="btnLoading" style="display: none;">
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Logging in...
                    </span>
                </button>
            </form>
        </div>

        <!-- Register Link -->
        <div class="register-link">
            Don't have an account? <a href="{{ route('register') }}">Register here</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Password toggle functionality
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        // Form submission loading state
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoading = document.getElementById('btnLoading');

        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
        });

        // Auto-dismiss success messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-success, .alert-info');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>
