<?php
/**
 * Fetch Brands Action
 * Retrieves all brands for the current admin user
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
        'message' => 'You must be an admin to view brands'
    ]);
    exit();
}

require_once '../controllers/brand_controller.php';

$created_by = get_user_id();

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
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while fetching brands.'
    ]);
}
?>