# Hostinger Cloud Hosting Deployment Guide
## Med Rep Booking System - Laravel Application

**Target Environment:** Hostinger Cloud Hosting
**Access Method:** FTP + hPanel Dashboard
**Date:** January 12, 2026

---

## ✅ Prerequisites Checklist

Before starting deployment, ensure you have:

- [ ] Hostinger Cloud Hosting account active
- [ ] FTP credentials (from hPanel → Files → FTP Accounts)
- [ ] Domain already configured and pointing to Hostinger
- [ ] SSL certificate enabled (free Let's Encrypt available)
- [ ] Database access in hPanel
- [ ] Access to hPanel dashboard

---

## 📋 System Requirements

**Your Laravel App Requires:**
- PHP 8.1 or higher (8.2 recommended)
- MySQL 8.0 or compatible
- PHP Extensions: PDO, mbstring, exif, pcntl, bcmath, gd, zip, curl, xml
- Composer (for dependencies)
- 512MB+ PHP memory limit
- File upload size: 64MB+

**Check PHP Version in hPanel:**
1. Go to hPanel → Advanced → PHP Configuration
2. Ensure PHP 8.1+ is selected
3. Enable required extensions

---

## 🚀 Deployment Steps

### Step 1: Prepare Files Locally

**Option A: Download from GitHub**
```bash
# On your local computer
git clone https://github.com/ghuffy11-lgtm/Med-Rep-Booking-System.git
cd Med-Rep-Booking-System
```

**Option B: Export from Docker (if currently running)**
```bash
# If you have Docker version running
docker cp pharmacy_php:/var/www/html ./laravel-export
cd laravel-export
```

---

### Step 2: Install Dependencies Locally

**IMPORTANT:** You must install dependencies on your local computer first (Hostinger Cloud without SSH cannot run composer).

```bash
# Navigate to the src/ folder
cd src/

# Install production dependencies
composer install --no-dev --optimize-autoloader

# This creates the vendor/ folder with all Laravel dependencies
```

**What this does:**
- Downloads all Laravel packages
- Creates optimized autoloader
- Prepares application for production

---

### Step 3: Configure Environment File

1. **Copy `.env.example` to `.env`:**
```bash
cp .env.example .env
```

2. **Edit `.env` file with Hostinger details:**

```env
# Application
APP_NAME="Med Rep Booking System"
APP_ENV=production
APP_KEY=              # We'll generate this later
APP_DEBUG=false       # MUST be false in production
APP_URL=https://yourdomain.com

# Database (Get from hPanel → Databases)
DB_CONNECTION=mysql
DB_HOST=localhost     # Usually localhost on Hostinger
DB_PORT=3306
DB_DATABASE=u123456789_pharmacy  # Your Hostinger database name
DB_USERNAME=u123456789_pharma    # Your Hostinger database user
DB_PASSWORD=your_secure_password

# Mail Configuration (Use your SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Session & Cache
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Security
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
```

3. **Generate Application Key:**

You need to generate `APP_KEY`. Two options:

**Option A - On local computer:**
```bash
php artisan key:generate
# This updates .env with APP_KEY
```

**Option B - Manual generation:**
```bash
# Generate a random key
php -r "echo base64_encode(random_bytes(32));"
# Copy the output and add to .env:
# APP_KEY=base64:YOUR_GENERATED_KEY_HERE
```

---

### Step 4: Create Database on Hostinger

1. **Login to hPanel**
2. **Go to:** Databases → MySQL Databases
3. **Click:** "Create new database"
4. **Database Name:** `pharmacy_db` (or your choice)
5. **Username:** Create new user with strong password
6. **Grant:** All privileges to this user
7. **Note down:** Database name, username, password

**Important:** Use these credentials in your `.env` file from Step 3.

---

### Step 5: Upload Files via FTP

**What to Upload:**
Upload everything from `src/` folder EXCEPT:
- `.git/` (if present)
- `.env.example`
- `node_modules/` (if present)
- `tests/`
- `README.md` (optional)

**Where to Upload:**
```
Hostinger File Structure:
/domains/yourdomain.com/
    /public_html/          ← Upload public/ folder contents HERE
        index.php
        .htaccess
        css/
        js/
        images/
    /laravel/              ← Upload everything else HERE
        app/
        bootstrap/
        config/
        database/
        resources/
        routes/
        storage/
        vendor/
        artisan
        composer.json
        .env
```

**FTP Upload Steps:**

1. **Connect via FTP:**
   - Host: ftp.yourdomain.com
   - Port: 21
   - Username: (from hPanel → FTP Accounts)
   - Password: (from hPanel → FTP Accounts)
   - Use FileZilla or similar FTP client

2. **Create folder structure:**
   ```
   Create folder: /domains/yourdomain.com/laravel/
   ```

3. **Upload Laravel files:**
   - Upload `app/`, `bootstrap/`, `config/`, etc. → `/laravel/`
   - Upload `vendor/` → `/laravel/vendor/`
   - Upload `.env` → `/laravel/.env`
   - Upload `storage/` → `/laravel/storage/`

4. **Upload public files:**
   - Upload contents of `public/` → `/public_html/`
   - Make sure `index.php` is in `/public_html/`
   - Make sure `.htaccess` is in `/public_html/`

---

### Step 6: Update index.php Path

**Edit `/public_html/index.php`:**

Find these lines:
```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

Change to:
```php
require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';
```

**This tells Laravel where to find the application files.**

---

### Step 7: Set File Permissions

In hPanel File Manager:

1. **Set permissions for storage:**
   - `/laravel/storage/` → 775
   - `/laravel/storage/framework/` → 775
   - `/laravel/storage/logs/` → 775

2. **Set permissions for bootstrap/cache:**
   - `/laravel/bootstrap/cache/` → 775

**How to set permissions in hPanel:**
1. Go to Files → File Manager
2. Navigate to folder
3. Right-click → Permissions
4. Set to 775 (rwxrwxr-x)

---

### Step 8: Run Database Migrations

**Since you don't have SSH, you need to run migrations via a temporary script.**

1. **Create file:** `/public_html/migrate.php`

```php
<?php
// TEMPORARY MIGRATION SCRIPT - DELETE AFTER USE

require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Run migrations
Artisan::call('migrate', ['--force' => true]);
echo "Migration output:\n";
echo Artisan::output();

// OPTIONAL: Seed database (if needed)
// Artisan::call('db:seed', ['--force' => true]);
// echo "Seed output:\n";
// echo Artisan::output();

echo "\n\nDone! DELETE THIS FILE NOW!";
?>
```

2. **Run migration:**
   - Visit: https://yourdomain.com/migrate.php
   - Check output for success
   - **IMMEDIATELY DELETE THIS FILE** after use (security risk!)

3. **Delete:** `/public_html/migrate.php`

---

### Step 9: Configure .htaccess

**Ensure `/public_html/.htaccess` contains:**

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

### Step 10: Configure PHP Settings

**In hPanel → Advanced → PHP Configuration:**

Set these values:
```
memory_limit = 512M
max_execution_time = 300
upload_max_filesize = 64M
post_max_size = 64M
max_input_vars = 3000
```

Enable extensions:
- [x] pdo_mysql
- [x] mbstring
- [x] gd
- [x] zip
- [x] curl
- [x] xml
- [x] bcmath

---

### Step 11: Test the Application

1. **Visit:** https://yourdomain.com
2. **Check for:**
   - ✅ Homepage loads
   - ✅ No Laravel errors
   - ✅ SSL certificate is active (https://)
   - ✅ Login page accessible
   - ✅ Registration works
   - ✅ Database connections work

3. **Common issues:**
   - **500 Error:** Check file permissions on storage/ and bootstrap/cache/
   - **404 on routes:** Check .htaccess file exists and mod_rewrite is enabled
   - **Database error:** Verify .env database credentials
   - **APP_KEY error:** Generate APP_KEY in .env

---

### Step 12: Security Hardening

1. **Delete unnecessary files:**
```
/public_html/migrate.php (if you created it)
/public_html/install.php (if exists)
```

2. **Verify `.env` is NOT in public_html:**
   - `.env` should be in `/laravel/.env` ONLY
   - Should NOT be accessible via web browser

3. **Test security:**
   - Visit: https://yourdomain.com/.env (should get 404)
   - Visit: https://yourdomain.com/laravel/ (should get 403 or 404)

4. **Enable Cloudflare (Optional but recommended):**
   - Go to hPanel → Website → Cloudflare
   - Enable for additional DDoS protection

---

## 🔄 Updating the Application

When you need to deploy updates:

1. **Pull latest from GitHub**
2. **Install dependencies:** `composer install --no-dev`
3. **Upload changed files via FTP**
4. **Clear cache** (create temporary script):

```php
<?php
// cache-clear.php - DELETE AFTER USE
require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Artisan::call('cache:clear');
Artisan::call('config:clear');
Artisan::call('route:clear');
Artisan::call('view:clear');

echo "Cache cleared!";
?>
```

5. Visit: https://yourdomain.com/cache-clear.php
6. DELETE the cache-clear.php file immediately

---

## 📧 Email Configuration

**Using Hostinger SMTP:**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@yourdomain.com
```

**Create email account:**
1. hPanel → Emails → Email Accounts
2. Create new email account
3. Use credentials in .env

---

## 🐛 Troubleshooting

### Issue: White page / 500 Error

**Solution:**
1. Check storage/ permissions (775)
2. Check bootstrap/cache/ permissions (775)
3. Verify .env file exists in /laravel/
4. Check PHP version is 8.1+

### Issue: CSS/JS not loading

**Solution:**
1. Verify public files are in /public_html/
2. Check .htaccess exists in /public_html/
3. Clear browser cache
4. Run asset optimization command

### Issue: Database connection error

**Solution:**
1. Verify database credentials in .env
2. Check DB_HOST=localhost
3. Ensure database exists in hPanel
4. Test database connection via phpMyAdmin

### Issue: Routes returning 404

**Solution:**
1. Check .htaccess in /public_html/
2. Verify mod_rewrite is enabled (should be by default)
3. Clear route cache

---

## 📝 Maintenance Tasks

**Regular maintenance:**

1. **Monitor logs:**
   - Download `/laravel/storage/logs/laravel.log` via FTP
   - Check for errors regularly

2. **Backup database:**
   - hPanel → Databases → phpMyAdmin
   - Export database regularly

3. **Backup files:**
   - Download entire `/laravel/` folder monthly via FTP

4. **Update dependencies:**
   - Run `composer update` locally
   - Upload updated vendor/ folder

---

## ✅ Post-Deployment Checklist

After deployment, verify:

- [ ] Homepage loads without errors
- [ ] SSL certificate active (https://)
- [ ] Login functionality works
- [ ] Registration and email verification works
- [ ] Booking creation works
- [ ] Admin panel accessible
- [ ] Mobile view displays correctly
- [ ] Email notifications sending
- [ ] Database connections stable
- [ ] File uploads working (if applicable)
- [ ] Cron jobs configured (if needed)
- [ ] Error logging enabled
- [ ] Cache configured properly
- [ ] Security: .env not web-accessible
- [ ] Security: laravel/ folder not web-accessible

---

## 🆘 Need Help?

**Hostinger Support:**
- Live Chat: Available 24/7 in hPanel
- Knowledge Base: https://support.hostinger.com

**Laravel Documentation:**
- https://laravel.com/docs/10.x/deployment

**Application Support:**
- Check SESSION_SUMMARY_JAN_11_2026.md for detailed app info
- Contact: m.d.office@hadiclinic.com.kw

---

**End of Deployment Guide**
