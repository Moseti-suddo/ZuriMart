<?php
require_once '../config/db.php';

// Getting the JSON data sent from JavaScript
$data = json_decode(file_get_contents('php://input'), true);

$fullName = trim($data['fullName']);
$email    = trim($data['email']);
$password = $data['password'];

if (empty($fullName) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    echo json_encode(['success' => false, 'message' => 'This email is already registered']);
    exit;
}
mysqli_stmt_close($stmt);

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'sss', $fullName, $email, $hashedPassword);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Account created successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>