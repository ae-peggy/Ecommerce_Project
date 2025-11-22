<?php
require_once '../settings/core.php';

// Check if user is logged in and is admin
if (!is_logged_in() || !is_admin()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]);
    exit();
}

// Check if order_id is provided
if (!isset($_POST['order_id']) || empty($_POST['order_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Order ID is required'
    ]);
    exit();
}

require_once '../controllers/order_controller.php';

$order_id = (int)$_POST['order_id'];

// Delete the order
$result = delete_order_ctr($order_id);

if ($result) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Order deleted successfully'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to delete order. Please try again.'
    ]);
}
?>

