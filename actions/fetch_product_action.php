<?php
/**
 * Fetch Product Action
 * Retrieves details for a single product by ID
 */

header('Content-Type: application/json');
require_once '../settings/core.php';
require_once '../controllers/product_controller.php';

// Get and validate product ID
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

if ($product_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid product ID'
    ]);
    exit();
}

// Fetch product details from database
$product = get_product_by_id_ctr($product_id);

if ($product) {
    echo json_encode([
        'status' => 'success',
        'product' => $product
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product not found'
    ]);
}
?>
