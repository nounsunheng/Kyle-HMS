# 📦 Installation Guide - Kyle-HMS

This comprehensive guide will walk you through installing Kyle-HMS on various platforms.

## Table of Contents

- [Windows Installation (XAMPP)](#windows-installation-xampp)
- [macOS Installation](#macos-installation)
- [Linux Installation (Ubuntu)](#linux-installation-ubuntu)
- [Docker Installation](#docker-installation)
- [Post-Installation Setup](#post-installation-setup)
- [Troubleshooting](#troubleshooting)

---

## Windows Installation (XAMPP)

### Prerequisites

1. **Download and Install XAMPP**
   - Visit: https://www.apachefriends.org/
   - Download XAMPP 8.2.12 or higher
   - Install to `C:\xampp`

2. **Download and Install Composer**
   - Visit: https://getcomposer.org/download/
   - Run the Windows installer
   - Verify: `composer --version`

3. **Download and Install Node.js**
   - Visit: https://nodejs.org/
   - Download LTS version (18.x or higher)
   - Verify: `node --version` and `npm --version`

4. **Download and Install Git**
   - Visit: https://git-scm.com/download/win
   - Download and install
   - Verify: `git --version`

### Step-by-Step Installation

#### 1. Start XAMPP Services

```bash
# Open XAMPP Control Panel
# Start Apache
# Start MySQL
```

#### 2. Clone Repository

```bash
# Open Command Prompt or Git Bash
cd C:\xampp\htdocs

# Clone the repository
git clone https://github.com/nounsunheng/Kyle-HMS.git

# Navigate to project
cd Kyle-HMS
```

#### 3. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

#### 4. Configure Environment

```bash
# Copy environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

#### 5. Configure Database

Open `phpMyAdmin` (http://localhost/phpmyadmin):

```sql
CREATE DATABASE kyle_hms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kyle_hms
DB_USERNAME=root
DB_PASSWORD=
```

#### 6. Run Migrations and Seeders

```bash
# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

#### 7. Create Storage Link

```bash
php artisan storage:link
```

#### 8. Set Permissions

Right-click on these folders and ensure full permissions:
- `storage/`
- `bootstrap/cache/`

#### 9. Build Assets

```bash
# Development
npm run dev

# Or for production
npm run build
```

#### 10. Start Application

```bash
# Option 1: Using Laravel built-in server
php artisan serve

# Access at: http://localhost:8000
```

Or use XAMPP:

```
# Access at: http://localhost/Kyle-HMS/public
```

### Setting Up Virtual Host (Recommended)

#### 1. Configure httpd-vhosts.conf

Open `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

Add:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/Kyle-HMS/public"
    ServerName kyle-hms.local
    
    <Directory "C:/xampp/htdocs/Kyle-HMS/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/kyle-hms-error.log"
    CustomLog "logs/kyle-hms-access.log" common
</VirtualHost>
```

#### 2. Update Windows Hosts File

Open `C:\Windows\System32\drivers\etc\hosts` as Administrator

Add:

```
127.0.0.1 kyle-hms.local
```

#### 3. Restart Apache

Restart Apache in XAMPP Control Panel

#### 4. Update .env

```env
APP_URL=http://kyle-hms.local
```

#### 5. Access Application

Visit: http://kyle-hms.local

---

## macOS Installation

### Prerequisites

1. **Install Homebrew**

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

2. **Install PHP, MySQL, and Composer**

```bash
# Install PHP 8.2
brew install php@8.2

# Install MySQL
brew install mysql

# Install Composer
brew install composer

# Install Node.js
brew install node
```

3. **Install Git**

```bash
brew install git
```

### Step-by-Step Installation

#### 1. Start MySQL

```bash
brew services start mysql

# Secure installation (optional)
mysql_secure_installation
```

#### 2. Clone Repository

```bash
# Navigate to desired directory
cd ~/Sites

# Clone repository
git clone https://github.com/nounsunheng/Kyle-HMS.git
cd Kyle-HMS
```

#### 3. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

#### 4. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 5. Create Database

```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE kyle_hms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

Update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kyle_hms
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### 6. Run Migrations

```bash
php artisan migrate
php artisan db:seed
```

#### 7. Create Storage Link

```bash
php artisan storage:link
```

#### 8. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
```

#### 9. Build Assets

```bash
npm run dev
```

#### 10. Start Server

```bash
php artisan serve
```

Visit: http://localhost:8000

### Using Valet (Recommended for macOS)

```bash
# Install Valet
composer global require laravel/valet

# Install Valet
valet install

# Navigate to project directory
cd ~/Sites/Kyle-HMS

# Link project
valet link kyle-hms

# Secure with HTTPS (optional)
valet secure kyle-hms
```

Access: http://kyle-hms.test or https://kyle-hms.test

---

## Linux Installation (Ubuntu)

### Prerequisites

```bash
# Update package list
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2 and extensions
sudo apt install -y php8.2 php8.2-cli php8.2-common php8.2-mysql \
    php8.2-xml php8.2-curl php8.2-mbstring php8.2-zip php8.2-gd \
    php8.2-bcmath php8.2-intl

# Install MySQL
sudo apt install -y mysql-server

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Git
sudo apt install -y git

# Install Nginx (optional, for production)
sudo apt install -y nginx
```

### Step-by-Step Installation

#### 1. Secure MySQL

```bash
sudo mysql_secure_installation
```

#### 2. Create Database

```bash
sudo mysql -u root -p

CREATE DATABASE kyle_hms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'kylehms'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON kyle_hms.* TO 'kylehms'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### 3. Clone Repository

```bash
cd /var/www
sudo git clone https://github.com/nounsunheng/Kyle-HMS.git
cd Kyle-HMS
```

#### 4. Set Ownership

```bash
sudo chown -R www-data:www-data /var/www/Kyle-HMS
sudo chmod -R 775 /var/www/Kyle-HMS/storage
sudo chmod -R 775 /var/www/Kyle-HMS/bootstrap/cache
```

#### 5. Install Dependencies

```bash
composer install
npm install
```

#### 6. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kyle_hms
DB_USERNAME=kylehms
DB_PASSWORD=strong_password
```

#### 7. Run Migrations

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

#### 8. Build Assets

```bash
npm run build
```

#### 9. Configure Nginx (Production)

Create `/etc/nginx/sites-available/kyle-hms`:

```nginx
server {
    listen 80;
    server_name kyle-hms.local;
    root /var/www/Kyle-HMS/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/kyle-hms /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### 10. Start PHP-FPM

```bash
sudo systemctl start php8.2-fpm
sudo systemctl enable php8.2-fpm
```

---

## Docker Installation

### Prerequisites

- Docker Desktop installed
- Docker Compose installed

### docker-compose.yml

Create `docker-compose.yml` in project root:

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: kyle-hms-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    networks:
      - kyle-hms-network

  nginx:
    image: nginx:alpine
    container_name: kyle-hms-nginx
    restart: unless-stopped
    ports:
      - "8000:80"
    volumes:
      - ./:/var/www
      - ./docker/nginx/conf.d:/etc/nginx/conf.d
    networks:
      - kyle-hms-network

  mysql:
    image: mysql:8.0
    container_name: kyle-hms-mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: kyle_hms
      MYSQL_ROOT_PASSWORD: root
      MYSQL_USER: kylehms
      MYSQL_PASSWORD: password
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - kyle-hms-network
    ports:
      - "3307:3306"

  node:
    image: node:18
    container_name: kyle-hms-node
    working_dir: /var/www
    volumes:
      - ./:/var/www
    command: npm run dev
    networks:
      - kyle-hms-network

networks:
  kyle-hms-network:
    driver: bridge

volumes:
  mysql_data:
    driver: local
```

### Dockerfile

Create `Dockerfile`:

```dockerfile
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www

EXPOSE 9000
CMD ["php-fpm"]
```

### Installation Steps

```bash
# Build and start containers
docker-compose up -d

# Install dependencies
docker-compose exec app composer install
docker-compose exec app npm install

# Configure environment
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate

# Update .env for Docker
# DB_HOST=mysql
# DB_DATABASE=kyle_hms
# DB_USERNAME=kylehms
# DB_PASSWORD=password

# Run migrations
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan storage:link

# Build assets
docker-compose exec node npm run build
```

Access: http://localhost:8000

---

## Post-Installation Setup

### 1. Default Credentials

After seeding, use these credentials:

```
Admin:
Email: admin@kylehms.com
Password: password

Doctor:
Email: doctor1@kylehms.com
Password: password

Patient:
Email: patient1@kylehms.com
Password: password
```

### 2. Configure Mail (Optional)

For email notifications, update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@kylehms.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Queue Configuration

For background jobs:

```bash
# Run queue worker
php artisan queue:work

# Or use supervisor in production
```

### 4. Optimize for Performance

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

---

## Troubleshooting

### Common Issues

#### 1. Port Already in Use

```bash
# Find process using port 8000
# Windows
netstat -ano | findstr :8000

# Kill process
taskkill /PID <process_id> /F

# macOS/Linux
lsof -ti:8000 | xargs kill -9
```

#### 2. Composer Memory Issues

```bash
# Increase memory limit
php -d memory_limit=-1 /usr/local/bin/composer install
```

#### 3. NPM Permission Errors (Linux/macOS)

```bash
# Fix NPM permissions
sudo chown -R $USER:$(id -gn $USER) ~/.npm
sudo chown -R $USER:$(id -gn $USER) ~/.config
```

#### 4. MySQL Connection Refused

```bash
# Check MySQL status
# Windows (XAMPP)
# Open XAMPP Control Panel and start MySQL

# macOS
brew services restart mysql

# Linux
sudo systemctl restart mysql
```

#### 5. Storage Permission Denied

```bash
# Windows
# Right-click → Properties → Security → Edit → Add full control

# macOS/Linux
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Getting Help

If you encounter issues:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check Apache/Nginx error logs
3. Enable debug mode: `APP_DEBUG=true` in `.env`
4. Search GitHub Issues
5. Contact: nounsunheng290503@gmail.com

---

## Next Steps

After successful installation:

1. ✅ Login with default credentials
2. ✅ Explore the three portals (Admin, Doctor, Patient)
3. ✅ Create test data
4. ✅ Read User Manual
5. ✅ Customize for your needs

---

**Installation complete! 🎉**

Visit the [User Manual](USER_MANUAL.md) to learn how to use Kyle-HMS.
