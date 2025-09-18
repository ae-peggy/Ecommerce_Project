<?php
header('Content-Type: application/json');
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log all POST data for debugging
error_log("POST data received: " . print_r($_POST, true));

// Prevent registration if already logged in
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
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$country = $_POST['country'] ?? '';
$city = $_POST['city'] ?? '';
$phone_number = $_POST['phone_number'] ?? '';
$role = $_POST['role'] ?? 2; // Default role = customer

// Log collected data
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

// Step 2: Basic validation
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

$user_id = register_user_ctr($name, $email, $password, $country, $city, $phone_number, $role);

if ($user_id && !str_starts_with($user_id, "ERROR")) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Registration successful! Redirecting to login...',
        'redirect' => '../login/login.php'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => is_string($user_id) ? $user_id : 'Failed to register user. Please try again.'
    ]);
}
?>
