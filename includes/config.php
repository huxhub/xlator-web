<?php
/**
 * Xlator - Configuration & Helper Functions
 * Optimized for Hostinger PHP Hosting
 */

// Error reporting (Set to 0 in production on Hostinger)
ini_set('display_errors', '0');
error_reporting(E_ALL);

/**
 * Load environment variables from .env file if available
 *
 * @param string $filePath
 * @return void
 */
if (!function_exists('load_dotenv')) {
    function load_dotenv(string $filePath): void {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return;
        }
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0) {
                continue;
            }
            if (strpos($line, '=') !== false) {
                list($key, $val) = explode('=', $line, 2);
                $key = trim($key);
                $val = trim($val);
                // Strip surrounding single or double quotes
                if ((substr($val, 0, 1) === '"' && substr($val, -1) === '"') ||
                    (substr($val, 0, 1) === "'" && substr($val, -1) === "'")) {
                    $val = substr($val, 1, -1);
                }
                putenv("$key=$val");
                $_ENV[$key] = $val;
                $_SERVER[$key] = $val;
            }
        }
    }
}

// Load .env
load_dotenv(__DIR__ . '/../.env');

/**
 * Helper to fetch environment variable with fallback
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed {
        $val = getenv($key);
        if ($val !== false) {
            return $val;
        }
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}

// Site constants
define('SITE_NAME', env('SITE_NAME', 'Xlator'));
define('SITE_TAGLINE', env('SITE_TAGLINE', "Brands Aren't Born. They're Built Together."));
define('ADMIN_EMAIL', env('ADMIN_EMAIL', 'hello@xlator.com'));
define('DATA_DIR', __DIR__ . '/../data');

// Admin credentials loaded from environment (.env)
define('ADMIN_USERNAME', env('ADMIN_USERNAME', 'xlator'));
define('ADMIN_PASSWORD', env('ADMIN_PASSWORD', ''));
define('ADMIN_PASSWORD_HASH', env('ADMIN_PASSWORD_HASH', ''));

// Upload limits (configurable via .env: MAX_UPLOAD_SIZE=5242880 or MAX_UPLOAD_SIZE_MB=5)
define('MAX_UPLOAD_SIZE', (int)env('MAX_UPLOAD_SIZE', 5 * 1024 * 1024)); // 5MB default
define('MAX_UPLOAD_SIZE_MB', (int)env('MAX_UPLOAD_SIZE_MB', 5));

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
 *
 * @return string
 */
function get_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 *
 * @param string|null $token
 * @return bool
 */
function verify_csrf_token(?string $token): bool {
    return !empty($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Admin authentication checks
 *
 * @return bool
 */
function is_admin_logged_in(): bool {
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * @param string $redirect_to
 * @return void
 */
function require_admin_login(string $redirect_to = '/admin/login.php'): void {
    if (!is_admin_logged_in()) {
        header("Location: $redirect_to");
        exit;
    }
}

/**
 * Flash messaging system
 *
 * @param string $type
 * @param string $message
 * @return void
 */
function set_flash_message(string $type, string $message): void {
    $_SESSION['flash_message'] = [
        'type' => $type, // 'success', 'error', 'info'
        'text' => $message
    ];
}

/**
 * @return array<string, string>|null
 */
function get_flash_message(): ?array {
    if (!empty($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }
    return null;
}

/**
 * Resolve image asset path for admin or frontend
 *
 * @param string|null $path
 * @param bool $is_admin
 * @return string
 */
function get_asset_url(?string $path, bool $is_admin = false): string {
    if (empty($path)) {
        return '/images/collaboration.jpg';
    }
    if (preg_match('#^(https?:)?//#', $path)) {
        return $path;
    }
    $clean_path = ltrim($path, '/');
    return '/' . $clean_path;
}

/**
 * Generate URL with relative path support
 *
 * @param string $path
 * @param bool $is_nested
 * @return string
 */
function url(string $path = '', bool $is_nested = false): string {
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
 *
 * @param string $path
 * @param bool $is_nested
 * @return string
 */
function asset(string $path, bool $is_nested = false): string {
    $prefix = $is_nested ? '../' : '';
    return $prefix . ltrim($path, '/');
}
