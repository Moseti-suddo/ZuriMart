<?php
require_once '../includes/auth.php';
requireRole('admin');
// Only admins reach this point
// Everyone else gets redirected to denied.php
?>



<?php
$pageTitle = 'Dashboard | ZuriMart Admin';
require_once '../includes/header.php';
?>

<div class="d-flex" style="min-height: 100vh;">

    <!-- SIDEBAR -->
    <div class="bg-dark text-white p-3" style="width: 220px; min-height: 100vh;">
        <h5 class="text-center mb-4">⚙️ Admin Panel</h5>
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="dashboard.php" class="nav-link text-white active">
                    📊 Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="products.php" class="nav-link text-white">
                    📦 Products
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="orders.php" class="nav-link text-white">
                    🧾 Orders
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="users.php" class="nav-link text-white">
                    👥 Users
                </a>
            </li>
            <li class="nav-item mt-4">
                <a href="../logout.php" class="nav-link text-danger">
                    🚪 Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- MAIN CONTENT -->
    <div class="flex-grow-1 p-4 bg-light">

        <!-- Top bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Welcome, Admin 👋</h4>
            <span class="text-muted"><?php echo date('l, d F Y'); ?></span>
        </div>

        <!-- STAT CARDS -->
        <div class="row g-3 mb-4">

            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Total Sales</h6>
                        <h3 class="text-success">KSh 0</h3>
                        <small class="text-muted">All time revenue</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Orders Today</h6>
                        <h3 class="text-primary">0</h3>
                        <small class="text-muted">Pending fulfilment</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Total Products</h6>
                        <h3 class="text-warning">0</h3>
                        <small class="text-muted">In catalog</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Registered Users</h6>
                        <h3 class="text-danger">0</h3>
                        <small class="text-muted">Total customers</small>
                    </div>
                </div>
            </div>

        </div>

        <!-- SALES CHART -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="mb-3">Sales Overview</h6>
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>

        <!-- RECENT ORDERS TABLE -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="mb-3">Recent Orders</h6>
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No orders yet
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales chart setup
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Sales (KSh)',
                data: [0, 0, 0, 0, 0, 0, 0],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>