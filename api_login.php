<?php
/**
 * IHSG Screener - Login API
 * POST /api/login
 */

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method tidak diizinkan', 405);
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validasi input
if (!isset($data['email']) || !isset($data['password'])) {
    sendError('Email dan password harus diisi', 400);
}

$email = sanitize($data['email']);
$password = sanitize($data['password']);
$rememberMe = $data['remember_me'] ?? false;

// Validasi email format
if (!isValidEmail($email)) {
    sendError('Format email tidak valid', 400);
}

// Validasi password tidak kosong
if (empty($password)) {
    sendError('Password tidak boleh kosong', 400);
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Check login attempts (rate limiting)
    $ip = $_SERVER['REMOTE_ADDR'];
    $attemptKey = "login_attempts_{$ip}";
    $lastAttemptKey = "last_login_attempt_{$ip}";
    
    if (isset($_SESSION[$attemptKey]) && $_SESSION[$attemptKey] >= MAX_LOGIN_ATTEMPTS) {
        $lastAttempt = $_SESSION[$lastAttemptKey] ?? 0;
        if (time() - $lastAttempt < LOGIN_ATTEMPT_TIMEOUT) {
            sendError('Terlalu banyak percobaan login. Coba lagi dalam 15 menit', 429);
        } else {
            $_SESSION[$attemptKey] = 0;
        }
    }
    
    // Query user by email
    $stmt = $db->prepare("
        SELECT id, username, email, password, is_verified, is_active, full_name, profile_picture
        FROM users 
        WHERE email = ? 
        LIMIT 1
    ");
    
    if (!$stmt) {
        throw new Exception('Database error: ' . $db->error);
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        // Increment attempt counter
        $_SESSION[$attemptKey] = ($_SESSION[$attemptKey] ?? 0) + 1;
        $_SESSION[$lastAttemptKey] = time();
        
        logActivity(null, 'failed_login', "Email: $email");
        sendError('Email atau password salah', 401);
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Check if user is active
    if (!$user['is_active']) {
        logActivity($user['id'], 'blocked_login', null);
        sendError('Akun Anda telah diblokir. Hubungi support', 403);
    }
    
    // Verify password
    if (!verifyPassword($password, $user['password'])) {
        // Increment attempt counter
        $_SESSION[$attemptKey] = ($_SESSION[$attemptKey] ?? 0) + 1;
        $_SESSION[$lastAttemptKey] = time();
        
        logActivity($user['id'], 'failed_login', null);
        sendError('Email atau password salah', 401);
    }
    
    // Reset login attempts
    unset($_SESSION[$attemptKey]);
    unset($_SESSION[$lastAttemptKey]);
    
    // Update last login time
    $updateStmt = $db->prepare("
        UPDATE users 
        SET last_login = NOW() 
        WHERE id = ?
    ");
    $updateStmt->bind_param("i", $user['id']);
    $updateStmt->execute();
    $updateStmt->close();
    
    // Log successful login
    logActivity($user['id'], 'success_login', null);
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['profile_picture'] = $user['profile_picture'];
    $_SESSION['login_time'] = time();
    
    // Handle "Remember Me" - set longer cookie
    if ($rememberMe) {
        $_SESSION['remember_me'] = true;
        $_SESSION['remember_me_time'] = time();
        setcookie('user_token', generateToken(32), time() + (30 * 24 * 60 * 60), '/');
    }
    
    // Response
    sendSuccess('Login berhasil', [
        'user_id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'full_name' => $user['full_name'],
        'profile_picture' => $user['profile_picture'],
        'is_verified' => (bool)$user['is_verified'],
        'redirect' => FRONTEND_URL . 'dashboard.html'
    ], 200);
    
} catch (Exception $e) {
    sendError('Kesalahan server: ' . $e->getMessage(), 500);
}

$db->close();

?>
