<?php
$pageTitle = 'Cart | ZuriMart';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container mt-4">
    <h4 class="mb-4">🛒 Your Cart</h4>

    <div class="row">

        <!-- CART ITEMS -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">

                    <!-- Cart item row -->
                    <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                        <img src="https://via.placeholder.com/80x80?text=Item"
                             class="rounded me-3" alt="Product">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Sample Product</h6>
                            <small class="text-muted">Category: Electronics</small>
                        </div>
                        <div class="d-flex align-items-center me-3">
                            <button class="btn btn-sm btn-outline-secondary">-</button>
                            <span class="mx-2">1</span>
                            <button class="btn btn-sm btn-outline-secondary">+</button>
                        </div>
                        <div class="me-3">
                            <strong>KSh 1,500</strong>
                        </div>
                        <button class="btn btn-sm btn-danger">✕</button>
                    </div>

                    <p class="text-muted text-center mt-3">
                        Cart functionality will be connected to database in Week 5
                    </p>

                </div>
            </div>
        </div>

        <!-- ORDER SUMMARY -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3">Order Summary</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>KSh 1,500</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery</span>
                        <span class="text-success">Free</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold mb-3">
                        <span>Total</span>
                        <span>KSh 1,500</span>
                    </div>
                    <button class="btn btn-success w-100">
                        Proceed to Checkout
                    </button>
                    <a href="products.php" class="btn btn-outline-secondary w-100 mt-2">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>