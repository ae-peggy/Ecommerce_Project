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
    <link rel="stylesheet" href="../css/admin_pages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../admin/includes/nav.php'; ?>

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
                <input type="hidden" id="productId" name="product_id" value="">
                <input type="hidden" id="productImagePath" name="product_image" value="">
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
                        <label for="productPrice">Price (GH₵) *</label>
                        <input type="number" id="productPrice" name="product_price" step="0.01" min="0" placeholder="0.00" required>
                        <div class="error-message" id="productPrice-error"></div>
                    </div>
                    
                    <!-- Stock Quantity -->
                    <div class="form-group">
                        <label for="productQty">Stock Quantity *</label>
                        <input type="number" id="productQty" name="product_qty" min="0" step="1" placeholder="e.g., 25" required>
                        <div class="error-message" id="productQty-error"></div>
                        <small style="color: #6b7280;">Tier 2 artisans rely on this value to manage inventory.</small>
                    </div>
                    
                    <!-- Description -->
                    <div class="form-group full-width">
                        <label for="productDesc">Description *</label>
                        <textarea id="productDesc" name="product_desc" rows="4" placeholder="Describe your product..." required></textarea>
                        <div class="error-message" id="productDescription-error"></div>
                    </div>
                    
                    <!-- Keywords -->
                    <div class="form-group full-width">
                        <label for="productKeywords">Keywords (comma-separated)</label>
                        <input type="text" id="productKeywords" name="product_keywords" placeholder="e.g., handmade, traditional, authentic">
                    </div>
                    
                    <!-- Image Upload -->
                    <div class="form-group full-width">
                        <label for="productImage">Product Image *</label>
                        <div class="file-upload-wrapper">
                            <input type="file" id="productImage" name="product_image" accept="image/*" onchange="previewImageFile(this)">
                            <button type="button" onclick="document.getElementById('productImage').click()" class="btn btn-secondary">Choose File</button>
                            <button type="button" id="uploadBtn" onclick="uploadImage()" class="btn btn-primary" style="margin-left: 10px;">Upload Image</button>
                        </div>
                        <img id="imagePreview" class="image-preview" alt="Preview" style="display: none; max-width: 300px; margin-top: 12px; border-radius: 8px;">
                        <div class="error-message" id="productImage-error"></div>
                        <small style="color: #6b7280; display: block; margin-top: 8px;">Please select a file and click "Upload Image" before saving the product.</small>
                    </div>
                </div>
                
                <div style="display: flex; gap: 12px; margin-top: 30px;">
                    <button type="submit" id="saveProductBtn" class="btn btn-primary">Save Product</button>
                    <button type="button" onclick="closeProductModal()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/product.js"></script>
    <script>
        // Preview image function for admin product form
        function previewImageFile(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
        
        // Ensure editProduct works correctly
        const originalEditProduct = window.editProduct;
        if (originalEditProduct) {
            window.editProduct = function(product) {
                originalEditProduct(product);
                // Clear file input when editing
                const fileInput = document.getElementById('productImage');
                if (fileInput) {
                    fileInput.value = '';
                }
            };
        }
    </script>
</body>
</html>