<?php
/**
 * Add Category Action
 * Handles the creation of new product categories by admin users
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
$cat_name = trim($_POST['cat_name'] ?? '');
$created_by = get_user_id();

// Validate required fields
if (empty($cat_name)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category name is required'
    ]);
    exit();
}

// Validate category name length
if (strlen($cat_name) < 2) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category name must be at least 2 characters long'
    ]);
    exit();
}

if (strlen($cat_name) > 100) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category name must be less than 100 characters'
    ]);
    exit();
}

// Validate category name format
if (!preg_match('/^[a-zA-Z0-9\s\-&]+$/', $cat_name)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category name can only contain letters, numbers, spaces, hyphens, and ampersands'
    ]);
    exit();
}

// Check for duplicate category name
if (category_exists_ctr($cat_name, $created_by)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'A category with this name already exists'
    ]);
    exit();
}

// Add category to database
try {
    $category_id = add_category_ctr($cat_name, $created_by);
    
    if ($category_id) {
        log_user_activity("Added category: $cat_name (ID: $category_id)");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Category added successfully!',
            'category_id' => $category_id,
            'category_name' => $cat_name
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add category. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while adding the category.'
    ]);
}
?>