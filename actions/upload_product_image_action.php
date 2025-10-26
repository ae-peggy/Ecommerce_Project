<?php
header('Content-Type: application/json');

// Include core session management functions
require_once '../settings/core.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to upload images'
    ]);
    exit();
}

// Check if user is admin
if (!is_admin()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be an admin to upload product images'
    ]);
    exit();
}

// Check if file was uploaded
if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] === UPLOAD_ERR_NO_FILE) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No image file uploaded'
    ]);
    exit();
}

$file = $_FILES['product_image'];
$user_id = get_user_id();
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

// Validate file upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    $upload_errors = [
        UPLOAD_ERR_INI_SIZE => "File is too large (exceeds server limit)",
        UPLOAD_ERR_FORM_SIZE => "File is too large (exceeds form limit)",
        UPLOAD_ERR_PARTIAL => "File was only partially uploaded",
        UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder",
        UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
        UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload"
    ];
    
    echo json_encode([
        'status' => 'error',
        'message' => $upload_errors[$file['error']] ?? 'Unknown upload error'
    ]);
    exit();
}

// Validate file size (max 5MB for images)
$max_size = 5 * 1024 * 1024; // 5MB
if ($file['size'] > $max_size) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Image is too large. Maximum size is 5MB'
    ]);
    exit();
}

// Validate file type (images only)
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_types)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed'
    ]);
    exit();
}

// Get file extension
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($file_extension, $allowed_extensions)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid file extension'
    ]);
    exit();
}

// Create directory structure: uploads/u{user_id}/p{product_id}/
$base_upload_dir = '../uploads/';
$user_dir = $base_upload_dir . 'u' . $user_id . '/';

// For new products (no product_id yet), use temporary folder
if ($product_id > 0) {
    $product_dir = $user_dir . 'p' . $product_id . '/';
} else {
    // Temporary storage for new products
    $product_dir = $user_dir . 'temp_' . time() . '/';
}

// Create directories if they don't exist
if (!is_dir($base_upload_dir)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Upload directory does not exist. Please contact administrator.'
    ]);
    exit();
}

if (!is_dir($user_dir)) {
    if (!mkdir($user_dir, 0755, true)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create user directory'
        ]);
        exit();
    }
}

if (!is_dir($product_dir)) {
    if (!mkdir($product_dir, 0755, true)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create product directory'
        ]);
        exit();
    }
}

// Generate unique filename
$unique_filename = uniqid('img_', true) . '.' . $file_extension;
$target_file = $product_dir . $unique_filename;

// Verify the target path is within uploads directory (security check)
$real_base = realpath($base_upload_dir);
$real_target = realpath(dirname($target_file));

if ($real_target === false || strpos($real_target, $real_base) !== 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid upload path'
    ]);
    exit();
}

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $target_file)) {
    // Set proper file permissions
    chmod($target_file, 0644);
    
    // Return relative path for database storage
    $db_path = str_replace('../', '', $target_file);
    
    error_log("Image uploaded successfully: $db_path");
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Image uploaded successfully',
        'file_path' => $db_path,
        'file_name' => $unique_filename,
        'file_size' => $file['size']
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to move uploaded file'
    ]);
}
?>