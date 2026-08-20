# 🧪 API Testing Guide - IHSG Screener

Panduan lengkap untuk testing semua API endpoints dengan berbagai tools.

---

## 🛠️ Testing Tools

### Recommended Tools
1. **Postman** - https://www.postman.com/downloads/
2. **Insomnia** - https://insomnia.rest/
3. **curl** (command line)
4. **Thunder Client** - VS Code extension
5. **REST Client** - VS Code extension

### Install curl (jika belum ada)

**Windows:**
```bash
# curl sudah include di Windows 10+
# Verify:
curl --version
```

**Linux:**
```bash
sudo apt update
sudo apt install curl
```

**macOS:**
```bash
brew install curl
```

---

## 📚 API Endpoints

### Base URL
```
http://localhost/ihsg-screener/
```

---

## ✍️ API_REGISTER - Register User

### Endpoint
```
POST /api_register.php
```

### Request Headers
```
Content-Type: application/json
```

### Request Body
```json
{
  "username": "testuser",
  "email": "test@example.com",
  "password": "TestPassword123",
  "confirm_password": "TestPassword123"
}
```

### Testing dengan curl

```bash
curl -X POST http://localhost/ihsg-screener/api_register.php \
  -H "Content-Type: application/json" \
  -d '{
    "username": "testuser",
    "email": "test@example.com",
    "password": "TestPassword123",
    "confirm_password": "TestPassword123"
  }'
```

### Testing dengan Postman

1. New > Request
2. Method: POST
3. URL: http://localhost/ihsg-screener/api_register.php
4. Headers:
   - Key: Content-Type
   - Value: application/json
5. Body > raw > JSON:
   ```json
   {
     "username": "testuser",
     "email": "test@example.com",
     "password": "TestPassword123",
     "confirm_password": "TestPassword123"
   }
   ```
6. Send

### Success Response (201)
```json
{
  "status": "success",
  "message": "Pendaftaran berhasil! Silakan login",
  "data": {
    "user_id": 1,
    "username": "testuser",
    "email": "test@example.com",
    "message": "Email verifikasi akan dikirim segera",
    "redirect": "http://localhost/ihsg-screener/index.html"
  },
  "timestamp": "2024-03-21 10:30:00"
}
```

### Error Response Examples

**Missing Fields (400)**
```json
{
  "status": "error",
  "message": "Validasi gagal: Email harus diisi, Password harus diisi",
  "data": null,
  "timestamp": "2024-03-21 10:30:00"
}
```

**Weak Password (400)**
```json
{
  "status": "error",
  "message": "Password minimal 8 karakter, harus ada huruf besar, huruf kecil, dan angka",
  "data": null,
  "timestamp": "2024-03-21 10:30:00"
}
```

**Duplicate Email (409)**
```json
{
  "status": "error",
  "message": "Email sudah terdaftar",
  "data": null,
  "timestamp": "2024-03-21 10:30:00"
}
```

---

## 🔑 API_LOGIN - Login User

### Endpoint
```
POST /api_login.php
```

### Request Headers
```
Content-Type: application/json
```

### Request Body
```json
{
  "email": "test@example.com",
  "password": "TestPassword123",
  "remember_me": false
}
```

### Testing dengan curl

```bash
curl -X POST http://localhost/ihsg-screener/api_login.php \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "TestPassword123",
    "remember_me": false
  }' \
  -c cookies.txt
```

Note: `-c cookies.txt` saves cookies untuk persistent session

### Success Response (200)
```json
{
  "status": "success",
  "message": "Login berhasil",
  "data": {
    "user_id": 1,
    "username": "testuser",
    "email": "test@example.com",
    "full_name": "Test User",
    "profile_picture": null,
    "is_verified": false,
    "redirect": "http://localhost/ihsg-screener/dashboard.html"
  },
  "timestamp": "2024-03-21 10:30:00"
}
```

### Error Response Examples

**Invalid Credentials (401)**
```json
{
  "status": "error",
  "message": "Email atau password salah",
  "data": null,
  "timestamp": "2024-03-21 10:30:00"
}
```

**Too Many Attempts (429)**
```json
{
  "status": "error",
  "message": "Terlalu banyak percobaan login. Coba lagi dalam 15 menit",
  "data": null,
  "timestamp": "2024-03-21 10:30:00"
}
```

---

## 🔍 API_AUTH_CHECK - Check Authentication

### Endpoint
```
GET /api_auth_check.php
```

### Testing dengan curl

```bash
# Without session
curl http://localhost/ihsg-screener/api_auth_check.php

# With saved cookies
curl http://localhost/ihsg-screener/api_auth_check.php \
  -b cookies.txt
```

