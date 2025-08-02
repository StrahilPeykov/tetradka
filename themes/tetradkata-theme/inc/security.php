<?php
/**
 * Security and Performance
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Remove WordPress Version
 */
remove_action('wp_head', 'wp_generator');

/**
 * Remove Unnecessary Features
 */
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_shortlink_wp_head');

/**
 * Disable XML-RPC
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Hide Admin Bar for Non-Admins
 */
if (!current_user_can('administrator') && !is_admin()) {
    show_admin_bar(false);
}

/**
 * Limit Login Attempts (Basic)
 */
function tetradkata_limit_login_attempts() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $attempts = get_transient('login_attempts_' . $ip);
    
    if ($attempts && $attempts >= 5) {
        wp_die('Too many failed login attempts. Please try again later.');
    }
}
add_action('wp_login_failed', 'tetradkata_limit_login_attempts');

function tetradkata_track_failed_login($username) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $attempts = get_transient('login_attempts_' . $ip) ?: 0;
    $attempts++;
    
    set_transient('login_attempts_' . $ip, $attempts, 15 * MINUTE_IN_SECONDS);
}
add_action('wp_login_failed', 'tetradkata_track_failed_login');

function tetradkata_clear_login_attempts($user_login) {
    $ip = $_SERVER['REMOTE_ADDR'];
    delete_transient('login_attempts_' . $ip);
}
add_action('wp_login', 'tetradkata_clear_login_attempts');
?>