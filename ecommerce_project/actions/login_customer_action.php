<?php
header('Content-Type: application/json');
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log all POST data for debugging
error_log("Login POST data received: " . print_r($_POST, true));

// Prevent login if already logged in
if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You are already logged in'
    ]);
    exit();
}

// Include the customer controller
require_once '../controllers/customer_controller.php';

// Collect form data safely
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Log collected data (don't log password for security)
error_log("Login attempt - Email: $email");

// Step 1: Check required fields
if (empty($email) || empty($password)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email and password are required'
    ]);
    exit();
}

// Step 2: Basic email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_log("Invalid email format: $email");
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email format'
    ]);
    exit();
}

// Step 3: Attempt login
error_log("Attempting login for: $email");
try {
    $customer_data = login_customer_ctr($email, $password);
    
    if ($customer_data) {
        // Login successful - Set session variables
        $_SESSION['user_id'] = $customer_data['customer_id'];
        $_SESSION['user_name'] = $customer_data['customer_name'];
        $_SESSION['user_email'] = $customer_data['customer_email'];
        $_SESSION['user_role'] = $customer_data['user_role'];
        $_SESSION['user_country'] = $customer_data['customer_country'];
        $_SESSION['user_city'] = $customer_data['customer_city'];
        
        error_log("Login successful for user ID: " . $customer_data['customer_id']);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful! Redirecting...',
            'redirect' => '../index.php',
            'user_name' => $customer_data['customer_name']
        ]);
        
    } else {
        // Login failed
        error_log("Login failed for: $email");
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid email or password'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error during login: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Login error. Please try again.'
    ]);
}
?>