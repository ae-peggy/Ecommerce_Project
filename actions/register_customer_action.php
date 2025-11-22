<?php
header('Content-Type: application/json');

// Include core session management functions
require_once '../settings/core.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log all POST data for debugging
error_log("POST data received: " . print_r($_POST, true));

// Prevent registration if already logged in
if (is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You are already logged in'
    ]);
    exit();
}

// Include the customer controller
require_once '../controllers/customer_controller.php';

// Collect form data safely
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$country = $_POST['country'] ?? '';
$city = $_POST['city'] ?? '';
$phone_number = $_POST['phone_number'] ?? '';
$register_as_artisan = isset($_POST['register_as_artisan']) && $_POST['register_as_artisan'] == '1';
$artisan_tier = isset($_POST['artisan_tier']) ? (int)$_POST['artisan_tier'] : 0;
$role = $register_as_artisan ? 2 : 2; // Role 2 = Artisan (both regular customers and artisans use role 2, tier determines services)

// Log collected data (don't log password for security)
error_log("Collected data - Name: $name, Email: $email, Country: $country, City: $city, Phone: $phone_number");

// Step 1: Check required fields
if (empty($name) || empty($email) || empty($password) || empty($country) || empty($city) || empty($phone_number)) {
    error_log("Missing required fields");
    echo json_encode([
        'status' => 'error',
        'message' => 'All fields are required'
    ]);
    exit();
}

// Validate artisan tier if registering as artisan
if ($register_as_artisan) {
    if ($artisan_tier !== 1 && $artisan_tier !== 2) {
        error_log("Invalid artisan tier: $artisan_tier (register_as_artisan: " . ($register_as_artisan ? 'true' : 'false') . ")");
        echo json_encode([
            'status' => 'error',
            'message' => 'Please select a valid tier (Tier 1 or Tier 2)'
        ]);
        exit();
    }
}

// Step 2: Basic validation
// Validate name format (letters, spaces, hyphens, and apostrophes only)
if (!preg_match("/^[a-zA-Z\s'\-]+$/u", $name)) {
    error_log("Invalid name format: $name");
    echo json_encode([
        'status' => 'error',
        'message' => 'Name can only contain letters, spaces, hyphens, and apostrophes'
    ]);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_log("Invalid email format: $email");
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email format'
    ]);
    exit();
}

// Validate password length (minimum 6 characters)
if (strlen($password) < 6) {
    error_log("Password too short");
    echo json_encode([
        'status' => 'error',
        'message' => 'Password must be at least 6 characters long'
    ]);
    exit();
}

// Step 3: Check if email is unique
error_log("Checking if email exists: $email");
try {
    $existingUser = get_user_by_email_ctr($email);
    error_log("Email check result: " . ($existingUser ? "exists" : "doesn't exist"));
    
    if ($existingUser) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Email already registered'
        ]);
        exit();
    }
} catch (Exception $e) {
    error_log("Error checking email: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error during email check'
    ]);
    exit();
}

// Step 4: Register user
error_log("Attempting to register user");
try {
    $user_id = register_user_ctr($name, $email, $password, $country, $city, $phone_number, $role);
    error_log("Registration result - User ID: " . ($user_id ? $user_id : "false"));
    
    // ADD THIS DEBUG CODE:
    if (!$user_id) {
        error_log("Registration failed. Checking database connection...");
        // Test database connection
        require_once '../classes/customer_class.php';
        $customer = new customer_class();
        error_log("Database connection test successful");
    }
    
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
            $business_name = $name . "'s Crafts";
            
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
    error_log("Error during registration: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error during registration: ' . $e->getMessage()
    ]);
}
?>