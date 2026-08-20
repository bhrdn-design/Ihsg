<?php
/**
 * IHSG Screener - Forgot Password API
 * POST /api/forgot-password
 * Send password reset link to email
 */

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method tidak diizinkan', 405);
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || empty($data['email'])) {
    sendError('Email harus diisi', 400);
}

$email = sanitize($data['email']);

if (!isValidEmail($email)) {
    sendError('Format email tidak valid', 400);
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if email exists
    $stmt = $db->prepare("SELECT id, username FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        $stmt->close();
        // Don't reveal if email exists or not for security
        sendSuccess('Jika email terdaftar, link reset akan dikirim dalam beberapa menit', null, 200);
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Generate reset token
    $resetToken = generateToken(32);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Store reset token
    $insertStmt = $db->prepare("
        INSERT INTO password_resets (email, token, expires_at)
        VALUES (?, ?, ?)
    ");
    $insertStmt->bind_param("sss", $email, $resetToken, $expiresAt);
    $insertStmt->execute();
    $insertStmt->close();
    
    // Create reset link
    $resetLink = FRONTEND_URL . 'reset-password.html?token=' . $resetToken;
    
    // TODO: Send email with reset link
    // sendResetEmail($email, $user['username'], $resetLink);
    
    logActivity($user['id'], 'password_reset_requested', null);
    
    sendSuccess('Email reset password telah dikirim', null, 200);
    
} catch (Exception $e) {
    sendError('Kesalahan server: ' . $e->getMessage(), 500);
}

$db->close();

?>
