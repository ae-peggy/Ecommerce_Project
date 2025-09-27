<?php
header('Content-Type: application/json');

// Include core session management functions
require_once '../settings/core.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log all POST data for debugging
error_log("Delete category POST data received: " . print_r($_POST, true));

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

// Include the category controller
require_once '../controllers/category_controller.php';

// Collect form data safely
$cat_id = (int)($_POST['cat_id'] ?? 0);
$created_by = get_user_id(); // Get current admin user ID

// Log collected data
error_log("Deleting category - ID: $cat_id, User: $created_by");

// Step 1: Check required fields
if (empty($cat_id)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category ID is required'
    ]);
    exit();
}

// Step 2: Check if category exists and belongs to current user
$existing_category = get_category_by_id_ctr($cat_id, $created_by);
if (!$existing_category) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category not found or you do not have permission to delete it'
    ]);
    exit();
}

// Step 3: Delete category
error_log("Attempting to delete category: $cat_id ({$existing_category['cat_name']})");
try {
    $result = delete_category_ctr($cat_id, $created_by);
    
    if ($result) {
        error_log("Category deleted successfully: $cat_id");
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
    error_log("Error deleting category: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while deleting the category.'
    ]);
}
?>