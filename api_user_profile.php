<?php
/**
 * IHSG Screener - User Profile API
 * GET /api/user/profile - Get user profile
 * PUT /api/user/profile - Update user profile
 */

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

// Require authentication
if (!isLoggedIn()) {
    sendError('Tidak ada sesi login', 401);
}

$method = $_SERVER['REQUEST_METHOD'];
$userId = getCurrentUserId();

try {
    $db = Database::getInstance()->getConnection();
    
    if ($method === 'GET') {
        // Get user profile
        handleGetProfile($db, $userId);
    } 
    elseif ($method === 'PUT') {
        // Update user profile
        handleUpdateProfile($db, $userId);
    } 
    else {
        sendError('Method tidak diizinkan', 405);
    }
    
    $db->close();
    
} catch (Exception $e) {
    sendError('Kesalahan server: ' . $e->getMessage(), 500);
}

/**
 * Handle GET - Fetch user profile
 */
function handleGetProfile($db, $userId) {
    $stmt = $db->prepare("
        SELECT 
            id, username, email, full_name, phone, profile_picture,
            is_verified, last_login, created_at, updated_at
        FROM users 
        WHERE id = ? 
        LIMIT 1
    ");
    
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        $stmt->close();
        sendError('User tidak ditemukan', 404);
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    sendSuccess('Profil user berhasil diambil', [
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'full_name' => $user['full_name'],
        'phone' => $user['phone'],
        'profile_picture' => $user['profile_picture'],
        'is_verified' => (bool)$user['is_verified'],
        'last_login' => $user['last_login'],
        'created_at' => $user['created_at'],
        'updated_at' => $user['updated_at']
    ], 200);
}

/**
 * Handle PUT - Update user profile
 */
function handleUpdateProfile($db, $userId) {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Get current user
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        $stmt->close();
        sendError('User tidak ditemukan', 404);
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Validate input
    $fullName = $data['full_name'] ?? $user['full_name'];
    $phone = $data['phone'] ?? $user['phone'];
    
    // Sanitize inputs
    $fullName = sanitize($fullName);
    $phone = sanitize($phone);
    
    // Validate phone format (basic)
    if (!empty($phone) && !preg_match('/^(\d{10,15})$/', str_replace(['-', ' ', '+'], '', $phone))) {
        sendError('Format nomor telepon tidak valid', 400);
    }
    
    // Update profile
    $updateStmt = $db->prepare("
        UPDATE users 
        SET full_name = ?, phone = ?, updated_at = NOW()
        WHERE id = ?
    ");
    
    $updateStmt->bind_param("ssi", $fullName, $phone, $userId);
    
    if (!$updateStmt->execute()) {
        $updateStmt->close();
        sendError('Gagal update profil', 500);
    }
    
    $updateStmt->close();
    
    logActivity($userId, 'profile_updated', "Updated: full_name, phone");
    
    sendSuccess('Profil berhasil diupdate', [
        'full_name' => $fullName,
        'phone' => $phone,
        'updated_at' => date('Y-m-d H:i:s')
    ], 200);
}

?>
