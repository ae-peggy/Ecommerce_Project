<?php
/**
 * Artisan Category Action
 * Handles category management for Tier 1 artisans
 */

header('Content-Type: application/json');

// Include core session management
require_once '../settings/core.php';
require_once '../classes/artisan_class.php';

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to perform this action'
    ]);
    exit();
}

// Check if user is artisan (Tier 1 only can add categories)
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 2) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Only artisans can perform this action'
    ]);
    exit();
}

// Check if artisan is Tier 1
$artisan_tier = $_SESSION['artisan_tier'] ?? null;
if ($artisan_tier != 1) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Only Tier 1 artisans can add categories'
    ]);
    exit();
}

// Include the category controller
require_once '../controllers/category_controller.php';

// Collect form data safely
$cat_name = trim($_POST['cat_name'] ?? '');
$created_by = get_user_id();

// Validate category name
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

// Validate category name format (letters, numbers, spaces, hyphens, apostrophes, ampersands)
if (!preg_match("/^[a-zA-Z0-9\s\-&']+$/", $cat_name)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category name can only contain letters, numbers, spaces, hyphens, apostrophes, and ampersands'
    ]);
    exit();
}

// Check if category already exists
if (category_exists_ctr($cat_name, $created_by)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'A category with this name already exists'
    ]);
    exit();
}

// Add category
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
    error_log("Error adding category: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while adding the category.'
    ]);
}
?>

