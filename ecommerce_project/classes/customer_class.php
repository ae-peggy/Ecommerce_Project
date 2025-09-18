<?php
require_once(__DIR__ . '/../settings/db_class.php');

class customer_class extends db_connection {
    
    public function add_customer($name, $email, $password, $country, $city, $contact, $user_role = 2) {
    $sql = "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) 
            VALUES ('$name', '$email', '$password', '$country', '$city', '$contact', $user_role)";

    if ($this->db_write_query($sql)) {
        return $this->last_insert_id();
    } else {
        return "ERROR: " . mysqli_error($this->db_conn());
    }
}

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
?>
