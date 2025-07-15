<?php
require_once 'db_connection.php';

header('Content-Type: application/json');
$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['task_id'];
    $title = $_POST['title'];
    $description = $_POST['description'] ?? '';
    $deadline = $_POST['deadline'];
    $priorityName = $_POST['priority'];
    $statusName = $_POST['status'];

    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $filename = 'task_' . time() . '_' . basename($_FILES['image']['name']);
        $target = '../uploads/tasks/' . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $imagePath = $filename;
        } else {
            $response['message'] = 'Image upload failed.';
            echo json_encode($response);
            exit;
        }
    }

    try {
        // Fetch priority_id
        $stmtPriority = $pdo->prepare("SELECT priority_id FROM task_priority_levels WHERE priority_name = :priority");
        $stmtPriority->execute(['priority' => $priorityName]);
        $priorityRow = $stmtPriority->fetch();
        if (!$priorityRow) throw new Exception("Invalid priority name");
        $priorityId = $priorityRow['priority_id'];

        // Fetch status_id
        $stmtStatus = $pdo->prepare("SELECT status_id FROM task_status WHERE status_name = :status");
        $stmtStatus->execute(['status' => $statusName]);
        $statusRow = $stmtStatus->fetch();
        if (!$statusRow) throw new Exception("Invalid status name");
        $statusId = $statusRow['status_id'];

        // Build update query
        $sql = "
            UPDATE tasks 
            SET 
                task_title = :title,
                task_description = :description,
                task_due_date = :deadline,
                priority_id = :priority_id,
                status_id = :status_id" .
                ($imagePath ? ", task_img = :image" : "") . "
            WHERE task_id = :task_id
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':deadline', $deadline);
        $stmt->bindParam(':priority_id', $priorityId, PDO::PARAM_INT);
        $stmt->bindParam(':status_id', $statusId, PDO::PARAM_INT);
        if ($imagePath) {
            $stmt->bindParam(':image', $imagePath);
        }
        $stmt->bindParam(':task_id', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        $response['success'] = true;
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
