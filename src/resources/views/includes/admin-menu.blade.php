{{-- Admin Menu - Shared by Pharmacy Admin & Super Admin --}}
<a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2"></i>
    <span>Dashboard</span>
</a>

<a href="{{ route('admin.statistics.index') }}" class="nav-link {{ request()->routeIs('admin.statistics.*') ? 'active' : '' }}">
    <i class="bi bi-graph-up"></i>
    <span>Statistics</span>
</a>

<a href="{{ route('admin.bookings.pending') }}" class="nav-link {{ request()->routeIs('admin.bookings.pending') ? 'active' : '' }}">
    <i class="bi bi-clock-history"></i>
    <span>Pending Approvals</span>
    @php
        $pendingCount = \App\Models\Booking::where('status', 'pending')->count();
    @endphp
    @if($pendingCount > 0)
        <span class="badge bg-danger ms-auto">{{ $pendingCount }}</span>
    @endif
</a>

<a href="{{ route('admin.bookings.index') }}" class="nav-link {{ request()->routeIs('admin.bookings.index') ? 'active' : '' }}">
    <i class="bi bi-calendar-check"></i>
    <span>All Bookings</span>
</a>

<a href="{{ route('admin.departments.index') }}" class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
    <i class="bi bi-building"></i>
    <span>Departments</span>
</a>

<a href="{{ route('admin.schedules.index') }}" class="nav-link {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
    <i class="bi bi-calendar3"></i>
    <span>Schedules</span>
</a>

<div style="padding: 0.5rem 1.5rem; margin-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
    <small style="opacity: 0.6; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Security</small>
</div>

<a href="{{ route('2fa.setup') }}" class="nav-link {{ request()->routeIs('2fa.*') ? 'active' : '' }}">
    <i class="bi bi-shield-lock"></i>
    <span>Two-Factor Auth (2FA)</span>
</a>
