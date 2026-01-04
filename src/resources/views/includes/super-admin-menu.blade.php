{{-- Super Admin Exclusive Menu --}}
<div style="padding: 0.5rem 1.5rem; margin-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
    <small style="opacity: 0.6; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px;">Super Admin</small>
</div>

<a href="{{ route('super-admin.users.index') }}" class="nav-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}">
    <i class="bi bi-people"></i>
    <span>Manage Users</span>
</a>

<a href="{{ route('super-admin.config.edit') }}" class="nav-link {{ request()->routeIs('super-admin.config.*') ? 'active' : '' }}">
    <i class="bi bi-gear"></i>
    <span>System Config</span>
</a>
