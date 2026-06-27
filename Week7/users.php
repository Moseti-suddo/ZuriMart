<?php
session_start();

// Only admins can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /shop/login.php');
    exit;
}

require_once '../config/db.php';

if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);

    // Prevent admin from deleting their own account
    if ($delete_id === (int)$_SESSION['user_id']) {
        header('Location: users.php?error=selfdelete');
        exit;
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location: users.php?deleted=1');
    exit;
}


$createError   = '';
$createSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $password  = $_POST['password'];
    $role      = $_POST['role'];

    $allowed_roles = ['admin', 'manager', 'customer'];

    if (!in_array($role, $allowed_roles)) {
        $createError = 'Invalid role selected';
    } elseif (empty($full_name) || empty($email) || empty($password)) {
        $createError = 'All fields are required';
    } else {
        // Check email not already taken
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $createError = 'Email already registered';
            mysqli_stmt_close($stmt);
        } else {
            mysqli_stmt_close($stmt);

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($conn,
                "INSERT INTO users (full_name, email, password, role) 
                 VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'ssss',
                $full_name, $email, $hashedPassword, $role);

            if (mysqli_stmt_execute($stmt)) {
                $createSuccess = 'User created successfully';
            } else {
                $createError = 'Failed to create user';
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// ========================================
// FETCH ALL USERS
// ========================================
$users = mysqli_query($conn,
    "SELECT id, full_name, email, role, created_at 
     FROM users ORDER BY created_at DESC");

$pageTitle = 'Manage Users | ZuriMart Admin';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container mt-4">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="section-title">👥 Manage Users</h4>
        <span style="color:#94a3b8;"><?= date('l, d F Y') ?></span>
    </div>

    <!-- ALERTS -->
    <?php if ($createError): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($createError) ?></div>
    <?php endif; ?>
    <?php if ($createSuccess): ?>
        <div class="alert alert-success"><?= htmlspecialchars($createSuccess) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">User deleted successfully.</div>
    <?php endif; ?>
    <?php if (isset($_GET['error']) && $_GET['error'] === 'selfdelete'): ?>
        <div class="alert alert-danger">You cannot delete your own account.</div>
    <?php endif; ?>

    <!-- CREATE USER FORM -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <h6 style="color:#94a3b8; margin-bottom:1rem;">Create New User</h6>
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="full_name"
                               class="form-control"
                               placeholder="Full Name" required>
                    </div>
                    <div class="col-md-3">
                        <input type="email" name="email"
                               class="form-control"
                               placeholder="Email Address" required>
                    </div>
                    <div class="col-md-2">
                        <input type="password" name="password"
                               class="form-control"
                               placeholder="Password" required>
                    </div>
                    <div class="col-md-2">
                        <select name="role" class="form-select" required>
                            <option value="">Select Role</option>
                            <option value="admin">Administrator</option>
                            <option value="manager">Manager</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            Create User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- USERS TABLE -->
    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="p-4 pb-2">
                <h6 style="color:#94a3b8;">All Users</h6>
            </div>
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr style="border-bottom:1px solid #2d2d44;">
                        <th style="padding:1rem;">#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($user = mysqli_fetch_assoc($users)): ?>
                    <tr style="border-bottom:1px solid #2d2d44;">
                        <td style="padding:0.8rem;"><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td style="color:#94a3b8;">
                            <?= htmlspecialchars($user['email']) ?>
                        </td>
                        <td>
                            <?php
                            // Colour coded role badges matching brand colours
                            $badgeStyle = match($user['role']) {
                                'admin'    => 'background:#ef4444;',
                                'manager'  => 'background:#f59e0b; color:#0f0f13;',
                                'customer' => 'background:#22c55e;',
                                default    => 'background:#94a3b8;'
                            };
                            ?>
                            <span class="badge" style="<?= $badgeStyle ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td style="color:#94a3b8;">
                            <?= date('d M Y', strtotime($user['created_at'])) ?>
                        </td>
                        <td>
                            <?php if ($user['id'] !== (int)$_SESSION['user_id']): ?>
                                <a href="users.php?delete=<?= $user['id'] ?>"
                                   class="btn btn-sm"
                                   style="background:#ef4444; color:white;"
                                   onclick="return confirm('Delete <?= htmlspecialchars($user['full_name']) ?>? This cannot be undone.')">
                                   🗑️ Delete
                                </a>
                            <?php else: ?>
                                <span style="color:#94a3b8; font-size:0.85rem;">
                                    Current user
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- BACK BUTTON -->
    <div class="mb-4">
        <a href="dashboard.php" class="btn btn-outline-primary">
            ← Back to Dashboard
        </a>
    </div>

</div>

<?php require_once '../includes/footer.php'; ?>