@extends('layouts.app')

@section('title', 'Two-Factor Authentication')
@section('page-title', 'Two-Factor Authentication')

@section('sidebar-menu')
    <a href="{{ route('admin.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('admin.bookings.pending') }}" class="nav-link">
        <i class="bi bi-hourglass-split"></i> Pending Queue
    </a>
    <a href="{{ route('admin.bookings.index') }}" class="nav-link">
        <i class="bi bi-calendar-check"></i> All Bookings
    </a>
    <a href="{{ route('admin.schedules.index') }}" class="nav-link">
        <i class="bi bi-calendar3"></i> Schedules
    </a>
    <a href="{{ route('admin.reports.today') }}" class="nav-link">
        <i class="bi bi-file-text"></i> Today's Report
    </a>
    <a href="{{ route('admin.statistics.index') }}" class="nav-link">
        <i class="bi bi-graph-up"></i> Statistics
    </a>

    @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('admin.departments.index') }}" class="nav-link">
            <i class="bi bi-hospital"></i> Departments
        </a>

        <hr style="border-color: rgba(255,255,255,0.2); margin: 1rem 1.5rem;">

        <a href="{{ route('super-admin.users.index') }}" class="nav-link">
            <i class="bi bi-people"></i> Manage Users
        </a>
        <a href="{{ route('super-admin.config.edit') }}" class="nav-link">
            <i class="bi bi-gear"></i> System Config
        </a>
        <a href="{{ route('2fa.setup') }}" class="nav-link active">
            <i class="bi bi-shield-lock"></i> Two-Factor Auth
        </a>
    @endif
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- 2FA Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-shield-lock"></i> Two-Factor Authentication Status
                    </h5>
                    @if($user->hasTwoFactorEnabled())
                        <span class="badge bg-success" style="font-size: 0.9rem;">
                            <i class="bi bi-check-circle"></i> Enabled
                        </span>
                    @else
                        <span class="badge bg-secondary" style="font-size: 0.9rem;">
                            <i class="bi bi-x-circle"></i> Disabled
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    @if($user->hasTwoFactorEnabled())
                        <div class="alert alert-success">
                            <i class="bi bi-shield-check"></i>
                            <strong>Two-Factor Authentication is Active</strong>
                            <p class="mb-0 mt-2">Your account is secured with two-factor authentication. You'll need to enter a code from your authenticator app when logging in.</p>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h6><i class="bi bi-info-circle"></i> What is 2FA?</h6>
                                <p class="text-muted">Two-factor authentication adds an extra layer of security to your account by requiring a verification code in addition to your password.</p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="bi bi-phone"></i> Authenticator App</h6>
                                <p class="text-muted">Use Google Authenticator, Microsoft Authenticator, or any compatible TOTP authenticator app to generate codes.</p>
                            </div>
                        </div>

                        <hr>

                        <!-- Recovery Codes -->
                        <div class="mt-4">
                            <h6><i class="bi bi-key"></i> Recovery Codes</h6>
                            <p class="text-muted">Recovery codes can be used to access your account if you lose access to your authenticator app.</p>

                            @if(count($recoveryCodes) > 0)
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <strong>You have {{ count($recoveryCodes) }} recovery code(s) remaining.</strong>
                                    <p class="mb-0 mt-2">Store these codes in a safe place. Each code can only be used once.</p>
                                </div>
                            @else
                                <div class="alert alert-danger">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <strong>You have no recovery codes remaining!</strong>
                                    <p class="mb-0 mt-2">Generate new recovery codes immediately to ensure you don't lose access to your account.</p>
                                </div>
                            @endif

                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#regenerateCodesModal">
                                <i class="bi bi-arrow-clockwise"></i> Regenerate Recovery Codes
                            </button>
                        </div>

                        <hr>

                        <!-- Trusted Devices -->
                        <div class="mt-4">
                            <h6><i class="bi bi-devices"></i> Trusted Devices</h6>
                            <p class="text-muted">Manage devices that can skip 2FA verification for 30 days.</p>
                            <a href="{{ route('2fa.trusted-devices') }}" class="btn btn-outline-info">
                                <i class="bi bi-gear"></i> Manage Trusted Devices
                            </a>
                        </div>

                        <hr>

                        <!-- Disable 2FA -->
                        <div class="mt-4">
                            <h6 class="text-danger"><i class="bi bi-x-circle"></i> Disable Two-Factor Authentication</h6>
                            <p class="text-muted">Disabling 2FA will reduce your account security. You'll only need your password to log in.</p>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#disable2FAModal">
                                <i class="bi bi-x-circle"></i> Disable 2FA
                            </button>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Enhance Your Account Security</strong>
                            <p class="mb-0 mt-2">Two-factor authentication is not currently enabled on your account. Enable it now to add an extra layer of security.</p>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h6><i class="bi bi-shield-check"></i> Benefits of 2FA</h6>
                                <ul class="text-muted">
                                    <li>Protects against password theft</li>
                                    <li>Adds an extra security layer</li>
                                    <li>Required for Super Admin accounts (optional)</li>
                                    <li>Works with popular authenticator apps</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="bi bi-clock-history"></i> How It Works</h6>
                                <ol class="text-muted">
                                    <li>Install an authenticator app on your phone</li>
                                    <li>Scan the QR code we provide</li>
                                    <li>Enter the 6-digit code to verify</li>
                                    <li>Save your recovery codes securely</li>
                                </ol>
                            </div>
                        </div>

                        <hr>

                        <form action="{{ route('2fa.enable') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-shield-lock"></i> Enable Two-Factor Authentication
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Regenerate Recovery Codes Modal -->
<div class="modal fade" id="regenerateCodesModal" tabindex="-1" aria-labelledby="regenerateCodesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="regenerateCodesModalLabel">
                    <i class="bi bi-arrow-clockwise"></i> Regenerate Recovery Codes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('2fa.recovery.regenerate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Warning:</strong> This will invalidate your existing recovery codes. You'll need to save the new codes.
                    </div>
                    <div class="mb-3">
                        <label for="password_regenerate" class="form-label">Confirm Your Password</label>
                        <input type="password" class="form-control" id="password_regenerate" name="password" required>
                        @error('password')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise"></i> Regenerate Codes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Disable 2FA Modal -->
<div class="modal fade" id="disable2FAModal" tabindex="-1" aria-labelledby="disable2FAModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="disable2FAModalLabel">
                    <i class="bi bi-x-circle"></i> Disable Two-Factor Authentication
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('2fa.disable') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Are you sure?</strong> Disabling 2FA will reduce your account security.
                    </div>
                    <p>This will:</p>
                    <ul>
                        <li>Remove 2FA requirement from your account</li>
                        <li>Delete all recovery codes</li>
                        <li>Remove all trusted devices</li>
                    </ul>
                    <div class="mb-3">
                        <label for="password_disable" class="form-label">Confirm Your Password</label>
                        <input type="password" class="form-control" id="password_disable" name="password" required>
                        @error('password')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Disable 2FA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
