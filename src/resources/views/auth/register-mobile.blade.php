<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Pharmacy Booking System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>

    <style>
        body {
            /* Your Theme: primary-light to primary-dark */
            background: linear-gradient(135deg, #6c8cef 0%, #224abe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 20px 60px 20px; /* Added bottom padding for mobile browsers */
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .register-header {
            /* Your Theme: primary-color to primary-dark */
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .register-body {
            padding: 30px 20px 10px 20px;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 4px rgba(78, 115, 223, 0.1);
        }

        /* Password Toggle Styles */
        .position-relative { position: relative; }
        .toggle-password {
            position: absolute;
            top: 19px;
            right: 15px;
            cursor: pointer;
            font-size: 1.2rem;
            color: #6c757d;
            z-index: 10;
        }

        .btn-register {
            width: 100%;
            height: 58px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 12px;
            background: #4e73df;
            border: none;
            color: white;
            margin-top: 10px;
            transition: all 0.2s;
        }

        .btn-register:hover {
            background: #224abe;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
        }

        .login-link {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            margin: 0 -20px 0 -20px; /* Fixed: Removed negative bottom margin */
            border-radius: 0 0 20px 20px;
            border-top: 1px solid #eee;
        }

        .login-link a {
            color: #4e73df;
            font-weight: 600;
            text-decoration: none;
        }

        .h-captcha {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        @media (max-width: 767.98px) {
            body { align-items: flex-start; }
            .h-captcha { transform: scale(0.9); }
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="register-header">
        <h2 class="mb-1">Create Account</h2>
        <p class="mb-0 opacity-75">Join our pharmacy network</p>
    </div>

    <div class="register-body">
        <form action="{{ route('register') }}" method="POST" id="registerForm">
            @csrf

		@if ($errors->any())
		    <div class="alert alert-danger alert-dismissible fade show">
		        <strong>Registration failed:</strong>
		        <ul class="mb-0 mt-2">
		            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

            <div class="form-floating">
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Full Name" required>
                <label for="name"><i class="bi bi-person"></i> Full Name</label>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-floating">
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Email" inputmode="email" required>
                <label for="email"><i class="bi bi-envelope"></i> Email Address</label>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

	    <div class="form-floating">
		<input type="text" class="form-control @error('company') is-invalid @enderror" id="company" name="company" value="{{ old('company') }}" placeholder="Company Name" required>
		<label for="company"><i class="bi bi-building"></i> Company Name</label>
		@error('company') <div class="invalid-feedback">{{ $message }}</div> @enderror
	   </div>

            <div class="form-floating">
                <input type="text" class="form-control @error('civil_id') is-invalid @enderror" id="civil_id" name="civil_id" value="{{ old('civil_id') }}" placeholder="Civil ID" inputmode="numeric" pattern="[0-9]{12}" maxlength="12" required>
                <label for="civil_id"><i class="bi bi-credit-card"></i> Civil ID</label>
                @error('civil_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small class="text-muted">Enter 12 digits</small>
            </div>

            <div class="form-floating">
                <input type="tel" class="form-control @error('mobile_number') is-invalid @enderror" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" placeholder="Mobile Number" inputmode="numeric" pattern="[0-9]{8}" maxlength="8" required>
                <label for="mobile_number"><i class="bi bi-phone"></i> Mobile Number (8 digits)</label>
                @error('mobile_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small class="text-muted">Enter exactly 8 digits</small>
            </div>

            <div class="form-floating position-relative">
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" style="padding-right: 50px;" required>
                <label for="password"><i class="bi bi-lock"></i> Password</label>
                <i class="bi bi-eye-slash toggle-password" id="togglePassword"></i>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small class="text-muted">Must contain uppercase, lowercase and numbers</small>
            </div>

            <div class="form-floating position-relative">
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" style="padding-right: 50px;" required>
                <label for="password_confirmation"><i class="bi bi-lock-fill"></i> Confirm Password</label>
                <i class="bi bi-eye-slash toggle-password" id="toggleConfirm"></i>
            </div>

            <div class="h-captcha" data-sitekey="{{ config('services.hcaptcha.site_key') }}"></div>
            @error('h-captcha-response') <div class="text-danger small text-center mb-3">{{ $message }}</div> @enderror

            <button type="submit" class="btn btn-register" id="submitBtn">
                <span id="btnText">Create Account</span>
                <span id="btnLoading" class="spinner-border spinner-border-sm" style="display: none;"></span>
            </button>
        </form>

        <div class="login-link">
            Already have an account? <a href="{{ route('login') }}">Login here</a>
        </div>
    </div>
</div>

<script>
    // 1. Password Visibility Toggle Logic
    function initPasswordToggle(iconId, inputId) {
        const icon = document.getElementById(iconId);
        const input = document.getElementById(inputId);
        icon.addEventListener('click', () => {
            const type = input.type === 'password' ? 'text' : 'password';
            input.type = type;
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    }
    initPasswordToggle('togglePassword', 'password');
    initPasswordToggle('toggleConfirm', 'password_confirmation');

    // 2. Form Submission Loading State
    const form = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');

form.addEventListener('submit', function(e) {
    // Allow form to submit - server will validate hCaptcha
    submitBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline-block';
    });
</script>

</body>
</html>