### Success Response (200)
```json
{
  "status": "success",
  "message": "User terautentikasi",
  "data": {
    "user_id": 1,
    "username": "testuser",
    "email": "test@example.com",
    "full_name": "Test User",
    "profile_picture": null,
    "is_verified": false,
    "last_login": "2024-03-21 10:30:00",
    "is_authenticated": true
  },
  "timestamp": "2024-03-21 10:30:00"
}
```

### Error Response (401)
```json
{
  "status": "error",
  "message": "Tidak ada sesi login",
  "data": null,
  "timestamp": "2024-03-21 10:30:00"
}
```

---

## 🚪 API_LOGOUT - Logout User

### Endpoint
```
POST /api_logout.php
```

### Request Headers
```
Content-Type: application/json
```

### Testing dengan curl

```bash
curl -X POST http://localhost/ihsg-screener/api_logout.php \
  -H "Content-Type: application/json" \
  -b cookies.txt
```

### Success Response (200)
```json
{
  "status": "success",
  "message": "Logout berhasil",
  "data": {
    "redirect": "http://localhost/ihsg-screener/index.html"
  },
  "timestamp": "2024-03-21 10:30:00"
}
```

---

## 👤 API_USER_PROFILE - Get User Profile

### Endpoint
```
GET /api_user_profile.php
```

### Testing dengan curl

```bash
curl http://localhost/ihsg-screener/api_user_profile.php \
  -b cookies.txt
```

### Success Response (200)
```json
{
  "status": "success",
  "message": "Profil user berhasil diambil",
  "data": {
    "id": 1,
    "username": "testuser",
    "email": "test@example.com",
    "full_name": "Test User",
    "phone": "081234567890",
    "profile_picture": "path/to/picture.jpg",
    "is_verified": false,
    "last_login": "2024-03-21 10:30:00",
    "created_at": "2024-03-21 10:00:00",
    "updated_at": "2024-03-21 10:30:00"
  },
  "timestamp": "2024-03-21 10:30:00"
}
```

---

## ✏️ API_USER_PROFILE - Update User Profile

### Endpoint
```
PUT /api_user_profile.php
```

### Request Body
```json
{
  "full_name": "Test User Updated",
  "phone": "081234567890"
}
```

### Testing dengan curl

```bash
curl -X PUT http://localhost/ihsg-screener/api_user_profile.php \
  -H "Content-Type: application/json" \
  -d '{
    "full_name": "Test User Updated",
    "phone": "081234567890"
  }' \
  -b cookies.txt
```

### Success Response (200)
```json
{
  "status": "success",
  "message": "Profil berhasil diupdate",
  "data": {
    "full_name": "Test User Updated",
    "phone": "081234567890",
    "updated_at": "2024-03-21 10:35:00"
  },
  "timestamp": "2024-03-21 10:35:00"
}
```

---

## 🔐 API_CHANGE_PASSWORD - Change Password

### Endpoint
```
POST /api_change_password.php
```

### Request Body
```json
{
  "current_password": "TestPassword123",
  "new_password": "NewPassword456",
  "confirm_password": "NewPassword456"
}
```

### Testing dengan curl

```bash
curl -X POST http://localhost/ihsg-screener/api_change_password.php \
  -H "Content-Type: application/json" \
  -d '{
    "current_password": "TestPassword123",
    "new_password": "NewPassword456",
    "confirm_password": "NewPassword456"
  }' \
  -b cookies.txt
```

### Success Response (200)
```json
{
  "status": "success",
  "message": "Password berhasil diubah",
  "data": {
    "message": "Password Anda telah berhasil diubah"
  },
  "timestamp": "2024-03-21 10:35:00"
}
```

### Error Examples

**Wrong Current Password (401)**
```json
{
  "status": "error",
  "message": "Password saat ini tidak sesuai",
  "data": null,
  "timestamp": "2024-03-21 10:35:00"
}
```

**Weak New Password (400)**
```json
{
  "status": "error",
  "message": "Password baru minimal 8 karakter, harus ada huruf besar, kecil, dan angka",
  "data": null,
  "timestamp": "2024-03-21 10:35:00"
}
```

---

## ⭐ API_WATCHLIST - Get Watchlist

### Endpoint
```
GET /api_watchlist.php
```

### Testing dengan curl

```bash
curl http://localhost/ihsg-screener/api_watchlist.php \
  -b cookies.txt
```

