<?php
require 'db_connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['priority'])) {
    $name = trim($_POST['priority']);

    if ($name === '') {
        echo json_encode(['success' => false, 'message' => 'Priority name is required.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO task_priority_levels (priority_name) VALUES (:name)");
        $stmt->execute(['name' => $name]);
        $id = $pdo->lastInsertId();
        echo json_encode([
            'success' => true,
            'priority_id' => $id,
            'priority_name' => htmlspecialchars($name)
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DB error.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
