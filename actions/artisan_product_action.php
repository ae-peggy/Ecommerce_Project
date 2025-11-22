<?php
// Start output buffering to catch any errors
ob_start();

// Set error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors, we'll handle them
ini_set('log_errors', 1);

header('Content-Type: application/json');

// Include core session management (handles session_start)
require_once '../settings/core.php';
require_once '../classes/artisan_class.php';

// Check if user is artisan
if (!is_artisan() && !isset($_SESSION['artisan_id'])) {
    if (ob_get_level() > 0) {
        ob_clean();
    }
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$artisan = new artisan_class();
$artisan_id = (int)$_SESSION['artisan_id'];

function sanitize_upload_path($path) {
    $path = trim($path);
    $path = str_replace(['..\\', '../'], '', $path);
    return ltrim($path, '/');
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_cat = (int)($_POST['product_cat'] ?? 0);
            $product_brand = (int)($_POST['product_brand'] ?? 0);
            $product_title = trim($_POST['product_title'] ?? '');
            $product_price = isset($_POST['product_price']) ? (float)$_POST['product_price'] : 0;
            $product_desc = trim($_POST['product_desc'] ?? '');
            $product_keywords = trim($_POST['product_keywords'] ?? '');
            $product_qty = (int)($_POST['product_qty'] ?? 0);
            // Accept both product_image_path (from form) and product_image (from JS)
            $product_image_path = sanitize_upload_path($_POST['product_image_path'] ?? $_POST['product_image'] ?? '');

            // For Tier 1 artisans: Get or create brand from business_name if not provided
            $artisan_tier = $_SESSION['artisan_tier'] ?? null;
            if ($artisan_tier == 1 && $product_brand <= 0) {
                $business_name = $_SESSION['business_name'] ?? '';
                if (!empty($business_name)) {
                    require_once '../controllers/brand_controller.php';
                    $user_id = (int)$_SESSION['user_id'];
                    $product_brand = get_or_create_brand_ctr($business_name, $user_id);
                    if (!$product_brand) {
                        if (ob_get_level() > 0) {
                            ob_clean();
                        }
                        echo json_encode(['success' => false, 'message' => 'Failed to create brand from business name']);
                        exit();
                    }
                } else {
                    if (ob_get_level() > 0) {
                        ob_clean();
                    }
                    echo json_encode(['success' => false, 'message' => 'Business name not found. Please update your profile.']);
                    exit();
                }
            }

            if (!$product_cat || !$product_brand || empty($product_title) || $product_price <= 0 || $product_qty < 0) {
                if (ob_get_level() > 0) {
                    ob_clean();
                }
                echo json_encode(['success' => false, 'message' => 'Please provide all required product details']);
                exit();
            }

            if (empty($product_image_path)) {
                if (ob_get_level() > 0) {
                    ob_clean();
                }
                echo json_encode(['success' => false, 'message' => 'Product image is required. Please upload an image first.']);
                exit();
            }

            // Use relative path (like old working code)
            $absoluteImagePath = '../' . $product_image_path;
            if (!file_exists($absoluteImagePath)) {
                if (ob_get_level() > 0) {
                    ob_clean();
                }
                echo json_encode(['success' => false, 'message' => 'Uploaded image not found. Please re-upload the image.']);
                exit();
            }

            try {
                // Get user_id (customer_id) for created_by field
                $user_id = (int)$_SESSION['user_id'];
                
                // First, add the product with temporary image path
                $product_id = $artisan->add_product(
                    $artisan_id,
                    $product_cat,
                    $product_brand,
                    $product_title,
                    $product_price,
                    $product_desc,
                    $product_image_path, // Temporary path
                    $product_keywords,
                    $product_qty,
                    $user_id // created_by
                );

                // Check if product was added successfully
                // Note: mysqli_insert_id can return 0 if no auto-increment column exists
                // But if the insert succeeded, we should have a product_id > 0
                if ($product_id && $product_id > 0) {
                    // Product created successfully, now rename temp folder to use product ID
                    // This matches the structure used by upload_product_image_action.php
                    $user_id = $_SESSION['user_id'];
                    $old_path = '../' . $product_image_path;
                    
                    // Check if this is a temp folder upload
                    if (strpos($product_image_path, 'temp_') !== false) {
                        // Extract temp folder name and filename
                        // Path format: uploads/u{user_id}/temp_{id}/{filename}
                        $path_parts = explode('/', $product_image_path);
                        $filename = end($path_parts); // Get the filename (e.g., "1.jpg")
                        
                        // Create new directory structure: uploads/u{user_id}/p{product_id}/
                        $new_dir = '../uploads/u' . $user_id . '/p' . $product_id . '/';
                        $new_path = $new_dir . $filename;
                        $new_db_path = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $filename;
                        
                        // Create product directory if it doesn't exist
                        if (!is_dir($new_dir)) {
                            if (!@mkdir($new_dir, 0755, true)) {
                                error_log("Failed to create product directory: $new_dir");
                            }
                        }
                        
                        // Move the file from temp folder to product folder
                        if (file_exists($old_path)) {
                            if (rename($old_path, $new_path)) {
                                // Update product with new image path
                                $db_conn = $artisan->db_conn();
                                $safe_path = mysqli_real_escape_string($db_conn, $new_db_path);
                                $update_sql = "UPDATE products SET product_image = '$safe_path' WHERE product_id = $product_id";
                                $artisan->db_query($update_sql);
                                
                                // Clean up temp directory if empty
                                $old_dir = dirname($old_path);
                                if (is_dir($old_dir) && count(glob($old_dir . '/*')) === 0) {
                                    @rmdir($old_dir);
                                }
                            } else {
                                error_log("Failed to move image from $old_path to $new_path");
                            }
                        }
                    } else {
                        // Image is already in the correct location (not a temp upload)
                        // Just ensure the path is correct in the database
                        $db_conn = $artisan->db_conn();
                        $safe_path = mysqli_real_escape_string($db_conn, $product_image_path);
                        $update_sql = "UPDATE products SET product_image = '$safe_path' WHERE product_id = $product_id";
                        $artisan->db_query($update_sql);
                    }
                    
                    // Clear output buffer before sending JSON response
                    if (ob_get_level() > 0) {
                        ob_clean();
                    }
                    
                    // Send JSON response
                    $response = json_encode([
                        'success' => true, 
                        'message' => 'Product added successfully',
                        'product_id' => $product_id
                    ]);
                    
                    // Ensure no output before JSON
                    if (ob_get_level() > 0) {
                        ob_clean();
                    }
                    
                    echo $response;
                    
                    // Flush output and exit immediately
                    if (ob_get_level() > 0) {
                        ob_end_flush();
                    }
                    exit();
                } else {
                    // Get database error if available
                    $db_conn = $artisan->db_conn();
                    $error_msg = $db_conn ? mysqli_error($db_conn) : 'Unknown database error';
                    
                    // Check if product was actually inserted (maybe insert_id returned 0)
                    // Verify by checking if a product with matching details exists
                    $verify_sql = "SELECT product_id FROM products WHERE artisan_id = $artisan_id AND product_title = '" . 
                                 mysqli_real_escape_string($db_conn, $product_title) . 
                                 "' AND product_price = $product_price ORDER BY product_id DESC LIMIT 1";
                    $verify_result = $artisan->db_fetch_one($verify_sql);
                    
                    if ($verify_result && !empty($verify_result['product_id'])) {
                        // Product was actually added, but insert_id returned 0
                        $product_id = (int)$verify_result['product_id'];
                        error_log("Product added but insert_id was 0. Found product_id: $product_id");
                        
                        // Continue with success path
                        // Move/rename image if needed (same logic as success case)
                        $user_id = $_SESSION['user_id'];
                        $old_path = '../' . $product_image_path;
                        
                        // Check if this is a temp folder upload
                        if (strpos($product_image_path, 'temp_') !== false) {
                            // Extract filename from path
                            $path_parts = explode('/', $product_image_path);
                            $filename = end($path_parts);
                            
                            // Create new directory structure: uploads/u{user_id}/p{product_id}/
                            $new_dir = '../uploads/u' . $user_id . '/p' . $product_id . '/';
                            $new_path = $new_dir . $filename;
                            $new_db_path = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $filename;
                            
                            if (!is_dir($new_dir)) {
                                @mkdir($new_dir, 0755, true);
                            }
                            
                            if (file_exists($old_path)) {
                                if (rename($old_path, $new_path)) {
                                    $safe_path = mysqli_real_escape_string($db_conn, $new_db_path);
                                    $update_sql = "UPDATE products SET product_image = '$safe_path' WHERE product_id = $product_id";
                                    $artisan->db_query($update_sql);
                                    
                                    $old_dir = dirname($old_path);
                                    if (is_dir($old_dir) && count(glob($old_dir . '/*')) === 0) {
                                        @rmdir($old_dir);
                                    }
                                }
                            }
                        } else {
                            // Image is already in the correct location
                            $safe_path = mysqli_real_escape_string($db_conn, $product_image_path);
                            $update_sql = "UPDATE products SET product_image = '$safe_path' WHERE product_id = $product_id";
                            $artisan->db_query($update_sql);
                        }
                        
                        // Clear output buffer before sending JSON response
                        if (ob_get_level() > 0) {
                            ob_clean();
                        }
                        
                        $response = json_encode([
                            'success' => true, 
                            'message' => 'Product added successfully',
                            'product_id' => $product_id
                        ]);
                        
                        if (ob_get_level() > 0) {
                            ob_clean();
                        }
                        
                        echo $response;
                        if (ob_get_level() > 0) {
                            ob_end_flush();
                        }
                        exit();
                    } else {
                        // Product was not added
                    error_log("Failed to add product. Database error: " . $error_msg);
                        
                        // Clear output buffer before sending JSON response
                        if (ob_get_level() > 0) {
                            ob_clean();
                        }
                        
                        $response = json_encode([
                        'success' => false, 
                        'message' => 'Failed to add product. Please check all fields and try again.'
                    ]);
                        
                        if (ob_get_level() > 0) {
                            ob_clean();
                        }
                        
                        echo $response;
                        if (ob_get_level() > 0) {
                            ob_end_flush();
                        }
                        exit();
                    }
                }
            } catch (Exception $e) {
                if (ob_get_level() > 0) {
                    ob_clean();
                }
                error_log("Exception in artisan_product_action.php (add): " . $e->getMessage());
                echo json_encode([
                    'success' => false, 
                    'message' => 'An error occurred while adding the product: ' . $e->getMessage()
                ]);
                exit(); // Exit immediately after sending JSON
            }
        } else {
            if (ob_get_level() > 0) {
                ob_clean();
            }
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = (int)($_POST['product_id'] ?? 0);

            if (!$artisan->verify_product_ownership($product_id, $artisan_id)) {
                if (ob_get_level() > 0) {
                    ob_clean();
                }
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }

            $product_cat = (int)($_POST['product_cat'] ?? 0);
            $product_brand = (int)($_POST['product_brand'] ?? 0);
            $product_title = trim($_POST['product_title'] ?? '');
            $product_price = isset($_POST['product_price']) ? (float)$_POST['product_price'] : 0;
            $product_desc = trim($_POST['product_desc'] ?? '');
            $product_keywords = trim($_POST['product_keywords'] ?? '');
            $product_qty = (int)($_POST['product_qty'] ?? 0);
            // Accept both product_image_path (from form) and product_image (from JS)
            $product_image_path = sanitize_upload_path($_POST['product_image_path'] ?? $_POST['product_image'] ?? '');

            $result = $artisan->update_product(
                $product_id,
                $artisan_id,
                $product_cat,
                $product_brand,
                $product_title,
                $product_price,
                $product_desc,
                $product_keywords,
                $product_qty
            );

            if ($result && !empty($product_image_path)) {
                // Use relative path (like old working code)
                $absoluteImagePath = '../' . $product_image_path;
                if (file_exists($absoluteImagePath)) {
                    $safe_path = mysqli_real_escape_string($artisan->db_conn(), $product_image_path);
                    $update_sql = "UPDATE products SET product_image = '$safe_path' WHERE product_id = '$product_id' AND artisan_id = '$artisan_id'";
                    $artisan->db_query($update_sql);
                }
            }

            if (ob_get_level() > 0) {
                ob_clean();
            }
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update product']);
            }
            exit();
        } else {
            if (ob_get_level() > 0) {
                ob_clean();
            }
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }
        break;

    case 'delete':
        $product_id = (int)($_POST['product_id'] ?? 0);

        if (!$artisan->verify_product_ownership($product_id, $artisan_id)) {
            if (ob_get_level() > 0) {
                ob_clean();
            }
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $result = $artisan->delete_product($product_id, $artisan_id);

        if (ob_get_level() > 0) {
            ob_clean();
        }
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
        }
        exit();
        break;

    default:
        if (ob_get_level() > 0) {
            ob_clean();
        }
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit();
}

// This should never be reached, but just in case
if (ob_get_level() > 0) {
    ob_end_clean();
}
// No closing PHP tag to prevent any whitespace output