<?php
/**
 * Update Brand Action
 * Handles updating existing brands by admin users
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
$brand_name = trim($_POST['brand_name'] ?? '');
$created_by = get_user_id();

// Validate required fields
if (empty($brand_id) || empty($brand_name)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Brand ID and name are required'
    ]);
    exit();
}

// Validate brand name length
if (strlen($brand_name) < 2) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Brand name must be at least 2 characters long'
    ]);
    exit();
}

if (strlen($brand_name) > 100) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Brand name must be less than 100 characters'
    ]);
    exit();
}

// Validate brand name format
if (!preg_match("/^[a-zA-Z0-9\s\-&']+$/", $brand_name)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Brand name can only contain letters, numbers, spaces, hyphens, apostrophes, and ampersands'
    ]);
    exit();
}

// Verify brand exists and user has permission
$existing_brand = get_brand_by_id_ctr($brand_id, $created_by);
if (!$existing_brand) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Brand not found or you do not have permission to edit it'
    ]);
    exit();
}

// Update brand in database
try {
    $result = update_brand_ctr($brand_id, $brand_name, $created_by);
    
    if ($result) {
        log_user_activity("Updated brand: {$existing_brand['brand_name']} to $brand_name (ID: $brand_id)");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Brand updated successfully!',
            'brand_id' => $brand_id,
            'brand_name' => $brand_name,
            'old_name' => $existing_brand['brand_name']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update brand. Name may already exist.'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while updating the brand.'
    ]);
}
?>