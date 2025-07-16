<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['account_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$accountId = $_SESSION['account_id'];
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

try {
    // Get tasks due today and tomorrow that are not completed
    $stmt = $pdo->prepare("
        SELECT 
            t.task_id,
            t.task_title,
            t.task_due_date,
            DATEDIFF(t.task_due_date, :today) as days_until_due,
            s.status_name
        FROM tasks t
        LEFT JOIN task_status s ON t.status_id = s.status_id
        WHERE t.account_id = :account_id 
        AND t.task_due_date BETWEEN :today AND :tomorrow
        AND (s.status_name != 'Completed' OR s.status_name IS NULL)
        ORDER BY t.task_due_date ASC
    ");
    
    $stmt->execute([
        'account_id' => $accountId,
        'today' => $today,
        'tomorrow' => $tomorrow
    ]);
    
    $upcomingTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'upcoming_deadlines' => $upcomingTasks
    ]);
    
} catch (PDOException $e) {
    error_log("Error in check_deadlines.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
