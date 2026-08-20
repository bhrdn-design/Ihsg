# ✅ Complete Setup Checklist - IHSG Screener Login System

Gunakan checklist ini untuk memastikan semua telah dikonfigurasi dengan benar.

---

## 📦 File & Struktur

### Frontend Files
- [ ] `index.html` - Login & Register page (tersedia)
- [ ] `dashboard.html` - Dashboard setelah login (tersedia)
- [ ] `styles.css` - CSS styling (tersedia)
- [ ] `main.js` - JavaScript untuk login page (tersedia)
- [ ] `dashboard.js` - JavaScript untuk dashboard (tersedia)

### Backend Files
- [ ] `config.php` - Konfigurasi database (tersedia)
- [ ] `api_login.php` - API login endpoint (tersedia)
- [ ] `api_register.php` - API register endpoint (tersedia)
- [ ] `api_logout.php` - API logout endpoint (tersedia)
- [ ] `api_auth_check.php` - API auth check (tersedia)
- [ ] `api_forgot_password.php` - API forgot password (tersedia)
- [ ] `api_user_profile.php` - API user profile (tersedia)
- [ ] `api_change_password.php` - API change password (tersedia)
- [ ] `api_watchlist.php` - API watchlist management (tersedia)

### Configuration Files
- [ ] `database_schema.sql` - Database schema (tersedia)
- [ ] `.htaccess` - Apache configuration (tersedia)
- [ ] `package.json` - Node dependencies (tersedia)
- [ ] `README.md` - Main documentation (tersedia)
- [ ] `SETUP_INSTRUCTIONS.md` - Setup guide (tersedia)

---

## 🔧 Installation Checklist

### Step 1: System Requirements
- [ ] PHP 7.4+ installed
- [ ] MySQL 5.7+ installed
- [ ] Apache or Nginx web server
- [ ] Curl/wget untuk testing API

### Step 2: Database Setup
- [ ] MySQL server running
- [ ] Database `ihsg_screener` created
- [ ] Database user `ihsg_user` created
- [ ] Database schema imported from `database_schema.sql`
- [ ] All 5 tables created:
  - [ ] `users`
  - [ ] `watchlist`
  - [ ] `saved_filters`
  - [ ] `password_resets`
  - [ ] `login_logs`

Verify dengan:
```bash
mysql -u ihsg_user -p -D ihsg_screener -e "SHOW TABLES;"
```

### Step 3: File Structure
- [ ] All files copied to web root
- [ ] File permissions set correctly:
  ```bash
  chmod 755 /var/www/html/ihsg-screener
  chmod 644 /var/www/html/ihsg-screener/*.{php,html,css,js}
  ```
- [ ] `.htaccess` file in place (jika menggunakan Apache)

### Step 4: Configuration
- [ ] `config.php` updated dengan:
  - [ ] `DB_HOST` = correct host
  - [ ] `DB_USER` = correct user
  - [ ] `DB_PASS` = correct password
  - [ ] `DB_NAME` = `ihsg_screener`
  - [ ] `API_BASE_URL` = correct URL
  - [ ] `FRONTEND_URL` = correct URL
  - [ ] `JWT_SECRET` = strong secret key
  - [ ] `APP_ENV` = `development` (or `production`)

- [ ] `main.js` & `dashboard.js` updated dengan:
  - [ ] `API_BASE` = correct API base URL
  - [ ] All API endpoints point to correct files

### Step 5: Web Server
- [ ] Apache/Nginx running
- [ ] PHP module enabled (mod_php / php-fpm)
- [ ] `mod_rewrite` enabled (Apache)
- [ ] `.htaccess` respected (Apache)
- [ ] CORS headers configured (if needed)

---

## 🧪 Testing Checklist

### Access & Basic Functionality
- [ ] Can access `http://localhost/ihsg-screener/index.html`
- [ ] Login page displays correctly
- [ ] Register form visible
- [ ] Forgot password link works
- [ ] Switch between login/register forms works
- [ ] Password visibility toggle works

### Registration
- [ ] Can fill registration form
- [ ] Username validation works:
  - [ ] Min 3 characters required
  - [ ] No special characters allowed
  - [ ] Prevents duplicate username
- [ ] Email validation works:
  - [ ] Valid email format required
  - [ ] Prevents duplicate email
- [ ] Password validation works:
  - [ ] Min 8 characters required
  - [ ] Must have uppercase letter
  - [ ] Must have lowercase letter
  - [ ] Must have number
- [ ] Password confirmation matches
- [ ] Can register new account successfully
- [ ] Success message displayed
- [ ] After register, can login with new account

