<?php
/**
 * Delete Brand Action
 * Handles the deletion of brands by admin users
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
        'message' => 'You must be an admin to manage brands'
    ]);
    exit();
}

require_once '../controllers/brand_controller.php';

// Collect and sanitize form data
$brand_id = (int)($_POST['brand_id'] ?? 0);
$created_by = get_user_id();

// Validate required fields
if (empty($brand_id)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Brand ID is required'
    ]);
    exit();
}

// Verify brand exists and user has permission
$existing_brand = get_brand_by_id_ctr($brand_id, $created_by);
if (!$existing_brand) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Brand not found or you do not have permission to delete it'
    ]);
    exit();
}

// Delete brand from database
try {
    $result = delete_brand_ctr($brand_id, $created_by);
    
    if ($result) {
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
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while deleting the brand.'
    ]);
}
?>