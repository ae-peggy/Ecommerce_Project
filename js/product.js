// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Load products when page loads (only if productsGrid exists - admin pages)
    const productsGrid = document.getElementById('productsGrid');
    if (productsGrid) {
    loadProducts();
    }
    
    // Product form submission
    const productForm = document.getElementById('productForm');
    if (productForm) {
        const context = productForm.dataset.context || 'admin';
        productForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveProduct(context);
        });
    }
});

/**
 * Load and display all products
 */
function loadProducts() {
    fetch('../actions/fetch_products_action.php', {
        method: 'POST'
    })
    .then(response => response.text())
    .then(text => {
        console.log('Raw response:', text);
        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                displayProducts(data.data);
                updateProductCount(data.count);
            } else {
                showMessage(data.message, 'error');
                displayProducts([]);
            }
        } catch (e) {
            console.error('JSON Parse error:', e);
            showMessage('Error loading products: ' + text, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Failed to load products', 'error');
    });
}

/**
 * Display products in grid
 */
function displayProducts(products) {
    const grid = document.getElementById('productsGrid');
    const noDataDiv = document.getElementById('noProductsMessage');
    
    if (!grid) return;
    
    grid.innerHTML = '';
    
    if (products.length === 0) {
        if (noDataDiv) {
            noDataDiv.style.display = 'block';
        }
        return;
    }
    
    if (noDataDiv) {
        noDataDiv.style.display = 'none';
    }
    
    products.forEach(product => {
        const card = document.createElement('div');
        card.className = 'product-card';
        
        // Construct image URL safely
        let imageSrc = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="280" height="200"%3E%3Crect width="280" height="200" fill="%23fef2f2"/%3E%3Ctext x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="18" fill="%23dc2626"%3ENo Image%3C/text%3E%3C/svg%3E';
        if (product.product_image && product.product_image.trim() !== '') {
            let imgPath = product.product_image;
            // Remove leading ../ if present
            if (imgPath.startsWith('../')) {
                imgPath = imgPath.substring(3);
            }
            // Ensure it starts with uploads/
            if (!imgPath.startsWith('uploads/')) {
                imgPath = 'uploads/' + imgPath.replace(/^\/+/, '');
            }
            imageSrc = '../' + imgPath;
        }
        
        card.innerHTML = `
            <img src="${imageSrc}" alt="${escapeHtml(product.product_title)}" class="product-image" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22280%22 height=%22200%22%3E%3Crect width=%22280%22 height=%22200%22 fill=%22%23fef2f2%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial, sans-serif%22 font-size=%2218%22 fill=%22%23dc2626%22%3ENo Image%3C/text%3E%3C/svg%3E';">
            <div class="product-info">
                <div class="product-title">${escapeHtml(product.product_title)}</div>
                <div class="product-price">GHS${parseFloat(product.product_price).toFixed(2)}</div>
                <div class="product-meta">
                    <strong>Category:</strong> ${escapeHtml(product.cat_name || 'N/A')}<br>
                    <strong>Brand:</strong> ${escapeHtml(product.brand_name || 'N/A')}
                </div>
                <div class="product-actions">
                    <button class="btn btn-sm btn-primary" onclick='editProduct(${JSON.stringify(product)})'>
                        Edit
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="viewProduct(${product.product_id})">
                        View
                    </button>
                    <button class="btn btn-sm" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: white; border: none;" onclick="deleteProduct(${product.product_id}, '${escapeHtml(product.product_title)}'); return false;">
                        Delete
                    </button>
                </div>
            </div>
        `;
        
        grid.appendChild(card);
    });
}

/**
 * Open product modal for adding
 */
