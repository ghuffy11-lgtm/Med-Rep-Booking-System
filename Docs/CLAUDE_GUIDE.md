# Claude Code Guide - Med-Rep Booking System

**Quick Reference for Claude Code Across Sessions**

---

## 🎯 System Identity

**What is this?** Medical Representative Booking System for a pharmacy in Kuwait.

**Tech Stack:**
- Laravel 10.x + PHP 8.2.29
- MySQL Database
- Docker deployment (pharmacy_php container)
- Bootstrap 5 + Chart.js frontend

**Current Branch:** `claude/statistics-and-2fa-mt8lz`

**Your Role:** Full-stack development assistant maintaining and enhancing this live production system.

---

## ⚠️ CRITICAL: Production System Rules

### DO NOT:
1. ❌ Make changes without reading the file first
2. ❌ Commit changes unless explicitly requested by user
3. ❌ Push to any branch except those starting with `claude/` and ending with session ID
4. ❌ Use `--force` on git push
5. ❌ Delete files without confirmation
6. ❌ Add emojis unless user explicitly requests
7. ❌ Create documentation files proactively (only when asked)
8. ❌ Modify functional workflows without user approval

### ALWAYS:
1. ✅ Read files before editing them
2. ✅ Use proper git commit messages (see format below)
3. ✅ Test changes mentally before implementation
4. ✅ Prefer editing existing files over creating new ones
5. ✅ Use specialized tools (Read, Edit, Write) over bash commands for file operations
6. ✅ Ask for clarification when unclear

---

## 🏗️ System Architecture Quick Reference

### Single-Pharmacy System ⚠️
**IMPORTANT:** This is NOT a multi-pharmacy system. There is NO `pharmacy_id` field on users.
- All bookings belong to ONE pharmacy
- Departments differentiate areas within the pharmacy
- Never check for `pharmacy_id` or `user->pharmacy_id`

### User Roles
```php
1. super_admin     // Full system access, user management, 2FA optional
2. pharmacy_admin  // Booking management, statistics, departments
3. representative  // Create bookings, view history
```

### Booking Statuses
```php
'pending'   // Yellow - Awaiting approval
'approved'  // Green - Confirmed by admin
'rejected'  // Red - Denied by admin
'cancelled' // Gray - Cancelled by admin or rep
```

### Database Tables (Key Ones)
```
bookings (id, user_id, department_id, booking_date, time_slot, status, ...)
users (id, name, email, role, google2fa_secret, google2fa_enabled, ...)
departments (id, name, is_active)
trusted_devices (id, user_id, device_token, expires_at, ...)
```

---

## 🚀 Common Tasks & Workflows

### 1. Fixing Bugs

**Before starting:**
```bash
# 1. Read the error message carefully
# 2. Identify the file and line number
# 3. Read the entire file context
```

**Common error patterns:**
- "Attempt to read property on array" → Use `$item['key']` not `$item->key`
- "Undefined array key" → Field name mismatch (super admin vs pharmacy admin)
- "Class not found" → Missing import or wrong namespace
- "pharmacy_id" errors → Remove checks (single-pharmacy system)

**Process:**
1. Use `Read` tool to view the file
2. Use `Edit` tool to fix (never guess - read first!)
3. Test mentally or ask user to test
4. Only commit when user confirms it works

### 2. Adding Statistics Features

**Pattern:**
```php
// 1. Update StatisticsService.php (service layer)
public static function getNewMetric($month = null, $year = null): array {
    $month = $month ?? now()->month;
    $year = $year ?? now()->year;
    // ... query with month/year filtering
}

// 2. Update StatisticsController.php (controller layer)
$newMetric = StatisticsService::getNewMetric($selectedMonth, $selectedYear);

// 3. Update view file (blade template)
{{ $newMetric['value'] }}

// 4. Update exports if needed (Excel + PDF)
```

**Key principle:** All statistics methods accept `$month` and `$year` parameters.

### 3. Working with 2FA

