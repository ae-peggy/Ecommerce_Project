<?php
require_once '../settings/core.php';
require_artisan('../login/login.php');

header('Content-Type: application/json');

$artisan_id = get_artisan_id();
if (!$artisan_id) {
    echo json_encode(['success' => false, 'message' => 'Artisan not found']);
    exit;
}

if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['photo'];
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
$max_size = 5 * 1024 * 1024; // 5MB

// Validate file type
if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and WEBP are allowed.']);
    exit;
}

// Validate file size
if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit.']);
    exit;
}

// Use base uploads directory (shared with admin)
$upload_dir = '../uploads/';

// Create upload directory only if it doesn't exist
if (!is_dir($upload_dir)) {
    if (!@mkdir($upload_dir, 0755, true)) {
        $error = error_get_last();
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to create upload directory. Please contact administrator.',
            'error' => $error['message'] ?? 'Permission denied'
        ]);
        exit;
    }
}

// Check if directory is writable
if (!is_writable($upload_dir)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Upload directory is not writable. Please contact administrator to fix permissions.'
    ]);
    exit;
}

// Generate unique filename with proper naming convention to avoid conflicts
// Format: artisan_{artisan_id}_{timestamp}_{uniqid}.{ext}
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$filename = 'artisan_' . $artisan_id . '_' . time() . '_' . uniqid() . '.' . $file_extension;
$file_path = $upload_dir . $filename;

// Verify the target path is within uploads directory (security check)
$real_base = realpath($upload_dir);
$real_target = realpath(dirname($file_path));

if ($real_target === false || strpos($real_target, $real_base) !== 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid upload path']);
    exit;
}

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $file_path)) {
    // Set proper file permissions
    chmod($file_path, 0644);
    
    // Return relative path from project root
    $relative_path = 'uploads/' . $filename;
    echo json_encode([
        'success' => true, 
        'path' => $relative_path,
        'message' => 'Photo uploaded successfully'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save file']);
}
?>