function openProductModal() {
    const modal = document.getElementById('productModal');
    if (!modal) return; // Modal doesn't exist on this page
    
    const form = document.getElementById('productForm');
    const title = document.getElementById('modalTitle');
    
    if (form) {
    // Reset form
    form.reset();
        const productId = document.getElementById('productId');
        if (productId) productId.value = '';
        const productImagePath = document.getElementById('productImagePath');
        if (productImagePath) productImagePath.value = '';
    }
    
    // Hide image preview
    const preview = document.getElementById('imagePreview');
    if (preview) {
        preview.style.display = 'none';
    }
    
    // Set title
    if (title) {
    title.textContent = 'Add New Product';
    }
    
    // Show modal
    modal.style.display = 'block';
}

/**
 * Close product modal
 */
function closeProductModal() {
    const modal = document.getElementById('productModal');
    if (modal) {
    modal.style.display = 'none';
    }
}

/**
 * Edit product
 */
function editProduct(product) {
    const modal = document.getElementById('productModal');
    if (!modal) {
        console.error('Product modal not found');
        return;
    }
    
    const title = document.getElementById('modalTitle');
    if (title) {
        title.textContent = 'Edit Product';
    }
    
    // Fill form with product data
    const productIdField = document.getElementById('productId');
    if (productIdField) {
        productIdField.value = product.product_id || '';
    }
    
    const titleField = document.getElementById('productTitle');
    if (titleField) {
        titleField.value = product.product_title || '';
    }
    
    const categoryField = document.getElementById('productCategory');
    if (categoryField) {
        categoryField.value = product.product_cat || '';
    }
    
    const brandField = document.getElementById('productBrand');
    if (brandField) {
        brandField.value = product.product_brand || '';
    }
    
    const priceField = document.getElementById('productPrice');
    if (priceField) {
        priceField.value = product.product_price || '';
    }
    
    const descField = document.getElementById('productDesc');
    if (descField) {
        descField.value = product.product_desc || '';
    }
    
    const keywordsField = document.getElementById('productKeywords');
    if (keywordsField) {
        keywordsField.value = product.product_keywords || '';
    }
    
    const imagePathField = document.getElementById('productImagePath');
    if (imagePathField) {
        imagePathField.value = product.product_image || '';
    }
    
    // Show image preview if exists
    const preview = document.getElementById('imagePreview');
    if (preview) {
        if (product.product_image && product.product_image.trim() !== '') {
            let imgPath = product.product_image;
            // Remove leading ../ if present
            if (imgPath.startsWith('../')) {
                imgPath = imgPath.substring(3);
            }
            // Ensure it starts with uploads/
            if (!imgPath.startsWith('uploads/')) {
                imgPath = 'uploads/' + imgPath.replace(/^\/+/, '');
            }
            preview.src = '../' + imgPath;
            preview.style.display = 'block';
            preview.onerror = function() {
                this.onerror = null;
                this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="300" height="300"%3E%3Crect width="300" height="300" fill="%23fef2f2"/%3E%3Ctext x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="18" fill="%23dc2626"%3EImage Not Found%3C/text%3E%3C/svg%3E';
            };
        } else {
            preview.style.display = 'none';
        }
    }
    
    // Show modal
    modal.style.display = 'block';
}

/**
 * View product details (display in modal)
 */
