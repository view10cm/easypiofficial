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
                <!-- Header Section -->
                <div class="text-center mb-5">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                        style="width:80px; height:80px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <i class="bi bi-question-circle-fill text-white" style="font-size:2.5rem;"></i>
                    </div>
                    <h1 class="fw-bold mb-2" style="color:#333;">Help Center</h1>
                    <p class="text-muted fs-5">Find answers to common questions and get help with your tasks</p>
                </div>

                <!-- Search Bar
                <div class="row justify-content-center mb-5">
                    <div class="col-md-8">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0" style="border-color:#e9ecef;">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0"
                                placeholder="Search for help topics..." style="border-color:#e9ecef;">
                        </div>
                    </div>
                </div> -->

                <!-- Quick Help Cards -->
                <div class="flex justify-content-center row mb-5">
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm h-100"
                            style="border-radius:16px; transition:transform 0.2s;"
                            onmouseover="this.style.transform='translateY(-5px)'"
                            onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-4 text-center">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                                    style="width:60px; height:60px; background:rgba(185, 119, 14, 0.1);">
                                    <i class="bi bi-gear-fill" style="font-size:1.8rem; color:#17a2b8;""></i>
                                </div>
                                <h5 class="fw-bold mb-2" style="color:#333;">Account Settings</h5>
                                <p class="text-muted mb-3">Customize your profile, notifications, and preferences</p>
                                <a href="#" class="btn btn-sm rounded-pill"
                                    style="background:#17a2b8; color:white; padding:8px 20px;">View Settings</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm h-100"
                            style="border-radius:16px; transition:transform 0.2s;"
                            onmouseover="this.style.transform='translateY(-5px)'"
                            onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-4 text-center">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                                    style="width:60px; height:60px; background:rgba(23, 162, 184, 0.1);">
                                    <i class="bi bi-headset" style="font-size:1.8rem; color:#17a2b8;"></i>
                                </div>
                                <h5 class="fw-bold mb-2" style="color:#333;">Contact Support</h5>
                                <p class="text-muted mb-3">Get direct help from our support team for technical issues
                                </p>
                                <a href="#" class="btn btn-sm rounded-pill"
                                    style="background:#17a2b8; color:white; padding:8px 20px;">Contact Us</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="card border-0 shadow-sm" style="border-radius:16px;">
                    <div class="card-header bg-white border-0 p-4" style="border-radius:16px 16px 0 0;">
                        <h4 class="fw-bold mb-0" style="color:#333;">Frequently Asked Questions</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="accordion" id="helpAccordion">
                            <div class="accordion-item border-0 mb-3">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button bg-light rounded-3 shadow-sm fw-semibold"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                        aria-expanded="true" aria-controls="collapseOne"
                                        style="border:none; color:#333;">
                                        <i class="bi bi-grid-3x3-gap-fill me-2" style="color:#ff6b6b;"></i>
                                        How to use the Dashboard?
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show"
                                    aria-labelledby="headingOne" data-bs-parent="#helpAccordion">
                                    <div class="accordion-body bg-white rounded-3 mt-2 p-4"
                                        style="border-left:4px solid #ff6b6b;">
                                        <p class="text-muted mb-3">The dashboard provides a comprehensive overview of
                                            your tasks and activities. Here's how to navigate:</p>
                                        <ul class="text-muted">
                                            <li>Use the sidebar menu to access different sections</li>
                                            <li>View your task summary and recent activities</li>
                                            <li>Track your progress with visual indicators</li>
                                            <li>Access quick actions from the main dashboard</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 mb-3">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed bg-light rounded-3 shadow-sm fw-semibold"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                        aria-expanded="false" aria-controls="collapseTwo"
                                        style="border:none; color:#333;">
                                        <i class="bi bi-plus-circle-fill me-2" style="color:#28a745;"></i>
                                        How to add a new task?
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                    data-bs-parent="#helpAccordion">
                                    <div class="accordion-body bg-white rounded-3 mt-2 p-4"
                                        style="border-left:4px solid #28a745;">
                                        <p class="text-muted mb-3">Adding a new task is simple and straightforward:</p>
                                        <ol class="text-muted">
                                            <li>Navigate to the "My Task" section from the sidebar</li>
                                            <li>Click on the "Add New Task" button</li>
                                            <li>Fill in the task details (title, description, priority, deadline)</li>
                                            <li>Add any additional notes or attachments if needed</li>
                                            <li>Click "Save" to create your task</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 mb-3">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed bg-light rounded-3 shadow-sm fw-semibold"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                        aria-expanded="false" aria-controls="collapseThree"
                                        style="border:none; color:#333;">
                                        <i class="bi bi-gear-fill me-2" style="color:#b9770e;"></i>
                                        How to customize settings?
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse"
                                    aria-labelledby="headingThree" data-bs-parent="#helpAccordion">
                                    <div class="accordion-body bg-white rounded-3 mt-2 p-4"
                                        style="border-left:4px solid #b9770e;">
                                        <p class="text-muted mb-3">Personalize your experience by customizing your
                                            settings:</p>
                                        <ul class="text-muted">
                                            <li><strong>Profile Settings:</strong> Update your personal information and
                                                profile picture</li>
                                            <li><strong>Notification Preferences:</strong> Choose how and when you
                                                receive notifications</li>
                                            <li><strong>Theme Options:</strong> Select your preferred color scheme and
                                                layout</li>
                                            <li><strong>Privacy Settings:</strong> Manage your data sharing and privacy
                                                preferences</li>
                                            <li><strong>Account Security:</strong> Update passwords and enable
                                                two-factor authentication</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 mb-3">
                                <h2 class="accordion-header" id="headingFour">
                                    <button class="accordion-button collapsed bg-light rounded-3 shadow-sm fw-semibold"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour"
                                        aria-expanded="false" aria-controls="collapseFour"
                                        style="border:none; color:#333;">
                                        <i class="bi bi-exclamation-triangle-fill me-2" style="color:#ffc107;"></i>
                                        How to manage task priorities?
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                                    data-bs-parent="#helpAccordion">
                                    <div class="accordion-body bg-white rounded-3 mt-2 p-4"
                                        style="border-left:4px solid #ffc107;">
                                        <p class="text-muted mb-3">Task priorities help you organize and focus on what
                                            matters most:</p>
                                        <ul class="text-muted">
                                            <li><span class="badge bg-danger me-2">Extreme</span> Critical tasks that
                                                need immediate attention</li>
                                            <li><span class="badge bg-warning text-dark me-2">High</span> Important
                                                tasks with upcoming deadlines</li>
                                            <li><span class="badge bg-info me-2">Moderate</span> Regular tasks that can
                                                be scheduled</li>
                                            <li><span class="badge bg-success me-2">Low</span> Non-urgent tasks that can
                                                be done when time permits</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 mb-3">
                                <h2 class="accordion-header" id="headingFive">
                                    <button class="accordion-button collapsed bg-light rounded-3 shadow-sm fw-semibold"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive"
                                        aria-expanded="false" aria-controls="collapseFive"
                                        style="border:none; color:#333;">
                                        <i class="bi bi-shield-check-fill me-2" style="color:#17a2b8;"></i>
                                        How to keep my data secure?
                                    </button>
                                </h2>
                                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                                    data-bs-parent="#helpAccordion">
                                    <div class="accordion-body bg-white rounded-3 mt-2 p-4"
                                        style="border-left:4px solid #17a2b8;">
                                        <p class="text-muted mb-3">We take your data security seriously. Here are our
                                            security measures:</p>
                                        <ul class="text-muted">
                                            <li>All data is encrypted during transmission and storage</li>
                                            <li>Regular security audits and updates</li>
                                            <li>Two-factor authentication available</li>
                                            <li>Secure backup systems in place</li>
                                            <li>No data is shared with third parties without consent</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Section -->
                <div class="row mt-5">
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100"
                            style="border-radius:16px;background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <div class="card-body p-4 text-white">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-envelope-fill me-3" style="font-size:1.5rem;"></i>
                                    <h5 class="mb-0">Email Support</h5>
                                </div>
                                <p class="mb-3">Get help via email for detailed questions</p>
                                <p class="mb-0"><strong>support@easypi.com</strong></p>
                                <small class="opacity-75">Response within 24 hours</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100"
                            style="border-radius:16px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <div class="card-body p-4 text-white">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-chat-dots-fill me-3" style="font-size:1.5rem;"></i>
                                    <h5 class="mb-0">Chat with Pie-chan</h5>
                                </div>
                                <p class="mb-3">Get instant help from our support team</p>
                                <p class="mb-0"><strong>Available 24/7</strong></p>
                                <small class="opacity-75">Average response time: 5 seconds</small>
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
</html>