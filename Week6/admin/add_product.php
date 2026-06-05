
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

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = floatval($_POST['price']);
    $stock       = intval($_POST['stock']);
    $category_id = intval($_POST['category_id']);
    $image       = '';

    // ─────────────────────────────────────────
    // HANDLE IMAGE UPLOAD
    // Check if a file was submitted and is valid
    // ─────────────────────────────────────────
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {

        // Get the file details
        $file     = $_FILES['image'];
        $fileName = $file['name'];
        $fileSize = $file['size'];
        $fileTmp  = $file['tmp_name'];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Only allow these image types — security check
        $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($fileExt, $allowedTypes)) {
            $message     = 'Only JPG, PNG, and WEBP images are allowed.';
            $messageType = 'danger';
        } elseif ($fileSize > 2 * 1024 * 1024) {
            // Reject files over 2MB
            $message     = 'Image must be under 2MB.';
            $messageType = 'danger';
        } else {
            // Generate a unique filename to avoid conflicts
            // e.g. product_64f3a1b2c3d4e.jpg
            $image    = 'product_' . uniqid() . '.' . $fileExt;
            $dest     = '../uploads/' . $image;

            // Move file from temp location to uploads folder
            if (!move_uploaded_file($fileTmp, $dest)) {
                $message     = 'Failed to upload image.';
                $messageType = 'danger';
                $image       = '';
            }
        }
    }

    // Only save to DB if no upload error occurred
    if (empty($message)) {
        if (empty($name) || empty($price) || empty($category_id)) {
            $message     = 'Please fill in all required fields.';
            $messageType = 'danger';
        } else {
            $stmt = mysqli_prepare($conn,
                "INSERT INTO products (category_id, name, description, price, stock, image)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            // s = string, i = integer, d = decimal
            mysqli_stmt_bind_param($stmt, 'issdis',
                $category_id, $name, $description, $price, $stock, $image
            );

            if (mysqli_stmt_execute($stmt)) {
                $message     = 'Product added successfully!';
                $messageType = 'success';
            } else {
                $message     = 'Failed to add product. Please try again.';
                $messageType = 'danger';
            }
            mysqli_stmt_close($stmt);
        }
    }
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

$pageTitle = 'Add Product | ZuriMart Admin';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-8">

      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="section-title">Add <span>New Product</span></h4>
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

          <!-- enctype="multipart/form-data" is REQUIRED for file uploads -->
          <!-- Without it, $_FILES will always be empty -->
          <form method="POST" action="add_product.php"
                enctype="multipart/form-data">

            <div class="mb-3">
              <label class="form-label" for="name">
                Product Name <span style="color:#f59e0b">*</span>
              </label>
              <input type="text" class="form-control" id="name" name="name"
                     placeholder="e.g. Wireless Earbuds" required>
            </div>

            <div class="mb-3">
              <label class="form-label" for="category_id">
                Category <span style="color:#f59e0b">*</span>
              </label>
              <select class="form-control" id="category_id"
                      name="category_id" required>
                <option value="">-- Select Category --</option>
                <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                  <option value="<?= $cat['id'] ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label" for="description">Description</label>
              <textarea class="form-control" id="description"
                        name="description" rows="3"
                        placeholder="Brief product description"></textarea>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label" for="price">
                  Price (KSh) <span style="color:#f59e0b">*</span>
                </label>
                <input type="number" class="form-control" id="price"
                       name="price" step="0.01" min="0"
                       placeholder="e.g. 2500" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label" for="stock">Stock Quantity</label>
                <input type="number" class="form-control" id="stock"
                       name="stock" min="0" placeholder="e.g. 50" value="0">
              </div>
            </div>

            <!-- IMAGE UPLOAD FIELD -->
            <div class="mb-3">
              <label class="form-label" for="image">
                Product Image
                <small style="color:#94a3b8">(JPG, PNG, WEBP — max 2MB)</small>
              </label>
              <input type="file" class="form-control" id="image"
                     name="image" accept=".jpg,.jpeg,.png,.webp">

              <!-- Live image preview before upload -->
              <div id="imagePreview" class="mt-2" style="display:none;">
                <img id="previewImg" src="" alt="Preview"
                     style="max-height:150px; border-radius:8px;
                            border:1px solid #2d2d44;">
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">
              Add Product
            </button>

          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- IMAGE PREVIEW SCRIPT -->
<!-- Shows a preview of the selected image before uploading -->
<script>
document.getElementById('image').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>