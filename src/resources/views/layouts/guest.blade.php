<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') - Pharmacy Booking System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #13a0d5 0%, #a2b2e3 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .auth-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        
        .auth-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .auth-header {
            background: linear-gradient(135deg, #13a0d5 0%, #224abe 100%);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
        }
        
        .auth-header h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.75rem;
        }
        
        .auth-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        .auth-body {
            padding: 2.5rem 2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #13a0d5 0%, #224abe 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.25rem;
        }
        
        .invalid-feedback {
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        
        .auth-footer {
            text-align: center;
            padding: 1.5rem 2rem;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        
        .auth-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .logo-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .form-check-input:checked {
            background-color: #13a0d5;
            border-color: #667eea;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
		<div class="logo-icon">
		    <img src="{{ asset('images/logo.svg') }}" alt="System Logo" style="width: 80px; height: 80px; object-fit: contain;">
		</div>
                <h3>@yield('header-title', 'Pharmacy Booking')</h3>
                <p>@yield('header-subtitle', 'Representative Appointment System')</p>
            </div>
            
            <div class="auth-body">
                @if(session('success'))
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        {{ session('warning') }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i>
                        {{ session('info') }}
                    </div>
                @endif

                @yield('content')
            </div>
            
            @hasSection('footer')
                <div class="auth-footer">
                    @yield('footer')
                </div>
            @endif
        </div>
        
        <div class="text-center mt-4">
            <p class="text-white mb-0">
                <small>&copy; {{ date('Y') }} Pharmacy Booking System. All rights reserved.</small>
            </p>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
