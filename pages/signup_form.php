<?php
session_start();
if (!empty($_SESSION['account_id'])) {
    header('Location: dashboard.php'); // or wherever your homepage is
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EasyPi - Sign Up</title>
  <link rel="icon" type="image/png" href="../assets/titlelogo.png">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="../css/sign_up.css">
</head>
<body>
  <div class="background-pattern"></div>

  <div class="signup-container">
    <div class="row g-0">
      <div class="col-lg-5 left-side d-none d-lg-flex">
        <img src="../assets/register.png" alt="Illustration" class="left-side-img">
      </div>
      <div class="col-lg-7 right-side">
        <h2 class="form-title">Sign Up</h2>
        <form id="signupForm">
          <div class="form-group">
            <i class="fas fa-user-circle form-icon"></i>
            <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username" required>
          </div>
          <div class="form-group">
            <i class="fas fa-envelope form-icon"></i>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" required>
          </div>
          <div class="form-group">
            <i class="fas fa-lock form-icon"></i>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
          </div>
          <div class="form-group">
            <i class="fas fa-lock form-icon"></i>
            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="agreeTerms" required>
            <label class="form-check-label" for="agreeTerms">I agree to all terms</label>
          </div>
          <button type="submit" class="btn btn-register" id="registerBtn">Register</button>
          <div class="signin-link">
            Already have an account? <a href="sign_in.html">Sign In</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script type="module" src="../scripts/sign_up.js"></script>
</body>
</html>