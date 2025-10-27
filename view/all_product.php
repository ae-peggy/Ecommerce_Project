<?php
// Include core session management functions
require_once 'settings/core.php';

// Include controllers
require_once 'controllers/product_controller.php';
require_once 'controllers/category_controller.php';
require_once 'controllers/brand_controller.php';

// Get all products
$products = get_all_products_ctr();

// Get all categories and brands for filters (from any admin)
require_once 'classes/category_class.php';
require_once 'classes/brand_class.php';
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #fafafa;
            line-height: 1.6;
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            padding: 20px 0;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(20px);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
        }

        .logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 28px;
            font-weight: 600;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-link {
            color: #374151;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #dc2626;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            padding: 60px 40px;
            text-align: center;
        }

        .hero h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3rem;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .hero p {
            font-size: 1.1rem;
            opacity: 0.95;
        }

        /* Filters Section */
        .filters-section {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 40px;
        }

        .filters-bar {
            background: white;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .filter-group label {
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .filter-group select {
            padding: 10px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            min-width: 180px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-group select:focus {
            outline: none;
            border-color: #dc2626;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
        }

        .search-box input:focus {
            outline: none;
            border-color: #dc2626;
        }

        .btn-filter {
            padding: 10px 24px;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }

        /* Products Section */
        .products-section {
            max-width: 1400px;
            margin: 0 auto 60px;
            padding: 0 40px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            color: #1a1a1a;
        }

        .product-count {
            color: #6b7280;
            font-size: 1rem;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
        }

        .product-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            background: #f3f4f6;
        }

        .product-info {
            padding: 20px;
        }

        .product-category {
            font-size: 12px;
            color: #dc2626;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .product-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-brand {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 15px;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #dc2626;
            margin-bottom: 15px;
        }

        .btn-add-cart {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-add-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }

        .no-products {
            text-align: center;
            padding: 80px 20px;
            color: #9ca3af;
        }

        .no-products-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 40px;
        }

        .page-btn {
            padding: 10px 16px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .page-btn:hover {
            border-color: #dc2626;
            color: #dc2626;
        }

        .page-btn.active {
            background: #dc2626;
            color: white;
            border-color: #dc2626;
        }

        @media (max-width: 768px) {
            .filters-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-group {
                width: 100%;
            }

            .filter-group select,
            .search-box input {
                width: 100%;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }

            .hero h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Aya Crafts</a>
            <div class="nav-links">
                <a href="index.php" class="nav-link">Home</a>
                <a href="view/all_product.php" class="nav-link">Shop</a>
                <?php if (is_logged_in()): ?>
                    <?php if (is_admin()): ?>
                        <a href="admin/product.php" class="nav-link">Admin</a>
                    <?php endif; ?>
                    <a href="login/logout.php" class="nav-link">Logout</a>
                <?php else: ?>
                    <a href="login/login.php" class="nav-link">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Discover Authentic African Artistry</h1>
        <p>Explore our curated collection of handcrafted treasures</p>
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
                    <div class="product-card" onclick="viewProduct(<?php echo $product['product_id']; ?>)">
                        <img 
                            src="<?php echo $product['product_image'] ? $product['product_image'] : 'https://via.placeholder.com/280x280?text=No+Image'; ?>" 
                            alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                            class="product-image"
                            onerror="this.src='https://via.placeholder.com/280x280?text=No+Image'"
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
                                $<?php echo number_format($product['product_price'], 2); ?>
                            </div>
                            <button class="btn-add-cart" onclick="addToCart(event, <?php echo $product['product_id']; ?>)">
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
            window.location.href = `view/single_product.php?id=${productId}`;
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

            let url = 'view/product_search_result.php?';
            
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
</body>
</html>