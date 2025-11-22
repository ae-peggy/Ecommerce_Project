<?php
/**
 * Core Session Management & Authorization Functions
 * This file handles session management and user privilege checking
 */

// Configure secure session settings BEFORE starting session
if (session_status() === PHP_SESSION_NONE) {
    // Session cookie security settings
    ini_set('session.cookie_httponly', 1);           // Prevent JavaScript access to session cookie
    ini_set('session.cookie_secure', 0);            // Set to 1 if using HTTPS
    ini_set('session.use_only_cookies', 1);         // Only use cookies for session IDs
    ini_set('session.cookie_samesite', 'Lax');      // CSRF protection
    
    // Session timeout (30 minutes of inactivity)
    ini_set('session.gc_maxlifetime', 1800);        // 30 minutes in seconds
    ini_set('session.cookie_lifetime', 0);          // Cookie expires when browser closes (0) or set to 1800 for 30 min
    
    // Session storage settings
    ini_set('session.save_handler', 'files');       // Use file-based sessions
    ini_set('session.gc_probability', 1);           // Garbage collection probability
    ini_set('session.gc_divisor', 100);             // Garbage collection divisor
    
    // Start session
    session_start();
    
    // Regenerate session ID periodically to prevent session fixation (every 5 minutes)
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } else if (time() - $_SESSION['created'] > 300) {
        // Regenerate session ID every 5 minutes
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
    
    // Check for session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        // Session expired (30 minutes of inactivity)
        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    
    // Validate session security (IP and User Agent) if user is logged in
    if (isset($_SESSION['user_id'])) {
        validate_session_security();
    }
}

/**
 * Validate session security - check IP address and user agent
 * Prevents session hijacking by detecting changes in client environment
 */
function validate_session_security() {
    $current_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $current_ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    // If session has stored IP/UA, validate them
    if (isset($_SESSION['ip_address']) && isset($_SESSION['user_agent'])) {
        // Allow IP changes for users behind proxies (check if IP changed significantly)
        // For now, we'll be lenient with IP but strict with User Agent
        if ($_SESSION['user_agent'] !== $current_ua) {
            // User agent changed - potential session hijacking
            error_log("Session security violation: User Agent mismatch for user ID: " . ($_SESSION['user_id'] ?? 'unknown'));
            // Don't destroy session immediately, but log it
            // In production, you might want to require re-authentication
        }
        
        // Update IP if it changed (user might be on mobile network)
        if ($_SESSION['ip_address'] !== $current_ip) {
            error_log("IP address changed for user ID: " . ($_SESSION['user_id'] ?? 'unknown') . " from " . $_SESSION['ip_address'] . " to " . $current_ip);
            $_SESSION['ip_address'] = $current_ip;
        }
    } else {
        // First time - store IP and UA
        $_SESSION['ip_address'] = $current_ip;
        $_SESSION['user_agent'] = $current_ua;
    }
}

/**
 * Check if a user is logged in
 * @return bool - Returns true if user is logged in, false otherwise
 */
function is_logged_in() {
    // Check if user_id exists in session and is not empty
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        return false;
    }
    
    // Additional check: ensure session hasn't expired
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        // Session expired - clear it
        session_unset();
        session_destroy();
        return false;
    }
    
    return true;
}

/**
 * Check if the logged-in user has administrative privileges
 * @return bool - Returns true if user is admin (role = 1), false otherwise
 */
