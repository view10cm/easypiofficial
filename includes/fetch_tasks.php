<?php
session_start();
require '../includes/db_connection.php';

// Check if user is logged in and task_id is present
if (empty($_SESSION['account_id']) || empty($_GET['task_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$taskId = $_GET['task_id'];
$accountId = $_SESSION['account_id'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            t.task_id,
            t.task_title,
            t.task_description,
            t.task_due_date,
            t.task_img,
            p.priority_name,
            s.status_name
        FROM tasks t
        LEFT JOIN task_priority_levels p ON t.priority_id = p.priority_id
        LEFT JOIN task_status s ON t.status_id = s.status_id
        WHERE t.task_id = :task_id AND t.account_id = :account_id
        LIMIT 1
    ");
    $stmt->execute([
        'task_id' => $taskId,
        'account_id' => $accountId
    ]);

    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($task) {
        echo json_encode($task);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Task not found']);
    }

} catch (PDOException $e) {
    error_log("Database error in get_task.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
