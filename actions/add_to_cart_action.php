<?php
/**
 * Add to Cart Action
 * Handles adding products to the shopping cart
 */

header('Content-Type: application/json');
require_once '../settings/core.php';

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please login to add items to cart',
        'redirect' => '../login/login.php'
    ]);
    exit();
}

require_once '../controllers/cart_controller.php';

// Collect and sanitize form data
$product_id = (int)($_POST['product_id'] ?? 0);
$qty = (int)($_POST['qty'] ?? 1);
$customer_id = get_user_id();

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

// Check stock availability
require_once '../classes/product_class.php';
$product_obj = new product_class();
$available_stock = (int)($product['product_qty'] ?? 0);

if ($available_stock == 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'This product is sold out'
    ]);
    exit();
}

if ($available_stock < $qty) {
    echo json_encode([
        'status' => 'error',
        'message' => "Only {$available_stock} unit(s) available in stock"
    ]);
    exit();
}

// Check if item already in cart and total would exceed stock
require_once '../classes/cart_class.php';
$cart_obj = new cart_class();
$existing_cart_item = $cart_obj->check_product_in_cart($product_id, $customer_id);
if ($existing_cart_item) {
    $current_cart_qty = (int)$existing_cart_item['qty'];
    $total_requested = $current_cart_qty + $qty;
    if ($total_requested > $available_stock) {
        echo json_encode([
            'status' => 'error',
            'message' => "Cannot add more. You already have {$current_cart_qty} in cart. Only {$available_stock} total available."
        ]);
        exit();
    }
}

// Add to cart
try {
    $result = add_to_cart_ctr($product_id, $customer_id, $qty);
    
    if ($result) {
        // Get updated cart count
        $cart_count = get_cart_count_ctr($customer_id);
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
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while adding to cart.'
    ]);
}
?>