**Login flow:**
```
1. Email/Password → AuthController::login()
2. Check 2FA enabled → if yes, check trusted device
3. Not trusted → Redirect to 2fa.challenge (user stays authenticated)
4. Verify code → TwoFactorController::verify2FA()
5. Success → Redirect based on role
```

**Session keys:**
- `2fa:auth:id` - User ID pending verification
- `2fa:auth:remember` - Remember me checkbox
- `2fa:verified` - Verification status

**Never use:** `Auth::logout()` during 2FA flow - user must stay authenticated!

### 4. Git Operations

**Commit message format:**
```bash
<type>: <description>

# Types:
feat:   # New feature
fix:    # Bug fix
docs:   # Documentation
style:  # Code style (no functional change)
refactor: # Code refactoring
test:   # Adding tests
chore:  # Maintenance
```

**Branch naming:**
```
claude/<feature-name>-<session-id>
Example: claude/statistics-and-2fa-mt8lz
```

**Push format:**
```bash
git push -u origin claude/<branch-name>
# Retry up to 4 times with exponential backoff if network errors
```

### 5. File Operations

**Always use specialized tools:**
```bash
# DON'T use:
cat, head, tail, grep, find, sed, awk, echo >

# DO use:
Read    # Read files
Edit    # Edit files (exact string replacement)
Write   # Create new files
Glob    # Find files by pattern
Grep    # Search content
Bash    # Only for actual terminal commands (git, composer, etc.)
```

---

## 📁 Critical File Locations

### Controllers
```
src/app/Http/Controllers/
  ├── StatisticsController.php     # Statistics dashboard & exports
  ├── TwoFactorController.php      # 2FA setup & verification
  └── AuthController.php            # Login with 2FA integration
```

### Services (Business Logic)
```
src/app/Services/
  ├── StatisticsService.php        # All statistics calculations
  ├── BookingService.php           # Booking operations
  └── AuditLogService.php          # Audit logging
```

### Models
```
src/app/Models/
  ├── User.php                     # User + 2FA fields
  ├── Booking.php                  # Booking records
  ├── Department.php               # Pharmacy departments
  └── TrustedDevice.php            # 2FA trusted devices
```

### Views (Blade Templates)
```
src/resources/views/
  ├── admin/statistics/
  │   ├── super-admin.blade.php   # Super admin dashboard
  │   ├── pharmacy-admin.blade.php # Pharmacy admin dashboard
  │   └── pdf-export.blade.php     # PDF export template
  ├── auth/
  │   └── 2fa-challenge.blade.php  # 2FA verification page
  └── profile/
      └── 2fa-setup.blade.php      # 2FA setup wizard
```

### Exports
```
src/app/Exports/
  └── StatisticsExport.php         # Excel export (multiple sheets)
```

---

## 🔧 Environment & Tools

### Docker Commands
```bash
# Note: User system may not have docker command available
# If docker not found, work with files directly

# If docker is available:
docker exec -it pharmacy_php bash
docker exec pharmacy_php php artisan <command>
```

### Laravel Artisan
```bash
php artisan migrate              # Run migrations
php artisan cache:clear          # Clear cache
php artisan config:clear         # Clear config cache
php artisan view:clear           # Clear view cache
php artisan optimize             # Optimize application
```

### Composer
```bash
composer install                 # Install dependencies
composer update                  # Update dependencies
composer require <package>       # Add package

# Platform config (critical!):
# composer.json must have:
"config": {
    "platform": {
        "php": "8.2.0"
    }
}
```

---

## 🐛 Troubleshooting Patterns

### Pattern 1: Array vs Object Access
**Error:** `Attempt to read property "name" on array`

**Diagnosis:**
```php
// Service returns array
return ['name' => 'John', 'email' => 'john@example.com'];

// View tries to access as object
{{ $user->name }}  // ❌ Wrong

// Fix
{{ $user['name'] }} // ✅ Correct
```

### Pattern 2: Field Name Mismatches
**Error:** `Undefined array key "total_departments"`

