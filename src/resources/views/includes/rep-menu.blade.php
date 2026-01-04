{{-- Representative Menu --}}
<a href="{{ route('rep.dashboard') }}" class="nav-link {{ request()->routeIs('rep.dashboard') ? 'active' : '' }}">
    <i class="bi bi-house-door"></i>
    <span>Dashboard</span>
</a>

<a href="{{ route('rep.bookings.create') }}" class="nav-link {{ request()->routeIs('rep.bookings.create') ? 'active' : '' }}">
    <i class="bi bi-plus-circle"></i>
    <span>New Booking</span>
</a>

<a href="{{ route('rep.bookings.index') }}" class="nav-link {{ request()->routeIs('rep.bookings.index') ? 'active' : '' }}">
    <i class="bi bi-list-ul"></i>
    <span>My Bookings</span>
</a>

<a href="{{ route('rep.bookings.history') }}" class="nav-link {{ request()->routeIs('rep.bookings.history') ? 'active' : '' }}">
    <i class="bi bi-clock-history"></i>
    <span>History</span>
</a>

<a href="{{ route('rep.profile.edit') }}" class="nav-link {{ request()->routeIs('rep.profile.*') ? 'active' : '' }}">
    <i class="bi bi-person-circle"></i>
    <span>My Profile</span>
</a>
