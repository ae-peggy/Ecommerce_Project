<?php
// Include the cart class
require_once(__DIR__ . '/../classes/cart_class.php');

/**
 * Add a product to the cart
 * @param int $product_id - Product ID
 * @param int $customer_id - Customer ID
 * @param int $qty - Quantity to add
 * @return bool - Returns true if successful, false if failed
 */
function add_to_cart_ctr($product_id, $customer_id, $qty) {
    $cart = new cart_class();
    return $cart->add_to_cart($product_id, $customer_id, $qty);
}

/**
 * Check if product exists in cart
 * @param int $product_id - Product ID
 * @param int $customer_id - Customer ID
 * @return array|false - Returns cart item data or false
 */
function check_product_in_cart_ctr($product_id, $customer_id) {
    $cart = new cart_class();
    return $cart->check_product_in_cart($product_id, $customer_id);
}

/**
 * Update cart item quantity
 * @param int $product_id - Product ID
 * @param int $customer_id - Customer ID
 * @param int $qty - New quantity
 * @return bool - Returns true if successful, false if failed
 */
function update_cart_item_ctr($product_id, $customer_id, $qty) {
    $cart = new cart_class();
    return $cart->update_cart_quantity($product_id, $customer_id, $qty);
}

/**
 * Remove a product from the cart
 * @param int $product_id - Product ID
 * @param int $customer_id - Customer ID
 * @return bool - Returns true if successful, false if failed
 */
function remove_from_cart_ctr($product_id, $customer_id) {
    $cart = new cart_class();
    return $cart->remove_from_cart($product_id, $customer_id);
}

/**
 * Get all cart items for a user with product details
 * @param int $customer_id - Customer ID
 * @return array|false - Returns array of cart items or false if failed
 */
function get_user_cart_ctr($customer_id) {
    $cart = new cart_class();
    return $cart->get_user_cart($customer_id);
}

/**
 * Empty the entire cart for a user
 * @param int $customer_id - Customer ID
 * @return bool - Returns true if successful, false if failed
 */
function empty_cart_ctr($customer_id) {
    $cart = new cart_class();
    return $cart->empty_cart($customer_id);
}

/**
 * Get the total price of all items in the cart
 * @param int $customer_id - Customer ID
 * @return float - Returns total cart value
 */
function get_cart_total_ctr($customer_id) {
    $cart = new cart_class();
    return $cart->get_cart_total($customer_id);
}

/**
 * Get the total number of items in the cart
 * @param int $customer_id - Customer ID
 * @return int - Returns total item count
 */
function get_cart_count_ctr($customer_id) {
    $cart = new cart_class();
    return $cart->get_cart_count($customer_id);
}
?>