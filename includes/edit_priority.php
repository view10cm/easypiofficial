<?php
require 'db_connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['priority_id'], $_POST['priority_name'])) {

    $id = intval($_POST['priority_id']);
    $name = trim($_POST['priority_name']);

    if ($name === '') {
        echo json_encode(['success' => false, 'message' => 'Name cannot be empty.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE task_priority_levels SET priority_name = :name WHERE priority_id = :id");
        $stmt->execute([
            'name' => $name,
            'id'   => $id
        ]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        error_log('Edit Error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'DB error.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
