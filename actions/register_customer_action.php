<?php
/**
 * Register Customer Action
 * Handles new user registration including artisan accounts
 */

header('Content-Type: application/json');
require_once '../settings/core.php';

// Prevent registration if already logged in
if (is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You are already logged in'
    ]);
    exit();
}

require_once '../controllers/customer_controller.php';

// Collect form data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$country = $_POST['country'] ?? '';
$city = $_POST['city'] ?? '';
$phone_number = $_POST['phone_number'] ?? '';
$register_as_artisan = isset($_POST['register_as_artisan']) && $_POST['register_as_artisan'] == '1';
$artisan_tier = isset($_POST['artisan_tier']) ? (int)$_POST['artisan_tier'] : 0;
$role = $register_as_artisan ? 2 : 2;

// Validate required fields
if (empty($name) || empty($email) || empty($password) || empty($country) || empty($city) || empty($phone_number)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'All fields are required'
    ]);
    exit();
}

// Validate artisan tier if registering as artisan
if ($register_as_artisan) {
    if ($artisan_tier !== 1 && $artisan_tier !== 2) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Please select a valid tier (Tier 1 or Tier 2)'
        ]);
        exit();
    }
}

// Validate name format
if (!preg_match("/^[a-zA-Z\s'\-]+$/u", $name)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Name can only contain letters, spaces, hyphens, and apostrophes'
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

// Validate password length
if (strlen($password) < 6) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Password must be at least 6 characters long'
    ]);
    exit();
}

// Check if email is unique
try {
    $existingUser = get_user_by_email_ctr($email);
    
    if ($existingUser) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Email already registered'
        ]);
        exit();
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error during email check'
    ]);
    exit();
}

// Register user
try {
    $user_id = register_user_ctr($name, $email, $password, $country, $city, $phone_number, $role);
    
    if ($user_id) {
        // If registering as artisan, create artisan record
        if ($register_as_artisan && $artisan_tier > 0) {
            require_once '../classes/artisan_class.php';
            $artisan = new artisan_class();
            
            // Determine approval status based on tier
            // Tier 1: Auto-approved, Tier 2: Pending admin approval
            $approval_status = ($artisan_tier == 1) ? 'approved' : 'pending';
            $commission_rate = ($artisan_tier == 1) ? 20.0 : 30.0;
            
            // Get business name (use customer name as default)
            $business_name = $name;
            
            // Create artisan record (always creates as 'approved', we'll update if needed)
            $artisan_result = $artisan->create_artisan($user_id, $business_name, $artisan_tier, $commission_rate);
            
            if ($artisan_result) {
                // Get artisan ID and update approval status if needed
                $artisan_data = $artisan->get_artisan_by_customer($user_id);
                if ($artisan_data && $approval_status == 'pending') {
                    $artisan->update_approval_status($artisan_data['artisan_id'], 'pending');
                }
                
                $message = 'Registration successful! ';
                if ($approval_status == 'approved') {
                    $message .= 'Your artisan account has been auto-approved. You can now start adding products!';
                } else {
                    $message .= 'Your artisan account is pending admin approval. You will be notified once approved.';
                }
                
                echo json_encode([
                    'status' => 'success',
                    'message' => $message,
                    'redirect' => '../login/login.php'
                ]);
            } else {
                // Customer created but artisan record failed
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Account created but failed to register as artisan. Please contact support.'
                ]);
            }
        } else {
            // Regular customer registration
            echo json_encode([
                'status' => 'success',
                'message' => 'Registration successful! Redirecting to login...',
                'redirect' => '../login/login.php'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to register user. Please try again.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error during registration: ' . $e->getMessage()
    ]);
}
?>