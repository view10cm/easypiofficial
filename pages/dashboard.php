<?php
session_start();
require '../includes/db_connection.php';

// If user is not logged in, redirect to sign-in
if (empty($_SESSION['account_id'])) {
    header('Location: ../pages/sign_in.php');
    exit;
}

try {
    // Fetch user data
    $stmt = $pdo->prepare("SELECT email, profile_picture FROM accounts WHERE account_id = :id LIMIT 1");
    $stmt->execute(['id' => $_SESSION['account_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If user not found, force logout
    if (!$user) {
        session_unset();
        session_destroy();
        header('Location: ../pages/sign_in.php');
        exit;
    }

    // Sanitize and set variables
    $email = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
    $profilePicture = !empty($user['profile_picture'])
        ? '../uploads/' . htmlspecialchars($user['profile_picture'], ENT_QUOTES, 'UTF-8')
        : '../assets/default_pp.jpg';

} catch (PDOException $e) {
    // On DB error, redirect to sign-in
    error_log('DB error: ' . $e->getMessage());
    header('Location: ../pages/sign_in.php');
    exit;
}
  // Fetch today's tasks
    $stmt = $pdo->prepare("
        SELECT t.task_id, t.task_title, t.task_description, t.task_due_date, t.task_img,
               p.priority_name, s.status_name
        FROM tasks t
        LEFT JOIN task_priority_levels p ON t.priority_id = p.priority_id
        LEFT JOIN task_status s ON t.status_id = s.status_id
        WHERE t.account_id = :account_id
          AND DATE(t.task_due_date) = CURDATE()
        ORDER BY t.task_due_date ASC
    ");
    $stmt->execute(['account_id' => $_SESSION['account_id']]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $statusColors = [
        'Not Started' => '#ff6b6b',
        'In Progress' => '#17a2b8',
        'Completed'   => '#28a745'
    ];

// Fetch completed tasks excluding today
$completedStmt = $pdo->prepare("
    SELECT t.task_title, t.task_description, t.task_due_date, t.task_img,
           p.priority_name, s.status_name
    FROM tasks t
    LEFT JOIN task_priority_levels p ON t.priority_id = p.priority_id
    LEFT JOIN task_status s ON t.status_id = s.status_id
    WHERE t.account_id = :account_id
      AND s.status_name = 'Completed'
      AND DATE(t.task_due_date) <> CURDATE()
    ORDER BY t.task_due_date DESC
");
$completedStmt->execute(['account_id' => $_SESSION['account_id']]);
$completedTasks = $completedStmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch counts by status
$statusStmt = $pdo->prepare("
    SELECT s.status_name, COUNT(*) AS total
    FROM tasks t
    LEFT JOIN task_status s ON t.status_id = s.status_id
    WHERE t.account_id = :account_id
    GROUP BY s.status_name
");
$statusStmt->execute(['account_id' => $_SESSION['account_id']]);
$statusCounts = $statusStmt->fetchAll(PDO::FETCH_KEY_PAIR);

$totalTasks = array_sum($statusCounts);

$completedPct = $totalTasks ? round(($statusCounts['Completed'] ?? 0) / $totalTasks * 100) : 0;
$inProgressPct = $totalTasks ? round(($statusCounts['In Progress'] ?? 0) / $totalTasks * 100) : 0;
$notStartedPct = $totalTasks ? round(($statusCounts['Not Started'] ?? 0) / $totalTasks * 100) : 0;

function getDashOffset($percent) {
    return 188 - (188 * ($percent / 100));
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyPi - User Dashboard</title>
    <link rel="icon" type="image/png" href="../assets/titlelogo.png">
    <link rel="stylesheet" href="../css/general_components.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body>
    <!-- Navbar -->
    <div id="navbar-container"></div>

    <!-- Content -->
    <div class="content-wrapper">
        <div id="sidebar-container"></div>

        <div class="main-content">
            <div class="container-fluid">
                <div style="margin-left:10px; width:100%;">
                    <div class="container-fluid py-4">
                        <div class="row g-4">
                            <!-- Left Column: To-Do -->
                            <div class="col-lg-7">
                                <div class="card mb-4 shadow-sm">
                                    <h5 class="card-title fw-bold ms-4 mt-4 mb-4"
                                        style="color:#333; text-decoration: underline; text-decoration-color: #1286cc; text-decoration-thickness: 2px;">
                                        Dashboard
                                    </h5>

                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <i class="bi bi-clipboard-check"></i>
                                                <span class="fw-bold text-primary">To-Do</span>
                                                <span class="text-muted ms-2"><?= date("d F - l") ?></span>
                                            </div>
                                        </div>

                                        <!-- Task Cards -->
                                        <div class="mb-3">
                                            <?php if (!empty($tasks)): ?>
                                                <?php foreach ($tasks as $task): ?>
                                                    <?php
                                                        // Fix image path logic to match the task upload structure
                                                        $isUploadedImage = !empty($task['task_img']) && strpos($task['task_img'], 'task_') === 0;
                                                        $imgPath = $isUploadedImage
                                                            ? '../uploads/tasks/' . htmlspecialchars($task['task_img'], ENT_QUOTES, 'UTF-8')
                                                            : '../assets/' . htmlspecialchars($task['task_img'] ?: 'working.png', ENT_QUOTES, 'UTF-8');

                                                        $statusColor = $statusColors[$task['status_name']] ?? '#6c757d';
                                                    ?>
                                                    <div class="card border-0 shadow-sm mb-3">
                                                        <div class="card-body d-flex">
                                                            <div>
                                                                <span class="badge mb-2" style="background-color: <?= $statusColor ?>;">
                                                                    <?= htmlspecialchars($task['status_name']) ?>
                                                                </span>
                                                                <h5 class="card-title mb-1"><?= htmlspecialchars($task['task_title']) ?></h5>
                                                                <p class="card-text mb-2"><?= htmlspecialchars($task['task_description']) ?></p>
                                                                <div class="small text-muted">
                                                                    Priority: <span class="text-warning"><?= htmlspecialchars($task['priority_name']) ?></span> |
                                                                    Status: <span style="color: <?= $statusColor ?>;"><?= htmlspecialchars($task['status_name']) ?></span> |
                                                                    Due: <?= date('d/m/Y g:i A', strtotime($task['task_due_date'])) ?>
                                                                </div>
                                                            </div>
                                                            <img src="<?= $imgPath ?>" alt="Task Image" class="rounded ms-auto"
                                                                style="width:80px; height:60px; object-fit:cover;">
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p class="text-muted">No tasks for today.</p>
                                            <?php endif; ?>
                                        </div>
                                        <!-- End Task Cards -->
                                    </div>
                                </div>
                            </div>
                            <!-- Right Column: Task Status & Completed Task -->
                           <div class="col-lg-5">
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="fw-bold mb-3"><i class="bi bi-pie-chart"></i> Task Status</div>
            <div class="d-flex justify-content-between align-items-center">
                <!-- Completed -->
                <div class="text-center">
                    <svg width="70" height="70">
                        <circle cx="35" cy="35" r="30" stroke="#28a745" stroke-width="8"
                                fill="none" stroke-dasharray="188"
                                stroke-dashoffset="<?= getDashOffset($completedPct) ?>" />
                        <text x="35" y="40" text-anchor="middle" font-size="18"
                              fill="#222"><?= $completedPct ?>%</text>
                    </svg>
                    <div class="small mt-2 text-success fw-semibold">Completed</div>
                                            </div>
                <!-- In Progress -->
                <div class="text-center">
                    <svg width="70" height="70">
                        <circle cx="35" cy="35" r="30" stroke="#0d6efd" stroke-width="8"
                                fill="none" stroke-dasharray="188"
                                stroke-dashoffset="<?= getDashOffset($inProgressPct) ?>" />
                        <text x="35" y="40" text-anchor="middle" font-size="18"
                              fill="#222"><?= $inProgressPct ?>%</text>
                    </svg>
                    <div class="small mt-2 text-primary fw-semibold">In Progress</div>
                </div>

                <!-- Not Started -->
                <div class="text-center">
                    <svg width="70" height="70">
                        <circle cx="35" cy="35" r="30" stroke="#dc3545" stroke-width="8"
                                fill="none" stroke-dasharray="188"
                                stroke-dashoffset="<?= getDashOffset($notStartedPct) ?>" />
                        <text x="35" y="40" text-anchor="middle" font-size="18"
                              fill="#222"><?= $notStartedPct ?>%</text>
                    </svg>
                    <div class="small mt-2 text-danger fw-semibold">Not Started</div>
                </div>
            </div>
        </div>
    </div>
                            


                                <div class="card shadow-sm">
    <div class="card-body">
        <div class="fw-bold mb-3 text-danger">
            <i class="bi bi-check2-circle"></i> Completed Task
        </div>
        <div class="mb-3">
            <?php if (!empty($completedTasks)): ?>
                <?php foreach ($completedTasks as $task): ?>
                    <?php
                        // Fix image path logic for completed tasks
                        $isUploadedImage = !empty($task['task_img']) && strpos($task['task_img'], 'task_') === 0;
                        $imgPath = $isUploadedImage
                            ? '../uploads/tasks/' . htmlspecialchars($task['task_img'], ENT_QUOTES, 'UTF-8')
                            : '../assets/' . htmlspecialchars($task['task_img'] ?: 'working.png', ENT_QUOTES, 'UTF-8');

                        // Format time difference
                        $daysAgo = floor((time() - strtotime($task['task_due_date'])) / 86400);
                        $completedText = $daysAgo === 0 ? "Completed today"
                                        : ($daysAgo === 1 ? "Completed yesterday"
                                        : "Completed $daysAgo days ago");
                    ?>
                    <div class="card border-0 shadow-sm mb-2">
                        <div class="card-body d-flex">
                            <div>
                                <span class="badge bg-success mb-2">Completed</span>
                                <h6 class="card-title mb-1"><?= htmlspecialchars($task['task_title']) ?></h6>
                                <p class="card-text mb-2"><?= htmlspecialchars($task['task_description']) ?></p>
                                <div class="small text-muted"><?= $completedText ?></div>
                            </div>
                            <img src="<?= $imgPath ?>" alt="Task Image" class="rounded ms-auto"
                                 style="width:80px; height:60px; object-fit:cover;">
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No completed tasks yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div id="chatbot-container"></div>
</body>
<script type="importmap">
  {
    "imports": {
      "@google/generative-ai": "https://esm.run/@google/generative-ai"
    }
  }
</script>
<script type="module" src="../scripts/components.js"></script>
<script type="module" src="../scripts/chatbot.js"></script>
<script type="module" src="../scripts/chatbot_task_flow.js"></script>
</html>