<?php
/**
 * Delete Category Action
 * Handles the deletion of categories by admin users
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
        'message' => 'You must be an admin to manage categories'
    ]);
    exit();
}

require_once '../controllers/category_controller.php';

// Collect and sanitize form data
$cat_id = (int)($_POST['cat_id'] ?? 0);
$created_by = get_user_id();

// Validate required fields
if (empty($cat_id)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category ID is required'
    ]);
    exit();
}

// Verify category exists and user has permission
$existing_category = get_category_by_id_ctr($cat_id, $created_by);
if (!$existing_category) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category not found or you do not have permission to delete it'
    ]);
    exit();
}

// Delete category from database
try {
    $result = delete_category_ctr($cat_id, $created_by);
    
    if ($result) {
        log_user_activity("Deleted category: {$existing_category['cat_name']} (ID: $cat_id)");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Category deleted successfully!',
            'deleted_category' => [
                'id' => $cat_id,
                'name' => $existing_category['cat_name']
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to delete category. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while deleting the category.'
    ]);
}
?>