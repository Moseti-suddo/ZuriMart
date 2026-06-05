
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

// Get the product ID from the URL
$id = intval($_GET['id'] ?? 0);
if ($id === 0) {
    header('Location: products.php');
    exit;
}

$message = '';
$messageType = '';

// ─────────────────────────────────────────
// HANDLE UPDATE FORM SUBMISSION
// ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = floatval($_POST['price']);
    $stock       = intval($_POST['stock']);
    $category_id = intval($_POST['category_id']);
    $image       = $_POST['existing_image']; // Keep old image by default

    // Handle new image upload if provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $file      = $_FILES['image'];
        $fileExt   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($fileExt, $allowedTypes)) {
            $message     = 'Only JPG, PNG, and WEBP images are allowed.';
            $messageType = 'danger';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $message     = 'Image must be under 2MB.';
            $messageType = 'danger';
        } else {
            $newImage = 'product_' . uniqid() . '.' . $fileExt;
            if (move_uploaded_file($file['tmp_name'], '../uploads/' . $newImage)) {
                // Delete old image file if it exists
                if (!empty($image) && file_exists('../uploads/' . $image)) {
                    unlink('../uploads/' . $image);
                }
                $image = $newImage;
            }
        }
    }

    if (empty($message)) {
        if (empty($name) || empty($price) || empty($category_id)) {
            $message     = 'Please fill in all required fields.';
            $messageType = 'danger';
        } else {
            $stmt = mysqli_prepare($conn,
                "UPDATE products 
                 SET category_id=?, name=?, description=?, price=?, stock=?, image=?
                 WHERE id=?"
            );
            mysqli_stmt_bind_param($stmt, 'issdisi',
                $category_id, $name, $description, $price, $stock, $image, $id
            );

            if (mysqli_stmt_execute($stmt)) {
                header('Location: products.php?updated=1');
                exit;
            } else {
                $message     = 'Failed to update product.';
                $messageType = 'danger';
            }
        }
    }
}

// Fetch the current product data to pre-fill the form
$result  = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header('Location: products.php');
    exit;
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

$pageTitle = 'Edit Product | ZuriMart Admin';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-8">

      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="section-title">Edit <span>Product</span></h4>
        <a href="products.php" class="btn btn-outline-primary btn-sm">
          ← Back to Products
        </a>
      </div>

      <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> mb-4">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <div class="card">
        <div class="card-body p-4">
          <form method="POST" 
                action="edit_product.php?id=<?= $id ?>"
                enctype="multipart/form-data">

            <!-- Hidden field preserves the old image filename -->
            <input type="hidden" name="existing_image" 
                   value="<?= htmlspecialchars($product['image'] ?? '') ?>">

            <div class="mb-3">
              <label class="form-label" for="name">
                Product Name <span style="color:#f59e0b">*</span>
              </label>
              <!-- value= pre-fills with current product name -->
              <input type="text" class="form-control" id="name" name="name"
                     value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label" for="category_id">
                Category <span style="color:#f59e0b">*</span>
              </label>
              <select class="form-control" id="category_id" 
                      name="category_id" required>
                <option value="">-- Select Category --</option>
                <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                  <option value="<?= $cat['id'] ?>"
                    <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label" for="description">Description</label>
              <textarea class="form-control" id="description" 
                        name="description" rows="3">
                <?= htmlspecialchars($product['description'] ?? '') ?>
              </textarea>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label" for="price">
                  Price (KSh) <span style="color:#f59e0b">*</span>
                </label>
                <input type="number" class="form-control" id="price"
                       name="price" step="0.01" min="0"
                       value="<?= $product['price'] ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label" for="stock">Stock Quantity</label>
                <input type="number" class="form-control" id="stock"
                       name="stock" min="0"
                       value="<?= $product['stock'] ?>">
              </div>
            </div>

            <!-- CURRENT IMAGE PREVIEW -->
            <div class="mb-3">
              <label class="form-label">Current Image</label>
              <div class="mb-2">
                <?php if (!empty($product['image']) && 
                           file_exists('../uploads/' . $product['image'])): ?>
                  <img src="../uploads/<?= htmlspecialchars($product['image']) ?>"
                       style="height:100px; border-radius:8px;
                              border:1px solid #2d2d44;">
                <?php else: ?>
                  <div style="color:#94a3b8; font-size:0.9rem;">
                    No image uploaded
                  </div>
                <?php endif; ?>
              </div>

              <label class="form-label" for="image">
                Replace Image
                <small style="color:#94a3b8">(leave blank to keep current)</small>
              </label>
              <input type="file" class="form-control" id="image"
                     name="image" accept=".jpg,.jpeg,.png,.webp">
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">
              Save Changes
            </button>

          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>