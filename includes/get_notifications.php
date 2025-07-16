<?php
session_start();
require_once 'db_connection.php';
require_once '../functions/taskFunctions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['account_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

try {
    $notificationData = getNotificationData();
    
    echo json_encode([
        'success' => true,
        'data' => $notificationData
    ]);
} catch (Exception $e) {
    error_log("Error in get_notifications.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
?>
