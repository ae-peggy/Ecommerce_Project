<?php
require_once '../settings/core.php';
require_artisan('../login/login.php');

$artisan_id = get_artisan_id();

// Fetch artisan stats
$product_count = get_artisan_product_count($artisan_id);
$pending_orders = get_artisan_pending_orders($artisan_id);
$total_sales = get_artisan_total_sales($artisan_id);
$recent_products = get_artisan_recent_products($artisan_id, 5);

if (!function_exists('artisan_image_src')) {
    function artisan_image_src($image) {
        if (empty($image)) {
            return 'https://via.placeholder.com/120x120?text=No+Image';
        }
        if (strpos($image, 'uploads/') === 0) {
            return '../' . ltrim($image, '/');
        }
        return '../images/products/' . ltrim($image, '/');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Aya Crafts Artisan Portal</title>
    <link rel="stylesheet" href="../css/artisan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <!-- Main Content -->
    <div class="artisan-main-content">
        <div class="artisan-section-card">
            <h1>Welcome, <?php echo htmlspecialchars(get_user_name() ?? 'Artisan'); ?></h1>
        </div>

        <!-- Stats Cards -->
        <div class="artisan-stats-grid">
            <div class="artisan-stat-card">
                <div class="artisan-stat-icon blue">
                    <i class="fas fa-box"></i>
                </div>
                <div class="artisan-stat-details">
                    <h3><?php echo $product_count; ?></h3>
                    <p>Total Products</p>
                </div>
            </div>

            <div class="artisan-stat-card">
                <div class="artisan-stat-icon orange">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="artisan-stat-details">
                    <h3><?php echo $pending_orders; ?></h3>
                    <p>Pending Orders</p>
                </div>
            </div>

            <div class="artisan-stat-card">
                <div class="artisan-stat-icon green">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="artisan-stat-details">
                    <h3>GHS <?php echo number_format($total_sales, 2); ?></h3>
                    <p>Total Sales</p>
                </div>
            </div>

            <div class="artisan-stat-card">
                <div class="artisan-stat-icon purple">
                    <i class="fas fa-star"></i>
                </div>
                <div class="artisan-stat-details">
                    <h3>4.8</h3>
                    <p>Average Rating</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="artisan-section-card">
            <h2>Quick Actions</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 24px;">
                <a href="add_product.php" class="artisan-btn artisan-btn-primary">
                    <i class="fas fa-plus"></i> Add New Product
                </a>
                <a href="my_products.php" class="artisan-btn artisan-btn-secondary">
                    <i class="fas fa-edit"></i> Manage Products
                </a>
                <a href="orders.php" class="artisan-btn artisan-btn-secondary">
                    <i class="fas fa-shipping-fast"></i> View Orders
                </a>
                <a href="profile.php" class="artisan-btn artisan-btn-secondary">
                    <i class="fas fa-user-edit"></i> Update Profile
                </a>
            </div>
        </div>

        <!-- Recent Products -->
        <div class="artisan-section-card">
            <h2>Recent Products</h2>
            <div class="artisan-table-responsive" style="margin-top: 24px;">
                <table class="artisan-data-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_products)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px;">
                                    No products yet. <a href="add_product.php" style="color: var(--primary);">Add your first product</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_products as $product): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo artisan_image_src($product['product_image']); ?>" 
                                         alt="Product" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"
                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/60x60?text=No+Image';">
                                </td>
                                <td><?php echo htmlspecialchars($product['product_title']); ?></td>
                                <td><?php echo htmlspecialchars($product['cat_name'] ?? 'N/A'); ?></td>
                                <td>GHS <?php echo number_format($product['product_price'], 2); ?></td>
                                <td>
                                    <span class="artisan-badge <?php echo $product['product_qty'] > 10 ? 'artisan-badge-success' : 'artisan-badge-warning'; ?>">
                                        <?php echo $product['product_qty']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="artisan-badge artisan-badge-success">Active</span>
                                </td>
                                <td>
                                    <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" 
                                       style="color: var(--primary); margin-right: 12px;" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" 
                                       style="color: #ef4444;" 
                                       onclick="if(confirm('Delete this product?')) { fetch('../actions/artisan_product_action.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'action=delete&product_id=<?php echo $product['product_id']; ?>'}).then(r=>r.json()).then(d=>{if(d.success){location.reload();}else{alert(d.message);}}); } return false;" 
                                       title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
    <script>
        // Mobile menu toggle
        document.getElementById('artisanHamburger')?.addEventListener('click', function() {
            this.classList.toggle('active');
            document.getElementById('artisanNavMenu')?.classList.toggle('active');
        });
    </script>
</body>
</html>