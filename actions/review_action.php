<?php
/**
 * Review Action
 * Handles product review operations (add, update, delete, get)
 */

header('Content-Type: application/json');
ob_start();
require_once '../settings/core.php';

if (!is_logged_in()) {
    ob_end_clean();
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to submit a review'
    ]);
    exit();
}

require_once '../controllers/review_controller.php';

$action = isset($_POST['action']) ? trim($_POST['action']) : '';

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $customer_id = get_user_id();
            $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
            $review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : null;
            
            if ($product_id <= 0) {
                ob_end_clean();
                echo json_encode(['status' => 'error', 'message' => 'Invalid product ID']);
                exit();
            }
            
            if ($rating < 1 || $rating > 5) {
                ob_end_clean();
                echo json_encode(['status' => 'error', 'message' => 'Rating must be between 1 and 5']);
                exit();
            }
            
            // Check if customer purchased the product
            $is_verified = check_verified_purchase_ctr($product_id, $customer_id);
            
            $review_id = add_review_ctr($product_id, $customer_id, $rating, $review_text, $is_verified);
            
            if ($review_id) {
                ob_end_clean();
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Review submitted successfully',
                    'review_id' => $review_id
                ]);
            } else {
                ob_end_clean();
                echo json_encode(['status' => 'error', 'message' => 'Failed to submit review. You may have already reviewed this product.']);
            }
        }
        break;
        
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $review_id = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;
            $customer_id = get_user_id();
            $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
            $review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : null;
            
            if ($review_id <= 0) {
                ob_end_clean();
                echo json_encode(['status' => 'error', 'message' => 'Invalid review ID']);
                exit();
            }
            
            if ($rating < 1 || $rating > 5) {
                ob_end_clean();
                echo json_encode(['status' => 'error', 'message' => 'Rating must be between 1 and 5']);
                exit();
            }
            
            $result = update_review_ctr($review_id, $customer_id, $rating, $review_text);
            
            if ($result) {
                ob_end_clean();
                echo json_encode(['status' => 'success', 'message' => 'Review updated successfully']);
            } else {
                ob_end_clean();
                echo json_encode(['status' => 'error', 'message' => 'Failed to update review']);
            }
        }
        break;
        
    case 'delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $review_id = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;
            $customer_id = get_user_id();
            
            if ($review_id <= 0) {
                ob_end_clean();
                echo json_encode(['status' => 'error', 'message' => 'Invalid review ID']);
                exit();
            }
            
            $result = delete_review_ctr($review_id, $customer_id);
            
            if ($result) {
                ob_end_clean();
                echo json_encode(['status' => 'success', 'message' => 'Review deleted successfully']);
            } else {
                ob_end_clean();
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete review']);
            }
        }
        break;
        
    case 'get':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            
            if ($product_id <= 0) {
                ob_end_clean();
                echo json_encode(['status' => 'error', 'message' => 'Invalid product ID']);
                exit();
            }
            
            $reviews = get_product_reviews_ctr($product_id);
            $stats = get_review_stats_ctr($product_id);
            
            ob_end_clean();
            echo json_encode([
                'status' => 'success',
                'reviews' => $reviews ?: [],
                'stats' => $stats ?: ['avg_rating' => 0, 'total_reviews' => 0]
            ]);
        }
        break;
        
    default:
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>
