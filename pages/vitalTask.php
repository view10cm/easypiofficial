<?php
session_start();
require '../includes/db_connection.php';

// Redirect if not logged in
if (empty($_SESSION['account_id'])) {
    header('Location: ../pages/sign_in.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT email, profile_picture FROM accounts WHERE account_id = :id LIMIT 1");
    $stmt->execute(['id' => $_SESSION['account_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_unset();
        session_destroy();
        header('Location: ../pages/sign_in.php');
        exit;
    }

    $email = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
    $profilePicture = !empty($user['profile_picture'])
        ? '../uploads/' . htmlspecialchars($user['profile_picture'], ENT_QUOTES, 'UTF-8')
        : '../assets/placeholder.png';

} catch (PDOException $e) {
    error_log('DB error: ' . $e->getMessage());
    header('Location: ../pages/sign_in.php');
    exit;
}

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>EasyPi - Dashboard</title>
  <link rel="icon" type="image/png" href="../assets/titlelogo.png">
  <link rel="stylesheet" href="../css/general_components.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div id="navbar-container"></div>
<div class="content-wrapper">
  <div id="sidebar-container"></div>
  <div class="main-content p-4">
    <div class="row">
      <!-- Left Column - Task List -->
<div class="col-md-6 pe-3">
  <div class="card shadow-sm h-100" style="border-radius:16px;">
    <div class="card-body p-4">
      <h5 class="card-title fw-bold mb-4 text-decoration-underline" style="color:#333; text-decoration-color: #1286cc;">Vital Tasks</h5>

      <?php if (!empty($tasks)): ?>
        <?php foreach ($tasks as $task): ?>
          <?php $statusColor = $statusColors[$task['status_name']] ?? '#6c757d'; ?>
          <div class="card mb-3 border-0 shadow-sm"
               style="border-radius:12px; background:#f8f9fa; cursor:pointer;"
               onclick="loadTaskDetails(<?= (int)$task['task_id'] ?>)">
            <div class="card-body p-3">
              <div class="row align-items-center">
                <div class="col-auto">
                  <div class="rounded-circle d-flex align-items-center justify-content-center"
                       style="width:12px; height:12px; border:2px solid <?= $statusColor ?>;"></div>
                </div>
                <div class="col">
                  <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                      <h6 class="mb-1 fw-semibold" style="color:#333;">
                        <?= htmlspecialchars($task['task_title']) ?>
                      </h6>
                      <p class="text-muted mb-2" style="font-size:0.9rem;">
                        <?= htmlspecialchars(mb_strimwidth($task['task_description'], 0, 60, '...')) ?>
                      </p>
                      <div class="d-flex gap-3">
                        <small class="text-muted">Priority:
                          <span><?= htmlspecialchars($task['priority_name'] ?? 'Unknown') ?></span>
                        </small>
                        <small class="text-muted">Status:
                          <span style="color:<?= $statusColor ?>;"><?= htmlspecialchars($task['status_name'] ?? 'Unknown') ?></span>
                        </small>
                        <small class="text-muted">Due: <?= date("d/m/Y", strtotime($task['task_due_date'])) ?></small>
                      </div>
                    </div>
                    <div class="ms-2">
                      <img src="<?= $task['task_img'] ? '../uploads/tasks/' . htmlspecialchars($task['task_img']) : '../assets/placeholder.png' ?>"
                           alt="Task image" class="rounded" style="width:50px; height:35px; object-fit:cover;">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <center><p class="text-muted">You have no important tasks for today.</p></center>
      <?php endif; ?>
    </div>
  </div>
</div>


     <!-- Right Column - Task Details -->
<div class="col-md-6 ps-3 d-flex flex-column">
  <div class="card shadow-sm h-100" style="border-radius:16px;">
    <div class="card-body p-4 d-flex flex-column justify-content-center" id="task-details-container">

      <!-- Empty State -->
      <div class="text-center" id="emptyDetails">
        <div class="mb-4">
          <i class="bi bi-card-text" style="font-size: 3rem; color: #e9ecef;"></i>
        </div>
        <h5 class="text-muted fw-bold mb-3 text-center">Select a task to view details</h5>
        <p class="text-muted" style="font-size: 1rem;">
          Click on a task from the left panel to see its details here
        </p>
      </div>

      <!-- Task Details (will be filled dynamically) -->
      <div id="taskDetails" class="d-none">
        <div class="position-relative mb-4 text-center">
          <img id="task-img" class="rounded shadow-sm" style="max-width: 100%; max-height: 200px; object-fit: cover;">
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 class="fw-bold mb-0" id="task-title" style="color:#333;"></h4>
        </div>

        <div class="row mb-4">
          <div class="col-6">
            <div class="d-flex align-items-center">
              <span class="me-2" style="color:#666;">Priority:</span>
              <span class="badge rounded-pill" id="task-priority" style="background:#ccc; color:#fff;"></span>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex align-items-center">
              <span class="me-2" style="color:#666;">Status:</span>
              <span class="badge rounded-pill" id="task-status" style="background:#ccc; color:#fff;"></span>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <small class="text-muted">Deadline: <span id="task-deadline"></span></small>
        </div>

        <div class="mb-3">
          <strong style="color:#333;">Task Description:</strong>
          <p class="text-muted mt-2" id="task-description" style="font-size:0.95rem; line-height:1.6;"></p>
        </div>
      </div>
    </div>
  </div>
</div>


<div id="chatbot-container"></div>
</body>
<script src="../scripts/vitalTask.js" defer></script>
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