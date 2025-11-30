<?php
/**
 * Save Delivery Info Action
 * Stores delivery information in session for checkout
 */

header('Content-Type: application/json');
require_once '../settings/core.php';

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please login first'
    ]);
    exit();
}

// Get delivery information from POST
$input = json_decode(file_get_contents('php://input'), true);

$delivery_location = isset($input['delivery_location']) ? trim($input['delivery_location']) : null;
$recipient_name = isset($input['recipient_name']) ? trim($input['recipient_name']) : null;
$recipient_number = isset($input['recipient_number']) ? trim($input['recipient_number']) : null;
$delivery_notes = isset($input['delivery_notes']) ? trim($input['delivery_notes']) : null;

// Store in session for later use during payment verification
$_SESSION['delivery_info'] = [
    'delivery_location' => $delivery_location,
    'recipient_name' => $recipient_name,
    'recipient_number' => $recipient_number,
    'delivery_notes' => $delivery_notes
];

echo json_encode([
    'status' => 'success',
    'message' => 'Delivery information saved'
]);
?>
