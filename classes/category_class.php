<?php
// Include the database connection file
require_once(__DIR__ . '/../settings/db_class.php');

/**
 * Category Class - handles all category-related database operations
 * This class extends your official database connection class
 */
class category_class extends db_connection {
    
    /**
     * Add a new category to the database
     * @param string $cat_name - Category name
     * @param int $created_by - User ID who created the category
     * @return int|false - Returns category ID if successful, false if failed
     */
   public function add_category($cat_name, $created_by) {
    error_log("=== ADD_CATEGORY METHOD CALLED ===");
    try {
        // Check if category name already exists for this user
        if ($this->category_exists($cat_name, $created_by)) {
            error_log("Category already exists: $cat_name for user: $created_by");
            return false;
        }
        
        // Escape data to prevent SQL injection
        $cat_name = mysqli_real_escape_string($this->db_conn(), $cat_name);
        $created_by = (int)$created_by;
        
        // Prepare SQL query
        $sql = "INSERT INTO categories (cat_name, created_by) VALUES ('$cat_name', $created_by)";
        
        error_log("Executing SQL: $sql");
        
        // Execute the query
        if ($this->db_write_query($sql)) {
            $category_id = $this->last_insert_id();
            error_log("Category added successfully with ID: $category_id");
            return $category_id;
        } else {
            // ADD THIS: Log the actual database error
            $error = mysqli_error($this->db_conn());
            error_log("Database insert failed. MySQL error: " . $error);
            error_log("Failed SQL: " . $sql);
            return false;
        }
        
    } catch (Exception $e) {
        error_log("Error adding category: " . $e->getMessage());
        return false;
    }
}
    
    /**
     * Check if category name already exists for a specific user
     * @param string $cat_name - Category name to check
     * @param int $created_by - User ID
     * @return bool - Returns true if category exists, false if not
     */
    public function category_exists($cat_name, $created_by) {
    try {
        $cat_name = mysqli_real_escape_string($this->db_conn(), $cat_name);
        $created_by = (int)$created_by;
        $sql = "SELECT cat_id FROM categories WHERE cat_name = '$cat_name' AND created_by = $created_by";
        
        $result = $this->db_fetch_one($sql);
        
        // Correct logic: check if result is an array with data
        return ($result !== null && $result !== false);
        
    } catch (Exception $e) {
        error_log("Error checking category: " . $e->getMessage());
        return false;
    }
}
    
    /**
     * Get all categories created by a specific user
     * @param int $created_by - User ID
     * @return array|false - Returns array of categories or false if failed
     */
    public function get_categories_by_user($created_by) {
        try {
            $created_by = (int)$created_by;
            $sql = "SELECT * FROM categories WHERE created_by = $created_by ORDER BY cat_name ASC";
            return $this->db_fetch_all($sql);
        } catch (Exception $e) {
            error_log("Error getting categories by user: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get a single category by ID and user (for security)
     * @param int $cat_id - Category ID
     * @param int $created_by - User ID (for security check)
     * @return array|false - Returns category data or false if not found
     */
    public function get_category_by_id($cat_id, $created_by) {
        try {
            $cat_id = (int)$cat_id;
            $created_by = (int)$created_by;
            $sql = "SELECT * FROM categories WHERE cat_id = $cat_id AND created_by = $created_by";
            return $this->db_fetch_one($sql);
        } catch (Exception $e) {
            error_log("Error getting category by ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update category name
     * @param int $cat_id - Category ID
     * @param string $cat_name - New category name
     * @param int $created_by - User ID (for security check)
     * @return bool - Returns true if successful, false if failed
     */
    public function update_category($cat_id, $cat_name, $created_by) {
        try {
            // Check if new name already exists (excluding current category)
            if ($this->category_name_exists_except_current($cat_name, $created_by, $cat_id)) {
                error_log("Category name already exists: $cat_name");
                return false;
            }
            
            $cat_id = (int)$cat_id;
            $created_by = (int)$created_by;
            $cat_name = mysqli_real_escape_string($this->db_conn(), $cat_name);
            
            $sql = "UPDATE categories SET cat_name = '$cat_name' WHERE cat_id = $cat_id AND created_by = $created_by";
            
            error_log("Executing update SQL: $sql");
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error updating category: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if category name exists for user, excluding a specific category ID
     * @param string $cat_name - Category name
     * @param int $created_by - User ID
     * @param int $exclude_id - Category ID to exclude from check
     * @return bool - Returns true if name exists, false if not
     */
       private function category_name_exists_except_current($cat_name, $created_by, $exclude_id) {
        try {
            $cat_name = mysqli_real_escape_string($this->db_conn(), $cat_name);
            $created_by = (int)$created_by;
            $exclude_id = (int)$exclude_id;
            
            $sql = "SELECT cat_id FROM categories WHERE cat_name = '$cat_name' AND created_by = $created_by AND cat_id != $exclude_id";
            $result = $this->db_fetch_one($sql);
            
            return ($result !== null && $result !== false);
            
        } catch (Exception $e) {
            error_log("Error checking category name exists: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a category
     * @param int $cat_id - Category ID to delete
     * @param int $created_by - User ID (for security check)
     * @return bool - Returns true if successful, false if failed
     */
    public function delete_category($cat_id, $created_by) {
        try {
            $cat_id = (int)$cat_id;
            $created_by = (int)$created_by;
            
            // First check if category exists and belongs to user
            $category = $this->get_category_by_id($cat_id, $created_by);
            if (!$category) {
                error_log("Category not found or doesn't belong to user: $cat_id");
                return false;
            }
            
            $sql = "DELETE FROM categories WHERE cat_id = $cat_id AND created_by = $created_by";
            
            error_log("Executing delete SQL: $sql");
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error deleting category: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get total count of categories for a user
     * @param int $created_by - User ID
     * @return int - Returns count of categories
     */
    public function get_category_count($created_by) {
        try {
            $created_by = (int)$created_by;
            $sql = "SELECT COUNT(*) as count FROM categories WHERE created_by = $created_by";
            $result = $this->db_fetch_one($sql);
            return $result ? (int)$result['count'] : 0;
        } catch (Exception $e) {
            error_log("Error getting category count: " . $e->getMessage());
            return 0;
        }
    }
}
?>