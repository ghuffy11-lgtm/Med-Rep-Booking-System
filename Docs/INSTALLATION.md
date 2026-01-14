# Installation Guide

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Local Development Setup](#local-development-setup)
3. [Production Deployment](#production-deployment)
4. [Docker Configuration](#docker-configuration)
5. [Environment Configuration](#environment-configuration)
6. [Database Setup](#database-setup)
7. [Verification](#verification)

---

## Prerequisites

### System Requirements
- **Docker**: 20.10+ with Docker Compose
- **Git**: 2.x+
- **Minimum RAM**: 2GB
- **Free Disk Space**: 10GB

### Required Accounts
- **Email Provider**: SMTP credentials for sending emails
- **hCaptcha**: Site key and secret key ([Get them here](https://www.hcaptcha.com/))

---

## Local Development Setup

### Step 1: Clone the Repository

```bash
git clone https://github.com/ghuffy11-lgtm/Med-Rep-Booking-System.git
cd Med-Rep-Booking-System
```

### Step 2: Configure Environment

```bash
# Copy environment example
cp .env.example src/.env

# Generate application key
docker-compose run --rm php php artisan key:generate
```

### Step 3: Configure .env File

Edit `src/.env` and update these critical values:

```bash
APP_NAME="Med Rep Booking System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8085
APP_TIMEZONE=Asia/Kuwait

# Database (use Docker defaults)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=pharmacy_db
DB_USERNAME=pharmacy_user
DB_PASSWORD=pharmacy_pass

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"

# hCaptcha
HCAPTCHA_SITE_KEY=your-site-key
HCAPTCHA_SECRET_KEY=your-secret-key
```

### Step 4: Start Docker Containers

```bash
# Start all services
docker-compose up -d

# Wait for MySQL to be ready (30 seconds)
sleep 30
```

### Step 5: Run Database Migrations

```bash
# Run migrations
docker exec -it pharmacy_php php artisan migrate

# Seed database (optional - creates test data)
docker exec -it pharmacy_php php artisan db:seed
```

### Step 6: Create First Super Admin

```bash
# Access the container
docker exec -it pharmacy_php php artisan tinker

# In tinker, run:
$user = new App\Models\User();
$user->name = 'Super Admin';
$user->email = 'admin@example.com';
$user->password = Hash::make('YourSecurePassword123!');
$user->role = 'super_admin';
$user->company = 'System';
$user->civil_id = '000000000000';
$user->mobile_number = '12345678';
$user->is_active = true;
$user->email_verified_at = now();
$user->save();
exit
```

### Step 7: Access the Application

Open your browser and navigate to:
- **Application**: http://localhost:8085
- **phpMyAdmin**: http://localhost:8086 (if configured)

**Login with:**
- Email: admin@example.com
- Password: YourSecurePassword123!

---

## Production Deployment

### Step 1: Prepare Production Server

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

### Step 2: Clone Repository on Server

```bash
cd /var/www
git clone https://github.com/ghuffy11-lgtm/Med-Rep-Booking-System.git
cd Med-Rep-Booking-System
```

### Step 3: Configure Production Environment

```bash
# Copy and edit .env
cp .env.example src/.env
nano src/.env
```

**Production .env settings:**

```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Use strong database credentials
DB_PASSWORD=strong-random-password-here

# Configure production email
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-production-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### Step 4: SSL Certificate (Recommended)

```bash
# Install Certbot
sudo apt install certbot

# Generate SSL certificate
sudo certbot certonly --standalone -d your-domain.com

# Certificates will be in:
# /etc/letsencrypt/live/your-domain.com/
```

### Step 5: Update Docker Compose for Production

Edit `docker-compose.yml` ports section:

```yaml
nginx:
  ports:
    - "80:80"
    - "443:443"
```

### Step 6: Start Production Services

```bash
# Start containers
docker-compose up -d

# Wait for MySQL
sleep 30

# Generate app key
docker exec -it pharmacy_php php artisan key:generate

# Run migrations
docker exec -it pharmacy_php php artisan migrate

# Optimize for production
docker exec -it pharmacy_php php artisan config:cache
docker exec -it pharmacy_php php artisan route:cache
docker exec -it pharmacy_php php artisan view:cache
```

### Step 7: Create Super Admin (Production)

Follow Step 6 from Local Setup to create the first admin user.

---

## Docker Configuration

### Container Overview

| Container | Purpose | Port |
|-----------|---------|------|
| pharmacy_nginx | Web server | 8085 (HTTP), 8443 (HTTPS) |
| pharmacy_php | PHP application | Internal |
| pharmacy_mysql | Database | 3307 (external) |
| pharmacy_phpmyadmin | DB management | 8086 |

### Timezone Configuration

The system is configured for **Asia/Kuwait (GMT+3)**.

Docker containers sync time with:
- Environment variable: `TZ=Asia/Kuwait`
- Host timezone files: `/etc/localtime` and `/etc/timezone`

### Volume Mounts

```yaml
volumes:
  - ./src:/var/www/html          # Application code
  - ./ssl:/etc/nginx/ssl:ro       # SSL certificates
  - mysql_data:/var/lib/mysql     # Database persistence
```

---

## Environment Configuration

### Required Environment Variables

```bash
# Application
APP_NAME=               # Application name
APP_ENV=                # local/production
APP_KEY=                # Auto-generated
APP_DEBUG=              # true/false
APP_URL=                # Full application URL
APP_TIMEZONE=           # Asia/Kuwait

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=pharmacy_db
DB_USERNAME=pharmacy_user
DB_PASSWORD=            # Set strong password

# Mail
MAIL_MAILER=smtp
MAIL_HOST=              # SMTP server
MAIL_PORT=              # 587/465
MAIL_USERNAME=          # Email username
MAIL_PASSWORD=          # Email password
MAIL_ENCRYPTION=        # tls/ssl
MAIL_FROM_ADDRESS=      # From email
MAIL_FROM_NAME=         # From name

# hCaptcha
HCAPTCHA_SITE_KEY=      # From hCaptcha dashboard
HCAPTCHA_SECRET_KEY=    # From hCaptcha dashboard
```

---

## Database Setup

### Initial Migration

```bash
# Run all migrations
docker exec -it pharmacy_php php artisan migrate

# Check migration status
docker exec -it pharmacy_php php artisan migrate:status
```

### Database Backup

```bash
# Create backup
docker exec pharmacy_mysql mysqldump -u root -p pharmacy_db > backup.sql

# Restore backup
docker exec -i pharmacy_mysql mysql -u root -p pharmacy_db < backup.sql
```

### Reset Database (Development Only)

```bash
# Drop all tables and re-run migrations
docker exec -it pharmacy_php php artisan migrate:fresh

# With seeder
docker exec -it pharmacy_php php artisan migrate:fresh --seed
```

---

## Verification

### Check Container Status

```bash
docker ps
```

All containers should show "Up" status.

### Check Application Health

1. **Homepage**: Navigate to http://localhost:8085
2. **Login Page**: Should load without errors
3. **Registration**: Test registration form
4. **Email**: Check email delivery (SMTP configured)

### Check Database Connection

```bash
docker exec -it pharmacy_php php artisan tinker
>>> DB::connection()->getPdo();
>>> exit
```

Should return PDO connection object without errors.

### Check Logs

```bash
# Application logs
docker logs pharmacy_php

# Nginx logs
docker logs pharmacy_nginx

# MySQL logs
docker logs pharmacy_mysql
```

---

## Common Issues

### Port Already in Use

```bash
# Change ports in docker-compose.yml
ports:
  - "8090:80"  # Change 8085 to 8090
```

### MySQL Connection Refused

```bash
# Wait longer for MySQL to start
sleep 60

# Or check MySQL logs
docker logs pharmacy_mysql
```

### Permission Denied

```bash
# Fix Laravel storage permissions
docker exec -it pharmacy_php chmod -R 775 storage bootstrap/cache
docker exec -it pharmacy_php chown -R www-data:www-data storage bootstrap/cache
```

### Email Not Sending

1. Verify SMTP credentials in `.env`
2. Check firewall allows outbound SMTP
3. Test with tinker:
```bash
docker exec -it pharmacy_php php artisan tinker
>>> Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

---

## Updating the Application

```bash
# Pull latest changes
git pull origin main

# Stop containers
docker-compose down

# Rebuild (if needed)
docker-compose build

# Start containers
docker-compose up -d

# Run new migrations
docker exec -it pharmacy_php php artisan migrate

# Clear cache
docker exec -it pharmacy_php php artisan config:clear
docker exec -it pharmacy_php php artisan cache:clear
docker exec -it pharmacy_php php artisan view:clear
```

---

## Next Steps

After successful installation:

1. ✅ Configure system settings via Super Admin panel
2. ✅ Create pharmacy departments
3. ✅ Configure time slots
4. ✅ Create pharmacy admin accounts
5. ✅ Test the full booking workflow

For detailed usage, see the [User Manual](./USER_MANUAL.md) and [Admin Guide](./ADMIN_GUIDE.md).
