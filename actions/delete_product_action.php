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

// Check if user is admin
if (!is_admin()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be an admin to delete products'
    ]);
    exit();
}

// Include the product controller
require_once '../controllers/product_controller.php';

// Collect form data safely
$product_id = (int)($_POST['product_id'] ?? $_GET['product_id'] ?? 0);
$created_by = get_user_id();

// Log collected data
error_log("Deleting product - ID: $product_id, User: $created_by");

// Step 1: Validate required fields
if ($product_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid product ID'
    ]);
    exit();
}

// Step 2: Verify product exists and belongs to current user (admin can delete their own products)
$product = get_product_by_id_and_user_ctr($product_id, $created_by);
if (!$product) {
    // For admin, allow deletion of any product if they own it or if it's a general product
    // Check if product exists at all
    $product_check = get_product_by_id_ctr($product_id);
    if (!$product_check) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Product not found'
        ]);
        exit();
    }
    // Admin can delete any product - proceed
}

// Step 3: Delete product
try {
    $result = delete_product_ctr($product_id, $created_by);
    
    if ($result) {
        log_user_activity("Deleted product ID: $product_id");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Product deleted successfully!'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to delete product. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error deleting product: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while deleting the product.'
    ]);
}
?>