**Diagnosis:**
```php
// Super admin uses:
'total_departments' => Department::where('is_active', 1)->count()

// Pharmacy admin uses:
'active_departments' => Department::where('is_active', 1)->count()

// Fix in view/export:
$overview['total_departments'] ?? $overview['active_departments'] ?? 'N/A'
```

### Pattern 3: Single-Pharmacy Issues
**Error:** `No pharmacy assigned to your account`

**Diagnosis:**
```php
// Code checks for pharmacy_id (doesn't exist!)
$pharmacyId = Auth::user()->pharmacy_id;
if (!$pharmacyId) {
    return redirect()->with('error', 'No pharmacy assigned');
}

// Fix: Remove check, set to null
$pharmacyId = null; // Single-pharmacy system
```

### Pattern 4: 2FA Login Loop
**Error:** After login, redirected back to login page

**Diagnosis:**
```php
// Code calls logout during 2FA
Auth::logout(); // ❌ Clears session!

// Fix: Keep user authenticated
session()->put('2fa:auth:id', $user->id);
// Don't logout!
```

### Pattern 5: Status Query Issues
**Error:** Approval rate shows 0%

**Diagnosis:**
```php
// Using wrong status name
WHERE bookings.status = "confirmed"  // ❌ Wrong status

// Fix: Use correct status
WHERE bookings.status = "approved"   // ✅ Correct
```

---

## 📊 Statistics Implementation Guide

### Service Method Template
```php
public static function getMetric(?int $pharmacyId = null, int $month = null, int $year = null): array
{
    // 1. Set defaults
    $month = $month ?? now()->month;
    $year = $year ?? now()->year;

    // 2. Calculate date range
    $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
    $endOfMonth = $startOfMonth->copy()->endOfMonth();

    // 3. Build query with date filtering
    $query = Booking::whereBetween('created_at', [$startOfMonth, $endOfMonth]);

    // 4. Note: $pharmacyId always null (single-pharmacy)

    // 5. Return data
    return [
        'labels' => [...],
        'data' => [...],
    ];
}
```

### Controller Usage
```php
public function index(Request $request)
{
    // Get selected month/year
    $selectedMonth = $request->input('month', now()->month);
    $selectedYear = $request->input('year', now()->year);

    // Call service with month/year
    $metric = StatisticsService::getMetric(null, $selectedMonth, $selectedYear);

    // Pass to view
    return view('admin.statistics.super-admin', compact('metric', 'selectedMonth', 'selectedYear'));
}
```

### View Usage (Blade)
```blade
<!-- Month/Year selection form -->
<form method="GET" action="{{ route('admin.statistics.index') }}">
    <select name="month">
        @foreach(range(1, 12) as $m)
            <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
            </option>
        @endforeach
    </select>
    <select name="year">
        @foreach(range(date('Y'), date('Y') - 5) as $y)
            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                {{ $y }}
            </option>
        @endforeach
    </select>
    <button type="submit">View Statistics</button>
</form>

<!-- Export buttons with month/year -->
<a href="{{ route('admin.statistics.export.excel', ['month' => $selectedMonth, 'year' => $selectedYear]) }}">
    Export Excel
</a>
```

---

## 🔐 2FA Implementation Guide

### Setup Flow
```php
// 1. Generate secret
$google2fa = app(Google2FA::class);
$secret = $google2fa->generateSecretKey();

// 2. Generate QR code URL
$qrCodeUrl = $google2fa->getQRCodeUrl(
    'Med-Rep Booking',
    $user->email,
    $secret
);

// 3. Generate recovery codes
$recoveryCodes = [];
for ($i = 0; $i < 10; $i++) {
    $recoveryCodes[] = Str::random(10);
}

// 4. Store in database (not enabled yet!)
$user->update([
    'google2fa_secret' => $secret,
    'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
]);

// 5. Show QR code + recovery codes to user

// 6. User confirms with first code
$valid = $google2fa->verifyKey($secret, $request->code);
if ($valid) {
    $user->update(['google2fa_enabled' => true]);
}
```

