<?php
// ─────────────────────────────────────────
// SECRET ADMIN REGISTRATION PAGE
// This URL is never linked anywhere publicly
// Only people who know it exists can access it
// In production you would delete this file
// after creating your admin accounts
// ─────────────────────────────────────────

// Secret key — change this to something only you know
define('ADMIN_SECRET', 'zurimart2024');

require_once '../config/db.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $secret   = trim($_POST['secret_key']);
    $fullName = trim($_POST['full_name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Check secret key first
    if ($secret !== ADMIN_SECRET) {
        $message     = 'Invalid secret key.';
        $messageType = 'danger';
    } elseif (empty($fullName) || empty($email) || empty($password)) {
        $message     = 'All fields are required.';
        $messageType = 'danger';
    } elseif (strlen($password) < 8) {
        $message     = 'Password must be at least 8 characters.';
        $messageType = 'danger';
    } else {
        // Check if email already exists
        $check = mysqli_prepare($conn, 
            "SELECT id FROM users WHERE email = ?"
        );
        mysqli_stmt_bind_param($check, 's', $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $message     = 'An account with that email already exists.';
            $messageType = 'danger';
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert with role = admin
            $stmt = mysqli_prepare($conn,
                "INSERT INTO users (full_name, email, password, role) 
                 VALUES (?, ?, ?, 'admin')"
            );
            mysqli_stmt_bind_param($stmt, 'sss',
                $fullName, $email, $hashedPassword
            );

            if (mysqli_stmt_execute($stmt)) {
                $message     = 'Admin account created successfully! You can now login.';
                $messageType = 'success';
            } else {
                $message     = 'Failed to create account.';
                $messageType = 'danger';
            }
        }
    }
}

$pageTitle = 'Admin Registration | ZuriMart';
require_once '../includes/header.php';
?>

<div class="auth-page">
  <div class="auth-card">

    <div class="auth-banner">
      <div class="banner-icon">🔐</div>
      <h2>Zuri<span style="color:#f59e0b">Mart</span></h2>
      <p>Admin Account Registration</p>
    </div>

    <div class="auth-body">
      <h4>Create Admin Account</h4>
      <p class="subtitle">Restricted access — secret key required</p>

      <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> mb-3">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="register_admin.php">

        <div class="mb-3">
          <label class="form-label" for="secret_key">Secret Key</label>
          <input type="password" class="form-control" id="secret_key"
                 name="secret_key" placeholder="Enter admin secret key" required>
        </div>

        <div class="mb-3">
          <label class="form-label" for="full_name">Full Name</label>
          <input type="text" class="form-control" id="full_name"
                 name="full_name" placeholder="Enter full name" required>
        </div>

        <div class="mb-3">
          <label class="form-label" for="email">Email Address</label>
          <input type="email" class="form-control" id="email"
                 name="email" placeholder="Enter email" required>
        </div>

        <div class="mb-3">
          <label class="form-label" for="password">Password</label>
          <input type="password" class="form-control" id="password"
                 name="password" placeholder="Min 8 characters" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2">
          Create Admin Account
        </button>

      </form>

      <hr class="auth-divider">
      <p class="auth-footer-text">
        Already have an account? <a href="/shop/login.php">Login here</a>
      </p>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/shop/assets/js/main.js"></script>
</body>
</html>