### Login
- [ ] Can fill login form
- [ ] Email validation works
- [ ] Invalid credentials show error
- [ ] Valid credentials allow login
- [ ] Remember me checkbox works
- [ ] Session created after successful login
- [ ] Redirects to dashboard.html

### Dashboard
- [ ] User information displayed correctly
- [ ] User avatar shows correct initial
- [ ] Logout button visible & functional
- [ ] All feature cards displayed
- [ ] Navigation menu works

### Logout
- [ ] Logout button visible
- [ ] Clicking logout confirms action
- [ ] Session destroyed
- [ ] Redirects to login page
- [ ] Cannot access dashboard after logout (403/401)

### API Testing
Test dengan curl atau Postman:

```bash
# Test Register
curl -X POST http://localhost/ihsg-screener/api_register.php \
  -H "Content-Type: application/json" \
  -d '{
    "username":"testuser",
    "email":"test@example.com",
    "password":"TestPass123",
    "confirm_password":"TestPass123"
  }'

# Response should be: {"status":"success",...}
```

```bash
# Test Login
curl -X POST http://localhost/ihsg-screener/api_login.php \
  -H "Content-Type: application/json" \
  -d '{
    "email":"test@example.com",
    "password":"TestPass123",
    "remember_me":false
  }'

# Response should be: {"status":"success","data":{...}}
```

```bash
# Test Auth Check (requires valid session)
curl http://localhost/ihsg-screener/api_auth_check.php

# Response: {"status":"success",...} or {"status":"error",...}
```

---

## 🔐 Security Checklist

### Password Security
- [ ] Passwords hashed with bcrypt
- [ ] Min password length enforced (8 characters)
- [ ] Password strength requirements enforced
- [ ] Password confirmation required on register
- [ ] Current password verified on change password
- [ ] Passwords never logged or stored in plain text

### Session Security
- [ ] Sessions using secure cookies
- [ ] HttpOnly flag set on cookies
- [ ] Session timeout configured (30 days)
- [ ] Session regeneration on login
- [ ] Proper session destruction on logout

### Input Security
- [ ] All inputs sanitized with `sanitize()`
- [ ] SQL Injection prevention (prepared statements)
- [ ] XSS prevention (HTML escaping)
- [ ] CSRF tokens implemented (if needed)
- [ ] Rate limiting on login attempts
- [ ] Max 5 login attempts per 15 minutes

### Database Security
- [ ] Strong database password set
- [ ] Database user has minimal privileges
- [ ] No root password in config
- [ ] Backups created regularly
- [ ] Database access restricted to localhost

### Application Security
- [ ] Debug mode disabled in production
- [ ] Error messages don't reveal sensitive info
- [ ] Sensitive files protected (.env, config with DB pass)
- [ ] HTTPS enabled in production
- [ ] Security headers configured
- [ ] CORS properly configured

### Activity Logging
- [ ] Login attempts logged (success & failure)
- [ ] Password changes logged
- [ ] Account creations logged
- [ ] Logout events logged
- [ ] Failed login attempts tracked
- [ ] Suspicious activities flagged

---

## 📊 Database Verification

