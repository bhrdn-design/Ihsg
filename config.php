<?php
/**
 * IHSG Screener - Config File
 * Konfigurasi database dan sistem
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Sesuaikan dengan password MySQL Anda
define('DB_NAME', 'ihsg_screener');
define('DB_PORT', 3306);

// Security Configuration
define('JWT_SECRET', 'your_super_secret_jwt_key_change_this_in_production_2024');
define('SESSION_TIMEOUT', 30 * 24 * 60 * 60); // 30 hari
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_MAX_LENGTH', 255);

// Email Configuration (untuk email verification & password reset)
define('MAIL_FROM', 'noreply@ihsgscreener.com');
define('MAIL_SMTP_HOST', 'smtp.gmail.com');
define('MAIL_SMTP_PORT', 587);
define('MAIL_SMTP_USER', 'your_email@gmail.com');
define('MAIL_SMTP_PASS', 'your_app_password');  // Gunakan app-specific password untuk Gmail

// API Configuration
define('API_BASE_URL', 'http://localhost/ihsg-screener/api/');
define('FRONTEND_URL', 'http://localhost/ihsg-screener/');
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_ATTEMPT_TIMEOUT', 15 * 60); // 15 menit

// Application Settings
define('APP_NAME', 'IHSG Screener');
define('APP_VERSION', '1.7');
define('APP_ENV', 'development'); // 'production' atau 'development'

// Error Reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Session Configuration
ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);
session_set_cookie_params([
    'lifetime' => SESSION_TIMEOUT,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CORS Configuration
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

/**
 * Database Connection Class
 */
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
            
            if ($this->conn->connect_error) {
                throw new Exception('Database Connection Failed: ' . $this->conn->connect_error);
            }
            
            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            die(json_encode(['error' => $e->getMessage()]));
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function __clone() {
        throw new Exception("Database singleton tidak bisa di-clone");
    }

    public function __wakeup() {
        throw new Exception("Database singleton tidak bisa di-unserialize");
    }
}

/**
 * Helper Functions
 */

// Sanitize input
function sanitize($input) {
    $db = Database::getInstance()->getConnection();
    return $db->real_escape_string(trim($input));
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Generate token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

// Send JSON response
function sendResponse($status, $message, $data = null, $code = 200) {
    http_response_code($code);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// Send error response
function sendError($message, $code = 400, $data = null) {
    sendResponse('error', $message, $data, $code);
}

// Send success response
function sendSuccess($message, $data = null, $code = 200) {
    sendResponse('success', $message, $data, $code);
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

// Get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . FRONTEND_URL . 'index.html');
        exit;
    }
}

// Validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validate password strength
function isStrongPassword($password) {
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return false;
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }
    return true;
}

// Log activity
function logActivity($userId, $action, $details = null) {
    try {
        $db = Database::getInstance()->getConnection();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $stmt = $db->prepare("
            INSERT INTO login_logs (user_id, ip_address, user_agent, login_status, login_time)
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        $stmt->bind_param("isss", $userId, $ip, $userAgent, $action);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        // Silent fail untuk logging
    }
}

?>