function viewProduct(productId) {
    // Fetch product details
    fetch(`../actions/fetch_product_action.php?product_id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.product) {
                displayProductModal(data.product);
            } else {
                showMessage(data.message || 'Product not found', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Failed to load product details', 'error');
        });
}

/**
 * Display product details in a modal
 */
function displayProductModal(product) {
    // Create modal if it doesn't exist
    let modal = document.getElementById('viewProductModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'viewProductModal';
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content" style="max-width: 800px; max-height: 90vh; overflow-y: auto;">
                <span class="modal-close" onclick="closeViewProductModal()" style="position: absolute; top: 20px; right: 20px; font-size: 28px; cursor: pointer; color: #6b7280;">&times;</span>
                <h2 class="section-title" style="margin-top: 0;">Product Details</h2>
                <div id="viewProductContent"></div>
                <div style="display: flex; gap: 12px; margin-top: 25px; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeViewProductModal()">Close</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    const content = document.getElementById('viewProductContent');
    // Construct image URL safely
    let imageSrc = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="300"%3E%3Crect width="400" height="300" fill="%23fef2f2"/%3E%3Ctext x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="18" fill="%23dc2626"%3ENo Image%3C/text%3E%3C/svg%3E';
    if (product.product_image && product.product_image.trim() !== '') {
        let imgPath = product.product_image;
        // Remove leading ../ if present
        if (imgPath.startsWith('../')) {
            imgPath = imgPath.substring(3);
        }
        // Ensure it starts with uploads/
        if (!imgPath.startsWith('uploads/')) {
            imgPath = 'uploads/' + imgPath.replace(/^\/+/, '');
        }
        imageSrc = '../' + imgPath;
    }
    
    content.innerHTML = `
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px;">
            <div>
                <img src="${imageSrc}" alt="${escapeHtml(product.product_title)}" 
                     style="width: 100%; height: 400px; object-fit: cover; border-radius: 12px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);"
                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22300%22%3E%3Crect width=%22400%22 height=%22300%22 fill=%22%23fef2f2%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial, sans-serif%22 font-size=%2218%22 fill=%22%23dc2626%22%3ENo Image%3C/text%3E%3C/svg%3E';">
            </div>
            <div>
                <div style="font-size: 12px; color: #dc2626; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 12px;">
                    ${escapeHtml(product.cat_name || 'Uncategorized')}
                </div>
                <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 32px; font-weight: 500; color: #1a1a1a; margin-bottom: 12px;">
                    ${escapeHtml(product.product_title)}
                </h2>
                <div style="font-size: 14px; color: #6b7280; margin-bottom: 20px;">
                    by <strong>${escapeHtml(product.brand_name || 'Unknown')}</strong>
                </div>
                <div style="font-size: 28px; font-weight: 700; background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 24px;">
                    GHS ${parseFloat(product.product_price || 0).toFixed(2)}
                </div>
                ${product.product_desc ? `
                    <div style="margin-bottom: 24px;">
                        <h3 style="font-weight: 600; color: #374151; margin-bottom: 12px; font-size: 16px;">Description</h3>
                        <p style="color: #6b7280; line-height: 1.7; white-space: pre-wrap;">${escapeHtml(product.product_desc)}</p>
                    </div>
                ` : ''}
                <div style="display: grid; gap: 12px; padding: 20px; background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); border-radius: 12px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 600; color: #6b7280;">Product ID:</span>
                        <span style="color: #1a1a1a;">#${product.product_id}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 600; color: #6b7280;">Category:</span>
                        <span style="color: #1a1a1a;">${escapeHtml(product.cat_name || 'N/A')}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 600; color: #6b7280;">Brand:</span>
                        <span style="color: #1a1a1a;">${escapeHtml(product.brand_name || 'N/A')}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 600; color: #6b7280;">Stock:</span>
                        <span style="color: #1a1a1a;">${product.product_qty || 0} units</span>
                    </div>
                    ${product.artisan_name ? `
                        <div style="display: flex; justify-content: space-between;">
                            <span style="font-weight: 600; color: #6b7280;">Artisan:</span>
                            <span style="color: #1a1a1a;">${escapeHtml(product.artisan_name)}</span>
                        </div>
                    ` : ''}
                </div>
                ${product.product_keywords ? `
                    <div style="margin-top: 20px;">
                        <h3 style="font-weight: 600; color: #374151; margin-bottom: 12px; font-size: 16px;">Keywords</h3>
                        <div style="display: flex; flex-wrap: gap: 8px;">
                            ${product.product_keywords.split(',').map(k => `
                                <span style="padding: 6px 12px; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); color: #dc2626; border-radius: 50px; font-size: 12px; font-weight: 500;">
                                    ${escapeHtml(k.trim())}
                                </span>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
    
    modal.style.display = 'block';
}

function closeViewProductModal() {
    const modal = document.getElementById('viewProductModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

/**
 * Delete product (admin only)
 */
function deleteProduct(productId, productTitle) {
    if (!confirm(`Are you sure you want to delete "${productTitle}"? This action cannot be undone.`)) {
        return;
    }
    
    fetch('../actions/delete_product_action.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + productId
    })
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.status === 'success' || data.success === true) {
                showMessage(data.message || 'Product deleted successfully', 'success');
                // Reload products list
                const productsGrid = document.getElementById('productsGrid');
                if (productsGrid) {
                    loadProducts();
                }
            } else {
                showMessage(data.message || 'Failed to delete product', 'error');
            }
        } catch (e) {
            console.error('JSON Parse error:', e);
            showMessage('Server response error: ' + text, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Failed to delete product', 'error');
    });
}

// Close modal when clicking outside
if (typeof window !== 'undefined') {
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('viewProductModal');
        if (event.target == modal) {
            closeViewProductModal();
        }
    });
}

