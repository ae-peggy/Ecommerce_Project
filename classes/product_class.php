<?php
// Include the database connection file
require_once(__DIR__ . '/../settings/db_class.php');

/**
 * Product Class - handles all product-related database operations
 */
class product_class extends db_connection {
    
    /**
     * Add a new product to the database
     */
    public function add_product($cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $created_by) {
        error_log("=== ADD_PRODUCT METHOD CALLED ===");
        try {
            // Escape data to prevent SQL injection
            $cat_id = (int)$cat_id;
            $brand_id = (int)$brand_id;
            $title = mysqli_real_escape_string($this->db_conn(), $title);
            $price = (float)$price;
            $desc = mysqli_real_escape_string($this->db_conn(), $desc);
            $image = mysqli_real_escape_string($this->db_conn(), $image);
            $keywords = mysqli_real_escape_string($this->db_conn(), $keywords);
            $created_by = (int)$created_by;
            
            // Prepare SQL query
            $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, 
                    product_desc, product_image, product_keywords, created_by) 
                    VALUES ($cat_id, $brand_id, '$title', $price, '$desc', '$image', '$keywords', $created_by)";
            
            error_log("Executing SQL: $sql");
            
            // Execute the query
            if ($this->db_write_query($sql)) {
                $product_id = $this->last_insert_id();
                error_log("Product added successfully with ID: $product_id");
                return $product_id;
            } else {
                $error = mysqli_error($this->db_conn());
                error_log("Database insert failed. MySQL error: " . $error);
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Error adding product: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all products created by a specific user
     */
    public function get_products_by_user($created_by) {
        try {
            $created_by = (int)$created_by;
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE p.created_by = $created_by 
                    ORDER BY p.created_date DESC";
            return $this->db_fetch_all($sql);
        } catch (Exception $e) {
            error_log("Error getting products by user: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all products (for customer view)
     */
    public function get_all_products() {
        try {
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    ORDER BY p.created_date DESC";
            return $this->db_fetch_all($sql);
        } catch (Exception $e) {
            error_log("Error getting all products: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get a single product by ID
     */
    public function get_product_by_id($product_id) {
        try {
            $product_id = (int)$product_id;
            $sql = "SELECT p.*, c.cat_name, b.brand_name, a.artisan_id, a.business_name as artisan_name
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    LEFT JOIN artisans a ON p.artisan_id = a.artisan_id
                    WHERE p.product_id = $product_id";
            return $this->db_fetch_one($sql);
        } catch (Exception $e) {
            error_log("Error getting product by ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get a single product by ID with user check (for edit/delete security)
     */
    public function get_product_by_id_and_user($product_id, $created_by) {
        try {
            $product_id = (int)$product_id;
            $created_by = (int)$created_by;
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE p.product_id = $product_id AND p.created_by = $created_by";
            return $this->db_fetch_one($sql);
        } catch (Exception $e) {
            error_log("Error getting product by ID and user: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update product information
     */
    public function update_product($product_id, $cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $created_by) {
        try {
            $product_id = (int)$product_id;
            $cat_id = (int)$cat_id;
            $brand_id = (int)$brand_id;
            $title = mysqli_real_escape_string($this->db_conn(), $title);
            $price = (float)$price;
            $desc = mysqli_real_escape_string($this->db_conn(), $desc);
            $keywords = mysqli_real_escape_string($this->db_conn(), $keywords);
            $created_by = (int)$created_by;
            
            // Handle image update - only update if new image provided
            if (!empty($image)) {
                $image = mysqli_real_escape_string($this->db_conn(), $image);
                $sql = "UPDATE products SET 
                        product_cat = $cat_id,
                        product_brand = $brand_id,
                        product_title = '$title',
                        product_price = $price,
                        product_desc = '$desc',
                        product_image = '$image',
                        product_keywords = '$keywords'
                        WHERE product_id = $product_id AND created_by = $created_by";
            } else {
                // Don't update image if not provided
                $sql = "UPDATE products SET 
                        product_cat = $cat_id,
                        product_brand = $brand_id,
                        product_title = '$title',
                        product_price = $price,
                        product_desc = '$desc',
                        product_keywords = '$keywords'
                        WHERE product_id = $product_id AND created_by = $created_by";
            }
            
            error_log("Executing update SQL: $sql");
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a product
     */
    public function delete_product($product_id, $created_by) {
        try {
            $product_id = (int)$product_id;
            $created_by = (int)$created_by;
            
            $sql = "DELETE FROM products WHERE product_id = $product_id AND created_by = $created_by";
            
            error_log("Executing delete SQL: $sql");
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error deleting product: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Search products by title or keywords
     */
    public function search_products($query) {
        try {
            $query = mysqli_real_escape_string($this->db_conn(), $query);
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE p.product_title LIKE '%$query%' 
                    OR p.product_keywords LIKE '%$query%'
                    OR p.product_desc LIKE '%$query%'
                    ORDER BY p.created_date DESC";
            return $this->db_fetch_all($sql);
        } catch (Exception $e) {
            error_log("Error searching products: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Filter products by category
     */
    public function filter_products_by_category($cat_id) {
        try {
            $cat_id = (int)$cat_id;
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE p.product_cat = $cat_id
                    ORDER BY p.created_date DESC";
            return $this->db_fetch_all($sql);
        } catch (Exception $e) {
            error_log("Error filtering products by category: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Filter products by brand
     */
    public function filter_products_by_brand($brand_id) {
        try {
            $brand_id = (int)$brand_id;
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE p.product_brand = $brand_id
                    ORDER BY p.created_date DESC";
            return $this->db_fetch_all($sql);
        } catch (Exception $e) {
            error_log("Error filtering products by brand: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Filter products by artisan ID
     */
    public function filter_products_by_artisan($artisan_id) {
        try {
            $artisan_id = (int)$artisan_id;
            $sql = "SELECT p.*, c.cat_name, b.brand_name, a.artisan_id, a.business_name as artisan_name
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    LEFT JOIN artisans a ON p.artisan_id = a.artisan_id
                    WHERE p.artisan_id = $artisan_id
                    ORDER BY p.created_date DESC";
            return $this->db_fetch_all($sql);
        } catch (Exception $e) {
            error_log("Error filtering products by artisan: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get product count for a user
     */
    public function get_product_count($created_by) {
        try {
            $created_by = (int)$created_by;
            $sql = "SELECT COUNT(*) as count FROM products WHERE created_by = $created_by";
            $result = $this->db_fetch_one($sql);
            return $result ? (int)$result['count'] : 0;
        } catch (Exception $e) {
            error_log("Error getting product count: " . $e->getMessage());
            return 0;
        }
    }

    public function get_all_categories() {
        try {
            $sql = "SELECT * FROM categories ORDER BY cat_name ASC";
            return $this->db_fetch_all($sql);
        } catch (Exception $e) {
            error_log("Error getting categories: " . $e->getMessage());
            return false;
        }
    }

    public function get_all_brands() {
        try {
            $sql = "SELECT * FROM brands ORDER BY brand_name ASC";
            return $this->db_fetch_all($sql);
        } catch (Exception $e) {
            error_log("Error getting brands: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reduce product stock quantity
     * @param int $product_id - Product ID
     * @param int $quantity - Quantity to reduce
     * @return bool - Returns true if successful, false if failed
     */
    public function reduce_stock($product_id, $quantity) {
        try {
            $product_id = (int)$product_id;
            $quantity = (int)$quantity;
            
            if ($quantity <= 0) {
                return false;
            }
            
            $sql = "UPDATE products SET product_qty = product_qty - $quantity 
                    WHERE product_id = $product_id AND product_qty >= $quantity";
            
            error_log("Reducing stock - Product: $product_id, Qty: $quantity");
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error reducing stock: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if product has sufficient stock
     * @param int $product_id - Product ID
     * @param int $quantity - Required quantity
     * @return bool - Returns true if stock is available, false otherwise
     */
    public function check_stock_availability($product_id, $quantity) {
        try {
            $product_id = (int)$product_id;
            $quantity = (int)$quantity;
            
            $sql = "SELECT product_qty FROM products WHERE product_id = $product_id";
            $result = $this->db_fetch_one($sql);
            
            if (!$result) {
                return false;
            }
            
            $available_stock = (int)$result['product_qty'];
            return $available_stock >= $quantity;
            
        } catch (Exception $e) {
            error_log("Error checking stock: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get product stock quantity
     * @param int $product_id - Product ID
     * @return int - Returns stock quantity or 0 if not found
     */
    public function get_product_stock($product_id) {
        try {
            $product_id = (int)$product_id;
            $sql = "SELECT product_qty FROM products WHERE product_id = $product_id";
            $result = $this->db_fetch_one($sql);
            return $result ? (int)$result['product_qty'] : 0;
        } catch (Exception $e) {
            error_log("Error getting product stock: " . $e->getMessage());
            return 0;
        }
    }
}

?>