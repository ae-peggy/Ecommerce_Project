<?php
require_once '../settings/core.php';
require_artisan('../login/login.php');

header('Content-Type: application/json');

$artisan_id = get_artisan_id();
if (!$artisan_id) {
    echo json_encode(['success' => false, 'message' => 'Artisan not found']);
    exit;
}

require_once '../classes/artisan_class.php';
$artisan = new artisan_class();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'save':
        // Get form data
        $artisan_bio = trim($_POST['artisan_bio'] ?? '');
        $cultural_meaning = trim($_POST['cultural_meaning'] ?? '');
        $crafting_method = trim($_POST['crafting_method'] ?? '');
        $artisan_location = trim($_POST['artisan_location'] ?? '');
        
        // Get photos array from JSON or comma-separated string
        $artisan_photos = [];
        if (!empty($_POST['artisan_photos'])) {
            if (is_string($_POST['artisan_photos'])) {
                $decoded = json_decode($_POST['artisan_photos'], true);
                $artisan_photos = is_array($decoded) ? $decoded : explode(',', $_POST['artisan_photos']);
            } else if (is_array($_POST['artisan_photos'])) {
                $artisan_photos = $_POST['artisan_photos'];
            }
        }
        
        // Clean up photos array (remove empty values)
        $artisan_photos = array_filter(array_map('trim', $artisan_photos));
        $artisan_photos = array_values($artisan_photos); // Re-index array
        
        // Update about page
        $result = $artisan->update_artisan_about(
            $artisan_id,
            $artisan_bio,
            $cultural_meaning,
            $crafting_method,
            $artisan_location,
            $artisan_photos
        );
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'About page updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update about page']);
        }
        break;
        
    case 'get':
        // Get about page data
        $about_data = $artisan->get_artisan_about($artisan_id);
        if ($about_data) {
            echo json_encode(['success' => true, 'data' => $about_data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'About page not found']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>

