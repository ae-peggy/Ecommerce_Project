<?php
header('Content-Type: application/json');

// Include core session management functions
require_once '../settings/core.php';

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please login to view cart'
    ]);
    exit();
}

// Include the cart controller
require_once '../controllers/cart_controller.php';

$customer_id = get_user_id();

try {
    // Get cart items
    $cart_items = get_user_cart_ctr($customer_id);
    
    // Get cart total
    $cart_total = get_cart_total_ctr($customer_id);
    
    // Ensure cart_items is an array
    if ($cart_items === false || $cart_items === null) {
        $cart_items = [];
    }
    
    // Ensure cart_total is a number
    if ($cart_total === false || $cart_total === null) {
        $cart_total = 0;
    }
    
    error_log("Cart items count: " . count($cart_items) . ", Total: " . $cart_total);
    
    echo json_encode([
        'status' => 'success',
        'items' => $cart_items,
        'total' => $cart_total,
        'count' => count($cart_items)
    ]);
    
} catch (Exception $e) {
    error_log("Error getting cart: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while fetching cart.'
    ]);
}
?>