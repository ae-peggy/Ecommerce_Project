// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Load brands when page loads
    loadBrands();
    
    // Add brand form submission
    const addForm = document.getElementById('addBrandForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addBrand();
        });
    }
    
    // Edit brand form submission
    const editForm = document.getElementById('editBrandForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateBrand();
        });
    }
});

/**
 * Load and display all brands
 */
function loadBrands() {
    fetch('../actions/fetch_brand_action.php', {
        method: 'POST'
    })
    .then(response => response.text())
    .then(text => {
        console.log('Raw response:', text);
        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                displayBrands(data.data);
                updateBrandCount(data.count);
            } else {
                showMessage(data.message, 'error');
                displayBrands([]); // Show empty state
            }
        } catch (e) {
            console.error('JSON Parse error:', e);
            showMessage('Error loading brands: ' + text, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Failed to load brands', 'error');
    });
}

/**
 * Display brands in the table
 */
function displayBrands(brands) {
    const tbody = document.getElementById('brandsTableBody');
    const noDataDiv = document.getElementById('noBrandsMessage');
    
    if (!tbody) return;
    
    // Clear existing content
    tbody.innerHTML = '';
    
    if (brands.length === 0) {
        // Show no data message
        if (noDataDiv) {
            noDataDiv.style.display = 'block';
        }
        return;
    }
    
    // Hide no data message
    if (noDataDiv) {
        noDataDiv.style.display = 'none';
    }
    
    // Add each brand to the table
    brands.forEach((brand, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${index + 1}</td>
            <td><strong>${escapeHtml(brand.brand_name)}</strong></td>
            <td>${formatDate(brand.created_date)}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editBrand(${brand.brand_id}, '${escapeHtml(brand.brand_name)}')">
                    Edit
                </button>
                <button class="btn btn-sm btn-danger" onclick="confirmDeleteBrand(${brand.brand_id}, '${escapeHtml(brand.brand_name)}')">
                    Delete
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

/**
 * Add new brand
 */
function addBrand() {
    const form = document.getElementById('addBrandForm');
    const nameInput = document.getElementById('brandName');
    const submitBtn = document.getElementById('addBrandBtn');
    
    if (!validateBrandName(nameInput.value)) {
        return;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.textContent = 'Adding...';
    
    const formData = new FormData(form);
    
    fetch('../actions/add_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        console.log('Add brand response:', text);
        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                showMessage(data.message, 'success');
                form.reset();
                loadBrands(); // Reload the brands list
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
        showMessage('Failed to add brand', 'error');
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.textContent = 'Add Brand';
    });
}

/**
 * Show edit form for a brand
 */
function editBrand(brandId, brandName) {
    // Show edit modal/section
    const editSection = document.getElementById('editBrandSection');
    const editIdInput = document.getElementById('editBrandId');
    const editNameInput = document.getElementById('editBrandName');
    
    if (editSection && editIdInput && editNameInput) {
        editSection.style.display = 'block';
        editIdInput.value = brandId;
        editNameInput.value = brandName;
        editNameInput.focus();
        
        // Scroll to edit section
        editSection.scrollIntoView({ behavior: 'smooth' });
    }
}

/**
 * Cancel edit operation
 */
function cancelEdit() {
    const editSection = document.getElementById('editBrandSection');
    const editForm = document.getElementById('editBrandForm');
    
    if (editSection) {
        editSection.style.display = 'none';
    }
    if (editForm) {
        editForm.reset();
    }
}

/**
 * Update brand
 */
function updateBrand() {
    const form = document.getElementById('editBrandForm');
    const nameInput = document.getElementById('editBrandName');
    const submitBtn = document.getElementById('updateBrandBtn');
    
    if (!validateBrandName(nameInput.value)) {
        return;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.textContent = 'Updating...';
    
    const formData = new FormData(form);
    
    fetch('../actions/update_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        console.log('Update brand response:', text);
        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                showMessage(data.message, 'success');
                cancelEdit(); // Hide edit form
                loadBrands(); // Reload the brands list
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
        showMessage('Failed to update brand', 'error');
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.textContent = 'Update Brand';
    });
}

/**
 * Confirm brand deletion
 */
function confirmDeleteBrand(brandId, brandName) {
    if (confirm(`Are you sure you want to delete the brand "${brandName}"?\n\nThis action cannot be undone.`)) {
        deleteBrand(brandId, brandName);
    }
}

/**
 * Delete brand
 */
function deleteBrand(brandId, brandName) {
    const formData = new FormData();
    formData.append('brand_id', brandId);
    
    fetch('../actions/delete_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        console.log('Delete brand response:', text);
        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                showMessage(data.message, 'success');
                loadBrands(); // Reload the brands list
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
        showMessage('Failed to delete brand', 'error');
    });
}

/**
 * Validate brand name
 */
function validateBrandName(name) {
    const trimmedName = name.trim();
    
    if (trimmedName.length < 2) {
        showFieldError('brandName', 'Brand name must be at least 2 characters long');
        return false;
    }
    
    if (trimmedName.length > 100) {
        showFieldError('brandName', 'Brand name must be less than 100 characters');
        return false;
    }
    
    // Check format: letters, numbers, spaces, hyphens, apostrophes, ampersands only
    if (!/^[a-zA-Z0-9\s\-&']+$/.test(trimmedName)) {
        showFieldError('brandName', 'Brand name can only contain letters, numbers, spaces, hyphens, apostrophes, and ampersands');
        return false;
    }
    
    clearFieldError('brandName');
    return true;
}

/**
 * Show field-specific error message
 */
function showFieldError(fieldName, message) {
    const field = document.getElementById(fieldName) || document.getElementById('edit' + fieldName.charAt(0).toUpperCase() + fieldName.slice(1));
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
    const field = document.getElementById(fieldName) || document.getElementById('edit' + fieldName.charAt(0).toUpperCase() + fieldName.slice(1));
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
        errorDiv.style.color = '#dc2626';
        errorDiv.style.fontSize = '13px';
        errorDiv.style.marginTop = '6px';
        errorDiv.style.display = 'none';
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
    messageDiv.style.padding = '16px 20px';
    messageDiv.style.borderRadius = '8px';
    messageDiv.style.marginBottom = '20px';
    messageDiv.style.fontWeight = '600';
    messageDiv.style.fontSize = '14px';
    
    if (type === 'success') {
        messageDiv.style.backgroundColor = '#d1fae5';
        messageDiv.style.color = '#065f46';
        messageDiv.style.border = '2px solid #6ee7b7';
    } else {
        messageDiv.style.backgroundColor = '#fee2e2';
        messageDiv.style.color = '#991b1b';
        messageDiv.style.border = '2px solid #fecaca';
    }
    
    // Insert message at the top of the page
    const container = document.querySelector('.container') || document.body;
    container.insertBefore(messageDiv, container.firstChild);
    
    // Auto-remove messages after 5 seconds
    setTimeout(() => {
        if (messageDiv && messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 5000);
    
    // Scroll to top to show message
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/**
 * Update brand count display
 */
function updateBrandCount(count) {
    const countElement = document.getElementById('brandCount');
    if (countElement) {
        countElement.textContent = count;
    }
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Format date for display
 */
function formatDate(dateString) {
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    } catch (e) {
        return dateString; // Return original if parsing fails
    }
}