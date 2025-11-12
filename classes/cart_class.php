<?php
// Include the database connection file
require_once(__DIR__ . '/../settings/db_class.php');

/**
 * Cart Class - handles all cart-related database operations
 * This class extends the official database connection class
 */
class cart_class extends db_connection {
    
    /**
     * Add a product to the cart
     * If product already exists, increment quantity instead of creating duplicate
     * @param int $product_id - Product ID
     * @param int $customer_id - Customer ID
     * @param int $qty - Quantity to add
     * @return bool - Returns true if successful, false if failed
     */
    public function add_to_cart($product_id, $customer_id, $qty) {
        error_log("=== ADD_TO_CART METHOD CALLED ===");
        try {
            $product_id = (int)$product_id;
            $customer_id = (int)$customer_id;
            $qty = (int)$qty;
            
            // Check if product already exists in cart
            $existing = $this->check_product_in_cart($product_id, $customer_id);
            
            if ($existing) {
                // Product exists, update quantity
                $new_qty = $existing['qty'] + $qty;
                $sql = "UPDATE cart SET qty = $new_qty WHERE p_id = $product_id AND c_id = $customer_id";
                error_log("Updating existing cart item. New qty: $new_qty");
            } else {
                // Product doesn't exist, insert new
                $sql = "INSERT INTO cart (p_id, c_id, qty) VALUES ($product_id, $customer_id, $qty)";
                error_log("Inserting new cart item");
            }
            
            error_log("Executing SQL: $sql");
            
            if ($this->db_write_query($sql)) {
                error_log("Cart operation successful");
                return true;
            } else {
                $error = mysqli_error($this->db_conn());
                error_log("Cart operation failed. MySQL error: " . $error);
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Error in add_to_cart: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if a product already exists in the cart
     * @param int $product_id - Product ID
     * @param int $customer_id - Customer ID
     * @return array|false - Returns cart item data or false if not found
     */
    public function check_product_in_cart($product_id, $customer_id) {
        try {
            $product_id = (int)$product_id;
            $customer_id = (int)$customer_id;
            
            $sql = "SELECT * FROM cart WHERE p_id = $product_id AND c_id = $customer_id";
            $result = $this->db_fetch_one($sql);
            
            return ($result !== null && $result !== false) ? $result : false;
            
        } catch (Exception $e) {
            error_log("Error checking product in cart: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update the quantity of a product in the cart
     * @param int $product_id - Product ID
     * @param int $customer_id - Customer ID
     * @param int $qty - New quantity
     * @return bool - Returns true if successful, false if failed
     */
    public function update_cart_quantity($product_id, $customer_id, $qty) {
        try {
            $product_id = (int)$product_id;
            $customer_id = (int)$customer_id;
            $qty = (int)$qty;
            
            if ($qty <= 0) {
                error_log("Quantity must be greater than 0");
                return false;
            }
            
            $sql = "UPDATE cart SET qty = $qty WHERE p_id = $product_id AND c_id = $customer_id";
            
            error_log("Executing update SQL: $sql");
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error updating cart quantity: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remove a product from the cart
     * @param int $product_id - Product ID
     * @param int $customer_id - Customer ID
     * @return bool - Returns true if successful, false if failed
     */
    public function remove_from_cart($product_id, $customer_id) {
        try {
            $product_id = (int)$product_id;
            $customer_id = (int)$customer_id;
            
            $sql = "DELETE FROM cart WHERE p_id = $product_id AND c_id = $customer_id";
            
            error_log("Executing delete SQL: $sql");
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error removing from cart: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all cart items for a user with product details
     * @param int $customer_id - Customer ID
     * @return array|false - Returns array of cart items with product details or false if failed
     */
    public function get_user_cart($customer_id) {
        try {
            $customer_id = (int)$customer_id;
            
            $sql = "SELECT 
                        c.p_id,
                        c.qty,
                        p.product_title,
                        p.product_price,
                        p.product_image,
                        p.product_cat,
                        p.product_brand,
                        cat.cat_name,
                        b.brand_name,
                        (c.qty * p.product_price) as subtotal
                    FROM cart c
                    INNER JOIN products p ON c.p_id = p.product_id
                    LEFT JOIN categories cat ON p.product_cat = cat.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE c.c_id = $customer_id
                    ORDER BY c.p_id DESC";
            
            return $this->db_fetch_all($sql);
            
        } catch (Exception $e) {
            error_log("Error getting user cart: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Empty the entire cart for a user
     * @param int $customer_id - Customer ID
     * @return bool - Returns true if successful, false if failed
     */
    public function empty_cart($customer_id) {
        try {
            $customer_id = (int)$customer_id;
            
            $sql = "DELETE FROM cart WHERE c_id = $customer_id";
            
            error_log("Emptying cart for user: $customer_id");
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error emptying cart: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get the total price of all items in the cart
     * @param int $customer_id - Customer ID
     * @return float - Returns total cart value
     */
    public function get_cart_total($customer_id) {
        try {
            $customer_id = (int)$customer_id;
            
            $sql = "SELECT SUM(c.qty * p.product_price) as total
                    FROM cart c
                    INNER JOIN products p ON c.p_id = p.product_id
                    WHERE c.c_id = $customer_id";
            
            $result = $this->db_fetch_one($sql);
            
            return $result ? (float)$result['total'] : 0.00;
            
        } catch (Exception $e) {
            error_log("Error getting cart total: " . $e->getMessage());
            return 0.00;
        }
    }
    
    /**
     * Get the total number of items in the cart
     * @param int $customer_id - Customer ID
     * @return int - Returns total item count
     */
    public function get_cart_count($customer_id) {
        try {
            $customer_id = (int)$customer_id;
            
            $sql = "SELECT SUM(qty) as count FROM cart WHERE c_id = $customer_id";
            
            $result = $this->db_fetch_one($sql);
            
            return $result ? (int)$result['count'] : 0;
            
        } catch (Exception $e) {
            error_log("Error getting cart count: " . $e->getMessage());
            return 0;
        }
    }
}
?>