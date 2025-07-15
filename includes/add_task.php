<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['account_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

$accountId   = $_SESSION['account_id'];
$title       = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$deadline    = $_POST['deadline'] ?? '';
$priority    = $_POST['priority'] ?? '';
$status      = $_POST['status'] ?? '';
$task_img    = '../assets/working.png'; // <- Always set default first

// Get priority_id
$priorityStmt = $pdo->prepare("SELECT priority_id FROM task_priority_levels WHERE priority_name = :priority");
$priorityStmt->execute(['priority' => $priority]);
$priorityId = $priorityStmt->fetchColumn();

// Get status_id
$statusStmt = $pdo->prepare("SELECT status_id FROM task_status WHERE status_name = :status");
$statusStmt->execute(['status' => $status]);
$statusId = $statusStmt->fetchColumn();

if (!$priorityId || !$statusId) {
    echo json_encode(['success' => false, 'message' => 'Invalid priority or status.']);
    exit;
}

// Handle image upload (if provided)
if (!empty($_FILES['image']['name'])) {
    $targetDir = '../uploads/tasks/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $filename = uniqid('task_', true) . '_' . basename($_FILES['image']['name']);
    $targetPath = $targetDir . $filename;

    $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png'];

    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid image type.']);
        exit;
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $task_img = $filename; // <-- Set to uploaded image
    } else {
        echo json_encode(['success' => false, 'message' => 'Image upload failed.']);
        exit;
    }
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO tasks (
            account_id, task_title, task_description, priority_id, status_id, task_due_date, task_img
        ) VALUES (
            :account_id, :title, :description, :priority_id, :status_id, :deadline, :task_img
        )
    ");

    $stmt->execute([
        'account_id'   => $accountId,
        'title'        => $title,
        'description'  => $description,
        'priority_id'  => $priorityId,
        'status_id'    => $statusId,
        'deadline'     => $deadline,
        'task_img'     => $task_img
    ]);

    echo json_encode(['success' => true, 'message' => 'Task added successfully.']);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
