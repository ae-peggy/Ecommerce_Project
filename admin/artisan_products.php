<?php
require_once '../settings/core.php';
require_admin('../login/login.php');

$artisans = get_all_approved_artisans();
$categories = get_all_categories();
$brands = get_all_brands();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Product for Artisan</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h2>Admin Panel</h2>
        </div>
        <ul class="nav-menu">
            <li>
                <a href="dashboard.php">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="artisans.php">
                    <i class="fas fa-users"></i>
                    <span>Artisans</span>
                </a>
            </li>
            <li class="active">
                <a href="artisan_products.php">
                    <i class="fas fa-box"></i>
                    <span>Artisan Products</span>
                </a>
            </li>
            <li>
                <a href="category.php">
                    <i class="fas fa-list"></i>
                    <span>Categories</span>
                </a>
            </li>
            <li>
                <a href="brand.php">
                    <i class="fas fa-tag"></i>
                    <span>Brands</span>
                </a>
            </li>
            <li>
                <a href="product.php">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Products</span>
                </a>
            </li>
            <li>
                <a href="../view/orders.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
            </li>
            <li>
                <a href="../index.php">
                    <i class="fas fa-store"></i>
                    <span>View Store</span>
                </a>
            </li>
            <li>
                <a href="../login/logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h1>Upload Product for Artisan</h1>
            <div class="user-info">
                <a href="artisans.php" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Artisans
                </a>
            </div>
        </div>

        <div class="content-area">
            <div class="section-card">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <span>Use this form to upload products on behalf of artisans who need assistance.</span>
                </div>

                <form id="addArtisanProductForm" enctype="multipart/form-data">
                    <div class="form-grid">
                        <!-- Select Artisan -->
                        <div class="form-group full-width">
                            <label for="artisan_id">Select Artisan *</label>
                            <select id="artisan_id" name="artisan_id" class="form-control" required onchange="loadArtisanInfo()">
                                <option value="">Choose an artisan...</option>
                                <?php foreach ($artisans as $artisan): ?>
                                    <option value="<?php echo $artisan['artisan_id']; ?>" 
                                            data-name="<?php echo htmlspecialchars($artisan['customer_name']); ?>"
                                            data-business="<?php echo htmlspecialchars($artisan['business_name']); ?>">
                                        <?php echo htmlspecialchars($artisan['business_name']); ?> 
                                        (<?php echo htmlspecialchars($artisan['customer_name']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Artisan Info Display -->
                        <div id="artisanInfo" class="form-group full-width" style="display: none;">
                            <div class="info-box">
                                <strong>Selected Artisan:</strong>
                                <p id="artisanDetails"></p>
                            </div>
                        </div>

                        <!-- Product Title -->
                        <div class="form-group">
                            <label for="product_title">Product Title *</label>
                            <input type="text" id="product_title" name="product_title" 
                                   class="form-control" required>
                        </div>

                        <!-- Category -->
                        <div class="form-group">
                            <label for="product_cat">Category *</label>
                            <select id="product_cat" name="product_cat" class="form-control" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['cat_id']; ?>">
                                        <?php echo htmlspecialchars($category['cat_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Brand -->
                        <div class="form-group">
                            <label for="product_brand">Brand *</label>
                            <select id="product_brand" name="product_brand" class="form-control" required>
                                <option value="">Select Brand</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo $brand['brand_id']; ?>">
                                        <?php echo htmlspecialchars($brand['brand_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Price -->
                        <div class="form-group">
                            <label for="product_price">Price (GHS) *</label>
                            <input type="number" id="product_price" name="product_price" 
                                   class="form-control" step="0.01" min="0" required>
                        </div>

                        <!-- Quantity -->
                        <div class="form-group">
                            <label for="product_qty">Stock Quantity *</label>
                            <input type="number" id="product_qty" name="product_qty" 
                                   class="form-control" min="0" required>
                        </div>

                        <!-- Keywords -->
                        <div class="form-group">
                            <label for="product_keywords">Keywords (comma-separated)</label>
                            <input type="text" id="product_keywords" name="product_keywords" 
                                   class="form-control" placeholder="e.g., handmade, ceramic, blue">
                        </div>

                        <!-- Description (Full Width) -->
                        <div class="form-group full-width">
                            <label for="product_desc">Product Description *</label>
                            <textarea id="product_desc" name="product_desc" 
                                      class="form-control" rows="5" required></textarea>
                        </div>

                        <!-- Product Image -->
                        <div class="form-group full-width">
                            <label for="product_image">Product Image *</label>
                            <div class="file-upload-area">
                                <input type="file" id="product_image" name="product_image" 
                                       class="file-input" accept="image/*" required onchange="previewImage(event)">
                                <label for="product_image" class="file-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Click to upload or drag and drop</span>
                                    <small>PNG, JPG, JPEG (MAX. 5MB)</small>
                                </label>
                                <div id="imagePreview" class="image-preview"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Upload Product for Artisan
                        </button>
                        <button type="reset" class="btn-secondary">
                            <i class="fas fa-redo"></i> Reset Form
                        </button>
                    </div>
                </form>
            </div>

            <!-- Recent Uploads -->
            <div class="section-card">
                <h2>Recently Uploaded Products</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Artisan</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="recentUploads">
                            <tr>
                                <td colspan="6" class="text-center">No recent uploads</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
    <script>
        function loadArtisanInfo() {
            const select = document.getElementById('artisan_id');
            const option = select.options[select.selectedIndex];
            const infoDiv = document.getElementById('artisanInfo');
            const detailsDiv = document.getElementById('artisanDetails');
            
            if (select.value) {
                const name = option.dataset.name;
                const business = option.dataset.business;
                detailsDiv.innerHTML = `<strong>${business}</strong><br>Owner: ${name}`;
                infoDiv.style.display = 'block';
            } else {
                infoDiv.style.display = 'none';
            }
        }

        function previewImage(event) {
            const preview = document.getElementById('imagePreview');
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        document.getElementById('addArtisanProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'add_for_artisan');
            
            fetch('../actions/admin_artisan_actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product uploaded successfully for artisan!');
                    this.reset();
                    document.getElementById('artisanInfo').style.display = 'none';
                    document.getElementById('imagePreview').style.display = 'none';
                    loadRecentUploads();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        function loadRecentUploads() {
            // Load recent uploads via AJAX
            fetch('../actions/admin_artisan_actions.php?action=get_recent_uploads')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('recentUploads');
                    if (!data.success || !Array.isArray(data.data) || data.data.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="6" class="text-center">No recent uploads</td>
                            </tr>
                        `;
                        return;
                    }

                    tbody.innerHTML = data.data.map(item => {
                        const uploadedDate = item.created_date ? new Date(item.created_date) : null;
                        const formattedDate = uploadedDate
                            ? uploadedDate.toLocaleDateString(undefined, {year: 'numeric', month: 'short', day: 'numeric'})
                            : '—';
                        return `
                            <tr>
                                <td>${formattedDate}</td>
                                <td>${item.product_title ?? 'N/A'}</td>
                                <td>${item.business_name ?? 'N/A'}</td>
                                <td>GHS ${Number(item.product_price ?? 0).toFixed(2)}</td>
                                <td>${item.product_qty ?? 0}</td>
                                <td><span class="badge badge-success">Uploaded</span></td>
                            </tr>
                        `;
                    }).join('');
                })
                .catch(() => {
                    const tbody = document.getElementById('recentUploads');
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center text-danger">Unable to load recent uploads</td>
                        </tr>
                    `;
                });
        }

        loadRecentUploads();
    </script>
</body>
</html>