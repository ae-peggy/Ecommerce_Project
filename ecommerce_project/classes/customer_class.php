<?php
// Include the database connection file
require_once(__DIR__ . '/../settings/db_class.php');

// Only declare class if it doesn't already exist
if (!class_exists('customer_class')) {

    class customer_class extends db_connection {
    
     //Add a new customer to the database
    public function add_customer($name, $email, $password, $country, $city, $contact, $user_role = 2) {
        try {
            // First check if email already exists
            if ($this->email_exists($email)) {
                return false; // Email already exists
            }
            
            // Prepare SQL query
            $sql = "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) 
                    VALUES ('$name', '$email', '$password', '$country', '$city', '$contact', $user_role)";
            
            // Execute the query
            if ($this->db_write_query($sql)) {
                // Return the last inserted ID
                return $this->last_insert_id();
            } else {
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Error adding customer: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if email already exists in database
     * @param string $email - Email to check
     * @return bool - Returns true if email exists, false if not
     */
    public function email_exists($email) {
        try {
            $sql = "SELECT customer_id FROM customer WHERE customer_email = '$email'";
            
            // Use db_fetch_one to get a single record
            $result = $this->db_fetch_one($sql);
            
            // If result is not false, email exists
            return ($result !== false);
            
        } catch (Exception $e) {
            error_log("Error checking email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get customer by email
     * @param string $email - Customer email
     * @return array|false - Returns customer data array or false if not found
     */
    public function get_customer_by_email($email) {
        try {
            $sql = "SELECT * FROM customer WHERE customer_email = '$email'";
            return $this->db_fetch_one($sql);
        } catch (Exception $e) {
            error_log("Error getting customer by email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get customer by ID
     * @param int $customer_id - Customer ID
     * @return array|false - Returns customer data array or false if not found
     */
    public function get_customer_by_id($customer_id) {
        try {
            $sql = "SELECT * FROM customer WHERE customer_id = $customer_id";
            return $this->db_fetch_one($sql);
        } catch (Exception $e) {
            error_log("Error getting customer by ID: " . $e->getMessage());
            return false;
        }
    }
    
    
    // Update customer information
    public function edit_customer($customer_id, $name, $email, $country, $city, $contact) {
        try {
            $sql = "UPDATE customer SET 
                    customer_name = '$name',
                    customer_email = '$email',
                    customer_country = '$country',
                    customer_city = '$city',
                    customer_contact = '$contact'
                    WHERE customer_id = $customer_id";
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error updating customer: " . $e->getMessage());
            return false;
        }
    }
    
     //Delete customer

    public function delete_customer($customer_id) {
        try {
            $sql = "DELETE FROM customer WHERE customer_id = $customer_id";
            return $this->db_write_query($sql);
        } catch (Exception $e) {
            error_log("Error deleting customer: " . $e->getMessage());
            return false;
        }
    }
    }
}
?>