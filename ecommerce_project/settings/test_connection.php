<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test database connection
require_once 'db_class.php';


// Create an instance of the database connection class
$db = new db_connection();

// Test the connection
if ($db->db_connect()) {
    echo "✅ Database connection successful!<br>";
    echo "Connected to database: " . DATABASE . "<br>";
    echo "Server: " . SERVER . "<br>";
    echo "Username: " . USERNAME . "<br>";
} else {
    echo "❌ Database connection failed!<br>";
    echo "Please check your database credentials in db_cred.php<br>";
    echo "Make sure XAMPP MySQL is running<br>";
}
?>