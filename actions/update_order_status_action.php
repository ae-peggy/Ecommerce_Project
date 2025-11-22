<?php
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

// Check if user is artisan or admin
if (!is_artisan() && !is_admin()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]);
    exit();
}

// Get order ID and status
$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$order_status = isset($_POST['order_status']) ? trim($_POST['order_status']) : '';

// Validate inputs
if ($order_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid order ID'
    ]);
    exit();
}

$valid_statuses = ['pending', 'processing', 'completed', 'cancelled'];
if (!in_array(strtolower($order_status), $valid_statuses)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid order status'
    ]);
    exit();
}

// For artisans, verify the order contains their products
if (is_artisan() && !is_admin()) {
    $artisan_id = get_artisan_id();
    require_once '../classes/artisan_class.php';
    $artisan_obj = new artisan_class();
    
    // Check if order contains products from this artisan
    require_once '../classes/order_class.php';
    $order_obj = new order_class();
    $order_products = $order_obj->get_order_products($order_id);
    
    $has_artisan_product = false;
    foreach ($order_products as $item) {
        require_once '../controllers/product_controller.php';
        $product = get_product_by_id_ctr($item['product_id']);
        if ($product && isset($product['artisan_id']) && $product['artisan_id'] == $artisan_id) {
            $has_artisan_product = true;
            break;
        }
    }
    
    if (!$has_artisan_product) {
        echo json_encode([
            'status' => 'error',
            'message' => 'You can only update orders containing your products'
        ]);
        exit();
    }
}

// Update order status
require_once '../controllers/order_controller.php';
$result = update_order_status_ctr($order_id, $order_status);

if ($result) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Order status updated successfully'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update order status'
    ]);
}
?>

