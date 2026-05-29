<?php
// Start the session — must be first line before any output
session_start();

require_once '../config/db.php';

// Get JSON data from JavaScript
$data     = json_decode(file_get_contents('php://input'), true);
$email    = trim($data['email']);
$password = $data['password'];

// Basic server-side validation
if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Look up user by email using prepared statement
$stmt = mysqli_prepare($conn, "SELECT id, full_name, password, role FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {

    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $email;
    $_SESSION['role']      = $user['role'];

    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'role'    => $user['role']
    ]);

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>