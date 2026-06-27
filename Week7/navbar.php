<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isAdmin    = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$isManager  = isset($_SESSION['role']) && $_SESSION['role'] === 'manager';
$isLoggedIn = isset($_SESSION['user_id']);
?>

<nav class="navbar navbar-expand-md navbar-dark">
  <div class="container">

    <a class="navbar-brand" href="/shop/index.php">🛍️ ZuriMart</a>

    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">

      <?php if ($isAdmin): ?>
        <!-- ADMIN NAVBAR -->
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="/shop/admin/dashboard.php">📊 Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/shop/admin/products.php">📦 Products</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/shop/admin/add_product.php">➕ Add</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/shop/admin/users.php">👥 Users</a>
          </li>
        </ul>
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="/shop/index.php" style="color:#f59e0b;">
              🛍️ Store
            </a>
          </li>
          <li class="nav-item">
            <span class="nav-link" style="color:#94a3b8;">
              👤 <?= htmlspecialchars($_SESSION['user_name']) ?>
            </span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/shop/logout.php" style="color:#ef4444;">
              Logout
            </a>
          </li>
        </ul>

      <?php elseif ($isManager): ?>
        <!-- MANAGER NAVBAR -->
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="/shop/admin/manager_dashboard.php">
              📊 Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/shop/admin/products.php">📦 Products</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/shop/admin/add_product.php">➕ Add</a>
          </li>
        </ul>
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="/shop/index.php" style="color:#f59e0b;">
              🛍️ Store
            </a>
          </li>
          <li class="nav-item">
            <span class="nav-link" style="color:#94a3b8;">
              👤 <?= htmlspecialchars($_SESSION['user_name']) ?>
            </span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/shop/logout.php" style="color:#ef4444;">
              Logout
            </a>
          </li>
        </ul>

      <?php elseif ($isLoggedIn): ?>
        <!-- CUSTOMER NAVBAR -->
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="/shop/index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/shop/pages/products.php">Products</a>
          </li>
        </ul>
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="/shop/pages/cart.php">
              🛒 Cart 
              <span class="badge bg-danger cart-badge" style="display:none;">0</span>
            </a>
          </li>
          <li class="nav-item">
            <span class="nav-link" style="color:#94a3b8;">
              👤 <?= htmlspecialchars($_SESSION['user_name']) ?>
            </span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/shop/logout.php" style="color:#ef4444;">
              Logout
            </a>
          </li>
        </ul>

      <?php else: ?>
        <!-- GUEST NAVBAR -->
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="/shop/index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/shop/pages/products.php">Products</a>
          </li>
        </ul>
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="/shop/pages/cart.php">
              🛒 Cart
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/shop/login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/shop/register.php">Register</a>
          </li>
        </ul>

      <?php endif; ?>

    </div>
  </div>
</nav>
