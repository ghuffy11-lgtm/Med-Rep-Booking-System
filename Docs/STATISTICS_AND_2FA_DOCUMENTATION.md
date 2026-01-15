# Med-Rep Booking System - Statistics Dashboard & 2FA Documentation

**Version:** 1.0
**Last Updated:** January 15, 2026
**Branch:** `claude/statistics-and-2fa-mt8lz`

---

## Table of Contents

1. [System Overview](#system-overview)
2. [Statistics Dashboard](#statistics-dashboard)
3. [Two-Factor Authentication (2FA)](#two-factor-authentication-2fa)
4. [Technical Implementation](#technical-implementation)
5. [Database Schema](#database-schema)
6. [Code Architecture](#code-architecture)
7. [API & Service Layer](#api--service-layer)
8. [Troubleshooting Guide](#troubleshooting-guide)
9. [Future Enhancements](#future-enhancements)

---

## System Overview

### Project Description
Med-Rep Booking System is a Laravel 10.x application designed to manage medical representative appointments with pharmacy departments. The system allows representatives to book appointments, pharmacy admins to manage bookings, and provides comprehensive statistics and security features.

### System Architecture
- **Framework:** Laravel 10.x
- **PHP Version:** 8.2.29
- **Database:** MySQL
- **Deployment:** Docker (pharmacy_php container)
- **Frontend:** Blade templates with Bootstrap 5 & Chart.js
- **Key Packages:**
  - `pragmarx/google2fa`: Two-factor authentication
  - `bacon/bacon-qr-code`: QR code generation
  - `maatwebsite/excel`: Excel exports
  - `barryvdh/laravel-dompdf`: PDF exports

### User Roles
1. **Super Admin**: Full system access, user management, system configuration
2. **Pharmacy Admin**: Booking management, department management, statistics
3. **Representative**: Create bookings, view booking history

### Single-Pharmacy Architecture
**IMPORTANT**: This system is designed as a **single-pharmacy system**. There is NO `pharmacy_id` field on users or multi-pharmacy support. All bookings belong to a single pharmacy entity, and departments are what differentiate different areas of the pharmacy.

---

## Statistics Dashboard

### Overview
The Statistics Dashboard provides comprehensive analytics for booking data with month/year selection capability. Available to both Super Admin and Pharmacy Admin roles.

### Features

#### 1. **Overview Cards**
Displays key metrics at a glance:
- **Total Bookings**: All-time booking count
- **Bookings This Month**: Count for selected month
- **Today's Bookings**: Current day count
- **Pending Approvals**: Bookings awaiting approval
- **Active Representatives**: Total active representative users (Super Admin) / Unique representatives with bookings (Pharmacy Admin)
- **Active Pharmacies**: Always 1 (single-pharmacy system)
- **Active Departments**: Count of active departments
- **Approval Rate**: Percentage of approved vs total bookings for selected month
- **Avg. Daily Bookings**: (Pharmacy Admin only) Average bookings per day

#### 2. **Month/Year Selection**
- Dropdown selectors for month (January-December) and year (current year to 5 years back)
- "View Statistics" button to load selected period
- "Reset to Current Month" button to return to current month
- Selected month/year is passed to all queries and charts

#### 3. **Month-over-Month Comparison**
- Compares selected month with previous month
- Shows percentage change with directional indicators (↑ or ↓)
- Color-coded: green for increase, gray for decrease

#### 4. **Charts**

**a) 30-Day Bookings Trend (Line Chart)**
- Shows daily booking counts for the entire selected month
- X-axis: Days of the month (e.g., "Jan 01", "Jan 02")
- Y-axis: Number of bookings
- Fills in missing dates with 0 count

**b) Status Distribution (Pie Chart)**
- Shows breakdown of bookings by status for selected month
- **All 4 statuses always displayed:**
  - Pending (Yellow)
  - Approved (Green)
  - Rejected (Red)
  - Cancelled (Gray)
- Shows 0 count if no bookings in that status

**c) Peak Booking Hours (Bar Chart)**
- Shows booking distribution by hour of day (0-23)
- Helps identify busiest times
- Filters by selected month

#### 5. **Top 10 Departments Table**
- Ranked by total bookings
- Shows:
  - Department name
  - This month count (for selected month)
  - Last month count (for month before selected)
  - Change percentage
  - Trend indicator (↑ or ↓)

#### 6. **Top 10 Representatives Table**
- Ranked by total bookings
- Shows:
  - Representative name
  - Company name
  - Total bookings
  - Approved bookings
  - Approval rate (percentage with progress bar)

#### 7. **Export Functions**

**Excel Export:**
- Multiple sheets:
  - Overview: All metrics in table format
  - Top Departments: Ranked list with comparisons
  - Top Representatives: Performance metrics
  - 30-Day Trend: Date and count data
- File format: `.xlsx`
- Filename: `statistics-report-YYYY-MM-DD-HHmmss.xlsx`
- Includes selected month/year data

**PDF Export:**
- **Landscape orientation** for better data display
- Includes:
  - Header with generation date and user name
  - Overview statistics table
  - Month comparison section
  - Top Departments table
  - Top Representatives table
- File format: `.pdf`
- Filename: `statistics-report-YYYY-MM-DD-HHmmss.pdf`
- Styled with professional formatting

### Access Control
- **Super Admin**: Can access via `/admin/statistics`
- **Pharmacy Admin**: Can access via `/admin/statistics`
- **Representative**: No access (redirected to dashboard)

### Routes
```php
Route::get('/admin/statistics', [StatisticsController::class, 'index'])
    ->name('admin.statistics.index');
Route::get('/admin/statistics/export/excel', [StatisticsController::class, 'exportExcel'])
    ->name('admin.statistics.export.excel');
Route::get('/admin/statistics/export/pdf', [StatisticsController::class, 'exportPdf'])
    ->name('admin.statistics.export.pdf');
```

### Views
- **Super Admin**: `resources/views/admin/statistics/super-admin.blade.php`
- **Pharmacy Admin**: `resources/views/admin/statistics/pharmacy-admin.blade.php`
- **PDF Export**: `resources/views/admin/statistics/pdf-export.blade.php`

---

## Two-Factor Authentication (2FA)

### Overview
Google Authenticator-based 2FA adds an extra security layer for Super Admin accounts. It is **optional** and **not enforced** - Super Admins can choose to enable it.

### Features

#### 1. **2FA Setup**
- Available only to Super Admin users
- Accessed via User Profile → "Enable 2FA" button
- Setup process:
  1. Generate secret key
  2. Display QR code for scanning with Google Authenticator app
  3. Show manual entry key (for users who can't scan QR)
  4. Generate 10 recovery codes (one-time use)
  5. User confirms setup by entering first verification code
  6. 2FA is enabled and stored in database

#### 2. **2FA Login Flow**
When a Super Admin with 2FA enabled logs in:

**Step 1: Email/Password Authentication**
- User enters email and password
- System validates credentials
- If valid and 2FA is enabled, proceed to Step 2

**Step 2: Device Trust Check**
- System checks if current device is trusted (via `trusted_devices` table)
- Cookie-based trust: `trusted_device_token` cookie
- If device is trusted → Login successful
- If device is NOT trusted → Proceed to Step 3

**Step 3: 2FA Challenge**
- User redirected to 2FA challenge page
- **User remains authenticated during challenge**
- Session stores: `2fa:auth:id`, `2fa:auth:remember`, `2fa:verified`
- User can enter:
  - **Option A**: 6-digit TOTP code from Google Authenticator
  - **Option B**: One of the 10 recovery codes
- "Trust this device for 30 days" checkbox option

**Step 4: Verification**
- Code is validated against stored secret
- If valid:
  - Session updated: `2fa:verified` = true
  - If "Trust device" checked: Device token saved to database and cookie
  - Audit log entry created
  - User redirected to appropriate dashboard
- If invalid: Error message, user can retry

#### 3. **Trusted Devices**
- Trusted devices skip 2FA challenge for 30 days
- Stored in `trusted_devices` table with:
  - `device_token`: Unique UUID
  - `device_name`: Browser and OS info
  - `ip_address`: Last used IP
  - `expires_at`: 30 days from trust date
- Users can view and revoke trusted devices from profile

#### 4. **Recovery Codes**
- 10 unique codes generated during setup
- Each code is single-use
- Hashed in database for security
- Can be regenerated (invalidates old codes)
- Used when user loses access to authenticator app

#### 5. **2FA Management**
Super Admin can:
- **Enable 2FA**: From profile page
- **Disable 2FA**: Remove authentication requirement
- **Regenerate Recovery Codes**: Get new set of 10 codes
- **View Trusted Devices**: See all trusted devices
- **Revoke Trusted Devices**: Remove device trust

### Security Features

#### Session Management
- User stays authenticated during 2FA challenge (no logout)
- Session keys prefixed with `2fa:` for clarity
- Session cleaned after successful 2FA

#### Audit Logging
All 2FA activities are logged:
- `2fa_enabled`: User enabled 2FA
- `2fa_disabled`: User disabled 2FA
- `login_2fa_success`: Successful 2FA login
- `login_2fa_failed`: Failed 2FA attempt
- `2fa_recovery_used`: Recovery code used
- `2fa_codes_regenerated`: New recovery codes generated

#### Rate Limiting
- Login attempts are rate-limited
- Failed 2FA attempts are logged
- Potential for account lockout (configurable)

### Technical Implementation

#### Database Tables

**users table** (additional fields):
```sql
google2fa_secret VARCHAR(255) NULL
google2fa_enabled BOOLEAN DEFAULT FALSE
two_factor_recovery_codes TEXT NULL
```

**trusted_devices table**:
```sql
id BIGINT PRIMARY KEY
user_id BIGINT (FK to users)
device_token VARCHAR(255) UNIQUE
device_name VARCHAR(255)
ip_address VARCHAR(45)
last_used_at TIMESTAMP
expires_at TIMESTAMP
created_at TIMESTAMP
updated_at TIMESTAMP
```

#### Models

**User.php**:
```php
protected $fillable = [
    'google2fa_secret',
    'google2fa_enabled',
    'two_factor_recovery_codes',
    // ... other fields
];

protected $casts = [
    'google2fa_enabled' => 'boolean',
];

public function getTwoFactorSecret(): ?string
public function getTwoFactorRecoveryCodes(): array
public function hasTwoFactorEnabled(): bool
public function trustedDevices(): HasMany
```

**TrustedDevice.php**:
```php
protected $fillable = [
    'user_id',
    'device_token',
    'device_name',
    'ip_address',
    'last_used_at',
    'expires_at',
];

public function user(): BelongsTo
public function scopeActive($query)
public function isExpired(): bool
```

#### Controllers

**TwoFactorController.php** - Handles 2FA operations:
- `enableTwoFactor()`: Setup wizard
- `confirmTwoFactor()`: Verify and enable
- `disableTwoFactor()`: Remove 2FA
- `show2FAChallenge()`: Display challenge page
- `verify2FA()`: Verify TOTP code
- `verifyRecoveryCode()`: Verify recovery code
- `regenerateRecoveryCodes()`: Generate new codes
- `showTrustedDevices()`: List trusted devices
- `revokeTrustedDevice()`: Remove device trust

**AuthController.php** - Modified login flow:
- Check 2FA enabled status
- Check trusted device
- Redirect to 2FA challenge if needed
- Store session data for 2FA flow

#### Service Layer

**Google2FA Integration**:
```php
use PragmaRX\Google2FA\Google2FA;

$google2fa = app(Google2FA::class);
$secret = $google2fa->generateSecretKey();
$qrCodeUrl = $google2fa->getQRCodeUrl($companyName, $email, $secret);
$valid = $google2fa->verifyKey($secret, $code);
```

#### Routes

```php
// 2FA Setup (authenticated Super Admin only)
Route::post('/profile/2fa/enable', [TwoFactorController::class, 'enableTwoFactor'])
    ->name('profile.2fa.enable');
Route::post('/profile/2fa/confirm', [TwoFactorController::class, 'confirmTwoFactor'])
    ->name('profile.2fa.confirm');
Route::post('/profile/2fa/disable', [TwoFactorController::class, 'disableTwoFactor'])
    ->name('profile.2fa.disable');

// 2FA Challenge (during login, accessible to authenticated users)
Route::get('/2fa/challenge', [TwoFactorController::class, 'show2FAChallenge'])
    ->name('2fa.challenge');
Route::post('/2fa/verify', [TwoFactorController::class, 'verify2FA'])
    ->name('2fa.verify');
Route::post('/2fa/verify-recovery', [TwoFactorController::class, 'verifyRecoveryCode'])
    ->name('2fa.verify.recovery');

// Trusted Devices
Route::get('/profile/trusted-devices', [TwoFactorController::class, 'showTrustedDevices'])
    ->name('profile.trusted-devices');
Route::delete('/profile/trusted-devices/{device}', [TwoFactorController::class, 'revokeTrustedDevice'])
    ->name('profile.trusted-device.revoke');
```

---

## Technical Implementation

### File Structure

```
src/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php           # Modified for 2FA flow
│   │   │   ├── TwoFactorController.php       # 2FA management
│   │   │   └── StatisticsController.php      # Statistics dashboard
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php                          # 2FA fields added
│   │   ├── TrustedDevice.php                 # Device trust model
│   │   └── Booking.php
│   ├── Services/
│   │   ├── StatisticsService.php             # Statistics data aggregation
│   │   └── AuditLogService.php               # Audit logging
│   └── Exports/
│       └── StatisticsExport.php              # Excel export logic
├── resources/
│   └── views/
│       ├── admin/
│       │   └── statistics/
│       │       ├── super-admin.blade.php     # Super admin dashboard
│       │       ├── pharmacy-admin.blade.php  # Pharmacy admin dashboard
│       │       └── pdf-export.blade.php      # PDF export template
│       ├── profile/
│       │   ├── 2fa-setup.blade.php           # 2FA setup wizard
│       │   └── trusted-devices.blade.php     # Trusted devices list
│       └── auth/
│           └── 2fa-challenge.blade.php       # 2FA verification page
├── database/
│   └── migrations/
│       ├── xxxx_add_two_factor_to_users_table.php
│       └── xxxx_create_trusted_devices_table.php
└── composer.json                              # Package dependencies
```

### Key Configuration Files

**composer.json**:
```json
{
  "require": {
    "php": "^8.1",
    "laravel/framework": "^10.0",
    "bacon/bacon-qr-code": "^2.0",
    "barryvdh/laravel-dompdf": "^2.0",
    "maatwebsite/excel": "^3.1",
    "pragmarx/google2fa": "^8.0",
    "pragmarx/google2fa-laravel": "^2.1"
  },
  "config": {
    "platform": {
      "php": "8.2.0"
    }
  }
}
```

---

## Database Schema

### Bookings Table
```sql
CREATE TABLE bookings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT,                    -- FK to users (representative)
    department_id BIGINT,              -- FK to departments
    booking_date DATE,
    time_slot TIME,
    status ENUM('pending', 'approved', 'rejected', 'cancelled'),
    rejection_reason TEXT NULL,
    cancelled_by BIGINT NULL,          -- FK to users (admin who cancelled)
    cancelled_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (cancelled_by) REFERENCES users(id)
);
```

### Users Table (with 2FA fields)
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    role ENUM('super_admin', 'pharmacy_admin', 'representative'),
    company VARCHAR(255) NULL,
    civil_id VARCHAR(255) NULL,
    mobile_number VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,

    -- 2FA Fields
    google2fa_secret VARCHAR(255) NULL,
    google2fa_enabled BOOLEAN DEFAULT FALSE,
    two_factor_recovery_codes TEXT NULL,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Trusted Devices Table
```sql
CREATE TABLE trusted_devices (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    device_token VARCHAR(255) UNIQUE NOT NULL,
    device_name VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Departments Table
```sql
CREATE TABLE departments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## Code Architecture

### MVC Pattern

**Models**: Eloquent ORM models representing database tables
- `User`: User authentication and profile
- `Booking`: Booking records
- `Department`: Pharmacy departments
- `TrustedDevice`: 2FA trusted devices

**Views**: Blade templates for UI
- Statistics dashboards (super-admin, pharmacy-admin)
- 2FA setup and challenge pages
- Export templates (PDF)

**Controllers**: Handle HTTP requests and business logic
- `StatisticsController`: Statistics display and exports
- `TwoFactorController`: 2FA setup and verification
- `AuthController`: Login flow with 2FA integration

### Service Layer Pattern

**StatisticsService.php**: Centralized statistics logic
- `getSuperAdminOverview()`: Overview metrics for super admin
- `getPharmacyAdminOverview()`: Overview metrics for pharmacy admin
- `getBookingsTrend()`: Daily booking counts for selected month
- `getStatusDistribution()`: Status breakdown with all 4 statuses
- `getPeakHours()`: Hourly booking distribution
- `getTopDepartments()`: Top 10 departments with month comparison
- `getTopRepresentatives()`: Top 10 reps with approval rates
- `getMonthComparison()`: Month-over-month comparison
- `calculateApprovalRate()`: Approval percentage for selected month

All methods accept optional `$month` and `$year` parameters to filter data.

### Export Pattern

**Excel Export** (`StatisticsExport.php`):
- Implements `WithMultipleSheets` for multiple tabs
- Separate sheet classes:
  - `OverviewSheet`: Key metrics
  - `DepartmentsSheet`: Department rankings
  - `RepresentativesSheet`: Representative performance
  - `TrendDataSheet`: Daily trend data
- Uses `maatwebsite/excel` package

**PDF Export** (Blade view → DOMPDF):
- Single-page landscape report
- Uses `barryvdh/laravel-dompdf` package
- CSS styling embedded in view
- Data passed from controller as array

---

## API & Service Layer

### StatisticsService Methods

#### `getSuperAdminOverview(int $month = null, int $year = null): array`
Returns overview statistics for super admin.

**Parameters:**
- `$month`: Selected month (1-12), defaults to current
- `$year`: Selected year, defaults to current

**Returns:**
```php
[
    'total_bookings' => int,           // All-time count
    'bookings_this_month' => int,      // Selected month count
    'bookings_today' => int,           // Today's count
    'pending_approvals' => int,        // Pending status count
    'total_representatives' => int,    // Active representative users
    'total_pharmacies' => 1,           // Always 1 (single-pharmacy)
    'total_departments' => int,        // Active departments
    'approval_rate' => float,          // Approval % for selected month
]
```

#### `getPharmacyAdminOverview(int $pharmacyId = null, int $month = null, int $year = null): array`
Returns overview statistics for pharmacy admin.

**Parameters:**
- `$pharmacyId`: Always null (single-pharmacy system)
- `$month`: Selected month (1-12), defaults to current
- `$year`: Selected year, defaults to current

**Returns:**
```php
[
    'total_bookings' => int,
    'bookings_this_month' => int,
    'bookings_today' => int,
    'pending_approvals' => int,
    'active_representatives' => int,   // Unique reps with bookings
    'active_departments' => int,
    'approval_rate' => float,
    'avg_response_time' => string,     // e.g., "2 hours"
    'avg_daily_bookings' => float,
]
```

#### `getTopDepartments(int $limit = 10, ?int $pharmacyId = null, int $month = null, int $year = null): array`
Returns top departments ranked by total bookings.

**Returns:** Array of arrays:
```php
[
    [
        'department' => string,        // Department name
        'total_bookings' => int,       // All-time count
        'this_month' => int,           // Selected month count
        'last_month' => int,           // Previous month count
        'change' => float,             // Percentage change
        'change_direction' => string,  // 'up', 'down', or 'same'
    ],
    // ... more departments
]
```

#### `getTopRepresentatives(int $limit = 10, ?int $pharmacyId = null, int $month = null, int $year = null): array`
Returns top representatives ranked by total bookings.

**Returns:** Array of arrays:
```php
[
    [
        'name' => string,              // Representative name
        'company' => string,           // Company name
        'total_bookings' => int,       // All-time count
        'approved_bookings' => int,    // Approved count
        'this_month' => int,           // Selected month count
        'last_month' => int,           // Previous month count
        'approval_rate' => float,      // Approval percentage
        'change' => float,             // Monthly change %
        'change_direction' => string,  // 'up', 'down', or 'same'
    ],
    // ... more representatives
]
```

#### `getStatusDistribution(?int $pharmacyId = null, int $month = null, int $year = null): array`
Returns booking status distribution for pie chart.

**Returns:**
```php
[
    'labels' => ['Pending', 'Approved', 'Rejected', 'Cancelled'],
    'data' => [10, 25, 5, 3],  // Counts for selected month
]
```

**Important:** Always returns all 4 statuses, even if count is 0.

---

## Troubleshooting Guide

### Common Issues & Solutions

#### 1. "Attempt to read property on array"
**Symptom:** Error when viewing statistics dashboard
**Cause:** View trying to access array data as object properties
**Solution:** Use array bracket notation `$item['field']` instead of `$item->field`

#### 2. "No pharmacy assigned to your account"
**Symptom:** Pharmacy admin can't access statistics
**Cause:** Code checking for `pharmacy_id` field that doesn't exist
**Solution:** Remove pharmacy_id checks, set `$pharmacyId = null` (single-pharmacy system)

#### 3. "Undefined array key 'total_departments'"
**Symptom:** Error during Excel/PDF export
**Cause:** Field name mismatch between super admin and pharmacy admin overviews
**Solution:** Use null coalescing: `$overview['total_departments'] ?? $overview['active_departments'] ?? 'N/A'`

#### 4. "Class 'BaconQrCode\Writer' not found"
**Symptom:** 2FA QR code fails to generate
**Cause:** Missing package or wrong version
**Solution:** Run `composer require bacon/bacon-qr-code:^2.0`

#### 5. Login Loop After Enabling 2FA
**Symptom:** After login, redirected back to login page
**Cause:** Session cleared during 2FA flow
**Solution:**
- Don't call `Auth::logout()` during 2FA challenge
- Use session keys: `2fa:auth:id`, `2fa:auth:remember`
- Remove guest middleware from 2FA challenge routes

#### 6. 404 Error After 2FA Code Entry
**Symptom:** After entering 2FA code, page not found
**Cause:** Wrong session key in verification method
**Solution:** Ensure all methods use `2fa:auth:id` session key

#### 7. Approval Rate Shows 0%
**Symptom:** Top Representatives approval rate is empty
**Cause:** Wrong booking status in query (using "confirmed" instead of "approved")
**Solution:** Change status check to `bookings.status = "approved"`

#### 8. PDF Export Shows Incomplete Data
**Symptom:** Tables cut off in PDF
**Cause:** Portrait orientation too narrow
**Solution:** Change to landscape: `$pdf->setPaper('A4', 'landscape')`

#### 9. Status Distribution Missing Rejected
**Symptom:** Pie chart doesn't show rejected bookings
**Cause:** Only showing statuses with bookings > 0
**Solution:** Always return all 4 statuses with 0 count if needed

#### 10. Composer Dependency Conflicts
**Symptom:** Can't install packages, version conflicts
**Cause:** PHP version mismatch
**Solution:**
- Add platform config to composer.json: `"platform": {"php": "8.2.0"}`
- Run `composer update` to regenerate lock file

---

## Future Enhancements

### Potential Features

1. **Advanced Filtering**
   - Filter statistics by department
   - Filter by representative
   - Custom date ranges (not just month)

2. **Real-time Dashboard**
   - WebSocket integration for live updates
   - Auto-refresh every X minutes

3. **Email Reports**
   - Scheduled reports (daily, weekly, monthly)
   - Email statistics to admin users

4. **Comparative Analytics**
   - Year-over-year comparison
   - Quarter-over-quarter trends

5. **Representative Performance Scoring**
   - Weighted scoring system
   - Gamification elements

6. **2FA Enhancements**
   - SMS-based 2FA option
   - Backup email codes
   - Biometric authentication (WebAuthn)

7. **Multi-Pharmacy Support**
   - Add pharmacy_id field
   - Pharmacy-specific statistics
   - Cross-pharmacy comparisons

8. **API Endpoints**
   - RESTful API for statistics
   - Third-party integrations
   - Mobile app support

### Known Limitations

1. **Single-Pharmacy Architecture**: System doesn't support multiple pharmacies
2. **Month-Only Selection**: Can't select custom date ranges
3. **No Data Caching**: Statistics queries run fresh every time (consider Redis)
4. **Limited Export Customization**: Can't choose which metrics to export
5. **No Scheduled Reports**: Must manually export
6. **2FA Not Enforced**: Optional for super admins, consider making mandatory

---

## Maintenance Notes

### Regular Tasks

1. **Database Cleanup**
   - Clean up expired trusted devices: `TrustedDevice::where('expires_at', '<', now())->delete()`
   - Archive old bookings (optional)

2. **Security Audits**
   - Review audit logs regularly
   - Check for suspicious 2FA failures
   - Monitor trusted device usage

3. **Performance Monitoring**
   - Monitor statistics query performance
   - Consider adding indexes on created_at, booking_date
   - Cache frequently accessed statistics

4. **Backup & Recovery**
   - Regular database backups
   - Backup 2FA recovery codes securely
   - Test restore procedures

### Version Control

- **Main Branch**: Production-ready code
- **Development Branch**: Testing new features
- **Feature Branches**: `claude/feature-name-sessionid` format
- **Branch Naming**: Must start with `claude/` and end with session ID for git push

### Deployment Checklist

- [ ] Run `composer install --no-dev --optimize-autoloader`
- [ ] Run `php artisan migrate` for new migrations
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Clear config: `php artisan config:clear`
- [ ] Clear views: `php artisan view:clear`
- [ ] Optimize: `php artisan optimize`
- [ ] Test 2FA flow with multiple users
- [ ] Test statistics export functions
- [ ] Verify month selection functionality
- [ ] Check all 4 booking statuses in pie chart

---

## Contact & Support

For issues or questions:
1. Check this documentation first
2. Review git commit history for recent changes
3. Check Laravel logs: `storage/logs/laravel.log`
4. Review audit logs in database for 2FA issues
5. Test in Docker container: `docker exec -it pharmacy_php bash`

---

**Document Version History:**
- v1.0 (2026-01-15): Initial comprehensive documentation created

**Branch:** `claude/statistics-and-2fa-mt8lz`
**Status:** ✅ Production Ready
