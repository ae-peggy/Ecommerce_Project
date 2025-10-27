<?php
// Include core session management functions
require_once '../settings/core.php';

// Include controllers
require_once '../controllers/product_controller.php';
require_once '../controllers/category_controller.php';
require_once '../controllers/brand_controller.php';

// Get search parameters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$brand_filter = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;

// Perform search/filter
$products = [];

if ($search_query) {
    // Search by query
    $products = search_products_ctr($search_query);
} elseif ($category_filter > 0) {
    // Filter by category
    $products = filter_products_by_category_ctr($category_filter);
} elseif ($brand_filter > 0) {
    // Filter by brand
    $products = filter_products_by_brand_ctr($brand_filter);
} else {
    // No filters, show all
    $products = get_all_products_ctr();
}

// Get all categories and brands for filters
require_once '../classes/category_class.php';
require_once '../classes/brand_class.php';
$category_obj = new category_class();
$brand_obj = new brand_class();

$categories = $category_obj->db_fetch_all("SELECT DISTINCT cat_id, cat_name FROM categories ORDER BY cat_name ASC");
$brands = $brand_obj->db_fetch_all("SELECT DISTINCT brand_id, brand_name FROM brands ORDER BY brand_name ASC");

// Build page title
$page_title = "Search Results";
if ($search_query) {
    $page_title = "Results for \"" . htmlspecialchars($search_query) . "\"";
} elseif ($category_filter > 0) {
    foreach($categories as $cat) {
        if ($cat['cat_id'] == $category_filter) {
            $page_title = htmlspecialchars($cat['cat_name']) . " Products";
            break;
        }
    }
} elseif ($brand_filter > 0) {
    foreach($brands as $brand) {
        if ($brand['brand_id'] == $brand_filter) {
            $page_title = htmlspecialchars($brand['brand_name']) . " Products";
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Aya Crafts</title>
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

        /* Search Header */
        .search-header {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            padding: 50px 40px;
            text-align: center;
        }

        .search-header h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .search-header p {
            font-size: 1.1rem;
            opacity: 0.95;
        }

        /* Filters Section */
        .filters-section {
            max-width: 1400px;
            margin: 30px auto;
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

        .filter-group select,
        .filter-group input {
            padding: 10px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            min-width: 180px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #dc2626;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
        }

        .search-box input {
            width: 100%;
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

        .btn-clear {
            padding: 10px 24px;
            background: #6b7280;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-clear:hover {
            background: #4b5563;
        }

        /* Active Filters Display */
        .active-filters {
            max-width: 1400px;
            margin: 0 auto 30px;
            padding: 0 40px;
        }

        .active-filters-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-tag {
            background: #fef2f2;
            color: #dc2626;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .filter-tag button {
            background: none;
            border: none;
            color: #dc2626;
            cursor: pointer;
            font-size: 16px;
            padding: 0;
            line-height: 1;
        }

        /* Results Section */
        .results-section {
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

        .result-count {
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

        .no-results {
            text-align: center;
            padding: 80px 20px;
            color: #9ca3af;
        }

        .no-results-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .no-results h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #4b5563;
        }

        .no-results p {
            margin-bottom: 25px;
        }

        .btn-browse {
            padding: 12px 32px;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-browse:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
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
            text-decoration: none;
            color: #374151;
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
            .filter-group input {
                width: 100%;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }

            .search-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo">Aya Crafts</a>
            <div class="nav-links">
                <a href="../index.php" class="nav-link">Home</a>
                <a href="all_product.php" class="nav-link">Shop</a>
                <?php if (is_logged_in()): ?>
                    <?php if (is_admin()): ?>
                        <a href="../admin/product.php" class="nav-link">Admin</a>
                    <?php endif; ?>
                    <a href="../login/logout.php" class="nav-link">Logout</a>
                <?php else: ?>
                    <a href="../login/login.php" class="nav-link">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Search Header -->
    <section class="search-header">
        <h1><?php echo $page_title; ?></h1>
        <p><?php echo $products ? count($products) : 0; ?> product(s) found</p>
    </section>

    <!-- Active Filters -->
    <?php if ($search_query || $category_filter || $brand_filter): ?>
    <div class="active-filters">
        <div class="active-filters-bar">
            <?php if ($search_query): ?>
                <div class="filter-tag">
                    Search: "<?php echo htmlspecialchars($search_query); ?>"
                    <button onclick="removeFilter('search')">×</button>
                </div>
            <?php endif; ?>
            
            <?php if ($category_filter): ?>
                <?php foreach($categories as $cat): ?>
                    <?php if ($cat['cat_id'] == $category_filter): ?>
                        <div class="filter-tag">
                            Category: <?php echo htmlspecialchars($cat['cat_name']); ?>
                            <button onclick="removeFilter('category')">×</button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <?php if ($brand_filter): ?>
                <?php foreach($brands as $brand): ?>
                    <?php if ($brand['brand_id'] == $brand_filter): ?>
                        <div class="filter-tag">
                            Brand: <?php echo htmlspecialchars($brand['brand_name']); ?>
                            <button onclick="removeFilter('brand')">×</button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <button class="btn-clear" onclick="clearAllFilters()">Clear All</button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filters Section -->
    <div class="filters-section">
        <div class="filters-bar">
            <div class="search-box">
                <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="🔍 Refine search..." 
                    value="<?php echo htmlspecialchars($search_query); ?>"
                >
            </div>
            
            <div class="filter-group">
                <label>Category:</label>
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                    <?php if ($categories): foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['cat_id']; ?>" <?php echo ($cat['cat_id'] == $category_filter) ? 'selected' : ''; ?>>
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
                        <option value="<?php echo $brand['brand_id']; ?>" <?php echo ($brand['brand_id'] == $brand_filter) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($brand['brand_name']); ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            
            <button class="btn-filter" onclick="applyFilters()">Apply</button>
        </div>
    </div>

    <!-- Results Section -->
    <section class="results-section">
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
                <div class="no-results">
                    <div class="no-results-icon">🔍</div>
                    <h3>No Products Found</h3>
                    <p>We couldn't find any products matching your criteria.</p>
                    <p style="margin-bottom: 25px;">Try adjusting your filters or browse all products.</p>
                    <a href="all_product.php" class="btn-browse">Browse All Products</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination (for future implementation if > 10 products) -->
        <?php if ($products && count($products) > 10): ?>
        <div class="pagination">
            <a href="#" class="page-btn">« Previous</a>
            <a href="#" class="page-btn active">1</a>
            <a href="#" class="page-btn">2</a>
            <a href="#" class="page-btn">3</a>
            <a href="#" class="page-btn">Next »</a>
        </div>
        <?php endif; ?>
    </section>

    <script>
        // View product details
        function viewProduct(productId) {
            window.location.href = `single_product.php?id=${productId}`;
        }

        // Add to cart (placeholder)
        function addToCart(event, productId) {
            event.stopPropagation();
            alert('Add to cart functionality will be implemented in future labs. Product ID: ' + productId);
        }

        // Apply filters
        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const category = document.getElementById('categoryFilter').value;
            const brand = document.getElementById('brandFilter').value;

            let url = 'product_search_result.php?';
            
            if (search) url += `search=${encodeURIComponent(search)}&`;
            if (category) url += `category=${category}&`;
            if (brand) url += `brand=${brand}&`;

            window.location.href = url;
        }

        // Remove specific filter
        function removeFilter(type) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.delete(type);
            
            const newUrl = urlParams.toString() ? 
                'product_search_result.php?' + urlParams.toString() : 
                'all_product.php';
            
            window.location.href = newUrl;
        }

        // Clear all filters
        function clearAllFilters() {
            window.location.href = 'all_product.php';
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