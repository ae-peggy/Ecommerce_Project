<?php
header('Content-Type: application/json');

// Include core session management functions
require_once '../settings/core.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log all POST data for debugging
error_log("Add brand POST data received: " . print_r($_POST, true));

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
$brand_name = trim($_POST['brand_name'] ?? '');
$created_by = get_user_id(); // Get current admin user ID

// Log collected data
error_log("Adding brand - Name: $brand_name, Created by: $created_by");

// Step 1: Check required fields
if (empty($brand_name)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Brand name is required'
    ]);
    exit();
}

// Step 2: Validate brand name length
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

// Step 3: Validate brand name format (letters, numbers, spaces, hyphens, apostrophes, ampersands)
if (!preg_match("/^[a-zA-Z0-9\s\-&']+$/", $brand_name)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Brand name can only contain letters, numbers, spaces, hyphens, apostrophes, and ampersands'
    ]);
    exit();
}

// Step 4: Check if brand already exists
if (brand_exists_ctr($brand_name, $created_by)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'A brand with this name already exists'
    ]);
    exit();
}

// Step 5: Add brand
error_log("Attempting to add brand: $brand_name");
try {
    $brand_id = add_brand_ctr($brand_name, $created_by);
    
    if ($brand_id) {
        error_log("Brand added successfully with ID: $brand_id");
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
    error_log("Error adding brand: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while adding the brand.'
    ]);
}
?>