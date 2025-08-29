<?php
/**
 * Security and Performance Enhancements
 * 
 * @package TetradkataTheme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Remove WordPress Version from various outputs
 */
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');

// Remove version from scripts and styles
function tetradkata_remove_version_scripts_styles($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'tetradkata_remove_version_scripts_styles', 9999);
add_filter('script_loader_src', 'tetradkata_remove_version_scripts_styles', 9999);

/**
 * Remove unnecessary header information
 */
remove_action('wp_head', 'rsd_link'); // Remove RSD link
remove_action('wp_head', 'wlwmanifest_link'); // Remove Windows Live Writer
remove_action('wp_head', 'wp_shortlink_wp_head'); // Remove shortlink
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head'); // Remove adjacent posts
remove_action('wp_head', 'print_emoji_detection_script', 7); // Remove emoji scripts
remove_action('wp_print_styles', 'print_emoji_styles'); // Remove emoji styles
remove_action('wp_head', 'rest_output_link_wp_head'); // Remove REST API link
remove_action('wp_head', 'wp_oembed_add_discovery_links'); // Remove oEmbed
remove_action('template_redirect', 'rest_output_link_header', 11); // Remove REST API header

/**
 * Disable XML-RPC
 */
add_filter('xmlrpc_enabled', '__return_false');
add_filter('wp_headers', function($headers) {
    unset($headers['X-Pingback']);
    return $headers;
});

/**
 * Hide login errors (prevents username enumeration)
 */
function tetradkata_hide_login_errors() {
    return __('Login failed. Please check your credentials and try again.', 'tetradkata');
}
add_filter('login_errors', 'tetradkata_hide_login_errors');

/**
 * Disable user enumeration
 */
function tetradkata_disable_user_enumeration() {
    if (!is_admin() && !empty($_GET['author'])) {
        wp_safe_redirect(home_url(), 301);
        exit;
    }
}
add_action('init', 'tetradkata_disable_user_enumeration');

/**
 * Enhanced login security with rate limiting
 */
class Tetradkata_Login_Security {
    private $option_name = 'tetradkata_login_attempts';
    private $max_attempts = 5;
    private $lockout_duration = 900; // 15 minutes
    
    public function __construct() {
        add_action('wp_login_failed', array($this, 'handle_failed_login'));
        add_action('wp_login', array($this, 'handle_successful_login'), 10, 2);
        add_filter('authenticate', array($this, 'check_login_attempts'), 30, 3);
        add_action('init', array($this, 'cleanup_old_attempts'));
    }
    
    private function get_client_ip() {
        $ip_keys = array('HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                $ip = filter_var($_SERVER[$key], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
                if ($ip !== false) {
                    return $ip;
                }
            }
        }
        return '0.0.0.0';
    }
    
    public function handle_failed_login($username) {
        $ip = $this->get_client_ip();
        $attempts = get_option($this->option_name, array());
        
        if (!isset($attempts[$ip])) {
            $attempts[$ip] = array(
                'count' => 0,
                'first_attempt' => time(),
                'last_attempt' => time()
            );
        }
        
        $attempts[$ip]['count']++;
        $attempts[$ip]['last_attempt'] = time();
        
        update_option($this->option_name, $attempts);
        
        // Log suspicious activity
        if ($attempts[$ip]['count'] >= $this->max_attempts) {
            error_log(sprintf(
                'Tetradkata Security: IP %s blocked after %d failed login attempts',
                $ip,
                $attempts[$ip]['count']
            ));
        }
    }
    
    public function handle_successful_login($user_login, $user) {
        $ip = $this->get_client_ip();
        $attempts = get_option($this->option_name, array());
        
        if (isset($attempts[$ip])) {
            unset($attempts[$ip]);
            update_option($this->option_name, $attempts);
        }
    }
    
    public function check_login_attempts($user, $username, $password) {
        if (empty($username) || empty($password)) {
            return $user;
        }
        
        $ip = $this->get_client_ip();
        $attempts = get_option($this->option_name, array());
        
        if (isset($attempts[$ip])) {
            $time_passed = time() - $attempts[$ip]['last_attempt'];
            
            if ($attempts[$ip]['count'] >= $this->max_attempts && $time_passed < $this->lockout_duration) {
                $remaining = ceil(($this->lockout_duration - $time_passed) / 60);
                return new WP_Error('too_many_attempts', sprintf(
                    __('Too many failed login attempts. Please try again in %d minutes.', 'tetradkata'),
                    $remaining
                ));
            }
            
            // Reset if lockout period has passed
            if ($time_passed >= $this->lockout_duration) {
                unset($attempts[$ip]);
                update_option($this->option_name, $attempts);
            }
        }
        
        return $user;
    }
    
    public function cleanup_old_attempts() {
        if (!wp_doing_cron()) {
            return;
        }
        
        $attempts = get_option($this->option_name, array());
        $current_time = time();
        $updated = false;
        
        foreach ($attempts as $ip => $data) {
            if (($current_time - $data['last_attempt']) > 86400) { // 24 hours
                unset($attempts[$ip]);
                $updated = true;
            }
        }
        
        if ($updated) {
            update_option($this->option_name, $attempts);
        }
    }
}

