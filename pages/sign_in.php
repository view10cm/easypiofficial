<?php
session_start();
if (!empty($_SESSION['account_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EasyPi - Sign In</title>
  <link rel="icon" type="image/png" href="../assets/titlelogo.png">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="../css/sign_in.css">
</head>
<body>
<div class="background-pattern"></div>

  <div class="login-container">
    <div class="row g-0">
      <div class="col-lg-7 left-side">
        <h2 class="form-title">Login</h2>
        <form id="loginForm">
          <div class="form-group">
            <i class="fas fa-user form-icon"></i>
            <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username" required>
          </div>
          <div class="form-group">
            <i class="fas fa-lock form-icon"></i>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="rememberMe">
            <label class="form-check-label" for="rememberMe">Remember Me</label>
          </div>
          <button type="submit" class="btn btn-login" id="loginBtn">Login</button>
          <div class="register-link">
            Don’t have an account? <a href="signup_form.php">Create One</a>
          </div>
        </form>
      </div>
      <div class="col-lg-5 right-side d-none d-lg-flex">
        <img src="../assets/login.png" alt="Illustration">
      </div>
    </div>
  </div>
<script type="module" src="../scripts/sign_in.js"></script>
</body>
</html>