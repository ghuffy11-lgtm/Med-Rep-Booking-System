# Development Guide

## Table of Contents
1. [Introduction](#introduction)
2. [Development Environment Setup](#development-environment-setup)
3. [Project Structure](#project-structure)
4. [Coding Standards](#coding-standards)
5. [Git Workflow](#git-workflow)
6. [Database Development](#database-development)
7. [Frontend Development](#frontend-development)
8. [Testing](#testing)
9. [Debugging](#debugging)
10. [API Development](#api-development)
11. [Deployment](#deployment)
12. [Contributing](#contributing)

---

## Introduction

This guide is for developers working on the Medical Representative Booking System. It covers setup, standards, workflows, and best practices for contributing to the project.

**Technology Stack**:
- Backend: Laravel 10.x (PHP 8.2+)
- Frontend: Bootstrap 5.3.0, Vanilla JavaScript
- Database: MySQL 8.0
- Infrastructure: Docker, Nginx

**Prerequisites**:
- PHP 8.2 or higher
- Composer
- Node.js & NPM (for asset compilation)
- Docker & Docker Compose
- Git
- IDE (VS Code, PhpStorm recommended)

---

## Development Environment Setup

### 1. Clone Repository

```bash
git clone https://github.com/your-org/Med-Rep-Booking-System.git
cd Med-Rep-Booking-System
```

### 2. Start Docker Containers

```bash
docker-compose up -d
```

This starts:
- Nginx (port 8080)
- PHP-FPM (PHP 8.2)
- MySQL (port 3307 external, 3306 internal)

### 3. Install PHP Dependencies

```bash
docker exec -it pharmacy_php composer install
```

### 4. Configure Environment

```bash
cd src
cp .env.example .env
```

Edit `.env` with your local settings:
```env
APP_NAME="Pharmacy Booking"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080
APP_TIMEZONE=Asia/Kuwait

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=pharmacy_db
DB_USERNAME=pharmacy_user
DB_PASSWORD=pharmacy_pass

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@pharmacy.local"
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Generate Application Key

```bash
docker exec -it pharmacy_php php artisan key:generate
```

### 6. Run Migrations

```bash
docker exec -it pharmacy_php php artisan migrate
```

### 7. Seed Database (Optional)

```bash
docker exec -it pharmacy_php php artisan db:seed
```

This creates:
- Super Admin user (admin@example.com)
- Sample pharmacies
- Sample departments
- Sample schedules

### 8. Access Application

Open browser: `http://localhost:8080`

**Default Super Admin**:
- Email: admin@example.com
- Password: Check seeder file or set in .env

### 9. IDE Setup

#### VS Code Extensions

Install these extensions:
```
- PHP Intelephense
- Laravel Extra Intellisense
- Laravel Blade Snippets
- Docker
- GitLens
- ESLint
- Prettier
```

#### PhpStorm

1. Install Laravel plugin
2. Enable Laravel support: Settings → PHP → Laravel → Enable
3. Configure PHP interpreter to use Docker PHP container
4. Configure database connection

### 10. Set Up File Permissions

```bash
docker exec -it pharmacy_php chmod -R 775 storage bootstrap/cache
docker exec -it pharmacy_php chown -R www-data:www-data storage bootstrap/cache
```

---

## Project Structure

### Overview

```
Med-Rep-Booking-System/
├── docker/                     # Docker configuration
│   ├── nginx/
│   └── php/
├── src/                       # Laravel application root
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/   # Application controllers
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── BookingController.php
│   │   │   │   ├── DepartmentController.php
│   │   │   │   └── PharmacyController.php
│   │   │   ├── Middleware/    # Custom middleware
│   │   │   │   ├── RoleMiddleware.php
│   │   │   │   └── SecurityHeaders.php
│   │   │   └── Kernel.php
│   │   ├── Models/            # Eloquent models
│   │   │   ├── User.php
│   │   │   ├── Booking.php
│   │   │   ├── Department.php
│   │   │   └── Pharmacy.php
│   │   ├── Services/          # Business logic services
│   │   │   ├── AuditLogService.php
│   │   │   └── ValidationRuleService.php
│   │   └── Providers/
│   ├── config/                # Configuration files
│   ├── database/
│   │   ├── migrations/        # Database migrations
│   │   └── seeders/           # Database seeders
│   ├── resources/
│   │   └── views/             # Blade templates
│   │       ├── layouts/
│   │       ├── auth/
│   │       ├── bookings/
│   │       └── departments/
│   ├── routes/
│   │   ├── web.php            # Web routes
│   │   └── api.php            # API routes
│   ├── public/                # Public assets
│   ├── storage/               # Storage (logs, cache, uploads)
│   └── tests/                 # Unit and feature tests
├── Docs/                      # Documentation
└── docker-compose.yml         # Docker orchestration
```

### Key Directories

**app/Http/Controllers/**:
- Handle HTTP requests
- Validate input
- Call service/model methods
- Return views or JSON responses

**app/Models/**:
- Eloquent ORM models
- Define relationships
- Define scopes and accessors
- Business logic (model-specific)

**app/Services/**:
- Reusable business logic
- Complex operations
- Third-party integrations

**app/Http/Middleware/**:
- Request/response filtering
- Authentication/authorization
- Security headers

**resources/views/**:
- Blade templates
- Layout files
- Component files

**database/migrations/**:
- Database schema changes
- Version controlled
- Up/down methods

**routes/web.php**:
- Web application routes
- Grouped by functionality
- Middleware applied

---

## Coding Standards

### PHP Standards (PSR-12)

Follow PSR-12 coding style standard.

**Namespace and Use Statements**:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
```

**Class Naming**:
```php
// Controllers: Singular + Controller suffix
class BookingController extends Controller

// Models: Singular, PascalCase
class Booking extends Model

// Services: Descriptive + Service suffix
class ValidationRuleService
```

**Method Naming**:
```php
// camelCase for methods
public function createBooking()
public function getAvailableSlots()

// Verb-first for actions
public function store()    // Create/save
public function update()   // Update existing
public function destroy()  // Delete
```

**Variable Naming**:
```php
// camelCase for variables
$userId = Auth::id();
$bookingDate = $request->input('booking_date');

// Descriptive names
$isActive = true;            // Good
$flag = true;                // Bad

$userBookings = Booking::where(...);  // Good
$data = Booking::where(...);          // Bad
```

**Constants**:
```php
// UPPER_SNAKE_CASE for constants
const MAX_BOOKING_ADVANCE_DAYS = 30;
const DEFAULT_SLOT_DURATION = 30;
```

**Comments**:
```php
/**
 * Create a new booking.
 *
 * @param Request $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function store(Request $request)
{
    // Validate input
    $validated = $request->validate([...]);

    // Create booking
    $booking = Booking::create($validated);

    // Log action
    AuditLogService::log('booking_created', auth()->id());

    return redirect()->route('bookings.index')
        ->with('success', 'Booking created successfully');
}
```

### Laravel Best Practices

**Use Eloquent ORM**:
```php
// Good: Eloquent
$users = User::where('is_active', 1)->get();

// Bad: Raw SQL (unless necessary)
$users = DB::select('SELECT * FROM users WHERE is_active = 1');
```

**Use Route Model Binding**:
```php
// routes/web.php
Route::get('/bookings/{booking}', [BookingController::class, 'show']);

// BookingController.php
public function show(Booking $booking)
{
    // $booking is automatically loaded
    return view('bookings.show', compact('booking'));
}
```

**Use Form Requests for Complex Validation**:
```php
// app/Http/Requests/StoreBookingRequest.php
class StoreBookingRequest extends FormRequest
{
    public function rules()
    {
        return [
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'department_id' => 'required|exists:departments,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'time_slot' => 'required|date_format:H:i',
        ];
    }
}

// Controller
public function store(StoreBookingRequest $request)
{
    // Already validated
    $booking = Booking::create($request->validated());
}
```

**Use Resource Controllers**:
```php
// routes/web.php
Route::resource('bookings', BookingController::class);

// Generates:
// GET    /bookings           → index()
// GET    /bookings/create    → create()
// POST   /bookings           → store()
// GET    /bookings/{id}      → show()
// GET    /bookings/{id}/edit → edit()
// PUT    /bookings/{id}      → update()
// DELETE /bookings/{id}      → destroy()
```

**Use Eager Loading**:
```php
// Good: Eager load relationships
$bookings = Booking::with(['user', 'pharmacy', 'department'])->get();

// Bad: N+1 query problem
$bookings = Booking::all();
foreach ($bookings as $booking) {
    echo $booking->user->name;  // Separate query for each booking
}
```

**Use Transactions**:
```php
use Illuminate\Support\Facades\DB;

DB::transaction(function () {
    $booking = Booking::create([...]);

    Department::where('id', $booking->department_id)
        ->decrement('available_slots');

    AuditLogService::log('booking_created', auth()->id());
});
```

### Blade Template Standards

**Use Components**:
```blade
{{-- resources/views/components/alert.blade.php --}}
<div class="alert alert-{{ $type }}" role="alert">
    <i class="bi bi-{{ $icon }}"></i>
    {{ $slot }}
</div>

{{-- Usage --}}
<x-alert type="success" icon="check-circle">
    Booking created successfully!
</x-alert>
```

**Escape Output**:
```blade
{{-- Escaped (safe, default) --}}
{{ $user->name }}

{{-- Unescaped (only if HTML is trusted) --}}
{!! $htmlContent !!}
```

**Use @auth/@guest**:
```blade
@auth
    <p>Welcome, {{ auth()->user()->name }}</p>
@endauth

@guest
    <a href="{{ route('login') }}">Login</a>
@endguest
```

**Use @can for Authorization**:
```blade
@can('update', $booking)
    <a href="{{ route('bookings.edit', $booking) }}">Edit</a>
@endcan
```

### JavaScript Standards

**Use ES6+**:
```javascript
// Arrow functions
const blockPastSlots = () => {
    // Implementation
};

// Template literals
const message = `Booking created for ${date}`;

// Destructuring
const { name, email } = user;

// Const/let instead of var
const API_URL = '/api/bookings';
let currentPage = 1;
```

**Event Listeners**:
```javascript
// Good: addEventListener
document.getElementById('submitBtn').addEventListener('click', handleSubmit);

// Bad: Inline onclick
// <button onclick="handleSubmit()">Submit</button>
```

**AJAX with Fetch**:
```javascript
fetch('/api/bookings', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(data)
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));
```

### CSS/SCSS Standards

**Use Bootstrap Classes**:
```html
<!-- Good: Bootstrap utility classes -->
<div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <button class="btn btn-primary">Submit</button>
        </div>
    </div>
</div>

<!-- Bad: Custom CSS for everything -->
<div class="my-custom-container">
    <div class="my-custom-row">
        <div class="my-custom-col">
            <button class="my-custom-button">Submit</button>
        </div>
    </div>
</div>
```

**Custom CSS Organization**:
```css
/* Variables */
:root {
    --primary-color: #0d6efd;
    --danger-color: #dc3545;
}

/* Base styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Layout */
.main-container {
    max-width: 1200px;
}

/* Components */
.booking-card {
    border-radius: 8px;
}

/* Utilities */
.text-truncate-2 {
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Responsive */
@media (max-width: 768px) {
    .desktop-only {
        display: none;
    }
}
```

---

## Git Workflow

### Branch Strategy

**Main Branches**:
- `main` or `master`: Production-ready code
- `develop`: Integration branch for features

**Feature Branches**:
- `feature/booking-system`
- `feature/email-notifications`
- `feature/user-profile`

**Bugfix Branches**:
- `bugfix/login-validation`
- `bugfix/booking-cancel`

**Hotfix Branches** (for production):
- `hotfix/security-patch`
- `hotfix/critical-bug`

### Workflow

1. **Create Feature Branch**:
```bash
git checkout develop
git pull origin develop
git checkout -b feature/new-feature
```

2. **Make Changes**:
```bash
# Make your code changes
git add .
git commit -m "Add new feature"
```

3. **Push to Remote**:
```bash
git push origin feature/new-feature
```

4. **Create Pull Request**:
- Go to GitHub/GitLab
- Create PR from `feature/new-feature` to `develop`
- Add description and reviewers
- Wait for review

5. **Merge to Develop**:
- After approval, merge PR
- Delete feature branch

6. **Release to Production**:
```bash
git checkout main
git merge develop
git tag v1.0.0
git push origin main --tags
```

### Commit Messages

Follow Conventional Commits specification:

**Format**:
```
<type>(<scope>): <subject>

<body>

<footer>
```

**Types**:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting)
- `refactor`: Code refactoring
- `test`: Adding tests
- `chore`: Build/config changes

**Examples**:
```bash
# Feature
git commit -m "feat(booking): Add booking cancellation feature"

# Bug fix
git commit -m "fix(auth): Fix login verification message not showing"

# Documentation
git commit -m "docs(readme): Update installation instructions"

# Refactor
git commit -m "refactor(validation): Extract validation rules to service"

# Multi-line commit
git commit -m "feat(booking): Add booking approval workflow

- Add pending status for new bookings
- Add admin approval interface
- Send email notifications on approval/rejection

Closes #123"
```

### Pull Request Guidelines

**PR Title**:
```
feat: Add booking cancellation feature
fix: Resolve login verification issue
```

**PR Description Template**:
```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Changes Made
- Added booking cancellation endpoint
- Updated booking status enum
- Added cancellation reason field

## Testing
- [ ] Unit tests added
- [ ] Feature tests added
- [ ] Manual testing completed

## Screenshots (if applicable)
[Add screenshots]

## Related Issues
Closes #123
```

---

## Database Development

### Creating Migrations

**Create migration**:
```bash
docker exec -it pharmacy_php php artisan make:migration create_bookings_table
```

**Migration structure**:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pharmacy_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->date('booking_date');
            $table->time('time_slot');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])
                ->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('booking_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
```

**Run migrations**:
```bash
docker exec -it pharmacy_php php artisan migrate
```

**Rollback**:
```bash
docker exec -it pharmacy_php php artisan migrate:rollback
```

### Creating Seeders

**Create seeder**:
```bash
docker exec -it pharmacy_php php artisan make:seeder PharmacySeeder
```

**Seeder structure**:
```php
<?php

namespace Database\Seeders;

use App\Models\Pharmacy;
use Illuminate\Database\Seeder;

class PharmacySeeder extends Seeder
{
    public function run(): void
    {
        $pharmacies = [
            [
                'name' => 'Hadi Clinic Pharmacy - Salmiya',
                'location' => 'Salmiya, Salem Al Mubarak Street',
                'contact_number' => '12345678',
                'email' => 'salmiya@hadiclinic.com.kw',
                'is_active' => true,
            ],
            // More pharmacies...
        ];

        foreach ($pharmacies as $pharmacy) {
            Pharmacy::create($pharmacy);
        }
    }
}
```

**Run seeders**:
```bash
docker exec -it pharmacy_php php artisan db:seed
docker exec -it pharmacy_php php artisan db:seed --class=PharmacySeeder
```

### Query Builder Tips

**Select**:
```php
// All columns
$users = DB::table('users')->get();

// Specific columns
$users = DB::table('users')->select('name', 'email')->get();

// Distinct
$roles = DB::table('users')->distinct()->pluck('role');
```

**Where Clauses**:
```php
// Basic where
$users = DB::table('users')->where('is_active', 1)->get();

// Multiple conditions
$users = DB::table('users')
    ->where('is_active', 1)
    ->where('role', 'representative')
    ->get();

// Or where
$users = DB::table('users')
    ->where('role', 'super_admin')
    ->orWhere('role', 'pharmacy_admin')
    ->get();

// Where in
$users = DB::table('users')
    ->whereIn('id', [1, 2, 3])
    ->get();

// Where null
$unverified = DB::table('users')
    ->whereNull('email_verified_at')
    ->get();
```

**Joins**:
```php
$bookings = DB::table('bookings')
    ->join('users', 'bookings.user_id', '=', 'users.id')
    ->join('departments', 'bookings.department_id', '=', 'departments.id')
    ->select('bookings.*', 'users.name as user_name', 'departments.name as department_name')
    ->get();
```

**Aggregates**:
```php
// Count
$count = DB::table('bookings')->count();
$activeCount = DB::table('bookings')->where('status', 'confirmed')->count();

// Sum
$total = DB::table('payments')->sum('amount');

// Average
$avg = DB::table('ratings')->avg('score');

// Max/Min
$max = DB::table('bookings')->max('booking_date');
$min = DB::table('bookings')->min('booking_date');
```

---

## Frontend Development

### Blade Templating

**Layouts**:
```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pharmacy Booking')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body>
    @include('partials.navbar')

    <main class="container mt-4">
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
```

**Using Layouts**:
```blade
{{-- resources/views/bookings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
    <h1>My Bookings</h1>

    @foreach($bookings as $booking)
        <div class="card mb-3">
            <div class="card-body">
                <h5>{{ $booking->department->name }}</h5>
                <p>{{ $booking->booking_date }} at {{ $booking->time_slot }}</p>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
<script>
    console.log('Bookings page loaded');
</script>
@endpush
```

### JavaScript Development

**Organize by Feature**:
```
public/js/
├── app.js           # Main application JS
├── auth.js          # Authentication related
├── booking.js       # Booking functionality
└── validation.js    # Form validation
```

**Example Module**:
```javascript
// public/js/booking.js

const BookingModule = {
    init() {
        this.bindEvents();
        this.blockPastSlots();
    },

    bindEvents() {
        document.getElementById('booking_date')?.addEventListener('change', () => {
            this.loadAvailableSlots();
        });
    },

    async loadAvailableSlots() {
        const date = document.getElementById('booking_date').value;
        const departmentId = document.getElementById('department_id').value;

        try {
            const response = await fetch(`/api/slots?date=${date}&department=${departmentId}`);
            const slots = await response.json();
            this.renderSlots(slots);
        } catch (error) {
            console.error('Error loading slots:', error);
        }
    },

    renderSlots(slots) {
        const container = document.getElementById('time-slots');
        container.innerHTML = slots.map(slot => `
            <button type="button" class="btn btn-outline-primary"
                    data-slot="${slot.time}">
                ${slot.time}
            </button>
        `).join('');
    },

    blockPastSlots() {
        // Implementation
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    BookingModule.init();
});
```

### CSS/SCSS Development

**Custom Styles**:
```css
/* public/css/custom.css */

:root {
    --primary-color: #0d6efd;
    --secondary-color: #6c757d;
    --success-color: #198754;
    --danger-color: #dc3545;
    --border-radius: 8px;
}

/* Booking Card */
.booking-card {
    border-radius: var(--border-radius);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.booking-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Status Badges */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 500;
}

.status-pending { background-color: #fff3cd; color: #856404; }
.status-confirmed { background-color: #d1e7dd; color: #0f5132; }
.status-cancelled { background-color: #f8d7da; color: #842029; }
.status-completed { background-color: #cfe2ff; color: #084298; }

/* Responsive */
@media (max-width: 768px) {
    .booking-card {
        margin-bottom: 1rem;
    }

    .desktop-only {
        display: none !important;
    }
}
```

---

## Testing

### Unit Tests

**Create test**:
```bash
docker exec -it pharmacy_php php artisan make:test BookingTest --unit
```

**Test structure**:
```php
<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_booking()
    {
        $user = User::factory()->create();

        $booking = Booking::create([
            'user_id' => $user->id,
            'pharmacy_id' => 1,
            'department_id' => 1,
            'booking_date' => now()->addDay(),
            'time_slot' => '10:00',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_booking_belongs_to_user()
    {
        $booking = Booking::factory()->create();

        $this->assertInstanceOf(User::class, $booking->user);
    }
}
```

### Feature Tests

**Create test**:
```bash
docker exec -it pharmacy_php php artisan make:test BookingControllerTest
```

**Test structure**:
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Pharmacy;
use App\Models\Department;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_bookings()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/bookings');

        $response->assertStatus(200);
        $response->assertViewIs('bookings.index');
    }

    public function test_guest_cannot_view_bookings()
    {
        $response = $this->get('/bookings');

        $response->assertRedirect('/login');
    }

    public function test_user_can_create_booking()
    {
        $user = User::factory()->create();
        $pharmacy = Pharmacy::factory()->create();
        $department = Department::factory()->create(['pharmacy_id' => $pharmacy->id]);

        $response = $this->actingAs($user)->post('/bookings', [
            'pharmacy_id' => $pharmacy->id,
            'department_id' => $department->id,
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'time_slot' => '10:00',
        ]);

        $response->assertRedirect('/bookings');
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'pharmacy_id' => $pharmacy->id,
        ]);
    }
}
```

### Running Tests

**Run all tests**:
```bash
docker exec -it pharmacy_php php artisan test
```

**Run specific test**:
```bash
docker exec -it pharmacy_php php artisan test --filter=test_user_can_create_booking
```

**Run with coverage**:
```bash
docker exec -it pharmacy_php php artisan test --coverage
```

---

## Debugging

### Laravel Debugbar

**Install**:
```bash
docker exec -it pharmacy_php composer require barryvdh/laravel-debugbar --dev
```

**Use**: Automatically shows debug toolbar at bottom of page in dev mode

### Logging

**Log levels**:
```php
use Illuminate\Support\Facades\Log;

Log::emergency('System is down!');
Log::alert('High priority');
Log::critical('Critical condition');
Log::error('Error occurred');
Log::warning('Warning message');
Log::notice('Notice');
Log::info('Informational');
Log::debug('Debug info');
```

**View logs**:
```bash
docker exec -it pharmacy_php tail -f storage/logs/laravel.log
```

### Tinker (REPL)

**Start tinker**:
```bash
docker exec -it pharmacy_php php artisan tinker
```

**Examples**:
```php
// Query database
User::count()
User::where('role', 'super_admin')->first()

// Create record
$user = new User;
$user->name = 'Test';
$user->email = 'test@example.com';
$user->save()

// Test relationships
$booking = Booking::first();
$booking->user
$booking->department

// Test services
AuditLogService::log('test', 1);
```

### Dump and Die

**dd() - Dump and Die**:
```php
dd($variable);  // Dumps variable and stops execution
```

**dump() - Dump and Continue**:
```php
dump($variable);  // Dumps variable but continues execution
```

---

## API Development

### Creating API Endpoints

**routes/api.php**:
```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/bookings', [API\BookingController::class, 'index']);
    Route::post('/bookings', [API\BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [API\BookingController::class, 'show']);
});
```

**API Controller**:
```php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'pharmacy', 'department'])
            ->where('user_id', auth()->id())
            ->get();

        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'department_id' => 'required|exists:departments,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'time_slot' => 'required|date_format:H:i',
        ]);

        $booking = Booking::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        return response()->json($booking, 201);
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        return response()->json($booking->load(['user', 'pharmacy', 'department']));
    }
}
```

### API Resources

**Create resource**:
```bash
docker exec -it pharmacy_php php artisan make:resource BookingResource
```

**Resource class**:
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'pharmacy' => [
                'id' => $this->pharmacy->id,
                'name' => $this->pharmacy->name,
            ],
            'department' => [
                'id' => $this->department->id,
                'name' => $this->department->name,
            ],
            'booking_date' => $this->booking_date,
            'time_slot' => $this->time_slot,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
```

**Use in controller**:
```php
public function index()
{
    $bookings = Booking::where('user_id', auth()->id())->get();
    return BookingResource::collection($bookings);
}
```

---

## Deployment

### Preparation

1. **Update .env for production**:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prod_database
DB_USERNAME=prod_user
DB_PASSWORD=secure_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=app-password
MAIL_ENCRYPTION=tls
```

2. **Optimize application**:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

3. **Set permissions**:
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

4. **Run migrations**:
```bash
php artisan migrate --force
```

### Deployment Checklist

- [ ] Update .env with production values
- [ ] Set APP_DEBUG=false
- [ ] Set APP_ENV=production
- [ ] Configure database credentials
- [ ] Configure mail settings
- [ ] Run migrations
- [ ] Cache config/routes/views
- [ ] Set proper file permissions
- [ ] Configure SSL certificate
- [ ] Set up backup automation
- [ ] Configure monitoring
- [ ] Test all critical functionality

---

## Contributing

### Code Review Process

1. **Submit PR** with clear description
2. **Self-review** your changes
3. **Request reviewers**
4. **Address feedback** promptly
5. **Ensure tests pass**
6. **Wait for approval**
7. **Merge** when approved

### Code Review Checklist

Reviewers should check:
- [ ] Code follows project standards
- [ ] Tests are included and passing
- [ ] No security vulnerabilities
- [ ] Documentation is updated
- [ ] No breaking changes (or documented)
- [ ] Performance considerations
- [ ] Error handling is proper
- [ [ ] Database migrations are reversible

### Getting Help

- **Documentation**: Check Docs/ folder
- **Laravel Docs**: https://laravel.com/docs
- **Stack Overflow**: Tag with `laravel`
- **Team Chat**: Slack/Discord channel
- **Issue Tracker**: GitHub Issues

---

**Version**: 1.0
**Last Updated**: January 2026
**Document ID**: DEV-GUIDE-001

For questions: dev-team@example.com
