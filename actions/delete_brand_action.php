<?php
header('Content-Type: application/json');

// Include core session management functions
require_once '../settings/core.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log all POST data for debugging
error_log("Delete brand POST data received: " . print_r($_POST, true));

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
        'message' => 'You must be an admin to manage brands'
    ]);
    exit();
}

// Include the brand controller
require_once '../controllers/brand_controller.php';

// Collect form data safely
$brand_id = (int)($_POST['brand_id'] ?? 0);
$created_by = get_user_id(); // Get current admin user ID

// Log collected data
error_log("Deleting brand - ID: $brand_id, User: $created_by");

// Step 1: Check required fields
if (empty($brand_id)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Brand ID is required'
    ]);
    exit();
}

// Step 2: Check if brand exists and belongs to current user
$existing_brand = get_brand_by_id_ctr($brand_id, $created_by);
if (!$existing_brand) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Brand not found or you do not have permission to delete it'
    ]);
    exit();
}

// Step 3: Delete brand
error_log("Attempting to delete brand: $brand_id ({$existing_brand['brand_name']})");
try {
    $result = delete_brand_ctr($brand_id, $created_by);
    
    if ($result) {
        error_log("Brand deleted successfully: $brand_id");
        log_user_activity("Deleted brand: {$existing_brand['brand_name']} (ID: $brand_id)");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Brand deleted successfully!',
            'deleted_brand' => [
                'id' => $brand_id,
                'name' => $existing_brand['brand_name']
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to delete brand. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error deleting brand: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while deleting the brand.'
    ]);
}
?>