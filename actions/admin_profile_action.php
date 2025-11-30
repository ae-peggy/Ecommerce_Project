<?php
/**
 * Admin Profile Action
 * Handles admin profile updates and password changes
 */

header('Content-Type: application/json');

require_once '../settings/core.php';
require_admin('../login/login.php');

require_once '../controllers/customer_controller.php';
require_once '../classes/customer_class.php';

$admin_id = get_user_id();
$action = $_POST['action'] ?? '';

$customer = new customer_class();

switch ($action) {
    case 'update_personal':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customer_name = trim($_POST['customer_name'] ?? '');
            $customer_email = trim($_POST['customer_email'] ?? '');
            $customer_contact = trim($_POST['customer_contact'] ?? '');
            $customer_country = trim($_POST['customer_country'] ?? '');
            $customer_city = trim($_POST['customer_city'] ?? '');
            
            if (empty($customer_name) || empty($customer_email) || empty($customer_contact)) {
                echo json_encode(['success' => false, 'message' => 'Name, email, and contact are required']);
                exit();
            }
            
            // Check if email is already taken by another user
            $existing_user = get_user_by_email_ctr($customer_email);
            if ($existing_user && $existing_user['customer_id'] != $admin_id) {
                echo json_encode(['success' => false, 'message' => 'Email is already taken by another user']);
                exit();
            }
            
            $result = update_user_ctr($admin_id, $customer_name, $customer_email, $customer_country, $customer_city, $customer_contact);
            
            if ($result) {
                // Update session
                $_SESSION['customer_name'] = $customer_name;
                $_SESSION['customer_email'] = $customer_email;
                echo json_encode(['success' => true, 'message' => 'Personal information updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update personal information']);
            }
        }
        break;
        
    case 'change_password':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            
            if (empty($current_password) || empty($new_password)) {
                echo json_encode(['success' => false, 'message' => 'Current password and new password are required']);
                exit();
            }
            
            if (strlen($new_password) < 6) {
                echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters long']);
                exit();
            }
            
            // Get current password hash
            $admin_data = get_user_by_id_ctr($admin_id);
            
            if (!$admin_data) {
                echo json_encode(['success' => false, 'message' => 'User not found']);
                exit();
            }
            
            // Verify current password
            if (!password_verify($current_password, $admin_data['customer_pass'])) {
                echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
                exit();
            }
            
            // Hash new password
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password
            $result = $customer->update_password($admin_id, $new_password_hash);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to change password']);
            }
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>

