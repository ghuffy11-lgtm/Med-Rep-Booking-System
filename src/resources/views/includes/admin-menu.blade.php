{{-- Admin Menu - Shared by Pharmacy Admin & Super Admin --}}
<a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2"></i>
    <span>Dashboard</span>
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
