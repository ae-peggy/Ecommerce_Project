<?php
header('Content-Type: application/json');

// Include core session management functions
require_once '../settings/core.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log POST data for debugging
error_log("Empty cart action called");

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please login to manage your cart'
    ]);
    exit();
}

// Include the cart controller
require_once '../controllers/cart_controller.php';

$customer_id = get_user_id();

error_log("Emptying cart for customer: $customer_id");

// Empty cart
try {
    $result = empty_cart_ctr($customer_id);
    
    if ($result) {
        error_log("Cart emptied successfully");
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
    error_log("Error emptying cart: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while emptying cart.'
    ]);
}
?>