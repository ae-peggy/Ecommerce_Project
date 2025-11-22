<?php
header('Content-Type: application/json');

// Include core session management functions
require_once '../settings/core.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log all POST data for debugging
error_log("Add product POST data received: " . print_r($_POST, true));

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to perform this action'
    ]);
    exit();
}

// Check if user is admin
if (!is_admin() && !is_artisan()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be an admin or artisan to add products'
    ]);
    exit();
}

// Include the product controller
require_once '../controllers/product_controller.php';

// Collect form data safely
$cat_id = (int)($_POST['product_cat'] ?? 0);
$brand_id = (int)($_POST['product_brand'] ?? 0);
$title = trim($_POST['product_title'] ?? '');
$price = floatval($_POST['product_price'] ?? 0);
$desc = trim($_POST['product_desc'] ?? '');
$image = trim($_POST['product_image'] ?? '');
$keywords = trim($_POST['product_keywords'] ?? '');
$created_by = get_user_id();

// Log collected data
error_log("Adding product - Title: $title, Price: $price, Cat: $cat_id, Brand: $brand_id, User: $created_by");

// Step 1: Validate required fields
if (empty($title)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product title is required'
    ]);
    exit();
}

if ($cat_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please select a category'
    ]);
    exit();
}

if ($brand_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please select a brand'
    ]);
    exit();
}

if ($price <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product price must be greater than zero'
    ]);
    exit();
}

// Step 2: Validate title length
if (strlen($title) < 3) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product title must be at least 3 characters long'
    ]);
    exit();
}

if (strlen($title) > 200) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product title must be less than 200 characters'
    ]);
    exit();
}

// Step 3: Validate price
if ($price > 1000000) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product price seems unreasonably high'
    ]);
    exit();
}

// Step 4: Add product
error_log("Attempting to add product: $title");
try {
    $product_id = add_product_ctr($cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $created_by);
    
    if ($product_id) {
        error_log("Product added successfully with ID: $product_id");

        // Session is already started by core.php
        if (isset($_SESSION['temp_upload_id']) && !empty($image)) {
            $user_id = $created_by;
            $temp_folder = '../uploads/u' . $user_id . '/' . $_SESSION['temp_upload_id'] . '/';
            $new_folder = '../uploads/u' . $user_id . '/p' . $product_id . '/';
            
            if (is_dir($temp_folder)) {
                // Rename the folder
                if (rename($temp_folder, $new_folder)) {
                    error_log("Renamed temp folder to product folder: $temp_folder -> $new_folder");
                    
                    // Update image path in database
                    $old_image_path = $image;
                    $new_image_path = str_replace($_SESSION['temp_upload_id'], 'p' . $product_id, $image);
                    
                    // Update the product with new image path
                    require_once '../classes/product_class.php';
                    $product_obj = new product_class();
                    $update_sql = "UPDATE products SET product_image = '" . 
                                  mysqli_real_escape_string($product_obj->db_conn(), $new_image_path) . 
                                  "' WHERE product_id = $product_id";
                    $product_obj->db_write_query($update_sql);
                    
                    error_log("Updated image path: $old_image_path -> $new_image_path");
                } else {
                    error_log("Failed to rename temp folder: $temp_folder");
                }
                
                // Clear temp upload session
                unset($_SESSION['temp_upload_id']);
            }
        }
        
        log_user_activity("Added product: $title (ID: $product_id)");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Product added successfully!',
            'product_id' => $product_id,
            'product_title' => $title
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add product. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error adding product: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while adding the product.'
    ]);
}
?>