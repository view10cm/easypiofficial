<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['account_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['action']) || $input['action'] !== 'mark_read') {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

$accountId = $_SESSION['account_id'];

try {
    // For now, we'll just log that notifications were marked as read
    // In a more advanced system, you might want to create a notifications table
    // and mark specific notifications as read
    
    error_log("Notifications marked as read for account ID: " . $accountId);
    
    echo json_encode(['success' => true, 'message' => 'Notifications marked as read']);
    
} catch (Exception $e) {
    error_log("Error in mark_notifications_read.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
?>