### Login Flow
```php
// 1. Validate credentials
if (!Auth::attempt($credentials, $remember)) {
    return back()->withErrors(['email' => 'Invalid credentials']);
}

$user = Auth::user();

// 2. Check if 2FA enabled
if (!$user->google2fa_enabled) {
    return redirect()->route($dashboardRoute);
}

// 3. Check trusted device
$isTrusted = TrustedDevice::where('user_id', $user->id)
    ->where('device_token', $request->cookie('trusted_device_token'))
    ->where('expires_at', '>', now())
    ->exists();

if ($isTrusted) {
    return redirect()->route($dashboardRoute);
}

// 4. Require 2FA
session()->put('2fa:auth:id', $user->id);
session()->put('2fa:auth:remember', $remember);
session()->put('2fa:verified', false);

return redirect()->route('2fa.challenge');
```

### Verification Flow
```php
// Get user from session (NOT from Auth::user()!)
$userId = session('2fa:auth:id');
$user = User::find($userId);

// Verify code
$google2fa = app(Google2FA::class);
$secret = decrypt($user->google2fa_secret);
$valid = $google2fa->verifyKey($secret, $request->code);

if (!$valid) {
    return back()->withErrors(['code' => 'Invalid code']);
}

// Mark as verified
session()->put('2fa:verified', true);

// Trust device if requested
if ($request->trust_device) {
    $token = Str::uuid();
    TrustedDevice::create([
        'user_id' => $user->id,
        'device_token' => $token,
        'device_name' => $request->userAgent(),
        'ip_address' => $request->ip(),
        'expires_at' => now()->addDays(30),
    ]);
    cookie()->queue('trusted_device_token', $token, 43200); // 30 days
}

// Log success
AuditLogService::log($user, 'login_2fa_success', null, ['method' => '2fa_code']);

// Redirect based on role
return redirect()->route($dashboardRoute);
```

---

## 🎨 Frontend Patterns

### Chart.js Implementation
```javascript
// Status Distribution (Pie Chart)
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Pending', 'Approved', 'Rejected', 'Cancelled'],
        datasets: [{
            data: [10, 25, 5, 3],
            backgroundColor: [
                'rgba(255, 193, 7, 0.8)',   // pending - yellow
                'rgba(40, 167, 69, 0.8)',   // approved - green
                'rgba(220, 53, 69, 0.8)',   // rejected - red
                'rgba(108, 117, 125, 0.8)'  // cancelled - gray
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
```

### Bootstrap Card Pattern
```blade
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Metric Name
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ number_format($value) }}
                    </div>
                </div>
                <div class="col-auto">
                    <i class="bi bi-icon-name fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>
```

---

## 📦 Export Implementation

### Excel Export Pattern
```php
// 1. Create Export class
class StatisticsExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new OverviewSheet($this->data['overview']),
            new DepartmentsSheet($this->data['departments']),
        ];
    }
}

// 2. Create Sheet classes
class OverviewSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    public function collection()
    {
        return collect([
            ['Metric', 'Value'],
            ['Total Bookings', $this->overview['total_bookings']],
        ]);
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function title(): string
    {
        return 'Overview';
    }
}

// 3. Controller usage
return Excel::download(
    new StatisticsExport($data, $isSuperAdmin),
    'statistics-report-' . date('Y-m-d-His') . '.xlsx'
);
```

### PDF Export Pattern
```php
// 1. Controller
$pdf = \PDF::loadView('admin.statistics.pdf-export', $data);
$pdf->setPaper('A4', 'landscape'); // Important!
return $pdf->download('statistics-report-' . date('Y-m-d-His') . '.pdf');

// 2. Blade view with inline CSS
<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 15mm; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
    </style>
</head>
<body>
    <!-- Content here -->
</body>
</html>
```

---

## 🧪 Testing Checklist

When implementing features, mentally verify:

