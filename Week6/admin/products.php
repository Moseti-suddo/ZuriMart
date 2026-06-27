<?php
require_once '../includes/auth.php';
requireRole(['admin', 'manager']);
// Admins and managers can manage products
// Customers get redirected
?>



<?php
session_start();

// ─────────────────────────────────────────
// ADMIN PROTECTION
// If not logged in or not an admin,
// redirect to login page immediately
// ─────────────────────────────────────────
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /shop/login.php');
    exit;
}
?>

<?php
session_start();
require_once '../config/db.php';

// ─────────────────────────────────────────
// HANDLE DELETE
// If delete button is clicked, ?delete=ID
// appears in the URL — we catch it here
// ─────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Get image filename before deleting
    // so we can delete the file too
    $row = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT image FROM products WHERE id = $id"
    ));
    
    // Delete from database
    $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    
    // Delete image file from uploads/ if it exists
    if (!empty($row['image']) && file_exists('../uploads/' . $row['image'])) {
        unlink('../uploads/' . $row['image']);
    }
    
    header('Location: products.php?deleted=1');
    exit;
}

// Fetch all products with their category name using JOIN
$products = mysqli_query($conn,
    "SELECT products.*, categories.name AS category_name
     FROM products
     JOIN categories ON products.category_id = categories.id
     ORDER BY products.created_at DESC"
);

$pageTitle = 'Manage Products | ZuriMart Admin';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container mt-4">

  <!-- PAGE HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="section-title">Manage <span>Products</span></h4>
    <a href="add_product.php" class="btn btn-primary btn-sm">
      + Add New Product
    </a>
  </div>

  <!-- SUCCESS MESSAGES -->
  <?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success mb-4">Product deleted successfully.</div>
  <?php endif; ?>
  <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success mb-4">Product updated successfully.</div>
  <?php endif; ?>

  <!-- PRODUCTS TABLE -->
  <div class="card">
    <div class="card-body p-0">
      <table class="table table-dark table-hover mb-0">
        <thead>
          <tr style="border-bottom: 1px solid #2d2d44;">
            <th style="padding:1rem;">Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($products) === 0): ?>
            <tr>
              <td colspan="6" class="text-center py-4" style="color:#94a3b8;">
                No products found. 
                <a href="add_product.php">Add your first product</a>
              </td>
            </tr>
          <?php else: ?>
            <?php while ($p = mysqli_fetch_assoc($products)): ?>
              <tr style="border-bottom: 1px solid #2d2d44;">

                <!-- PRODUCT IMAGE -->
                <td style="padding:0.8rem;">
                  <?php if (!empty($p['image']) && 
                             file_exists('../uploads/' . $p['image'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($p['image']) ?>"
                         style="width:50px; height:50px; object-fit:cover;
                                border-radius:6px;">
                  <?php else: ?>
                    <div style="width:50px; height:50px; background:#1a0533;
                                border-radius:6px; display:flex;
                                align-items:center; justify-content:center;
                                font-size:1.5rem;">📦</div>
                  <?php endif; ?>
                </td>

                <!-- NAME -->
                <td style="vertical-align:middle;">
                  <?= htmlspecialchars($p['name']) ?>
                </td>

                <!-- CATEGORY -->
                <td style="vertical-align:middle; color:#94a3b8;">
                  <?= htmlspecialchars($p['category_name']) ?>
                </td>

                <!-- PRICE -->
                <td style="vertical-align:middle; color:#f59e0b; font-weight:600;">
                  KSh <?= number_format($p['price'], 2) ?>
                </td>

                <!-- STOCK -->
                <td style="vertical-align:middle;">
                  <?php if ($p['stock'] > 0): ?>
                    <span style="color:#22c55e;"><?= $p['stock'] ?></span>
                  <?php else: ?>
                    <span style="color:#ef4444;">Out of stock</span>
                  <?php endif; ?>
                </td>

                <!-- ACTION BUTTONS -->
                <td style="vertical-align:middle;">

                  <!-- EDIT — goes to edit_product.php with the product ID -->
                  <a href="edit_product.php?id=<?= $p['id'] ?>"
                     class="btn btn-sm btn-outline-primary me-1">
                    Edit
                  </a>

                  <!-- DELETE — links back to this page with ?delete=ID -->
                  <!-- onclick confirms before deleting -->
                  <a href="products.php?delete=<?= $p['id'] ?>"
                     class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Delete <?= htmlspecialchars($p['name']) ?>? This cannot be undone.')">
                    Delete
                  </a>

                </td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php require_once '../includes/footer.php'; ?>