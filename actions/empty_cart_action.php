<?php
/**
 * Empty Cart Action
 * Handles clearing all items from the shopping cart
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

$customer_id = get_user_id();

// Empty cart
try {
    $result = empty_cart_ctr($customer_id);
    
    if ($result) {
        log_user_activity("Emptied shopping cart");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Cart emptied successfully',
            'cart_count' => 0,
            'cart_total' => '0.00'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to empty cart. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while emptying cart.'
    ]);
}
?>