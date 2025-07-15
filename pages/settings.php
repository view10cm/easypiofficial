<?php
session_start();
require '../includes/db_connection.php';

// If user is not logged in, redirect to sign-in
if (empty($_SESSION['account_id'])) {
    header('Location: ../pages/sign_in.php');
    exit;
}

try {
    // Fetch user data based on schema
    $stmt = $pdo->prepare("SELECT username, email, profile_picture FROM accounts WHERE account_id = :id LIMIT 1");
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
    $username = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
    $profilePicture = !empty($user['profile_picture']) && file_exists('../uploads/' . $user['profile_picture'])
        ? '../uploads/' . htmlspecialchars($user['profile_picture'], ENT_QUOTES, 'UTF-8')
        : '../assets/default_pp.jpg';

} catch (PDOException $e) {
    // On DB error, redirect to sign-in
    error_log('DB error: ' . $e->getMessage());
    header('Location: ../pages/sign_in.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_credentials'])) {
    try {
        // Validate and sanitize input
        $newUsername = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
        $newEmail = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8');

        // Check if email is valid
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $modalMessage = "Invalid email format.";
            $modalType = "error";
        } else {
            // Update user credentials in the database
            $updateStmt = $pdo->prepare("UPDATE accounts SET username = :username, email = :email WHERE account_id = :id");
            $updateStmt->execute([
                'username' => $newUsername,
                'email' => $newEmail,
                'id' => $_SESSION['account_id']
            ]);

            $modalMessage = "Credentials updated successfully!";
            $modalType = "success";
        }
    } catch (PDOException $e) {
        error_log('DB error: ' . $e->getMessage());
        $modalMessage = "An error occurred while updating credentials.";
        $modalType = "error";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
    try {
        // Validate and sanitize input
        $currentPassword = trim($_POST['current_password']);
        $newPassword = trim($_POST['new_password']);
        $confirmPassword = trim($_POST['confirm_password']);

        // Check if new passwords match
        if ($newPassword !== $confirmPassword) {
            $modalMessage = "New passwords do not match.";
            $modalType = "error";
        } else {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM accounts WHERE account_id = :id LIMIT 1");
            $stmt->execute(['id' => $_SESSION['account_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($currentPassword, $user['password'])) {
                // Update password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("UPDATE accounts SET password = :password WHERE account_id = :id");
                $updateStmt->execute([
                    'password' => $hashedPassword,
                    'id' => $_SESSION['account_id']
                ]);

                $modalMessage = "Password updated successfully!";
                $modalType = "success";
            } else {
                $modalMessage = "Current password is incorrect.";
                $modalType = "error";
            }
        }
    } catch (PDOException $e) {
        error_log('DB error: ' . $e->getMessage());
        $modalMessage = "An error occurred while updating the password.";
        $modalType = "error";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_picture'])) {
    try {
        $uploadDir = '../uploads/';

        // Check if the uploads directory exists, create it if not
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
            $fileName = $_FILES['profile_picture']['name'];
            $fileSize = $_FILES['profile_picture']['size'];
            $fileType = $_FILES['profile_picture']['type'];

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($fileType, $allowedTypes)) {
                $modalMessage = "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
                $modalType = "error";
            } elseif ($fileSize > 2 * 1024 * 1024) { // 2MB limit
                $modalMessage = "File size exceeds the limit of 2MB.";
                $modalType = "error";
            } else {
                $newFileName = uniqid() . '-' . basename($fileName);
                $destPath = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    // Resize image based on user selection
                    $resizeOption = $_POST['resize_options'];
                    if ($resizeOption !== 'original') {
                        list($width, $height) = getimagesize($destPath);
                        $newWidth = $resizeOption === 'small' ? 100 : ($resizeOption === 'medium' ? 300 : 500);
                        $newHeight = $newWidth; // Assuming square resize

                        $imageResource = imagecreatefromstring(file_get_contents($destPath));
                        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                        imagecopyresampled($resizedImage, $imageResource, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                        if ($fileType === 'image/jpeg') {
                            imagejpeg($resizedImage, $destPath);
                        } elseif ($fileType === 'image/png') {
                            imagepng($resizedImage, $destPath);
                        } elseif ($fileType === 'image/gif') {
                            imagegif($resizedImage, $destPath);
                        }

                        imagedestroy($imageResource);
                        imagedestroy($resizedImage);
                    }

                    // Update profile picture in the database
                    $updateStmt = $pdo->prepare("UPDATE accounts SET profile_picture = :profile_picture WHERE account_id = :id");
                    $updateStmt->execute([
                        'profile_picture' => $newFileName,
                        'id' => $_SESSION['account_id']
                    ]);

                    $modalMessage = "Profile picture updated successfully!";
                    $modalType = "success";
                } else {
                    $modalMessage = "Failed to upload the file.";
                    $modalType = "error";
                }
            }
        } else {
            $modalMessage = "No file uploaded or an error occurred.";
            $modalType = "error";
        }
    } catch (PDOException $e) {
        error_log('DB error: ' . $e->getMessage());
        $modalMessage = "An error occurred while updating the profile picture.";
        $modalType = "error";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_picture'])) {
    try {
        $updateStmt = $pdo->prepare("UPDATE accounts SET profile_picture = NULL WHERE account_id = :id");
        $updateStmt->execute(['id' => $_SESSION['account_id']]);

        $modalMessage = "Profile picture removed successfully!";
        $modalType = "success";
    } catch (PDOException $e) {
        error_log('DB error: ' . $e->getMessage());
        $modalMessage = "An error occurred while removing the profile picture.";
        $modalType = "error";
    }
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
      <div style="margin-left:20px; width:calc(100% - 320px);">
        <div class="container-fluid p-4">
          <!-- Header Section -->
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Account Information</h4>
          

          </div>

          <!-- Profile Info Section -->
          <div class="d-flex align-items-center mb-4 position-relative">
            <div class="position-relative" style="width: 60px; height: 60px;">
              <img src="<?php echo !empty($profilePicture) ? $profilePicture : '../assets/default_pp.jpg'; ?>" alt="Profile" class="rounded-circle"
                style="width:100%; height:100%; object-fit:cover;">
              <button class="btn btn-sm position-absolute" style="bottom: 0; right: 0; background: none; border: none; color: white;" data-bs-toggle="modal" data-bs-target="#editPictureModal">
                <i class="bi bi-pencil"></i>
              </button>
            </div>
            <div class="ms-3">
              <h6 class="mb-1"><?php echo $username; ?></h6>
              <p class="text-muted mb-0 small"><?php echo $email; ?></p>
            </div>
          </div>

          <!-- Tabs Navigation -->
          <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active small" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal"
                type="button" role="tab" aria-controls="personal" aria-selected="true">
                <i class="bi bi-person me-2"></i>Personal Information
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link small" id="password-tab" data-bs-toggle="tab" data-bs-target="#password"
                type="button" role="tab" aria-controls="password" aria-selected="false">
                <i class="bi bi-lock me-2"></i>Password & Security
              </button>
            </li>
          </ul>

          <!-- Tab Content -->
          <div class="tab-content" id="settingsTabsContent">
            <!-- Personal Information Tab -->
            <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
              <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                  <form method="post" action="">
                    <div class="mb-3">
                      <label for="username" class="form-label fw-semibold small">Username</label>
                      <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>">
                    </div>
                    <div class="mb-3">
                      <label for="email" class="form-label fw-semibold small">Email Address</label>
                      <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>">
                    </div>
            
                    <div class="d-flex gap-3">
                      <button type="submit" name="update_credentials" class="btn btn-primary btn-sm px-4" style="background-color: #1286cc; border-color: #1286cc;">
                        <i class="bi bi-check2 me-2"></i>Save Changes
                      </button>
                      <button type="reset" class="btn btn-outline-secondary btn-sm px-4">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Password & Security Tab -->
            <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
              <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                  <form method="post" action="">
                    <div class="mb-4">
                      <h6 class="fw-semibold mb-3">Change Password</h6>
                      <div class="mb-3">
                        <label for="currentPassword" class="form-label fw-semibold small">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" name="current_password" placeholder="Enter current password" required>
                      </div>
                      <div class="mb-3">
                        <label for="newPassword" class="form-label fw-semibold small">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="new_password" placeholder="Enter new password" required>
                      </div>
                      <div class="mb-3">
                        <label for="confirmPassword" class="form-label fw-semibold small">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password" placeholder="Confirm new password" required>
                      </div>
                    </div>


                    <div class="d-flex gap-3">
                      <button type="submit" name="update_password" class="btn btn-primary btn-sm px-4" style="background-color: #1286cc; border-color: #1286cc;">
                        <i class="bi bi-shield-check me-2"></i>Update Security
                      </button>
                      <button type="reset" class="btn btn-outline-secondary btn-sm px-4">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                      </button>
                    </div>
                  </form>
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

  <!-- Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="feedbackModalLabel">
          <?php echo isset($modalType) && $modalType === "success" ? "Success" : "Error"; ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php echo isset($modalMessage) ? $modalMessage : ""; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Editing Profile Picture -->
<div class="modal fade" id="editPictureModal" tabindex="-1" aria-labelledby="editPictureModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPictureModalLabel">Edit Profile Picture</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="post" action="" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="profile_picture" class="form-label">Upload Picture</label>
            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
          </div>
          <button type="submit" name="update_picture" class="btn btn-primary">Save Changes</button>
          <button type="submit" name="remove_picture" class="btn btn-danger">Remove Picture</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  // Show modal if there's a message
  <?php if (isset($modalMessage)) { ?>
    var feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
    feedbackModal.show();
  <?php } ?>
</script>
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