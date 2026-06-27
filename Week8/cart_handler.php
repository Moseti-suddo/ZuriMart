<?php
session_start();
require_once '../config/db.php';

// Must be logged in to use cart
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Please login to add items to cart',
        'redirect' => '/shop/login.php'
    ]);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$data    = json_decode(file_get_contents('php://input'), true);
$action  = $data['action'] ?? '';

switch ($action) {

    // ========================================
    // ADD ITEM TO CART
    // ========================================
    case 'add':
        $product_id = (int)$data['product_id'];
        $quantity   = (int)($data['quantity'] ?? 1);

        // First verify the product actually exists
        $check = mysqli_prepare($conn, 
            "SELECT id, name, stock FROM products WHERE id = ?");
        mysqli_stmt_bind_param($check, 'i', $product_id);
        mysqli_stmt_execute($check);
        $product = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
        mysqli_stmt_close($check);

        if (!$product) {
            echo json_encode([
                'success' => false, 
                'message' => 'Product not found'
            ]);
            exit;
        }

        if ($product['stock'] < 1) {
            echo json_encode([
                'success' => false, 
                'message' => 'Sorry, this product is out of stock'
            ]);
            exit;
        }

        // INSERT or UPDATE if already in cart
        // ON DUPLICATE KEY handles the UNIQUE constraint we set
        // If the row exists, it adds the new quantity to existing quantity
        $stmt = mysqli_prepare($conn,
        "INSERT INTO cart_items (user_id, product_id, quantity) 
        VALUES (?, ?, 1)
        ON DUPLICATE KEY UPDATE quantity = quantity + 1");
        mysqli_stmt_bind_param($stmt, 'ii', $user_id, $product_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Get updated cart count for the badge
        $count = getCartCount($conn, $user_id);

        echo json_encode([
            'success'    => true,
            'message'    => $product['name'] . ' added to cart',
            'cart_count' => $count
        ]);
        break;

    // ========================================
    // UPDATE ITEM QUANTITY
    // ========================================
    case 'update':
        $product_id = (int)$data['product_id'];
        $quantity   = (int)$data['quantity'];

        if ($quantity < 1) {
            // If quantity drops below 1, remove the item
            $stmt = mysqli_prepare($conn,
                "DELETE FROM cart_items 
                 WHERE user_id = ? AND product_id = ?");
            mysqli_stmt_bind_param($stmt, 'ii', $user_id, $product_id);
        } else {
            $stmt = mysqli_prepare($conn,
                "UPDATE cart_items SET quantity = ? 
                 WHERE user_id = ? AND product_id = ?");
            mysqli_stmt_bind_param($stmt, 'iii', 
                $quantity, $user_id, $product_id);
        }

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Recalculate totals to send back
        $totals = getCartTotals($conn, $user_id);
        $count  = getCartCount($conn, $user_id);

        echo json_encode([
            'success'     => true,
            'cart_count'  => $count,
            'cart_total'  => $totals['total'],
            'item_total'  => $totals['items'][$product_id] ?? 0
        ]);
        break;

    // ========================================
    // REMOVE ITEM FROM CART
    // ========================================
    case 'remove':
        $product_id = (int)$data['product_id'];

        $stmt = mysqli_prepare($conn,
            "DELETE FROM cart_items 
             WHERE user_id = ? AND product_id = ?");
        mysqli_stmt_bind_param($stmt, 'ii', $user_id, $product_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $totals = getCartTotals($conn, $user_id);
        $count  = getCartCount($conn, $user_id);

        echo json_encode([
            'success'    => true,
            'message'    => 'Item removed from cart',
            'cart_count' => $count,
            'cart_total' => $totals['total']
        ]);
        break;

    // ========================================
    // GET CART COUNT ONLY
    // ========================================
    case 'count':
        echo json_encode([
            'success'    => true,
            'cart_count' => getCartCount($conn, $user_id)
        ]);
        break;

    default:
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid action'
        ]);
}

// ========================================
// HELPER FUNCTIONS
// ========================================

function getCartCount($conn, $user_id) {
    // Returns total number of items in cart
    // SUM(quantity) so 3 of one product counts as 3
    $stmt = mysqli_prepare($conn,
        "SELECT SUM(quantity) AS total FROM cart_items WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return (int)($result['total'] ?? 0);
}

function getCartTotals($conn, $user_id) {
    // Returns total price and per-item totals
    $stmt = mysqli_prepare($conn,
        "SELECT cart_items.product_id,
                cart_items.quantity,
                products.price,
                (cart_items.quantity * products.price) AS item_total
         FROM cart_items
         JOIN products ON cart_items.product_id = products.id
         WHERE cart_items.user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $total = 0;
    $items = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $total += $row['item_total'];
        $items[$row['product_id']] = $row['item_total'];
    }

    mysqli_stmt_close($stmt);
    return ['total' => $total, 'items' => $items];
}
?>