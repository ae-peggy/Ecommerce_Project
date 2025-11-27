<?php
// Include the database connection file
require_once(__DIR__ . '/../settings/db_class.php');

// Only declare class if it doesn't already exist
if (!class_exists('customer_class')) {

    class customer_class extends db_connection {
    
     //Add a new customer to the database
    public function add_customer($name, $email, $password, $country, $city, $contact, $user_role = 2) {
    error_log("=== ADD_CUSTOMER METHOD CALLED ===");
    try {
        // First check if email already exists
        if ($this->email_exists($email)) {
            return false; // Email already exists
        }
        
        // Escape data to prevent SQL injection
        $name = mysqli_real_escape_string($this->db_conn(), $name);
        $email = mysqli_real_escape_string($this->db_conn(), $email);
        $password = mysqli_real_escape_string($this->db_conn(), $password);
        $country = mysqli_real_escape_string($this->db_conn(), $country);
        $city = mysqli_real_escape_string($this->db_conn(), $city);
        $contact = mysqli_real_escape_string($this->db_conn(), $contact);
        
        // Prepare SQL query
        $sql = "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) 
                VALUES ('$name', '$email', '$password', '$country', '$city', '$contact', $user_role)";
        
        error_log("Executing SQL: $sql");
        
        // Execute the query
        if ($this->db_write_query($sql)) {
            // Return the last inserted ID
            $customer_id = $this->last_insert_id();
            error_log("Customer added successfully with ID: $customer_id");
            return $customer_id;
        } else {
            // ADD THIS: Log the actual database error
            $error = mysqli_error($this->db_conn());
            error_log("Database insert failed. MySQL error: " . $error);
            error_log("Failed SQL: " . $sql);
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
            $result = $this->db_fetch_one($sql);
            
            // Correct logic: check if result is an array with data
            return ($result !== null && $result !== false);
            
        } catch (Exception $e) {
            error_log("Error checking email: " . $e->getMessage());
            return false;
        }
    }
    
    // These methods should return false when no record is found
    public function get_customer_by_email($email) {
        try {
            $sql = "SELECT * FROM customer WHERE customer_email = '$email'";
            $result = $this->db_fetch_one($sql);
            return ($result !== null && $result !== false) ? $result : false;
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
    
    // Update customer password
    public function update_password($customer_id, $new_password_hash) {
        try {
            $customer_id = (int)$customer_id;
            $password = mysqli_real_escape_string($this->db_conn(), $new_password_hash);
            
            $sql = "UPDATE customer SET customer_pass = '$password' WHERE customer_id = $customer_id";
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error updating password: " . $e->getMessage());
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

    public function login_customer($email, $password) {
        try {
            $conn = $this->db_conn();
            
            if (!$conn) {
                error_log("Login failed: Unable to obtain database connection");
                return false;
            }
            
            $sql = "SELECT * FROM customer WHERE customer_email = ? LIMIT 1";
            $stmt = mysqli_prepare($conn, $sql);
            
            if ($stmt === false) {
                error_log("Login failed: Unable to prepare statement - " . mysqli_error($conn));
                return false;
            }
            
            mysqli_stmt_bind_param($stmt, 's', $email);
            
            if (!mysqli_stmt_execute($stmt)) {
                error_log("Login failed: Statement execution error - " . mysqli_stmt_error($stmt));
                mysqli_stmt_close($stmt);
                return false;
            }
            
            $result = mysqli_stmt_get_result($stmt);
            $customer = $result ? mysqli_fetch_assoc($result) : null;
            mysqli_stmt_close($stmt);
            
            if (!$customer) {
                error_log("Login failed: Email not found - $email");
                return false;
            }
            
            if (password_verify($password, $customer['customer_pass'])) {
                error_log("Login successful for: $email");
                return $customer;
            }
            
            error_log("Login failed: Wrong password for - $email");
            return false;
            
        } catch (Exception $e) {
            error_log("Error during login: " . $e->getMessage());
            return false;
        }
    }
    }
}
?>