# ⚡ QUICK START - IHSG Screener Login System

Panduan tercepat untuk setup & mulai coding dalam 5 menit!

---

## 🚀 Setup Tercepat (5 Menit)

### Step 1: Copy Files
```bash
# Copy semua file ke folder project
mkdir ~/projects/ihsg-screener
cd ~/projects/ihsg-screener
# Copy semua file yang sudah dibuat ke sini
```

### Step 2: Setup Database (2 Menit)
```bash
# Buka MySQL
mysql -u root -p

# Copy-paste ini:
CREATE DATABASE ihsg_screener;
CREATE USER 'ihsg_user'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON ihsg_screener.* TO 'ihsg_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import schema
mysql -u ihsg_user -p ihsg_screener < database_schema.sql
```

### Step 3: Update Config (1 Menit)
Edit `config.php`:
```php
define('DB_USER', 'ihsg_user');
define('DB_PASS', 'password123');
define('DB_NAME', 'ihsg_screener');
```

### Step 4: Run Server (1 Menit)
```bash
# Gunakan built-in PHP server
php -S localhost:8000 -t .

# Atau dengan Apache, akses:
http://localhost/ihsg-screener/index.html
```

### Step 5: Test (1 Menit)
1. Buka http://localhost:8000/index.html
2. Klik "Daftar"
3. Isi form:
   - Username: `testuser`
   - Email: `test@example.com`
   - Password: `TestPass123`
4. Klik "Buat Akun"
5. Login dengan akun tersebut
6. 🎉 Berhasil!

---

## 📁 Daftar File

### Frontend (3 files)
```
index.html       - Login & Register page
dashboard.html   - Dashboard setelah login
styles.css       - Semua styling
main.js          - Login functionality
dashboard.js     - Dashboard functionality
```

### Backend (9 files)
```
config.php               - Database config & helpers
api_login.php            - Login endpoint
api_register.php         - Register endpoint
api_logout.php           - Logout endpoint
api_auth_check.php       - Check authentication
api_forgot_password.php  - Password reset
api_user_profile.php     - User profile GET/PUT
api_change_password.php  - Change password
api_watchlist.php        - Watchlist management
```

### Database (1 file)
```
database_schema.sql  - Create tables
```

### Documentation (5 files)
```
README.md                      - Main documentation
SETUP_INSTRUCTIONS.md          - Step-by-step setup
COMPLETE_SETUP_CHECKLIST.md   - Verification checklist
API_TESTING_GUIDE.md           - How to test APIs
QUICK_START.md                 - File ini
```

### Configuration (2 files)
```
.htaccess        - Apache URL rewriting
package.json     - Node dependencies (optional)
```

**Total: 21 Files** ✅

---

## 🎯 Quick Reference

### File Structure di Server
```
/var/www/html/ihsg-screener/
├── index.html
├── dashboard.html
├── styles.css
├── main.js
├── dashboard.js
├── config.php
├── api_login.php
├── api_register.php
├── api_logout.php
├── api_auth_check.php
├── api_forgot_password.php
├── api_user_profile.php
├── api_change_password.php
├── api_watchlist.php
├── database_schema.sql
├── .htaccess
├── README.md
├── SETUP_INSTRUCTIONS.md
├── COMPLETE_SETUP_CHECKLIST.md
├── API_TESTING_GUIDE.md
├── QUICK_START.md
└── package.json
```

---

## 🔧 Development vs Production

### Development
```php
// config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('APP_ENV', 'development');

// main.js
const API_BASE = 'http://localhost:8000/';
```

### Production
```php
// config.php
define('DB_HOST', 'db.example.com');
define('DB_USER', 'prod_user');
define('DB_PASS', 'strong_password_here');
define('APP_ENV', 'production');

// main.js
const API_BASE = 'https://api.example.com/ihsg-screener/';
```

---

## 📚 Dokumentasi Lengkap

| Dokumen | Untuk Apa |
|---------|-----------|
| **README.md** | Overview lengkap & fitur |
| **SETUP_INSTRUCTIONS.md** | Setup detail per OS |
| **QUICK_START.md** | Setup cepat (ini) |
| **COMPLETE_SETUP_CHECKLIST.md** | Verification checklist |
| **API_TESTING_GUIDE.md** | Testing API endpoints |

---

## 🧪 Testing Quick Commands

### Test dengan curl

**Register:**
```bash
curl -X POST http://localhost:8000/api_register.php \
  -H "Content-Type: application/json" \
  -d '{"username":"test","email":"test@test.com","password":"Test123456","confirm_password":"Test123456"}'
```

**Login:**
```bash
curl -X POST http://localhost:8000/api_login.php \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"Test123456"}' \
  -c cookies.txt
```

**Auth Check:**
```bash
curl http://localhost:8000/api_auth_check.php -b cookies.txt
```

---

## 🔐 Security Checklist

### Sebelum Production
- [ ] Update `DB_PASS` dengan password kuat
- [ ] Change `JWT_SECRET` ke random string
- [ ] Set `APP_ENV = 'production'`
- [ ] Enable HTTPS/SSL
- [ ] Backup database
- [ ] Test security features

---

## ❓ FAQ Cepat

**Q: Database connection error?**
A: Check MySQL running, update DB credentials di config.php

