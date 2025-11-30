<?php
/**
 * Add Product Action
 * Handles the creation of new products by admin or artisan users
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
if (!is_admin() && !is_artisan()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be an admin or artisan to add products'
    ]);
    exit();
}

require_once '../controllers/product_controller.php';

// Collect and sanitize form data
$cat_id = (int)($_POST['product_cat'] ?? 0);
$brand_id = (int)($_POST['product_brand'] ?? 0);
$title = trim($_POST['product_title'] ?? '');
$price = floatval($_POST['product_price'] ?? 0);
$desc = trim($_POST['product_desc'] ?? '');
$image = trim($_POST['product_image'] ?? '');
$keywords = trim($_POST['product_keywords'] ?? '');
$qty = isset($_POST['product_qty']) ? (int)$_POST['product_qty'] : 0;
$artisan_id = !empty($_POST['artisan_id']) ? (int)$_POST['artisan_id'] : null;
$created_by = get_user_id();

// Validate required fields
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

if ($qty < 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Stock quantity cannot be negative'
    ]);
    exit();
}

// Validate title length
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

// Validate price range
if ($price > 1000000) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product price seems unreasonably high'
    ]);
    exit();
}

// Add product to database
try {
    $product_id = add_product_ctr($cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $qty, $created_by, $artisan_id);
    
    if ($product_id) {
        // Handle temporary upload folder renaming
        if (isset($_SESSION['temp_upload_id']) && !empty($image)) {
            $user_id = $created_by;
            // Use relative paths (like old working code)
            $temp_folder = '../uploads/u' . $user_id . '/' . $_SESSION['temp_upload_id'] . '/';
            $new_folder = '../uploads/u' . $user_id . '/p' . $product_id . '/';
            
            if (is_dir($temp_folder)) {
                // Rename the folder
                if (rename($temp_folder, $new_folder)) {
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
                }
                
                // Clear temp upload session
                unset($_SESSION['temp_upload_id']);
            }
        }
        
        log_user_activity("Added product: $title (ID: $product_id)");
        
        // Get the final image path after folder rename
        $final_image_path = $image;
        if (isset($_SESSION['temp_upload_id']) && !empty($image)) {
            $final_image_path = str_replace($_SESSION['temp_upload_id'], 'p' . $product_id, $image);
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Product added successfully!',
            'product_id' => $product_id,
            'product_title' => $title,
            'image_path' => $final_image_path
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add product. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while adding the product.'
    ]);
}
?>