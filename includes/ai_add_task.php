<?php
require 'db_connection.php';
session_start();

$data = json_decode(file_get_contents('php://input'), true);

if (empty($_SESSION['account_id']) || empty($data['title']) || empty($data['deadline'])) {
    http_response_code(400);
    echo 'Missing required fields';
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO tasks (
        account_id, task_title, task_description, task_due_date,
        priority_id, status_id, task_img
    ) VALUES (
        :account_id, :title, :description, :deadline,
        :priority_id, :status_id, :task_img
    )");

    $stmt->execute([
        'account_id'   => $_SESSION['account_id'],
        'title'        => $data['title'],
        'description'  => $data['description'],
        'deadline'     => $data['deadline'],
        'priority_id'  => $data['priority_id'],
        'status_id'    => $data['status_id'],
        'task_img'     => '../assets/working.png'
    ]);

    echo "success";
} catch (Exception $e) {
    http_response_code(500);
    echo "Failed to add task";
}
