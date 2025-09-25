<?php
// Include core session management functions
require_once 'settings/core.php';

// Log the logout action
if (is_logged_in()) {
    log_user_activity("User logged out");
}

// Destroy all session data
session_unset();     // Remove all session variables
session_destroy();   // Destroy the session

// Redirect to homepage with a message
header("Location: index.php?message=logged_out");
exit();
?>