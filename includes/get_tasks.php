<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['account_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$accountId = $_SESSION['account_id'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            t.task_id,
            t.task_title,
            t.task_description,
            t.task_img,
            t.task_due_date,
            t.created_at,
            p.priority_name,
            s.status_name
        FROM tasks t
        JOIN task_priority_levels p ON t.priority_id = p.priority_id
        JOIN task_status s ON t.status_id = s.status_id
        WHERE t.account_id = :account_id
        ORDER BY t.created_at DESC
    ");
    $stmt->execute(['account_id' => $accountId]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'tasks' => $tasks]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
