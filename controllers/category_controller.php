<?php
// Include the category class
require_once(__DIR__ . '/../classes/category_class.php');

/**
 * Add a new category
 * @param string $cat_name - Category name
 * @param int $created_by - User ID who created the category
 * @return int|false - Returns category ID if successful, false if failed
 */
function add_category_ctr($cat_name, $created_by) {
    // Create instance of category class
    $category = new category_class();
    
    // Call the add_category method
    return $category->add_category($cat_name, $created_by);
}

/**
 * Get all categories created by a specific user
 * @param int $created_by - User ID
 * @return array|false - Returns array of categories or false if failed
 */
function get_categories_by_user_ctr($created_by) {
    // Create instance of category class
    $category = new category_class();
    
    // Get categories by user
    return $category->get_categories_by_user($created_by);
}

/**
 * Get a single category by ID (with user security check)
 * @param int $cat_id - Category ID
 * @param int $created_by - User ID (for security check)
 * @return array|false - Returns category data or false if not found
 */
function get_category_by_id_ctr($cat_id, $created_by) {
    // Create instance of category class
    $category = new category_class();
    
    // Get category by ID
    return $category->get_category_by_id($cat_id, $created_by);
}

/**
 * Update category name
 * @param int $cat_id - Category ID
 * @param string $cat_name - New category name
 * @param int $created_by - User ID (for security check)
 * @return bool - Returns true if successful, false if failed
 */
function update_category_ctr($cat_id, $cat_name, $created_by) {
    // Create instance of category class
    $category = new category_class();
    
    // Update category
    return $category->update_category($cat_id, $cat_name, $created_by);
}

/**
 * Delete a category
 * @param int $cat_id - Category ID to delete
 * @param int $created_by - User ID (for security check)
 * @return bool - Returns true if successful, false if failed
 */
function delete_category_ctr($cat_id, $created_by) {
    // Create instance of category class
    $category = new category_class();
    
    // Delete category
    return $category->delete_category($cat_id, $created_by);
}

/**
 * Check if category name exists for a user
 * @param string $cat_name - Category name to check
 * @param int $created_by - User ID
 * @return bool - Returns true if category exists, false if not
 */
function category_exists_ctr($cat_name, $created_by) {
    // Create instance of category class
    $category = new category_class();
    
    // Check if category exists
    return $category->category_exists($cat_name, $created_by);
}

/**
 * Get count of categories for a user
 * @param int $created_by - User ID
 * @return int - Returns count of categories
 */
function get_category_count_ctr($created_by) {
    // Create instance of category class
    $category = new category_class();
    
    // Get category count
    return $category->get_category_count($created_by);
}
?>