# Troubleshooting Guide

## Table of Contents
1. [Installation Issues](#installation-issues)
2. [Authentication & Login Issues](#authentication--login-issues)
3. [Database Issues](#database-issues)
4. [Email Issues](#email-issues)
5. [Booking Issues](#booking-issues)
6. [Performance Issues](#performance-issues)
7. [Docker Issues](#docker-issues)
8. [Error Messages](#error-messages)
9. [Common Questions](#common-questions)
10. [Getting Support](#getting-support)

---

## Installation Issues

### Issue: Docker containers won't start

**Symptoms**:
- `docker-compose up` fails
- Containers exit immediately
- Port binding errors

**Solutions**:

**1. Port already in use**:
```bash
# Check what's using port 8080
sudo lsof -i :8080

# Check what's using port 3307
sudo lsof -i :3307

# Kill the process or change port in docker-compose.yml
```

**2. Docker daemon not running**:
```bash
# Start Docker
sudo systemctl start docker

# Enable Docker on boot
sudo systemctl enable docker
```

**3. Insufficient permissions**:
```bash
# Add user to docker group
sudo usermod -aG docker $USER

# Log out and log back in
```

**4. Corrupted containers**:
```bash
# Remove all containers and volumes
docker-compose down -v

# Rebuild
docker-compose up -d --build
```

---

### Issue: Cannot access application at localhost:8080

**Symptoms**:
- Browser shows "can't connect" or "timeout"
- ERR_CONNECTION_REFUSED

**Solutions**:

**1. Check containers are running**:
```bash
docker ps
# Should show pharmacy_nginx, pharmacy_php, pharmacy_mysql
```

**2. Check Nginx logs**:
```bash
docker logs pharmacy_nginx
```

**3. Check if port is accessible**:
```bash
curl http://localhost:8080
```

**4. Firewall blocking**:
```bash
# Ubuntu/Debian
sudo ufw allow 8080

# CentOS/RHEL
sudo firewall-cmd --add-port=8080/tcp --permanent
sudo firewall-cmd --reload
```

---

### Issue: Composer install fails

**Symptoms**:
- "Your requirements could not be resolved"
- PHP version incompatibility
- Memory limit errors

**Solutions**:

**1. PHP version mismatch**:
```bash
# Check PHP version (must be 8.2+)
docker exec -it pharmacy_php php -v

# If wrong version, rebuild containers
docker-compose down
docker-compose up -d --build
```

**2. Memory limit**:
```bash
# Increase memory limit
docker exec -it pharmacy_php php -d memory_limit=2G /usr/local/bin/composer install
```

**3. Clear composer cache**:
```bash
docker exec -it pharmacy_php composer clear-cache
docker exec -it pharmacy_php composer install
```

**4. Update composer**:
```bash
docker exec -it pharmacy_php composer self-update
docker exec -it pharmacy_php composer install
```

---

### Issue: Permission denied errors

**Symptoms**:
- Cannot write to storage/
- Cannot write to bootstrap/cache/
- 500 error on page load

**Solutions**:

```bash
# Fix permissions
docker exec -it pharmacy_php chmod -R 775 storage bootstrap/cache
docker exec -it pharmacy_php chown -R www-data:www-data storage bootstrap/cache

# If still issues, use 777 (less secure, dev only)
docker exec -it pharmacy_php chmod -R 777 storage bootstrap/cache
```

---

## Authentication & Login Issues

### Issue: Cannot login - "These credentials do not match our records"

**Symptoms**:
- Correct email and password but login fails
- Worked before but not now

**Solutions**:

**1. Verify user exists**:
```bash
docker exec -it pharmacy_php php artisan tinker
```
```php
User::where('email', 'your-email@example.com')->first()
```

**2. Verify email is verified**:
```php
$user = User::where('email', 'your-email@example.com')->first();
$user->email_verified_at  // Should not be null
```

**3. Verify account is active**:
```php
$user = User::where('email', 'your-email@example.com')->first();
$user->is_active  // Should be 1
```

**4. Reset password**:
- Use "Forgot Password" feature
- Or reset via tinker:
```php
$user = User::where('email', 'your-email@example.com')->first();
$user->password = Hash::make('NewPassword123!');
$user->save();
```

---

### Issue: "Please verify your email address" message

**Symptoms**:
- Registered but cannot login
- Message says to verify email
- Already clicked verification link

**Solutions**:

**1. Check verification status**:
```bash
docker exec -it pharmacy_php php artisan tinker
```
```php
$user = User::where('email', 'your-email@example.com')->first();
$user->email_verified_at  // Check if null
```

**2. Manually verify**:
```php
$user = User::where('email', 'your-email@example.com')->first();
$user->email_verified_at = now();
$user->save();
```

**3. Resend verification email**:
- Try to login
- Click "Resend Verification Email"
- Check inbox (and spam folder)

---

### Issue: "Account pending approval" message

**Symptoms**:
- Verified email but still cannot login
- Message says account pending approval

**Solutions**:

**1. Wait for admin approval** (normal):
- Usually takes 1-2 business days
- Admin will activate account

**2. Manually activate** (if you're admin):
```bash
docker exec -it pharmacy_php php artisan tinker
```
```php
$user = User::where('email', 'user-email@example.com')->first();
$user->is_active = 1;
$user->save();
```

---

### Issue: Session expires too quickly

**Symptoms**:
- Logged out after a few minutes
- Have to login repeatedly

**Solutions**:

**1. Check session lifetime in .env**:
```env
SESSION_LIFETIME=120  # Minutes (increase if needed)
```

**2. Check "Remember Me" checkbox** when logging in

**3. Clear browser cookies** and try again

**4. Check session driver**:
```env
SESSION_DRIVER=file  # Or database, redis
```

---

### Issue: CSRF token mismatch (419 error)

**Symptoms**:
- 419 Page Expired error when submitting forms
- "CSRF token mismatch"

**Solutions**:

**1. Clear browser cache**:
- Hard refresh: Ctrl+Shift+R (Windows/Linux) or Cmd+Shift+R (Mac)

**2. Try incognito/private mode**

**3. Check CSRF token in form**:
```blade
<form method="POST">
    @csrf  {{-- Should be present --}}
    ...
</form>
```

**4. Clear application cache**:
```bash
docker exec -it pharmacy_php php artisan cache:clear
docker exec -it pharmacy_php php artisan config:clear
```

**5. Check session is working**:
```bash
# Check storage/framework/sessions/ has files
docker exec -it pharmacy_php ls -la storage/framework/sessions/
```

---

## Database Issues

### Issue: "Access denied for user" error

**Symptoms**:
- SQLSTATE[HY000] [1045] Access denied
- Cannot connect to database

**Solutions**:

**1. Verify database credentials in .env**:
```env
DB_HOST=mysql  # Container name for Docker
DB_PORT=3306
DB_DATABASE=pharmacy_db
DB_USERNAME=pharmacy_user
DB_PASSWORD=pharmacy_pass
```

**2. Check if MySQL container is running**:
```bash
docker ps | grep mysql
```

**3. Test database connection**:
```bash
docker exec -it pharmacy_mysql mysql -u pharmacy_user -ppharmacy_pass pharmacy_db
```

**4. If connection fails, recreate database**:
```bash
docker exec -it pharmacy_mysql mysql -u root -proot_password
```
```sql
DROP DATABASE IF EXISTS pharmacy_db;
CREATE DATABASE pharmacy_db;
GRANT ALL PRIVILEGES ON pharmacy_db.* TO 'pharmacy_user'@'%';
FLUSH PRIVILEGES;
EXIT;
```

---

### Issue: "Table doesn't exist" error

**Symptoms**:
- SQLSTATE[42S02]: Base table or view not found
- Table 'pharmacy_db.users' doesn't exist

**Solutions**:

**1. Run migrations**:
```bash
docker exec -it pharmacy_php php artisan migrate
```

**2. Check migration status**:
```bash
docker exec -it pharmacy_php php artisan migrate:status
```

**3. If migrations are stuck, reset**:
```bash
docker exec -it pharmacy_php php artisan migrate:fresh
```

**⚠️ WARNING**: `migrate:fresh` drops all tables! Only use in development.

---

### Issue: "Column not found" error

**Symptoms**:
- SQLSTATE[42S22]: Column not found
- Column 'mobile_number' doesn't exist

**Solutions**:

**1. Run pending migrations**:
```bash
docker exec -it pharmacy_php php artisan migrate
```

**2. Check if migration file exists**:
```bash
docker exec -it pharmacy_php ls src/database/migrations/ | grep mobile_number
```

**3. If migration exists but didn't run**:
```bash
# Check migrations table
docker exec -it pharmacy_mysql mysql -u pharmacy_user -ppharmacy_pass pharmacy_db -e "SELECT * FROM migrations;"

# Run specific migration
docker exec -it pharmacy_php php artisan migrate --path=/database/migrations/2026_01_12_000000_add_mobile_number_to_users_table.php
```

---

### Issue: Database connection timeout

**Symptoms**:
- SQLSTATE[HY000] [2002] Connection timed out
- Slow queries

**Solutions**:

**1. Restart MySQL container**:
```bash
docker restart pharmacy_mysql
```

**2. Check MySQL logs**:
```bash
docker logs pharmacy_mysql
```

**3. Increase connection timeout in .env**:
```env
DB_TIMEOUT=60
```

**4. Check MySQL resource usage**:
```bash
docker stats pharmacy_mysql
```

---

## Email Issues

### Issue: Emails not being sent

**Symptoms**:
- No verification emails received
- No booking confirmation emails
- No errors in logs

**Solutions**:

**1. Check email configuration in .env**:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pharmacy.local"
MAIL_FROM_NAME="${APP_NAME}"
```

**2. Test email sending**:
```bash
docker exec -it pharmacy_php php artisan tinker
```
```php
Mail::raw('Test email', function($msg) {
    $msg->to('test@example.com')->subject('Test');
});
```

**3. Check Laravel logs**:
```bash
docker exec -it pharmacy_php tail -f storage/logs/laravel.log
```

**4. For Gmail**:
- Enable 2-factor authentication
- Generate App Password
- Use App Password in MAIL_PASSWORD

**5. For development, use MailHog**:
```env
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

Add MailHog to docker-compose.yml:
```yaml
mailhog:
  image: mailhog/mailhog:latest
  ports:
    - "8025:8025"  # Web UI
    - "1025:1025"  # SMTP
```

Access at: http://localhost:8025

---

### Issue: Emails going to spam

**Symptoms**:
- Emails sent but received in spam folder
- Low deliverability

**Solutions**:

**1. Configure SPF record** (DNS):
```
v=spf1 include:_spf.google.com ~all
```

**2. Configure DKIM** (email provider)

**3. Use reputable SMTP service**:
- SendGrid
- Mailgun
- Amazon SES
- Postmark

**4. Use proper from address**:
```env
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Pharmacy Booking"
```

---

### Issue: "Failed to authenticate" SMTP error

**Symptoms**:
- Swift_TransportException: Failed to authenticate
- Authentication credentials invalid

**Solutions**:

**1. For Gmail**:
- Use App Password, not regular password
- Enable "Less secure app access" (not recommended)

**2. Verify credentials**:
```bash
# Test SMTP connection
telnet smtp.gmail.com 587
```

**3. Check encryption setting**:
```env
MAIL_ENCRYPTION=tls  # Or ssl
```

**4. Check port**:
- Port 587: TLS
- Port 465: SSL
- Port 25: No encryption (usually blocked)

---

## Booking Issues

### Issue: No time slots available

**Symptoms**:
- Select date but no time slots show
- Time slots section is empty
- "No available slots" message

**Solutions**:

**1. Check if department has schedule**:
```bash
docker exec -it pharmacy_php php artisan tinker
```
```php
$dept = Department::find(1);
$dept->schedules;  // Should return schedules
```

**2. Check if schedule is active**:
```php
$dept = Department::find(1);
$dept->schedules()->where('is_active', 1)->get();
```

**3. Check if date matches schedule day**:
```php
// Monday = 1, Tuesday = 2, ..., Sunday = 0
$date = '2026-01-15';  // Example date
$dayOfWeek = date('w', strtotime($date));  // 0-6

$schedule = Schedule::where('department_id', 1)
    ->where('day_of_week', $dayOfWeek)
    ->where('is_active', 1)
    ->first();
```

**4. Check if slots are already booked**:
```php
$bookings = Booking::where('department_id', 1)
    ->where('booking_date', '2026-01-15')
    ->where('status', '!=', 'cancelled')
    ->get();
```

**5. Create schedule if missing**:
- Login as admin
- Go to Departments → Select Department → Schedules
- Click "Add Schedule"

---

### Issue: Cannot cancel booking

**Symptoms**:
- Cancel button not visible
- Cancel button disabled
- "Cannot cancel" error

**Solutions**:

**1. Check booking status**:
- Can only cancel Pending or Confirmed bookings
- Cannot cancel Completed or already Cancelled

**2. Check cancellation deadline**:
- Cannot cancel if less than 24 hours before appointment
- Check system settings for cancellation policy

**3. Contact admin**:
- Admin can cancel any booking
- Explain reason for cancellation

---

### Issue: Booking created but not showing in list

**Symptoms**:
- Success message shown
- Booking not in "My Bookings"
- No confirmation email

**Solutions**:

**1. Check database**:
```bash
docker exec -it pharmacy_php php artisan tinker
```
```php
$bookings = Booking::where('user_id', auth()->id())->get();
```

**2. Check filters**:
- Remove any status filters
- Check date range filter
- Try "All Bookings" view

**3. Hard refresh browser**:
- Ctrl+Shift+R (Windows/Linux)
- Cmd+Shift+R (Mac)

**4. Check Laravel logs for errors**:
```bash
docker exec -it pharmacy_php tail -f storage/logs/laravel.log
```

---

## Performance Issues

### Issue: Pages loading slowly

**Symptoms**:
- Long page load times (>5 seconds)
- Slow database queries
- Timeout errors

**Solutions**:

**1. Enable caching**:
```bash
docker exec -it pharmacy_php php artisan config:cache
docker exec -it pharmacy_php php artisan route:cache
docker exec -it pharmacy_php php artisan view:cache
```

**2. Clear cache if stale**:
```bash
docker exec -it pharmacy_php php artisan cache:clear
docker exec -it pharmacy_php php artisan config:clear
docker exec -it pharmacy_php php artisan route:clear
docker exec -it pharmacy_php php artisan view:clear
```

**3. Optimize database**:
```bash
docker exec -it pharmacy_mysql mysql -u pharmacy_user -ppharmacy_pass pharmacy_db -e "OPTIMIZE TABLE users, bookings, pharmacies, departments;"
```

**4. Enable OPcache** (PHP):
Check `docker/php/Dockerfile` has:
```dockerfile
RUN docker-php-ext-install opcache
```

**5. Use database indexes**:
- Indexes already added in migrations
- Check with:
```sql
SHOW INDEX FROM bookings;
```

**6. Use eager loading**:
```php
// Good
$bookings = Booking::with(['user', 'pharmacy', 'department'])->get();

// Bad (N+1 problem)
$bookings = Booking::all();
foreach ($bookings as $booking) {
    echo $booking->user->name;
}
```

---

### Issue: High memory usage

**Symptoms**:
- PHP memory limit errors
- Container using excessive RAM
- Out of memory errors

**Solutions**:

**1. Increase PHP memory limit**:
Edit `docker/php/Dockerfile`:
```dockerfile
RUN echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memory-limit.ini
```

Rebuild:
```bash
docker-compose down
docker-compose up -d --build
```

**2. Use pagination**:
```php
// Instead of
$bookings = Booking::all();

// Use
$bookings = Booking::paginate(20);
```

**3. Use chunking for large datasets**:
```php
Booking::chunk(100, function ($bookings) {
    foreach ($bookings as $booking) {
        // Process booking
    }
});
```

**4. Optimize queries**:
- Select only needed columns
- Use indexes
- Avoid N+1 problems

---

## Docker Issues

### Issue: "Cannot remove container" error

**Symptoms**:
- docker-compose down fails
- "container is in use"
- "device or resource busy"

**Solutions**:

**1. Force remove**:
```bash
docker-compose down --remove-orphans
docker rm -f pharmacy_nginx pharmacy_php pharmacy_mysql
```

**2. Stop all containers**:
```bash
docker stop $(docker ps -aq)
docker rm $(docker ps -aq)
```

**3. Restart Docker daemon**:
```bash
sudo systemctl restart docker
```

---

### Issue: Volumes not persisting data

**Symptoms**:
- Database reset after restart
- Uploaded files disappear
- Data loss

**Solutions**:

**1. Check volumes in docker-compose.yml**:
```yaml
volumes:
  mysql_data:

services:
  mysql:
    volumes:
      - mysql_data:/var/lib/mysql
```

**2. Check volume exists**:
```bash
docker volume ls | grep pharmacy
```

**3. If volume missing, create**:
```bash
docker volume create med-rep-booking-system_mysql_data
```

---

### Issue: "No space left on device"

**Symptoms**:
- Docker commands fail
- Cannot create containers
- Disk space errors

**Solutions**:

**1. Check disk space**:
```bash
df -h
```

**2. Clean Docker**:
```bash
# Remove unused images
docker image prune -a

# Remove unused volumes
docker volume prune

# Remove unused containers
docker container prune

# Remove everything unused
docker system prune -a --volumes
```

**3. Check Docker disk usage**:
```bash
docker system df
```

---

## Error Messages

### 500 Internal Server Error

**Symptoms**:
- White page with "500 Internal Server Error"
- No detailed error message

**Solutions**:

**1. Enable debug mode temporarily**:
```env
APP_DEBUG=true
```

**2. Check Laravel logs**:
```bash
docker exec -it pharmacy_php tail -50 storage/logs/laravel.log
```

**3. Check Nginx error log**:
```bash
docker logs pharmacy_nginx
```

**4. Check PHP error log**:
```bash
docker exec -it pharmacy_php tail -f /var/log/php-fpm-error.log
```

**5. Common causes**:
- Missing .env file
- Incorrect APP_KEY
- File permission issues
- Missing dependencies

---

### 404 Not Found

**Symptoms**:
- Page shows "404 Not Found"
- Route exists but not found

**Solutions**:

**1. Clear route cache**:
```bash
docker exec -it pharmacy_php php artisan route:clear
docker exec -it pharmacy_php php artisan route:cache
```

**2. Check if route exists**:
```bash
docker exec -it pharmacy_php php artisan route:list | grep bookings
```

**3. Check Nginx configuration**:
- Verify `try_files` directive
- Ensure all requests go to `index.php`

---

### 403 Forbidden

**Symptoms**:
- "403 Forbidden" error
- "You don't have permission"

**Solutions**:

**1. Check file permissions**:
```bash
docker exec -it pharmacy_php ls -la /var/www/html
```

**2. Check role/permissions**:
- Verify user has required role
- Check middleware on route

**3. Check .htaccess** (if applicable)

---

### 419 Page Expired

**Symptoms**:
- "419 Page Expired" when submitting form
- CSRF token mismatch

**Solutions**:

**1. Add @csrf to form**:
```blade
<form method="POST">
    @csrf
    ...
</form>
```

**2. Clear browser cache**

**3. Check session is working**:
```bash
docker exec -it pharmacy_php php artisan tinker
```
```php
session(['test' => 'value']);
session('test');  // Should return 'value'
```

---

## Common Questions

### Q: How do I reset admin password?

**A**: Via command line:
```bash
docker exec -it pharmacy_php php artisan tinker
```
```php
$admin = User::where('email', 'admin@example.com')->first();
$admin->password = Hash::make('NewPassword123!');
$admin->save();
```

---

### Q: How do I backup the database?

**A**:
```bash
docker exec pharmacy_mysql mysqldump -u pharmacy_user -ppharmacy_pass pharmacy_db > backup_$(date +%Y%m%d).sql
```

---

### Q: How do I restore a backup?

**A**:
```bash
docker exec -i pharmacy_mysql mysql -u pharmacy_user -ppharmacy_pass pharmacy_db < backup_20260114.sql
```

---

### Q: How do I view logs?

**A**:
```bash
# Laravel logs
docker exec -it pharmacy_php tail -f storage/logs/laravel.log

# Nginx logs
docker logs pharmacy_nginx

# PHP logs
docker logs pharmacy_php

# MySQL logs
docker logs pharmacy_mysql
```

---

### Q: How do I add a new admin user?

**A**:
```bash
docker exec -it pharmacy_php php artisan tinker
```
```php
User::create([
    'name' => 'New Admin',
    'email' => 'newadmin@example.com',
    'password' => Hash::make('SecurePassword123!'),
    'civil_id' => '123456789012',
    'mobile_number' => '98765432',
    'role' => 'super_admin',
    'is_active' => 1,
    'email_verified_at' => now(),
]);
```

---

### Q: How do I clear all caches?

**A**:
```bash
docker exec -it pharmacy_php php artisan optimize:clear
```

This clears:
- Application cache
- Config cache
- Route cache
- View cache
- Compiled classes

---

### Q: Application is slow after update

**A**: Rebuild caches:
```bash
docker exec -it pharmacy_php php artisan optimize:clear
docker exec -it pharmacy_php php artisan optimize
```

---

## Getting Support

### Before Contacting Support

1. **Check this troubleshooting guide**
2. **Check Laravel logs**: `storage/logs/laravel.log`
3. **Check error message carefully**
4. **Try searching the error online**
5. **Check if issue persists in incognito/private mode**

### Information to Provide

When contacting support, provide:

1. **Error message**: Exact text of error
2. **Steps to reproduce**: What were you doing when error occurred?
3. **Environment**: Dev, staging, or production?
4. **Browser/device**: Chrome 120 on Windows 11
5. **Logs**: Relevant log entries
6. **Screenshots**: If applicable
7. **What you've tried**: Solutions already attempted

### Contact Information

- **Technical Support**: tech-support@example.com
- **Emergency Hotline**: +965 XXXX XXXX (24/7 for critical issues)
- **Documentation**: https://your-domain.com/docs
- **Issue Tracker**: https://github.com/your-org/project/issues

### Response Times

- **Critical** (system down): 1 hour
- **High** (major functionality broken): 4 hours
- **Medium** (minor functionality affected): 1 business day
- **Low** (questions, enhancements): 2-3 business days

---

**Version**: 1.0
**Last Updated**: January 2026
**Document ID**: TROUBLESHOOT-001

For immediate assistance with critical issues: +965 XXXX XXXX
