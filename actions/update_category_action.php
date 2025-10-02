<?php
header('Content-Type: application/json');

// Include core session management functions
require_once '../settings/core.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log all POST data for debugging
error_log("Update category POST data received: " . print_r($_POST, true));

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
$cat_name = trim($_POST['cat_name'] ?? '');
$created_by = get_user_id(); // Get current admin user ID

// Log collected data
error_log("Updating category - ID: $cat_id, Name: $cat_name, User: $created_by");

// Step 1: Check required fields
if (empty($cat_id) || empty($cat_name)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category ID and name are required'
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

// Step 3: Validate category name format
if (!preg_match('/^[a-zA-Z0-9\s\-&]+$/', $cat_name)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category name can only contain letters, numbers, spaces, hyphens, and ampersands'
    ]);
    exit();
}

// Step 4: Check if category exists and belongs to current user
$existing_category = get_category_by_id_ctr($cat_id, $created_by);
if (!$existing_category) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category not found or you do not have permission to edit it'
    ]);
    exit();
}

// Step 5: Update category
error_log("Attempting to update category: $cat_id to $cat_name");
try {
    $result = update_category_ctr($cat_id, $cat_name, $created_by);
    
    if ($result) {
        error_log("Category updated successfully: $cat_id");
        log_user_activity("Updated category: {$existing_category['cat_name']} to $cat_name (ID: $cat_id)");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Category updated successfully!',
            'category_id' => $cat_id,
            'category_name' => $cat_name,
            'old_name' => $existing_category['cat_name']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update category. Name may already exist.'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error updating category: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while updating the category.'
    ]);
}
?>