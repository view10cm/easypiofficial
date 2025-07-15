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
        : '../assets/img/placeholder.png';

} catch (PDOException $e) {
    error_log('DB error: ' . $e->getMessage());
    header('Location: ../pages/sign_in.php');
    exit;
}
?>

<aside class="sidebar d-flex flex-column align-items-center py-4">
    <div class="mb-3 text-center">
        <img 
            src="<?= $profilePicture ?>" 
            alt="Profile Picture" 
            class="rounded-circle"
            style="width:80px; height:80px; object-fit:cover; border:4px solid #fff;"
        >
        <div class="text-white-50" style="font-size:0.95rem;">
            <?= $email ?>
        </div>
    </div>

    <nav class="w-100 mt-4 flex-grow-1">
        <ul class="nav flex-column gap-2 px-3">
            <li>
                <a class="nav-link rounded-3 d-flex align-items-center px-3 py-2" href="dashboard.php">
                   <i class="bi bi-grid-fill me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a class="nav-link d-flex align-items-center px-3 py-2" href="vitalTask.php">
                   <i class="bi bi-exclamation-circle me-2"></i> Vital Task
                </a>
            </li>
            <li>
                <a class="nav-link d-flex align-items-center px-3 py-2" href="myTask.php">
                   <i class="bi bi-check2-square me-2"></i> My Task
                </a>
            </li>
            <li>
                <a class="nav-link d-flex align-items-center px-3 py-2" href="taskCategories.php">
                   <i class="bi bi-list-ul me-2"></i> Task Categories
                </a>
            </li>
            <li>
                <a class="nav-link d-flex align-items-center px-3 py-2" href="settings.php">
                   <i class="bi bi-gear me-2"></i> Settings
                </a>
            </li>
            <li>
                <a class="nav-link d-flex align-items-center px-3 py-2" href="help.php">
                   <i class="bi bi-question-circle me-2"></i> Help
                </a>
            </li>
        </ul>
    </nav>

    <div class="w-100 px-3 mb-2">
        <a class="nav-link d-flex align-items-center px-3 py-2" href="../includes/logout.php">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
    </div>
</aside>
