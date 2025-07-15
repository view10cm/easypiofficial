<?php
require 'db_connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['priority_id'])) {
    $id = intval($_POST['priority_id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM task_priority_levels WHERE priority_id = :id");
        $stmt->execute(['id' => $id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DB error.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
