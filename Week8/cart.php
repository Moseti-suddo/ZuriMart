<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config/db.php';

$pageTitle  = 'My Cart | ZuriMart';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Fetch cart items for logged in user
$cartItems = [];
$cartTotal = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    $result  = mysqli_query($conn,
        "SELECT cart_items.id, cart_items.quantity,
                products.id AS product_id,
                products.name, products.price, products.image,
                (cart_items.quantity * products.price) AS item_total
         FROM cart_items
         JOIN products ON cart_items.product_id = products.id
         WHERE cart_items.user_id = $user_id
         ORDER BY cart_items.added_at DESC"
    );

    while ($item = mysqli_fetch_assoc($result)) {
        $cartItems[] = $item;
        $cartTotal  += $item['item_total'];
    }
}
?>

<div class="container mt-4">
    <h4 class="mb-4">🛒 Your Cart
        <span class="badge bg-primary ms-2" id="cartItemCount">
            <?= count($cartItems) ?>
        </span>
    </h4>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <!-- Not logged in -->
        <div class="text-center py-5">
            <h5>Please login to view your cart</h5>
            <a href="/shop/login.php" class="btn btn-primary mt-3">Login</a>
        </div>

    <?php elseif (empty($cartItems)): ?>
        <!-- Empty cart -->
        <div class="text-center py-5">
            <h1>🛒</h1>
            <h5 class="mt-3">Your cart is empty</h5>
            <a href="/shop/pages/products.php" 
               class="btn btn-primary mt-3">Start Shopping</a>
        </div>

    <?php else: ?>
        <div class="row">

            <!-- CART ITEMS -->
            <div class="col-md-8">
                <div class="card shadow-sm mb-3">
                    <div class="card-body p-0">

                        <?php foreach ($cartItems as $item): ?>
                        <div class="d-flex align-items-center p-3 border-bottom cart-item"
                             id="cart-item-<?= $item['product_id'] ?>">

                            <!-- Product image -->
                            <img src="<?= $item['image'] 
                                ? '/shop/assets/images/products/' . htmlspecialchars($item['image'])
                                : 'https://via.placeholder.com/80x80?text=Product' ?>"
                                 class="rounded me-3"
                                 style="width:80px; height:80px; object-fit:cover;"
                                 alt="<?= htmlspecialchars($item['name']) ?>">

                            <!-- Product details -->
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <?= htmlspecialchars($item['name']) ?>
                                </h6>
                                <small class="text-muted">
                                    KSh <?= number_format($item['price'], 2) ?> each
                                </small>
                            </div>

                            <!-- Quantity controls -->
                            <div class="d-flex align-items-center me-3">
                                <button class="btn btn-sm btn-outline-secondary qty-btn"
                                        data-product-id="<?= $item['product_id'] ?>"
                                        data-action="decrease">
                                    −
                                </button>
                                <span class="mx-2 quantity-display" 
                                    id="qty-<?= $item['product_id'] ?>">
                                    <?= $item['quantity'] ?>
                                </span>
                                <button class="btn btn-sm btn-outline-secondary qty-btn"
                                        data-product-id="<?= $item['product_id'] ?>"
                                        data-action="increase">
                                    +
                                </button>
                            </div>

                            <!-- Item total -->
                            <div class="me-3 text-end" style="min-width:80px;">
                                <strong id="item-total-<?= $item['product_id'] ?>">
                                    KSh <?= number_format($item['item_total'], 2) ?>
                                </strong>
                            </div>

                            <!-- Remove button -->
                            <button class="btn btn-sm btn-danger"
                                    onclick="removeFromCart(<?= $item['product_id'] ?>)">
                                ✕
                            </button>

                        </div>
                        <?php endforeach; ?>

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
                            <span id="cartTotal">
                                KSh <?= number_format($cartTotal, 2) ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery</span>
                            <span class="text-success">Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold mb-3">
                            <span>Total</span>
                            <span id="cartTotalFinal">
                                KSh <?= number_format($cartTotal, 2) ?>
                            </span>
                        </div>

                        <a href="/shop/pages/checkout.php" 
                           class="btn btn-success w-100 mb-2">
                            Proceed to Checkout
                        </a>
                        <a href="/shop/pages/products.php" 
                           class="btn btn-outline-secondary w-100">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>

<script>
// Attach quantity button events after page loads
document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.qty-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const productId  = parseInt(this.dataset.productId);
            const action     = this.dataset.action;

            // Read the CURRENT quantity from the display span
            const currentQty = parseInt(
                document.getElementById('qty-' + productId).textContent
            );

            // Calculate new quantity based on action
            const newQty = action === 'increase' 
                ? currentQty + 1 
                : currentQty - 1;

            updateCart(productId, newQty);
        });
    });

});

function updateCart(productId, newQuantity) {
    fetch('/shop/includes/cart_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action:     'update',
            product_id: productId,
            quantity:   newQuantity
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (newQuantity < 1) {
                // Remove the entire item row
                document.getElementById('cart-item-' + productId).remove();
            } else {
                // Update the quantity display with new value
                document.getElementById('qty-' + productId).textContent = newQuantity;
                // Update item total
                document.getElementById('item-total-' + productId).textContent =
                    'KSh ' + Number(data.item_total).toLocaleString('en-KE', 
                        {minimumFractionDigits: 2});
            }
            // Update cart totals
            document.getElementById('cartTotal').textContent =
                'KSh ' + Number(data.cart_total).toLocaleString('en-KE', 
                    {minimumFractionDigits: 2});
            document.getElementById('cartTotalFinal').textContent =
                'KSh ' + Number(data.cart_total).toLocaleString('en-KE', 
                    {minimumFractionDigits: 2});
            // Update navbar badge
            updateCartBadge(data.cart_count);
        }
    });
}

function removeFromCart(productId) {
    if (!confirm('Remove this item from your cart?')) return;

    fetch('/shop/includes/cart_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action:     'remove',
            product_id: productId
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cart-item-' + productId).remove();
            document.getElementById('cartTotal').textContent =
                'KSh ' + Number(data.cart_total).toLocaleString('en-KE', 
                    {minimumFractionDigits: 2});
            document.getElementById('cartTotalFinal').textContent =
                'KSh ' + Number(data.cart_total).toLocaleString('en-KE', 
                    {minimumFractionDigits: 2});
            updateCartBadge(data.cart_count);
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>