/**
 * Upload image
 */
function uploadImage() {
    const fileInput = document.getElementById('productImage');
    const uploadBtn = document.getElementById('uploadBtn');
    
    if (!fileInput.files || fileInput.files.length === 0) {
        showMessage('Please select an image file first', 'error');
        return;
    }
    
    const file = fileInput.files[0];
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showMessage('Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed', 'error');
        return;
    }
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        showMessage('Image is too large. Maximum size is 5MB', 'error');
        return;
    }
    
    // Show loading state
    uploadBtn.disabled = true;
    uploadBtn.textContent = 'Uploading...';
    
    const formData = new FormData();
    formData.append('product_image', file);
    
    // Add product_id if editing
    const productId = document.getElementById('productId').value;
    if (productId) {
        formData.append('product_id', productId);
    }
    
    fetch('../actions/upload_product_image_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        console.log('Upload response:', text);
        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                // Validate file_path
                if (!data.file_path || data.file_path.trim() === '') {
                    showMessage('Upload succeeded but no file path returned. Please try again.', 'error');
                    return;
                }
                
                // Store image path
                const imagePathInput = document.getElementById('productImagePath');
                if (imagePathInput) {
                    imagePathInput.value = data.file_path;
                }
                
                // Show preview - construct URL safely
                const preview = document.getElementById('imagePreview');
                if (preview) {
                    // Ensure file_path doesn't already start with ../
                    let imageUrl = data.file_path;
                    if (imageUrl.startsWith('../')) {
                        imageUrl = imageUrl.substring(3); // Remove ../
                    }
                    if (!imageUrl.startsWith('uploads/')) {
                        imageUrl = 'uploads/' + imageUrl.replace(/^\/+/, ''); // Remove leading slashes
                    }
                    
                    // Construct preview URL
                    const previewUrl = '../' + imageUrl;
                    console.log('Setting preview URL:', previewUrl);
                    
                    preview.src = previewUrl;
                    preview.style.display = 'block';
                    preview.onerror = function() {
                        console.error('Preview image failed to load:', previewUrl);
                        this.onerror = null;
                        this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="300" height="300"%3E%3Crect width="300" height="300" fill="%23fef2f2"/%3E%3Ctext x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="18" fill="%23dc2626"%3EImage Not Found%3C/text%3E%3C/svg%3E';
                        showMessage('Uploaded image could not be displayed. The file may not be accessible.', 'error');
                    };
                }
                
                showMessage('Image uploaded successfully!', 'success');
            } else {
                // Clear preview and path on error
                const preview = document.getElementById('imagePreview');
                if (preview) {
                    preview.style.display = 'none';
                    preview.removeAttribute('src');
                }
                const imagePathInput = document.getElementById('productImagePath');
                if (imagePathInput) {
                    imagePathInput.value = '';
                }
                const fileInput = document.getElementById('productImage');
                if (fileInput) {
                    fileInput.value = '';
                }
                showMessage(data.message || 'Image upload failed', 'error');
            }
        } catch (e) {
            console.error('JSON Parse error:', e);
            showMessage('Server response error: ' + text, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Clear preview and path on network error
        const preview = document.getElementById('imagePreview');
        if (preview) {
            preview.style.display = 'none';
            preview.removeAttribute('src');
        }
        const imagePathInput = document.getElementById('productImagePath');
        if (imagePathInput) {
            imagePathInput.value = '';
        }
        showMessage('Failed to upload image. Please check your connection and try again.', 'error');
    })
    .finally(() => {
        // Reset button state
        uploadBtn.disabled = false;
        uploadBtn.textContent = 'Upload Image';
    });
}