**Q: Login page blank?**
A: Check PHP extension enabled, verify file permissions

**Q: Can't upload file?**
A: Set folder permissions: `chmod 755 /var/www/html/ihsg-screener`

**Q: How to customize design?**
A: Edit `styles.css` untuk warna, fonts, dll.

**Q: How to add new feature?**
A: Buat API PHP baru, call dari JavaScript dengan `fetch()`

---

## 🎓 Learning Path

### 1. Understand (30 min)
- Read README.md
- Review file structure
- Understand authentication flow

### 2. Setup (15 min)
- Follow QUICK_START.md
- Verify with SETUP_CHECKLIST.md
- Test basic functionality

### 3. Test (20 min)
- Test Register flow
- Test Login flow
- Test APIs with curl
- Read API_TESTING_GUIDE.md

### 4. Customize (varies)
- Update styles.css
- Add new fields to form
- Extend database schema
- Add new API endpoints

### 5. Deploy (30 min)
- Follow SETUP_INSTRUCTIONS.md untuk production
- Test thoroughly
- Set up backups
- Monitor logs

---

## 🚀 Next Steps

### Immediate
1. Setup sistem sesuai QUICK_START.md
2. Test Register & Login
3. Verify database

### Short Term
1. Customize design sesuai brand
2. Add email verification
3. Add password reset email
4. Extend user profile

### Long Term
1. Add 2-Factor Authentication
2. Implement OAuth (Google, Facebook)
3. Create Admin Panel
4. Add user management features
5. Implement activity dashboard
6. Add analytics

---

## 📞 Support

### When Something Goes Wrong

1. **Check Error Log:**
   ```bash
   # Apache
   tail -f /var/log/apache2/error.log
   
   # PHP
   tail -f /var/log/php-errors.log
   
   # MySQL
   tail -f /var/log/mysql/error.log
   ```

2. **Check Browser Console:**
   - Press F12
   - Check Console tab
   - Look for JavaScript errors

3. **Check Database:**
   ```bash
   mysql -u ihsg_user -p -D ihsg_screener
   SHOW TABLES;
   SELECT COUNT(*) FROM users;
   ```

4. **Read Documentation:**
   - README.md - General info
   - SETUP_INSTRUCTIONS.md - Setup issues
   - API_TESTING_GUIDE.md - API issues
   - COMPLETE_SETUP_CHECKLIST.md - Verification

---

## ✨ Features Overview

### ✅ Implemented
- Login/Register dengan validation
- Session management
- Password hashing (bcrypt)
- Rate limiting login attempts
- User profile management
- Password change
- Watchlist management
- Activity logging
- Responsive design
- Error handling

### 🔄 Optional
- Email verification
- Password reset via email
- OAuth login (Google, Facebook)
- 2-Factor Authentication
- Admin dashboard
- User management

### 📊 Monitoring
- Login logs
- Password change logs
- Activity tracking
- Error logging

---

## 🎉 Selamat!

Anda sekarang punya sistem login LENGKAP yang siap untuk:
- ✅ Personal use
- ✅ Small project
- ✅ Startup
- ✅ Enterprise (dengan extension)

---

## 💡 Pro Tips

1. **Use Environment Variables** untuk sensitive data
2. **Enable HTTPS** untuk production
3. **Setup regular backups** dari database
4. **Monitor log files** untuk security issues
5. **Keep dependencies updated**
6. **Test thoroughly** sebelum production
7. **Document changes** untuk maintenance

---

## 🔗 Useful Links

- **PHP Documentation**: https://www.php.net/docs.php
- **MySQL Documentation**: https://dev.mysql.com/doc/
- **Apache Rewrite Guide**: https://httpd.apache.org/docs/current/mod/mod_rewrite.html
- **Security Best Practices**: https://owasp.org/www-project-top-ten/
- **CSS Reference**: https://developer.mozilla.org/en-US/docs/Web/CSS
- **JavaScript Guide**: https://developer.mozilla.org/en-US/docs/Web/JavaScript

---

## 📝 Changelog

### v1.7 (Current) ✅
- ✅ Complete login system
- ✅ User registration
- ✅ Password management
- ✅ User profile
- ✅ Watchlist feature
- ✅ Comprehensive documentation
- ✅ API testing guide
- ✅ Security features

---

## 🏆 What You Get

```
21 Files
├── 5 Frontend Files
├── 9 Backend Files
├── 1 Database File
├── 5 Documentation Files
└── 1 Configuration File

Features:
✅ Complete authentication system
✅ Secure password handling
✅ Session management
✅ User profiles
✅ Watchlist management
✅ Activity logging
✅ Rate limiting
✅ Comprehensive documentation
✅ API endpoints
✅ Testing guides
✅ Security best practices
✅ Responsive design
✅ Production-ready
✅ Free & open source

Time to setup: ~15 minutes
Time to understand: ~1 hour
Time to customize: ~1-2 hours
Time to production: ~1 day
```

---

## 🎯 Ready to Start?

**Follow steps di QUICK_START.md di atas untuk mulai dalam 5 menit!**

Or read **README.md** untuk dokumentasi lengkap.

---

**Happy Coding! 🚀**

*Dibuat dengan ❤️ untuk investor & developer Indonesia*
