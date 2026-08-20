# 🚀 Setup Lengkap IHSG Screener - Step by Step

Panduan lengkap setup sistem login untuk berbagai Operating System.

---

## 📋 Daftar OS

1. [Windows 10/11 dengan XAMPP](#windows-dengan-xampp)
2. [Linux Ubuntu/Debian](#linux-ubuntu)
3. [macOS](#macos)
4. [Docker (Recommended)](#docker)

---

## Windows dengan XAMPP

### Prerequisites
- XAMPP installed (Apache + MySQL + PHP)
- Download: https://www.apachefriends.org/

### Step 1: Install XAMPP

1. Download XAMPP dari https://www.apachefriends.org/
2. Run installer dan ikuti instruksi
3. Install ke folder default (C:\xampp)
4. Finish installation

### Step 2: Start Apache & MySQL

1. Buka XAMPP Control Panel
2. Click "Start" pada:
   - Apache
   - MySQL
3. Tunggu hingga berstatus "Running" (highlight hijau)

### Step 3: Download Project Files

1. Extract semua file ke:
   ```
   C:\xampp\htdocs\ihsg-screener\
   ```
   
   Struktur folder:
   ```
   C:\xampp\htdocs\ihsg-screener\
   ├── index.html
   ├── dashboard.html
   ├── styles.css
   ├── main.js
   ├── config.php
   ├── api_login.php
   ├── api_register.php
   ├── api_logout.php
   ├── api_auth_check.php
   ├── database_schema.sql
   └── README.md
   ```

### Step 4: Create Database

1. Buka browser, akses: http://localhost/phpmyadmin
2. Login (default: root, no password)
3. Di sidebar, click "New"
4. Database name: `ihsg_screener`
5. Collation: `utf8mb4_unicode_ci`
6. Click "Create"
7. Select database `ihsg_screener`
8. Click "Import"
9. Choose file: `database_schema.sql`
10. Click "Import"

### Step 5: Configure Database

1. Open file: `C:\xampp\htdocs\ihsg-screener\config.php`
2. Find dan update:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Leave empty for default XAMPP
   define('DB_NAME', 'ihsg_screener');
   ```

### Step 6: Test Setup

1. Buka browser
2. Akses: http://localhost/ihsg-screener/index.html
3. Harus muncul login page dengan design modern
4. Coba klik "Daftar" untuk register form
5. Coba register akun baru:
   - Username: `testuser`
   - Email: `test@example.com`
   - Password: `TestPassword123`
6. Klik "Buat Akun"
7. Harus muncul pesan sukses
8. Login dengan akun tersebut
9. Harus redirect ke dashboard.html

### Troubleshooting Windows

**Error: "MySQL not running"**
- Check XAMPP Control Panel, ensure MySQL is running
- Atau restart MySQL: Stop → Start

**Error: "Access denied for user 'root'@'localhost'"**
- Update DB_PASS di config.php dengan password MySQL Anda
- Default XAMPP: empty password

**Port 3306 already in use**
- Ada aplikasi lain menggunakan MySQL
- Setup custom port di config.php

---

## Linux (Ubuntu/Debian)

### Prerequisites
- Ubuntu 18.04 atau lebih tinggi
- Terminal access
- Sudo privileges

### Step 1: Install LAMP Stack

```bash
# Update system
sudo apt update
sudo apt upgrade -y

# Install Apache
sudo apt install apache2 -y
sudo systemctl start apache2
sudo systemctl enable apache2

# Install MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation

# Install PHP
sudo apt install php php-mysql php-cli php-gd php-curl -y

# Verify installation
php -v
apache2 -v
mysql --version
```

### Step 2: Setup Database

```bash
# Login ke MySQL
mysql -u root -p

# Create database & user
CREATE DATABASE ihsg_screener;
CREATE USER 'ihsg_user'@'localhost' IDENTIFIED BY 'strong_password_123';
GRANT ALL PRIVILEGES ON ihsg_screener.* TO 'ihsg_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import schema
mysql -u ihsg_user -p ihsg_screener < /path/to/database_schema.sql
```

### Step 3: Setup Project Files

```bash
# Create directory
sudo mkdir -p /var/www/html/ihsg-screener
sudo chown -R $USER:$USER /var/www/html/ihsg-screener

# Copy files
cp -r /path/to/ihsg-screener/* /var/www/html/ihsg-screener/

# Set permissions
sudo chmod -R 755 /var/www/html/ihsg-screener
sudo chmod -R 644 /var/www/html/ihsg-screener/*.{php,html,css,js}
```

### Step 4: Configure Apache

```bash
# Enable mod_rewrite
sudo a2enmod rewrite

# Create virtual host config
sudo nano /etc/apache2/sites-available/ihsg-screener.conf
```

Add this content:
```apache
<VirtualHost *:80>
    ServerName localhost
    ServerAlias ihsg-screener.local
    DocumentRoot /var/www/html/ihsg-screener

    <Directory /var/www/html/ihsg-screener>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/ihsg-error.log
    CustomLog ${APACHE_LOG_DIR}/ihsg-access.log combined
</VirtualHost>
```

Enable site:
```bash
sudo a2ensite ihsg-screener.conf
sudo apache2ctl configtest  # Should output "Syntax OK"
sudo systemctl reload apache2
```

### Step 5: Configure Application

```bash
# Edit config.php
nano /var/www/html/ihsg-screener/config.php

# Update:
# define('DB_USER', 'ihsg_user');
# define('DB_PASS', 'strong_password_123');
```

### Step 6: Test Setup

```bash
# Access via terminal
curl http://localhost/ihsg-screener/index.html | head -20

# Or open in browser
# http://localhost/ihsg-screener/index.html
```

### Linux Troubleshooting

**Error: "Permission denied"**
```bash
sudo chmod -R 755 /var/www/html/ihsg-screener
sudo chown -R www-data:www-data /var/www/html/ihsg-screener
```

**Error: "MySQL connection refused"**
```bash
# Check MySQL status
sudo systemctl status mysql

# Restart if needed
sudo systemctl restart mysql
```

**Error: "Apache modules not enabled"**
```bash
# Enable required modules
sudo a2enmod php8.1
sudo a2enmod rewrite
sudo a2enmod headers
sudo systemctl reload apache2
```

---

## macOS

### Prerequisites
- macOS 10.14 or higher
- Homebrew installed

### Step 1: Install Homebrew

```bash
# Install Homebrew
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Verify
brew --version
```

### Step 2: Install LAMP Stack

```bash
# Update Homebrew
brew update

# Install Apache (usually pre-installed)
# But you can use Homebrew version
brew install httpd

# Install MySQL
brew install mysql

# Install PHP
brew install php

# Start services
brew services start httpd
brew services start mysql
```

### Step 3: Setup Database

```bash
# Secure MySQL installation
mysql_secure_installation

# Create database
mysql -u root -p

# In MySQL:
CREATE DATABASE ihsg_screener;
CREATE USER 'ihsg_user'@'localhost' IDENTIFIED BY 'strong_password_123';
GRANT ALL PRIVILEGES ON ihsg_screener.* TO 'ihsg_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import schema
mysql -u ihsg_user -p ihsg_screener < ~/Downloads/database_schema.sql
```

### Step 4: Setup Project

```bash
# Create directory
sudo mkdir -p /Library/WebServer/Documents/ihsg-screener
sudo chown -R $(whoami) /Library/WebServer/Documents/ihsg-screener

# Copy files
cp -r ~/Downloads/ihsg-screener/* /Library/WebServer/Documents/ihsg-screener/

# Set permissions
chmod -R 755 /Library/WebServer/Documents/ihsg-screener
chmod -R 644 /Library/WebServer/Documents/ihsg-screener/*.{php,html,css,js}
```

### Step 5: Configure Apache

```bash
# Edit Apache config
sudo nano /etc/apache2/httpd.conf

# Find dan uncomment:
# LoadModule php_module libexec/apache2/mod_php.so

# Restart Apache
sudo apachectl restart
```

### Step 6: Configure Application

```bash
# Edit config
nano /Library/WebServer/Documents/ihsg-screener/config.php

# Update database credentials
```

### Step 7: Test

```bash
# Access
open http://localhost/ihsg-screener/index.html
```

### macOS Troubleshooting

**Error: "Apache won't start"**
```bash
# Check Apache config
sudo apachectl configtest

# Restart with verbose
sudo apachectl -k restart -v
```

**MySQL not found**
```bash
# Find MySQL socket
sudo brew services list

# Check if running
ps aux | grep mysql
```

---

## Docker (Recommended for Production)

### Prerequisites
- Docker Desktop installed
- Docker Compose installed

### Step 1: Create Docker Files

**Create: `Dockerfile`**
```dockerfile
FROM php:8.1-apache

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable mod_rewrite
RUN a2enmod rewrite

# Copy project
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
```

**Create: `docker-compose.yml`**
```yaml
version: '3.8'

services:
  web:
    build: .
    container_name: ihsg-screener-web
    ports:
      - "80:80"
    volumes:
      - ./:/var/www/html
    depends_on:
      - db
    environment:
      - DB_HOST=db
      - DB_USER=ihsg_user
      - DB_PASS=secure_password_123
      - DB_NAME=ihsg_screener

  db:
    image: mysql:8.0
    container_name: ihsg-screener-db
    ports:
      - "3306:3306"
    environment:
      - MYSQL_ROOT_PASSWORD=root_password_123
      - MYSQL_DATABASE=ihsg_screener
      - MYSQL_USER=ihsg_user
      - MYSQL_PASSWORD=secure_password_123
    volumes:
      - db_data:/var/lib/mysql
      - ./database_schema.sql:/docker-entrypoint-initdb.d/schema.sql

volumes:
  db_data:
```

### Step 2: Run Docker

```bash
# Build dan run containers
docker-compose up -d

# Verify
docker-compose ps

# Should show:
# ihsg-screener-web - running
# ihsg-screener-db - running
```

### Step 3: Access Application

```bash
# Open browser
http://localhost/ihsg-screener/index.html
```

### Step 4: Docker Commands

```bash
# View logs
docker-compose logs -f web
docker-compose logs -f db

# Stop containers
docker-compose stop

# Remove containers
docker-compose down

# Rebuild
docker-compose build --no-cache
docker-compose up -d
```

### Docker Troubleshooting

**Error: "Port 80 already in use"**
```bash
# Change port in docker-compose.yml
ports:
  - "8080:80"

# Then access: http://localhost:8080/ihsg-screener/
```

**Error: "Database connection refused"**
```bash
# Wait for DB to be ready
# DB may take 10-15 seconds to start
# Check logs: docker-compose logs db
```

---

## 🧪 Testing After Setup

### Test Checklist

- [ ] Can access http://localhost/ihsg-screener/index.html
- [ ] Login page displays correctly
- [ ] Register form works
- [ ] Can create new account
- [ ] Can login with created account
- [ ] Redirects to dashboard.html after login
- [ ] Logout button works
- [ ] Can view user profile
- [ ] Can change password
- [ ] Database tables created correctly

### Test API Endpoints

```bash
# Test login
curl -X POST http://localhost/ihsg-screener/api_login.php \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"TestPassword123"}'

# Test register
curl -X POST http://localhost/ihsg-screener/api_register.php \
  -H "Content-Type: application/json" \
  -d '{"username":"newuser","email":"new@example.com","password":"TestPassword123","confirm_password":"TestPassword123"}'

# Test auth check
curl http://localhost/ihsg-screener/api_auth_check.php
```

---

## 🔐 Security Checklist

Before going to production:

- [ ] Change all default passwords
- [ ] Update JWT_SECRET in config.php
- [ ] Enable HTTPS/SSL
- [ ] Disable debug mode (APP_ENV = production)
- [ ] Set correct file permissions
- [ ] Backup database regularly
- [ ] Update all dependencies
- [ ] Enable firewall
- [ ] Use strong database passwords
- [ ] Implement rate limiting

---

## 📞 Support

### Common Issues

**"Cannot access database"**
- Check if MySQL is running
- Verify credentials in config.php
- Ensure database exists

**"Login page not loading"**
- Check Apache is running
- Verify file permissions
- Check browser console for errors

**"API endpoints returning 404"**
- Verify .htaccess is in place
- Check Apache mod_rewrite is enabled
- Verify file names are correct

**"Session not persisting"**
- Check PHP session folder is writable
- Verify SESSION_TIMEOUT setting
- Check browser cookies enabled

---

## 📚 Next Steps

After successful setup:

1. **Customize Design** - Edit styles.css
2. **Add Features** - Create new API endpoints
3. **Deploy** - Follow production deployment guide
4. **Monitor** - Set up logging & monitoring
5. **Backup** - Regular database backups
6. **Updates** - Keep PHP & MySQL updated

---

## 🎉 Congratulations!

Your IHSG Screener login system is ready!

**Happy Coding! 🚀**
