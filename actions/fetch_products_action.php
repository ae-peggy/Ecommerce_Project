<?php
/**
 * Fetch Products Action
 * Retrieves all products for the current admin or artisan user
 */

header('Content-Type: application/json');
require_once '../settings/core.php';

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to perform this action'
    ]);
    exit();
}

// Check if user is admin or artisan
if (!is_admin() && !is_artisan()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be an admin or artisan to view products'
    ]);
    exit();
}

require_once '../controllers/product_controller.php';

$created_by = get_user_id();

try {
    // Get all products for this admin user
    $products = get_products_by_user_ctr($created_by);
    
    if ($products !== false) {
        echo json_encode([
            'status' => 'success',
            'data' => $products,
            'count' => count($products)
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'data' => [],
            'count' => 0,
            'message' => 'No products found'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while fetching products.'
    ]);
}
?>