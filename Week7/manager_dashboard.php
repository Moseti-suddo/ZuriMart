<?php
session_start();

// Only managers can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header('Location: /shop/login.php');
    exit;
}

require_once '../config/db.php';

// Total products
$totalProducts = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS count FROM products")
)['count'];

// Total orders
$totalOrders = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS count FROM orders")
)['count'];

// Pending orders
$pendingOrders = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS count FROM orders WHERE status = 'pending'")
)['count'];

// Total revenue
$totalRevenue = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(total_amount) AS total FROM orders WHERE status != 'cancelled'")
)['total'] ?? 0;

// Recent orders
$recentOrders = mysqli_query($conn,
    "SELECT orders.id, users.full_name, orders.total_amount,
            orders.status, orders.created_at
     FROM orders
     JOIN users ON orders.user_id = users.id
     ORDER BY orders.created_at DESC
     LIMIT 5"
);

$pageTitle = 'Manager Dashboard | ZuriMart';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container mt-4">

    <!-- WELCOME BAR -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="section-title">
            Welcome, <span><?= htmlspecialchars($_SESSION['user_name']) ?></span> 👋
        </h4>
        <span style="color:#94a3b8;"><?= date('l, d F Y') ?></span>
    </div>

    <!-- MANAGER NOTICE -->
    <div class="alert" style="background:#1a1a2e; border:1px solid #f59e0b; color:#f59e0b;">
        🔑 You are logged in as <strong>Store Manager</strong>. 
        You can manage products and orders. 
        User management is restricted to Administrators.
    </div>

    <!-- STAT CARDS -->
    <div class="row g-3 mb-4">

        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div style="font-size:2rem;">💰</div>
                <h6 style="color:#94a3b8; margin-top:0.5rem;">Total Revenue</h6>
                <h3 style="color:#22c55e; font-weight:700;">
                    KSh <?= number_format($totalRevenue, 2) ?>
                </h3>
                <small style="color:#94a3b8;">All time</small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div style="font-size:2rem;">🧾</div>
                <h6 style="color:#94a3b8; margin-top:0.5rem;">Total Orders</h6>
                <h3 style="color:#7c3aed; font-weight:700;">
                    <?= $totalOrders ?>
                </h3>
                <small style="color:#94a3b8;">All orders</small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div style="font-size:2rem;">⏳</div>
                <h6 style="color:#94a3b8; margin-top:0.5rem;">Pending Orders</h6>
                <h3 style="color:#f59e0b; font-weight:700;">
                    <?= $pendingOrders ?>
                </h3>
                <small style="color:#94a3b8;">Needs attention</small>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div style="font-size:2rem;">📦</div>
                <h6 style="color:#94a3b8; margin-top:0.5rem;">Total Products</h6>
                <h3 style="color:#ef4444; font-weight:700;">
                    <?= $totalProducts ?>
                </h3>
                <small style="color:#94a3b8;">In catalog</small>
            </div>
        </div>

    </div>

    <!-- RECENT ORDERS TABLE -->
    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="p-4 pb-2">
                <h6 style="color:#94a3b8;">Recent Orders</h6>
            </div>
            <table class="table table-dark table-hover mb-0">
                <thead>
                    <tr style="border-bottom:1px solid #2d2d44;">
                        <th style="padding:1rem;">#</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($recentOrders) === 0): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4" 
                                style="color:#94a3b8;">
                                No orders yet
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php while ($order = mysqli_fetch_assoc($recentOrders)): ?>
                            <tr style="border-bottom:1px solid #2d2d44;">
                                <td style="padding:0.8rem;">#<?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['full_name']) ?></td>
                                <td style="color:#f59e0b;">
                                    KSh <?= number_format($order['total_amount'], 2) ?>
                                </td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'pending'   => '#f59e0b',
                                        'completed' => '#22c55e',
                                        'cancelled' => '#ef4444',
                                    ];
                                    $color = $statusColors[$order['status']] ?? '#94a3b8';
                                    ?>
                                    <span style="color:<?= $color ?>; font-weight:600;">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td style="color:#94a3b8;">
                                    <?= date('d M Y', strtotime($order['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- QUICK ACTIONS — manager has products and orders, no users -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="/shop/admin/add_product.php" 
               class="btn btn-primary w-100 py-3">
                ➕ Add New Product
            </a>
        </div>
        <div class="col-md-4">
            <a href="/shop/admin/products.php" 
               class="btn btn-outline-primary w-100 py-3">
                📦 Manage Products
            </a>
        </div>
        <div class="col-md-4">
            <a href="/shop/index.php" class="btn w-100 py-3"
               style="background:#1a1a2e; border:1px solid #2d2d44; color:#f59e0b;">
                🛍️ View Store
            </a>
        </div>
    </div>

</div>

<?php require_once '../includes/footer.php'; ?>