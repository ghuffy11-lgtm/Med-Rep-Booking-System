@extends('layouts.app')

@section('title', 'Trusted Devices')
@section('page-title', 'Manage Trusted Devices')

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
        <a href="{{ route('2fa.setup') }}" class="nav-link">
            <i class="bi bi-shield-lock"></i> Two-Factor Auth
        </a>
    @endif
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('2fa.setup') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to 2FA Settings
                </a>
            </div>

            <!-- Trusted Devices Card -->
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-devices"></i> Trusted Devices
                    </h5>
                    @if($trustedDevices->count() > 0)
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#revokeAllModal">
                            <i class="bi bi-trash"></i> Revoke All
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>What are trusted devices?</strong>
                        <p class="mb-0 mt-2">Trusted devices can skip 2FA verification for 30 days. After 30 days, you'll need to verify again. You can revoke trust from any device at any time.</p>
                    </div>

                    @if($trustedDevices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;"><i class="bi bi-device-hdd"></i></th>
                                        <th style="width: 30%;">Device</th>
                                        <th style="width: 20%;">IP Address</th>
                                        <th style="width: 20%;">Last Used</th>
                                        <th style="width: 15%;">Expires</th>
                                        <th style="width: 10%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trustedDevices as $device)
                                        <tr>
                                            <td class="text-center">
                                                @if(str_contains(strtolower($device->user_agent), 'mobile') || str_contains(strtolower($device->user_agent), 'android') || str_contains(strtolower($device->user_agent), 'iphone'))
                                                    <i class="bi bi-phone text-primary" style="font-size: 1.5rem;"></i>
                                                @else
                                                    <i class="bi bi-laptop text-info" style="font-size: 1.5rem;"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $device->device_name ?: 'Unknown Device' }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    {{ Str::limit($device->user_agent, 50) }}
                                                </small>
                                            </td>
                                            <td>
                                                <code>{{ $device->ip_address }}</code>
                                            </td>
                                            <td>
                                                <i class="bi bi-clock"></i>
                                                {{ $device->last_used_at->diffForHumans() }}
                                            </td>
                                            <td>
                                                @php
                                                    $daysLeft = now()->diffInDays($device->expires_at, false);
                                                @endphp
                                                @if($daysLeft > 7)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-calendar-check"></i> {{ (int)$daysLeft }} days
                                                    </span>
                                                @elseif($daysLeft > 3)
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-calendar-event"></i> {{ (int)$daysLeft }} days
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-calendar-x"></i> {{ (int)$daysLeft }} days
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('2fa.trusted-devices.revoke', $device->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to revoke trust from this device?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Revoke Trust">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-device-hdd" style="font-size: 4rem; color: #ccc;"></i>
                            <h5 class="mt-3 text-muted">No Trusted Devices</h5>
                            <p class="text-muted">You haven't trusted any devices yet. When you log in, you can choose to trust a device for 30 days.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Security Tips -->
            <div class="card mt-4 border-warning">
                <div class="card-header bg-warning text-dark">
                    <i class="bi bi-shield-exclamation"></i> Security Recommendations
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li><strong>Review regularly:</strong> Check this list periodically and revoke devices you don't recognize</li>
                        <li><strong>Lost device?</strong> Immediately revoke trust if you lose a device</li>
                        <li><strong>Public computers:</strong> Never trust public or shared computers</li>
                        <li><strong>Different locations:</strong> If you see logins from unexpected locations, revoke all devices and change your password</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revoke All Modal -->
<div class="modal fade" id="revokeAllModal" tabindex="-1" aria-labelledby="revokeAllModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="revokeAllModalLabel">
                    <i class="bi bi-trash"></i> Revoke All Trusted Devices
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('2fa.trusted-devices.revoke-all') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Are you sure?</strong> This will revoke trust from all devices.
                    </div>
                    <p>This will:</p>
                    <ul>
                        <li>Remove all trusted devices ({{ $trustedDevices->count() }} device{{ $trustedDevices->count() !== 1 ? 's' : '' }})</li>
                        <li>Require 2FA verification on all devices next time you log in</li>
                    </ul>
                    <div class="mb-3">
                        <label for="password_revoke_all" class="form-label">Confirm Your Password</label>
                        <input type="password" class="form-control" id="password_revoke_all" name="password" required>
                        @error('password')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Revoke All Devices
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
