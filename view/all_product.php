<?php
// Include core session management functions
require_once '../settings/core.php';

// Include controllers
require_once '../controllers/product_controller.php';
require_once '../controllers/category_controller.php';
require_once '../controllers/brand_controller.php';

// Collect filter inputs
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$brand_filter = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;
$min_price_input = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price_input = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$artisan_id = isset($_GET['artisan_id']) ? (int)$_GET['artisan_id'] : 0;

$valid_sorts = ['newest', 'price_asc', 'price_desc', 'best_sellers'];
if (!in_array($sort_option, $valid_sorts, true)) {
    $sort_option = 'newest';
}

$filters = ['sort' => $sort_option];

if ($search_query !== '') {
    $filters['search'] = $search_query;
}
if ($category_filter > 0) {
    $filters['category'] = $category_filter;
}
if ($brand_filter > 0) {
    $filters['brand'] = $brand_filter;
}
if ($artisan_id > 0) {
    $filters['artisan'] = $artisan_id;
}
if ($min_price_input !== '' && is_numeric($min_price_input)) {
    $filters['min_price'] = (float)$min_price_input;
}
if ($max_price_input !== '' && is_numeric($max_price_input)) {
    $filters['max_price'] = (float)$max_price_input;
}

if ($artisan_id > 0) {
    require_once '../classes/artisan_class.php';
    $artisan_obj = new artisan_class();
    $filtered_artisan = $artisan_obj->get_artisan_by_id($artisan_id);
} else {
    $filtered_artisan = null;
}

$products = get_all_products_ctr($filters);

// Get all categories and brands for filters (from any admin)
require_once '../classes/category_class.php';
require_once '../classes/brand_class.php';
$category_obj = new category_class();
$brand_obj = new brand_class();

$categories = $category_obj->db_fetch_all("SELECT DISTINCT cat_id, cat_name FROM categories ORDER BY cat_name ASC");
$brands = $brand_obj->db_fetch_all("SELECT DISTINCT brand_id, brand_name FROM brands ORDER BY brand_name ASC");

$price_slider_min = 0;
$price_slider_max = 5000;
$min_slider_value = ($min_price_input !== '' && is_numeric($min_price_input)) ? (float)$min_price_input : $price_slider_min;
$max_slider_value = ($max_price_input !== '' && is_numeric($max_price_input)) ? (float)$max_price_input : $price_slider_max;
if ($min_slider_value < $price_slider_min) {
    $min_slider_value = $price_slider_min;
}
if ($max_slider_value > $price_slider_max) {
    $max_slider_value = $price_slider_max;
}
if ($min_slider_value > $max_slider_value) {
    $min_slider_value = $price_slider_min;
}

$selected_category_name = '';
if ($category_filter > 0 && $categories) {
    foreach ($categories as $cat) {
        if ((int)$cat['cat_id'] === $category_filter) {
            $selected_category_name = $cat['cat_name'];
            break;
        }
    }
}