### Statistics Feature
- [ ] Works for super admin
- [ ] Works for pharmacy admin
- [ ] Month selection changes data correctly
- [ ] Year selection works
- [ ] Charts display all data points
- [ ] Status distribution shows all 4 statuses
- [ ] Export to Excel works with selected month
- [ ] Export to PDF works (landscape orientation)
- [ ] Tables show correct data (array access)
- [ ] No errors in console/logs

### 2FA Feature
- [ ] QR code displays correctly
- [ ] Recovery codes generated
- [ ] Can verify with authenticator app
- [ ] Login redirects to 2FA challenge
- [ ] User stays authenticated during challenge
- [ ] Code verification works
- [ ] Recovery code works (single use)
- [ ] Trust device works (30 days)
- [ ] Can disable 2FA
- [ ] Audit logs created

---

## 💡 Pro Tips for Future Claude

### 1. Always Read First
Never edit a file without reading it first. The `Edit` tool will fail if you haven't read the file in this conversation.

### 2. Trust the User's Reports
If user says something isn't working, believe them. Don't assume the code is correct.

### 3. Field Name Variations
Super admin and pharmacy admin often have different field names:
- `total_representatives` vs `active_representatives`
- `total_departments` vs `active_departments`

Use null coalescing in exports: `$overview['field1'] ?? $overview['field2'] ?? 'N/A'`

### 4. Single-Pharmacy System
Whenever you see `pharmacy_id` checks or references, it's a bug. Remove them.

### 5. Array vs Object
StatisticsService returns arrays (not collections, not models). Always use bracket notation in views.

### 6. Status Names
Booking statuses are: `pending`, `approved`, `rejected`, `cancelled`
NOT: `confirmed`, `completed`, `declined`, etc.

### 7. Month/Year Filtering
Every statistics method should accept optional `$month` and `$year` parameters and default to current month.

### 8. Git Branch Names
Must start with `claude/` and end with session ID. Don't create branches without this format.

### 9. Commit Messages
Use conventional commits format: `type: description`
Keep descriptions under 72 characters.

### 10. Documentation
Only create docs when explicitly asked. Don't add markdown files proactively.

---

## 🔗 Quick Links

**Documentation:**
- Full System Documentation: `/Docs/STATISTICS_AND_2FA_DOCUMENTATION.md`
- This Guide: `/Docs/CLAUDE_GUIDE.md`

**Key Files:**
- Statistics Service: `src/app/Services/StatisticsService.php`
- Statistics Controller: `src/app/Http/Controllers/StatisticsController.php`
- 2FA Controller: `src/app/Http/Controllers/TwoFactorController.php`
- Auth Controller: `src/app/Http/Controllers/AuthController.php`

**Views:**
- Super Admin Dashboard: `src/resources/views/admin/statistics/super-admin.blade.php`
- Pharmacy Admin Dashboard: `src/resources/views/admin/statistics/pharmacy-admin.blade.php`
- 2FA Challenge: `src/resources/views/auth/2fa-challenge.blade.php`

**Database:**
- Migrations: `src/database/migrations/`
- Models: `src/app/Models/`

---

## 🆘 When You're Stuck

1. **Read the documentation first** (`/Docs/STATISTICS_AND_2FA_DOCUMENTATION.md`)
2. **Check this guide** for patterns and solutions
3. **Look at similar existing code** in the codebase
4. **Ask the user** for clarification - they know the system best
5. **Check Laravel logs** (`storage/logs/laravel.log`) for errors
6. **Search for patterns** using Grep tool
7. **Review git history** for recent changes: `git log --oneline -20`

---

## 🎯 Session Startup Checklist

When starting a new session:
1. [ ] Read this guide (you're doing it!)
2. [ ] Check current branch: `git branch`
3. [ ] Check git status: `git status`
4. [ ] Understand user's request clearly
5. [ ] Plan your approach
6. [ ] Execute with proper tools
7. [ ] Commit only when user confirms
8. [ ] Update documentation if needed

---

**Remember:** This is a LIVE PRODUCTION SYSTEM used by real users in Kuwait. Every change matters. Be careful, be thorough, and always prioritize system stability.

Good luck, future Claude! 🚀
