<?php
/**
 * IHSG Screener - Logout API
 * POST /api/logout
 */

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method tidak diizinkan', 405);
}

try {
    // Log the logout activity
    if (isLoggedIn()) {
        logActivity(getCurrentUserId(), 'user_logout', null);
    }
    
    // Destroy session
    $userId = $_SESSION['user_id'] ?? null;
    
    session_destroy();
    
    // Clear cookies
    if (isset($_COOKIE['PHPSESSID'])) {
        setcookie('PHPSESSID', '', time() - 3600, '/');
    }
    
    if (isset($_COOKIE['user_token'])) {
        setcookie('user_token', '', time() - 3600, '/');
    }
    
    sendSuccess('Logout berhasil', [
        'redirect' => FRONTEND_URL . 'index.html'
    ], 200);
    
} catch (Exception $e) {
    sendError('Kesalahan server: ' . $e->getMessage(), 500);
}

?>