$selected_brand_name = '';
if ($brand_filter > 0 && $brands) {
    foreach ($brands as $brand) {
        if ((int)$brand['brand_id'] === $brand_filter) {
            $selected_brand_name = $brand['brand_name'];
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
    <section class="hero" aria-live="polite">
        <?php if ($filtered_artisan && !empty($filtered_artisan['business_name'])): ?>
            <h1>Products by <?php echo htmlspecialchars($filtered_artisan['business_name']); ?></h1>
            <p>Explore handcrafted treasures from this artisan</p>
        <?php elseif ($search_query !== ''): ?>
            <h1>Results for "<?php echo htmlspecialchars($search_query); ?>"</h1>
            <p>Use the filters below to refine your discovery</p>
        <?php elseif ($selected_category_name !== ''): ?>
            <h1><?php echo htmlspecialchars($selected_category_name); ?> Collection</h1>
            <p>Curated pieces from this category</p>
        <?php elseif ($selected_brand_name !== ''): ?>
            <h1><?php echo htmlspecialchars($selected_brand_name); ?> Selection</h1>
            <p>Handpicked creations from this brand</p>
        <?php else: ?>
            <h1>Discover Authentic African Artistry</h1>
            <p>Explore our curated collection of handcrafted treasures</p>
        <?php endif; ?>
    </section>

    <!-- Filters Section -->
    <div class="filters-section">
        <form 
            id="productFilters" 
            class="filters-bar" 
            method="GET" 
            role="search" 
            aria-label="Product filters"
        >
            <?php if ($artisan_id > 0): ?>
                <input type="hidden" name="artisan_id" value="<?php echo $artisan_id; ?>">
            <?php endif; ?>
            
            <div class="search-box">
                <label for="searchInput" class="sr-only">Search products</label>
                <input 
                    type="text" 
                    id="searchInput" 
                    name="search"
                    placeholder="🔍 Search products..."
                    value="<?php echo htmlspecialchars($search_query); ?>"
                    aria-label="Search all products"
                >
            </div>
            
            <div class="filter-group">
                <label for="categoryFilter">Category:</label>
                <select id="categoryFilter" name="category" aria-label="Filter by category">
                    <option value="">All Categories</option>
                    <?php if ($categories): foreach($categories as $cat): ?>
                        <option 
                            value="<?php echo $cat['cat_id']; ?>" 
                            <?php echo ($cat['cat_id'] === $category_filter) ? 'selected' : ''; ?>
                        >
                            <?php echo htmlspecialchars($cat['cat_name']); ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="brandFilter">Brand:</label>
                <select id="brandFilter" name="brand" aria-label="Filter by brand">
                    <option value="">All Brands</option>
                    <?php if ($brands): foreach($brands as $brand): ?>
                        <option 
                            value="<?php echo $brand['brand_id']; ?>"
                            <?php echo ($brand['brand_id'] === $brand_filter) ? 'selected' : ''; ?>
                        >
                            <?php echo htmlspecialchars($brand['brand_name']); ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            
            <div class="filter-group price-range">
                <label for="minPrice">Price range (GHS):</label>
                <div class="price-inputs">
                    <input 
                        type="number" 
                        step="0.01" 
                        id="minPrice" 
                        name="min_price" 
                        placeholder="Min"
                        min="<?php echo $price_slider_min; ?>"
                        max="<?php echo $price_slider_max; ?>"
                        value="<?php echo htmlspecialchars($min_price_input); ?>"
                        aria-label="Minimum price"
                    >
                    <span aria-hidden="true">—</span>
                    <input 
                        type="number" 
                        step="0.01" 
                        id="maxPrice" 
                        name="max_price" 
                        placeholder="Max"
                        min="<?php echo $price_slider_min; ?>"
                        max="<?php echo $price_slider_max; ?>"
                        value="<?php echo htmlspecialchars($max_price_input); ?>"
                        aria-label="Maximum price"
                    >
                </div>
                <div class="price-slider" aria-label="Adjust price range like a Shein price bar">
                    <div class="slider-track" id="priceSliderTrack"></div>
                    <input 
                        type="range" 
                        id="minPriceSlider" 
                        min="<?php echo $price_slider_min; ?>" 
                        max="<?php echo $price_slider_max; ?>" 
                        step="10" 
                        value="<?php echo $min_slider_value; ?>"
                        aria-label="Minimum price slider"
                    >
                    <input 
                        type="range" 
                        id="maxPriceSlider" 
                        min="<?php echo $price_slider_min; ?>" 
                        max="<?php echo $price_slider_max; ?>" 
                        step="10" 
                        value="<?php echo $max_slider_value; ?>"
                        aria-label="Maximum price slider"
                    >
                </div>
                <small class="price-hint">Use the slider or boxes above to fine-tune prices</small>
            </div>
            
            <div class="filter-group">
                <label for="sortSelect">Sort:</label>
                <select id="sortSelect" name="sort" aria-label="Sort products">
                    <option value="newest" <?php echo $sort_option === 'newest' ? 'selected' : ''; ?>>Newest arrivals</option>
                    <option value="price_asc" <?php echo $sort_option === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_desc" <?php echo $sort_option === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="best_sellers" <?php echo $sort_option === 'best_sellers' ? 'selected' : ''; ?>>Best sellers</option>
                </select>
        </div>
            
            <button 
                type="submit" 
                class="btn-filter" 
                aria-label="Apply selected filters"
            >
                Apply Filters
            </button>
        </form>
    </div>

    <!-- Products Section -->
    <section class="products-section">
        <div class="section-header">
            <h2 class="section-title">All Products</h2>
            <span 
                class="product-count" 
                id="productCount" 
                aria-live="polite" 
                aria-atomic="true"
            >
                <?php echo $products ? count($products) : 0; ?> products
            </span>
        </div>

        <div 
            class="products-grid" 
            id="productsGrid" 
            role="list" 
            aria-live="polite"
        >
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

                    <?php 
                    $product_stock = (int)($product['product_qty'] ?? 0);
                    $is_sold_out = $product_stock == 0;
                    ?>
                    <div 
                        class="product-card <?php echo $is_sold_out ? 'sold-out' : ''; ?>"
                        data-product-id="<?php echo $product['product_id']; ?>"
                        role="listitem"
                        tabindex="0"
                        aria-label="<?php echo htmlspecialchars($product['product_title']); ?>, priced at GHS <?php echo number_format($product['product_price'], 2); ?><?php echo $is_sold_out ? ', currently sold out' : ''; ?>"
                        onclick="viewProduct(<?php echo $product['product_id']; ?>)"
                    >
                        <?php if ($is_sold_out): ?>
                        <div class="sold-out-badge">
                            <span>SOLD OUT</span>
                        </div>
                        <?php endif; ?>
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
                            <?php if ($is_sold_out): ?>
                            <button class="btn-add-cart" disabled style="opacity: 0.5; cursor: not-allowed;">
                                Sold Out
                            </button>
                            <?php else: ?>
                            <button 
                                class="btn-add-cart" 
                                onclick="addToCart(event, <?php echo $product['product_id']; ?>)"
                                aria-label="Add <?php echo htmlspecialchars($product['product_title']); ?> to cart"
                            >
                                🛒 Add to Cart
                            </button>
                            <?php endif; ?>
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
            if (event) {
                event.stopPropagation();
            }
            alert('Add to cart functionality will be implemented in future labs. Product ID: ' + productId);
            // TODO: Implement cart functionality
        }

        const searchInput = document.getElementById('searchInput');
        const filtersForm = document.getElementById('productFilters');
        const minPriceInput = document.getElementById('minPrice');
        const maxPriceInput = document.getElementById('maxPrice');
        const minPriceSlider = document.getElementById('minPriceSlider');
        const maxPriceSlider = document.getElementById('maxPriceSlider');
        const sliderTrack = document.getElementById('priceSliderTrack');

        if (searchInput && filtersForm) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    filtersForm.submit();
                }
            });
        }

        if (filtersForm) {
            filtersForm.addEventListener('submit', function() {
                filtersForm.setAttribute('aria-busy', 'true');
            });
        }

        const formatCurrency = (value) => {
            return Number(value || 0).toFixed(2);
        };

        const updateSliderTrack = () => {
            if (!minPriceSlider || !maxPriceSlider || !sliderTrack) return;
            const minValue = parseFloat(minPriceSlider.value);
            const maxValue = parseFloat(maxPriceSlider.value);
            const minPercent = ((minValue - minPriceSlider.min) / (minPriceSlider.max - minPriceSlider.min)) * 100;
            const maxPercent = ((maxValue - maxPriceSlider.min) / (maxPriceSlider.max - maxPriceSlider.min)) * 100;
            sliderTrack.style.background = `linear-gradient(90deg, #e5e7eb ${minPercent}%, #f87171 ${minPercent}%, #f87171 ${maxPercent}%, #e5e7eb ${maxPercent}%)`;
        };

        const syncInputsFromSlider = () => {
            if (!minPriceSlider || !maxPriceSlider || !minPriceInput || !maxPriceInput) return;
            let minVal = parseFloat(minPriceSlider.value);
            let maxVal = parseFloat(maxPriceSlider.value);

            if (minVal > maxVal) {
                [minVal, maxVal] = [maxVal, minVal];
                minPriceSlider.value = minVal;
                maxPriceSlider.value = maxVal;
            }

            minPriceInput.value = minVal === parseFloat(minPriceInput.min) ? '' : formatCurrency(minVal);
            maxPriceInput.value = maxVal === parseFloat(maxPriceInput.max || maxPriceSlider.max) ? '' : formatCurrency(maxVal);
            updateSliderTrack();
        };

        const sanitizeNumber = (value) => {
            if (value === '' || value === null || isNaN(value)) return null;
            return parseFloat(value);
        };

        const syncSliderFromInputs = () => {
            if (!minPriceSlider || !maxPriceSlider || !minPriceInput || !maxPriceInput) return;
            const minVal = sanitizeNumber(minPriceInput.value);
            const maxVal = sanitizeNumber(maxPriceInput.value);

            if (minVal !== null) {
                minPriceSlider.value = Math.max(minVal, parseFloat(minPriceSlider.min));
            } else {
                minPriceSlider.value = minPriceSlider.min;
            }

            if (maxVal !== null) {
                maxPriceSlider.value = Math.min(maxVal, parseFloat(maxPriceSlider.max));
            } else {
                maxPriceSlider.value = maxPriceSlider.max;
            }

            if (parseFloat(minPriceSlider.value) > parseFloat(maxPriceSlider.value)) {
                minPriceSlider.value = maxPriceSlider.value;
            }

            updateSliderTrack();
        };

        if (minPriceSlider && maxPriceSlider) {
            minPriceSlider.addEventListener('input', syncInputsFromSlider);
            maxPriceSlider.addEventListener('input', syncInputsFromSlider);
            syncInputsFromSlider();
        }

        if (minPriceInput) {
            minPriceInput.addEventListener('change', syncSliderFromInputs);
            minPriceInput.addEventListener('blur', syncSliderFromInputs);
        }

        if (maxPriceInput) {
            maxPriceInput.addEventListener('change', syncSliderFromInputs);
            maxPriceInput.addEventListener('blur', syncSliderFromInputs);
        }

        document.querySelectorAll('.product-card').forEach((card) => {
            card.addEventListener('keypress', function(event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    const productId = card.getAttribute('data-product-id');
                    if (productId) {
                        viewProduct(productId);
            }
                }
            });
        });
    </script>
        <!-- Include cart.js for add to cart functionality -->
    <script src="../js/cart.js"></script>
</body>
</html>