<?php
// Include the product class
require_once(__DIR__ . '/../classes/product_class.php');

/**
 * Add a new product
 */
function add_product_ctr($cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $created_by) {
    $product = new product_class();
    return $product->add_product($cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $created_by);
}

/**
 * Get all products created by a specific user
 */
function get_products_by_user_ctr($created_by) {
    $product = new product_class();
    return $product->get_products_by_user($created_by);
}

/**
 * Get all products (for customer view)
 */
function get_all_products_ctr(array $filters = []) {
    $product = new product_class();
    return $product->get_all_products($filters);
}

/**
 * Get a single product by ID
 */
function get_product_by_id_ctr($product_id) {
    $product = new product_class();
    return $product->get_product_by_id($product_id);
}

/**
 * Get a single product by ID with user check (for edit/delete)
 */
function get_product_by_id_and_user_ctr($product_id, $created_by) {
    $product = new product_class();
    return $product->get_product_by_id_and_user($product_id, $created_by);
}

/**
 * Update product information
 */
function update_product_ctr($product_id, $cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $created_by) {
    $product = new product_class();
    return $product->update_product($product_id, $cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $created_by);
}

/**
 * Delete a product
 */
function delete_product_ctr($product_id, $created_by) {
    $product = new product_class();
    return $product->delete_product($product_id, $created_by);
}

/**
 * Search products
 */
function search_products_ctr($query) {
    $product = new product_class();
    return $product->search_products($query);
}

/**
 * Filter products by category
 */
function filter_products_by_category_ctr($cat_id) {
    $product = new product_class();
    return $product->filter_products_by_category($cat_id);
}

/**
 * Filter products by brand
 */
function filter_products_by_brand_ctr($brand_id) {
    $product = new product_class();
    return $product->filter_products_by_brand($brand_id);
}

/**
 * Get product count for a user
 */
function get_product_count_ctr($created_by) {
    $product = new product_class();
    return $product->get_product_count($created_by);
}

/**
 * Filter products by artisan ID
 */
function filter_products_by_artisan_ctr($artisan_id) {
    $product = new product_class();
    return $product->filter_products_by_artisan($artisan_id);
}
?>