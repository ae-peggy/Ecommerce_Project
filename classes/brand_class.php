<?php
// Include the database connection file
require_once(__DIR__ . '/../settings/db_class.php');

/**
 * Brand Class - handles all brand-related database operations
 * This class extends the official database connection class
 */
class brand_class extends db_connection {
    
    /**
     * Add a new brand to the database
     * @param string $brand_name - Brand name
     * @param int $created_by - User ID who created the brand
     * @return int|false - Returns brand ID if successful, false if failed
     */
    public function add_brand($brand_name, $created_by) {
        error_log("=== ADD_BRAND METHOD CALLED ===");
        try {
            // Check if brand name already exists for this user
            if ($this->brand_exists($brand_name, $created_by)) {
                error_log("Brand already exists: $brand_name for user: $created_by");
                return false;
            }
            
            // Escape data to prevent SQL injection
            $brand_name = mysqli_real_escape_string($this->db_conn(), $brand_name);
            $created_by = (int)$created_by;
            
            // Prepare SQL query
            $sql = "INSERT INTO brands (brand_name, created_by) VALUES ('$brand_name', $created_by)";
            
            error_log("Executing SQL: $sql");
            
            // Execute the query
            if ($this->db_write_query($sql)) {
                $brand_id = $this->last_insert_id();
                error_log("Brand added successfully with ID: $brand_id");
                return $brand_id;
            } else {
                $error = mysqli_error($this->db_conn());
                error_log("Database insert failed. MySQL error: " . $error);
                error_log("Failed SQL: " . $sql);
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Error adding brand: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if brand name already exists for a specific user
     * @param string $brand_name - Brand name to check
     * @param int $created_by - User ID
     * @return bool - Returns true if brand exists, false if not
     */
    public function brand_exists($brand_name, $created_by) {
        try {
            $brand_name = mysqli_real_escape_string($this->db_conn(), $brand_name);
            $created_by = (int)$created_by;
            $sql = "SELECT brand_id FROM brands WHERE brand_name = '$brand_name' AND created_by = $created_by";
            
            $result = $this->db_fetch_one($sql);
            
            return ($result !== null && $result !== false);
            
        } catch (Exception $e) {
            error_log("Error checking brand: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all brands created by a specific user
     * @param int $created_by - User ID
     * @return array|false - Returns array of brands or false if failed
     */
    public function get_brands_by_user($created_by) {
        try {
            $created_by = (int)$created_by;
            $sql = "SELECT * FROM brands WHERE created_by = $created_by ORDER BY brand_name ASC";
            return $this->db_fetch_all($sql);
        } catch (Exception $e) {
            error_log("Error getting brands by user: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all brands (for dropdown population in product forms)
     * @return array|false - Returns array of all brands or false if failed
     */
    public function get_all_brands() {
        try {
            $sql = "SELECT * FROM brands ORDER BY brand_name ASC";
            return $this->db_fetch_all($sql);
        } catch (Exception $e) {
            error_log("Error getting all brands: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get a single brand by ID and user (for security)
     * @param int $brand_id - Brand ID
     * @param int $created_by - User ID (for security check)
     * @return array|false - Returns brand data or false if not found
     */
    public function get_brand_by_id($brand_id, $created_by) {
        try {
            $brand_id = (int)$brand_id;
            $created_by = (int)$created_by;
            $sql = "SELECT * FROM brands WHERE brand_id = $brand_id AND created_by = $created_by";
            return $this->db_fetch_one($sql);
        } catch (Exception $e) {
            error_log("Error getting brand by ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update brand name
     * @param int $brand_id - Brand ID
     * @param string $brand_name - New brand name
     * @param int $created_by - User ID (for security check)
     * @return bool - Returns true if successful, false if failed
     */
    public function update_brand($brand_id, $brand_name, $created_by) {
        try {
            // Check if new name already exists (excluding current brand)
            if ($this->brand_name_exists_except_current($brand_name, $created_by, $brand_id)) {
                error_log("Brand name already exists: $brand_name");
                return false;
            }
            
            $brand_id = (int)$brand_id;
            $created_by = (int)$created_by;
            $brand_name = mysqli_real_escape_string($this->db_conn(), $brand_name);
            
            $sql = "UPDATE brands SET brand_name = '$brand_name' WHERE brand_id = $brand_id AND created_by = $created_by";
            
            error_log("Executing update SQL: $sql");
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error updating brand: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if brand name exists for user, excluding a specific brand ID
     * @param string $brand_name - Brand name
     * @param int $created_by - User ID
     * @param int $exclude_id - Brand ID to exclude from check
     * @return bool - Returns true if name exists, false if not
     */
    private function brand_name_exists_except_current($brand_name, $created_by, $exclude_id) {
        try {
            $brand_name = mysqli_real_escape_string($this->db_conn(), $brand_name);
            $created_by = (int)$created_by;
            $exclude_id = (int)$exclude_id;
            
            $sql = "SELECT brand_id FROM brands WHERE brand_name = '$brand_name' AND created_by = $created_by AND brand_id != $exclude_id";
            $result = $this->db_fetch_one($sql);
            
            return ($result !== null && $result !== false);
            
        } catch (Exception $e) {
            error_log("Error checking brand name exists: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a brand
     * @param int $brand_id - Brand ID to delete
     * @param int $created_by - User ID (for security check)
     * @return bool - Returns true if successful, false if failed
     */
    public function delete_brand($brand_id, $created_by) {
        try {
            $brand_id = (int)$brand_id;
            $created_by = (int)$created_by;
            
            // First check if brand exists and belongs to user
            $brand = $this->get_brand_by_id($brand_id, $created_by);
            if (!$brand) {
                error_log("Brand not found or doesn't belong to user: $brand_id");
                return false;
            }
            
            $sql = "DELETE FROM brands WHERE brand_id = $brand_id AND created_by = $created_by";
            
            error_log("Executing delete SQL: $sql");
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error deleting brand: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get total count of brands for a user
     * @param int $created_by - User ID
     * @return int - Returns count of brands
     */
    public function get_brand_count($created_by) {
        try {
            $created_by = (int)$created_by;
            $sql = "SELECT COUNT(*) as count FROM brands WHERE created_by = $created_by";
            $result = $this->db_fetch_one($sql);
            return $result ? (int)$result['count'] : 0;
        } catch (Exception $e) {
            error_log("Error getting brand count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get brand by name (searches globally, not per user)
     * @param string $brand_name - Brand name to search
     * @return array|false - Returns brand data or false if not found
     */
    public function get_brand_by_name($brand_name) {
        try {
            $brand_name = mysqli_real_escape_string($this->db_conn(), $brand_name);
            $sql = "SELECT * FROM brands WHERE brand_name = '$brand_name' LIMIT 1";
            return $this->db_fetch_one($sql);
        } catch (Exception $e) {
            error_log("Error getting brand by name: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get or create brand by name (for Tier 1 artisans)
     * @param string $brand_name - Brand name
     * @param int $created_by - User ID who should own the brand
     * @return int|false - Returns brand ID if successful, false if failed
     */
    public function get_or_create_brand($brand_name, $created_by) {
        try {
            // First check if brand exists globally
            $existing_brand = $this->get_brand_by_name($brand_name);
            if ($existing_brand) {
                return (int)$existing_brand['brand_id'];
            }
            
            // If not exists, create it
            return $this->add_brand($brand_name, $created_by);
        } catch (Exception $e) {
            error_log("Error getting or creating brand: " . $e->getMessage());
            return false;
        }
    }
}
?>