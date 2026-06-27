<?php
require_once '../includes/auth.php';
requireRole('admin');
require_once '../config/db.php';
require_once '../includes/header.php';


if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    
    // Prevent admin from deleting themselves
    if ($delete_id === $_SESSION['user_id']) {
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

    // Validate role — only accept known roles
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
        } else {
            mysqli_stmt_close($stmt);
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = mysqli_prepare($conn, 
                "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'ssss', 
                $full_name, $email, $hashedPassword, $role);
            
            if (mysqli_stmt_execute($stmt)) {
                $createSuccess = 'User created successfully';
            } else {
                $createError = 'Failed to create user';
            }
        }
        mysqli_stmt_close($stmt);
    }
}


$users = mysqli_query($conn, 
    "SELECT id, full_name, email, role, created_at 
     FROM users ORDER BY created_at DESC");
?>

<div class="d-flex" style="min-height: 100vh;">

    <!-- SIDEBAR -->
    <div class="bg-dark text-white p-3" style="width: 220px; min-height: 100vh;">
        <h5 class="text-center mb-4">⚙️ Admin Panel</h5>
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="dashboard.php" class="nav-link text-white">📊 Dashboard</a>
            </li>
            <li class="nav-item mb-2">
                <a href="products.php" class="nav-link text-white">📦 Products</a>
            </li>
            <li class="nav-item mb-2">
                <a href="orders.php" class="nav-link text-white">🧾 Orders</a>
            </li>
            <li class="nav-item mb-2">
                <a href="users.php" class="nav-link text-white active">👥 Users</a>
            </li>
            <li class="nav-item mt-4">
                <a href="../logout.php" class="nav-link text-danger">🚪 Logout</a>
            </li>
        </ul>
    </div>

    <!-- MAIN CONTENT -->
    <div class="flex-grow-1 p-4 bg-light">

        <h4 class="mb-4">👥 Manage Users</h4>

        <!-- CREATE USER FORM -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h6 class="mb-3">Create New User</h6>

                <?php if ($createError): ?>
                    <div class="alert alert-danger"><?php echo $createError; ?></div>
                <?php endif; ?>
                <?php if ($createSuccess): ?>
                    <div class="alert alert-success"><?php echo $createSuccess; ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['deleted'])): ?>
                    <div class="alert alert-success">User deleted successfully.</div>
                <?php endif; ?>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'selfdelete'): ?>
                    <div class="alert alert-danger">You cannot delete your own account.</div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="full_name" 
                                   class="form-control" placeholder="Full Name" required>
                        </div>
                        <div class="col-md-3">
                            <input type="email" name="email" 
                                   class="form-control" placeholder="Email" required>
                        </div>
                        <div class="col-md-2">
                            <input type="password" name="password" 
                                   class="form-control" placeholder="Password" required>
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
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($user = mysqli_fetch_assoc($users)): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <?php
                                // Colour coded role badges
                                $badgeClass = match($user['role']) {
                                    'admin'    => 'bg-danger',
                                    'manager'  => 'bg-warning text-dark',
                                    'customer' => 'bg-success',
                                    default    => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                    <a href="users.php?delete=<?php echo $user['id']; ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this user?')">
                                       🗑️ Delete
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Current user</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>