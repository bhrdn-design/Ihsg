# IHSG Screener - Sistem Login Lengkap

Sistem login dan authentication lengkap untuk platform screening saham Indonesia berbasis AI. Gratis, open source, dan siap pakai!

## 📋 Daftar Isi

1. [Fitur](#fitur)
2. [Persyaratan Sistem](#persyaratan-sistem)
3. [Instalasi](#instalasi)
4. [Setup Database](#setup-database)
5. [Konfigurasi](#konfigurasi)
6. [Struktur File](#struktur-file)
7. [API Endpoints](#api-endpoints)
8. [Panduan Penggunaan](#panduan-penggunaan)
9. [Troubleshooting](#troubleshooting)

---

## ✨ Fitur

### Authentication & Security
- ✅ Login dengan email & password
- ✅ Register/Sign Up baru
- ✅ Session management dengan PHP
- ✅ Password hashing dengan bcrypt
- ✅ Remember Me (30 hari)
- ✅ Login attempt rate limiting
- ✅ Password reset via email
- ✅ Activity logging
- ✅ CORS configuration
- ✅ SQL Injection prevention

### Frontend
- ✅ Responsive design (mobile-first)
- ✅ Modern UI dengan glassmorphism
- ✅ Form validation (client-side)
- ✅ Password visibility toggle
- ✅ Loading spinner
- ✅ Success/Error modals
- ✅ Animated transitions

### Backend
- ✅ RESTful API endpoints
- ✅ Database abstraction layer
- ✅ Error handling yang baik
- ✅ Input sanitization
- ✅ User session management
- ✅ Login logs & audit trail

### Database
- ✅ Users table dengan fields lengkap
- ✅ Watchlist untuk favorit saham
- ✅ Saved filters
- ✅ Password reset tokens
- ✅ Login history/logs
- ✅ Proper indexes untuk performance

---

## 🖥️ Persyaratan Sistem

### Software Requirements
- **PHP** 7.4 atau lebih tinggi
- **MySQL** 5.7 atau lebih tinggi (atau MariaDB)
- **Web Server** (Apache, Nginx, atau IIS)
- **Browser** modern dengan JavaScript support

### Recommended
- PHP 8.1 atau lebih tinggi
- MySQL 8.0 atau lebih tinggi
- Apache dengan mod_rewrite
- SSL/TLS certificate untuk production

---

## 🚀 Instalasi

### Step 1: Download & Setup File

1. **Copy semua file ke folder project Anda:**
   ```bash
   mkdir -p /var/www/html/ihsg-screener
   cd /var/www/html/ihsg-screener
   # Copy semua file ke sini
   ```

2. **Set proper permissions:**
   ```bash
   chmod 755 /var/www/html/ihsg-screener
   chmod 644 /var/www/html/ihsg-screener/*.php
   chmod 644 /var/www/html/ihsg-screener/*.html
   chmod 644 /var/www/html/ihsg-screener/*.css
   chmod 644 /var/www/html/ihsg-screener/*.js
   ```

### Step 2: Setup Database

1. **Buka MySQL client:**
   ```bash
   mysql -u root -p
   ```

2. **Buat database dan user:**
   ```sql
   CREATE DATABASE ihsg_screener;
   CREATE USER 'ihsg_user'@'localhost' IDENTIFIED BY 'your_secure_password_here';
   GRANT ALL PRIVILEGES ON ihsg_screener.* TO 'ihsg_user'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

3. **Import database schema:**
   ```bash
   mysql -u ihsg_user -p ihsg_screener < database_schema.sql
   ```

4. **Verify tables created:**
   ```bash
   mysql -u ihsg_user -p -D ihsg_screener -e "SHOW TABLES;"
   ```

### Step 3: Konfigurasi File

Edit `config.php` dan sesuaikan:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'ihsg_user');
define('DB_PASS', 'your_secure_password_here');
define('DB_NAME', 'ihsg_screener');

define('API_BASE_URL', 'http://localhost/ihsg-screener/api/');
define('FRONTEND_URL', 'http://localhost/ihsg-screener/');
```

### Step 4: Test Setup

1. **Buka browser dan akses:**
   ```
   http://localhost/ihsg-screener/index.html
   ```

2. **Coba register akun baru**

3. **Coba login dengan akun tersebut**

4. **Harus redirect ke dashboard.html**

---

## ⚙️ Konfigurasi

### config.php - Settings Penting

#### Database Connection
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'ihsg_screener');
define('DB_PORT', 3306);
```

#### Security
```php
define('JWT_SECRET', 'change_this_in_production_with_strong_key');
define('SESSION_TIMEOUT', 30 * 24 * 60 * 60); // 30 hari
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_ATTEMPT_TIMEOUT', 15 * 60); // 15 menit
```

#### URLs
```php
define('API_BASE_URL', 'http://localhost/ihsg-screener/api/');
define('FRONTEND_URL', 'http://localhost/ihsg-screener/');
define('APP_ENV', 'development'); // atau 'production'
```

#### Email (untuk password reset)
```php
define('MAIL_FROM', 'noreply@ihsgscreener.com');
define('MAIL_SMTP_HOST', 'smtp.gmail.com');
define('MAIL_SMTP_PORT', 587);
define('MAIL_SMTP_USER', 'your_email@gmail.com');
define('MAIL_SMTP_PASS', 'your_app_password');
```

### main.js - API Base URL

Update di `main.js`:
```javascript
const API_BASE = 'http://localhost/ihsg-screener/';
const API_LOGIN = API_BASE + 'api_login.php';
const API_REGISTER = API_BASE + 'api_register.php';
// dst...
```

---

## 📁 Struktur File

```
ihsg-screener/
├── index.html                 # Login page
├── dashboard.html             # Main dashboard
├── reset-password.html        # Password reset page (buat sendiri)
├── 404.html                   # Error page (buat sendiri)
│
├── styles.css                 # Main CSS
├── main.js                    # Main JavaScript untuk login
├── dashboard.js               # JavaScript untuk dashboard
│
├── config.php                 # Konfigurasi database & app
├── api_login.php              # Login API endpoint
├── api_register.php           # Register API endpoint
├── api_logout.php             # Logout API endpoint
├── api_auth_check.php         # Check auth status
├── api_forgot_password.php    # Forgot password
│
├── database_schema.sql        # Database schema
├── README.md                  # File ini
└── .htaccess                  # Apache rewrite rules (opsional)
```

---

## 🔌 API Endpoints

### Login
```http
POST /api_login.php
Content-Type: application/json

{
  "email": "user@email.com",
  "password": "password123",
  "remember_me": true
}

Response (200 OK):
{
  "status": "success",
  "message": "Login berhasil",
  "data": {
    "user_id": 1,
    "username": "username",
    "email": "user@email.com",
    "full_name": "Full Name",
    "is_verified": true,
    "redirect": "http://localhost/ihsg-screener/dashboard.html"
  }
}
```

### Register
```http
POST /api_register.php
Content-Type: application/json

{
  "username": "newuser",
  "email": "new@email.com",
  "password": "SecurePass123",
  "confirm_password": "SecurePass123"
}

Response (201 Created):
{
  "status": "success",
  "message": "Pendaftaran berhasil",
  "data": {
    "user_id": 2,
    "username": "newuser",
    "email": "new@email.com"
  }
}
```

### Logout
```http
POST /api_logout.php

Response (200 OK):
{
  "status": "success",
  "message": "Logout berhasil",
  "data": {
    "redirect": "http://localhost/ihsg-screener/index.html"
  }
}
```

### Check Auth Status
```http
GET /api_auth_check.php

Response (200 OK):
{
  "status": "success",
  "message": "User terautentikasi",
  "data": {
    "user_id": 1,
    "username": "username",
    "email": "user@email.com",
    "is_authenticated": true
  }
}
```

---

## 📖 Panduan Penggunaan

### Untuk Users

1. **Register**
   - Klik "Daftar" di login page
   - Isi username, email, password
   - Klik "Buat Akun"
   - Akan redirect ke login

2. **Login**
   - Masukkan email & password
   - Klik checkbox "Ingat saya" jika ingin stay logged in
   - Klik "Masuk ke Screener"
   - Akan redirect ke dashboard

3. **Logout**
   - Klik tombol "Logout" di navbar
   - Session akan dihapus
   - Redirect ke login page

### Untuk Developers

1. **Customize UI/Design**
   - Edit `styles.css` untuk styling
   - Modifikasi `index.html` untuk struktur
   - Update `main.js` untuk behavior

2. **Extend Features**
   - Buat file PHP API baru di folder `api/`
   - Add endpoint di `config.php`
   - Call dari JavaScript dengan `fetch()`

3. **Database Queries**
   - Gunakan prepared statements (sudah ada di config)
   - Hindari SQL injection
   - Selalu validate & sanitize input

### Testing

**Login dengan test account:**
```
Email: test@example.com
Password: TestPassword123
```

Atau register akun baru untuk testing.

---

## 🐛 Troubleshooting

### "Database Connection Failed"

**Solusi:**
1. Check MySQL server berjalan: `sudo systemctl status mysql`
2. Verify database credentials di `config.php`
3. Test connection: `mysql -u ibsg_user -p -D ihsg_screener`

### "Login gagal - Email atau password salah"

**Solusi:**
1. Pastikan sudah register akun
2. Double-check email & password (case-sensitive)
3. Clear browser cache/cookies
4. Check login_logs di database untuk error details

### "Redirect loop / tidak bisa masuk dashboard"

**Solusi:**
1. Check session cache: `php -r "session_start(); print_r($_SESSION);"`
2. Clear PHP session files: `sudo rm -rf /var/lib/php/sessions/*`
3. Verify `api_auth_check.php` return correct data
4. Check browser cookies enabled

### "CORS Error"

**Solusi:**
1. Jika frontend & backend domain berbeda, update `.htaccess`:
   ```apache
   <IfModule mod_headers.c>
       Header set Access-Control-Allow-Origin "*"
       Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
       Header set Access-Control-Allow-Headers "Content-Type, Authorization"
   </IfModule>
   ```

2. Atau update di `config.php`:
   ```php
   header('Access-Control-Allow-Origin: http://your-frontend-domain.com');
   ```

### "Email tidak terkirim"

**Solusi:**
1. Uncomment email functions di API file
2. Install library email: `composer require phpmailer/phpmailer`
3. Setup SMTP credentials di `config.php`
4. Test SMTP connection

### "Password hashing tidak bekerja"

**Solusi:**
Pastikan `password_hash()` dan `password_verify()` tersedia:
```php
if (function_exists('password_hash')) {
    echo "Password functions available";
}
```

---

## 🔐 Security Best Practices

### Production Deployment

1. **Use HTTPS only**
   ```apache
   # Force HTTPS
   RewriteEngine On
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

2. **Change default credentials**
   - Update database username/password
   - Change JWT_SECRET di config.php
   - Update MAIL_SMTP credentials

3. **Disable debug mode**
   ```php
   define('APP_ENV', 'production');
   ```

4. **Add rate limiting**
   - Already implemented untuk login
   - Extend untuk API endpoints lain

5. **Update dependencies**
   ```bash
   composer update
   ```

6. **Regular backups**
   ```bash
   mysqldump -u root -p ihsg_screener > backup_$(date +%Y%m%d).sql
   ```

---

## 📚 File Documentation

### config.php
- Database connection setup
- Security configuration
- Helper functions untuk sanitasi & hashing
- Database singleton class

### api_login.php
- Handle user login
- Validate credentials
- Session management
- Rate limiting check

### api_register.php
- Handle user registration
- Validate input
- Check duplicate email/username
- Hash password

### api_logout.php
- Destroy session
- Clear cookies
- Log activity

### main.js
- Form handling
- API calls dengan fetch()
- Input validation
- Error display

---

## 🤝 Contributing

Untuk improve project ini:

1. Test semua features
2. Report bugs atau issues
3. Suggest improvements
4. Submit pull requests

---

## 📄 License

**Free & Open Source** - Dapat digunakan untuk personal & komersial

---

## 📞 Support

### Frequently Asked Questions

**Q: Apakah saya perlu membayar?**
A: Tidak, semuanya gratis dan open source!

**Q: Bisa di-deploy ke production?**
A: Ya, follow security best practices di atas.

**Q: Bisa customize design?**
A: Ya, edit `styles.css` dan `index.html` sesuai kebutuhan.

**Q: Bagaimana menambah fitur baru?**
A: Buat API PHP baru, then call dari JavaScript.

---

## 🎯 Roadmap

- [ ] Email verification untuk register
- [ ] Two-factor authentication (2FA)
- [ ] OAuth login (Google, Facebook)
- [ ] Admin panel
- [ ] User profile management
- [ ] Activity dashboard
- [ ] API rate limiting per user
- [ ] Caching dengan Redis
- [ ] Tests dengan PHPUnit

---

## 📝 Version History

### v1.7 (Current)
- ✅ Complete login & register system
- ✅ Session management
- ✅ Responsive design
- ✅ Database with all tables
- ✅ API endpoints
- ✅ Security features

---

**Dibuat dengan ❤️ untuk investor Indonesia**

Jangan lupa untuk DYOR (Do Your Own Research) sebelum investasi!

---

## 🚀 Quick Start (TL;DR)

```bash
# 1. Setup MySQL
mysql -u root -p < database_schema.sql

# 2. Update config.php dengan credentials

# 3. Copy files ke /var/www/html/ihsg-screener/

# 4. Buka http://localhost/ihsg-screener/

# 5. Register & Login!
```

---

**Happy Coding! 🎉**
