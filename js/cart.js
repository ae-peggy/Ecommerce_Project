// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Update cart count on page load
    updateCartCount();
    
    // If on cart page, load cart items
    if (document.getElementById('cartItemsContainer')) {
        loadCartItems();
    }
});

/**
 * Add product to cart
 */
function addToCart(productId, qty = 1) {
    // Disable button to prevent double-click
    const buttons = document.querySelectorAll(`[onclick*="addToCart(${productId}"]`);
    buttons.forEach(btn => {
        btn.disabled = true;
        btn.textContent = 'Adding...';
    });
    
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('qty', qty);
    
    fetch('../actions/add_to_cart_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        console.log('Add to cart response:', text);
        try {
            const data = JSON.parse(text);
            
            if (data.status === 'success') {
                showToast(data.message, 'success');
                updateCartCount(data.cart_count);
            } else if (data.redirect) {
                // Redirect to login
                window.location.href = data.redirect;
            } else {
                showToast(data.message, 'error');
            }
        } catch (e) {
            console.error('JSON Parse error:', e);
            showToast('Server error: ' + text, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to add to cart', 'error');
    })
    .finally(() => {
        // Re-enable button
        buttons.forEach(btn => {
            btn.disabled = false;
            btn.textContent = '🛒 Add to Cart';
        });
    });
}

/**
 * Load cart items (for cart.php page)
 */
function loadCartItems() {
    const container = document.getElementById('cartItemsContainer');
    const emptyState = document.getElementById('emptyCartState');
    const cartSummary = document.getElementById('cartSummary');
    
    if (!container) return;
    
    // Show loading state
    container.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px;">Loading cart...</td></tr>';
    
    fetch('../actions/get_cart_action.php', {
        method: 'POST'
    })
    .then(response => response.text())
    .then(text => {
        console.log('Get cart response:', text);
        try {
            const data = JSON.parse(text);
            
            if (data.status === 'success') {
                if (data.items && data.items.length > 0) {
                    displayCartItems(data.items, data.total);
                    emptyState.style.display = 'none';
                    cartSummary.style.display = 'block';
                } else {
                    container.innerHTML = '';
                    emptyState.style.display = 'block';
                    cartSummary.style.display = 'none';
                }
            } else {
                showToast(data.message, 'error');
                container.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px;">Error loading cart</td></tr>';
            }
        } catch (e) {
            console.error('JSON Parse error:', e);
            container.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px;">Error loading cart</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        container.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px;">Failed to load cart</td></tr>';
    });
}

/**
 * Display cart items in table
 */
function displayCartItems(items, total) {
    const container = document.getElementById('cartItemsContainer');
    container.innerHTML = '';
    
    items.forEach((item, index) => {
        const imagePath = item.product_image ? 
            (item.product_image.startsWith('../') ? item.product_image : '../' + item.product_image) :
            'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="80" height="80"%3E%3Crect width="80" height="80" fill="%23fef2f2"/%3E%3Ctext x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-size="12" fill="%23dc2626"%3ENo Image%3C/text%3E%3C/svg%3E';
        
        const row = document.createElement('tr');
        row.id = `cart-item-${item.p_id}`;
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>
                <img src="${escapeHtml(imagePath)}" 
                     alt="${escapeHtml(item.product_title)}" 
                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;"
                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2280%22 height=%2280%22%3E%3Crect width=%2280%22 height=%2280%22 fill=%22%23fef2f2%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2212%22 fill=%22%23dc2626%22%3ENo Image%3C/text%3E%3C/svg%3E'">
            </td>
            <td>
                <strong>${escapeHtml(item.product_title)}</strong><br>
                <small style="color: #6b7280;">${escapeHtml(item.cat_name || 'N/A')} - ${escapeHtml(item.brand_name || 'N/A')}</small>
            </td>
            <td>GHS ${parseFloat(item.product_price).toFixed(2)}</td>
            <td>
                <div style="display: flex; align-items: center; gap: 8px; justify-content: center;">
                    <button onclick="decrementQty(${item.p_id})" class="qty-btn" style="width: 30px; height: 30px; border: 1px solid #e5e7eb; background: white; border-radius: 4px; cursor: pointer; font-size: 16px;">−</button>
                    <input type="number" id="qty-${item.p_id}" value="${item.qty}" min="1" max="99" 
                           onchange="updateQuantity(${item.p_id}, this.value)"
                           style="width: 60px; text-align: center; padding: 6px; border: 1px solid #e5e7eb; border-radius: 4px;">
                    <button onclick="incrementQty(${item.p_id})" class="qty-btn" style="width: 30px; height: 30px; border: 1px solid #e5e7eb; background: white; border-radius: 4px; cursor: pointer; font-size: 16px;">+</button>
                </div>
            </td>
            <td id="subtotal-${item.p_id}">GHS ${parseFloat(item.subtotal).toFixed(2)}</td>
            <td>
                <button onclick="removeFromCart(${item.p_id})" class="btn-remove" style="background: #ef4444; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px;">
                    Remove
                </button>
            </td>
        `;
        container.appendChild(row);
    });
    
    // Update total
    document.getElementById('cartTotal').textContent = `GHS ${parseFloat(total).toFixed(2)}`;
}

/**
 * Increment quantity
 */
function incrementQty(productId) {
    const input = document.getElementById(`qty-${productId}`);
    let currentQty = parseInt(input.value) || 1;
    if (currentQty < 99) {
        input.value = currentQty + 1;
        updateQuantity(productId, input.value);
    }
}

/**
 * Decrement quantity
 */
function decrementQty(productId) {
    const input = document.getElementById(`qty-${productId}`);
    let currentQty = parseInt(input.value) || 1;
    if (currentQty > 1) {
        input.value = currentQty - 1;
        updateQuantity(productId, input.value);
    }
}

/**
 * Update quantity with debouncing
 */
let updateTimeout;
function updateQuantity(productId, qty) {
    clearTimeout(updateTimeout);
    
    updateTimeout = setTimeout(() => {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('qty', qty);
        
        fetch('../actions/update_quantity_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            console.log('Update quantity response:', text);
            try {
                const data = JSON.parse(text);
                
                if (data.status === 'success') {
                    // Update subtotal
                    document.getElementById(`subtotal-${productId}`).textContent = `GHS ${data.subtotal}`;
                    // Update cart total
                    document.getElementById('cartTotal').textContent = `GHS ${data.cart_total}`;
                    // Update cart count
                    updateCartCount(data.cart_count);
                    
                    showToast('Quantity updated', 'success');
                } else {
                    showToast(data.message, 'error');
                }
            } catch (e) {
                console.error('JSON Parse error:', e);
                showToast('Server error', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to update quantity', 'error');
        });
    }, 500); // 500ms debounce
}

/**
 * Remove item from cart
 */
function removeFromCart(productId) {
    if (!confirm('Remove this item from cart?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('product_id', productId);
    
    fetch('../actions/remove_from_cart_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        console.log('Remove from cart response:', text);
        try {
            const data = JSON.parse(text);
            
            if (data.status === 'success') {
                showToast(data.message, 'success');
                updateCartCount(data.cart_count);
                
                // Reload cart items
                loadCartItems();
            } else {
                showToast(data.message, 'error');
            }
        } catch (e) {
            console.error('JSON Parse error:', e);
            showToast('Server error', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to remove item', 'error');
    });
}

/**
 * Empty entire cart
 */
function emptyCart() {
    if (!confirm('Are you sure you want to empty your cart?\n\nThis action cannot be undone.')) {
        return;
    }
    
    fetch('../actions/empty_cart_action.php', {
        method: 'POST'
    })
    .then(response => response.text())
    .then(text => {
        console.log('Empty cart response:', text);
        try {
            const data = JSON.parse(text);
            
            if (data.status === 'success') {
                showToast(data.message, 'success');
                updateCartCount(0);
                
                // Show empty state
                document.getElementById('cartItemsContainer').innerHTML = '';
                document.getElementById('emptyCartState').style.display = 'block';
                document.getElementById('cartSummary').style.display = 'none';
            } else {
                showToast(data.message, 'error');
            }
        } catch (e) {
            console.error('JSON Parse error:', e);
            showToast('Server error', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to empty cart', 'error');
    });
}

/**
 * Update cart count badge
 */
function updateCartCount(count = null) {
    if (count !== null) {
        // Use provided count
        updateBadge(count);
    } else {
        // Fetch count from server
        fetch('../actions/get_cart_count_action.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                updateBadge(data.count);
            }
        })
        .catch(error => {
            console.error('Error fetching cart count:', error);
        });
    }
}

function updateBadge(count) {
    const badges = document.querySelectorAll('.cart-count-badge');
    badges.forEach(badge => {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    });
}

/**
 * Show toast notification
 */
function showToast(message, type) {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.toast-notification');
    existingToasts.forEach(toast => toast.remove());
    
    // Create toast
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.textContent = message;
    
    // Style toast
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.padding = '16px 24px';
    toast.style.borderRadius = '8px';
    toast.style.fontWeight = '600';
    toast.style.fontSize = '14px';
    toast.style.zIndex = '10000';
    toast.style.maxWidth = '400px';
    toast.style.boxShadow = '0 4px 20px rgba(0,0,0,0.15)';
    toast.style.animation = 'slideIn 0.3s ease';
    
    if (type === 'success') {
        toast.style.backgroundColor = '#d1fae5';
        toast.style.color = '#065f46';
        toast.style.border = '2px solid #6ee7b7';
    } else {
        toast.style.backgroundColor = '#fee2e2';
        toast.style.color = '#991b1b';
        toast.style.border = '2px solid #fecaca';
    }
    
    document.body.appendChild(toast);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
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

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
    
    .qty-btn:hover {
        background: #f3f4f6 !important;
    }
    
    .btn-remove:hover {
        background: #dc2626 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }
`;
document.head.appendChild(style);