<?php
header('Content-Type: application/json');

// Include core session management functions
require_once '../settings/core.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log POST data for debugging
error_log("Add to cart POST data: " . print_r($_POST, true));

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please login to add items to cart',
        'redirect' => '../login/login.php'
    ]);
    exit();
}

// Include the cart controller
require_once '../controllers/cart_controller.php';

// Collect form data safely
$product_id = (int)($_POST['product_id'] ?? 0);
$qty = (int)($_POST['qty'] ?? 1);
$customer_id = get_user_id();

error_log("Adding to cart - Product: $product_id, Qty: $qty, Customer: $customer_id");

// Validate product_id
if ($product_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid product ID'
    ]);
    exit();
}

// Validate quantity
if ($qty <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Quantity must be at least 1'
    ]);
    exit();
}

if ($qty > 99) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Maximum quantity is 99'
    ]);
    exit();
}

// Verify product exists
require_once '../controllers/product_controller.php';
$product = get_product_by_id_ctr($product_id);

if (!$product) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product not found'
    ]);
    exit();
}

// Add to cart
try {
    $result = add_to_cart_ctr($product_id, $customer_id, $qty);
    
    if ($result) {
        // Get updated cart count
        $cart_count = get_cart_count_ctr($customer_id);
        
        error_log("Product added to cart successfully");
        log_user_activity("Added product to cart: {$product['product_title']} (Qty: $qty)");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Product added to cart successfully!',
            'product_title' => $product['product_title'],
            'cart_count' => $cart_count
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add product to cart. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error adding to cart: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while adding to cart.'
    ]);
}
?>