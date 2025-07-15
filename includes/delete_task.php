<?php
require_once 'db_connection.php';
header('Content-Type: application/json');

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
    $taskId = intval($_POST['task_id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE task_id = :task_id");
        $stmt->bindParam(':task_id', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        $response['success'] = true;
    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
