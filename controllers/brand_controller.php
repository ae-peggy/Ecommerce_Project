<?php
// Include the brand class
require_once(__DIR__ . '/../classes/brand_class.php');

/**
 * Add a new brand
 * @param string $brand_name - Brand name
 * @param int $created_by - User ID who created the brand
 * @return int|false - Returns brand ID if successful, false if failed
 */
function add_brand_ctr($brand_name, $created_by) {
    // Create instance of brand class
    $brand = new brand_class();
    
    // Call the add_brand method
    return $brand->add_brand($brand_name, $created_by);
}

/**
 * Get all brands created by a specific user
 * @param int $created_by - User ID
 * @return array|false - Returns array of brands or false if failed
 */
function get_brands_by_user_ctr($created_by) {
    // Create instance of brand class
    $brand = new brand_class();
    
    // Get brands by user
    return $brand->get_brands_by_user($created_by);
}

/**
 * Get all brands (for dropdown population)
 * @return array|false - Returns array of all brands or false if failed
 */
function get_all_brands_ctr() {
    // Create instance of brand class
    $brand = new brand_class();
    
    // Get all brands
    return $brand->get_all_brands();
}

/**
 * Get a single brand by ID (with user security check)
 * @param int $brand_id - Brand ID
 * @param int $created_by - User ID (for security check)
 * @return array|false - Returns brand data or false if not found
 */
function get_brand_by_id_ctr($brand_id, $created_by) {
    // Create instance of brand class
    $brand = new brand_class();
    
    // Get brand by ID
    return $brand->get_brand_by_id($brand_id, $created_by);
}

/**
 * Update brand name
 * @param int $brand_id - Brand ID
 * @param string $brand_name - New brand name
 * @param int $created_by - User ID (for security check)
 * @return bool - Returns true if successful, false if failed
 */
function update_brand_ctr($brand_id, $brand_name, $created_by) {
    // Create instance of brand class
    $brand = new brand_class();
    
    // Update brand
    return $brand->update_brand($brand_id, $brand_name, $created_by);
}

/**
 * Delete a brand
 * @param int $brand_id - Brand ID to delete
 * @param int $created_by - User ID (for security check)
 * @return bool - Returns true if successful, false if failed
 */
function delete_brand_ctr($brand_id, $created_by) {
    // Create instance of brand class
    $brand = new brand_class();
    
    // Delete brand
    return $brand->delete_brand($brand_id, $created_by);
}

/**
 * Check if brand name exists for a user
 * @param string $brand_name - Brand name to check
 * @param int $created_by - User ID
 * @return bool - Returns true if brand exists, false if not
 */
function brand_exists_ctr($brand_name, $created_by) {
    // Create instance of brand class
    $brand = new brand_class();
    
    // Check if brand exists
    return $brand->brand_exists($brand_name, $created_by);
}

/**
 * Get count of brands for a user
 * @param int $created_by - User ID
 * @return int - Returns count of brands
 */
function get_brand_count_ctr($created_by) {
    // Create instance of brand class
    $brand = new brand_class();
    
    // Get brand count
    return $brand->get_brand_count($created_by);
}

/**
 * Get or create brand by name (for Tier 1 artisans)
 * @param string $brand_name - Brand name
 * @param int $created_by - User ID
 * @return int|false - Returns brand ID if successful, false if failed
 */
function get_or_create_brand_ctr($brand_name, $created_by) {
    // Create instance of brand class
    $brand = new brand_class();
    
    // Get or create brand
    return $brand->get_or_create_brand($brand_name, $created_by);
}
?>