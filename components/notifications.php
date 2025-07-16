<?php
require_once __DIR__ . '/../functions/taskFunctions.php';

function renderNotificationDropdown() {
    $notificationData = getNotificationData();
    $pendingTasks = $notificationData['pending_tasks'];
    $overdueCount = $notificationData['overdue_count'];
    $todayCount = $notificationData['today_count'];
    $totalPending = $notificationData['total_pending'];
    
    ob_start();
    ?>
    <div class="dropdown">
        <button class="btn position-relative <?php echo $totalPending > 0 ? 'text-danger' : 'text-muted'; ?>" 
                type="button" 
                id="notificationDropdown" 
                data-bs-toggle="dropdown" 
                aria-expanded="false"
                title="<?php echo $totalPending > 0 ? 'You have ' . $totalPending . ' pending task(s)' : 'No pending tasks'; ?>">
            <i class="bi <?php echo $totalPending > 0 ? 'bi-bell-fill' : 'bi-bell'; ?>"></i>
            <?php if ($totalPending > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?php echo $totalPending > 9 ? '9+' : $totalPending; ?>
                </span>
            <?php endif; ?>
        </button>
        
        <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown">
            <li>
                <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                    <span>Notifications</span>
                    <?php if ($totalPending > 0): ?>
                        <span class="badge bg-danger rounded-pill"><?php echo $totalPending; ?></span>
                    <?php endif; ?>
                </h6>
            </li>
            
            <?php if ($totalPending > 0): ?>
                <?php if ($overdueCount > 0): ?>
                    <li><h6 class="dropdown-header text-danger">Overdue Tasks (<?php echo $overdueCount; ?>)</h6></li>
                <?php endif; ?>
                
                <?php if ($todayCount > 0): ?>
                    <li><h6 class="dropdown-header text-warning">Due Today (<?php echo $todayCount; ?>)</h6></li>
                <?php endif; ?>
                
                <li><hr class="dropdown-divider"></li>
                
                <?php 
                $displayCount = 0;
                foreach ($pendingTasks as $task): 
                    if ($displayCount >= 5) break; // Limit to 5 notifications
                    $displayCount++;
                    
                    // Set timezone for proper date comparison
                    date_default_timezone_set('Asia/Manila');
                    
                    $dueDate = new DateTime($task['task_due_date']);
                    $today = new DateTime();
                    
                    // Compare only dates (without time) for accurate overdue calculation
                    $dueDateOnly = $dueDate->format('Y-m-d');
                    $todayOnly = $today->format('Y-m-d');
                    
                    $isOverdue = $dueDateOnly < $todayOnly;
                    $isToday = $dueDateOnly === $todayOnly;
                ?>
                    <li>
                        <a class="dropdown-item notification-item" href="../pages/vitalTask.php" 
                           data-task-id="<?php echo $task['task_id']; ?>"
                           onclick="viewTaskDetails(<?php echo $task['task_id']; ?>)">
                            <div class="d-flex align-items-start">
                                <div class="me-2">
                                    <i class="bi <?php echo $isOverdue ? 'bi-exclamation-triangle-fill text-danger' : ($isToday ? 'bi-clock-fill text-warning' : 'bi-calendar-fill text-info'); ?>"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($task['task_title']); ?></h6>
                                        <?php if (!empty($task['priority_name'])): ?>
                                            <span class="badge <?php 
                                                echo $task['priority_name'] === 'High' ? 'bg-danger' : 
                                                    ($task['priority_name'] === 'Medium' ? 'bg-warning' : 'bg-secondary'); 
                                            ?>"><?php echo htmlspecialchars($task['priority_name']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="mb-1 text-muted small">
                                        Due: <?php echo $dueDate->format('M j, Y'); ?>
                                        <?php if ($isOverdue): ?>
                                            <span class="text-danger">(Overdue)</span>
                                        <?php elseif ($isToday): ?>
                                            <span class="text-warning">(Today)</span>
                                        <?php endif; ?>
                                    </p>
                                    <?php if (!empty($task['status_name'])): ?>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($task['status_name']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
                
                <?php if ($totalPending > 5): ?>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-center text-primary" href="myTask.php">
                            View all <?php echo $totalPending; ?> pending tasks
                        </a>
                    </li>
                <?php endif; ?>
                
            <?php else: ?>
                <li>
                    <div class="dropdown-item text-center text-muted py-4">
                        <i class="bi bi-check-circle-fill text-success mb-2" style="font-size: 2rem;"></i>
                        <p class="mb-0">All caught up!</p>
                        <small>No pending tasks for today</small>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    
    <style>
    .notification-dropdown {
        min-width: 350px;
        max-width: 400px;
        max-height: 500px;
        overflow-y: auto;
    }
    
    .notification-item {
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
    }
    
    .notification-item:hover {
        background-color: #f8f9fa;
    }
    
    .notification-item:last-child {
        border-bottom: none;
    }
    
    .notification-item h6 {
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
    
    .notification-item p {
        font-size: 0.8rem;
        margin-bottom: 0.25rem;
    }
    
    .badge {
        font-size: 0.7rem;
    }
    </style>
    
    <script>
    function viewTaskDetails(taskId) {
        // You can customize this function to show task details
        // For now, it will redirect to the task page
        window.location.href = '../pages/vitalTask.php';
    }
    
    // Load the notification JavaScript file
    if (!document.querySelector('script[src*="notifications.js"]')) {
        const script = document.createElement('script');
        script.src = '../scripts/notifications.js';
        document.head.appendChild(script);
    }
    </script>
    <?php
    return ob_get_clean();
}
?>
