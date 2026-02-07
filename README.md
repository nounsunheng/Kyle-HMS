# 🏥 Kyle-HMS: Hospital Management System

<div align="center">

![Kyle-HMS Logo](https://img.shields.io/badge/Kyle--HMS-Hospital%20Management%20System-0066CC?style=for-the-badge&logo=hospital&logoColor=white)

**A comprehensive, production-ready Hospital Management System built with Laravel 12 and modern web technologies**

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3-4E56A6?style=flat-square&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-3-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![DaisyUI](https://img.shields.io/badge/DaisyUI-5-570DF8?style=flat-square&logo=daisyui&logoColor=white)](https://daisyui.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)

[Features](#-features) • [Installation](#-installation) • [Usage](#-usage) • [Documentation](#-documentation) • [Contributing](#-contributing)

</div>

---

## 📑 Table of Contents

- [About](#-about)
- [Features](#-features)
- [System Architecture](#-system-architecture)
- [Technology Stack](#-technology-stack)
- [Prerequisites](#-prerequisites)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Database Setup](#-database-setup)
- [Usage](#-usage)
- [User Roles & Permissions](#-user-roles--permissions)
- [Screenshots](#-screenshots)
- [Project Structure](#-project-structure)
- [Security](#-security)
- [Testing](#-testing)
- [API Documentation](#-api-documentation)
- [Deployment](#-deployment)
- [Troubleshooting](#-troubleshooting)
- [Contributing](#-contributing)
- [License](#-license)
- [Author](#-author)
- [Acknowledgments](#-acknowledgments)

---

## 🎯 About

**Kyle-HMS** (Kyle Hospital Management System) is a comprehensive, full-stack web application designed to streamline hospital operations and improve healthcare service delivery. Built with modern web technologies and following industry best practices, Kyle-HMS provides an intuitive interface for patients, doctors, and administrators to manage appointments, medical records, schedules, and more.

### 🎓 Academic Context

This project was developed as a **Software Development Project** demonstrating:
- Full-stack web application development
- Modern MVC architecture implementation
- Database design and management
- User authentication and authorization
- Role-based access control
- RESTful API design principles
- Responsive UI/UX design
- Security best practices

### 🌟 Project Highlights

- **Multi-portal System**: Separate interfaces for Patients, Doctors, and Administrators
- **Real-time Updates**: Powered by Laravel Livewire for reactive components
- **Secure Authentication**: Built with Laravel Breeze and Spatie Permissions
- **Responsive Design**: Mobile-first approach using Tailwind CSS and DaisyUI
- **Professional UI**: Custom design system with consistent color palette and typography
- **Production-ready**: Optimized for deployment with comprehensive error handling

---

## ✨ Features

### 👨‍⚕️ Patient Portal

- **User Registration & Authentication**
  - Secure registration with email verification
  - Profile management with avatar upload
  - Password reset functionality

- **Appointment Management**
  - Browse available doctors by specialty
  - View doctor profiles and schedules
  - Book appointments with real-time availability checking
  - View appointment history and status
  - Cancel appointments (with restrictions)
  - Receive appointment confirmations

- **Medical Records**
  - View personal medical history
  - Access prescriptions and diagnoses
  - Download medical documents
  - Track blood type and allergies

- **Doctor Discovery**
  - Search doctors by name or specialty
  - View doctor qualifications and experience
  - Read doctor biographies
  - Check availability and schedules

### 👨‍⚕️ Doctor Portal

- **Dashboard & Analytics**
  - View daily/weekly appointment statistics
  - Track patient count and upcoming appointments
  - Quick access to recent patients
  - Performance metrics visualization

- **Schedule Management**
  - Create flexible work schedules
  - Set appointment durations (15/30/45/60 minutes)
  - Define time slots and maximum appointments
  - Edit or cancel schedules
  - View calendar overview

- **Appointment Handling**
  - View all appointments (pending, confirmed, completed)
  - Confirm or decline appointment requests
  - Mark appointments as completed or no-show
  - Add notes to appointments
  - Reschedule when needed

- **Patient Management**
  - View patient list and profiles
  - Access patient medical history
  - Review previous consultations
  - Track patient visit frequency

- **Medical Records**
  - Create detailed medical records
  - Add diagnoses and treatments
  - Prescribe medications
  - Upload medical documents (lab results, x-rays, etc.)
  - Edit and update existing records

### 👨‍💼 Admin Portal

- **Comprehensive Dashboard**
  - System-wide statistics and metrics
  - Real-time appointment tracking
  - User growth analytics
  - Revenue and performance indicators
  - Interactive charts and graphs

- **User Management**
  - Manage all users (patients, doctors, admins)
  - Assign and modify user roles
  - Enable/disable user accounts
  - View user activity logs

- **Doctor Management**
  - Add new doctors to the system
  - Edit doctor profiles and credentials
  - Manage doctor specializations
  - Upload doctor photos
  - View doctor performance metrics

- **Patient Management**
  - View all registered patients
  - Edit patient information
  - Access patient medical histories
  - Manage patient appointments

- **Specialty Management**
  - Create and manage medical specialties
  - Assign doctors to specialties
  - Track specialty utilization

- **Appointment Oversight**
  - Monitor all system appointments
  - Cancel appointments when necessary
  - Generate appointment reports
  - Track appointment statistics

- **Reports & Analytics**
  - Generate comprehensive system reports
  - Export data (CSV, Excel, PDF)
  - Custom date range filtering
  - Summary reports for management

### 🔐 Security Features

- **Authentication & Authorization**
  - Laravel Breeze authentication scaffolding
  - Spatie Laravel Permission for role-based access
  - Custom middleware for route protection
  - Session management and security

- **Data Protection**
  - CSRF protection on all forms
  - XSS prevention
  - SQL injection protection via Eloquent ORM
  - Secure password hashing with bcrypt
  - File upload validation and sanitization

- **Access Control**
  - Role-based permissions (Admin, Doctor, Patient)
  - Resource-level authorization
  - Protected routes and API endpoints
  - Audit trails for sensitive operations

---

## 🏗️ System Architecture

Kyle-HMS follows the **MVC (Model-View-Controller)** architectural pattern with clear separation of concerns:

```
┌─────────────────────────────────────────────────┐
│                   PRESENTATION                   │
│              (Blade Templates + Livewire)        │
├─────────────────────────────────────────────────┤
│                   CONTROLLERS                    │
│        (Admin, Doctor, Patient, Auth)            │
├─────────────────────────────────────────────────┤
│                     MODELS                       │
│   (User, Patient, Doctor, Appointment, etc.)     │
├─────────────────────────────────────────────────┤
│                    DATABASE                      │
│              (MySQL with Migrations)             │
└─────────────────────────────────────────────────┘
```

### Database Schema

The system uses a relational database with the following core entities:

- **users**: Base authentication table
- **patients**: Patient-specific information
- **doctors**: Doctor profiles and credentials
- **specialties**: Medical specializations
- **schedules**: Doctor availability management
- **appointments**: Booking records
- **medical_records**: Patient medical history
- **admins**: Administrator profiles
- **roles & permissions**: RBAC tables (Spatie)

### Key Design Patterns

- **Repository Pattern**: For data access abstraction
- **Service Pattern**: For business logic encapsulation
- **Factory Pattern**: For model factories and seeders
- **Observer Pattern**: For model events
- **Middleware Pattern**: For request filtering

---

## 🛠️ Technology Stack

### Backend

| Technology | Version | Purpose |
|------------|---------|---------|
| **Laravel** | 12.x | PHP framework for backend logic |
| **PHP** | 8.2+ | Server-side programming language |
| **MySQL** | 8.0+ | Relational database management |
| **Livewire** | 3.x | Reactive components without JavaScript |
| **Spatie Permission** | 6.x | Role and permission management |
| **Laravel Breeze** | 2.x | Authentication scaffolding |
| **Intervention Image** | 1.x | Image processing and manipulation |

### Frontend

| Technology | Version | Purpose |
|------------|---------|---------|
| **Tailwind CSS** | 3.x | Utility-first CSS framework |
| **DaisyUI** | 5.x | Tailwind CSS component library |
| **Alpine.js** | 3.x | Lightweight JavaScript framework |
| **Chart.js** | 4.x | Data visualization and charts |
| **Vite** | 7.x | Fast frontend build tool |

### Development Tools

| Tool | Purpose |
|------|---------|
| **Composer** | PHP dependency management |
| **NPM** | JavaScript package management |
| **Laravel Pint** | PHP code style fixer |
| **Laravel Debugbar** | Development debugging |
| **Laravel IDE Helper** | IDE autocompletion |
| **PHPUnit** | PHP testing framework |

---

## 📋 Prerequisites

Before installing Kyle-HMS, ensure you have the following software installed on your system:

### Required Software

- **PHP** >= 8.2
  - Extensions: `pdo_mysql`, `mbstring`, `xml`, `bcmath`, `gd`, `fileinfo`
- **Composer** >= 2.8
- **Node.js** >= 18.x
- **NPM** >= 9.x or **Yarn** >= 1.22
- **MySQL** >= 8.0 or **MariaDB** >= 10.6
- **Git** (for version control)

### Recommended Tools

- **XAMPP** or **Laragon** (for local development on Windows)
- **VS Code** or **PhpStorm** (code editor)
- **Postman** or **Insomnia** (API testing)

### System Requirements

- **Memory**: Minimum 2GB RAM (4GB recommended)
- **Disk Space**: Minimum 500MB free space
- **Operating System**: Windows 10/11, macOS, or Linux

---

## 🚀 Installation

Follow these steps to set up Kyle-HMS on your local machine:

### Step 1: Clone the Repository

```bash
# Clone via HTTPS
git clone https://github.com/nounsunheng/Kyle-HMS.git

# Or clone via SSH
git clone git@github.com:nounsunheng/Kyle-HMS.git

# Navigate to project directory
cd Kyle-HMS
```

### Step 2: Install PHP Dependencies

```bash
# Install Composer dependencies
composer install

# Note: Use --no-dev for production
# composer install --no-dev --optimize-autoloader
```

### Step 3: Install JavaScript Dependencies

```bash
# Install NPM packages
npm install

# Or using Yarn
# yarn install
```

### Step 4: Environment Configuration

```bash
# Copy .env.example to .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 5: Configure Database

Edit the `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kyle_hms
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

### Step 6: Create Database

```bash
# Using MySQL command line
mysql -u root -p
CREATE DATABASE kyle_hms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Or use phpMyAdmin to create the database
```

### Step 7: Run Migrations

```bash
# Run all migrations to create database tables
php artisan migrate

# If you encounter issues, you can refresh migrations
# php artisan migrate:fresh
```

### Step 8: Seed the Database

```bash
# Seed with sample data (includes admin, doctors, patients)
php artisan db:seed

# Or run specific seeders
# php artisan db:seed --class=RolePermissionSeeder
# php artisan db:seed --class=AdminSeeder
# php artisan db:seed --class=DoctorSeeder
```

### Step 9: Create Storage Link

```bash
# Create symbolic link for file storage
php artisan storage:link
```

### Step 10: Build Frontend Assets

```bash
# Development build
npm run dev

# Or production build
npm run build
```

### Step 11: Start Development Server

```bash
# Start Laravel development server
php artisan serve

# Application will be available at http://localhost:8000
```

### Step 12: Access the Application

Open your browser and navigate to:
- **URL**: http://localhost:8000
- **Admin Login**: admin@kylehms.com / password
- **Doctor Login**: doctor1@kylehms.com / password
- **Patient Login**: patient1@kylehms.com / password

---

## ⚙️ Configuration

### Application Settings

Edit `.env` file for application-wide settings:

```env
APP_NAME=Kyle-HMS
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
```

### Virtual Host Setup (Optional)

For a better development experience, set up a virtual host:

#### Windows (XAMPP)

1. Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/Kyle-HMS/public"
    ServerName kyle-hms.local
    
    <Directory "C:/xampp/htdocs/Kyle-HMS/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

2. Edit `C:\Windows\System32\drivers\etc\hosts` (as Administrator):

```
127.0.0.1 kyle-hms.local
```

3. Restart Apache and access: http://kyle-hms.local

### Mail Configuration

For email notifications (optional):

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@kylehms.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 🗄️ Database Setup

### Database Schema Overview

Kyle-HMS uses the following database structure:

```sql
-- Core Tables
users              # Base authentication
patients           # Patient profiles
doctors            # Doctor profiles
specialties        # Medical specializations
schedules          # Doctor availability
appointments       # Booking records
medical_records    # Patient medical history
admins             # Administrator profiles

-- Permission Tables (Spatie)
roles              # User roles
permissions        # System permissions
role_has_permissions
model_has_roles
model_has_permissions
```

### Sample Data

After seeding, the database includes:

**Default Users:**
- 1 Admin account
- 10 Doctors (various specialties)
- 25 Patients
- 3 Roles (admin, doctor, patient)

**Specialties:**
- Cardiology
- Neurology
- Pediatrics
- Orthopedics
- Dermatology
- And more...

### Migrations

Run specific migrations:

```bash
# Run a specific migration
php artisan migrate --path=/database/migrations/2026_01_07_181350_create_specialties_table.php

# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Refresh database (drop all tables and re-run)
php artisan migrate:fresh --seed
```

---

## 📖 Usage

### Patient Workflow

1. **Register Account**
   - Navigate to registration page
   - Fill in personal information
   - Verify email address

2. **Browse Doctors**
   - View all available doctors
   - Filter by specialty
   - Check doctor profiles and schedules

3. **Book Appointment**
   - Select a doctor
   - Choose available time slot
   - Provide reason for visit
   - Submit booking request

4. **Manage Appointments**
   - View appointment status
   - Cancel appointments (if needed)
   - Receive confirmations

5. **View Medical Records**
   - Access personal medical history
   - View diagnoses and treatments
   - Download prescriptions

### Doctor Workflow

1. **Login & Dashboard**
   - Access doctor portal
   - View daily statistics
   - Check upcoming appointments

2. **Manage Schedule**
   - Create new schedules
   - Set available time slots
   - Define appointment duration
   - Cancel schedules when needed

3. **Handle Appointments**
   - Review pending requests
   - Confirm or decline appointments
   - Complete consultations
   - Mark no-shows

4. **Create Medical Records**
   - Document patient visits
   - Add diagnoses and treatments
   - Write prescriptions
   - Upload medical files

5. **View Patients**
   - Access patient list
   - Review medical histories
   - Track patient progress

### Admin Workflow

1. **System Oversight**
   - Monitor all system activity
   - View comprehensive statistics
   - Generate reports

2. **Manage Doctors**
   - Add new doctors
   - Edit doctor profiles
   - Assign specialties
   - Track performance

3. **Manage Patients**
   - View all patients
   - Edit patient information
   - Access medical records

4. **Manage Specialties**
   - Create new specialties
   - Edit existing specialties
   - Track specialty usage

5. **Generate Reports**
   - Export system data
   - Create summary reports
   - Analyze trends

---

## 👥 User Roles & Permissions

### Role Hierarchy

```
┌─────────────┐
│    Admin    │  ← Full system access
├─────────────┤
│   Doctor    │  ← Medical operations
├─────────────┤
│   Patient   │  ← Basic access
└─────────────┘
```

### Permission Matrix

| Feature | Admin | Doctor | Patient |
|---------|-------|--------|---------|
| View Dashboard | ✅ | ✅ | ✅ |
| Manage Users | ✅ | ❌ | ❌ |
| Manage Doctors | ✅ | ❌ | ❌ |
| Manage Patients | ✅ | ✅ (View) | ❌ |
| Create Schedules | ❌ | ✅ | ❌ |
| Book Appointments | ❌ | ❌ | ✅ |
| Confirm Appointments | ❌ | ✅ | ❌ |
| Create Medical Records | ❌ | ✅ | ❌ |
| View Own Medical Records | ✅ | ✅ | ✅ |
| Generate Reports | ✅ | ❌ | ❌ |
| Manage Specialties | ✅ | ❌ | ❌ |

### Default Credentials

After seeding, use these credentials to test different roles:

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

---

## 📸 Screenshots

### Patient Portal

![Patient Dashboard](docs/screenshots/patient-dashboard.png)
*Patient dashboard showing upcoming appointments and medical records*

![Doctor Browse](docs/screenshots/doctor-browse.png)
*Browse and search for doctors by specialty*

![Appointment Booking](docs/screenshots/appointment-booking.png)
*Book appointments with real-time availability*

### Doctor Portal

![Doctor Dashboard](docs/screenshots/doctor-dashboard.png)
*Doctor dashboard with statistics and upcoming appointments*

![Schedule Management](docs/screenshots/schedule-management.png)
*Create and manage work schedules*

![Medical Record Creation](docs/screenshots/medical-record-creation.png)
*Create detailed medical records for patients*

### Admin Portal

![Admin Dashboard](docs/screenshots/admin-dashboard.png)
*Comprehensive admin dashboard with system analytics*

![User Management](docs/screenshots/user-management.png)
*Manage all system users and roles*

---

## 📁 Project Structure

```
Kyle-HMS/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin portal controllers
│   │   │   ├── Doctor/         # Doctor portal controllers
│   │   │   ├── Patient/        # Patient portal controllers
│   │   │   └── Auth/           # Authentication controllers
│   │   ├── Middleware/         # Custom middleware
│   │   └── Requests/           # Form requests
│   ├── Livewire/               # Livewire components
│   ├── Models/                 # Eloquent models
│   └── Providers/              # Service providers
│
├── config/                     # Configuration files
├── database/
│   ├── migrations/             # Database migrations
│   ├── seeders/                # Database seeders
│   └── factories/              # Model factories
│
├── public/                     # Public assets
│   ├── build/                  # Compiled assets
│   └── storage/                # Symlinked storage
│
├── resources/
│   ├── css/                    # Stylesheets
│   ├── js/                     # JavaScript files
│   └── views/                  # Blade templates
│       ├── admin/              # Admin views
│       ├── doctor/             # Doctor views
│       ├── patient/            # Patient views
│       └── components/         # Reusable components
│
├── routes/
│   ├── web.php                 # Web routes
│   ├── api.php                 # API routes
│   └── auth.php                # Authentication routes
│
├── storage/                    # File storage
│   ├── app/
│   │   └── public/
│   │       ├── avatars/        # User profile images
│   │       └── medical_records/ # Medical documents
│   ├── framework/
│   └── logs/
│
├── tests/                      # Test files
│   ├── Feature/                # Feature tests
│   └── Unit/                   # Unit tests
│
├── .env                        # Environment configuration
├── composer.json               # PHP dependencies
├── package.json                # JavaScript dependencies
├── tailwind.config.js          # Tailwind configuration
├── vite.config.js              # Vite configuration
└── README.md                   # This file
```

---

## 🔒 Security

### Security Measures Implemented

1. **Authentication**
   - Laravel Breeze authentication
   - Secure password hashing (bcrypt)
   - Email verification support
   - Password reset functionality

2. **Authorization**
   - Role-based access control (RBAC)
   - Spatie Laravel Permission
   - Custom middleware for route protection
   - Resource-level authorization

3. **Data Protection**
   - CSRF protection on all forms
   - XSS prevention in Blade templates
   - SQL injection prevention via Eloquent
   - File upload validation
   - Image sanitization

4. **Session Security**
   - Secure session configuration
   - HTTP-only cookies
   - Session timeout
   - CSRF token rotation

### Security Best Practices

```php
// Always validate user input
$validated = $request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users',
]);

// Use authorization gates
if (Gate::denies('update-appointment', $appointment)) {
    abort(403);
}

// Protect routes with middleware
Route::middleware(['auth', 'isDoctor'])->group(function () {
    // Protected routes
});

// Sanitize file uploads
$request->validate([
    'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
]);
```

---

## 🧪 Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage report
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/AppointmentTest.php
```

### Available Tests

- **Authentication Tests**: Registration, login, logout, password reset
- **Appointment Tests**: Booking, cancellation, status updates
- **Schedule Tests**: Creation, updates, validation
- **Permission Tests**: Role-based access control

### Writing Tests

```php
// Example feature test
public function test_patient_can_book_appointment()
{
    $patient = User::factory()->create();
    $patient->assignRole('patient');
    
    $response = $this->actingAs($patient)
        ->post('/patient/appointments', [
            'doctor_id' => 1,
            'schedule_id' => 1,
            'appointment_time' => '09:00',
        ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('appointments', [
        'patient_id' => $patient->patient->id,
    ]);
}
```

---

## 🌐 API Documentation

Kyle-HMS includes a RESTful API for potential mobile app integration or third-party services.

### Authentication

```http
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

### Appointments

```http
# Get all appointments
GET /api/appointments
Authorization: Bearer {token}

# Create appointment
POST /api/appointments
Authorization: Bearer {token}
Content-Type: application/json

{
  "doctor_id": 1,
  "schedule_id": 1,
  "appointment_time": "09:00",
  "reason": "Regular checkup"
}

# Update appointment status
PATCH /api/appointments/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "confirmed"
}
```

---

## 🚀 Deployment

### Production Preparation

1. **Optimize Application**

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Build production assets
npm run build
```

2. **Environment Configuration**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Use strong APP_KEY
# php artisan key:generate

# Configure production database
DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=strong_password
```

3. **Security Checklist**

- [ ] Set `APP_DEBUG=false`
- [ ] Use HTTPS
- [ ] Set strong `APP_KEY`
- [ ] Configure firewall
- [ ] Set up automated backups
- [ ] Enable rate limiting
- [ ] Configure CORS properly
- [ ] Set up monitoring

### Deployment Options

#### Shared Hosting

1. Upload files via FTP/SFTP
2. Point domain to `/public` directory
3. Configure `.env` file
4. Run migrations via SSH or hosting panel

#### VPS (Ubuntu/Nginx)

```bash
# Install dependencies
sudo apt update
sudo apt install php8.2 mysql-server nginx

# Configure Nginx
sudo nano /etc/nginx/sites-available/kyle-hms

# Enable site
sudo ln -s /etc/nginx/sites-available/kyle-hms /etc/nginx/sites-enabled/

# Restart Nginx
sudo systemctl restart nginx
```

#### Docker

```dockerfile
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www
```

---

## 🐛 Troubleshooting

### Common Issues

#### 1. White Screen / 500 Error

```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### 2. Database Connection Error

```bash
# Verify database credentials in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kyle_hms
DB_USERNAME=root
DB_PASSWORD=

# Test connection
php artisan migrate:status
```

#### 3. Permission Denied Errors

```bash
# Fix storage permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache

# Fix ownership
chown -R www-data:www-data storage bootstrap/cache

# For Windows XAMPP
# Right-click folders → Properties → Security → Edit → Add full control
```

#### 4. NPM/Vite Errors

```bash
# Clear NPM cache
npm cache clean --force

# Reinstall dependencies
rm -rf node_modules package-lock.json
npm install

# Rebuild assets
npm run build
```

#### 5. Routes Not Working

```bash
# Clear route cache
php artisan route:clear

# Check .htaccess exists in public/
# Check Apache mod_rewrite is enabled
```

### Debug Mode

Enable detailed error reporting during development:

```env
APP_DEBUG=true
APP_ENV=local
```

**⚠️ Never enable `APP_DEBUG=true` in production!**

---

## 🤝 Contributing

We welcome contributions to Kyle-HMS! Here's how you can help:

### How to Contribute

1. **Fork the Repository**

```bash
# Fork on GitHub, then clone your fork
git clone https://github.com/YOUR_USERNAME/Kyle-HMS.git
```

2. **Create a Feature Branch**

```bash
git checkout -b feature/amazing-feature
```

3. **Make Changes**
   - Write clean, documented code
   - Follow PSR-12 coding standards
   - Add tests for new features
   - Update documentation

4. **Commit Changes**

```bash
git add .
git commit -m "Add amazing feature"
```

5. **Push to Branch**

```bash
git push origin feature/amazing-feature
```

6. **Open Pull Request**
   - Provide clear description
   - Reference any related issues
   - Wait for review

### Coding Standards

- Follow PSR-12 for PHP code
- Use Laravel best practices
- Write meaningful commit messages
- Add PHPDoc comments
- Keep methods focused and small

### Testing Guidelines

- Write tests for new features
- Maintain test coverage above 80%
- Run tests before submitting PR

```bash
php artisan test
```

---

## 👨‍💻 Author

**Noun Sunheng** (KYLE)

- 🎓 Software Development Student
- 📧 Email: nounsunheng290503@gmail.com
- 🐙 GitHub: [@nounsunheng](https://github.com/nounsunheng)

### About the Developer

Kyle-HMS was developed as a comprehensive software development project demonstrating full-stack web development skills, database design, and modern web technologies. The project showcases:

- Proficiency in Laravel framework
- Modern frontend development
- Database design and management
- User authentication and authorization
- RESTful API development
- Responsive web design
- Security best practices
- Git version control

---

### Resources & References

- [Laravel Documentation](https://laravel.com/docs)
- [Livewire Documentation](https://livewire.laravel.com)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [DaisyUI Components](https://daisyui.com/components)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- [GeeksforGeeks HMS Reference](https://www.geeksforgeeks.org/websites-apps/hospital-management-system-project-in-software-development/)

---

## 🔮 Future Enhancements

Planned features for future versions:

- [ ] Mobile application (React Native / Flutter)
- [ ] Advanced reporting and analytics
- [ ] Integration with payment gateways
- [ ] Video consultation feature
- [ ] SMS notifications
- [ ] Multi-language support
- [ ] Dark mode theme
- [ ] Advanced search filters
- [ ] Export data to various formats
- [ ] API documentation with Swagger
- [ ] Real-time chat between patients and doctors
- [ ] Prescription management system
- [ ] Laboratory test integration
- [ ] Pharmacy inventory management

---

<div align="center">

**Made by [Noun Sunheng](https://github.com/nounsunheng)**

⭐ Star this repository if you found it helpful!

[Report Bug](https://github.com/nounsunheng/Kyle-HMS/issues) • [Request Feature](https://github.com/nounsunheng/Kyle-HMS/issues)

</div>
