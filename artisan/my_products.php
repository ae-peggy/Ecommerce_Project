<?php
require_once '../settings/core.php';
require_artisan('../login/login.php');

$artisan_id = get_artisan_id();
$products = get_all_artisan_products($artisan_id);

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
    <title>My Products - Aya Crafts Artisan Portal</title>
    <link rel="stylesheet" href="../css/artisan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <!-- Main Content -->
    <div class="artisan-main-content">
        <div class="artisan-section-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                <h1>My Products</h1>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <div style="position: relative;">
                        <input type="text" id="searchInput" 
                               placeholder="Search products..." 
                               onkeyup="searchProducts()"
                               style="padding: 10px 40px 10px 16px; border: 2px solid #e5e7eb; border-radius: 50px; font-size: 14px; width: 300px; max-width: 100%;"
                               onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 4px rgba(220, 38, 38, 0.1)';"
                               onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                        <i class="fas fa-search" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #6b7280;"></i>
                    </div>
                    <a href="add_product.php" class="artisan-btn artisan-btn-primary">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                </div>
            </div>

            <div class="artisan-table-responsive">
                <table class="artisan-data-table" id="productsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Price (GHS)</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="9" style="text-align: center; padding: 60px 20px;">
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 16px;">
                                            <i class="fas fa-box-open" style="font-size: 64px; color: #9ca3af;"></i>
                                            <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 28px; color: var(--text-primary);">No Products Yet</h3>
                                            <p style="color: var(--text-secondary);">Start adding products to your store</p>
                                            <a href="add_product.php" class="artisan-btn artisan-btn-primary">Add Your First Product</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['product_id']; ?></td>
                                    <td>
                                        <img src="<?php echo artisan_image_src($product['product_image']); ?>" 
                                             alt="Product" 
                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"
                                             onerror="this.onerror=null; this.src='https://via.placeholder.com/60x60?text=No+Image';">
                                    </td>
                                    <td><?php echo htmlspecialchars($product['product_title']); ?></td>
                                    <td><?php echo htmlspecialchars($product['cat_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($product['brand_name'] ?? 'N/A'); ?></td>
                                    <td>GHS <?php echo number_format($product['product_price'], 2); ?></td>
                                    <td>
                                        <span class="artisan-badge <?php echo $product['product_qty'] > 10 ? 'artisan-badge-success' : ($product['product_qty'] > 0 ? 'artisan-badge-warning' : 'artisan-badge-danger'); ?>">
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
                                           onclick="deleteProduct(<?php echo $product['product_id']; ?>); return false;" 
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

    <script src="../js/product.js"></script>
    <script>
        // Mobile menu toggle
        document.getElementById('artisanHamburger')?.addEventListener('click', function() {
            this.classList.toggle('active');
            document.getElementById('artisanNavMenu')?.classList.toggle('active');
        });
        
        function searchProducts() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('productsTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < td.length; j++) {
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                tr[i].style.display = found ? '' : 'none';
            }
        }

        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product?')) {
                fetch('../actions/artisan_product_action.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=delete&product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Product deleted successfully');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }
    </script>
</body>
</html>