### Users Table
```sql
SELECT * FROM users LIMIT 1;
```
Should have:
- [ ] `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- [ ] `username` (VARCHAR, UNIQUE)
- [ ] `email` (VARCHAR, UNIQUE)
- [ ] `password` (VARCHAR, hashed)
- [ ] `full_name` (VARCHAR)
- [ ] `phone` (VARCHAR)
- [ ] `profile_picture` (VARCHAR)
- [ ] `is_verified` (BOOLEAN)
- [ ] `verification_code` (VARCHAR)
- [ ] `last_login` (TIMESTAMP)
- [ ] `created_at` (TIMESTAMP)
- [ ] `updated_at` (TIMESTAMP)
- [ ] `is_active` (BOOLEAN)

### Watchlist Table
- [ ] Properly linked to users table via `user_id`
- [ ] `stock_code` unique per user
- [ ] `entry_price` stored correctly
- [ ] `added_at` timestamp recorded

### Login Logs Table
- [ ] Records all login attempts
- [ ] Tracks IP addresses
- [ ] Tracks user agents
- [ ] Records status (success/failed)
- [ ] Timestamps accurate

### Password Resets Table
- [ ] Contains reset tokens
- [ ] Tokens expire after 1 hour
- [ ] Linked to email addresses

---

## 📈 Performance Checklist

### Database Performance
- [ ] Indexes created on `email`, `username` columns
- [ ] Queries optimized with prepared statements
- [ ] No N+1 query problems
- [ ] Slow queries logged (if applicable)

### Frontend Performance
- [ ] CSS minified (or acceptable size)
- [ ] JavaScript minified (optional)
- [ ] Images optimized
- [ ] No render-blocking resources
- [ ] Page loads in < 2 seconds

### Server Performance
- [ ] Apache/Nginx configured for concurrency
- [ ] PHP memory limit appropriate (>=128MB)
- [ ] Upload size limits configured
- [ ] Session garbage collection enabled

---

## 🌍 Deployment Checklist

### Pre-Deployment
- [ ] All tests passed locally
- [ ] No debug information in config
- [ ] All API endpoints working
- [ ] Database backups created
- [ ] Environment variables documented

### Production Deployment
- [ ] HTTPS/SSL certificate installed
- [ ] Database credentials changed
- [ ] JWT_SECRET changed to random string
- [ ] APP_ENV set to `production`
- [ ] Error reporting disabled
- [ ] Email configured for notifications
- [ ] Backup strategy implemented
- [ ] Monitoring/logging configured
- [ ] Firewall rules configured
- [ ] Rate limiting increased for production

### Post-Deployment
- [ ] Monitor error logs
- [ ] Check database performance
- [ ] Verify backups working
- [ ] Test email functionality
- [ ] Monitor user signups
- [ ] Check security logs

---

## 🆘 Troubleshooting Verification

### If Login Not Working
- [ ] PHP session extension enabled: `php -m | grep session`
- [ ] Session save path writable: `ls -l /var/lib/php/sessions/`
- [ ] Database connection working: check config.php
- [ ] Table exists: `SHOW TABLES;`
- [ ] User exists in database: check with SELECT query

### If Redirect Loop
- [ ] Check api_auth_check.php returns correct response
- [ ] Session data exists: check $SESSION
- [ ] Cookie settings correct in config.php
- [ ] Browser cookies enabled

### If 404 Errors
- [ ] Files exist in correct location
- [ ] File permissions correct (chmod 644)
- [ ] .htaccess in place and readable
- [ ] Apache rewrite module enabled
- [ ] Apache AllowOverride All configured

### If Database Errors
- [ ] MySQL service running: `systemctl status mysql`
- [ ] Database exists: `SHOW DATABASES;`
- [ ] User has privileges: `SHOW GRANTS;`
- [ ] Schema imported: `SHOW TABLES;`

---

## 📝 Documentation Checklist

- [ ] README.md read and understood
- [ ] SETUP_INSTRUCTIONS.md followed
- [ ] All API endpoints documented
- [ ] Database schema documented
- [ ] Configuration options documented
- [ ] Security practices documented
- [ ] Deployment procedures documented

---

## 🎯 Final Testing

### Complete User Flow Test

1. **Registration Flow**
   - [ ] Access login page
   - [ ] Click "Daftar"
   - [ ] Fill all registration fields correctly
   - [ ] Click "Buat Akun"
   - [ ] See success message
   - [ ] Auto-switch to login form

2. **Login Flow**
   - [ ] Enter email from registered account
   - [ ] Enter password
   - [ ] Click "Masuk ke Screener"
   - [ ] Redirected to dashboard
   - [ ] User information displayed

3. **Dashboard Flow**
   - [ ] View user profile
   - [ ] See welcome message
   - [ ] Feature cards displayed
   - [ ] Stats visible

4. **Logout Flow**
   - [ ] Click logout button
   - [ ] Confirm logout
   - [ ] Redirected to login
   - [ ] Cannot access dashboard

---

## ✨ Success Indicators

- [x] All checklist items completed
- [x] All tests passed
- [x] No console errors
- [x] No database errors
- [x] User can register & login
- [x] Session persists
- [x] Logout clears session
- [x] Password reset works
- [x] User profile accessible
- [x] Watchlist functional

---

## 🎉 Deployment Ready!

If all items are checked, your IHSG Screener login system is ready for deployment!

### Next Steps

1. **Deploy to Production**
   - Follow SETUP_INSTRUCTIONS.md for your OS
   - Configure HTTPS/SSL
   - Update security settings

2. **Monitor & Maintain**
   - Check error logs regularly
   - Monitor database performance
   - Review access logs
   - Update dependencies

3. **Backup & Recovery**
   - Schedule regular backups
   - Test restore procedures
   - Document recovery steps

4. **Enhance Features**
   - Add email verification
   - Implement 2FA
   - Add OAuth providers
   - Create admin panel

---

**🚀 Happy Deployment!**

---

## 📞 Support Resources

- **Documentation**: README.md
- **Setup Guide**: SETUP_INSTRUCTIONS.md
- **Code Issues**: Check error logs in `/var/log/apache2/` or `/var/log/php.log`
- **Database Issues**: Check MySQL error log
- **Browser Console**: Check browser console for JavaScript errors (F12)

---

**Dibuat dengan ❤️ untuk investor Indonesia**

*Happy Screening & Investing!*
