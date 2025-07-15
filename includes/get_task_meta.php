<?php
require 'db_connection.php';
header('Content-Type: application/json');

try {
    $priorities = $pdo->query("SELECT priority_id, priority_name FROM task_priority_levels")->fetchAll(PDO::FETCH_ASSOC);
    $statuses = $pdo->query("SELECT status_id, status_name FROM task_status")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'priorities' => $priorities,
        'statuses' => $statuses
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Unable to fetch task metadata.']);
}
