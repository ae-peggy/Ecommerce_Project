<?php
require_once(__DIR__ . '/../classes/review_class.php');

/**
 * Add a new product review
 */
function add_review_ctr($product_id, $customer_id, $rating, $review_text = null, $is_verified_purchase = false) {
    $review = new review_class();
    return $review->add_review($product_id, $customer_id, $rating, $review_text, $is_verified_purchase);
}

/**
 * Update an existing review
 */
function update_review_ctr($review_id, $customer_id, $rating, $review_text = null) {
    $review = new review_class();
    return $review->update_review($review_id, $customer_id, $rating, $review_text);
}

/**
 * Delete a review
 */
function delete_review_ctr($review_id, $customer_id) {
    $review = new review_class();
    return $review->delete_review($review_id, $customer_id);
}

/**
 * Get all reviews for a product
 */
function get_product_reviews_ctr($product_id, $status = 'approved') {
    $review = new review_class();
    return $review->get_product_reviews($product_id, $status);
}

/**
 * Get review statistics for a product
 */
function get_review_stats_ctr($product_id) {
    $review = new review_class();
    return $review->get_review_stats($product_id);
}

/**
 * Get review by customer for a product
 */
function get_review_by_customer_ctr($product_id, $customer_id) {
    $review = new review_class();
    return $review->get_review_by_customer($product_id, $customer_id);
}

/**
 * Check if customer purchased the product
 */
function check_verified_purchase_ctr($product_id, $customer_id) {
    $review = new review_class();
    return $review->check_verified_purchase($product_id, $customer_id);
}
?>