// Initialize login security
new Tetradkata_Login_Security();

/**
 * Hide Admin Bar for non-admins
 */
add_action('after_setup_theme', function() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
});

/**
 * Disable file editing in admin
 */
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

/**
 * Security Headers
 */
function tetradkata_security_headers() {
    if (!is_admin()) {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        
        // Content Security Policy (adjust as needed)
        $csp = "default-src 'self'; ";
        $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval' *.googleapis.com *.gstatic.com *.google-analytics.com *.googletagmanager.com *.facebook.net cdn.jsdelivr.net; ";
        $csp .= "style-src 'self' 'unsafe-inline' *.googleapis.com cdn.jsdelivr.net; ";
        $csp .= "img-src 'self' data: *.googleapis.com *.gstatic.com *.google-analytics.com *.facebook.com *.googleusercontent.com; ";
        $csp .= "font-src 'self' data: *.googleapis.com *.gstatic.com cdn.jsdelivr.net; ";
        $csp .= "connect-src 'self' *.google-analytics.com *.googletagmanager.com *.facebook.com; ";
        $csp .= "frame-src 'self' *.youtube.com *.vimeo.com *.facebook.com; ";
        
        header('Content-Security-Policy: ' . $csp);
    }
}
add_action('send_headers', 'tetradkata_security_headers');

/**
 * Sanitize uploaded file names
 */
function tetradkata_sanitize_file_name($filename) {
    $info = pathinfo($filename);
    $ext = empty($info['extension']) ? '' : '.' . $info['extension'];
    $name = basename($filename, $ext);
    
    // Remove special characters
    $name = preg_replace('/[^a-zA-Z0-9\-\_]/', '-', $name);
    
    // Remove multiple dashes
    $name = preg_replace('/-+/', '-', $name);
    
    // Add timestamp to ensure uniqueness
    $name = $name . '-' . time();
    
    return $name . $ext;
}
add_filter('sanitize_file_name', 'tetradkata_sanitize_file_name', 10);

/**
 * Limit login attempts via cookies (additional layer)
 */
function tetradkata_check_cookie_attempts() {
    if (isset($_COOKIE['tetradkata_login_attempts'])) {
        $attempts = intval($_COOKIE['tetradkata_login_attempts']);
        if ($attempts >= 5) {
            wp_die(__('Too many login attempts. Please clear your cookies and try again.', 'tetradkata'));
        }
    }
}
add_action('login_init', 'tetradkata_check_cookie_attempts');

/**
 * Update cookie on failed login
 */
function tetradkata_update_cookie_attempts() {
    $attempts = isset($_COOKIE['tetradkata_login_attempts']) ? intval($_COOKIE['tetradkata_login_attempts']) : 0;
    $attempts++;
    setcookie('tetradkata_login_attempts', $attempts, time() + 900, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
}
add_action('wp_login_failed', 'tetradkata_update_cookie_attempts');

/**
 * Clear cookie on successful login
 */
function tetradkata_clear_cookie_attempts() {
    if (isset($_COOKIE['tetradkata_login_attempts'])) {
        setcookie('tetradkata_login_attempts', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
    }
}
add_action('wp_login', 'tetradkata_clear_cookie_attempts');

/**
 * Disable REST API for non-authenticated users (optional - uncomment if needed)
 */
// add_filter('rest_authentication_errors', function($result) {
//     if (!empty($result)) {
//         return $result;
//     }
//     if (!is_user_logged_in()) {
//         return new WP_Error('rest_not_logged_in', 'You are not currently logged in.', array('status' => 401));
//     }
//     return $result;
// });

/**
 * Change login URL (basic protection against bots)
 * Access login via: yoursite.com/wp-login.php?tetradkata=secure
 */
function tetradkata_custom_login_url() {
    if (isset($_GET['tetradkata']) && $_GET['tetradkata'] === 'secure') {
        // Allow access
    } else {
        // Uncomment to enable custom login URL protection
        // wp_safe_redirect(home_url());
        // exit;
    }
}
// add_action('login_init', 'tetradkata_custom_login_url');

/**
 * Log security events
 */
function tetradkata_log_security_event($event_type, $details = '') {
    if (!WP_DEBUG_LOG) {
        return;
    }
    
    $log_message = sprintf(
        '[%s] Tetradkata Security Event: %s | IP: %s | Details: %s',
        current_time('mysql'),
        $event_type,
        $_SERVER['REMOTE_ADDR'],
        $details
    );
    
    error_log($log_message);
}