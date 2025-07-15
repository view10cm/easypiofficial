<?php
session_start();
session_unset();
session_destroy();

// Optional: Clear cookies if you use "remember me"
// setcookie('your_cookie_name', '', time() - 3600, '/');

// Redirect to login page
header('Location: ../pages/sign_in.php');
exit;
