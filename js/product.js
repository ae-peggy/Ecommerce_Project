// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Load products when page loads
    loadProducts();
    
    // Product form submission
    const productForm = document.getElementById('productForm');
    if (productForm) {
        productForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveProduct();
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
        
        const imageSrc = product.product_image ? `../${product.product_image}` : 'https://via.placeholder.com/280x200?text=No+Image';
        
        card.innerHTML = `
            <img src="${imageSrc}" alt="${escapeHtml(product.product_title)}" class="product-image" onerror="this.src='https://via.placeholder.com/280x200?text=No+Image'">
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
    const form = document.getElementById('productForm');
    const title = document.getElementById('modalTitle');
    
    // Reset form
    form.reset();
    document.getElementById('productId').value = '';
    document.getElementById('productImagePath').value = '';
    
    // Hide image preview
    const preview = document.getElementById('imagePreview');
    if (preview) {
        preview.style.display = 'none';
    }
    
    // Set title
    title.textContent = 'Add New Product';
    
    // Show modal
    modal.style.display = 'block';
}

/**
 * Close product modal
 */
function closeProductModal() {
    const modal = document.getElementById('productModal');
    modal.style.display = 'none';
}

/**
 * Edit product
 */
function editProduct(product) {
    const modal = document.getElementById('productModal');
    const title = document.getElementById('modalTitle');
    
    // Set title
    title.textContent = 'Edit Product';
    
    // Fill form with product data
    document.getElementById('productId').value = product.product_id;
    document.getElementById('productTitle').value = product.product_title;
    document.getElementById('productCategory').value = product.product_cat;
    document.getElementById('productBrand').value = product.product_brand;
    document.getElementById('productPrice').value = product.product_price;
    document.getElementById('productDesc').value = product.product_desc || '';
    document.getElementById('productKeywords').value = product.product_keywords || '';
    document.getElementById('productImagePath').value = product.product_image || '';
    
    // Show image preview if exists
    const preview = document.getElementById('imagePreview');
    if (product.product_image) {
        preview.src = `../${product.product_image}`;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
    
    // Show modal
    modal.style.display = 'block';
}

/**
 * View product details (navigate to single product page)
 */
function viewProduct(productId) {
    // For now, just show alert - will implement in Week 7
    alert('View product functionality will be implemented in Week 7');
    // Future: window.location.href = `../view/single_product.php?id=${productId}`;
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
                // Store image path
                document.getElementById('productImagePath').value = data.file_path;
                
                // Show preview
                const preview = document.getElementById('imagePreview');
                preview.src = `../${data.file_path}`;
                preview.style.display = 'block';
                
                showMessage('Image uploaded successfully!', 'success');
            } else {
                showMessage(data.message, 'error');
            }
        } catch (e) {
            console.error('JSON Parse error:', e);
            showMessage('Server response error: ' + text, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Failed to upload image', 'error');
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
function saveProduct() {
    const form = document.getElementById('productForm');
    const productId = document.getElementById('productId').value;
    const submitBtn = document.getElementById('submitProductBtn');
    
    // Validate form
    if (!validateProductForm()) {
        return;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.textContent = productId ? 'Updating...' : 'Saving...';
    
    const formData = new FormData(form);
    
    // Determine action URL
    const actionUrl = productId ? '../actions/update_product_action.php' : '../actions/add_product_action.php';
    
    fetch(actionUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        console.log('Save product response:', text);
        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                showMessage(data.message, 'success');
                closeProductModal();
                loadProducts(); // Reload products list
            } else {
                showMessage(data.message, 'error');
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
        submitBtn.disabled = false;
        submitBtn.textContent = 'Save Product';
    });
}

/**
 * Validate product form
 */
function validateProductForm() {
    let isValid = true;
    
    const title = document.getElementById('productTitle').value.trim();
    const category = document.getElementById('productCategory').value;
    const brand = document.getElementById('productBrand').value;
    const price = parseFloat(document.getElementById('productPrice').value);
    
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

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('productModal');
    if (event.target === modal) {
        closeProductModal();
    }
}