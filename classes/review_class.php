<?php
require_once(__DIR__ . '/../settings/db_class.php');

/**
 * Review Class - handles all product review-related database operations
 */
class review_class extends db_connection {
    
    /**
     * Add a new product review
     * @param int $product_id - Product ID
     * @param int $customer_id - Customer ID
     * @param int $rating - Rating (1-5)
     * @param string $review_text - Review text
     * @param bool $is_verified_purchase - Whether customer purchased the product
     * @return int|false - Returns review_id if successful, false if failed
     */
    public function add_review($product_id, $customer_id, $rating, $review_text = null, $is_verified_purchase = false) {
        try {
            $product_id = (int)$product_id;
            $customer_id = (int)$customer_id;
            $rating = (int)$rating;
            
            // Validate rating
            if ($rating < 1 || $rating > 5) {
                error_log("Invalid rating: $rating");
                return false;
            }
            
            // Check if customer already reviewed this product
            $existing = $this->get_review_by_customer($product_id, $customer_id);
            if ($existing) {
                error_log("Customer already reviewed this product");
                return false;
            }
            
            $conn = $this->db_conn();
            $review_text = $review_text ? mysqli_real_escape_string($conn, $review_text) : null;
            $is_verified = $is_verified_purchase ? 1 : 0;
            
            $sql = "INSERT INTO product_reviews (product_id, customer_id, rating, review_text, is_verified_purchase, status) 
                    VALUES ($product_id, $customer_id, $rating, " . 
                    ($review_text ? "'$review_text'" : "NULL") . ", $is_verified, 'approved')";
            
            error_log("Adding review: $sql");
            
            if ($this->db_write_query($sql)) {
                return $this->last_insert_id();
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error adding review: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing review
     * @param int $review_id - Review ID
     * @param int $customer_id - Customer ID (for verification)
     * @param int $rating - New rating
     * @param string $review_text - New review text
     * @return bool - Returns true if successful, false if failed
     */
    public function update_review($review_id, $customer_id, $rating, $review_text = null) {
        try {
            $review_id = (int)$review_id;
            $customer_id = (int)$customer_id;
            $rating = (int)$rating;
            
            if ($rating < 1 || $rating > 5) {
                return false;
            }
            
            $conn = $this->db_conn();
            $review_text = $review_text ? mysqli_real_escape_string($conn, $review_text) : null;
            
            $sql = "UPDATE product_reviews 
                    SET rating = $rating, review_text = " . 
                    ($review_text ? "'$review_text'" : "NULL") . 
                    " WHERE review_id = $review_id AND customer_id = $customer_id";
            
            return $this->db_write_query($sql);
        } catch (Exception $e) {
            error_log("Error updating review: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a review
     * @param int $review_id - Review ID
     * @param int $customer_id - Customer ID (for verification)
     * @return bool - Returns true if successful, false if failed
     */
    public function delete_review($review_id, $customer_id) {
        try {
            $review_id = (int)$review_id;
            $customer_id = (int)$customer_id;
            
            $sql = "DELETE FROM product_reviews WHERE review_id = $review_id AND customer_id = $customer_id";
            return $this->db_write_query($sql);
        } catch (Exception $e) {
            error_log("Error deleting review: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all reviews for a product
     * @param int $product_id - Product ID
     * @param string $status - Filter by status (default: 'approved')
     * @return array|false - Returns array of reviews or false if failed
     */
    public function get_product_reviews($product_id, $status = 'approved') {
        try {
            $product_id = (int)$product_id;
            $status = mysqli_real_escape_string($this->db_conn(), $status);
            
            $sql = "SELECT r.*, c.customer_name, c.customer_email
                    FROM product_reviews r
                    JOIN customer c ON r.customer_id = c.customer_id
                    WHERE r.product_id = $product_id AND r.status = '$status'
                    ORDER BY r.review_date DESC";
            
            return $this->db_fetch_all($sql);
        } catch (Exception $e) {
            error_log("Error getting product reviews: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get review statistics for a product
     * @param int $product_id - Product ID
     * @return array|false - Returns array with average rating, total reviews, and rating distribution
     */
    public function get_review_stats($product_id) {
        try {
            $product_id = (int)$product_id;
            
            $sql = "SELECT 
                        AVG(rating) as avg_rating,
                        COUNT(*) as total_reviews,
                        SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as rating_5,
                        SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as rating_4,
                        SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as rating_3,
                        SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as rating_2,
                        SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as rating_1
                    FROM product_reviews
                    WHERE product_id = $product_id AND status = 'approved'";
            
            $result = $this->db_fetch_one($sql);
            
            if ($result) {
                $result['avg_rating'] = round((float)$result['avg_rating'], 1);
                $result['total_reviews'] = (int)$result['total_reviews'];
                $result['rating_5'] = (int)$result['rating_5'];
                $result['rating_4'] = (int)$result['rating_4'];
                $result['rating_3'] = (int)$result['rating_3'];
                $result['rating_2'] = (int)$result['rating_2'];
                $result['rating_1'] = (int)$result['rating_1'];
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error getting review stats: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get review by customer for a product
     * @param int $product_id - Product ID
     * @param int $customer_id - Customer ID
     * @return array|false - Returns review data or false if not found
     */
    public function get_review_by_customer($product_id, $customer_id) {
        try {
            $product_id = (int)$product_id;
            $customer_id = (int)$customer_id;
            
            $sql = "SELECT * FROM product_reviews 
                    WHERE product_id = $product_id AND customer_id = $customer_id";
            
            return $this->db_fetch_one($sql);
        } catch (Exception $e) {
            error_log("Error getting review by customer: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if customer purchased the product (for verified purchase badge)
     * @param int $product_id - Product ID
     * @param int $customer_id - Customer ID
     * @return bool - Returns true if customer purchased the product
     */
    public function check_verified_purchase($product_id, $customer_id) {
        try {
            $product_id = (int)$product_id;
            $customer_id = (int)$customer_id;
            
            $sql = "SELECT COUNT(*) as count
                    FROM orders o
                    JOIN orderdetails od ON o.order_id = od.order_id
                    WHERE o.customer_id = $customer_id AND od.product_id = $product_id";
            
            $result = $this->db_fetch_one($sql);
            return $result && (int)$result['count'] > 0;
        } catch (Exception $e) {
            error_log("Error checking verified purchase: " . $e->getMessage());
            return false;
        }
    }
}
?>

