<?php
/**
 * Paystack Initialize Transaction
 * Initializes a payment transaction with Paystack gateway
 */

header('Content-Type: application/json');
require_once '../settings/core.php';
require_once '../settings/paystack_config.php';

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please login to complete payment'
    ]);
    exit();
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$amount = isset($input['amount']) ? floatval($input['amount']) : 0;
$customer_email = isset($input['email']) ? trim($input['email']) : '';

if (!$amount || !$customer_email) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid amount or email'
    ]);
    exit();
}

// Validate amount
if ($amount <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Amount must be greater than 0'
    ]);
    exit();
}

// Validate email
if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email address'
    ]);
    exit();
}

try {
    // Generate unique reference
    $customer_id = get_user_id();
    $reference = 'AYA-' . $customer_id . '-' . time();
    
    // Initialize Paystack transaction
    $paystack_response = paystack_initialize_transaction($amount, $customer_email, $reference);
    
    if (!$paystack_response) {
        throw new Exception("No response from Paystack API");
    }
    
    if (isset($paystack_response['status']) && $paystack_response['status'] === true) {
        // Store transaction reference in session for verification later
        $_SESSION['paystack_ref'] = $reference;
        $_SESSION['paystack_amount'] = $amount;
        $_SESSION['paystack_timestamp'] = time();
        
        echo json_encode([
            'status' => 'success',
            'authorization_url' => $paystack_response['data']['authorization_url'],
            'reference' => $reference,
            'access_code' => $paystack_response['data']['access_code'],
            'message' => 'Redirecting to payment gateway...'
        ]);
    } else {
        $error_message = $paystack_response['message'] ?? 'Payment gateway error';
        throw new Exception($error_message);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to initialize payment: ' . $e->getMessage()
    ]);
}
?>
