<?php
/**
 * IHSG Screener - Register API
 * POST /api/register
 */

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method tidak diizinkan', 405);
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validasi input
$errors = [];

if (!isset($data['username']) || empty($data['username'])) {
    $errors[] = 'Username harus diisi';
}

if (!isset($data['email']) || empty($data['email'])) {
    $errors[] = 'Email harus diisi';
}

if (!isset($data['password']) || empty($data['password'])) {
    $errors[] = 'Password harus diisi';
}

if (!isset($data['confirm_password']) || empty($data['confirm_password'])) {
    $errors[] = 'Konfirmasi password harus diisi';
}

if (count($errors) > 0) {
    sendError('Validasi gagal: ' . implode(', ', $errors), 400);
}

$username = sanitize($data['username']);
$email = sanitize($data['email']);
$password = sanitize($data['password']);
$confirmPassword = sanitize($data['confirm_password']);

// Validasi format username
if (strlen($username) < 3 || strlen($username) > 30) {
    sendError('Username harus antara 3-30 karakter', 400);
}

if (!preg_match('/^[a-zA-Z0-9._-]+$/', $username)) {
    sendError('Username hanya boleh berisi huruf, angka, titik, underscore, dan dash', 400);
}

// Validasi email format
if (!isValidEmail($email)) {
    sendError('Format email tidak valid', 400);
}

// Validasi password
if (!isStrongPassword($password)) {
    sendError('Password minimal 8 karakter, harus mengandung huruf besar, huruf kecil, dan angka', 400);
}

// Validasi confirm password
if ($password !== $confirmPassword) {
    sendError('Password dan konfirmasi password tidak cocok', 400);
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if email already exists
    $checkStmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $checkStmt->close();
        sendError('Email sudah terdaftar', 409);
    }
    $checkStmt->close();
    
    // Check if username already exists
    $checkStmt = $db->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $checkStmt->close();
        sendError('Username sudah digunakan', 409);
    }
    $checkStmt->close();
    
    // Hash password
    $hashedPassword = hashPassword($password);
    
    // Generate verification code
    $verificationCode = generateToken(32);
    
    // Insert new user
    $insertStmt = $db->prepare("
        INSERT INTO users (username, email, password, verification_code, is_verified, created_at)
        VALUES (?, ?, ?, ?, FALSE, NOW())
    ");
    
    if (!$insertStmt) {
        throw new Exception('Database error: ' . $db->error);
    }
    
    $insertStmt->bind_param("ssss", $username, $email, $hashedPassword, $verificationCode);
    
    if (!$insertStmt->execute()) {
        throw new Exception('Gagal mendaftar: ' . $insertStmt->error);
    }
    
    $userId = $db->insert_id;
    $insertStmt->close();
    
    // TODO: Kirim email verification
    // sendVerificationEmail($email, $verificationCode);
    
    // Log activity
    logActivity($userId, 'user_registered', null);
    
    // Response
    sendSuccess('Pendaftaran berhasil! Silakan login', [
        'user_id' => $userId,
        'username' => $username,
        'email' => $email,
        'message' => 'Email verifikasi akan dikirim segera',
        'redirect' => FRONTEND_URL . 'index.html'
    ], 201);
    
} catch (Exception $e) {
    sendError('Kesalahan server: ' . $e->getMessage(), 500);
}

$db->close();

?>
