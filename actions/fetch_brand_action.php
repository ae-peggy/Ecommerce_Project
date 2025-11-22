<?php
header('Content-Type: application/json');

// Include core session management functions
require_once '../settings/core.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
        'message' => 'You must be an admin to view brands'
    ]);
    exit();
}

// Include the brand controller
require_once '../controllers/brand_controller.php';

$created_by = get_user_id(); // Get current admin user ID

error_log("Fetching brands for user: $created_by");

try {
    // Get all brands for this admin user
    $brands = get_brands_by_user_ctr($created_by);
    
    if ($brands !== false) {
        echo json_encode([
            'status' => 'success',
            'data' => $brands,
            'count' => count($brands)
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'data' => [],
            'count' => 0,
            'message' => 'No brands found'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error fetching brands: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while fetching brands.'
    ]);
}
?>