<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /shop/login.php');
    exit;
}

// ─────────────────────────────────────────
// FETCH REAL STATS FROM DATABASE
// ─────────────────────────────────────────
require_once '../config/db.php';

// Total products
$totalProducts = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS count FROM products")
)['count'];

// Total registered users
$totalUsers = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS count FROM users")
)['count'];

// Total orders
$totalOrders = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS count FROM orders")
)['count'];

// Total revenue
$totalRevenue = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(total_amount) AS total FROM orders WHERE status != 'cancelled'")
)['total'] ?? 0;

// Recent orders with customer name
$recentOrders = mysqli_query($conn,
    "SELECT orders.id, users.full_name, orders.total_amount, 
            orders.status, orders.created_at
     FROM orders
     JOIN users ON orders.user_id = users.id
     ORDER BY orders.created_at DESC
     LIMIT 5"
);

$pageTitle = 'Dashboard | ZuriMart Admin';
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
        <div style="font-size:2rem;">📦</div>
        <h6 style="color:#94a3b8; margin-top:0.5rem;">Total Products</h6>
        <h3 style="color:#f59e0b; font-weight:700;">
          <?= $totalProducts ?>
        </h3>
        <small style="color:#94a3b8;">In catalog</small>
      </div>
    </div>

    <div class="col-6 col-md-3">
      <div class="card text-center p-3">
        <div style="font-size:2rem;">👥</div>
        <h6 style="color:#94a3b8; margin-top:0.5rem;">Registered Users</h6>
        <h3 style="color:#ef4444; font-weight:700;">
          <?= $totalUsers ?>
        </h3>
        <small style="color:#94a3b8;">Total customers</small>
      </div>
    </div>

  </div>

  <!-- SALES CHART -->
  <div class="card mb-4">
    <div class="card-body p-4">
      <h6 style="color:#94a3b8; margin-bottom:1rem;">Sales Overview</h6>
      <canvas id="salesChart" height="80"></canvas>
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
              <td colspan="5" class="text-center py-4" style="color:#94a3b8;">
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

  <!-- QUICK ACTION BUTTONS -->
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <a href="/shop/admin/add_product.php" class="btn btn-primary w-100 py-3">
        ➕ Add New Product
      </a>
    </div>
    <div class="col-md-4">
      <a href="/shop/admin/products.php" class="btn btn-outline-primary w-100 py-3">
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            label: 'Sales (KSh)',
            data: [0, 0, 0, 0, 0, 0, 0],
            borderColor: '#7c3aed',
            backgroundColor: 'rgba(124, 58, 237, 0.1)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#f59e0b',
            pointBorderColor: '#f59e0b',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { color: '#94a3b8' }, grid: { color: '#2d2d44' } },
            y: { ticks: { color: '#94a3b8' }, grid: { color: '#2d2d44' } }
        }
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>