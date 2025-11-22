<?php
require_once '../settings/core.php';
require_artisan('../login/login.php');

$artisan_id = get_artisan_id();
$artisan_tier = $_SESSION['artisan_tier'] ?? null;
$business_name = $_SESSION['business_name'] ?? '';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: my_products.php');
    exit();
}

// Get artisan details to ensure we have current business_name
if ($artisan_id) {
    $artisan_info = get_artisan_details($artisan_id);
    if ($artisan_info) {
        $business_name = $artisan_info['business_name'];
        $artisan_tier = $artisan_info['tier'];
    }
}

// Get product details
require_once '../controllers/product_controller.php';
$product = get_product_by_id_ctr($product_id);

if (!$product) {
    header('Location: my_products.php');
    exit();
}

// Verify product belongs to this artisan
require_once '../classes/artisan_class.php';
$artisan_obj = new artisan_class();
if (!$artisan_obj->verify_product_ownership($product_id, $artisan_id)) {
    header('Location: my_products.php');
    exit();
}

$categories = get_all_categories();
$brands = get_all_brands();

// For Tier 1: Get or create brand from business_name
$tier1_brand_id = null;
if ($artisan_tier == 1 && !empty($business_name)) {
    require_once '../controllers/brand_controller.php';
    $tier1_brand_id = get_or_create_brand_ctr($business_name, get_user_id());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Aya Crafts Artisan Portal</title>
    <link rel="stylesheet" href="../css/artisan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <!-- Main Content -->
    <div class="artisan-main-content">
        <div class="artisan-section-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h1>Edit Product</h1>
                <a href="my_products.php" class="artisan-btn artisan-btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Products
                </a>
            </div>
                <form id="productForm" data-context="artisan" novalidate>
                    <input type="hidden" id="productId" name="product_id" value="<?php echo $product_id; ?>">
                    <input type="hidden" id="productImagePath" name="product_image_path" value="<?php echo htmlspecialchars($product['product_image'] ?? ''); ?>">
                    <input type="hidden" name="action" value="update">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                        <!-- Product Title -->
                        <div class="artisan-form-group" style="grid-column: 1 / -1;">
                            <label for="productTitle">Product Title *</label>
                            <input type="text" id="productTitle" name="product_title" 
                                   class="artisan-form-control" value="<?php echo htmlspecialchars($product['product_title'] ?? ''); ?>" required>
                        </div>

                        <!-- Category -->
                        <div class="artisan-form-group">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <label for="productCategory">Category *</label>
                                <?php if ($artisan_tier == 1): ?>
                                    <a href="../admin/category.php" target="_blank" style="font-size: 12px; color: var(--primary); text-decoration: none;">
                                        <i class="fas fa-plus"></i> Add New Category
                                    </a>
                                <?php endif; ?>
                            </div>
                            <select id="productCategory" name="product_cat" class="artisan-form-control" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['cat_id']; ?>" <?php echo ($product['product_cat'] == $category['cat_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['cat_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Brand -->
                        <div class="artisan-form-group" id="brandGroup">
                            <label for="productBrand">Brand *</label>
                            <?php if ($artisan_tier == 1): ?>
                                <!-- Tier 1: Auto-populated from business name -->
                                <input type="hidden" id="productBrand" name="product_brand" value="<?php echo $tier1_brand_id ?? $product['product_brand']; ?>">
                                <div style="padding: 12px; background: #f3f4f6; border-radius: 8px; border: 1px solid #e5e7eb;">
                                    <strong><?php echo htmlspecialchars($business_name); ?></strong>
                                    <p style="font-size: 12px; color: #6b7280; margin: 4px 0 0 0;">Your brand is automatically set to your business name</p>
                                </div>
                            <?php else: ?>
                                <!-- Tier 2: Manual selection -->
                                <select id="productBrand" name="product_brand" class="artisan-form-control" required>
                                    <option value="">Select Brand</option>
                                    <?php foreach ($brands as $brand): ?>
                                        <option value="<?php echo $brand['brand_id']; ?>" <?php echo ($product['product_brand'] == $brand['brand_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($brand['brand_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <!-- Price -->
                        <div class="artisan-form-group">
                            <label for="productPrice">Price (GHS) *</label>
                            <input type="number" id="productPrice" name="product_price" 
                                   class="artisan-form-control" step="0.01" min="0" 
                                   value="<?php echo htmlspecialchars($product['product_price'] ?? ''); ?>" required>
                        </div>

                        <!-- Quantity -->
                        <div class="artisan-form-group">
                            <label for="productQty">Stock Quantity *</label>
                            <input type="number" id="productQty" name="product_qty" 
                                   class="artisan-form-control" min="0" 
                                   value="<?php echo htmlspecialchars($product['product_qty'] ?? 0); ?>" required>
                        </div>

                        <!-- Keywords -->
                        <div class="artisan-form-group" style="grid-column: 1 / -1;">
                            <label for="productKeywords">Keywords (comma-separated)</label>
                            <input type="text" id="productKeywords" name="product_keywords" 
                                   class="artisan-form-control" placeholder="e.g., handmade, ceramic, blue"
                                   value="<?php echo htmlspecialchars($product['product_keywords'] ?? ''); ?>">
                        </div>

                        <!-- Description (Full Width) -->
                        <div class="artisan-form-group" style="grid-column: 1 / -1;">
                            <label for="productDesc">Product Description *</label>
                            <textarea id="productDesc" name="product_desc" 
                                      class="artisan-form-control" rows="5" required><?php echo htmlspecialchars($product['product_desc'] ?? ''); ?></textarea>
                        </div>

                        <!-- Product Image -->
                        <div class="artisan-form-group" style="grid-column: 1 / -1;">
                            <label for="productImage">Product Image *</label>
                            <div style="border: 2px dashed #e5e7eb; border-radius: 12px; padding: 24px; text-align: center; background: #fafafa;">
                                <input type="file" id="productImage" 
                                       style="display: none;" accept="image/*">
                                <label for="productImage" style="cursor: pointer; display: block;">
                                    <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: var(--primary); margin-bottom: 12px;"></i>
                                    <div style="font-weight: 500; margin-bottom: 4px;">Click to upload or drag and drop</div>
                                    <small style="color: #6b7280;">PNG, JPG, JPEG, WEBP (MAX. 5MB)</small>
                                </label>
                                <button type="button" class="artisan-btn artisan-btn-primary" id="uploadBtn" onclick="uploadImage()" style="margin-top: 16px;">
                                    <i class="fas fa-upload"></i> Upload New Image
                                </button>
                                <p style="font-size: 12px; color: #6b7280; margin-top: 12px;">
                                    Upload a new image to replace the current one, or keep the existing image.
                                </p>
                                <?php if (!empty($product['product_image'])): 
                                    $image_path = strpos($product['product_image'], 'uploads/') === 0 ? '../' . ltrim($product['product_image'], '/') : '../images/products/' . ltrim($product['product_image'], '/');
                                ?>
                                    <img id="imagePreview" src="<?php echo htmlspecialchars($image_path); ?>" 
                                         style="max-width: 100%; max-height: 300px; margin-top: 16px; border-radius: 8px; object-fit: cover;" 
                                         alt="Product preview"
                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/300x300?text=Image+Not+Found'; this.style.display='block';">
                                <?php else: ?>
                                    <img id="imagePreview" style="display:none; max-width: 100%; max-height: 300px; margin-top: 16px; border-radius: 8px; object-fit: cover;" alt="Product preview"
                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/300x300?text=Image+Not+Found'; this.style.display='block';">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                        <button type="submit" class="artisan-btn artisan-btn-primary" id="submitProductBtn">
                            <i class="fas fa-save"></i> Update Product
                        </button>
                        <a href="my_products.php" class="artisan-btn artisan-btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
        </div>
    </div>

    <script src="../js/product.js"></script>
    <script>
        // Mobile menu toggle
        document.getElementById('artisanHamburger')?.addEventListener('click', function() {
            this.classList.toggle('active');
            document.getElementById('artisanNavMenu')?.classList.toggle('active');
        });
        
        // Show existing image preview on load
        document.addEventListener('DOMContentLoaded', function() {
            const imagePreview = document.getElementById('imagePreview');
            const imagePath = document.getElementById('productImagePath').value;
            if (imagePath && imagePreview) {
                imagePreview.style.display = 'block';
            }
        });
    </script>
</body>
</html>

