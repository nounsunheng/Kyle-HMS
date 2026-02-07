# ⚡ Quick Start Guide - Kyle-HMS

Get Kyle-HMS up and running in **5 minutes**!

## Prerequisites Check

Before starting, verify you have:

```bash
php --version     # Should be 8.2+
composer --version # Should be 2.8+
node --version    # Should be 18+
mysql --version   # Should be 8.0+
```

---

## 🚀 Installation (5 Steps)

### Step 1: Clone Repository (30 seconds)

```bash
git clone https://github.com/nounsunheng/Kyle-HMS.git
cd Kyle-HMS
```

### Step 2: Install Dependencies (2 minutes)

```bash
composer install
npm install
```

### Step 3: Environment Setup (30 seconds)

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file - Update database credentials:

```env
DB_DATABASE=kyle_hms
DB_USERNAME=root
DB_PASSWORD=
```

### Step 4: Database Setup (1 minute)

```bash
# Create database
mysql -u root -p
CREATE DATABASE kyle_hms;
EXIT;

# Run migrations and seeders
php artisan migrate --seed
php artisan storage:link
```

### Step 5: Build & Run (1 minute)

```bash
# Terminal 1: Build assets
npm run dev

# Terminal 2: Start server
php artisan serve
```

Visit: **http://localhost:8000**

---

## 🔐 Default Login Credentials

### Administrator
- Email: `admin@kylehms.com`
- Password: `password`

### Doctor
- Email: `doctor1@kylehms.com`
- Password: `password`

### Patient
- Email: `patient1@kylehms.com`
- Password: `password`

⚠️ **Change these passwords immediately!**

---

## 📁 Project Structure

```
Kyle-HMS/
├── app/              # Application code
├── resources/        # Views, assets
├── routes/           # Route definitions
├── database/         # Migrations, seeders
├── public/           # Web root
└── storage/          # File storage
```

---

## 🎯 Next Steps

1. ✅ **Explore the System**
   - Login with different roles
   - Try booking an appointment
   - Create a medical record

2. ✅ **Read Documentation**
   - [Full README](README.md)
   - [User Manual](USER_MANUAL.md)
   - [Technical Docs](TECHNICAL_DOCUMENTATION.md)

3. ✅ **Customize**
   - Update branding
   - Configure email
   - Add specialties

---

## 🐛 Common Issues

### Port 8000 Already in Use

```bash
# Use different port
php artisan serve --port=8001
```

### Database Connection Failed

```bash
# Check MySQL is running
# Verify credentials in .env
# Create database if missing
```

### NPM Errors

```bash
# Clear cache and reinstall
rm -rf node_modules package-lock.json
npm install
```

### Permission Errors (Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
```

---

## 💡 Quick Tips

**Virtual Host Setup:**
```apache
# Add to XAMPP httpd-vhosts.conf
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/Kyle-HMS/public"
    ServerName kyle-hms.local
</VirtualHost>
```

**Windows hosts file:**
```
# Add to C:\Windows\System32\drivers\etc\hosts
127.0.0.1 kyle-hms.local
```

**Clear All Caches:**
```bash
php artisan optimize:clear
```

**Production Build:**
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📞 Need Help?

- 📧 Email: nounsunheng290503@gmail.com
- 🐙 GitHub Issues: https://github.com/nounsunheng/Kyle-HMS/issues
- 📖 Docs: See [INSTALLATION.md](INSTALLATION.md)

---

## 🎓 Learning Resources

- [Laravel Docs](https://laravel.com/docs)
- [Livewire Docs](https://livewire.laravel.com)
- [Tailwind CSS](https://tailwindcss.com)

---

**You're all set! Happy coding! 🎉**

For detailed installation instructions, see [INSTALLATION.md](INSTALLATION.md)
