<?php
session_start();
require '../includes/db_connection.php';

// If user is not logged in, redirect to sign-in
if (empty($_SESSION['account_id'])) {
    header('Location: ../pages/sign_in.html');
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
        header('Location: ../pages/sign_in.html');
        exit;
    }

    // Sanitize and set variables
    $email = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
    $profilePicture = !empty($user['profile_picture'])
        ? '../uploads/' . htmlspecialchars($user['profile_picture'], ENT_QUOTES, 'UTF-8')
        : '../assets/img/placeholder.png';

} catch (PDOException $e) {
    // On DB error, redirect to sign-in
    error_log('DB error: ' . $e->getMessage());
    header('Location: ../pages/sign_in.html');
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
            <div class="container-fluid">
                <div style="margin-left:10px; width:100%;" class="p-4">
                    <div class="row h-100">
                        <!-- Left Column - Task List -->
                        <div class="col-md-6 pe-3">
                            <div class="card shadow-sm h-100" style="border-radius:16px;">
                                <div class="card-body p-4">

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="card-title fw-bold mb-0"
                                            style="color:#333; text-decoration: underline; text-decoration-color: #1286cc;; text-decoration-thickness: 2px;">
                                            My Tasks
                                        </h5>
                                        <a href="#" class="text-decoration-none"
                                            style="color:#1286cc; font-weight:bold; font-size:1.25rem;"
                                            data-bs-toggle="modal" data-bs-target="#addTaskModal">
                                            <i class="bi bi-plus-circle me-1"></i> Add Task
                                        </a>
                                    </div>

                                    <!-- Empty State -->
                                    <div class="text-center py-5" id="emptyState">
                                        <div class="mb-4">
                                            <i class="bi bi-clipboard2-check" style="font-size: 4rem; color: #e9ecef;"></i>
                                        </div>
                                        <h4 class="text-muted fw-bold mb-3">What's your next task for today?</h4>
                                        <p class="text-muted mb-4" style="font-size: 1.1rem;">
                                            Start organizing your day by adding your first task
                                        </p>
                                        <button class="btn btn-primary btn-lg rounded-pill px-4" 
                                                style="background: linear-gradient(135deg, #1286cc, #0ea5e9); border: none;"
                                                data-bs-toggle="modal" data-bs-target="#addTaskModal">
                                            <i class="bi bi-plus-circle me-2"></i>
                                            Create Your First Task
                                        </button>
                                    </div>

                                    <!-- Task Container (initially hidden) -->
                                    <div id="taskContainer" style="display: none;">
                                        <!-- Tasks will be dynamically added here -->
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Task Details -->
                        <div class="col-md-6 ps-3">
                            <div class="card shadow-sm h-100" style="border-radius:16px;">
                                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                                    
                                    <!-- Empty Details State -->
                                    <div class="text-center" id="emptyDetails">
                                        <div class="mb-4">
                                            <i class="bi bi-card-text" style="font-size: 3rem; color: #e9ecef;"></i>
                                        </div>
                                        <h5 class="text-muted fw-bold mb-3">Select a task to view details</h5>
                                        <p class="text-muted" style="font-size: 1rem;">
                                            Click on a task from the left panel to see its details here
                                        </p>
                                    </div>

                                    <!-- Task Details Container (initially hidden) -->
                                    <div id="taskDetails" style="display: none; width: 100%;">
                                        <!-- Task details will be dynamically populated here -->
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addTaskModalLabel">Add New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addTaskForm">
                        <div class="mb-3">
                            <label for="newTaskTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="newTaskTitle" placeholder="Enter task title" required>
                        </div>
                        <div class="mb-3">
                            <label for="newTaskObjective" class="form-label">Objective</label>
                            <input type="text" class="form-control" id="newTaskObjective" placeholder="Enter task objective">
                        </div>
                        <div class="mb-3">
                            <label for="newTaskDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="newTaskDescription" rows="3"
                                placeholder="Enter task description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="newTaskDeadline" class="form-label">Deadline</label>
                            <input type="text" class="form-control" id="newTaskDeadline" placeholder="e.g., End of Day, Tomorrow 5 PM">
                        </div>
                        <div class="mb-3">
                            <label for="newTaskPriority" class="form-label">Priority</label>
                            <select class="form-select" id="newTaskPriority">
                                <option value="Low">Low</option>
                                <option value="Moderate" selected>Moderate</option>
                                <option value="High">High</option>
                                <option value="Extreme">Extreme</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="newTaskStatus" class="form-label">Status</label>
                            <select class="form-select" id="newTaskStatus">
                                <option value="Not Started" selected>Not Started</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="newTaskNotes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="newTaskNotes" rows="2"
                                placeholder="Any additional notes or reminders"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="newTaskImage" class="form-label">Task Image (Optional)</label>
                            <input type="file" class="form-control" id="newTaskImage" accept="image/*">
                            <div class="mt-2" id="newTaskImagePreview" style="display: none;">
                                <img src="" alt="Task image preview" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" form="addTaskForm">Add Task</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:16px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editTaskModalLabel">Edit Task Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTaskForm">
                        <div class="mb-3">
                            <label for="editTaskTitle" class="form-label">Task Title</label>
                            <input type="text" class="form-control" id="editTaskTitle">
                        </div>
                        <div class="mb-3">
                            <label for="editTaskObjective" class="form-label">Objective</label>
                            <input type="text" class="form-control" id="editTaskObjective">
                        </div>
                        <div class="mb-3">
                            <label for="editTaskDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editTaskDescription" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editTaskDeadline" class="form-label">Deadline</label>
                            <input type="text" class="form-control" id="editTaskDeadline">
                        </div>
                        <div class="mb-3">
                            <label for="editTaskPriority" class="form-label">Priority</label>
                            <select class="form-select" id="editTaskPriority">
                                <option value="Low">Low</option>
                                <option value="Moderate">Moderate</option>
                                <option value="High">High</option>
                                <option value="Extreme">Extreme</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editTaskStatus" class="form-label">Status</label>
                            <select class="form-select" id="editTaskStatus">
                                <option value="Not Started">Not Started</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editTaskNotes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="editTaskNotes" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editTaskImage" class="form-label">Task Image (Optional)</label>
                            <input type="file" class="form-control" id="editTaskImage" accept="image/*">
                            <div class="mt-2" id="editTaskImagePreview" style="display: none;">
                                <img src="" alt="Task image preview" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" form="editTaskForm">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteTaskModal" tabindex="-1" aria-labelledby="deleteTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteTaskModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="fs-5 fw-semibold text-danger">Are you sure you want to delete this task?</p>
                    <p class="text-muted">This action cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <div id="chatbot-container"></div>
    <script type="module" src="../scripts/myTask.js"></script>
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
</html>