function is_admin($allowed_roles = [1]) {
    if (!is_logged_in()) {
        return false;
    }
    
    $user_role = $_SESSION['user_role'] ?? null;

    if ($user_role === null) {
        return false;
    }

    // Normalize allowed roles to an array of integers
    if (!is_array($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }

    $allowed_roles = array_map('intval', $allowed_roles);

    return in_array((int)$user_role, $allowed_roles, true);
}

// Check if user is artisan
function is_artisan() {
    if (!is_logged_in()) return false;
    // Check user_role is 2 (Artisan) and artisan_tier is set
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 2 
        && isset($_SESSION['artisan_tier']) && $_SESSION['artisan_tier'] == 1;
}

// Require artisan access
function require_artisan($redirect_url = '../login/login.php') {
    if (!is_artisan()) {
        header("Location: $redirect_url?error=artisan_access_required");
        exit();
    }
}

// Get artisan ID from session
function get_artisan_id() {
    return $_SESSION['artisan_id'] ?? null;
}

// Get customer ID from session
function get_customer_id() {
    return $_SESSION['customer_id'] ?? null;
}

// Wrapper functions for artisan class methods
function get_artisan_details($artisan_id) {
    require_once dirname(__FILE__) . '/../classes/artisan_class.php';
    $artisan = new artisan_class();
    return $artisan->get_artisan_by_id($artisan_id);
}

function get_customer_details($customer_id) {
    require_once dirname(__FILE__) . '/../classes/customer_class.php';
    $customer = new customer_class();
    return $customer->get_customer_by_id($customer_id);
}

function get_artisan_product_count($artisan_id) {
    require_once dirname(__FILE__) . '/../classes/artisan_class.php';
    $artisan = new artisan_class();
    return $artisan->get_product_count($artisan_id);
}

/**
 * Get orders containing artisan's products
 */
function get_artisan_orders($artisan_id) {
    require_once dirname(__FILE__) . '/../classes/artisan_class.php';
    $artisan = new artisan_class();
    return $artisan->get_orders_for_artisan($artisan_id);
}

function get_artisan_pending_orders($artisan_id) {
    require_once dirname(__FILE__) . '/../classes/artisan_class.php';
    $artisan = new artisan_class();
    return $artisan->get_pending_orders($artisan_id);
}

function get_artisan_total_sales($artisan_id) {
    require_once dirname(__FILE__) . '/../classes/artisan_class.php';
    $artisan = new artisan_class();
    return $artisan->get_total_sales($artisan_id);
}

/**
 * Get monthly sales data for artisan
 */
function get_artisan_monthly_sales($artisan_id, $days = 30) {
    require_once dirname(__FILE__) . '/../classes/artisan_class.php';
    $artisan = new artisan_class();
    return $artisan->get_monthly_sales_data($artisan_id, $days);
}

/**
 * Get top selling products for artisan
 */
function get_artisan_top_products($artisan_id, $limit = 5) {
    require_once dirname(__FILE__) . '/../classes/artisan_class.php';
    $artisan = new artisan_class();
    return $artisan->get_top_products_for_artisan($artisan_id, $limit);
}
function get_artisan_recent_products($artisan_id, $limit = 5) {
    require_once dirname(__FILE__) . '/../classes/artisan_class.php';
    $artisan = new artisan_class();
    return $artisan->get_artisan_products($artisan_id, $limit);
}

function get_all_artisan_products($artisan_id) {
    require_once dirname(__FILE__) . '/../classes/artisan_class.php';
    $artisan = new artisan_class();
    return $artisan->get_artisan_products($artisan_id);
}

function get_all_artisans() {
    require_once dirname(__FILE__) . '/../classes/artisan_class.php';
    $artisan = new artisan_class();
    return $artisan->get_all_artisans();
}

function get_all_approved_artisans() {
    require_once dirname(__FILE__) . '/../classes/artisan_class.php';
    $artisan = new artisan_class();
    return $artisan->get_approved_artisans();
}

/**
 * Get sales by category for artisan
 */
function get_artisan_sales_by_category($artisan_id) {
    require_once dirname(__FILE__) . '/../classes/artisan_class.php';
    $artisan = new artisan_class();
    return $artisan->get_sales_by_category($artisan_id);
}

function get_artisan_revenue_stats($artisan_id) {
    require_once dirname(__FILE__) . '/../classes/artisan_class.php';
    $artisan = new artisan_class();
    return $artisan->get_revenue_summary($artisan_id);
}

function get_all_categories() {
    require_once dirname(__FILE__) . '/../classes/product_class.php';
    $product = new product_class();
    return $product->get_all_categories();
}

function get_all_brands() {
    require_once dirname(__FILE__) . '/../classes/product_class.php';
    $product = new product_class();
    return $product->get_all_brands();
}

// Check if customer is an artisan
function check_artisan_status($customer_id) {
    require_once dirname(__FILE__) . '/../classes/artisan_class.php';
    $artisan = new artisan_class();
    return $artisan->get_artisan_by_customer($customer_id);
}
/**
 * Get current user's role
 * @return int|null - Returns user role or null if not logged in
 */
function get_user_role() {
    if (is_logged_in()) {
        return $_SESSION['user_role'] ?? null;
    }
    return null;
}

/**
 * Get current user's ID
 * @return int|null - Returns user ID or null if not logged in
 */
function get_user_id() {
    if (is_logged_in()) {
        return $_SESSION['user_id'] ?? null;
    }
    return null;
}

/**
 * Get current user's name
 * @return string|null - Returns user name or null if not logged in
 */
function get_user_name() {
    if (is_logged_in()) {
        return $_SESSION['user_name'] ?? '';
    }
    return null;
}

/**
 * Get current user's email
 * @return string|null - Returns user email or null if not logged in
 */
function get_user_email() {
    if (is_logged_in()) {
        return $_SESSION['user_email'] ?? null;
    }
    return null;
}

/**
 * Require user to be logged in - redirect if not
 * @param string $redirect_url - URL to redirect to if not logged in (default: login page)
 */
function require_login($redirect_url = 'login/login.php') {
    if (!is_logged_in()) {
        header("Location: $redirect_url");
        exit();
    }
}

/**
 * Require admin privileges - redirect if not admin
 * @param string $redirect_url - URL to redirect to if not admin (default: index page)
 */
function require_admin($redirect_url = 'index.php', $allowed_roles = [1]) {
    if (!is_admin($allowed_roles)) {
        // Log unauthorized access attempt
        error_log("Unauthorized admin access attempt by user ID: " . (get_user_id() ?? 'guest'));
        header("Location: $redirect_url?error=access_denied");
        exit();
    }
}

/**
 * Check if current user can access a specific resource
 * @param string $required_role - 'admin' or 'customer' or 'any'
 * @return bool - Returns true if user can access, false otherwise
 */
function can_access($required_role = 'any') {
    switch ($required_role) {
        case 'admin':
            return is_admin();
        case 'customer':
            return is_logged_in() && !is_admin();
        case 'any':
            return is_logged_in();
        default:
            return false;
    }
}

/**
 * Get user role name as string
 * @return string - Returns 'Admin', 'Customer', or 'Guest'
 */
function get_user_role_name() {
    if (!is_logged_in()) {
        return 'Guest';
    }
    
    return is_admin() ? 'Admin' : 'Customer';
}

/**
 * Log user activity (optional function for tracking)
 * @param string $activity - Description of the activity
 */
function log_user_activity($activity) {
    $user_info = is_logged_in() 
        ? "User ID: " . get_user_id() . " (" . get_user_name() . ")"
        : "Guest user";
    
    error_log("User Activity - $user_info - $activity");
}

/**
 * Get artisan about page data
 * @param int $artisan_id - Artisan ID
 * @return array|null - Artisan about data or null
 */
function get_artisan_about($artisan_id) {
    require_once dirname(__FILE__) . '/../classes/artisan_class.php';
    $artisan = new artisan_class();
    return $artisan->get_artisan_about($artisan_id);
}
?>