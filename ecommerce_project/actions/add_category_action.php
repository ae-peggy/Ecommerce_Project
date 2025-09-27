<?php
header('Content-Type: application/json');

// Include core session management functions
require_once '../settings/core.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log all POST data for debugging
error_log("Add category POST data received: " . print_r($_POST, true));

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
$cat_name = trim($_POST['cat_name'] ?? '');
$created_by = get_user_id(); // Get current admin user ID

// Log collected data
error_log("Adding category - Name: $cat_name, Created by: $created_by");

// Step 1: Check required fields
if (empty($cat_name)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category name is required'
    ]);
    exit();
}

// Step 2: Validate category name length
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

// Step 3: Validate category name format (letters, numbers, spaces, hyphens only)
if (!preg_match('/^[a-zA-Z0-9\s\-&]+$/', $cat_name)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category name can only contain letters, numbers, spaces, hyphens, and ampersands'
    ]);
    exit();
}

// Step 4: Check if category already exists
if (category_exists_ctr($cat_name, $created_by)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'A category with this name already exists'
    ]);
    exit();
}

// Step 5: Add category
error_log("Attempting to add category: $cat_name");
try {
    $category_id = add_category_ctr($cat_name, $created_by);
    
    if ($category_id) {
        error_log("Category added successfully with ID: $category_id");
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
    error_log("Error adding category: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while adding the category.'
    ]);
}
?>