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

    if (!$user) {
        session_unset();
        session_destroy();
        header('Location: ../pages/sign_in.php');
        exit;
    }

    // Set user variables
    $email = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
    $profilePicture = !empty($user['profile_picture'])
        ? '../uploads/' . htmlspecialchars($user['profile_picture'], ENT_QUOTES, 'UTF-8')
        : '../assets/img/placeholder.png';

    // Fetch task statuses
    $statusStmt = $pdo->query("SELECT status_id, status_name FROM task_status");
    $statuses = $statusStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch priorities
$priorityStmt = $pdo->query("SELECT * FROM task_priority_levels ORDER BY priority_id ASC");
$priorities = $priorityStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('DB error: ' . $e->getMessage());
    header('Location: ../pages/sign_in.php');
    exit;
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
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body>
  <!-- Navbar -->
   <div id="navbar-container"></div>

  <!-- Content -->
  <div class="content-wrapper">
  <div id="sidebar-container"></div>

    <!-- Scrollable Main Content -->
    <div class="main-content">
      <!-- Main Content (add margin-left to avoid overlap) -->
      <div style="margin-left:10px; width:100%;">
        <div class="container py-4" style="max-width:1400px;">
          <div class="bg-white rounded-4 shadow-sm p-4" style="border:1px solid #e3e3e3;">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="card-title fw-bold mb-3" 
             style="color:#333; text-decoration: underline; text-decoration-color: #1286cc;; text-decoration-thickness: 2px;">
                                    Task Categories</h5>
              <a href="dashboard.html" class="fw-semibold" style="color:#222; text-decoration:underline;">Go Back</a>
            </div>

            <!-- Task Status Table -->
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold" style="color:#222; border-bottom:2px solid #1286cc;;">Task Status</span>
              </div>
              <div class="table-responsive">
                <table id="taskStatusTable" class="table table-bordered align-middle"
                  style="border-radius:12px; overflow:hidden;">
                  <thead>
                <tr style="background:#f7f7f7;">
                <th style="width:100px; text-align: center; vertical-align: middle;">TS No.</th>
                <th style="text-align: center; vertical-align: middle;">Task Status</th>
               </tr>
                </thead>
                 <tbody>
  <?php
    $statusColors = [
      'Not Started' => '#ff6b6b',
      'In Progress' => '#17a2b8',
      'Completed'   => '#28a745'
    ];
  ?>
  <?php if (!empty($statuses)): ?>
    <?php foreach ($statuses as $index => $status): ?>
      <?php
        $color = $statusColors[$status['status_name']] ?? '#6c757d'; // default gray
      ?>
      <tr>
        <td style="text-align: center;"><?= $index + 1 ?></td>
        <td style="text-align: center; color: <?= $color ?>; font-weight: bold;">
          <?= htmlspecialchars($status['status_name']) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="2" class="text-center text-muted">No Task Status, please create your Task Status</td>
    </tr>
  <?php endif; ?>
</tbody>


                </table>
              </div>
            </div>

        <!-- Task Priority Table -->
<!-- Task Priority Section -->
<div class="mb-4">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <span class="fw-semibold" style="color:#222; border-bottom:2px solid #1286cc;">Task Priority</span>
    <a href="#" class="fw-semibold" style="color:#1286cc; text-decoration:none; font-size:1rem;"
      data-bs-toggle="modal" data-bs-target="#addTaskPriorityModal">+ Add Task Priority</a>
  </div>

  <div class="table-responsive">
    <table id="taskPriorityTable" class="table table-bordered align-middle" style="border-radius:12px; overflow:hidden;">
      <thead>
        <tr style="background:#f7f7f7;">
          <th style="width:100px;" class="text-center">TP No.</th>
          <th class="text-center">Task Priority</th>
          <th style="width:200px;" class="text-center">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($priorities)): ?>
          <?php foreach ($priorities as $index => $priority): ?>
            <tr data-priority-id="<?= $priority['priority_id'] ?>">
              <td class="text-center"><?= $index + 1 ?></td>
              <td class="fw-bold text-center"><?= htmlspecialchars($priority['priority_name']) ?></td>
              <td class="text-center">
                <button class="edit-btn btn btn-sm btn-warning" data-id="<?= $priority['priority_id'] ?>">
                  <i class="bi bi-pencil-square"></i> Edit
                </button>
                <button class="delete-btn btn btn-sm btn-danger ms-2" data-id="<?= $priority['priority_id'] ?>">
                  <i class="bi bi-trash"></i> Delete
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr id="noTaskPriorityRow">
            <td colspan="3" class="text-center text-muted">
              No Task Priority, please create your Task Priority
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Task Priority Modal -->
<div class="modal fade" id="addTaskPriorityModal" tabindex="-1" aria-labelledby="addTaskPriorityModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addPriorityForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="addTaskPriorityModalLabel">Add Task Priority</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="taskPriorityName" class="form-label">Task Priority Name</label>
            <input type="text" class="form-control" id="taskPriorityName" name="priority" placeholder="Enter priority name" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button id="addPriorityBtn" type="submit" class="btn" style="background:#1286cc; color:white;">
            Add Priority
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
    <!-- End Add Task Priority Modal -->

    <!-- Edit Task Priority Modal -->
    <div class="modal fade" id="editTaskPriorityModal" tabindex="-1" aria-labelledby="editTaskPriorityModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editTaskPriorityModalLabel">Edit Task Priority</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editPriorityForm">
        <div class="modal-body">
          <input type="hidden" id="editPriorityId">
          <div class="mb-3">
            <label for="editTaskPriorityName" class="form-label">Task Priority Name</label>
            <input type="text" class="form-control" id="editTaskPriorityName" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn" style="background:#ff6b6b; color:white;">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

    <!-- End Edit Task Priority Modal -->
    <!-- Delete Priority Modal -->
<div class="modal fade" id="deletePriorityModal" tabindex="-1" aria-labelledby="deletePriorityModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deletePriorityModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <p class="fs-5 fw-semibold text-danger">Are you sure you want to delete this task priority?</p>
        <p class="text-muted">This action cannot be undone.</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeletePriorityBtn">Delete</button>
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
<script type="module" src="../scripts/taskCategories.js"></script>
<script type="module" src="../scripts/chatbot.js"></script>
<script type="module" src="../scripts/chatbot_task_flow.js"></script>
</html>