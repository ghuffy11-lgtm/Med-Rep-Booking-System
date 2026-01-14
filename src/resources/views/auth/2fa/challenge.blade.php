<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .auth-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .code-input {
            letter-spacing: 1rem;
            font-size: 2rem;
            text-align: center;
            font-family: 'Courier New', monospace;
        }
        .recovery-link {
            cursor: pointer;
            color: #667eea;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="auth-card">
                    <div class="auth-header">
                        <i class="bi bi-shield-lock" style="font-size: 3rem;"></i>
                        <h3 class="mt-3 mb-0">Two-Factor Authentication</h3>
                        <p class="mb-0 mt-2 opacity-75">Enter the verification code</p>
                    </div>

                    <div class="p-4">
                        @if(session('info'))
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> {{ session('info') }}
                            </div>
                        @endif

                        <!-- Verification Code Form -->
                        <div id="codeForm">
                            <p class="text-center text-muted mb-4">
                                <i class="bi bi-phone"></i> Open your authenticator app and enter the 6-digit code
                            </p>

                            <form action="{{ route('2fa.verify') }}" method="POST">
                                @csrf

                                <div class="mb-4">
                                    <input type="text"
                                           class="form-control code-input @error('one_time_password') is-invalid @enderror"
                                           name="one_time_password"
                                           id="one_time_password"
                                           maxlength="6"
                                           placeholder="000000"
                                           pattern="[0-9]{6}"
                                           inputmode="numeric"
                                           required
                                           autofocus>
                                    @error('one_time_password')
                                        <div class="invalid-feedback text-center">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" name="trust_device" id="trust_device" value="1">
                                    <label class="form-check-label" for="trust_device">
                                        <i class="bi bi-shield-check"></i> Trust this device for 30 days
                                    </label>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-check-circle"></i> Verify Code
                                    </button>
                                </div>
                            </form>

                            <div class="text-center mt-4">
                                <p class="text-muted small">
                                    Lost access to your authenticator?<br>
                                    <a href="#" class="recovery-link" onclick="showRecoveryForm(); return false;">
                                        Use a recovery code instead
                                    </a>
                                </p>
                            </div>
                        </div>

                        <!-- Recovery Code Form (Hidden by default) -->
                        <div id="recoveryForm" style="display: none;">
                            <p class="text-center text-muted mb-4">
                                <i class="bi bi-key"></i> Enter one of your recovery codes
                            </p>

                            <form action="{{ route('2fa.verify.recovery') }}" method="POST">
                                @csrf

                                <div class="mb-4">
                                    <input type="text"
                                           class="form-control text-center font-monospace @error('recovery_code') is-invalid @enderror"
                                           name="recovery_code"
                                           id="recovery_code"
                                           placeholder="XXXXXXXXXX"
                                           style="letter-spacing: 0.2rem; font-size: 1.2rem;"
                                           required>
                                    @error('recovery_code')
                                        <div class="invalid-feedback text-center">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text text-center">
                                        Recovery codes are case-insensitive
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-warning btn-lg">
                                        <i class="bi bi-key"></i> Use Recovery Code
                                    </button>
                                </div>
                            </form>

                            <div class="text-center mt-4">
                                <a href="#" class="recovery-link" onclick="showCodeForm(); return false;">
                                    <i class="bi bi-arrow-left"></i> Back to verification code
                                </a>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Login
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <p class="text-white small">
                        <i class="bi bi-shield-check"></i> Your account is protected with two-factor authentication
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-format code input
        document.getElementById('one_time_password').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        });

        function showRecoveryForm() {
            document.getElementById('codeForm').style.display = 'none';
            document.getElementById('recoveryForm').style.display = 'block';
            document.getElementById('recovery_code').focus();
        }

        function showCodeForm() {
            document.getElementById('recoveryForm').style.display = 'none';
            document.getElementById('codeForm').style.display = 'block';
            document.getElementById('one_time_password').focus();
        }
    </script>
</body>
</html>
