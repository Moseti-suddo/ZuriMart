<?php
$pageTitle = 'Home | ZuriMart';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<!-- ========================================
     HERO SECTION
     A full-width banner with a gradient background,
     tagline, and two call-to-action buttons
======================================== -->
<section class="hero-section text-white text-center py-5">
  <div class="container py-4">
    <h1 class="display-4 fw-bold">Shop Everything <span>You Need</span></h1>    <p class="lead mb-4">Electronics, Clothing, Groceries and more — delivered to your door.</p>
    <a href="pages/products.php" class="btn btn-accent btn-lg me-2">Shop Now</a>
    <a href="register.php" class="btn btn-outline-light btn-lg">Create Account</a>
  </div>
</section>

<!-- ========================================
     CATEGORIES SECTION
     4 cards in a row, one per category
     Icons from Bootstrap Icons (loaded via CDN in header)
======================================== -->
<section class="py-5 bg-light">
  <div class="container">
    <h2 class="text-center mb-4">Shop by Category</h2>
    <div class="row g-4">

      <!-- Electronics -->
      <div class="col-6 col-md-3">
        <div class="card text-center h-100 shadow-sm category-card">
          <div class="card-body py-4">
            <div class="category-icon mb-3">📱</div>
            <h5 class="card-title">Electronics</h5>
            <a href="pages/products.php" class="btn btn-sm btn-outline-primary mt-2">Browse</a>
          </div>
        </div>
      </div>

      <!-- Clothing -->
      <div class="col-6 col-md-3">
        <div class="card text-center h-100 shadow-sm category-card">
          <div class="card-body py-4">
            <div class="category-icon mb-3">👕</div>
            <h5 class="card-title">Clothing</h5>
            <a href="pages/products.php" class="btn btn-sm btn-outline-primary mt-2">Browse</a>
          </div>
        </div>
      </div>

      <!-- Food & Groceries -->
      <div class="col-6 col-md-3">
        <div class="card text-center h-100 shadow-sm category-card">
          <div class="card-body py-4">
            <div class="category-icon mb-3">🛒</div>
            <h5 class="card-title">Food & Groceries</h5>
            <a href="pages/products.php" class="btn btn-sm btn-outline-primary mt-2">Browse</a>
          </div>
        </div>
      </div>

      <!-- Home & Living -->
      <div class="col-6 col-md-3">
        <div class="card text-center h-100 shadow-sm category-card">
          <div class="card-body py-4">
            <div class="category-icon mb-3">🏠</div>
            <h5 class="card-title">Home & Living</h5>
            <a href="pages/products.php" class="btn btn-sm btn-outline-primary mt-2">Browse</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ========================================
     FEATURED PRODUCTS SECTION
     4 static product cards for now
     We'll replace these with a PHP database loop later
======================================== -->
<section class="py-5">
  <div class="container">
    <h2 class="text-center mb-4">Featured Products</h2>
    <div class="row g-4">

      <!-- Product 1 -->
      <div class="col-6 col-md-3">
        <div class="card h-100 shadow-sm product-card">
          <div class="product-img-placeholder">📦</div>
          <div class="card-body">
            <h6 class="card-title">Wireless Earbuds</h6>
            <p class="text-primary fw-bold">KSh 2,500</p>
            <a href="pages/products.php" class="btn btn-primary btn-sm w-100">View Product</a>
          </div>
        </div>
      </div>

      <!-- Product 2 -->
      <div class="col-6 col-md-3">
        <div class="card h-100 shadow-sm product-card">
          <div class="product-img-placeholder">📦</div>
          <div class="card-body">
            <h6 class="card-title">Men's Casual Shirt</h6>
            <p class="text-primary fw-bold">KSh 1,200</p>
            <a href="pages/products.php" class="btn btn-primary btn-sm w-100">View Product</a>
          </div>
        </div>
      </div>

      <!-- Product 3 -->
      <div class="col-6 col-md-3">
        <div class="card h-100 shadow-sm product-card">
          <div class="product-img-placeholder">📦</div>
          <div class="card-body">
            <h6 class="card-title">Unga Pembe 2kg</h6>
            <p class="text-primary fw-bold">KSh 180</p>
            <a href="pages/products.php" class="btn btn-primary btn-sm w-100">View Product</a>
          </div>
        </div>
      </div>

      <!-- Product 4 -->
      <div class="col-6 col-md-3">
        <div class="card h-100 shadow-sm product-card">
          <div class="product-img-placeholder">📦</div>
          <div class="card-body">
            <h6 class="card-title">Desk Lamp</h6>
            <p class="text-primary fw-bold">KSh 850</p>
            <a href="pages/products.php" class="btn btn-primary btn-sm w-100">View Product</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>