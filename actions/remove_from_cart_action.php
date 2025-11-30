<?php
/**
 * Remove from Cart Action
 * Handles removing products from the shopping cart
 */

header('Content-Type: application/json');
require_once '../settings/core.php';

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please login to manage your cart'
    ]);
    exit();
}

require_once '../controllers/cart_controller.php';

// Collect and sanitize form data
$product_id = (int)($_POST['product_id'] ?? 0);
$customer_id = get_user_id();

// Validate product_id
if ($product_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid product ID'
    ]);
    exit();
}

// Remove from cart
try {
    $result = remove_from_cart_ctr($product_id, $customer_id);
    
    if ($result) {
        // Get updated cart count and total
        $cart_count = get_cart_count_ctr($customer_id);
        $cart_total = get_cart_total_ctr($customer_id);
        
        log_user_activity("Removed product from cart (Product ID: $product_id)");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Product removed from cart',
            'cart_count' => $cart_count,
            'cart_total' => number_format($cart_total, 2)
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to remove product from cart. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while removing from cart.'
    ]);
}
?>