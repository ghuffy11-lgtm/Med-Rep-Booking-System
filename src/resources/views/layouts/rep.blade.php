<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Representative Dashboard') - Pharmacy Booking</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Flatpickr CSS (Date Picker) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <!-- Mobile Responsive CSS for Representatives -->
    <link rel="stylesheet" href="{{ asset('css/mobile-rep.css') }}">

    <!-- Centralized Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    
    <style>
        /* Base layout styles */
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        /* Sidebar styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            /* Background controlled by theme.css */
            color: white;
            padding: 20px;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar .brand {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        /* Main content area */
        .main-content {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
        }

        /* Page header */
        .page-header {
            background: white;
            padding: 20px 30px;
            margin: -30px -30px 30px -30px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h4 {
            margin: 0;
            color: #495057;
        }

        /* Mobile hamburger menu (hidden on desktop) */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        /* Mobile overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        /* Mobile responsive adjustments */
        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                width: 280px;
                z-index: 1001;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-overlay.show {
                display: block;
            }

            .main-content {
                margin-left: 0;
                padding: 20px 16px;
            }

            .page-header {
                margin: -20px -16px 20px -16px;
                padding: 60px 16px 16px 16px;
            }

            .mobile-menu-toggle {
                display: block;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="rep-mobile-view has-bottom-nav">
    
    <!-- Mobile Menu Toggle Button -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="bi bi-list"></i>
    </button>

    <!-- Sidebar Overlay (mobile only) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="brand">
            <i class="bi bi-capsule"></i> Med. Rep. Appointment System
        </div>

        <nav>
            @yield('sidebar-menu', '')
            
            @if(!isset($hideSidebar) || !$hideSidebar)
            <a href="{{ route('rep.dashboard') }}" class="nav-link {{ request()->routeIs('rep.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('rep.bookings.create') }}" class="nav-link {{ request()->routeIs('rep.bookings.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i> New Booking
            </a>
            <a href="{{ route('rep.bookings.index') }}" class="nav-link {{ request()->routeIs('rep.bookings.index') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> My Bookings
            </a>
            <a href="{{ route('rep.bookings.history') }}" class="nav-link {{ request()->routeIs('rep.bookings.history') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> History
            </a>
            <a href="{{ route('rep.profile.edit') }}" class="nav-link {{ request()->routeIs('rep.profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i> Profile
            </a>

            <hr style="border-color: rgba(255,255,255,0.2); margin: 20px 0;">

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link" style="border: none; background: none; width: 100%; text-align: left;">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
            @endif
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h4>@yield('page-title', 'Dashboard')</h4>
            <div class="user-info mobile-hide">
                <small class="text-muted">{{ auth()->user()->name }}</small>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle"></i>
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </div>

    <!-- Optional: Bottom Navigation for Mobile -->
    @if(!isset($hideBottomNav) || !$hideBottomNav)
    <nav class="rep-mobile-nav-bottom mobile-only">
        <a href="{{ route('rep.dashboard') }}" class="nav-item">
            <div class="nav-link {{ request()->routeIs('rep.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i>
                <span>Home</span>
            </div>
        </a>
        <a href="{{ route('rep.bookings.create') }}" class="nav-item">
            <div class="nav-link {{ request()->routeIs('rep.bookings.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Book</span>
            </div>
        </a>
        <a href="{{ route('rep.bookings.index') }}" class="nav-item">
            <div class="nav-link {{ request()->routeIs('rep.bookings.index') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i>
                <span>Bookings</span>
            </div>
        </a>
        <a href="{{ route('rep.profile.edit') }}" class="nav-item">
            <div class="nav-link {{ request()->routeIs('rep.profile.*') ? 'active' : '' }}">
                <i class="bi bi-person"></i>
                <span>Profile</span>
            </div>
        </a>
    </nav>
    @endif

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Flatpickr JS (Date Picker) -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const menuToggle = document.getElementById('mobileMenuToggle');

            // Toggle sidebar on mobile
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            });

            // Close sidebar when clicking overlay
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });

            // Close sidebar when clicking a link (mobile only)
            if (window.innerWidth < 768) {
                document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                    link.addEventListener('click', function() {
                        sidebar.classList.remove('show');
                        overlay.classList.remove('show');
                    });
                });
            }
        });

        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>

    @stack('scripts')
</body>
</html>
