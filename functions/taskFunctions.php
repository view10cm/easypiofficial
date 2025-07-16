<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/db_connection.php';

function getPendingTasksForToday() {
    global $pdo;
    
    // Check if user is logged in
    if (!isset($_SESSION['account_id'])) {
        return [];
    }
    
    $accountId = $_SESSION['account_id'];
    $today = date('Y-m-d');
    
    try {
        // Get tasks that are due today or overdue and not completed
        $stmt = $pdo->prepare("
            SELECT 
                t.task_id,
                t.task_title,
                t.task_due_date,
                s.status_name,
                p.priority_name
            FROM tasks t
            LEFT JOIN task_status s ON t.status_id = s.status_id
            LEFT JOIN task_priority_levels p ON t.priority_id = p.priority_id
            WHERE t.account_id = :account_id 
            AND t.task_due_date <= :today 
            AND (s.status_name != 'Completed' OR s.status_name IS NULL)
            ORDER BY t.task_due_date ASC, p.priority_id DESC
        ");
        $stmt->execute([
            'account_id' => $accountId,
            'today' => $today
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching pending tasks: " . $e->getMessage());
        return [];
    }
}

function getOverdueTasksCount() {
    global $pdo;
    
    // Check if user is logged in
    if (!isset($_SESSION['account_id'])) {
        return 0;
    }
    
    $accountId = $_SESSION['account_id'];
    $today = date('Y-m-d');
    
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count
            FROM tasks t
            LEFT JOIN task_status s ON t.status_id = s.status_id
            WHERE t.account_id = :account_id 
            AND t.task_due_date < :today 
            AND (s.status_name != 'Completed' OR s.status_name IS NULL)
        ");
        $stmt->execute([
            'account_id' => $accountId,
            'today' => $today
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    } catch (PDOException $e) {
        error_log("Error fetching overdue tasks count: " . $e->getMessage());
        return 0;
    }
}

function getTodayTasksCount() {
    global $pdo;
    
    // Check if user is logged in
    if (!isset($_SESSION['account_id'])) {
        return 0;
    }
    
    $accountId = $_SESSION['account_id'];
    $today = date('Y-m-d');
    
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count
            FROM tasks t
            LEFT JOIN task_status s ON t.status_id = s.status_id
            WHERE t.account_id = :account_id 
            AND t.task_due_date = :today 
            AND (s.status_name != 'Completed' OR s.status_name IS NULL)
        ");
        $stmt->execute([
            'account_id' => $accountId,
            'today' => $today
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    } catch (PDOException $e) {
        error_log("Error fetching today tasks count: " . $e->getMessage());
        return 0;
    }
}

function getNotificationData() {
    $pendingTasks = getPendingTasksForToday();
    $overdueCount = getOverdueTasksCount();
    $todayCount = getTodayTasksCount();
    
    return [
        'pending_tasks' => $pendingTasks,
        'overdue_count' => $overdueCount,
        'today_count' => $todayCount,
        'total_pending' => count($pendingTasks)
    ];
}
?>
