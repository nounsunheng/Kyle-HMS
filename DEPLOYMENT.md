# 🚀 Deployment Guide - Kyle-HMS

Complete guide for deploying Kyle Hospital Management System to production.

## Table of Contents

- [Pre-Deployment Checklist](#pre-deployment-checklist)
- [Environment Preparation](#environment-preparation)
- [Deployment Methods](#deployment-methods)
- [Server Configuration](#server-configuration)
- [SSL/HTTPS Setup](#sslhttps-setup)
- [Performance Optimization](#performance-optimization)
- [Monitoring and Maintenance](#monitoring-and-maintenance)
- [Backup Strategy](#backup-strategy)
- [Troubleshooting](#troubleshooting)

---

## Pre-Deployment Checklist

### Code Preparation

- [ ] All features tested locally
- [ ] Database migrations verified
- [ ] Seeders reviewed (disable in production)
- [ ] Environment variables configured
- [ ] Dependencies updated
- [ ] Code committed to Git
- [ ] Version tagged
- [ ] Documentation updated

### Security Review

- [ ] APP_DEBUG set to false
- [ ] APP_ENV set to production
- [ ] Strong APP_KEY generated
- [ ] Database credentials secured
- [ ] File permissions verified
- [ ] CSRF protection enabled
- [ ] XSS prevention active
- [ ] SQL injection prevention (Eloquent)
- [ ] Rate limiting configured

### Performance Checks

- [ ] Routes cached
- [ ] Config cached
- [ ] Views cached
- [ ] Composer optimized
- [ ] Assets compiled (production build)
- [ ] Images optimized
- [ ] Database indexed
- [ ] Query optimization done

---

## Environment Preparation

### Server Requirements

**Minimum Specifications:**
- **CPU**: 2 cores
- **RAM**: 4GB
- **Storage**: 20GB SSD
- **OS**: Ubuntu 22.04 LTS or newer

**Recommended Specifications:**
- **CPU**: 4 cores
- **RAM**: 8GB
- **Storage**: 50GB SSD
- **OS**: Ubuntu 22.04 LTS

### Software Requirements

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required software
sudo apt install -y \
    nginx \
    mysql-server \
    php8.2-fpm \
    php8.2-cli \
    php8.2-common \
    php8.2-mysql \
    php8.2-xml \
    php8.2-curl \
    php8.2-mbstring \
    php8.2-zip \
    php8.2-gd \
    php8.2-bcmath \
    php8.2-intl \
    git \
    composer \
    nodejs \
    npm \
    certbot \
    python3-certbot-nginx
```

---

## Deployment Methods

### Method 1: Manual Deployment (Recommended for Learning)

#### Step 1: Prepare Server

```bash
# Create application directory
sudo mkdir -p /var/www/kyle-hms
sudo chown -R $USER:www-data /var/www/kyle-hms
cd /var/www/kyle-hms
```

#### Step 2: Clone Repository

```bash
# Clone from GitHub
git clone https://github.com/nounsunheng/Kyle-HMS.git .

# Or upload files via FTP/SFTP
# Then extract: tar -xzf kyle-hms.tar.gz
```

#### Step 3: Install Dependencies

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install JavaScript dependencies
npm install

# Build production assets
npm run build
```

#### Step 4: Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Edit configuration
nano .env
```

**Production .env Configuration:**

```env
APP_NAME=Kyle-HMS
APP_ENV=production
APP_KEY=base64:GENERATE_NEW_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kyle_hms_production
DB_USERNAME=kyle_hms_user
DB_PASSWORD=STRONG_PASSWORD_HERE

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_DRIVER=redis

MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=EMAIL_PASSWORD_HERE
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

#### Step 5: Generate Application Key

```bash
php artisan key:generate
```

#### Step 6: Setup Database

```bash
# Create database and user
sudo mysql -u root -p

# In MySQL console:
CREATE DATABASE kyle_hms_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'kyle_hms_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON kyle_hms_production.* TO 'kyle_hms_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations
php artisan migrate --force

# OPTIONAL: Seed with initial data (admin account)
php artisan db:seed --class=AdminSeeder --force
```

#### Step 7: Set Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/kyle-hms

# Set permissions
sudo chmod -R 755 /var/www/kyle-hms
sudo chmod -R 775 /var/www/kyle-hms/storage
sudo chmod -R 775 /var/www/kyle-hms/bootstrap/cache

# Create storage link
php artisan storage:link
```

#### Step 8: Optimize Application

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize Composer autoloader
composer dump-autoload --optimize
```

---

### Method 2: Automated Deployment with Git Hooks

Create deployment script: `/var/www/deploy.sh`

```bash
#!/bin/bash

echo "Starting deployment..."

# Navigate to project
cd /var/www/kyle-hms

# Pull latest code
git pull origin main

# Install/update dependencies
composer install --no-dev --optimize-autoloader

# Build assets
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chown -R www-data:www-data /var/www/kyle-hms
sudo chmod -R 755 /var/www/kyle-hms
sudo chmod -R 775 /var/www/kyle-hms/storage

# Restart services
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx

echo "Deployment complete!"
```

Make executable:
```bash
chmod +x /var/www/deploy.sh
```

---

### Method 3: Docker Deployment

**docker-compose.production.yml:**

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile.production
    container_name: kyle-hms-app
    restart: always
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    volumes:
      - ./storage:/var/www/storage
      - ./public:/var/www/public
    networks:
      - kyle-hms-network

  nginx:
    image: nginx:alpine
    container_name: kyle-hms-nginx
    restart: always
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./public:/var/www/public
      - ./docker/nginx/production.conf:/etc/nginx/conf.d/default.conf
      - ./ssl:/etc/nginx/ssl
    networks:
      - kyle-hms-network
    depends_on:
      - app

  mysql:
    image: mysql:8.0
    container_name: kyle-hms-mysql
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - kyle-hms-network

  redis:
    image: redis:alpine
    container_name: kyle-hms-redis
    restart: always
    networks:
      - kyle-hms-network

networks:
  kyle-hms-network:
    driver: bridge

volumes:
  mysql_data:
```

Deploy:
```bash
docker-compose -f docker-compose.production.yml up -d
```

---

## Server Configuration

### Nginx Configuration

Create: `/etc/nginx/sites-available/kyle-hms`

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    
    # Redirect HTTP to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    root /var/www/kyle-hms/public;
    index index.php index.html;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Logging
    access_log /var/log/nginx/kyle-hms-access.log;
    error_log /var/log/nginx/kyle-hms-error.log;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json;

    # PHP-FPM Configuration
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { 
        access_log off; 
        log_not_found off; 
    }
    
    location = /robots.txt  { 
        access_log off; 
        log_not_found off; 
    }

    # Deny access to hidden files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # PHP Processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 365d;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/kyle-hms /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### PHP-FPM Configuration

Edit: `/etc/php/8.2/fpm/pool.d/www.conf`

```ini
[www]
user = www-data
group = www-data
listen = /var/run/php/php8.2-fpm.sock
listen.owner = www-data
listen.group = www-data

pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500

php_admin_value[upload_max_filesize] = 10M
php_admin_value[post_max_size] = 10M
php_admin_value[memory_limit] = 256M
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm
```

---

## SSL/HTTPS Setup

### Using Let's Encrypt (Free SSL)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Test auto-renewal
sudo certbot renew --dry-run

# Auto-renewal cron job (already set up by certbot)
# Check: sudo systemctl status certbot.timer
```

### SSL Renewal

Certbot automatically renews certificates. Verify:

```bash
# Check renewal service
sudo systemctl status certbot.timer

# Manual renewal test
sudo certbot renew --dry-run
```

---

## Performance Optimization

### 1. Enable OPcache

Edit: `/etc/php/8.2/fpm/php.ini`

```ini
[opcache]
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### 2. Redis Caching

```bash
# Install Redis
sudo apt install redis-server

# Start Redis
sudo systemctl start redis
sudo systemctl enable redis

# Configure Laravel to use Redis
# Already set in .env:
# CACHE_DRIVER=redis
# SESSION_DRIVER=redis
```

### 3. Database Optimization

```sql
# In MySQL console:

# Enable query cache
SET GLOBAL query_cache_type = ON;
SET GLOBAL query_cache_size = 67108864;

# Optimize tables regularly
OPTIMIZE TABLE appointments, medical_records, schedules;

# Analyze tables
ANALYZE TABLE patients, doctors;
```

### 4. CDN Integration (Optional)

For static assets, consider using a CDN like Cloudflare:

1. Sign up at cloudflare.com
2. Add your domain
3. Update nameservers
4. Enable caching rules
5. Update APP_URL if needed

---

## Monitoring and Maintenance

### Log Monitoring

```bash
# Laravel logs
tail -f /var/www/kyle-hms/storage/logs/laravel.log

# Nginx access logs
tail -f /var/log/nginx/kyle-hms-access.log

# Nginx error logs
tail -f /var/log/nginx/kyle-hms-error.log

# PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

### Health Checks

Create health check endpoint:

**routes/api.php:**
```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
    ]);
});
```

### Monitoring Tools

1. **Laravel Telescope** (Development only)
2. **New Relic** (Application monitoring)
3. **Sentry** (Error tracking)
4. **Uptime Robot** (Uptime monitoring)

### Cron Jobs

Set up Laravel scheduler:

```bash
# Edit crontab
crontab -e

# Add this line:
* * * * * cd /var/www/kyle-hms && php artisan schedule:run >> /dev/null 2>&1
```

### Queue Workers

Set up queue worker with Supervisor:

**Create:** `/etc/supervisor/conf.d/kyle-hms-worker.conf`

```ini
[program:kyle-hms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/kyle-hms/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/kyle-hms/storage/logs/worker.log
stopwaitsecs=3600
```

Start worker:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start kyle-hms-worker:*
```

---

## Backup Strategy

### Automated Database Backup

Create backup script: `/usr/local/bin/backup-kyle-hms.sh`

```bash
#!/bin/bash

# Configuration
BACKUP_DIR="/var/backups/kyle-hms"
DB_NAME="kyle_hms_production"
DB_USER="kyle_hms_user"
DB_PASS="YOUR_DB_PASSWORD"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Files backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/kyle-hms/storage

# Keep only last 7 days
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: $DATE"
```

Make executable and schedule:
```bash
chmod +x /usr/local/bin/backup-kyle-hms.sh

# Add to crontab (daily at 2 AM)
0 2 * * * /usr/local/bin/backup-kyle-hms.sh
```

### Backup to Cloud

```bash
# Install AWS CLI
sudo apt install awscli

# Configure AWS credentials
aws configure

# Upload to S3
aws s3 sync /var/backups/kyle-hms s3://your-bucket-name/kyle-hms-backups/
```

---

## Troubleshooting

### Common Issues

#### 1. 500 Internal Server Error

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check Nginx error logs
sudo tail -f /var/log/nginx/kyle-hms-error.log

# Check PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log

# Common fixes:
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Check permissions
sudo chown -R www-data:www-data /var/www/kyle-hms
sudo chmod -R 775 storage bootstrap/cache
```

#### 2. Database Connection Error

```bash
# Test database connection
mysql -u kyle_hms_user -p kyle_hms_production

# Check .env configuration
cat .env | grep DB_

# Verify MySQL is running
sudo systemctl status mysql

# Restart MySQL
sudo systemctl restart mysql
```

#### 3. File Upload Issues

```bash
# Check PHP upload limits
php -i | grep upload_max_filesize
php -i | grep post_max_size

# Check storage permissions
ls -la storage/app/public

# Recreate storage link
php artisan storage:link
```

#### 4. Slow Performance

```bash
# Enable caching
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check MySQL slow queries
sudo tail -f /var/log/mysql/slow-query.log

# Optimize database
mysqlcheck -u root -p --optimize --all-databases

# Check server resources
htop
df -h
free -h
```

---

## Security Hardening

### 1. Firewall Configuration

```bash
# Install UFW
sudo apt install ufw

# Configure firewall
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow http
sudo ufw allow https

# Enable firewall
sudo ufw enable
sudo ufw status
```

### 2. Fail2Ban Setup

```bash
# Install Fail2Ban
sudo apt install fail2ban

# Configure
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
sudo nano /etc/fail2ban/jail.local

# Start service
sudo systemctl start fail2ban
sudo systemctl enable fail2ban
```

### 3. Regular Updates

```bash
# System updates
sudo apt update
sudo apt upgrade -y

# PHP updates
composer update

# NPM updates
npm update

# Security patches
sudo unattended-upgrades
```

---

## Rollback Procedure

If deployment fails:

```bash
# 1. Revert to previous Git commit
git revert HEAD

# 2. Restore database backup
mysql -u kyle_hms_user -p kyle_hms_production < /var/backups/kyle-hms/db_backup.sql

# 3. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx
```

---

## Post-Deployment Checklist

- [ ] Application accessible via HTTPS
- [ ] SSL certificate valid
- [ ] Database connected
- [ ] File uploads working
- [ ] Email sending functional
- [ ] Cron jobs running
- [ ] Queue workers active
- [ ] Backups configured
- [ ] Monitoring setup
- [ ] Logs accessible
- [ ] Performance optimized
- [ ] Security hardened

---

**Deployment Guide Version**: 1.0  
**Last Updated**: February 7, 2026  
**For**: Kyle-HMS v1.0

For deployment support: nounsunheng290503@gmail.com
