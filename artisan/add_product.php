<?php
require_once '../settings/core.php';
require_artisan('../login/login.php');

$artisan_id = get_artisan_id();
$artisan_tier = $_SESSION['artisan_tier'] ?? null;

// Block tier 2 artisans from accessing this page
if ($artisan_tier == 2) {
    header('Location: dashboard.php?error=access_denied');
    exit();
}

$business_name = $_SESSION['business_name'] ?? '';

// Get artisan details to ensure we have current business_name
if ($artisan_id) {
    $artisan_info = get_artisan_details($artisan_id);
    if ($artisan_info) {
        $business_name = $artisan_info['business_name'];
        $artisan_tier = $artisan_info['tier'];
    }
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
    <title>Add Product - Aya Crafts Artisan Portal</title>
    <link rel="stylesheet" href="../css/artisan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <!-- Main Content -->
    <div class="artisan-main-content">
        <div class="artisan-section-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h1>Add New Product</h1>
                <a href="my_products.php" class="artisan-btn artisan-btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Products
                </a>
            </div>
                <form id="productForm" data-context="artisan" novalidate>
                    <input type="hidden" id="productId" name="product_id" value="">
                    <input type="hidden" id="productImagePath" name="product_image_path" value="">
                    <input type="hidden" name="action" value="add">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                        <!-- Product Title -->
                        <div class="artisan-form-group" style="grid-column: 1 / -1;">
                            <label for="productTitle">Product Title *</label>
                            <input type="text" id="productTitle" name="product_title" 
                                   class="artisan-form-control" required>
                        </div>

                        <!-- Category -->
                        <div class="artisan-form-group">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <label for="productCategory">Category *</label>
                                <?php if ($artisan_tier == 1): ?>
                                    <button type="button" onclick="openCategoryModal()" style="font-size: 12px; color: var(--primary); text-decoration: none; background: none; border: none; cursor: pointer; padding: 0; display: flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-plus"></i> Add New Category
                                    </button>
                                <?php endif; ?>
                            </div>
                            <select id="productCategory" name="product_cat" class="artisan-form-control" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['cat_id']; ?>">
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
                                <input type="hidden" id="productBrand" name="product_brand" value="<?php echo $tier1_brand_id ?? ''; ?>">
                                <div style="padding: 12px; background: #f3f4f6; border-radius: 8px; border: 1px solid #e5e7eb;">
                                    <strong><?php echo htmlspecialchars($business_name); ?></strong>
                                    <p style="font-size: 12px; color: #6b7280; margin: 4px 0 0 0;">Your brand is automatically set to your business name</p>
                                </div>
                            <?php else: ?>
                                <!-- Tier 2: Manual selection -->
                                <select id="productBrand" name="product_brand" class="artisan-form-control" required>
                                    <option value="">Select Brand</option>
                                    <?php foreach ($brands as $brand): ?>
                                        <option value="<?php echo $brand['brand_id']; ?>">
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
                                   class="artisan-form-control" step="0.01" min="0" required>
                        </div>

                        <!-- Quantity -->
                        <div class="artisan-form-group">
                            <label for="productQty">Stock Quantity *</label>
                            <input type="number" id="productQty" name="product_qty" 
                                   class="artisan-form-control" min="0" required>
                        </div>

                        <!-- Keywords -->
                        <div class="artisan-form-group" style="grid-column: 1 / -1;">
                            <label for="productKeywords">Keywords (comma-separated)</label>
                            <input type="text" id="productKeywords" name="product_keywords" 
                                   class="artisan-form-control" placeholder="e.g., handmade, ceramic, blue">
                        </div>

                        <!-- Description (Full Width) -->
                        <div class="artisan-form-group" style="grid-column: 1 / -1;">
                            <label for="productDesc">Product Description *</label>
                            <textarea id="productDesc" name="product_desc" 
                                      class="artisan-form-control" rows="5" required></textarea>
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
                                    <i class="fas fa-upload"></i> Upload Image
                                </button>
                                <p style="font-size: 12px; color: #6b7280; margin-top: 12px;">
                                    Upload your image before saving the product.
                                </p>
                                <img id="imagePreview" style="display:none; max-width: 100%; max-height: 300px; margin-top: 16px; border-radius: 8px; object-fit: cover;" alt="Product preview"
                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/300x300?text=Image+Not+Found'; this.style.display='block';">
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                        <button type="submit" class="artisan-btn artisan-btn-primary" id="submitProductBtn">
                            <i class="fas fa-save"></i> Save Product
                        </button>
                        <button type="reset" class="artisan-btn artisan-btn-secondary">
                            <i class="fas fa-redo"></i> Reset Form
                        </button>
                    </div>
                </form>
        </div>
    </div>

    <!-- Category Modal -->
    <div id="categoryModal" class="artisan-modal" style="display: none;">
        <div class="artisan-modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="margin: 0; font-family: 'Cormorant Garamond', serif; font-size: 28px; color: #111827;">
                    <i class="fas fa-plus-circle" style="color: var(--primary);"></i> Add New Category
                </h2>
                <button type="button" onclick="closeCategoryModal()" style="background: none; border: none; font-size: 28px; color: #6b7280; cursor: pointer; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.3s ease;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="categoryForm" onsubmit="addCategory(event); return false;">
                <div class="artisan-form-group">
                    <label for="newCategoryName">Category Name *</label>
                    <input type="text" id="newCategoryName" name="cat_name" 
                           class="artisan-form-control" 
                           placeholder="e.g., Handmade Pottery, Traditional Textiles"
                           required 
                           minlength="2" 
                           maxlength="100"
                           pattern="[a-zA-Z0-9\s\-&']+"
                           title="Category name can only contain letters, numbers, spaces, hyphens, apostrophes, and ampersands">
                    <div id="categoryError" style="display: none; color: #dc2626; font-size: 13px; margin-top: 8px;"></div>
                </div>
                
                <div style="display: flex; gap: 12px; margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                    <button type="submit" class="artisan-btn artisan-btn-primary" id="submitCategoryBtn">
                        <i class="fas fa-save"></i> Add Category
                    </button>
                    <button type="button" onclick="closeCategoryModal()" class="artisan-btn artisan-btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .artisan-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            backdrop-filter: blur(4px);
        }

        .artisan-modal-content {
            background: white;
            border-radius: 20px;
            padding: 32px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .artisan-modal-content button[onclick*="closeCategoryModal"]:hover {
            background: #f3f4f6;
            color: #dc2626;
        }
    </style>

    <script src="../js/product.js"></script>
    <script>
        // Mobile menu toggle
        document.getElementById('artisanHamburger')?.addEventListener('click', function() {
            this.classList.toggle('active');
            document.getElementById('artisanNavMenu')?.classList.toggle('active');
        });

        // Category Modal Functions
        function openCategoryModal() {
            const modal = document.getElementById('categoryModal');
            if (modal) {
                modal.style.display = 'flex';
                document.getElementById('newCategoryName').focus();
            }
        }

        function closeCategoryModal() {
            const modal = document.getElementById('categoryModal');
            if (modal) {
                modal.style.display = 'none';
                // Reset form
                document.getElementById('categoryForm').reset();
                document.getElementById('categoryError').style.display = 'none';
                document.getElementById('categoryError').textContent = '';
            }
        }

        // Close modal when clicking outside
        document.getElementById('categoryModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCategoryModal();
            }
        });

        // Add Category Function
        function addCategory(event) {
            event.preventDefault();
            
            const categoryName = document.getElementById('newCategoryName').value.trim();
            const errorDiv = document.getElementById('categoryError');
            const submitBtn = document.getElementById('submitCategoryBtn');
            
            // Clear previous errors
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            
            // Validate
            if (!categoryName) {
                errorDiv.textContent = 'Category name is required';
                errorDiv.style.display = 'block';
                return;
            }
            
            if (categoryName.length < 2) {
                errorDiv.textContent = 'Category name must be at least 2 characters long';
                errorDiv.style.display = 'block';
                return;
            }
            
            if (categoryName.length > 100) {
                errorDiv.textContent = 'Category name must be less than 100 characters';
                errorDiv.style.display = 'block';
                return;
            }
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            
            // Send AJAX request
            const formData = new FormData();
            formData.append('cat_name', categoryName);
            
            fetch('../actions/artisan_category_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Add new category to dropdown
                    const categorySelect = document.getElementById('productCategory');
                    const newOption = document.createElement('option');
                    newOption.value = data.category_id;
                    newOption.textContent = data.category_name;
                    newOption.selected = true;
                    categorySelect.appendChild(newOption);
                    
                    // Show success message
                    showCategoryMessage('Category added successfully!', 'success');
                    
                    // Close modal after short delay
                    setTimeout(() => {
                        closeCategoryModal();
                    }, 1000);
                } else {
                    errorDiv.textContent = data.message || 'Failed to add category';
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.style.display = 'block';
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Add Category';
            });
        }

        function showCategoryMessage(message, type) {
            // Remove existing message
            const existingMessage = document.querySelector('.category-message');
            if (existingMessage) {
                existingMessage.remove();
            }
            
            // Create message element
            const messageDiv = document.createElement('div');
            messageDiv.className = 'category-message';
            messageDiv.textContent = message;
            
            // Style the message
            messageDiv.style.position = 'fixed';
            messageDiv.style.top = '20px';
            messageDiv.style.right = '20px';
            messageDiv.style.padding = '16px 24px';
            messageDiv.style.borderRadius = '8px';
            messageDiv.style.fontWeight = '600';
            messageDiv.style.fontSize = '14px';
            messageDiv.style.zIndex = '10001';
            messageDiv.style.maxWidth = '400px';
            messageDiv.style.boxShadow = '0 4px 20px rgba(0,0,0,0.15)';
            
            if (type === 'success') {
                messageDiv.style.backgroundColor = '#d1fae5';
                messageDiv.style.color = '#065f46';
                messageDiv.style.border = '2px solid #6ee7b7';
            } else {
                messageDiv.style.backgroundColor = '#fee2e2';
                messageDiv.style.color = '#991b1b';
                messageDiv.style.border = '2px solid #fecaca';
            }
            
            // Add to page
            document.body.appendChild(messageDiv);
            
            // Auto-remove after 3 seconds
            setTimeout(() => {
                if (messageDiv && messageDiv.parentNode) {
                    messageDiv.remove();
                }
            }, 3000);
        }
    </script>
</body>
</html>