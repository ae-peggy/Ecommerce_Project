<?php
/**
 * Process Checkout Action
 * Handles order creation and payment processing
 */

header('Content-Type: application/json');
require_once '../settings/core.php';

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please login to complete checkout'
    ]);
    exit();
}

// Include controllers
require_once '../controllers/cart_controller.php';
require_once '../controllers/order_controller.php';
require_once '../controllers/product_controller.php';
require_once '../settings/db_class.php';

$customer_id = get_user_id();
$customer_name = get_user_name();

try {
    // Step 1: Get cart items
    $cart_items = get_user_cart_ctr($customer_id);
    
    if (!$cart_items || count($cart_items) == 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Your cart is empty'
        ]);
        exit();
    }
    
    // Check stock availability for all items
    require_once '../classes/product_class.php';
    $product_obj = new product_class();
    
    foreach ($cart_items as $item) {
        $product_id = (int)$item['p_id'];
        $qty = (int)$item['qty'];
        
        // Check if product exists and has stock
        $product = get_product_by_id_ctr($product_id);
        if (!$product) {
            throw new Exception("Product ID {$product_id} not found");
        }
        
        $available_stock = (int)($product['product_qty'] ?? 0);
        if ($available_stock < $qty) {
            throw new Exception("Insufficient stock for {$product['product_title']}. Available: {$available_stock}, Requested: {$qty}");
        }
        
        if ($available_stock == 0) {
            throw new Exception("{$product['product_title']} is sold out");
        }
    }
    
    // Step 2: Calculate total amount
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['subtotal'];
    }
    
    // Generate unique invoice number
    $invoice_no = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    
    // Get current date
    $order_date = date('Y-m-d');
    $payment_date = date('Y-m-d');
    
    // Get delivery information from POST
    $delivery_location = isset($_POST['delivery_location']) ? trim($_POST['delivery_location']) : null;
    $recipient_name = isset($_POST['recipient_name']) ? trim($_POST['recipient_name']) : null;
    $recipient_number = isset($_POST['recipient_number']) ? trim($_POST['recipient_number']) : null;
    $delivery_notes = isset($_POST['delivery_notes']) ? trim($_POST['delivery_notes']) : null;
    
    // Create database connection for transaction
    $db = new db_connection();
    $conn = $db->db_conn();
    
    // Begin transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Create order with delivery information
        $order_id = create_order_ctr($customer_id, $invoice_no, $order_date, 'Completed', $delivery_location, $recipient_name, $recipient_number, $delivery_notes);
        
        if (!$order_id) {
            throw new Exception("Failed to create order");
        }
        
        // Add order details and reduce stock for each cart item
        foreach ($cart_items as $item) {
            $product_id = (int)$item['p_id'];
            $qty = (int)$item['qty'];
            
            $detail_result = add_order_details_ctr($order_id, $product_id, $qty);
            
            if (!$detail_result) {
                throw new Exception("Failed to add order details for product: " . $product_id);
            }
            
            // Reduce stock quantity
            $stock_reduced = $product_obj->reduce_stock($product_id, $qty);
            if (!$stock_reduced) {
                throw new Exception("Failed to reduce stock for product: " . $product_id);
            }
        }
        
        // Record payment
        $payment_id = record_payment_ctr($total_amount, $customer_id, $order_id, 'GHS', $payment_date);
        
        if (!$payment_id) {
            throw new Exception("Failed to record payment");
        }
        
        // Empty the cart
        $empty_result = empty_cart_ctr($customer_id);
        
        if (!$empty_result) {
            throw new Exception("Failed to empty cart");
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Log user activity
        log_user_activity("Completed checkout - Invoice: $invoice_no, Total: GHS $total_amount");
        
        // Return success response
        echo json_encode([
            'status' => 'success',
            'message' => 'Order placed successfully!',
            'order_id' => $order_id,
            'invoice_no' => $invoice_no,
            'total_amount' => number_format($total_amount, 2),
            'currency' => 'GHS',
            'order_date' => date('F j, Y', strtotime($order_date)),
            'customer_name' => $customer_name,
            'item_count' => count($cart_items)
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        throw $e;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Checkout failed: ' . $e->getMessage()
    ]);
}
?>