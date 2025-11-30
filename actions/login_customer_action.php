<?php
/**
 * Login Customer Action
 * Handles user authentication and session management
 */

header('Content-Type: application/json');
require_once '../settings/core.php';

// Prevent login if already logged in
if (is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You are already logged in'
    ]);
    exit();
}

require_once '../controllers/customer_controller.php';

// Collect form data
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Validate required fields
if (empty($email) || empty($password)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email and password are required'
    ]);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email format'
    ]);
    exit();
}

// Attempt login
try {
    $customer_data = login_customer_ctr($email, $password);
    
    if ($customer_data) {
        // Regenerate session ID on login to prevent session fixation attacks
        session_regenerate_id(true);
        
        // Login successful - Set session variables
        $_SESSION['user_id'] = $customer_data['customer_id'];
        $_SESSION['user_name'] = $customer_data['customer_name'];
        $_SESSION['user_email'] = $customer_data['customer_email'];
        $_SESSION['user_role'] = $customer_data['user_role'] ?? 0;
        $_SESSION['user_country'] = $customer_data['customer_country'];
        $_SESSION['user_city'] = $customer_data['customer_city'];
        $_SESSION['last_activity'] = time();
        $_SESSION['created'] = time();
        $_SESSION['login_time'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        // Check if user is an artisan
        require_once '../classes/artisan_class.php';
        $artisan = new artisan_class();

        $artisan_check = $artisan->get_artisan_by_customer($customer_data['customer_id']);

        if ($artisan_check) {
            // Approved Artisan
            if ($artisan_check['approval_status'] === 'approved') {
                $_SESSION['artisan_id'] = $artisan_check['artisan_id'];
                $_SESSION['artisan_tier'] = $artisan_check['tier'];
                $_SESSION['business_name'] = $artisan_check['business_name'];
                $_SESSION['user_role'] = 2;
                $redirect_url = '../artisan/dashboard.php';
                $message = 'Welcome Artisan! Redirecting to your dashboard...';
            } elseif ($artisan_check['approval_status'] === 'pending') {
                // Pending artisan
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Your artisan account is pending approval.'
                ]);
                exit();
            } elseif ($artisan_check['approval_status'] === 'suspended') {
                // Suspended artisan
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Your artisan account has been suspended.'
                ]);
                exit();
            }
        }

        // Determine redirect based on user role
        if (!isset($redirect_url)) {
            $user_role = $customer_data['user_role'] ?? 0;

            if ($user_role == 1) {
                // Admin user
                $redirect_url = '../admin/category.php';
                $message = 'Welcome Admin! Redirecting to dashboard...';
            } else {
                // Regular customer
                $redirect_url = '../index.php';
                $message = 'Login successful! Redirecting...';
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'redirect' => $redirect_url,
            'user_name' => $customer_data['customer_name'],
            'user_role' => $_SESSION['user_role']
        ]);
    
    } else {
        // Login failed
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid email or password'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Login error. Please try again.'
    ]);
}
?>