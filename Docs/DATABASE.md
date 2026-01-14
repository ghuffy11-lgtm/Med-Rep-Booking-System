# Database Schema Documentation

## Table of Contents
1. [Overview](#overview)
2. [Database Configuration](#database-configuration)
3. [Tables](#tables)
4. [Relationships](#relationships)
5. [Indexes](#indexes)
6. [Migrations](#migrations)
7. [Seeders](#seeders)
8. [Backup & Restore](#backup--restore)
9. [Common Queries](#common-queries)

---

## Overview

The Medical Representative Booking System uses MySQL 8.0 as its database management system. The database schema is managed through Laravel migrations, ensuring version control and consistency across environments.

**Database Name**: `pharmacy_db`
**Character Set**: `utf8mb4`
**Collation**: `utf8mb4_unicode_ci`

---

## Database Configuration

### Connection Settings

**Development** (docker-compose.yml):
```yaml
MYSQL_HOST: mysql (container name)
MYSQL_PORT: 3306 (internal), 3307 (external)
MYSQL_DATABASE: pharmacy_db
MYSQL_USER: pharmacy_user
MYSQL_PASSWORD: pharmacy_pass
MYSQL_ROOT_PASSWORD: root_password
```

**Production** (.env):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pharmacy_db
DB_USERNAME=pharmacy_user
DB_PASSWORD=secure_password_here
```

### Connection Pooling

Laravel uses persistent connections by default. For high-traffic environments, consider:
- MaxConnections: 150 (MySQL setting)
- Connection timeout: 30 seconds
- Read/Write splitting for large deployments

---

## Tables

### users

**Purpose**: Store user accounts (Super Admin, Pharmacy Admin, Representatives)

**Schema**:
```sql
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `civil_id` varchar(12) NOT NULL UNIQUE,
  `mobile_number` varchar(8) NOT NULL UNIQUE,
  `role` enum('super_admin','pharmacy_admin','representative') NOT NULL DEFAULT 'representative',
  `pharmacy_id` bigint(20) UNSIGNED NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_email_index` (`email`),
  KEY `users_civil_id_index` (`civil_id`),
  KEY `users_mobile_number_index` (`mobile_number`),
  KEY `users_pharmacy_id_foreign` (`pharmacy_id`),
  CONSTRAINT `users_pharmacy_id_foreign` FOREIGN KEY (`pharmacy_id`)
    REFERENCES `pharmacies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns**:
- `id`: Primary key
- `name`: Full name of the user
- `email`: Unique email address (used for login)
- `email_verified_at`: Timestamp of email verification (NULL if not verified)
- `password`: bcrypt hashed password
- `civil_id`: 12-digit Kuwait civil ID (unique identifier)
- `mobile_number`: 8-digit mobile number (Kuwait format)
- `role`: User role (super_admin, pharmacy_admin, representative)
- `pharmacy_id`: Foreign key to pharmacies table (NULL for super admin)
- `is_active`: Account status (0 = pending approval, 1 = active)
- `remember_token`: Token for "Remember Me" functionality
- `created_at`: Record creation timestamp
- `updated_at`: Record update timestamp

**Indexes**:
- PRIMARY: `id`
- UNIQUE: `email`, `civil_id`, `mobile_number`
- INDEX: `pharmacy_id`

**Validation Rules**:
```php
'name' => 'required|string|max:255'
'email' => 'required|email|unique:users,email'
'civil_id' => 'required|string|size:12|regex:/^[0-9]{12}$/|unique:users,civil_id'
'mobile_number' => 'required|string|size:8|regex:/^[0-9]{8}$/|unique:users,mobile_number'
'password' => Password::min(8)->max(64)->mixedCase()->numbers()->symbols()
'role' => 'required|in:super_admin,pharmacy_admin,representative'
'pharmacy_id' => 'required_if:role,pharmacy_admin,representative|exists:pharmacies,id'
```

---

### pharmacies

**Purpose**: Store pharmacy locations

**Schema**:
```sql
CREATE TABLE `pharmacies` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `contact_number` varchar(8) NOT NULL,
  `email` varchar(255) NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pharmacies_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns**:
- `id`: Primary key
- `name`: Pharmacy name (e.g., "Hadi Clinic Pharmacy - Salmiya")
- `location`: Full address or area (e.g., "Salmiya, Salem Al Mubarak Street")
- `contact_number`: 8-digit contact number
- `email`: Contact email (optional)
- `is_active`: Status (1 = active, 0 = inactive)
- `created_at`: Record creation timestamp
- `updated_at`: Record update timestamp

**Validation Rules**:
```php
'name' => 'required|string|max:255|unique:pharmacies,name'
'location' => 'required|string|max:255'
'contact_number' => 'required|string|size:8|regex:/^[0-9]{8}$/'
'email' => 'nullable|email|max:255'
'is_active' => 'boolean'
```

---

### departments

**Purpose**: Store pharmacy departments (e.g., Dermatology, Cardiology)

**Schema**:
```sql
CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pharmacy_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departments_pharmacy_id_foreign` (`pharmacy_id`),
  CONSTRAINT `departments_pharmacy_id_foreign` FOREIGN KEY (`pharmacy_id`)
    REFERENCES `pharmacies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns**:
- `id`: Primary key
- `pharmacy_id`: Foreign key to pharmacies table
- `name`: Department name (e.g., "Dermatology", "Cardiology")
- `description`: Detailed description (optional)
- `is_active`: Status (1 = active, 0 = inactive)
- `created_at`: Record creation timestamp
- `updated_at`: Record update timestamp

**Cascade Delete**: When a pharmacy is deleted, all its departments are deleted

**Validation Rules**:
```php
'pharmacy_id' => 'required|exists:pharmacies,id'
'name' => 'required|string|max:255'
'description' => 'nullable|string'
'is_active' => 'boolean'
```

---

### schedules

**Purpose**: Define department availability (working hours)

**Schema**:
```sql
CREATE TABLE `schedules` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `day_of_week` tinyint(3) UNSIGNED NOT NULL COMMENT '0=Sunday, 6=Saturday',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `slot_duration` tinyint(3) UNSIGNED NOT NULL DEFAULT 30 COMMENT 'Minutes per slot',
  `max_bookings_per_slot` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `schedules_department_id_foreign` (`department_id`),
  KEY `schedules_day_of_week_index` (`day_of_week`),
  CONSTRAINT `schedules_department_id_foreign` FOREIGN KEY (`department_id`)
    REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns**:
- `id`: Primary key
- `department_id`: Foreign key to departments table
- `day_of_week`: Day (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
- `start_time`: Opening time (e.g., "09:00:00")
- `end_time`: Closing time (e.g., "17:00:00")
- `slot_duration`: Minutes per booking slot (default: 30)
- `max_bookings_per_slot`: Maximum concurrent bookings (default: 1)
- `is_active`: Status (1 = active, 0 = inactive)
- `created_at`: Record creation timestamp
- `updated_at`: Record update timestamp

**Example**:
```php
// Monday 9 AM - 5 PM, 30-minute slots, 1 booking per slot
day_of_week: 1
start_time: '09:00:00'
end_time: '17:00:00'
slot_duration: 30
max_bookings_per_slot: 1
```

**Validation Rules**:
```php
'department_id' => 'required|exists:departments,id'
'day_of_week' => 'required|integer|min:0|max:6'
'start_time' => 'required|date_format:H:i'
'end_time' => 'required|date_format:H:i|after:start_time'
'slot_duration' => 'required|integer|min:15|max:120'
'max_bookings_per_slot' => 'required|integer|min:1|max:10'
```

---

### bookings

**Purpose**: Store booking appointments

**Schema**:
```sql
CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `pharmacy_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `booking_date` date NOT NULL,
  `time_slot` time NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `notes` text NULL,
  `cancellation_reason` text NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bookings_user_id_foreign` (`user_id`),
  KEY `bookings_pharmacy_id_foreign` (`pharmacy_id`),
  KEY `bookings_department_id_foreign` (`department_id`),
  KEY `bookings_booking_date_index` (`booking_date`),
  KEY `bookings_status_index` (`status`),
  CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_pharmacy_id_foreign` FOREIGN KEY (`pharmacy_id`)
    REFERENCES `pharmacies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_department_id_foreign` FOREIGN KEY (`department_id`)
    REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns**:
- `id`: Primary key
- `user_id`: Foreign key to users table (representative who created booking)
- `pharmacy_id`: Foreign key to pharmacies table
- `department_id`: Foreign key to departments table
- `booking_date`: Date of appointment (YYYY-MM-DD)
- `time_slot`: Time of appointment (HH:MM:SS)
- `status`: Current status
  - `pending`: Awaiting confirmation
  - `confirmed`: Confirmed by pharmacy admin
  - `cancelled`: Cancelled by user or admin
  - `completed`: Appointment completed
- `notes`: Additional notes from representative
- `cancellation_reason`: Reason for cancellation (if applicable)
- `cancelled_at`: Timestamp of cancellation
- `confirmed_at`: Timestamp of confirmation
- `completed_at`: Timestamp of completion
- `created_at`: Record creation timestamp
- `updated_at`: Record update timestamp

**Validation Rules**:
```php
'user_id' => 'required|exists:users,id'
'pharmacy_id' => 'required|exists:pharmacies,id'
'department_id' => 'required|exists:departments,id'
'booking_date' => 'required|date|after_or_equal:today'
'time_slot' => 'required|date_format:H:i'
'status' => 'in:pending,confirmed,cancelled,completed'
'notes' => 'nullable|string|max:500'
```

---

### audit_logs

**Purpose**: Audit trail for security and compliance

**Schema**:
```sql
CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NULL,
  `action` varchar(255) NOT NULL,
  `model` varchar(255) NULL,
  `record_id` bigint(20) UNSIGNED NULL,
  `changes` json NULL,
  `ip_address` varchar(45) NULL,
  `user_agent` text NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_action_index` (`action`),
  KEY `audit_logs_created_at_index` (`created_at`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns**:
- `id`: Primary key
- `user_id`: Foreign key to users table (NULL if system action)
- `action`: Action performed (e.g., "login", "booking_created", "user_approved")
- `model`: Model class name (e.g., "User", "Booking")
- `record_id`: ID of affected record
- `changes`: JSON of changes (before/after values)
- `ip_address`: IP address of user
- `user_agent`: Browser user agent string
- `created_at`: Timestamp of action

**Example Usage**:
```php
AuditLogService::log(
    'booking_created',
    auth()->id(),
    'Booking',
    $booking->id,
    null,
    request()->ip()
);
```

---

### password_reset_tokens

**Purpose**: Store password reset tokens

**Schema**:
```sql
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`),
  KEY `password_reset_tokens_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Columns**:
- `email`: User email (primary key)
- `token`: Hashed reset token
- `created_at`: Token creation timestamp

**Token Expiration**: 60 minutes (configurable in config/auth.php)

---

### sessions

**Purpose**: Store session data (file-based sessions)

**Schema**:
```sql
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NULL,
  `ip_address` varchar(45) NULL,
  `user_agent` text NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Note**: This table is used when `SESSION_DRIVER=database` in .env. Default is `file`.

---

## Relationships

### Entity Relationship Summary

```
users (1) ──────── (∞) bookings
users (∞) ──────── (1) pharmacies
pharmacies (1) ─── (∞) departments
pharmacies (1) ─── (∞) bookings
departments (1) ── (∞) schedules
departments (1) ── (∞) bookings
users (1) ──────── (∞) audit_logs
```

### Eloquent Relationships

#### User Model
```php
// User belongs to one Pharmacy
public function pharmacy(): BelongsTo
{
    return $this->belongsTo(Pharmacy::class);
}

// User has many Bookings
public function bookings(): HasMany
{
    return $this->hasMany(Booking::class);
}
```

#### Pharmacy Model
```php
// Pharmacy has many Users
public function users(): HasMany
{
    return $this->hasMany(User::class);
}

// Pharmacy has many Departments
public function departments(): HasMany
{
    return $this->hasMany(Department::class);
}

// Pharmacy has many Bookings
public function bookings(): HasMany
{
    return $this->hasMany(Booking::class);
}
```

#### Department Model
```php
// Department belongs to one Pharmacy
public function pharmacy(): BelongsTo
{
    return $this->belongsTo(Pharmacy::class);
}

// Department has many Schedules
public function schedules(): HasMany
{
    return $this->hasMany(Schedule::class);
}

// Department has many Bookings
public function bookings(): HasMany
{
    return $this->hasMany(Booking::class);
}
```

#### Booking Model
```php
// Booking belongs to User
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

// Booking belongs to Pharmacy
public function pharmacy(): BelongsTo
{
    return $this->belongsTo(Pharmacy::class);
}

// Booking belongs to Department
public function department(): BelongsTo
{
    return $this->belongsTo(Department::class);
}
```

---

## Indexes

### Primary Keys
All tables have an auto-increment `id` column as primary key.

### Unique Indexes
- `users.email`
- `users.civil_id`
- `users.mobile_number`
- `password_reset_tokens.email`

### Foreign Key Indexes
- `users.pharmacy_id`
- `departments.pharmacy_id`
- `schedules.department_id`
- `bookings.user_id`
- `bookings.pharmacy_id`
- `bookings.department_id`
- `audit_logs.user_id`
- `sessions.user_id`

### Performance Indexes
- `bookings.booking_date` - Fast date range queries
- `bookings.status` - Fast status filtering
- `schedules.day_of_week` - Fast schedule lookups
- `audit_logs.action` - Fast action filtering
- `audit_logs.created_at` - Fast time-based queries
- `sessions.last_activity` - Fast session cleanup

---

## Migrations

### Migration Files Location
`src/database/migrations/`

### Migration List (Chronological)

1. **2014_10_12_000000_create_users_table.php**
   - Creates users table

2. **2014_10_12_100000_create_password_reset_tokens_table.php**
   - Creates password_reset_tokens table

3. **2019_08_19_000000_create_failed_jobs_table.php**
   - Creates failed_jobs table (queue management)

4. **2019_12_14_000001_create_personal_access_tokens_table.php**
   - Creates personal_access_tokens table (API tokens)

5. **2026_01_10_000001_create_pharmacies_table.php**
   - Creates pharmacies table

6. **2026_01_10_000002_create_departments_table.php**
   - Creates departments table

7. **2026_01_10_000003_create_schedules_table.php**
   - Creates schedules table

8. **2026_01_10_000004_create_bookings_table.php**
   - Creates bookings table

9. **2026_01_10_000005_create_audit_logs_table.php**
   - Creates audit_logs table

10. **2026_01_12_000000_add_mobile_number_to_users_table.php**
    - Adds mobile_number column to users table

### Running Migrations

**Run all pending migrations**:
```bash
docker exec -it pharmacy_php php artisan migrate
```

**Rollback last migration**:
```bash
docker exec -it pharmacy_php php artisan migrate:rollback
```

**Rollback all migrations**:
```bash
docker exec -it pharmacy_php php artisan migrate:reset
```

**Rollback and re-run all migrations**:
```bash
docker exec -it pharmacy_php php artisan migrate:refresh
```

**Drop all tables and re-run migrations**:
```bash
docker exec -it pharmacy_php php artisan migrate:fresh
```

**Check migration status**:
```bash
docker exec -it pharmacy_php php artisan migrate:status
```

---

## Seeders

### Seeder Files Location
`src/database/seeders/`

### Available Seeders

**DatabaseSeeder.php** (Main seeder):
- Creates Super Admin user
- Creates sample pharmacies
- Creates sample departments
- Creates sample schedules

**Run all seeders**:
```bash
docker exec -it pharmacy_php php artisan db:seed
```

**Run specific seeder**:
```bash
docker exec -it pharmacy_php php artisan db:seed --class=PharmacySeeder
```

**Refresh migrations and seed**:
```bash
docker exec -it pharmacy_php php artisan migrate:fresh --seed
```

---

## Backup & Restore

### Manual Backup

**Backup database to file**:
```bash
docker exec pharmacy_mysql mysqldump \
  -u pharmacy_user \
  -ppharmacy_pass \
  pharmacy_db > backup_$(date +%Y%m%d_%H%M%S).sql
```

**Backup with compression**:
```bash
docker exec pharmacy_mysql mysqldump \
  -u pharmacy_user \
  -ppharmacy_pass \
  pharmacy_db | gzip > backup_$(date +%Y%m%d_%H%M%S).sql.gz
```

### Restore Database

**Restore from SQL file**:
```bash
docker exec -i pharmacy_mysql mysql \
  -u pharmacy_user \
  -ppharmacy_pass \
  pharmacy_db < backup.sql
```

**Restore from compressed file**:
```bash
gunzip < backup.sql.gz | docker exec -i pharmacy_mysql mysql \
  -u pharmacy_user \
  -ppharmacy_pass \
  pharmacy_db
```

### Automated Backups

**Cron job for daily backups** (Add to host crontab):
```bash
0 2 * * * /path/to/backup-script.sh
```

**backup-script.sh**:
```bash
#!/bin/bash
BACKUP_DIR="/backups/pharmacy_db"
DATE=$(date +%Y%m%d_%H%M%S)
FILENAME="pharmacy_db_$DATE.sql.gz"

docker exec pharmacy_mysql mysqldump \
  -u pharmacy_user \
  -ppharmacy_pass \
  pharmacy_db | gzip > "$BACKUP_DIR/$FILENAME"

# Keep only last 30 days of backups
find "$BACKUP_DIR" -name "pharmacy_db_*.sql.gz" -mtime +30 -delete
```

---

## Common Queries

### User Queries

**Find all active representatives**:
```sql
SELECT * FROM users
WHERE role = 'representative'
  AND is_active = 1
  AND email_verified_at IS NOT NULL;
```

**Find users pending approval**:
```sql
SELECT * FROM users
WHERE is_active = 0
  AND email_verified_at IS NOT NULL
ORDER BY created_at DESC;
```

**Count users by role**:
```sql
SELECT role, COUNT(*) as count
FROM users
GROUP BY role;
```

### Booking Queries

**Find today's bookings**:
```sql
SELECT b.*, u.name as user_name, d.name as department_name
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN departments d ON b.department_id = d.id
WHERE b.booking_date = CURDATE()
ORDER BY b.time_slot;
```

**Find available time slots for a department on a specific date**:
```sql
-- Get all possible slots from schedule
-- Then exclude already booked slots
SELECT s.start_time, s.end_time, s.slot_duration
FROM schedules s
WHERE s.department_id = ?
  AND s.day_of_week = DAYOFWEEK(?) - 1
  AND s.is_active = 1
  AND NOT EXISTS (
    SELECT 1 FROM bookings b
    WHERE b.department_id = ?
      AND b.booking_date = ?
      AND b.time_slot = s.start_time
      AND b.status IN ('pending', 'confirmed')
  );
```

**Count bookings by status**:
```sql
SELECT status, COUNT(*) as count
FROM bookings
GROUP BY status;
```

**Find overdue pending bookings (older than 7 days)**:
```sql
SELECT b.*, u.name as user_name
FROM bookings b
JOIN users u ON b.user_id = u.id
WHERE b.status = 'pending'
  AND b.created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY b.created_at;
```

### Pharmacy & Department Queries

**Find pharmacies with department count**:
```sql
SELECT p.*, COUNT(d.id) as department_count
FROM pharmacies p
LEFT JOIN departments d ON p.id = d.pharmacy_id AND d.is_active = 1
WHERE p.is_active = 1
GROUP BY p.id;
```

**Find busiest departments (most bookings)**:
```sql
SELECT d.name, p.name as pharmacy_name, COUNT(b.id) as booking_count
FROM departments d
JOIN pharmacies p ON d.pharmacy_id = p.id
LEFT JOIN bookings b ON d.id = b.department_id
GROUP BY d.id, d.name, p.name
ORDER BY booking_count DESC
LIMIT 10;
```

### Audit Log Queries

**Find recent login attempts**:
```sql
SELECT a.*, u.email
FROM audit_logs a
LEFT JOIN users u ON a.user_id = u.id
WHERE a.action = 'login'
ORDER BY a.created_at DESC
LIMIT 50;
```

**Find all actions by a specific user**:
```sql
SELECT * FROM audit_logs
WHERE user_id = ?
ORDER BY created_at DESC;
```

---

## Database Maintenance

### Optimize Tables
```bash
docker exec -it pharmacy_mysql mysql \
  -u pharmacy_user \
  -ppharmacy_pass \
  -e "OPTIMIZE TABLE pharmacy_db.users, pharmacy_db.bookings, pharmacy_db.audit_logs;"
```

### Analyze Tables (Update statistics)
```bash
docker exec -it pharmacy_mysql mysql \
  -u pharmacy_user \
  -ppharmacy_pass \
  -e "ANALYZE TABLE pharmacy_db.users, pharmacy_db.bookings;"
```

### Check Table Status
```bash
docker exec -it pharmacy_mysql mysql \
  -u pharmacy_user \
  -ppharmacy_pass \
  -e "SHOW TABLE STATUS FROM pharmacy_db;"
```

### Clean Old Audit Logs (Keep 90 days)
```sql
DELETE FROM audit_logs
WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

### Clean Expired Password Reset Tokens
```sql
DELETE FROM password_reset_tokens
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR);
```

---

## Performance Tips

1. **Use Eager Loading**: Avoid N+1 query problems
   ```php
   $bookings = Booking::with(['user', 'pharmacy', 'department'])->get();
   ```

2. **Use Indexes**: Ensure foreign keys and frequently queried columns are indexed

3. **Use Query Caching**: Cache frequently accessed data
   ```php
   $pharmacies = Cache::remember('active_pharmacies', 3600, function() {
       return Pharmacy::where('is_active', 1)->get();
   });
   ```

4. **Use Pagination**: Don't load all records at once
   ```php
   $bookings = Booking::paginate(20);
   ```

5. **Use Database Transactions**: For operations affecting multiple tables
   ```php
   DB::transaction(function() {
       // Multiple database operations
   });
   ```

---

## Conclusion

This database schema provides a robust foundation for the Medical Representative Booking System. Regular backups, proper indexing, and maintenance will ensure optimal performance and data integrity.

For more information:
- [Architecture Documentation](ARCHITECTURE.md)
- [Installation Guide](INSTALLATION.md)
- [Development Guide](DEVELOPMENT.md)
