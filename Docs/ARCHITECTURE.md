# System Architecture

## Table of Contents
1. [Overview](#overview)
2. [Technology Stack](#technology-stack)
3. [Project Structure](#project-structure)
4. [MVC Architecture](#mvc-architecture)
5. [Database Architecture](#database-architecture)
6. [Authentication Flow](#authentication-flow)
7. [Request Lifecycle](#request-lifecycle)
8. [Middleware Pipeline](#middleware-pipeline)
9. [Service Layer](#service-layer)
10. [Frontend Architecture](#frontend-architecture)

---

## Overview

The Medical Representative Booking System is built using the Laravel framework following the Model-View-Controller (MVC) architectural pattern. The system is containerized using Docker for consistent deployment across different environments.

### Architecture Principles

- **Separation of Concerns**: Clear separation between business logic, data access, and presentation
- **Single Responsibility**: Each component has a well-defined purpose
- **DRY (Don't Repeat Yourself)**: Reusable components and services
- **Security First**: Multiple layers of security controls
- **Scalability**: Designed to handle growing user base and data

---

## Technology Stack

### Backend
- **Framework**: Laravel 10.x
- **Language**: PHP 8.2+
- **ORM**: Eloquent
- **Template Engine**: Blade
- **Authentication**: Laravel Sanctum
- **Validation**: Laravel Validation Rules
- **Session Management**: File-based (production should use Redis)

### Database
- **DBMS**: MySQL 8.0
- **Migrations**: Laravel Migrations
- **Seeding**: Laravel Seeders

### Frontend
- **CSS Framework**: Bootstrap 5.3.0
- **Icons**: Bootstrap Icons 1.11.x
- **JavaScript**: Vanilla JS (no framework dependencies)
- **Responsive Design**: Mobile-first approach

### Infrastructure
- **Containerization**: Docker & Docker Compose
- **Web Server**: Nginx (Alpine)
- **PHP Runtime**: PHP-FPM 8.2

---

## Project Structure

```
Med-Rep-Booking-System/
├── Docs/                           # Documentation
│   ├── README.md
│   ├── INSTALLATION.md
│   ├── SECURITY.md
│   ├── ARCHITECTURE.md
│   └── ...
├── docker/                         # Docker configuration
│   ├── nginx/
│   │   ├── Dockerfile
│   │   └── default.conf
│   └── php/
│       └── Dockerfile
├── src/                           # Laravel application
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/       # Request handlers
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── BookingController.php
│   │   │   │   ├── DepartmentController.php
│   │   │   │   ├── PharmacyController.php
│   │   │   │   └── ...
│   │   │   ├── Middleware/        # HTTP middleware
│   │   │   │   ├── SecurityHeaders.php
│   │   │   │   ├── RoleMiddleware.php
│   │   │   │   └── ...
│   │   │   └── Kernel.php         # Middleware registration
│   │   ├── Models/                # Eloquent models
│   │   │   ├── User.php
│   │   │   ├── Booking.php
│   │   │   ├── Department.php
│   │   │   ├── Pharmacy.php
│   │   │   └── ...
│   │   ├── Services/              # Business logic
│   │   │   ├── AuditLogService.php
│   │   │   ├── ValidationRuleService.php
│   │   │   └── ...
│   │   └── Providers/             # Service providers
│   ├── config/                    # Configuration files
│   │   ├── app.php
│   │   ├── database.php
│   │   ├── auth.php
│   │   └── ...
│   ├── database/
│   │   ├── migrations/            # Database migrations
│   │   └── seeders/               # Database seeders
│   ├── resources/
│   │   └── views/                 # Blade templates
│   │       ├── layouts/           # Layout templates
│   │       │   ├── app.blade.php
│   │       │   └── guest.blade.php
│   │       ├── auth/              # Authentication views
│   │       ├── bookings/          # Booking views
│   │       ├── departments/       # Department views
│   │       └── ...
│   ├── routes/
│   │   ├── web.php               # Web routes
│   │   └── api.php               # API routes
│   └── public/                   # Public assets
│       ├── css/
│       └── js/
└── docker-compose.yml            # Docker orchestration
```

---

## MVC Architecture

### Model Layer (`app/Models/`)

**Responsibility**: Data representation and business logic

**Key Models**:

```php
// User Model
- Represents users (Super Admin, Pharmacy Admin, Representative)
- Relationships: hasMany(Booking), belongsTo(Pharmacy)
- Scopes: active(), byRole()

// Booking Model
- Represents booking appointments
- Relationships: belongsTo(User), belongsTo(Department), belongsTo(Pharmacy)
- Status: pending, confirmed, cancelled, completed

// Department Model
- Represents pharmacy departments
- Relationships: belongsTo(Pharmacy), hasMany(Booking), hasMany(Schedule)

// Pharmacy Model
- Represents pharmacy locations
- Relationships: hasMany(User), hasMany(Department), hasMany(Booking)

// Schedule Model
- Represents department availability
- Relationships: belongsTo(Department)
- Business logic: isAvailable(), getAvailableSlots()
```

### View Layer (`resources/views/`)

**Responsibility**: User interface presentation

**Layout Structure**:
```
layouts/
├── app.blade.php          # Authenticated user layout
└── guest.blade.php        # Guest/public layout

Features:
- @yield('content') for content injection
- @section/@show for reusable sections
- Responsive design with mobile-specific views
- Session flash message display
- CSRF token inclusion
```

### Controller Layer (`app/Http/Controllers/`)

**Responsibility**: Handle HTTP requests and coordinate between models and views

**Key Controllers**:

```php
// AuthController
- User registration with validation
- Email verification
- Login/logout with rate limiting
- Password reset functionality

// BookingController
- Create/view/cancel bookings
- Role-based access control
- Date/time slot validation
- Status management

// DepartmentController
- CRUD operations for departments
- Schedule management
- Pharmacy association

// PharmacyController
- CRUD operations for pharmacies
- User management
- Department oversight
```

**Controller Pattern**:
```php
public function method(Request $request)
{
    // 1. Validate input
    $validated = $request->validate([...]);

    // 2. Process business logic (often via Service)
    $result = ServiceClass::process($validated);

    // 3. Return response
    return redirect()->route('name')->with('success', 'Message');
}
```

---

## Database Architecture

### Entity Relationship Diagram

```
┌─────────────────┐
│     Users       │
├─────────────────┤
│ id              │──┐
│ name            │  │
│ email           │  │
│ civil_id        │  │
│ mobile_number   │  │
│ role            │  │
│ pharmacy_id     │──┼─────────┐
│ is_active       │  │         │
│ email_verified  │  │         │
└─────────────────┘  │         │
         │           │         │
         │ has many  │         │ belongs to
         │           │         │
         ▼           │         ▼
┌─────────────────┐  │  ┌─────────────────┐
│    Bookings     │  │  │   Pharmacies    │
├─────────────────┤  │  ├─────────────────┤
│ id              │  │  │ id              │
│ user_id         │──┘  │ name            │
│ pharmacy_id     │─────│ location        │
│ department_id   │──┐  │ contact_info    │
│ booking_date    │  │  │ is_active       │
│ time_slot       │  │  └─────────────────┘
│ status          │  │           │
│ notes           │  │           │ has many
└─────────────────┘  │           │
                     │           ▼
                     │  ┌─────────────────┐
                     │  │  Departments    │
                     │  ├─────────────────┤
                     │  │ id              │
                     └──│ pharmacy_id     │
                        │ name            │
                        │ description     │
                        │ is_active       │
                        └─────────────────┘
                                 │
                                 │ has many
                                 │
                                 ▼
                        ┌─────────────────┐
                        │   Schedules     │
                        ├─────────────────┤
                        │ id              │
                        │ department_id   │
                        │ day_of_week     │
                        │ start_time      │
                        │ end_time        │
                        │ max_bookings    │
                        └─────────────────┘
```

### Key Relationships

1. **User → Pharmacy**: Many-to-One (pharmacy_id)
2. **User → Booking**: One-to-Many (user_id)
3. **Pharmacy → Department**: One-to-Many (pharmacy_id)
4. **Pharmacy → Booking**: One-to-Many (pharmacy_id)
5. **Department → Schedule**: One-to-Many (department_id)
6. **Department → Booking**: One-to-Many (department_id)

---

## Authentication Flow

### Registration Flow

```
User Registration Request
        ↓
1. Validate Input
   - Name, email, civil_id (12 digits)
   - Mobile number (8 digits)
   - Password (min 8, uppercase, lowercase, numbers, symbols)
   - hCaptcha verification
        ↓
2. Check Uniqueness
   - Email must be unique
   - Civil ID must be unique
   - Mobile number must be unique
        ↓
3. Create User Account
   - Hash password (bcrypt)
   - Set role (default: representative)
   - Set is_active = false (pending approval)
   - email_verified_at = null
        ↓
4. Send Verification Email
   - Laravel's email verification system
   - Signed URL with expiration
        ↓
5. Redirect to Login
   - Success message
   - Prompt to verify email
```

### Login Flow

```
User Login Request
        ↓
1. Validate Credentials
   - Rate limiting: 5 attempts per minute
   - Email format validation
        ↓
2. Attempt Authentication
   - Laravel Auth::attempt()
   - Check email + password
        ↓
3. Email Verification Check
   - If not verified:
     * Flash warning message BEFORE logout
     * Logout user
     * Redirect to login
        ↓
4. Account Active Check
   - If not active (pending admin approval):
     * Flash error message BEFORE logout
     * Logout user
     * Redirect to login
        ↓
5. Successful Login
   - Regenerate session ID
   - Create audit log entry
   - Redirect based on role:
     * Super Admin → /super-admin/dashboard
     * Pharmacy Admin → /admin/dashboard
     * Representative → /dashboard
```

### Password Reset Flow

```
Forgot Password Request
        ↓
1. Validate Email
   - Rate limiting: 3 attempts per minute
        ↓
2. Generate Reset Token
   - Laravel Password::sendResetLink()
   - Token stored in password_reset_tokens table
        ↓
3. Send Reset Email
   - Signed URL with token
   - Expires in 60 minutes
        ↓
Reset Password Submission
        ↓
4. Validate Token & Password
   - Token valid and not expired
   - Password meets complexity requirements
   - Rate limiting: 5 attempts per minute
        ↓
5. Update Password
   - Hash new password
   - Invalidate reset token
   - Redirect to login
```

---

## Request Lifecycle

### HTTP Request Flow

```
1. Entry Point: public/index.php
        ↓
2. Bootstrap Laravel Application
   - Load environment variables (.env)
   - Load configuration files
   - Register service providers
        ↓
3. HTTP Kernel (app/Http/Kernel.php)
   - Load global middleware
   - Load middleware groups (web, api)
        ↓
4. Middleware Pipeline (in order)
   - EncryptCookies
   - AddQueuedCookiesToResponse
   - StartSession
   - ShareErrorsFromSession
   - VerifyCsrfToken
   - SubstituteBindings
   - SecurityHeaders (custom)
        ↓
5. Route Matching (routes/web.php)
   - Match URL to route definition
   - Apply route middleware (auth, role, throttle)
        ↓
6. Route Middleware
   - Authenticate (if auth middleware)
   - RoleMiddleware (if role middleware)
   - Throttle (if throttle middleware)
        ↓
7. Controller Method Execution
   - Dependency injection
   - Form request validation (if applicable)
   - Business logic execution
        ↓
8. Service Layer (if used)
   - ValidationRuleService
   - AuditLogService
        ↓
9. Model/Database Interaction
   - Eloquent ORM queries
   - Database transactions (if needed)
        ↓
10. View Rendering
    - Blade template compilation
    - Data binding
    - Layout composition
        ↓
11. Response Middleware
    - SecurityHeaders added
    - Cookies encrypted
        ↓
12. HTTP Response
    - Send to client
    - Close database connections
```

---

## Middleware Pipeline

### Global Middleware
Applied to all requests:
- None currently (all in middleware groups)

### Web Middleware Group
Applied to all web routes:

```php
1. EncryptCookies
   - Encrypts/decrypts cookies

2. AddQueuedCookiesToResponse
   - Adds queued cookies to response

3. StartSession
   - Starts PHP session
   - Loads session data

4. ShareErrorsFromSession
   - Shares validation errors with views

5. VerifyCsrfToken
   - Validates CSRF token on POST/PUT/DELETE

6. SubstituteBindings
   - Resolves route model bindings

7. SecurityHeaders (custom)
   - Adds HTTP security headers
   - X-Frame-Options, CSP, etc.
```

### Route Middleware
Applied to specific routes:

```php
1. auth
   - Requires authenticated user
   - Redirects to login if not authenticated

2. role:super_admin,pharmacy_admin
   - Requires specific role(s)
   - Returns 403 if unauthorized

3. verified
   - Requires verified email
   - Redirects to verification notice

4. throttle:5,1
   - Rate limiting
   - 5 requests per 1 minute

5. guest
   - Requires unauthenticated user
   - Redirects authenticated users to dashboard
```

---

## Service Layer

### Purpose
Encapsulate complex business logic outside of controllers to promote:
- Code reusability
- Testability
- Single Responsibility Principle

### Key Services

#### AuditLogService (`app/Services/AuditLogService.php`)

**Purpose**: Centralized audit logging

**Methods**:
```php
// Log any action
public static function log(
    string $action,
    ?int $userId = null,
    ?string $model = null,
    ?int $recordId = null,
    ?array $changes = null,
    ?string $ipAddress = null
): void

// Usage examples
AuditLogService::log('login', $user->id, 'User', $user->id);
AuditLogService::log('booking_created', $user->id, 'Booking', $booking->id);
AuditLogService::log('department_updated', $user->id, 'Department', $dept->id, $changes);
```

#### ValidationRuleService (`app/Services/ValidationRuleService.php`)

**Purpose**: Centralized validation rules

**Methods**:
```php
// Get user validation rules
public static function getUserRules(?int $userId = null): array

// Get pharmacy validation rules
public static function getPharmacyRules(?int $pharmacyId = null): array

// Get department validation rules
public static function getDepartmentRules(?int $departmentId = null): array

// Get booking validation rules
public static function getBookingRules(): array

// Get custom error messages
public static function getCustomMessages(): array

// Usage
$validated = $request->validate(
    ValidationRuleService::getUserRules(),
    ValidationRuleService::getCustomMessages()
);
```

---

## Frontend Architecture

### Responsive Design Strategy

**Mobile-First Approach**:
- Desktop views: Default
- Mobile views: Separate view files with `-mobile` suffix
- Detection: Server-side User-Agent parsing

### View Hierarchy

```
Desktop Views               Mobile Views
─────────────────          ─────────────────
auth/login.blade.php   →   auth/login-mobile.blade.php
auth/register.blade.php →  auth/register-mobile.blade.php
bookings/index.blade.php → bookings/index-mobile.blade.php
```

### JavaScript Architecture

**Vanilla JavaScript Approach**:
- No framework dependencies (React, Vue, etc.)
- Direct DOM manipulation
- Event listeners for interactivity
- Modular functions

**Key Features**:
```javascript
// Time slot blocking (past times)
function blockPastTimeSlots() {
    // Disable time slots before current time
}

// Password visibility toggle
document.getElementById('togglePassword').addEventListener('click', function() {
    // Toggle password field type
});

// Form validation
function validateCivilId(input) {
    // Validate 12-digit civil ID
}

function validateMobileNumber(input) {
    // Validate 8-digit mobile number
}
```

### CSS Architecture

**Bootstrap 5 Customization**:
```css
/* Custom CSS Variables */
:root {
    --primary-color: #0d6efd;
    --success-color: #198754;
    --danger-color: #dc3545;
}

/* Mobile-specific styles */
@media (max-width: 768px) {
    .mobile-hidden {
        display: none;
    }
}
```

---

## Security Architecture

### Defense in Depth

**Layer 1: Network/Infrastructure**
- Docker container isolation
- Nginx reverse proxy
- Rate limiting at application level

**Layer 2: Application**
- CSRF protection on all forms
- XSS prevention (Blade auto-escaping)
- SQL injection prevention (Eloquent ORM)
- Input validation on all requests

**Layer 3: Authentication & Authorization**
- Password complexity requirements
- Email verification
- Admin approval for new users
- Role-based access control (RBAC)

**Layer 4: Session & Data**
- Session regeneration on login
- Session invalidation on logout
- bcrypt password hashing
- Sensitive data logging avoided

**Layer 5: HTTP Headers**
- X-Frame-Options: SAMEORIGIN
- Content-Security-Policy
- X-Content-Type-Options: nosniff
- X-XSS-Protection

### Security Score: 9/10

See [SECURITY.md](SECURITY.md) for detailed security documentation.

---

## Scalability Considerations

### Current Architecture
- Single database server
- File-based sessions
- Suitable for small to medium deployments (< 1000 concurrent users)

### Scaling Recommendations

**For Medium Scale (1,000 - 10,000 users)**:
1. **Session Storage**: Move from file-based to Redis
2. **Database**: Add read replicas for reporting queries
3. **Caching**: Implement Redis for frequently accessed data
4. **CDN**: Use CDN for static assets

**For Large Scale (10,000+ users)**:
1. **Load Balancing**: Multiple application servers behind load balancer
2. **Database**: Master-slave replication with connection pooling
3. **Queue System**: Redis or RabbitMQ for background jobs
4. **Search**: Elasticsearch for advanced search features
5. **Monitoring**: Application performance monitoring (APM)

---

## Deployment Architecture

### Development Environment
```
Developer Machine
    ↓
Docker Compose (3 containers)
    ├── Nginx (port 8080)
    ├── PHP-FPM
    └── MySQL (port 3307)
```

### Production Environment (Recommended)
```
Internet
    ↓
Reverse Proxy (nginx/Apache)
    ↓
Docker Compose
    ├── Nginx
    ├── PHP-FPM
    └── MySQL

External Services:
    - Redis (sessions & cache)
    - Backup Server
    - Email Service (SMTP)
```

---

## Conclusion

This architecture provides a solid foundation for a secure, maintainable, and scalable medical representative booking system. The separation of concerns, security-first approach, and clean code principles ensure the system can grow with your needs.

For more information:
- [Installation Guide](INSTALLATION.md)
- [Security Documentation](SECURITY.md)
- [Database Schema](DATABASE.md)
- [Development Guide](DEVELOPMENT.md)
