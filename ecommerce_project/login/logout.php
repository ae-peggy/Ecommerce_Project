<?php
// Include core session management functions
require_once '../settings/core.php';

// Log the logout action if user is logged in
if (is_logged_in()) {
    log_user_activity("User logged out");
    error_log("Logging out user: " . get_user_name());
}

// Destroy all session data
session_unset();     // Remove all session variables
session_destroy();   // Destroy the session

// Start a new clean session
session_start();

// Regenerate session ID for security
session_regenerate_id(true);

// Redirect to homepage
header("Location: ../index.php?message=logged_out");
exit();
?>