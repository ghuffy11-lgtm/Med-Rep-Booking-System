<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - Pharmacy Booking System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

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

        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .back-link {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            margin: 0 -25px -35px -25px;
            border-radius: 0 0 20px 20px;
        }

        .back-link a {
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

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background: #fee;
            color: #c33;
        }

        .invalid-feedback {
            font-size: 14px;
            margin-top: 6px;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .help-text {
            font-size: 14px;
            color: #6c757d;
            margin-top: 8px;
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

            .back-link {
                margin: 0 -20px -30px -20px;
            }
        }

        .spinner-border-sm {
            width: 1.2rem;
            height: 1.2rem;
            border-width: 2px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <i class="bi bi-key"></i>
            <h3>Forgot Password?</h3>
            <p>We'll send you a reset link</p>
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

            <!-- Form -->
            <form action="{{ route('password.email') }}" method="POST" id="forgotForm">
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

                <div class="help-text">
                    <i class="bi bi-info-circle"></i> We'll send you a link to reset your password
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-login" id="submitBtn">
                    <span id="btnText">
                        <i class="bi bi-send"></i> Send Reset Link
                    </span>
                    <span id="btnLoading" style="display: none;">
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Sending...
                    </span>
                </button>
            </form>
        </div>

        <!-- Back Link -->
        <div class="back-link">
            <a href="{{ route('login') }}"><i class="bi bi-arrow-left"></i> Back to Login</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Form submission loading state
        const form = document.getElementById('forgotForm');
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
            const alerts = document.querySelectorAll('.alert-success');
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
