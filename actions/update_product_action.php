<?php
/**
 * Update Product Action
 * Handles updating existing products by admin users
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

// Check if user is admin
if (!is_admin()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be an admin to update products'
    ]);
    exit();
}

require_once '../controllers/product_controller.php';

// Collect and sanitize form data
$product_id = (int)($_POST['product_id'] ?? 0);
$cat_id = (int)($_POST['product_cat'] ?? 0);
$brand_id = (int)($_POST['product_brand'] ?? 0);
$title = trim($_POST['product_title'] ?? '');
$price = floatval($_POST['product_price'] ?? 0);
$desc = trim($_POST['product_desc'] ?? '');
$image = trim($_POST['product_image'] ?? '');
$keywords = trim($_POST['product_keywords'] ?? '');
$qty = isset($_POST['product_qty']) ? (int)$_POST['product_qty'] : 0;
$artisan_id = !empty($_POST['artisan_id']) ? (int)$_POST['artisan_id'] : null;
$created_by = get_user_id();

// Validate required fields
if ($product_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid product ID'
    ]);
    exit();
}

if (empty($title)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product title is required'
    ]);
    exit();
}

if ($cat_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please select a category'
    ]);
    exit();
}

if ($brand_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please select a brand'
    ]);
    exit();
}

if ($price <= 0) {
if ($qty < 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Stock quantity cannot be negative'
    ]);
    exit();
}

    echo json_encode([
        'status' => 'error',
        'message' => 'Product price must be greater than zero'
    ]);
    exit();
}

// Validate title length
if (strlen($title) < 3) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product title must be at least 3 characters long'
    ]);
    exit();
}

if (strlen($title) > 200) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product title must be less than 200 characters'
    ]);
    exit();
}

// Verify product exists and user has permission
$existing_product = get_product_by_id_and_user_ctr($product_id, $created_by);
if (!$existing_product) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product not found or you do not have permission to edit it'
    ]);
    exit();
}

// Update product in database
try {
    $result = update_product_ctr($product_id, $cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $qty, $created_by, $artisan_id);
    
    if ($result) {
        log_user_activity("Updated product: {$existing_product['product_title']} to $title (ID: $product_id)");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Product updated successfully!',
            'product_id' => $product_id,
            'product_title' => $title
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update product. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while updating the product.'
    ]);
}
?>