/**
 * Save product (add or update)
 */
function saveProduct(context = 'admin') {
    const form = document.getElementById('productForm');
    if (!form) return;
    
    const productIdInput = document.getElementById('productId');
    const productId = productIdInput ? productIdInput.value : '';
    const submitBtn = document.getElementById('submitProductBtn') || form.querySelector('button[type="submit"]');
    
    // Validate form
    if (!validateProductForm(context)) {
        return;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.textContent = productId ? 'Updating...' : 'Saving...';
    
    const formData = new FormData(form);
    
    // Determine action URL
    let actionUrl;
    if (context === 'artisan') {
        actionUrl = '../actions/artisan_product_action.php';
        formData.append('action', productId ? 'update' : 'add');
    } else {
        actionUrl = productId ? '../actions/update_product_action.php' : '../actions/add_product_action.php';
    }
    
    fetch(actionUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        console.log('Save product response:', text);
        try {
            const data = JSON.parse(text);
            if (data.status === 'success' || data.success === true) {
                showMessage(data.message || 'Product saved successfully', 'success');
                
                if (context === 'admin') {
                    const modal = document.getElementById('productModal');
                    if (modal) {
                closeProductModal();
                    }
                    const productsGrid = document.getElementById('productsGrid');
                    if (productsGrid) {
                loadProducts(); // Reload products list
                    }
                } else {
                    // Artisan context: redirect to products page after short delay
                    setTimeout(() => {
                        window.location.href = 'my_products.php';
                    }, 1500);
                }
            } else {
                showMessage(data.message || 'Failed to save product', 'error');
            }
        } catch (e) {
            console.error('JSON Parse error:', e);
            showMessage('Server response error: ' + text, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Failed to save product', 'error');
    })
    .finally(() => {
        // Reset button state
        if (submitBtn) {
        submitBtn.disabled = false;
            submitBtn.textContent = productId ? 'Save Product' : 'Save Product';
        }
    });
}

/**
 * Validate product form
 */
function validateProductForm(context = 'admin') {
    let isValid = true;
    
    const title = document.getElementById('productTitle') ? document.getElementById('productTitle').value.trim() : '';
    const category = document.getElementById('productCategory') ? document.getElementById('productCategory').value : '';
    const brand = document.getElementById('productBrand') ? document.getElementById('productBrand').value : '';
    const price = parseFloat(document.getElementById('productPrice') ? document.getElementById('productPrice').value : 0);
    const qtyField = document.getElementById('productQty');
    const qty = qtyField ? parseInt(qtyField.value, 10) : 0;
    const imagePathInput = document.getElementById('productImagePath');
    const productId = document.getElementById('productId') ? document.getElementById('productId').value : '';
    
    // Validate title
    if (title.length < 3) {
        showFieldError('productTitle', 'Product title must be at least 3 characters long');
        isValid = false;
    } else if (title.length > 200) {
        showFieldError('productTitle', 'Product title must be less than 200 characters');
        isValid = false;
    } else {
        clearFieldError('productTitle');
    }
    
    // Validate category
    if (!category || category === '') {
        showFieldError('productCategory', 'Please select a category');
        isValid = false;
    } else {
        clearFieldError('productCategory');
    }
    
    // Validate brand
    if (!brand || brand === '') {
        showFieldError('productBrand', 'Please select a brand');
        isValid = false;
    } else {
        clearFieldError('productBrand');
    }
    
    // Validate price
    if (isNaN(price) || price <= 0) {
        showFieldError('productPrice', 'Price must be greater than zero');
        isValid = false;
    } else if (price > 1000000) {
        showFieldError('productPrice', 'Price seems unreasonably high');
        isValid = false;
    } else {
        clearFieldError('productPrice');
    }
    
    // Validate quantity if field exists
    if (qtyField) {
        if (isNaN(qty) || qty < 0) {
            showFieldError('productQty', 'Quantity must be zero or greater');
            isValid = false;
        } else {
            clearFieldError('productQty');
        }
    }
    
    // Ensure image uploaded for new artisan entries
    if (context === 'artisan' && !productId) {
        if (!imagePathInput || imagePathInput.value.trim() === '') {
            showMessage('Please upload a product image before saving', 'error');
            // Scroll to image upload area
            const imageSection = document.getElementById('productImage')?.closest('.artisan-form-group');
            if (imageSection) {
                imageSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                imageSection.style.border = '2px solid #dc2626';
                imageSection.style.borderRadius = '12px';
                imageSection.style.padding = '2px';
                setTimeout(() => {
                    imageSection.style.border = '';
                    imageSection.style.padding = '';
                }, 3000);
            }
            isValid = false;
        } else {
            // Clear any previous error styling
            const imageSection = document.getElementById('productImage')?.closest('.artisan-form-group');
            if (imageSection) {
                imageSection.style.border = '';
                imageSection.style.padding = '';
            }
        }
    }
    
    return isValid;
}

/**
 * Show field-specific error message
 */
function showFieldError(fieldName, message) {
    const field = document.getElementById(fieldName);
    const errorDiv = document.getElementById(fieldName + '-error') || createErrorDiv(field, fieldName);
    
    if (field) {
        field.style.borderColor = '#dc2626';
    }
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }
}

/**
 * Clear field error
 */
function clearFieldError(fieldName) {
    const field = document.getElementById(fieldName);
    const errorDiv = document.getElementById(fieldName + '-error');
    
    if (field) {
        field.style.borderColor = '#e5e7eb';
    }
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
}

/**
 * Create error div if it doesn't exist
 */
function createErrorDiv(field, fieldName) {
    if (!field) return null;
    
    let errorDiv = document.getElementById(fieldName + '-error');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = fieldName + '-error';
        errorDiv.className = 'error-message';
        field.parentNode.appendChild(errorDiv);
    }
    return errorDiv;
}

/**
 * Show general message (success or error)
 */
function showMessage(message, type) {
    // Remove any existing message
    const existingMessage = document.querySelector('.alert-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `alert-message alert ${type === 'success' ? 'alert-success' : 'alert-error'}`;
    messageDiv.textContent = message;
    
    // Style the message
    messageDiv.style.position = 'fixed';
    messageDiv.style.top = '20px';
    messageDiv.style.right = '20px';
    messageDiv.style.padding = '16px 24px';
    messageDiv.style.borderRadius = '8px';
    messageDiv.style.fontWeight = '600';
    messageDiv.style.fontSize = '14px';
    messageDiv.style.zIndex = '10000';
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
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (messageDiv && messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 5000);
}

/**
 * Update product count display
 */
function updateProductCount(count) {
    const countElement = document.getElementById('productCount');
    if (countElement) {
        countElement.textContent = count;
    }
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close modal when clicking outside (only if modal exists)
window.onclick = function(event) {
    const modal = document.getElementById('productModal');
    if (modal && event.target === modal) {
        closeProductModal();
    }
}