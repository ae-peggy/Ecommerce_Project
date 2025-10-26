<?php
// Include core session management functions
require_once '../settings/core.php';

// Check if user is logged in
require_login('../login/login.php');

// Check if user is admin
require_admin('../index.php');

// Get categories and brands for dropdowns
require_once '../controllers/category_controller.php';
require_once '../controllers/brand_controller.php';

$user_id = get_user_id();
$categories = get_categories_by_user_ctr($user_id);
$brands = get_brands_by_user_ctr($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - Aya Crafts</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
        }

          .header {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            padding: 20px 0;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(220, 38, 38, 0.08);
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, 
                #dc2626 0%, #dc2626 10%,
                #991b1b 10%, #991b1b 20%,
                #ef4444 20%, #ef4444 30%,
                #dc2626 30%, #dc2626 40%,
                #b91c1c 40%, #b91c1c 50%,
                #dc2626 50%, #dc2626 60%,
                #991b1b 60%, #991b1b 70%,
                #ef4444 70%, #ef4444 80%,
                #dc2626 80%, #dc2626 90%,
                #b91c1c 90%, #b91c1c 100%
            );
        }

        .header-content {
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
            font-weight: 500;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 0.5px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        
        .page-header {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 
                0 10px 40px rgba(0, 0, 0, 0.06),
                0 0 0 1px rgba(220, 38, 38, 0.05);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, 
                #dc2626 0%, #dc2626 15%,
                #991b1b 15%, #991b1b 30%,
                #ef4444 30%, #ef4444 45%,
                #dc2626 45%, #dc2626 60%,
                #b91c1c 60%, #b91c1c 75%,
                #ef4444 75%, #ef4444 100%
            );
        }

        .page-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 42px;
            font-weight: 500;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }
        .page-subtitle {
            color: #6b7280;
            font-size: 1rem;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 10px;
            text-align: center;
            min-width: 150px;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.25);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.95;
            margin-top: 5px;
        }

        .section {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 
                0 10px 40px rgba(0, 0, 0, 0.06),
                0 0 0 1px rgba(220, 38, 38, 0.05);
            position: relative;
            height: fit-content;
            margin-bottom: 30px;
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem;
            color: #1a1a1a;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f3f4f6;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .file-upload-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        input[type="file"] {
            padding: 10px;
        }

        .image-preview {
            margin-top: 15px;
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            display: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .btn {
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
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
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.35);
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f3f4f6;
        }

        .product-info {
            padding: 20px;
        }

        .product-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: #1a1a1a;
        }

        .product-price {
            font-size: 1.3rem;
            color: #dc2626;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .product-meta {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 15px;
        }

        .product-actions {
            display: flex;
            gap: 10px;
        }

        .btn-sm {
            padding: 8px 18px;
            font-size: 14px;
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }

        .error-message {
            color: #dc2626;
            font-size: 13px;
            margin-top: 6px;
            display: none;
        }

         .nav-links {
            display: flex;
            gap: 10px;
        }

        .nav-link {
            color: #374151;
            text-decoration: none;
            padding: 10px 24px;
            border-radius: 50px;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
            border: 1.5px solid transparent;
        }

        .nav-link:hover {
            background: rgba(220, 38, 38, 0.05);
            border-color: rgba(220, 38, 38, 0.2);
            color: #dc2626;
            transform: translateY(-1px);
        }

        .nav-link.logout {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.2);
        }

        .nav-link.logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.3);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 1000;
            overflow-y: auto;
        }

        .modal-content {
            background: white;
            max-width: 800px;
            margin: 50px auto;
            padding: 40px;
            border-radius: 12px;
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 28px;
            cursor: pointer;
            color: #6b7280;
        }

        .modal-close:hover {
            color: #dc2626;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">Aya Crafts - Product Management</div>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars(get_user_name()); ?>!</span>
                <div class="nav-links">
                    <a href="../index.php" class="nav-link">Home</a>
                    <a href="category.php" class="nav-link">Categories</a>
                    <a href="brand.php" class="nav-link">Brands</a>
                    <a href="product.php" class="nav-link" style="background-color: rgba(255,255,255,0.2);">Products</a>
                    <a href="../login/logout.php" class="nav-link">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Product Management</h1>
            <p class="page-subtitle">Add and manage your product catalog</p>
            
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number" id="productCount">0</div>
                    <div class="stat-label">Total Products</div>
                </div>
            </div>
        </div>

        <!-- Add Product Button -->
        <div class="section">
            <button onclick="openProductModal()" class="btn btn-primary">➕ Add New Product</button>
        </div>

        <!-- Products Grid -->
        <div class="section">
            <h2 class="section-title">Your Products</h2>
            
            <div id="noProductsMessage" class="no-data" style="display: none;">
                <div style="font-size: 4rem; margin-bottom: 20px;">📦</div>
                <h3>No Products Yet</h3>
                <p>Start by adding your first product above.</p>
            </div>
            
            <div class="products-grid" id="productsGrid">
                <!-- Products will be loaded here by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeProductModal()">&times;</span>
            <h2 class="section-title" id="modalTitle">Add New Product</h2>
            
            <form id="productForm">
                <input type="hidden" id="productId" name="product_id">
                <input type="hidden" id="productImagePath" name="product_image">
                
                <div class="form-grid">
                    <!-- Product Title -->
                    <div class="form-group full-width">
                        <label for="productTitle">Product Title *</label>
                        <input type="text" id="productTitle" name="product_title" placeholder="Enter product name" required>
                        <div class="error-message" id="productTitle-error"></div>
                    </div>
                    
                    <!-- Category -->
                    <div class="form-group">
                        <label for="productCategory">Category *</label>
                        <select id="productCategory" name="product_cat" required>
                            <option value="">Select Category</option>
                            <?php 
                            if ($categories && count($categories) > 0):
                                foreach($categories as $cat): 
                            ?>
                                <option value="<?php echo $cat['cat_id']; ?>">
                                    <?php echo htmlspecialchars($cat['cat_name']); ?>
                                </option>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                        </select>
                        <div class="error-message" id="productCategory-error"></div>
                    </div>
                    
                    <!-- Brand -->
                    <div class="form-group">
                        <label for="productBrand">Brand *</label>
                        <select id="productBrand" name="product_brand" required>
                            <option value="">Select Brand</option>
                            <?php 
                            if ($brands && count($brands) > 0):
                                foreach($brands as $brand): 
                            ?>
                                <option value="<?php echo $brand['brand_id']; ?>">
                                    <?php echo htmlspecialchars($brand['brand_name']); ?>
                                </option>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                        </select>
                        <div class="error-message" id="productBrand-error"></div>
                    </div>
                    
                    <!-- Price -->
                    <div class="form-group">
                        <label for="productPrice">Price (GHS) *</label>
                        <input type="number" id="productPrice" name="product_price" step="0.01" min="0.01" placeholder="0.00" required>
                        <div class="error-message" id="productPrice-error"></div>
                    </div>
                    
                    <!-- Keywords -->
                    <div class="form-group">
                        <label for="productKeywords">Keywords</label>
                        <input type="text" id="productKeywords" name="product_keywords" placeholder="e.g., running, sports, comfortable">
                        <div class="error-message" id="productKeywords-error"></div>
                    </div>
                    
                    <!-- Description -->
                    <div class="form-group full-width">
                        <label for="productDesc">Description</label>
                        <textarea id="productDesc" name="product_desc" placeholder="Enter product description"></textarea>
                        <div class="error-message" id="productDesc-error"></div>
                    </div>
                    
                    <!-- Image Upload -->
                    <div class="form-group full-width">
                        <label for="productImage">Product Image</label>
                        <div class="file-upload-wrapper">
                            <input type="file" id="productImage" accept="image/*">
                            <button type="button" onclick="uploadImage()" class="btn btn-secondary" id="uploadBtn">Upload Image</button>
                        </div>
                        <img id="imagePreview" class="image-preview" alt="Product preview">
                        <div class="error-message" id="productImage-error"></div>
                    </div>
                </div>
                
                <div style="display: flex; gap: 12px; margin-top: 30px;">
                    <button type="submit" id="submitProductBtn" class="btn btn-primary">Save Product</button>
                    <button type="button" onclick="closeProductModal()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Include the JavaScript file -->
    <script src="../js/product.js"></script>
</body>
</html>