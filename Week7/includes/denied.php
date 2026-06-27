<?php
$pageTitle = 'Access Denied | ZuriMart';
require_once 'header.php';
require_once 'navbar.php';
?>

<div class="container mt-5 text-center">
    <div class="card shadow-sm mx-auto" style="max-width: 500px;">
        <div class="card-body p-5">
            <h1 class="display-1">🚫</h1>
            <h3 class="mt-3">Access Denied</h3>
            <p class="text-muted">
                You don't have permission to view this page.
            </p>
            <a href="/shop/index.php" class="btn btn-primary mt-2">
                Go to Homepage
            </a>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>