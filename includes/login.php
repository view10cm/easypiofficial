<?php
session_start(); // Must be at the top to set session
header('Content-Type: application/json');
require_once 'db_connection.php';

// Decode JSON input
$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

// Input validation
if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
    exit;
}

try {
    // Fetch user by username or email
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE username = :username OR email = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Validate password
    if ($user && password_verify($password, $user['password'])) {
        // ✅ Set session variable
        $_SESSION['account_id'] = $user['account_id'];

        echo json_encode(['success' => true, 'message' => 'Login successful.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>