<?php
// Include the customer class
require_once(__DIR__ . '/../classes/customer_class.php');

/**
 * Register a new customer
 * @param string $name - Customer name
 * @param string $email - Customer email
 * @param string $password - Plain text password (will be encrypted)
 * @param string $country - Customer country
 * @param string $city - Customer city
 * @param string $phone_number - Customer phone number
 * @param int $role - User role (default 2 for customer)
 * @return int|false - Returns customer ID if successful, false if failed
 */
function register_user_ctr($name, $email, $password, $country, $city, $phone_number, $role = 2) {
    // Create instance of customer class
    $customer = new customer_class();
    
    // Encrypt the password using PHP's password_hash function
    $encrypted_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Call the add_customer method
    return $customer->add_customer($name, $email, $encrypted_password, $country, $city, $phone_number, $role);
}

/**
 * Get customer by email
 * @param string $email - Customer email
 * @return array|false - Returns customer data or false if not found
 */
function get_user_by_email_ctr($email) {
    // Create instance of customer class
    $customer = new customer_class();
    
    // Get customer by email
    return $customer->get_customer_by_email($email);
}

/**
 * Get customer by ID
 * @param int $customer_id - Customer ID
 * @return array|false - Returns customer data or false if not found
 */
function get_user_by_id_ctr($customer_id) {
    // Create instance of customer class
    $customer = new customer_class();
    
    // Get customer by ID
    return $customer->get_customer_by_id($customer_id);
}

/**
 * Check if email exists
 * @param string $email - Email to check
 * @return bool - Returns true if email exists, false if not
 */
function email_exists_ctr($email) {
    // Create instance of customer class
    $customer = new customer_class();
    
    // Check if email exists
    return $customer->email_exists($email);
}

/**
 * Update customer information
 * @param int $customer_id - Customer ID
 * @param string $name - Customer name
 * @param string $email - Customer email
 * @param string $country - Customer country
 * @param string $city - Customer city
 * @param string $phone_number - Customer phone number
 * @return bool - Returns true if successful, false if failed
 */
function update_user_ctr($customer_id, $name, $email, $country, $city, $phone_number) {
    // Create instance of customer class
    $customer = new customer_class();
    
    // Update customer
    return $customer->edit_customer($customer_id, $name, $email, $country, $city, $phone_number);
}

/**
 * Delete customer
 * @param int $customer_id - Customer ID to delete
 * @return bool - Returns true if successful, false if failed
 */
function delete_user_ctr($customer_id) {
    // Create instance of customer class
    $customer = new customer_class();
    
    // Delete customer
    return $customer->delete_customer($customer_id);
}
?>