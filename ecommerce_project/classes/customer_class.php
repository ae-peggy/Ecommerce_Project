<?php
// Include the database connection file - adjust path based on your structure
require_once(__DIR__ . '/../settings/db_class.php');

/**
 * Customer Class - handles all customer-related database operations
 * This class extends your official database connection class
 */
class customer_class extends db_connection {
    
    /**
     * Add a new customer to the database
     * @param string $name - Customer name
     * @param string $email - Customer email
     * @param string $password - Encrypted password
     * @param string $country - Customer country
     * @param string $city - Customer city
     * @param string $contact - Customer contact number
     * @param int $user_role - User role (default 2 for customer)
     * @return int|false - Returns customer ID if successful, false if failed
     */
    public function add_customer($name, $email, $password, $country, $city, $contact, $user_role = 2) {
        try {
            // First check if email already exists
            if ($this->email_exists($email)) {
                error_log("Email already exists: $email");
                return false; // Email already exists
            }
            
            // Escape data to prevent SQL injection
            $name = mysqli_real_escape_string($this->db_conn(), $name);
            $email = mysqli_real_escape_string($this->db_conn(), $email);
            $password = mysqli_real_escape_string($this->db_conn(), $password);
            $country = mysqli_real_escape_string($this->db_conn(), $country);
            $city = mysqli_real_escape_string($this->db_conn(), $city);
            $contact = mysqli_real_escape_string($this->db_conn(), $contact);
            $user_role = (int)$user_role; // Cast to integer
            
            // Prepare SQL query
            $sql = "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) 
                    VALUES ('$name', '$email', '$password', '$country', '$city', '$contact', $user_role)";
            
            error_log("Executing SQL: $sql");
            
            // Execute the query
            if ($this->db_write_query($sql)) {
                $customer_id = $this->last_insert_id();
                error_log("Customer added successfully with ID: $customer_id");
                return $customer_id;
            } else {
                error_log("Failed to execute insert query");
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
            $email = mysqli_real_escape_string($this->db_conn(), $email);
            $sql = "SELECT customer_id FROM customer WHERE customer_email = '$email'";
            
            error_log("Checking email exists with SQL: $sql");
            
            // Use db_fetch_one to get a single record
            $result = $this->db_fetch_one($sql);
            
            error_log("Email check result: " . ($result ? "exists" : "doesn't exist"));
            
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
            $email = mysqli_real_escape_string($this->db_conn(), $email);
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
            $customer_id = (int)$customer_id;
            $sql = "SELECT * FROM customer WHERE customer_id = $customer_id";
            return $this->db_fetch_one($sql);
        } catch (Exception $e) {
            error_log("Error getting customer by ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update customer information
     * @param int $customer_id - Customer ID
     * @param string $name - Customer name
     * @param string $email - Customer email
     * @param string $country - Customer country
     * @param string $city - Customer city
     * @param string $contact - Customer contact number
     * @return bool - Returns true if successful, false if failed
     */
    public function edit_customer($customer_id, $name, $email, $country, $city, $contact) {
        try {
            $customer_id = (int)$customer_id;
            $name = mysqli_real_escape_string($this->db_conn(), $name);
            $email = mysqli_real_escape_string($this->db_conn(), $email);
            $country = mysqli_real_escape_string($this->db_conn(), $country);
            $city = mysqli_real_escape_string($this->db_conn(), $city);
            $contact = mysqli_real_escape_string($this->db_conn(), $contact);
            
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
    
    /**
     * Delete customer
     * @param int $customer_id - Customer ID to delete
     * @return bool - Returns true if successful, false if failed
     */
    public function delete_customer($customer_id) {
        try {
            $customer_id = (int)$customer_id;
            $sql = "DELETE FROM customer WHERE customer_id = $customer_id";
            return $this->db_write_query($sql);
        } catch (Exception $e) {
            error_log("Error deleting customer: " . $e->getMessage());
            return false;
        }
    }
}
?><?php
// Include the database connection file
require_once(__DIR__ . '/../settings/db_class.php');

// Only declare class if it doesn't already exist
if (!class_exists('customer_class')) {
    class customer_class extends db_connection {
        
        /**
         * Add a new customer to the database
         * @param string $name - Customer name
         * @param string $email - Customer email
         * @param string $password - Encrypted password
         * @param string $country - Customer country
         * @param string $city - Customer city
         * @param string $contact - Customer contact number
         * @param int $user_role - User role (default 2 for customer)
         * @return int|false - Returns customer ID if successful, false if failed
         */
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
        
        /**
         * Update customer information
         * @param int $customer_id - Customer ID
         * @param string $name - Customer name
         * @param string $email - Customer email
         * @param string $country - Customer country
         * @param string $city - Customer city
         * @param string $contact - Customer contact number
         * @return bool - Returns true if successful, false if failed
         */
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
        
        /**
         * Delete customer
         * @param int $customer_id - Customer ID to delete
         * @return bool - Returns true if successful, false if failed
         */
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