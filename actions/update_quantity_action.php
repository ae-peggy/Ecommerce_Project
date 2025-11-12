<?php
header('Content-Type: application/json');

// Include core session management functions
require_once '../settings/core.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log POST data for debugging
error_log("Update quantity POST data: " . print_r($_POST, true));

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

// Collect form data safely
$product_id = (int)($_POST['product_id'] ?? 0);
$qty = (int)($_POST['qty'] ?? 1);
$customer_id = get_user_id();

error_log("Updating quantity - Product: $product_id, Qty: $qty, Customer: $customer_id");

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

// Get product price for subtotal calculation
require_once '../controllers/product_controller.php';
$product = get_product_by_id_ctr($product_id);

if (!$product) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product not found'
    ]);
    exit();
}

// Update quantity
try {
    $result = update_cart_item_ctr($product_id, $customer_id, $qty);
    
    if ($result) {
        // Calculate new subtotal
        $subtotal = $product['product_price'] * $qty;
        
        // Get updated cart total and count
        $cart_total = get_cart_total_ctr($customer_id);
        $cart_count = get_cart_count_ctr($customer_id);
        
        error_log("Cart quantity updated successfully");
        log_user_activity("Updated cart quantity for product: {$product['product_title']} (Qty: $qty)");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Quantity updated successfully',
            'product_id' => $product_id,
            'qty' => $qty,
            'subtotal' => number_format($subtotal, 2),
            'cart_total' => number_format($cart_total, 2),
            'cart_count' => $cart_count
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update quantity. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error updating quantity: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while updating quantity.'
    ]);
}
?>