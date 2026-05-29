<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container">

        <!-- Shop logo/name — clicking it goes to homepage -->
        <a class="navbar-brand" href="/shop/index.php">🛍️ ZuriMart</a>

        <!-- This button appears ONLY on mobile — tapping it opens the menu -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- The actual menu — collapses on mobile -->
        <div class="collapse navbar-collapse" id="navMenu">

            <!-- Left side links -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/shop/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/shop/pages/products.php">Products</a>
                </li>
            </ul>

            <!-- Right side links -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/shop/pages/cart.php">
                        🛒 Cart <span class="badge bg-danger">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/shop/login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/shop/register.php">Register</a>
                </li>
            </ul>

        </div>
    </div>
</nav>