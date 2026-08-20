<?php
/**
 * IHSG Screener - Change Password API
 * POST /api/user/change-password
 */

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method tidak diizinkan', 405);
}

// Require authentication
if (!isLoggedIn()) {
    sendError('Tidak ada sesi login', 401);
}

$data = json_decode(file_get_contents("php://input"), true);
$userId = getCurrentUserId();

// Validate input
if (!isset($data['current_password']) || empty($data['current_password'])) {
    sendError('Password saat ini harus diisi', 400);
}

if (!isset($data['new_password']) || empty($data['new_password'])) {
    sendError('Password baru harus diisi', 400);
}

if (!isset($data['confirm_password']) || empty($data['confirm_password'])) {
    sendError('Konfirmasi password harus diisi', 400);
}

$currentPassword = sanitize($data['current_password']);
$newPassword = sanitize($data['new_password']);
$confirmPassword = sanitize($data['confirm_password']);

// Validate new password
if (!isStrongPassword($newPassword)) {
    sendError('Password baru minimal 8 karakter, harus ada huruf besar, kecil, dan angka', 400);
}

// Validate password match
if ($newPassword !== $confirmPassword) {
    sendError('Password baru dan konfirmasi tidak cocok', 400);
}

// Prevent same password
if ($currentPassword === $newPassword) {
    sendError('Password baru tidak boleh sama dengan password lama', 400);
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Get current user password
    $stmt = $db->prepare("SELECT password FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        $stmt->close();
        sendError('User tidak ditemukan', 404);
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Verify current password
    if (!verifyPassword($currentPassword, $user['password'])) {
        logActivity($userId, 'failed_password_change', 'Wrong current password');
        sendError('Password saat ini tidak sesuai', 401);
    }
    
    // Hash new password
    $hashedPassword = hashPassword($newPassword);
    
    // Update password
    $updateStmt = $db->prepare("
        UPDATE users 
        SET password = ?, updated_at = NOW()
        WHERE id = ?
    ");
    
    $updateStmt->bind_param("si", $hashedPassword, $userId);
    
    if (!$updateStmt->execute()) {
        $updateStmt->close();
        sendError('Gagal mengubah password', 500);
    }
    
    $updateStmt->close();
    
    logActivity($userId, 'password_changed', null);
    
    sendSuccess('Password berhasil diubah', [
        'message' => 'Password Anda telah berhasil diubah'
    ], 200);
    
} catch (Exception $e) {
    sendError('Kesalahan server: ' . $e->getMessage(), 500);
}

$db->close();

?>
