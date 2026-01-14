@extends('layouts.app')

@section('title', 'Enable 2FA')
@section('page-title', 'Enable Two-Factor Authentication')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="m-0">
                        <i class="bi bi-shield-lock"></i> Setup Two-Factor Authentication
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-phone" style="font-size: 3rem; color: #4e73df;"></i>
                        <h4 class="mt-3">Scan QR Code</h4>
                        <p class="text-muted">Use your authenticator app to scan this QR code</p>
                    </div>

                    <!-- QR Code -->
                    <div class="text-center mb-4">
                        <div class="p-4 bg-white border rounded d-inline-block">
                            {!! $qrCodeSvg !!}
                        </div>
                    </div>

                    <!-- Manual Entry -->
                    <div class="alert alert-info">
                        <strong><i class="bi bi-info-circle"></i> Can't scan the code?</strong>
                        <p class="mb-2 mt-2">Enter this secret key manually in your authenticator app:</p>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace" value="{{ $secret }}" id="secretKey" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copySecret()">
                                <i class="bi bi-clipboard"></i> Copy
                            </button>
                        </div>
                    </div>

                    <hr>

                    <!-- Verification Form -->
                    <div class="mt-4">
                        <h6><i class="bi bi-check-circle"></i> Verify Setup</h6>
                        <p class="text-muted">Enter the 6-digit code from your authenticator app to complete setup.</p>

                        <form action="{{ route('2fa.confirm') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="one_time_password" class="form-label">Verification Code</label>
                                <input type="text"
                                       class="form-control form-control-lg text-center font-monospace @error('one_time_password') is-invalid @enderror"
                                       id="one_time_password"
                                       name="one_time_password"
                                       maxlength="6"
                                       placeholder="000000"
                                       pattern="[0-9]{6}"
                                       inputmode="numeric"
                                       required
                                       autofocus
                                       style="letter-spacing: 0.5rem; font-size: 1.5rem;">
                                @error('one_time_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle"></i> Verify and Enable 2FA
                                </button>
                                <a href="{{ route('2fa.setup') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Instructions -->
                    <div class="mt-4">
                        <h6><i class="bi bi-question-circle"></i> Need Help?</h6>
                        <div class="accordion" id="helpAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                        Which authenticator app should I use?
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                    <div class="accordion-body">
                                        <p>We recommend:</p>
                                        <ul>
                                            <li><strong>Google Authenticator</strong> (iOS/Android)</li>
                                            <li><strong>Microsoft Authenticator</strong> (iOS/Android)</li>
                                            <li><strong>Authy</strong> (iOS/Android/Desktop)</li>
                                        </ul>
                                        <p class="mb-0">Any TOTP-compatible authenticator app will work.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                        What happens after I enable 2FA?
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                    <div class="accordion-body">
                                        <p>After enabling 2FA:</p>
                                        <ol>
                                            <li>You'll receive 8 recovery codes - save them safely</li>
                                            <li>Next time you log in, you'll need your password + 6-digit code</li>
                                            <li>You can choose to trust devices for 30 days</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copySecret() {
    const secretKey = document.getElementById('secretKey');
    secretKey.select();
    secretKey.setSelectionRange(0, 99999); // For mobile devices
    document.execCommand('copy');

    // Show feedback
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check"></i> Copied!';
    btn.classList.remove('btn-outline-secondary');
    btn.classList.add('btn-success');

    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-secondary');
    }, 2000);
}

// Auto-format code input
document.getElementById('one_time_password').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
});
</script>
@endsection