### Success Response (200)
```json
{
  "status": "success",
  "message": "Watchlist berhasil diambil",
  "data": {
    "count": 2,
    "items": [
      {
        "id": 1,
        "stock_code": "BBCA",
        "stock_name": "Bank Central Asia",
        "entry_price": 9825,
        "added_at": "2024-03-21 10:30:00"
      },
      {
        "id": 2,
        "stock_code": "BMRI",
        "stock_name": "Bank Mandiri",
        "entry_price": 5425,
        "added_at": "2024-03-21 10:35:00"
      }
    ]
  },
  "timestamp": "2024-03-21 10:35:00"
}
```

---

## ➕ API_WATCHLIST - Add to Watchlist

### Endpoint
```
POST /api_watchlist.php
```

### Request Body
```json
{
  "stock_code": "BBCA",
  "stock_name": "Bank Central Asia",
  "entry_price": 9825
}
```

### Testing dengan curl

```bash
curl -X POST http://localhost/ihsg-screener/api_watchlist.php \
  -H "Content-Type: application/json" \
  -d '{
    "stock_code": "BBCA",
    "stock_name": "Bank Central Asia",
    "entry_price": 9825
  }' \
  -b cookies.txt
```

### Success Response (201)
```json
{
  "status": "success",
  "message": "Saham berhasil ditambahkan ke watchlist",
  "data": {
    "id": 1,
    "stock_code": "BBCA",
    "stock_name": "Bank Central Asia",
    "entry_price": 9825,
    "added_at": "2024-03-21 10:35:00"
  },
  "timestamp": "2024-03-21 10:35:00"
}
```

### Error Examples

**Duplicate Stock (409)**
```json
{
  "status": "error",
  "message": "Saham sudah ada di watchlist Anda",
  "data": null,
  "timestamp": "2024-03-21 10:35:00"
}
```

---

## ❌ API_WATCHLIST - Remove from Watchlist

### Endpoint
```
DELETE /api_watchlist.php
```

### Request Body
```json
{
  "stock_code": "BBCA"
}
```

### Testing dengan curl

```bash
curl -X DELETE http://localhost/ihsg-screener/api_watchlist.php \
  -H "Content-Type: application/json" \
  -d '{
    "stock_code": "BBCA"
  }' \
  -b cookies.txt
```

### Success Response (200)
```json
{
  "status": "success",
  "message": "Saham berhasil dihapus dari watchlist",
  "data": {
    "stock_code": "BBCA"
  },
  "timestamp": "2024-03-21 10:35:00"
}
```

---

## 🧬 Postman Collection

### Export Collection untuk diimpor di Postman

```json
{
  "info": {
    "name": "IHSG Screener API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Register",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\"username\":\"testuser\",\"email\":\"test@example.com\",\"password\":\"TestPassword123\",\"confirm_password\":\"TestPassword123\"}"
        },
        "url": {
          "raw": "http://localhost/ihsg-screener/api_register.php",
          "protocol": "http",
          "host": ["localhost"],
          "path": ["ihsg-screener", "api_register.php"]
        }
      }
    },
    {
      "name": "Login",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\"email\":\"test@example.com\",\"password\":\"TestPassword123\",\"remember_me\":false}"
        },
        "url": {
          "raw": "http://localhost/ihsg-screener/api_login.php",
          "protocol": "http",
          "host": ["localhost"],
          "path": ["ihsg-screener", "api_login.php"]
        }
      }
    },
    {
      "name": "Auth Check",
      "request": {
        "method": "GET",
        "url": {
          "raw": "http://localhost/ihsg-screener/api_auth_check.php",
          "protocol": "http",
          "host": ["localhost"],
          "path": ["ihsg-screener", "api_auth_check.php"]
        }
      }
    },
    {
      "name": "Get Profile",
      "request": {
        "method": "GET",
        "url": {
          "raw": "http://localhost/ihsg-screener/api_user_profile.php",
          "protocol": "http",
          "host": ["localhost"],
          "path": ["ihsg-screener", "api_user_profile.php"]
        }
      }
    },
    {
      "name": "Update Profile",
      "request": {
        "method": "PUT",
        "header": [
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\"full_name\":\"Test User\",\"phone\":\"081234567890\"}"
        },
        "url": {
          "raw": "http://localhost/ihsg-screener/api_user_profile.php",
          "protocol": "http",
          "host": ["localhost"],
          "path": ["ihsg-screener", "api_user_profile.php"]
        }
      }
    },
    {
      "name": "Change Password",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\"current_password\":\"TestPassword123\",\"new_password\":\"NewPassword456\",\"confirm_password\":\"NewPassword456\"}"
        },
        "url": {
          "raw": "http://localhost/ihsg-screener/api_change_password.php",
          "protocol": "http",
          "host": ["localhost"],
          "path": ["ihsg-screener", "api_change_password.php"]
        }
      }
    },
    {
      "name": "Get Watchlist",
      "request": {
        "method": "GET",
        "url": {
          "raw": "http://localhost/ihsg-screener/api_watchlist.php",
          "protocol": "http",
          "host": ["localhost"],
          "path": ["ihsg-screener", "api_watchlist.php"]
        }
      }
    },
    {
      "name": "Add to Watchlist",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\"stock_code\":\"BBCA\",\"stock_name\":\"Bank Central Asia\",\"entry_price\":9825}"
        },
        "url": {
          "raw": "http://localhost/ihsg-screener/api_watchlist.php",
          "protocol": "http",
          "host": ["localhost"],
          "path": ["ihsg-screener", "api_watchlist.php"]
        }
      }
    },
    {
      "name": "Remove from Watchlist",
      "request": {
        "method": "DELETE",
        "header": [
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\"stock_code\":\"BBCA\"}"
        },
        "url": {
          "raw": "http://localhost/ihsg-screener/api_watchlist.php",
          "protocol": "http",
          "host": ["localhost"],
          "path": ["ihsg-screener", "api_watchlist.php"]
        }
      }
    },
    {
      "name": "Logout",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "url": {
          "raw": "http://localhost/ihsg-screener/api_logout.php",
          "protocol": "http",
          "host": ["localhost"],
          "path": ["ihsg-screener", "api_logout.php"]
        }
      }
    }
  ]
}
```

