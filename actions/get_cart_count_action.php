<?php
/**
 * Get Cart Count Action
 * Returns the number of items in the user's shopping cart
 */

header('Content-Type: application/json');
require_once '../settings/core.php';
require_once '../controllers/cart_controller.php';

// If user is not logged in, return 0
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'success',
        'count' => 0
    ]);
    exit();
}

$customer_id = get_user_id();

try {
    $count = get_cart_count_ctr($customer_id);
    
    echo json_encode([
        'status' => 'success',
        'count' => $count
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'success',
        'count' => 0
    ]);
}
?>