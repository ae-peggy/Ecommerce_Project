<?php
 // Get current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Geometric shapes -->
<div class="geometric-shape shape-1"></div>
<div class="geometric-shape shape-2"></div>

<!-- Header -->
<header class="header">
    <div class="header-content">
        <div class="logo-container">
            <a href="../index.php" style="display: flex; align-items: center; gap: 15px; text-decoration: none;">
                <div class="logo-symbol">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z"/>
                    </svg>
                </div>
                <div>
                    <div class="logo">Aya Crafts</div>
                    <span class="logo-subtitle">Admin Portal</span>
                </div>
            </a>
        </div>
        <div class="user-info">
            <div class="nav-links">
                <a href="../index.php" class="nav-link"><span>Home</span></a>
                <a href="../admin/category.php" class="nav-link <?php echo $current_page === 'category.php' ? 'active' : ''; ?>"><span>Categories</span></a>
                <a href="../admin/brand.php" class="nav-link <?php echo $current_page === 'brand.php' ? 'active' : ''; ?>"><span>Brands</span></a>
                <a href="../admin/product.php" class="nav-link <?php echo $current_page === 'product.php' ? 'active' : ''; ?>"><span>Products</span></a>
                <a href="../admin/tier2_dashboard.php" class="nav-link <?php echo $current_page === 'tier2_dashboard.php' ? 'active' : ''; ?>"><span>Dashboard</span></a>
                <a href="../admin/artisans.php" class="nav-link <?php echo $current_page === 'artisans.php' ? 'active' : ''; ?>"><span>Artisans</span></a>
                <a href="../view/orders.php" class="nav-link <?php echo $current_page === 'orders.php' ? 'active' : ''; ?>"><span>Orders</span></a>
                <a href="../admin/profile.php" class="nav-link <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>"><span>My Profile</span></a>
                <a href="../login/logout.php" class="nav-link logout"><span>Logout</span></a>
            </div>
        </div>
    </div>
</header>

