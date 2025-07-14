<?php
// Include the database connection
require_once 'db_connection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get form inputs and sanitize
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validate inputs
if (!$username || !$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit;
}

// Check for duplicate username or email
$stmt = $pdo->prepare("SELECT account_id FROM accounts WHERE username = ? OR email = ?");
$stmt->execute([$username, $email]);

if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Username or email already exists.']);
    exit;
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Insert user into database
$stmt = $pdo->prepare("INSERT INTO accounts (username, email, password) VALUES (?, ?, ?)");
$success = $stmt->execute([$username, $email, $hashedPassword]);

if ($success) {
    $lastId = $pdo->lastInsertId();

    // Get the account creation time
    $stmt = $pdo->prepare("SELECT date_created FROM accounts WHERE account_id = ?");
    $stmt->execute([$lastId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully.',
        'account_id' => $lastId,
        'date_created' => $row['date_created'] ?? null
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create account.']);
}
?>
