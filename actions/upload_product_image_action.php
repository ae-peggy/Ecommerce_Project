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

// Check if user has permission (admin or artisan)
if (!is_admin() && !is_artisan()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You do not have permission to upload product images'
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

// Validate file size (max 5MB)
$max_size = 5 * 1024 * 1024;
if ($file['size'] > $max_size) {
    echo json_encode([
        'status' => 'error',
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

// Base upload folder (use absolute path for multitenant server)
$base_upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';

// Create upload directory if it doesn't exist
if (!is_dir($base_upload_dir)) {
    if (!@mkdir($base_upload_dir, 0755, true)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Upload directory does not exist and could not be created. Please contact administrator.'
        ]);
        exit();
    }
}

// Check if directory is writable
if (!is_writable($base_upload_dir)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Upload directory is not writable. Please contact administrator to fix permissions.'
    ]);
    exit();
}

// User folder
$user_dir = $base_upload_dir . 'u' . $user_id . '/';
if (!is_dir($user_dir)) {
    if (!mkdir($user_dir, 0755, true)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create user directory'
        ]);
        exit();
    }
}

// Product folder
if ($product_id > 0) {
    $product_dir = $user_dir . 'p' . $product_id . '/';
    $is_temp = false;
} else {
    if (!isset($_SESSION['temp_upload_id'])) {
        $_SESSION['temp_upload_id'] = uniqid('temp_', true);
    }
    $product_dir = $user_dir . $_SESSION['temp_upload_id'] . '/';
    $is_temp = true;
}

// Create product directory if needed
if (!is_dir($product_dir)) {
    if (!mkdir($product_dir, 0755, true)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create product directory'
        ]);
        exit();
    }
}

// Determine filename
$existing_images = glob($product_dir . '*.' . $file_extension);
$image_number = count($existing_images) + 1;
$filename = $image_number . '.' . $file_extension;
$target_file = $product_dir . $filename;

// Security check - verify the target path is within uploads directory
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
    chmod($target_file, 0644);
    
    // Convert absolute path to relative path for database storage
    // Normalize DOCUMENT_ROOT (remove trailing slash if present)
    $doc_root = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
    $normalized_target = $target_file;
    
    // Remove DOCUMENT_ROOT to get relative path
    if (strpos($normalized_target, $doc_root) === 0) {
        $db_path = substr($normalized_target, strlen($doc_root) + 1); // +1 to remove leading slash
    } else {
        // Fallback: try to extract relative path
        $db_path = str_replace($doc_root . '/', '', $normalized_target);
    }
    
    // Ensure path starts with 'uploads/' and doesn't have leading slash
    $db_path = ltrim($db_path, '/');
    if (strpos($db_path, 'uploads/') !== 0) {
        // If path doesn't start with uploads/, prepend it
        $db_path = 'uploads/' . ltrim(str_replace('uploads/', '', $db_path), '/');
    }
    
    // Verify the file actually exists at the absolute path
    if (!file_exists($target_file)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'File was moved but cannot be verified. Please try again.'
        ]);
        exit();
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Image uploaded successfully',
        'file_path' => $db_path,
        'file_name' => $filename,
        'file_size' => $file['size'],
        'is_temp' => $is_temp,
        'temp_folder' => $is_temp ? basename($product_dir) : null
    ]);
} else {
    $error = error_get_last();
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to move uploaded file. ' . ($error['message'] ?? 'Please check directory permissions.')
    ]);
}
?>