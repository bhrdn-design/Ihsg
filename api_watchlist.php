<?php
/**
 * IHSG Screener - Watchlist API
 * GET /api/watchlist - Get user watchlist
 * POST /api/watchlist - Add to watchlist
 * DELETE /api/watchlist - Remove from watchlist
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
        // Get user watchlist
        handleGetWatchlist($db, $userId);
    } 
    elseif ($method === 'POST') {
        // Add to watchlist
        handleAddWatchlist($db, $userId);
    } 
    elseif ($method === 'DELETE') {
        // Remove from watchlist
        handleRemoveWatchlist($db, $userId);
    } 
    else {
        sendError('Method tidak diizinkan', 405);
    }
    
    $db->close();
    
} catch (Exception $e) {
    sendError('Kesalahan server: ' . $e->getMessage(), 500);
}

/**
 * Handle GET - Fetch user watchlist
 */
function handleGetWatchlist($db, $userId) {
    $stmt = $db->prepare("
        SELECT id, stock_code, stock_name, entry_price, added_at
        FROM watchlist
        WHERE user_id = ?
        ORDER BY added_at DESC
    ");
    
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $watchlist = [];
    while ($row = $result->fetch_assoc()) {
        $watchlist[] = [
            'id' => $row['id'],
            'stock_code' => $row['stock_code'],
            'stock_name' => $row['stock_name'],
            'entry_price' => $row['entry_price'],
            'added_at' => $row['added_at']
        ];
    }
    
    $stmt->close();
    
    sendSuccess('Watchlist berhasil diambil', [
        'count' => count($watchlist),
        'items' => $watchlist
    ], 200);
}

/**
 * Handle POST - Add to watchlist
 */
function handleAddWatchlist($db, $userId) {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Validate input
    if (!isset($data['stock_code']) || empty($data['stock_code'])) {
        sendError('Kode saham harus diisi', 400);
    }
    
    $stockCode = strtoupper(sanitize($data['stock_code']));
    $stockName = sanitize($data['stock_name'] ?? '');
    $entryPrice = floatval($data['entry_price'] ?? 0);
    
    // Validate stock code format
    if (!preg_match('/^[A-Z0-9]{1,5}$/', $stockCode)) {
        sendError('Format kode saham tidak valid', 400);
    }
    
    try {
        // Check if already in watchlist
        $checkStmt = $db->prepare("
            SELECT id FROM watchlist 
            WHERE user_id = ? AND stock_code = ?
            LIMIT 1
        ");
        
        $checkStmt->bind_param("is", $userId, $stockCode);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $checkStmt->close();
            sendError('Saham sudah ada di watchlist Anda', 409);
        }
        
        $checkStmt->close();
        
        // Add to watchlist
        $insertStmt = $db->prepare("
            INSERT INTO watchlist (user_id, stock_code, stock_name, entry_price, added_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        $insertStmt->bind_param("issd", $userId, $stockCode, $stockName, $entryPrice);
        
        if (!$insertStmt->execute()) {
            $insertStmt->close();
            sendError('Gagal menambah ke watchlist', 500);
        }
        
        $watchlistId = $db->insert_id;
        $insertStmt->close();
        
        logActivity($userId, 'watchlist_added', "Stock: $stockCode");
        
        sendSuccess('Saham berhasil ditambahkan ke watchlist', [
            'id' => $watchlistId,
            'stock_code' => $stockCode,
            'stock_name' => $stockName,
            'entry_price' => $entryPrice,
            'added_at' => date('Y-m-d H:i:s')
        ], 201);
        
    } catch (Exception $e) {
        throw $e;
    }
}

/**
 * Handle DELETE - Remove from watchlist
 */
function handleRemoveWatchlist($db, $userId) {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Validate input
    if (!isset($data['stock_code']) || empty($data['stock_code'])) {
        sendError('Kode saham harus diisi', 400);
    }
    
    $stockCode = strtoupper(sanitize($data['stock_code']));
    
    try {
        // Check if exists
        $checkStmt = $db->prepare("
            SELECT id FROM watchlist 
            WHERE user_id = ? AND stock_code = ?
            LIMIT 1
        ");
        
        $checkStmt->bind_param("is", $userId, $stockCode);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows === 0) {
            $checkStmt->close();
            sendError('Saham tidak ditemukan di watchlist', 404);
        }
        
        $checkStmt->close();
        
        // Delete from watchlist
        $deleteStmt = $db->prepare("
            DELETE FROM watchlist 
            WHERE user_id = ? AND stock_code = ?
        ");
        
        $deleteStmt->bind_param("is", $userId, $stockCode);
        
        if (!$deleteStmt->execute()) {
            $deleteStmt->close();
            sendError('Gagal menghapus dari watchlist', 500);
        }
        
        $deleteStmt->close();
        
        logActivity($userId, 'watchlist_removed', "Stock: $stockCode");
        
        sendSuccess('Saham berhasil dihapus dari watchlist', [
            'stock_code' => $stockCode
        ], 200);
        
    } catch (Exception $e) {
        throw $e;
    }
}

?>
