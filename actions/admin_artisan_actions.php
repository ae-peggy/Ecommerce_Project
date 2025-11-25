<?php
header('Content-Type: application/json');

// Include core session management (handles session_start)
require_once '../settings/core.php';
require_once '../classes/artisan_class.php';
require_once '../classes/customer_class.php';

// Check if user is admin
if (!is_admin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$artisan = new artisan_class();
$customer = new customer_class();

// Handle different actions
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        // Add new artisan
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customer_name = trim($_POST['customer_name'] ?? '');
            $customer_email_raw = trim($_POST['customer_email'] ?? '');
            $customer_email = filter_var($customer_email_raw, FILTER_VALIDATE_EMAIL);
            $customer_contact = trim($_POST['customer_contact'] ?? '');
            $business_name = trim($_POST['business_name'] ?? '');
            $tier = (int)($_POST['tier'] ?? 1);
            $commission_rate = isset($_POST['commission_rate']) ? (float)$_POST['commission_rate'] : 15.0;

            if (!$customer_email) {
                echo json_encode(['success' => false, 'message' => 'Invalid email address']);
                break;
            }
            
            // Generate secure temporary password (12 characters: uppercase, lowercase, numbers, special chars)
            $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ'; // Exclude I, O for clarity
            $lowercase = 'abcdefghijkmnpqrstuvwxyz'; // Exclude l, o for clarity
            $numbers = '23456789'; // Exclude 0, 1 for clarity
            $special = '!@#$%&*';
            
            $temp_password = '';
            $temp_password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
            $temp_password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
            $temp_password .= $numbers[random_int(0, strlen($numbers) - 1)];
            $temp_password .= $special[random_int(0, strlen($special) - 1)];
            
            // Fill remaining 8 characters with random from all character sets
            $all_chars = $uppercase . $lowercase . $numbers . $special;
            for ($i = 0; $i < 8; $i++) {
                $temp_password .= $all_chars[random_int(0, strlen($all_chars) - 1)];
            }
            
            // Shuffle the password to randomize character positions
            $temp_password = str_shuffle($temp_password);
            $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
            
            // Create customer account first
            $customer_id = $customer->add_customer(
                $customer_name,
                $customer_email,
                $hashed_password,
                '',
                '', // country and city not provided in admin form
                $customer_contact,
                2
            );
            
            if ($customer_id) {
                
                // Create artisan record
                $result = $artisan->create_artisan($customer_id, $business_name, $tier, $commission_rate);
                
                if ($result) {
                    // Return credentials for display to admin
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Artisan added successfully',
                        'credentials' => [
                            'email' => $customer_email,
                            'password' => $temp_password,
                            'name' => $customer_name
                        ]
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to create artisan record']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create customer account']);
            }
        }
        break;
        
    case 'approve':
        // Approve artisan
        $artisan_id = (int)($_POST['artisan_id'] ?? 0);
        $result = $artisan->update_approval_status($artisan_id, 'approved');
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Artisan approved']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to approve artisan']);
        }
        break;
        
    case 'bulk_approve':
        // Bulk approve multiple artisans
        $artisan_ids_str = $_POST['artisan_ids'] ?? '';
        if (empty($artisan_ids_str)) {
            echo json_encode(['success' => false, 'message' => 'No artisan IDs provided']);
            break;
        }
        
        $artisan_ids = array_map('intval', explode(',', $artisan_ids_str));
        $artisan_ids = array_filter($artisan_ids, function($id) { return $id > 0; });
        
        if (empty($artisan_ids)) {
            echo json_encode(['success' => false, 'message' => 'Invalid artisan IDs']);
            break;
        }
        
        $approved_count = 0;
        $failed_count = 0;
        
        foreach ($artisan_ids as $artisan_id) {
            $result = $artisan->update_approval_status($artisan_id, 'approved');
            if ($result) {
                $approved_count++;
            } else {
                $failed_count++;
            }
        }
        
        if ($approved_count > 0) {
            echo json_encode([
                'success' => true, 
                'message' => "Successfully approved $approved_count artisan(s)" . ($failed_count > 0 ? ". $failed_count failed." : ''),
                'approved_count' => $approved_count,
                'failed_count' => $failed_count
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to approve any artisans']);
        }
        break;
        
    case 'suspend':
        // Suspend artisan
        $artisan_id = (int)($_POST['artisan_id'] ?? 0);
        $result = $artisan->update_approval_status($artisan_id, 'suspended');
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Artisan suspended']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to suspend artisan']);
        }
        break;
        
    case 'delete':
        // Delete artisan
        $artisan_id = (int)($_POST['artisan_id'] ?? 0);
        
        // Get customer_id before deleting
        $artisan_data = $artisan->get_artisan_by_id($artisan_id);
        
        if ($artisan_data) {
            // Delete artisan record (this will also delete their products)
            $result = $artisan->delete_artisan($artisan_id);
            
            if ($result) {
                // Optionally delete customer account too
                $customer_id = (int)$artisan_data['customer_id'];
                $customer->delete_customer($customer_id);
                
                echo json_encode(['success' => true, 'message' => 'Artisan deleted']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete artisan']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Artisan not found']);
        }
        break;
        
    case 'add_for_artisan':
        // Admin uploads product for artisan
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $artisan_id = (int)($_POST['artisan_id'] ?? 0);
            $product_cat = (int)($_POST['product_cat'] ?? 0);
            $product_brand = (int)($_POST['product_brand'] ?? 0);
            $product_title = trim($_POST['product_title'] ?? '');
            $product_price = isset($_POST['product_price']) ? (float)$_POST['product_price'] : 0;
            $product_desc = trim($_POST['product_desc'] ?? '');
            $product_keywords = trim($_POST['product_keywords'] ?? '');
            $product_qty = (int)($_POST['product_qty'] ?? 0);
            
            // Handle image upload
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
                $target_dir = "../images/products/";
                $file_extension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
                $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
                $target_file = $target_dir . $new_filename;
                
                // Check file type
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array($file_extension, $allowed_types)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid file type']);
                    exit();
                }
                
                // Check file size (5MB max)
                if ($_FILES['product_image']['size'] > 5242880) {
                    echo json_encode(['success' => false, 'message' => 'File size too large']);
                    exit();
                }
                
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                    // Get admin user_id for created_by field
                    $admin_user_id = (int)$_SESSION['user_id'];
                    
                    $result = $artisan->add_product(
                        $artisan_id,
                        $product_cat,
                        $product_brand,
                        $product_title,
                        $product_price,
                        $product_desc,
                        $new_filename,
                        $product_keywords,
                        $product_qty,
                        $admin_user_id // created_by
                    );
                    
                    if ($result) {
                        echo json_encode(['success' => true, 'message' => 'Product added for artisan']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to add product']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No image uploaded']);
            }
        }
        break;
        
    case 'get_recent_uploads':
        // Get recently uploaded products by admin for artisans
        $sql = "SELECT 
                    p.product_title,
                    p.product_price,
                    p.product_qty,
                    p.created_date,
                    a.business_name
                FROM products p
                JOIN artisans a ON p.artisan_id = a.artisan_id
                ORDER BY p.created_date DESC
                LIMIT 10";
        $recent = $artisan->db_fetch_all($sql) ?? [];
        echo json_encode(['success' => true, 'data' => $recent]);
        break;
        
    case 'get':
        // Get artisan details for view/edit
        $artisan_id = (int)($_POST['artisan_id'] ?? $_GET['artisan_id'] ?? 0);
        if ($artisan_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid artisan ID']);
            break;
        }
        
        $artisan_data = $artisan->get_artisan_by_id($artisan_id);
        if ($artisan_data) {
            echo json_encode(['success' => true, 'data' => $artisan_data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Artisan not found']);
        }
        break;
        
    case 'update':
        // Update artisan details
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $artisan_id = (int)($_POST['artisan_id'] ?? 0);
            $customer_name = trim($_POST['customer_name'] ?? '');
            $customer_email_raw = trim($_POST['customer_email'] ?? '');
            $customer_email = filter_var($customer_email_raw, FILTER_VALIDATE_EMAIL);
            $customer_contact = trim($_POST['customer_contact'] ?? '');
            $business_name = trim($_POST['business_name'] ?? '');
            $tier = (int)($_POST['tier'] ?? 1);
            $commission_rate = isset($_POST['commission_rate']) ? (float)$_POST['commission_rate'] : 20.0;
            $approval_status = trim($_POST['approval_status'] ?? '');

            if ($artisan_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid artisan ID']);
                break;
            }
            
            if (!$customer_email) {
                echo json_encode(['success' => false, 'message' => 'Invalid email address']);
                break;
            }
            
            // Get artisan data to get customer_id
            $artisan_data = $artisan->get_artisan_by_id($artisan_id);
            if (!$artisan_data) {
                echo json_encode(['success' => false, 'message' => 'Artisan not found']);
                break;
            }
            
            $customer_id = (int)$artisan_data['customer_id'];
            
            // Update customer details (using edit_customer which accepts name, email, country, city, contact)
            $customer_updated = $customer->edit_customer(
                $customer_id,
                $customer_name,
                $customer_email,
                $artisan_data['customer_country'] ?? '',
                $artisan_data['customer_city'] ?? '',
                $customer_contact
            );
            
            if (!$customer_updated) {
                echo json_encode(['success' => false, 'message' => 'Failed to update customer details']);
                break;
            }
            
            // Update artisan details using artisan_class method
            $approval_status_param = !empty($approval_status) ? $approval_status : null;
            $result = $artisan->update_artisan_details($artisan_id, $business_name, $tier, $commission_rate, $approval_status_param);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Artisan updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update artisan']);
            }
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>