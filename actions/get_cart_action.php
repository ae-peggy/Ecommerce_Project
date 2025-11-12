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
    
    if ($cart_items !== false) {
        echo json_encode([
            'status' => 'success',
            'items' => $cart_items ? $cart_items : [],
            'total' => $cart_total,
            'count' => count($cart_items ? $cart_items : [])
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'items' => [],
            'total' => 0,
            'count' => 0
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error getting cart: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while fetching cart.'
    ]);
}
?>