# Configuration Reference

## Table of Contents
1. [Environment Variables](#environment-variables)
2. [Application Configuration](#application-configuration)
3. [Database Configuration](#database-configuration)
4. [Email Configuration](#email-configuration)
5. [Session Configuration](#session-configuration)
6. [Cache Configuration](#cache-configuration)
7. [Security Configuration](#security-configuration)
8. [Docker Configuration](#docker-configuration)
9. [Nginx Configuration](#nginx-configuration)
10. [PHP Configuration](#php-configuration)

---

## Environment Variables

All environment-specific configuration is stored in the `.env` file located in the `src/` directory.

### Creating .env File

```bash
cd src
cp .env.example .env
```

Edit `.env` with your specific settings.

---

### Application Settings

```env
# Application Name
APP_NAME="Pharmacy Booking System"

# Application Environment
# Options: local, development, staging, production
APP_ENV=local

# Application Key (auto-generated)
# Run: php artisan key:generate
APP_KEY=base64:...

# Debug Mode (NEVER true in production!)
# Options: true, false
APP_DEBUG=true

# Application URL
APP_URL=http://localhost:8080

# Application Timezone
# Options: Any valid PHP timezone
# Examples: Asia/Kuwait, UTC, America/New_York
APP_TIMEZONE=Asia/Kuwait
```

**Important Notes**:
- `APP_KEY` must be generated: `php artisan key:generate`
- `APP_DEBUG=false` in production (security risk if true)
- `APP_ENV=production` in production
- `APP_TIMEZONE` affects all timestamp displays and calculations

---

### Database Settings

```env
# Database Connection
# Options: mysql, pgsql, sqlite, sqlsrv
DB_CONNECTION=mysql

# Database Host
# Docker: mysql (container name)
# Local: 127.0.0.1 or localhost
DB_HOST=mysql

# Database Port
# MySQL default: 3306
# PostgreSQL default: 5432
DB_PORT=3306

# Database Name
DB_DATABASE=pharmacy_db

# Database Username
DB_USERNAME=pharmacy_user

# Database Password
DB_PASSWORD=pharmacy_pass
```

**For Docker**:
- `DB_HOST=mysql` (container name)
- `DB_PORT=3306` (internal port)

**For Local/Production**:
- `DB_HOST=127.0.0.1` or actual server IP
- `DB_PORT=3306` (or custom port)

---

### Email/SMTP Settings

```env
# Mail Driver
# Options: smtp, sendmail, mailgun, ses, postmark, log
MAIL_MAILER=smtp

# SMTP Host
MAIL_HOST=smtp.gmail.com

# SMTP Port
# 587: TLS
# 465: SSL
# 25: No encryption (usually blocked)
MAIL_PORT=587

# SMTP Username
MAIL_USERNAME=your-email@gmail.com

# SMTP Password
# For Gmail: Use App Password
MAIL_PASSWORD=your-app-password

# Encryption
# Options: tls, ssl, null
MAIL_ENCRYPTION=tls

# From Address
MAIL_FROM_ADDRESS="noreply@pharmacy.local"

# From Name
MAIL_FROM_NAME="${APP_NAME}"
```

#### Gmail Configuration

1. **Enable 2-Factor Authentication** on your Google account
2. **Generate App Password**:
   - Go to: https://myaccount.google.com/apppasswords
   - Select "Mail" and your device
   - Copy the 16-character password
3. **Use in .env**:
   ```env
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=your-16-char-app-password
   MAIL_ENCRYPTION=tls
   ```

#### SendGrid Configuration

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

#### Development (MailHog)

```env
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

Access MailHog UI: http://localhost:8025

---

### Session Settings

```env
# Session Driver
# Options: file, cookie, database, memcached, redis
SESSION_DRIVER=file

# Session Lifetime (minutes)
# Default: 120 (2 hours)
SESSION_LIFETIME=120

# Encrypt Session
# Options: true, false
SESSION_ENCRYPT=false

# Session Path
SESSION_PATH=/

# Session Domain
SESSION_DOMAIN=null

# Secure Cookies (HTTPS only)
# Options: true, false
# Set to true in production with HTTPS
SESSION_SECURE_COOKIE=false

# HTTP Only Cookies
# Options: true, false
# Always true for security
SESSION_HTTP_ONLY=true

# SameSite Cookie Setting
# Options: lax, strict, none
SESSION_SAME_SITE=lax
```

**Recommendations**:
- **Development**: `SESSION_DRIVER=file`
- **Production (small)**: `SESSION_DRIVER=database`
- **Production (large)**: `SESSION_DRIVER=redis`

---

### Cache Settings

```env
# Cache Driver
# Options: file, database, memcached, redis, array
CACHE_DRIVER=file

# Cache Prefix
CACHE_PREFIX=pharmacy_cache
```

**Recommendations**:
- **Development**: `CACHE_DRIVER=file`
- **Production (small)**: `CACHE_DRIVER=file` or `database`
- **Production (large)**: `CACHE_DRIVER=redis`

---

### Queue Settings

```env
# Queue Connection
# Options: sync, database, redis, sqs, beanstalkd
QUEUE_CONNECTION=sync
```

**Recommendations**:
- **Development**: `QUEUE_CONNECTION=sync` (no queue, runs immediately)
- **Production**: `QUEUE_CONNECTION=database` or `redis`

---

### Logging Settings

```env
# Log Channel
# Options: stack, single, daily, slack, syslog, errorlog
LOG_CHANNEL=stack

# Log Level
# Options: debug, info, notice, warning, error, critical, alert, emergency
LOG_LEVEL=debug

# Log Deprecations Channel
LOG_DEPRECATIONS_CHANNEL=null
```

**Production Recommendations**:
```env
LOG_CHANNEL=daily
LOG_LEVEL=warning
```

---

### Broadcasting Settings

```env
# Broadcast Driver
# Options: pusher, redis, log, null
BROADCAST_DRIVER=log
```

**For real-time features** (future enhancement):
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=mt1
```

---

### Filesystem Settings

```env
# Filesystem Driver
# Options: local, public, s3
FILESYSTEM_DISK=local

# AWS S3 Configuration (if using S3)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false
```

---

### Additional Settings

```env
# Pagination
# Default items per page
PAGINATION_PER_PAGE=20

# hCaptcha Keys
HCAPTCHA_SITE_KEY=your-site-key
HCAPTCHA_SECRET_KEY=your-secret-key

# Asset URL (CDN)
ASSET_URL=null
```

---

## Application Configuration

Configuration files are located in `src/config/` directory.

### config/app.php

```php
<?php

return [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => env('APP_TIMEZONE', 'Asia/Kuwait'),
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',

    'maintenance' => [
        'driver' => 'file',
    ],

    'providers' => [
        // Laravel service providers
        // ...
    ],

    'aliases' => [
        // Class aliases
        // ...
    ],
];
```

**Key Settings**:
- `timezone`: Default timezone for all timestamps
- `locale`: Application language
- `cipher`: Encryption algorithm (do not change)

---

### config/auth.php

```php
<?php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60, // minutes
            'throttle' => 60, // seconds
        ],
    ],

    'password_timeout' => 10800, // 3 hours
];
```

**Key Settings**:
- `expire`: Password reset token expiration (60 minutes)
- `throttle`: Throttle between reset requests (60 seconds)
- `password_timeout`: Password re-confirmation timeout (3 hours)

---

### config/database.php

```php
<?php

return [
    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
    ],

    'migrations' => 'migrations',
];
```

---

### config/mail.php

```php
<?php

return [
    'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],
];
```

---

### config/session.php

```php
<?php

return [
    'driver' => env('SESSION_DRIVER', 'file'),
    'lifetime' => env('SESSION_LIFETIME', 120),
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION'),
    'table' => 'sessions',
    'store' => env('SESSION_STORE'),
    'lottery' => [2, 100],
    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_session'
    ),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN'),
    'secure' => env('SESSION_SECURE_COOKIE'),
    'http_only' => true,
    'same_site' => 'lax',
];
```

---

## Database Configuration

### Connection Parameters

**Development**:
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=pharmacy_db
DB_USERNAME=pharmacy_user
DB_PASSWORD=pharmacy_pass
```

**Production**:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pharmacy_production_db
DB_USERNAME=pharmacy_prod_user
DB_PASSWORD=strong-secure-password-here
```

### Connection Pooling

For high-traffic environments, configure connection pooling in `config/database.php`:

```php
'mysql' => [
    // ... other settings
    'options' => [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_TIMEOUT => 30,
    ],
],
```

---

## Email Configuration

### Provider-Specific Settings

#### Gmail
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Pharmacy Booking"
```

#### SendGrid
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

#### Amazon SES
```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Pharmacy Booking"
```

#### Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.mailgun.org
MAILGUN_SECRET=your-mailgun-secret
MAILGUN_ENDPOINT=api.mailgun.net
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Pharmacy Booking"
```

---

## Security Configuration

### Rate Limiting

Configured in `src/routes/web.php`:

```php
// Login: 5 attempts per minute
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

// Registration: 3 attempts per hour
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:3,60');

// Password reset request: 3 per minute
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
    ->middleware('throttle:3,1');

// Password reset: 5 per minute
Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->middleware('throttle:5,1');
```

**Customize in** `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'api' => [
        'throttle:60,1', // 60 requests per minute
    ],
];
```

### Password Requirements

Configured in controllers using Laravel Password rules:

```php
use Illuminate\Validation\Rules\Password;

'password' => [
    'required',
    'confirmed',
    Password::min(8)          // Minimum 8 characters
        ->max(64)              // Maximum 64 characters
        ->mixedCase()          // Requires uppercase & lowercase
        ->numbers()            // Requires numbers
        ->symbols()            // Requires special characters
],
```

### CORS Configuration

Edit `config/cors.php`:

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

**For production**, restrict allowed origins:
```php
'allowed_origins' => ['https://yourdomain.com'],
```

---

## Docker Configuration

### docker-compose.yml

```yaml
version: '3.8'

services:
  nginx:
    build:
      context: ./docker/nginx
      dockerfile: Dockerfile
    container_name: pharmacy_nginx
    ports:
      - "8080:80"
    volumes:
      - ./src:/var/www/html
    depends_on:
      - php
    networks:
      - pharmacy_network
    restart: unless-stopped

  php:
    build:
      context: ./docker/php
      dockerfile: Dockerfile
    container_name: pharmacy_php
    volumes:
      - ./src:/var/www/html
      - /etc/localtime:/etc/localtime:ro
      - /etc/timezone:/etc/timezone:ro
    environment:
      DB_HOST: mysql
      DB_PORT: 3306
      DB_DATABASE: pharmacy_db
      DB_USERNAME: pharmacy_user
      DB_PASSWORD: pharmacy_pass
      TZ: Asia/Kuwait
    depends_on:
      - mysql
    networks:
      - pharmacy_network
    restart: unless-stopped

  mysql:
    image: mysql:8.0
    container_name: pharmacy_mysql
    ports:
      - "3307:3306"
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: pharmacy_db
      MYSQL_USER: pharmacy_user
      MYSQL_PASSWORD: pharmacy_pass
      TZ: Asia/Kuwait
    volumes:
      - mysql_data:/var/lib/mysql
      - /etc/localtime:/etc/localtime:ro
      - /etc/timezone:/etc/timezone:ro
    networks:
      - pharmacy_network
    command: --default-authentication-plugin=mysql_native_password
    restart: unless-stopped

networks:
  pharmacy_network:
    driver: bridge

volumes:
  mysql_data:
    driver: local
```

**Key Configuration**:
- `ports`: External:Internal port mapping
- `TZ`: Timezone environment variable
- `volumes`: Persistent data storage
- `restart`: Container restart policy

---

## Nginx Configuration

### docker/nginx/default.conf

```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;

    index index.php index.html;

    charset utf-8;

    # Increase max body size for file uploads
    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass php:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Key Settings**:
- `client_max_body_size`: Maximum upload size
- `fastcgi_pass`: PHP-FPM container and port
- `try_files`: Route all requests through index.php

---

## PHP Configuration

### docker/php/Dockerfile

```dockerfile
FROM php:8.2-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    libzip-dev \
    zip \
    unzip \
    git \
    mysql-client

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# PHP configuration
RUN echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memory-limit.ini
RUN echo "upload_max_filesize = 20M" > /usr/local/etc/php/conf.d/upload-max-filesize.ini
RUN echo "post_max_size = 20M" > /usr/local/etc/php/conf.d/post-max-size.ini

# OPcache configuration
RUN echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/opcache.ini

EXPOSE 9000

CMD ["php-fpm"]
```

**Key Settings**:
- `memory_limit`: PHP memory limit (512M)
- `upload_max_filesize`: Maximum upload file size (20M)
- `post_max_size`: Maximum POST data size (20M)
- OPcache: Improves PHP performance

---

## Production Checklist

### .env Settings
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` set to production URL
- [ ] Strong database password
- [ ] SMTP configured with production credentials
- [ ] `SESSION_SECURE_COOKIE=true` (if HTTPS)

### Laravel Optimizations
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Run `php artisan event:cache`

### Server Configuration
- [ ] PHP OPcache enabled
- [ ] File permissions set correctly
- [ ] SSL certificate configured
- [ ] Firewall configured
- [ ] Backups automated
- [ ] Monitoring configured

---

**Version**: 1.0
**Last Updated**: January 2026
**Document ID**: CONFIG-001

For configuration support: tech-support@example.com
