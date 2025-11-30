<?php
/**
 * Fetch Categories Action
 * Retrieves all categories for the current admin user
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
        'message' => 'You must be an admin to view categories'
    ]);
    exit();
}

require_once '../controllers/category_controller.php';

$created_by = get_user_id();

try {
    // Get all categories for this admin user
    $categories = get_categories_by_user_ctr($created_by);
    
    if ($categories !== false) {
        echo json_encode([
            'status' => 'success',
            'data' => $categories,
            'count' => count($categories)
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'data' => [],
            'count' => 0,
            'message' => 'No categories found'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while fetching categories.'
    ]);
}
?>