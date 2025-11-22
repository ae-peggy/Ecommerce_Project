<?php
// Include core session management functions
require_once '../settings/core.php';

// Include controllers
require_once '../controllers/product_controller.php';
require_once '../controllers/category_controller.php';
require_once '../controllers/brand_controller.php';

// Get products - filter by artisan_id if provided
$artisan_id = isset($_GET['artisan_id']) ? (int)$_GET['artisan_id'] : 0;
if ($artisan_id > 0) {
    $products = filter_products_by_artisan_ctr($artisan_id);
    // Get artisan info for display
    require_once '../classes/artisan_class.php';
    $artisan_obj = new artisan_class();
    $filtered_artisan = $artisan_obj->get_artisan_by_id($artisan_id);
} else {
    $products = get_all_products_ctr();
    $filtered_artisan = null;
}

// Get all categories and brands for filters (from any admin)
require_once '../classes/category_class.php';
require_once '../classes/brand_class.php';
$category_obj = new category_class();
$brand_obj = new brand_class();

$categories = $category_obj->db_fetch_all("SELECT DISTINCT cat_id, cat_name FROM categories ORDER BY cat_name ASC");
$brands = $brand_obj->db_fetch_all("SELECT DISTINCT brand_id, brand_name FROM brands ORDER BY brand_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop All Products - Aya Crafts</title>
    <link rel="stylesheet" href="../css/products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Geometric shapes and particles -->
    <div class="geometric-shape shape-1"></div>
    <div class="geometric-shape shape-2"></div>
    <div class="geometric-shape shape-3"></div>
    
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo-container">
                <a href="../index.php" class="logo-container" style="display: flex; align-items: center; gap: 15px; text-decoration: none;">
                    <div class="logo-symbol">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z"/>
                        </svg>
                    </div>
                    <div class="logo-text">
                        <div class="logo">Aya Crafts</div>
                        <span class="logo-subtitle">Authentic Artistry</span>
                    </div>
                </a>
            </div>
            <div class="nav-links">
                <a href="../index.php" class="nav-link secondary"><span>Home</span></a>
                <a href="all_product.php" class="nav-link secondary"><span>Shop</span></a>
                <a href="about_us.php" class="nav-link secondary"><span>About</span></a>
                <?php if (is_logged_in()): ?>
                    <?php if (is_admin()): ?>
                        <a href="../admin/product.php" class="nav-link secondary"><span>Admin</span></a>
                    <?php endif; ?>
                    <a href="../view/cart.php" class="nav-link secondary" style="position: relative;">
                        <span>Cart</span>
                        <span class="cart-count-badge">0</span>
                    </a>
                    <a href="../login/logout.php" class="nav-link"><span>Logout</span></a>
                <?php else: ?>
                    <a href="../login/login.php" class="nav-link secondary"><span>Login</span></a>
                    <a href="../login/register.php" class="nav-link"><span>Register</span></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <?php if ($filtered_artisan && !empty($filtered_artisan['business_name'])): ?>
            <h1>Products by <?php echo htmlspecialchars($filtered_artisan['business_name']); ?></h1>
            <p>Explore handcrafted treasures from this artisan</p>
        <?php else: ?>
            <h1>Discover Authentic African Artistry</h1>
            <p>Explore our curated collection of handcrafted treasures</p>
        <?php endif; ?>
    </section>

    <!-- Filters Section -->
    <div class="filters-section">
        <div class="filters-bar">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="🔍 Search products...">
            </div>
            
            <div class="filter-group">
                <label>Category:</label>
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                    <?php if ($categories): foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['cat_id']; ?>">
                            <?php echo htmlspecialchars($cat['cat_name']); ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label>Brand:</label>
                <select id="brandFilter">
                    <option value="">All Brands</option>
                    <?php if ($brands): foreach($brands as $brand): ?>
                        <option value="<?php echo $brand['brand_id']; ?>">
                            <?php echo htmlspecialchars($brand['brand_name']); ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            
            <button class="btn-filter" onclick="applyFilters()">Apply Filters</button>
        </div>
    </div>

    <!-- Products Section -->
    <section class="products-section">
        <div class="section-header">
            <h2 class="section-title">All Products</h2>
            <span class="product-count" id="productCount">
                <?php echo $products ? count($products) : 0; ?> products
            </span>
        </div>

        <div class="products-grid" id="productsGrid">
            <?php if ($products && count($products) > 0): ?>
                <?php foreach($products as $product): ?>

                    <?php
                    // Properly construct image path
                    $imagePath = '';
                    if (!empty($product['product_image'])) {
                        // If the path already starts with '../', use it as is
                        if (strpos($product['product_image'], '../') === 0) {
                            $imagePath = $product['product_image'];
                        } 
                        // If it starts with 'uploads/', add '../' prefix
                        elseif (strpos($product['product_image'], 'uploads/') === 0) {
                            $imagePath = '../' . $product['product_image'];
                        }
                        // Otherwise use as-is (might be full URL)
                        else {
                            $imagePath = $product['product_image'];
                        }
                        
                        // Verify file exists
                        $fullPath = __DIR__ . '/' . $imagePath;
                        if (!file_exists($fullPath)) {
                            error_log("Image file not found: $fullPath");
                            $imagePath = ''; // Reset to empty if file doesn't exist
                        }
                    }
                    
                    // Use data URI placeholder for no image (prevents recursive loading)
                    if (empty($imagePath)) {
                        $displayImage = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="280" height="280"%3E%3Crect width="280" height="280" fill="%23fef2f2"/%3E%3Ctext x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="18" fill="%23dc2626"%3ENo Image%3C/text%3E%3C/svg%3E';
                    } else {
                        $displayImage = $imagePath;
                    }
                    ?>

                    <div class="product-card" onclick="viewProduct(<?php echo $product['product_id']; ?>)">
                        <img 
                            src="<?php echo htmlspecialchars($displayImage); ?>"
                            alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                            class="product-image"
                            <?php if (!empty($imagePath)): ?>
                            onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22280%22 height=%22280%22%3E%3Crect width=%22280%22 height=%22280%22 fill=%22%23fef2f2%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial, sans-serif%22 font-size=%2218%22 fill=%22%23dc2626%22%3EImage Error%3C/text%3E%3C/svg%3E';"
                            <?php endif; ?>
                        >
                        <div class="product-info">
                            <div class="product-category">
                                <?php echo htmlspecialchars($product['cat_name'] ?? 'Uncategorized'); ?>
                            </div>
                            <h3 class="product-title">
                                <?php echo htmlspecialchars($product['product_title']); ?>
                            </h3>
                            <div class="product-brand">
                                by <?php echo htmlspecialchars($product['brand_name'] ?? 'Unknown'); ?>
                            </div>
                            <div class="product-price">
                                GHS <?php echo number_format($product['product_price'], 2); ?>
                            </div>
                            <button class="btn-add-cart" onclick="event.stopPropagation(); addToCart(<?php echo $product['product_id']; ?>, 1)">
                                🛒 Add to Cart
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-products">
                    <div class="no-products-icon">📦</div>
                    <h3>No Products Available</h3>
                    <p>Check back soon for amazing handcrafted items!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination (will implement if > 10 products) -->
        <div class="pagination" id="pagination" style="display: none;">
            <!-- Pagination buttons will be added by JavaScript -->
        </div>
    </section>

    <script>
        // View product details
        function viewProduct(productId) {
            window.location.href = `single_product.php?id=${productId}`;
        }

        // Add to cart (placeholder for now)
        function addToCart(event, productId) {
            event.stopPropagation(); // Prevent card click
            alert('Add to cart functionality will be implemented in future labs. Product ID: ' + productId);
            // TODO: Implement cart functionality
        }

        // Apply filters
        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const category = document.getElementById('categoryFilter').value;
            const brand = document.getElementById('brandFilter').value;

            let url = 'search.php?';
            
            if (search) url += `search=${encodeURIComponent(search)}&`;
            if (category) url += `category=${category}&`;
            if (brand) url += `brand=${brand}&`;

            window.location.href = url;
        }

        // Search on Enter key
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
    </script>
        <!-- Include cart.js for add to cart functionality -->
    <script src="../js/cart.js"></script>
</body>
</html>