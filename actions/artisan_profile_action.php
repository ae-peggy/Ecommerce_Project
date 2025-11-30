<?php
/**
 * Artisan Profile Action
 * Handles artisan profile updates and password changes
 */

header('Content-Type: application/json');
require_once '../settings/core.php';
require_once '../classes/artisan_class.php';
require_once '../classes/customer_class.php';

// Check if user is artisan
if (!is_artisan() && (!isset($_SESSION['artisan_id']) || !isset($_SESSION['user_id']))) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$artisan = new artisan_class();
$customer = new customer_class();
$artisan_id = $_SESSION['artisan_id'];
// Use user_id as customer_id for artisans (user_id is the customer_id)
$customer_id = $_SESSION['user_id'] ?? $_SESSION['customer_id'] ?? null;

// Handle different actions
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_business':
        // Update business information
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $business_name = trim($_POST['business_name'] ?? '');
            $business_desc = trim($_POST['business_desc'] ?? '');
            $business_phone = trim($_POST['business_phone'] ?? '');
            $business_address = trim($_POST['business_address'] ?? '');
            
            $result = $artisan->update_artisan_business(
                $artisan_id,
                $business_name,
                $business_desc,
                $business_phone,
                $business_address
            );
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Business information updated']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update business information']);
            }
        }
        break;
        
    case 'update_personal':
        // Update personal information
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customer_name = trim($_POST['customer_name'] ?? '');
            $customer_email = trim($_POST['customer_email'] ?? '');
            $customer_contact = trim($_POST['customer_contact'] ?? '');
            $customer_country = trim($_POST['customer_country'] ?? '');
            $customer_city = trim($_POST['customer_city'] ?? '');
            
            // Ensure customer_id is set
            if (!$customer_id) {
                echo json_encode(['success' => false, 'message' => 'User session not found. Please log in again.']);
                exit();
            }
            
            // Escape input for security
            $customer_id_int = (int)$customer_id;
            $safe_name = mysqli_real_escape_string($customer->db_conn(), $customer_name);
            $safe_email = mysqli_real_escape_string($customer->db_conn(), $customer_email);
            $safe_contact = mysqli_real_escape_string($customer->db_conn(), $customer_contact);
            $safe_country = mysqli_real_escape_string($customer->db_conn(), $customer_country);
            $safe_city = mysqli_real_escape_string($customer->db_conn(), $customer_city);
            
            $sql = "UPDATE customer SET 
                    customer_name = '$safe_name',
                    customer_email = '$safe_email',
                    customer_contact = '$safe_contact',
                    customer_country = '$safe_country',
                    customer_city = '$safe_city'
                    WHERE customer_id = $customer_id_int";
            
            $result = $customer->db_query($sql);
            
            if ($result) {
                // Update session
                $_SESSION['customer_name'] = $customer_name;
                echo json_encode(['success' => true, 'message' => 'Personal information updated']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update personal information']);
            }
        }
        break;
        
    case 'change_password':
        // Change password
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            
            // Ensure customer_id is set
            if (!$customer_id) {
                echo json_encode(['success' => false, 'message' => 'User session not found. Please log in again.']);
                exit();
            }
            
            // Get current password hash - use prepared statement for security
            $customer_id_int = (int)$customer_id;
            $sql = "SELECT customer_pass FROM customer WHERE customer_id = $customer_id_int";
            $user_data = $customer->db_fetch_one($sql);
            
            if ($user_data) {
                // Verify current password
                if (password_verify($current_password, $user_data['customer_pass'])) {
                    // Hash new password
                    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $safe_hash = mysqli_real_escape_string($customer->db_conn(), $new_password_hash);
                    
                    $update_sql = "UPDATE customer SET customer_pass = '$safe_hash' WHERE customer_id = $customer_id_int";
                    $result = $customer->db_query($update_sql);
                    
                    if ($result) {
                        echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to change password']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found']);
            }
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>