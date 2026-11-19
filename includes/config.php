<?php
/**
 * Xlator - Configuration & Helper Functions
 * Optimized for Hostinger PHP Hosting
 */

// Error reporting (Set to 0 in production on Hostinger)
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Site constants
define('SITE_NAME', 'Xlator');
define('SITE_TAGLINE', "Brands Aren't Born. They're Built Together.");
define('ADMIN_EMAIL', 'hello@xlator.com'); // Update with your Hostinger notification email
define('DATA_DIR', __DIR__ . '/../data');

// Admin credentials (Change these for your production Hostinger setup)
define('ADMIN_USERNAME', 'admin');
// Default password: "password123" (hashed with BCRYPT)
define('ADMIN_PASSWORD_HASH', '$2y$10$AT64XSre.2l56FoCx0AU/eagN2AD4PJaOqHQA5s97wAzPhc7e4tPG');

// Start session if not started, with robust fallback path
if (session_status() === PHP_SESSION_NONE) {
    $current_save_path = session_save_path();
    if (empty($current_save_path) || !is_writable($current_save_path)) {
        $temp = sys_get_temp_dir();
        if (is_writable($temp)) {
            session_save_path($temp);
        }
    }
    @session_start();
}

/**
 * Generate CSRF token
 */
function get_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Admin authentication checks
 */
function is_admin_logged_in() {
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function require_admin_login($redirect_to = 'login.php') {
    if (!is_admin_logged_in()) {
        header("Location: $redirect_to");
        exit;
    }
}

/**
 * Flash messaging system
 */
function set_flash_message($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // 'success', 'error', 'info'
        'text' => $message
    ];
}

function get_flash_message() {
    if (!empty($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }
    return null;
}

/**
 * Resolve relative image asset path for admin or frontend
 */
function get_asset_url($path, $is_admin = false) {
    if (empty($path)) {
        return $is_admin ? '../images/collaboration.jpg' : 'images/collaboration.jpg';
    }
    if (preg_match('#^(https?:)?//#', $path)) {
        return $path;
    }
    $clean_path = ltrim($path, '/');
    return $is_admin ? ('../' . $clean_path) : $clean_path;
}

/**
 * Generate URL with relative path support
 */
function url($path = '', $is_nested = false) {
    $prefix = $is_nested ? '../' : '';
    if (empty($path) || $path === '/') {
        return $prefix . 'index.php';
    }
    
    $clean_path = ltrim($path, '/');
    if (strpos($clean_path, '.php') === false && strpos($clean_path, '.html') === false && strpos($clean_path, '#') === false) {
        $parts = explode('?', $clean_path, 2);
        $file = $parts[0] . '.php';
        $query = isset($parts[1]) ? '?' . $parts[1] : '';
        return $prefix . $file . $query;
    }
    
    return $prefix . $clean_path;
}

/**
 * Generate Asset URL
 */
function asset($path, $is_nested = false) {
    $prefix = $is_nested ? '../' : '';
    return $prefix . ltrim($path, '/');
}
