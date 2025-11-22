<?php
header('Content-Type: application/json');

// Include core session management functions
require_once '../settings/core.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Include the product controller
require_once '../controllers/product_controller.php';

$created_by = get_user_id(); // Get current admin user ID

error_log("Fetching products for user: $created_by");

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
    error_log("Error fetching products: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while fetching products.'
    ]);
}
?>