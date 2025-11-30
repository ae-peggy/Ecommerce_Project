<?php
/**
 * Add Brand Action
 * Handles the creation of new brands by admin users
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
$brand_name = trim($_POST['brand_name'] ?? '');
$created_by = get_user_id();

// Validate required fields
if (empty($brand_name)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Brand name is required'
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

// Check for duplicate brand name
if (brand_exists_ctr($brand_name, $created_by)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'A brand with this name already exists'
    ]);
    exit();
}

// Add brand to database
try {
    $brand_id = add_brand_ctr($brand_name, $created_by);
    
    if ($brand_id) {
        log_user_activity("Added brand: $brand_name (ID: $brand_id)");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Brand added successfully!',
            'brand_id' => $brand_id,
            'brand_name' => $brand_name
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add brand. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while adding the brand.'
    ]);
}
?>