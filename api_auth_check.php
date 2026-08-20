<?php
/**
 * IHSG Screener - Auth Check API
 * GET /api/auth/check
 */

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if (!isLoggedIn()) {
        sendError('Tidak ada sesi login', 401);
    }
    
    $userId = getCurrentUserId();
    
    try {
        $db = Database::getInstance()->getConnection();
        
        // Get user info
        $stmt = $db->prepare("
            SELECT id, username, email, full_name, profile_picture, is_verified, last_login
            FROM users 
            WHERE id = ? 
            LIMIT 1
        ");
        
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows !== 1) {
            session_destroy();
            sendError('User tidak ditemukan', 404);
        }
        
        $user = $result->fetch_assoc();
        $stmt->close();
        
        sendSuccess('User terautentikasi', [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'full_name' => $user['full_name'],
            'profile_picture' => $user['profile_picture'],
            'is_verified' => (bool)$user['is_verified'],
            'last_login' => $user['last_login'],
            'is_authenticated' => true
        ], 200);
        
        $db->close();
    } catch (Exception $e) {
        sendError('Kesalahan database: ' . $e->getMessage(), 500);
    }
    
} catch (Exception $e) {
    sendError('Kesalahan server: ' . $e->getMessage(), 500);
}

?>
