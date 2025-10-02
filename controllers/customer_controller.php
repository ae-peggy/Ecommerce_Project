<?php
// Include the customer class
require_once(__DIR__ . '/../classes/customer_class.php');

function register_user_ctr($name, $email, $password, $country, $city, $phone_number, $role = 2) {
    // Create instance of customer class
    $customer = new customer_class();
    
    // Encrypt the password using PHP's password_hash function
    $encrypted_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Call the add_customer method
    return $customer->add_customer($name, $email, $encrypted_password, $country, $city, $phone_number, $role);
}


function get_user_by_email_ctr($email) {
    // Create instance of customer class
    $customer = new customer_class();
    
    // Get customer by email
    return $customer->get_customer_by_email($email);
}

function get_user_by_id_ctr($customer_id) {
    // Create instance of customer class
    $customer = new customer_class();
    
    // Get customer by ID
    return $customer->get_customer_by_id($customer_id);
}


function email_exists_ctr($email) {
    // Create instance of customer class
    $customer = new customer_class();
    
    // Check if email exists
    return $customer->email_exists($email);
}

function update_user_ctr($customer_id, $name, $email, $country, $city, $phone_number) {
    // Create instance of customer class
    $customer = new customer_class();
    
    // Update customer
    return $customer->edit_customer($customer_id, $name, $email, $country, $city, $phone_number);
}

function delete_user_ctr($customer_id) {
    // Create instance of customer class
    $customer = new customer_class();
    
    // Delete customer
    return $customer->delete_customer($customer_id);
}

function login_customer_ctr($email, $password) {
    // Create instance of customer class
    $customer = new customer_class();
    
    // Call login method
    return $customer->login_customer($email, $password);
}
?>
