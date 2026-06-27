<?php
$pageTitle = 'Products | ZuriMart';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
require_once '../config/db.php';

// ─────────────────────────────────────────
// STEP 1: GET THE CATEGORY FILTER
// If a category_id is in the URL (?category=2), use it.
// Otherwise default to 0 which means "show all"
// ─────────────────────────────────────────
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// ─────────────────────────────────────────
// STEP 2: FETCH ALL CATEGORIES
// We need this to build the filter buttons at the top
// ─────────────────────────────────────────
$catResult = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

// ─────────────────────────────────────────
// STEP 3: FETCH PRODUCTS — filtered or all
// If a category was selected, add a WHERE clause
// If not, fetch everything
// ─────────────────────────────────────────
if ($categoryId > 0) {
    $stmt = mysqli_prepare($conn, 
        "SELECT * FROM products WHERE category_id = ? ORDER BY created_at DESC"
    );
    mysqli_stmt_bind_param($stmt, "i", $categoryId);
    mysqli_stmt_execute($stmt);
    $products = mysqli_stmt_get_result($stmt);
} else {
    $products = mysqli_query($conn, 
        "SELECT * FROM products ORDER BY created_at DESC"
    );
}
?>

<div class="container mt-4">

  <!-- PAGE HEADER + SEARCH BAR -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="section-title">Our <span>Products</span></h4>
    <div class="input-group" style="width: 300px;">
      <input type="text" class="form-control" id="searchInput" 
             placeholder="Search products...">
      <button class="btn btn-primary" onclick="searchProducts()">Search</button>
    </div>
  </div>

  <!-- ─────────────────────────────────────────
       CATEGORY FILTER BUTTONS
       filter-btn class handles all styling
       active class highlights the selected one
       ───────────────────────────────────────── -->
  <div class="mb-4">

    <a href="products.php" 
       class="filter-btn <?= $categoryId === 0 ? 'active' : '' ?>">
      All
    </a>

    <?php while ($cat = mysqli_fetch_assoc($catResult)): ?>
      <a href="products.php?category=<?= $cat['id'] ?>" 
         class="filter-btn <?= $categoryId === $cat['id'] ? 'active' : '' ?>">
        <?= htmlspecialchars($cat['name']) ?>
      </a>
    <?php endwhile; ?>

  </div>

  <!-- ─────────────────────────────────────────
       PRODUCT GRID
       ───────────────────────────────────────── -->
  <div class="row g-4" id="productGrid">

    <?php 
    $count = mysqli_num_rows($products);

    if ($count === 0): ?>
      <div class="col-12">
        <div class="alert alert-info">No products found in this category.</div>
      </div>

    <?php else: ?>
      <?php while ($product = mysqli_fetch_assoc($products)): ?>

        <div class="col-6 col-md-3 product-item">
          <div class="card h-100 shadow-sm product-card">

            <!-- PRODUCT IMAGE -->
            <?php if (!empty($product['image']) && file_exists('../uploads/' . $product['image'])): ?>
              <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" 
                   class="card-img-top" 
                   alt="<?= htmlspecialchars($product['name']) ?>">
            <?php else: ?>
              <div class="product-img-placeholder">📦</div>
            <?php endif; ?>

            <div class="card-body d-flex flex-column">

              <!-- PRODUCT NAME -->
              <h6 class="card-title"><?= htmlspecialchars($product['name']) ?></h6>

              <!-- PRICE — now uses product-price class for gold color -->
              <p class="product-price mb-1">
                KSh <?= number_format($product['price'], 2) ?>
              </p>

              <!-- STOCK STATUS -->
              <?php if ($product['stock'] > 0): ?>
                <small class="text-success mb-2">In Stock (<?= $product['stock'] ?>)</small>
              <?php else: ?>
                <small class="text-danger mb-2">Out of Stock</small>
              <?php endif; ?>

              <!-- ADD TO CART BUTTON -->
              <?php if (isset($_SESSION['user_id'])): ?>
    <button class="btn btn-primary btn-sm w-100 add-to-cart-btn"
            data-product-id="<?= $product['id'] ?>">
        🛒 Add to Cart
    </button>
    <?php else: ?>
    <a href="/shop/login.php" class="btn btn-outline-primary btn-sm w-100">
        Login to Shop
    </a>
    <?php endif; ?>

            </div>
          </div>
        </div>

      <?php endwhile; ?>
    <?php endif; ?>

  </div>

</div>

<?php require_once '../includes/footer.php'; ?>