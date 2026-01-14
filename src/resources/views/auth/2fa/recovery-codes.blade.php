@extends('layouts.app')

@section('title', 'Recovery Codes')
@section('page-title', 'Two-Factor Authentication Recovery Codes')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="m-0">
                        <i class="bi bi-check-circle"></i> Two-Factor Authentication Enabled!
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-success">
                        <i class="bi bi-shield-check"></i>
                        <strong>Success!</strong> Two-factor authentication has been successfully enabled on your account.
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Important:</strong> Save these recovery codes in a safe place. You'll need them to access your account if you lose your authenticator device.
                    </div>

                    <div class="text-center mb-4">
                        <i class="bi bi-key" style="font-size: 3rem; color: #ffc107;"></i>
                        <h4 class="mt-3">Your Recovery Codes</h4>
                        <p class="text-muted">Each code can only be used once</p>
                    </div>

                    <!-- Recovery Codes Display -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <div class="row">
                                @foreach($recoveryCodes as $index => $code)
                                    <div class="col-md-6 mb-2">
                                        <div class="p-3 bg-white border rounded text-center font-monospace" style="letter-spacing: 0.2rem; font-size: 1.1rem;">
                                            <strong>{{ $index + 1 }}.</strong> {{ $code }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 mb-4">
                        <button type="button" class="btn btn-primary btn-lg" onclick="printCodes()">
                            <i class="bi bi-printer"></i> Print Recovery Codes
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="copyCodes()">
                            <i class="bi bi-clipboard"></i> Copy to Clipboard
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="downloadCodes()">
                            <i class="bi bi-download"></i> Download as Text File
                        </button>
                    </div>

                    <!-- Security Tips -->
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <i class="bi bi-lightbulb"></i> Security Tips
                        </div>
                        <div class="card-body">
                            <ul class="mb-0">
                                <li><strong>Store securely:</strong> Keep these codes in a password manager or secure location</li>
                                <li><strong>One-time use:</strong> Each recovery code can only be used once</li>
                                <li><strong>Regenerate anytime:</strong> You can generate new codes from your security settings</li>
                                <li><strong>Don't share:</strong> Never share these codes with anyone</li>
                            </ul>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="text-muted mb-3">
                            <i class="bi bi-check-circle"></i> I've saved my recovery codes safely
                        </p>
                        <a href="{{ route('2fa.setup') }}" class="btn btn-success btn-lg">
                            <i class="bi bi-arrow-right"></i> Continue to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden div for printing -->
<div id="printable" style="display: none;">
    <h2>{{ config('app.name') }} - Recovery Codes</h2>
    <p>Account: {{ auth()->user()->email }}</p>
    <p>Generated: {{ now()->format('F j, Y g:i A') }}</p>
    <hr>
    <h3>Recovery Codes (save these in a secure location):</h3>
    <ol>
        @foreach($recoveryCodes as $code)
            <li style="font-family: monospace; font-size: 14pt; margin: 10px 0;">{{ $code }}</li>
        @endforeach
    </ol>
    <hr>
    <p><strong>Important:</strong> Each code can only be used once. Store these codes securely.</p>
</div>

<script>
function copyCodes() {
    const codes = @json($recoveryCodes);
    const text = codes.join('\n');

    navigator.clipboard.writeText(text).then(() => {
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check"></i> Copied to Clipboard!';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-success');

        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-primary');
        }, 2000);
    });
}

function printCodes() {
    const printContent = document.getElementById('printable').innerHTML;
    const originalContent = document.body.innerHTML;

    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload(); // Reload to restore event listeners
}

function downloadCodes() {
    const codes = @json($recoveryCodes);
    const text = `{{ config('app.name') }} - Recovery Codes\n` +
                 `Account: {{ auth()->user()->email }}\n` +
                 `Generated: {{ now()->format('F j, Y g:i A') }}\n\n` +
                 `Recovery Codes (save these in a secure location):\n\n` +
                 codes.map((code, index) => `${index + 1}. ${code}`).join('\n') +
                 `\n\nImportant: Each code can only be used once. Store these codes securely.`;

    const blob = new Blob([text], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = '2fa-recovery-codes-' + Date.now() + '.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>
@endsection
