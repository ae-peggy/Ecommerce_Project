<?php
// Include core session management functions
require_once 'settings/core.php';

// Include product controller
require_once 'controllers/product_controller.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: view/all_product.php');
    exit();
}

// Get product details
$product = get_product_by_id_ctr($product_id);

if (!$product) {
    header('Location: view/all_product.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_title']); ?> - Aya Crafts</title>
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

        /* Breadcrumb */
        .breadcrumb {
            max-width: 1400px;
            margin: 30px auto 0;
            padding: 0 40px;
            color: #6b7280;
            font-size: 14px;
        }

        .breadcrumb a {
            color: #6b7280;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            color: #dc2626;
        }

        /* Product Section */
        .product-section {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 40px;
        }

        .product-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            padding: 60px;
        }

        .product-image-section {
            position: relative;
        }

        .product-image {
            width: 100%;
            height: 600px;
            object-fit: cover;
            border-radius: 12px;
            background: #f3f4f6;
        }

        .product-details-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .product-category {
            font-size: 14px;
            color: #dc2626;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 15px;
        }

        .product-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3rem;
            color: #1a1a1a;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .product-brand {
            font-size: 1.1rem;
            color: #6b7280;
            margin-bottom: 25px;
        }

        .product-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: #dc2626;
            margin-bottom: 30px;
        }

        .product-description {
            color: #4b5563;
            font-size: 1.05rem;
            line-height: 1.8;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 2px solid #f3f4f6;
        }

        .product-meta {
            margin-bottom: 30px;
        }

        .meta-item {
            display: flex;
            margin-bottom: 12px;
            font-size: 15px;
        }

        .meta-label {
            font-weight: 600;
            color: #374151;
            min-width: 120px;
        }

        .meta-value {
            color: #6b7280;
        }

        .product-keywords {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 35px;
        }

        .keyword-tag {
            background: #fef2f2;
            color: #dc2626;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }

        .product-actions {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 16px 40px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            flex: 1;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #374151;
            border: 2px solid #e5e7eb;
        }

        .btn-secondary:hover {
            border-color: #dc2626;
            color: #dc2626;
        }

        @media (max-width: 968px) {
            .product-container {
                grid-template-columns: 1fr;
                gap: 40px;
                padding: 40px;
            }

            .product-image {
                height: 400px;
            }

            .product-title {
                font-size: 2rem;
            }

            .product-price {
                font-size: 2rem;
            }

            .product-actions {
                flex-direction: column;
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

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="index.php">Home</a> / 
        <a href="view/all_product.php">Shop</a> / 
        <span><?php echo htmlspecialchars($product['product_title']); ?></span>
    </div>

    <!-- Product Section -->
    <section class="product-section">
        <div class="product-container">
            <!-- Product Image -->
            <div class="product-image-section">
                <img 
                    src="<?php echo $product['product_image'] ? $product['product_image'] : 'https://via.placeholder.com/600x600?text=No+Image'; ?>"
                    alt="<?php echo htmlspecialchars($product['product_title']); ?>"
                    class="product-image"
                    onerror="this.src='https://via.placeholder.com/600x600?text=No+Image'"
                >
            </div>

            <!-- Product Details -->
            <div class="product-details-section">
                <div class="product-category">
                    <?php echo htmlspecialchars($product['cat_name'] ?? 'Uncategorized'); ?>
                </div>

                <h1 class="product-title">
                    <?php echo htmlspecialchars($product['product_title']); ?>
                </h1>

                <div class="product-brand">
                    by <strong><?php echo htmlspecialchars($product['brand_name'] ?? 'Unknown'); ?></strong>
                </div>

                <div class="product-price">
                    $<?php echo number_format($product['product_price'], 2); ?>
                </div>

                <?php if (!empty($product['product_desc'])): ?>
                <div class="product-description">
                    <?php echo nl2br(htmlspecialchars($product['product_desc'])); ?>
                </div>
                <?php endif; ?>

                <div class="product-meta">
                    <div class="meta-item">
                        <span class="meta-label">Product ID:</span>
                        <span class="meta-value">#<?php echo $product['product_id']; ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Category:</span>
                        <span class="meta-value"><?php echo htmlspecialchars($product['cat_name'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Brand:</span>
                        <span class="meta-value"><?php echo htmlspecialchars($product['brand_name'] ?? 'N/A'); ?></span>
                    </div>
                </div>

                <?php if (!empty($product['product_keywords'])): ?>
                <div class="product-keywords">
                    <?php 
                    $keywords = explode(',', $product['product_keywords']);
                    foreach($keywords as $keyword): 
                        $keyword = trim($keyword);
                        if ($keyword):
                    ?>
                        <span class="keyword-tag"><?php echo htmlspecialchars($keyword); ?></span>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
                <?php endif; ?>

                <div class="product-actions">
                    <button class="btn btn-primary" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                        🛒 Add to Cart
                    </button>
                    <a href="view/all_product.php" class="btn btn-secondary">
                        ← Back to Shop
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script>
        function addToCart(productId) {
            alert('Add to cart functionality will be implemented in future labs. Product ID: ' + productId);
            // TODO: Implement cart functionality
        }
    </script>
</body>
</html>