---

## 📊 Testing Checklist

### Authentication Flow
- [ ] Register dengan data valid
- [ ] Register dengan email duplikat (error 409)
- [ ] Register dengan password lemah (error 400)
- [ ] Login dengan credentials benar
- [ ] Login dengan password salah (error 401)
- [ ] Login dengan email tidak terdaftar (error 401)
- [ ] Auth check tanpa session (error 401)
- [ ] Auth check dengan session valid (200)
- [ ] Logout berhasil

### User Profile
- [ ] Get profile tanpa login (error 401)
- [ ] Get profile dengan session valid (200)
- [ ] Update profile dengan data valid (200)
- [ ] Update profile dengan phone invalid (error 400)

### Password Management
- [ ] Change password dengan current password salah (error 401)
- [ ] Change password dengan password lemah (error 400)
- [ ] Change password berhasil dengan password baru strong
- [ ] Login dengan password lama (error 401)
- [ ] Login dengan password baru (200)

### Watchlist
- [ ] Get watchlist kosong (200, empty array)
- [ ] Add saham ke watchlist (201)
- [ ] Get watchlist dengan 1 item (200)
- [ ] Add saham duplikat (error 409)
- [ ] Remove saham dari watchlist (200)
- [ ] Get watchlist kembali kosong (200)

---

## 🐛 Debugging Tips

### Enable Debug Mode
Update di `config.php`:
```php
define('APP_ENV', 'development');
```

### View Raw Response
```bash
curl -i http://localhost/ihsg-screener/api_auth_check.php
```

### Save Full Response
```bash
curl http://localhost/ihsg-screener/api_auth_check.php > response.json
cat response.json | jq . # Pretty print
```

### Check Request Headers
```bash
curl -v http://localhost/ihsg-screener/api_auth_check.php 2>&1 | grep -A 10 "^>"
```

---

## 📈 Performance Testing

### Load Testing dengan Apache Bench

```bash
# Test homepage load
ab -n 1000 -c 10 http://localhost/ihsg-screener/index.html

# Test API endpoint
ab -n 1000 -c 10 http://localhost/ihsg-screener/api_auth_check.php
```

### Stress Testing

```bash
# Dengan curl
for i in {1..100}; do
  curl -X POST http://localhost/ihsg-screener/api_register.php \
    -H "Content-Type: application/json" \
    -d '{"username":"user'$i'","email":"user'$i'@test.com","password":"Test12345","confirm_password":"Test12345"}'
done
```

---

## ✅ Best Practices

1. **Always validate input** - Check required fields
2. **Test error cases** - Not just happy path
3. **Use consistent format** - JSON for all APIs
4. **Check status codes** - Verify correct HTTP status
5. **Monitor response time** - Should be < 200ms
6. **Test with tools** - Postman, curl, Insomnia
7. **Document responses** - Keep API docs updated
8. **Test security** - SQL injection, XSS, etc.

---

**Happy Testing! 🚀**
