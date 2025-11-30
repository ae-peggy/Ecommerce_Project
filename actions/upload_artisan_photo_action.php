<?php
/**
 * Upload Artisan Photo Action
 * Handles photo uploads for artisan about pages
 */

header('Content-Type: application/json');
require_once '../settings/core.php';

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in to upload photos'
    ]);
    exit();
}

// Check if user is artisan
if (!is_artisan()) {
    echo json_encode([
        'success' => false,
        'message' => 'You do not have permission to upload artisan photos'
    ]);
    exit();
}

$artisan_id = get_artisan_id();
if (!$artisan_id) {
    echo json_encode(['success' => false, 'message' => 'Artisan not found']);
    exit();
}

// Check if file was uploaded
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
    echo json_encode([
        'success' => false,
        'message' => 'No photo file uploaded'
    ]);
    exit();
}

$file = $_FILES['photo'];
$user_id = get_user_id();

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
        'success' => false,
        'message' => $upload_errors[$file['error']] ?? 'Unknown upload error'
    ]);
    exit();
}

// Validate file size (max 5MB)
$max_size = 5 * 1024 * 1024;
if ($file['size'] > $max_size) {
    echo json_encode([
        'success' => false,
        'message' => 'Image is too large. Maximum size is 5MB'
    ]);
    exit();
}

// Validate file type
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_types)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed'
    ]);
    exit();
}

// Get file extension
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($file_extension, $allowed_extensions)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid file extension'
    ]);
    exit();
}

// Base upload folder - use relative path (like upload_product_image_action.php)
$base_upload_dir = '../uploads/';

// Create base directories if they don't exist
if (!is_dir($base_upload_dir)) {
    if (!@mkdir($base_upload_dir, 0755, true)) {
        echo json_encode([
            'success' => false,
            'message' => 'Upload directory does not exist and could not be created. Please contact administrator.'
        ]);
        exit();
    }
}

// Check if directory is writable
if (!is_writable($base_upload_dir)) {
    echo json_encode([
        'success' => false,
        'message' => 'Upload directory is not writable. Please contact administrator to fix permissions.'
    ]);
    exit();
}

// User folder
$user_dir = $base_upload_dir . 'u' . $user_id . '/';
if (!is_dir($user_dir)) {
    if (!mkdir($user_dir, 0755, true)) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create user directory'
        ]);
        exit();
    }
}

// Artisan photos folder
$artisan_photos_dir = $user_dir . 'artisan_photos/';
if (!is_dir($artisan_photos_dir)) {
    if (!mkdir($artisan_photos_dir, 0755, true)) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create artisan photos directory'
        ]);
        exit();
    }
}

// Generate unique filename
$filename = 'artisan_' . $artisan_id . '_' . time() . '_' . uniqid() . '.' . $file_extension;
$target_file = $artisan_photos_dir . $filename;

// Security check - verify the target path is within uploads directory
$real_base = realpath($base_upload_dir);
$real_target = realpath(dirname($target_file));
if ($real_target === false || strpos($real_target, $real_base) !== 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid upload path'
    ]);
    exit();
}

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $target_file)) {
    // Set proper file permissions
    chmod($target_file, 0644);
    
    // Return relative path for database storage (like upload_product_image_action.php)
    $db_path = str_replace('../', '', $target_file);
    
    // Verify the file actually exists
    if (!file_exists($target_file)) {
        echo json_encode([
            'success' => false,
            'message' => 'File was moved but cannot be verified. Please try again.'
        ]);
        exit();
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Photo uploaded successfully',
        'path' => $db_path,
        'file_name' => $filename,
        'file_size' => $file['size']
    ]);
} else {
    $error = error_get_last();
    echo json_encode([
        'success' => false,
        'message' => 'Failed to move uploaded file. ' . ($error['message'] ?? 'Please check directory permissions.')
    ]);
}
?>

