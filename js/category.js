// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Load categories when page loads
    loadCategories();
    
    // Add category form submission
    const addForm = document.getElementById('addCategoryForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addCategory();
        });
    }
    
    // Edit category form submission
    const editForm = document.getElementById('editCategoryForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateCategory();
        });
    }
});

/**
 * Load and display all categories
 */
function loadCategories() {
    fetch('../actions/fetch_category_action.php', {
        method: 'POST'
    })
    .then(response => response.text())
    .then(text => {
        console.log('Raw response:', text);
        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                displayCategories(data.data);
                updateCategoryCount(data.count);
            } else {
                showMessage(data.message, 'error');
                displayCategories([]); // Show empty state
            }
        } catch (e) {
            console.error('JSON Parse error:', e);
            showMessage('Error loading categories: ' + text, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Failed to load categories', 'error');
    });
}

/**
 * Display categories in the table
 */
function displayCategories(categories) {
    const tbody = document.getElementById('categoriesTableBody');
    const noDataDiv = document.getElementById('noCategoriesMessage');
    
    if (!tbody) return;
    
    // Clear existing content
    tbody.innerHTML = '';
    
    if (categories.length === 0) {
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
    
    // Add each category to the table
    categories.forEach((category, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${escapeHtml(category.cat_name)}</td>
            <td>${formatDate(category.created_date)}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editCategory(${category.cat_id}, '${escapeHtml(category.cat_name)}')">
                    Edit
                </button>
                <button class="btn btn-sm btn-danger" onclick="confirmDeleteCategory(${category.cat_id}, '${escapeHtml(category.cat_name)}')">
                    Delete
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

/**
 * Add new category
 */
function addCategory() {
    const form = document.getElementById('addCategoryForm');
    const nameInput = document.getElementById('categoryName');
    const submitBtn = document.getElementById('addCategoryBtn');
    
    if (!validateCategoryName(nameInput.value)) {
        return;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.textContent = 'Adding...';
    
    const formData = new FormData(form);
    
    fetch('../actions/add_category_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        console.log('Add category response:', text);
        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                showMessage(data.message, 'success');
                form.reset();
                loadCategories(); // Reload the categories list
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
        showMessage('Failed to add category', 'error');
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.textContent = 'Add Category';
    });
}

/**
 * Show edit form for a category
 */
function editCategory(catId, catName) {
    // Show edit modal/section
    const editSection = document.getElementById('editCategorySection');
    const editIdInput = document.getElementById('editCategoryId');
    const editNameInput = document.getElementById('editCategoryName');
    
    if (editSection && editIdInput && editNameInput) {
        editSection.style.display = 'block';
        editIdInput.value = catId;
        editNameInput.value = catName;
        editNameInput.focus();
        
        // Scroll to edit section
        editSection.scrollIntoView({ behavior: 'smooth' });
    }
}

/**
 * Cancel edit operation
 */
function cancelEdit() {
    const editSection = document.getElementById('editCategorySection');
    const editForm = document.getElementById('editCategoryForm');
    
    if (editSection) {
        editSection.style.display = 'none';
    }
    if (editForm) {
        editForm.reset();
    }
}

/**
 * Update category
 */
function updateCategory() {
    const form = document.getElementById('editCategoryForm');
    const nameInput = document.getElementById('editCategoryName');
    const submitBtn = document.getElementById('updateCategoryBtn');
    
    if (!validateCategoryName(nameInput.value)) {
        return;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.textContent = 'Updating...';
    
    const formData = new FormData(form);
    
    fetch('../actions/update_category_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        console.log('Update category response:', text);
        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                showMessage(data.message, 'success');
                cancelEdit(); // Hide edit form
                loadCategories(); // Reload the categories list
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
        showMessage('Failed to update category', 'error');
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.textContent = 'Update Category';
    });
}

/**
 * Confirm category deletion
 */
function confirmDeleteCategory(catId, catName) {
    if (confirm(`Are you sure you want to delete the category "${catName}"?\n\nThis action cannot be undone.`)) {
        deleteCategory(catId, catName);
    }
}

/**
 * Delete category
 */
function deleteCategory(catId, catName) {
    const formData = new FormData();
    formData.append('cat_id', catId);
    
    fetch('../actions/delete_category_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        console.log('Delete category response:', text);
        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                showMessage(data.message, 'success');
                loadCategories(); // Reload the categories list
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
        showMessage('Failed to delete category', 'error');
    });
}

/**
 * Validate category name
 */
function validateCategoryName(name) {
    const trimmedName = name.trim();
    
    if (trimmedName.length < 2) {
        showFieldError('categoryName', 'Category name must be at least 2 characters long');
        return false;
    }
    
    if (trimmedName.length > 100) {
        showFieldError('categoryName', 'Category name must be less than 100 characters');
        return false;
    }
    
    // Check format: letters, numbers, spaces, hyphens, ampersands only
    if (!/^[a-zA-Z0-9\s\-&]+$/.test(trimmedName)) {
        showFieldError('categoryName', 'Category name can only contain letters, numbers, spaces, hyphens, and ampersands');
        return false;
    }
    
    clearFieldError('categoryName');
    return true;
}

/**
 * Show field-specific error message
 */
function showFieldError(fieldName, message) {
    const field = document.getElementById(fieldName) || document.getElementById('edit' + fieldName.charAt(0).toUpperCase() + fieldName.slice(1));
    const errorDiv = document.getElementById(fieldName + '-error') || createErrorDiv(field, fieldName);
    
    if (field) {
        field.style.borderColor = '#e74c3c';
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
        field.style.borderColor = '#ddd';
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
        errorDiv.style.color = 'red';
        errorDiv.style.fontSize = '14px';
        errorDiv.style.marginTop = '5px';
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
    messageDiv.style.padding = '15px';
    messageDiv.style.borderRadius = '4px';
    messageDiv.style.marginBottom = '20px';
    messageDiv.style.fontWeight = 'bold';
    
    if (type === 'success') {
        messageDiv.style.backgroundColor = '#d4edda';
        messageDiv.style.color = '#155724';
        messageDiv.style.border = '1px solid #c3e6cb';
    } else {
        messageDiv.style.backgroundColor = '#f8d7da';
        messageDiv.style.color = '#721c24';
        messageDiv.style.border = '1px solid #f5c6cb';
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
 * Update category count display
 */
function updateCategoryCount(count) {
    const countElement = document.getElementById('categoryCount');
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