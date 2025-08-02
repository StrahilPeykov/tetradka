<?php
/**
 * Custom Post Types
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Testimonials Post Type
 */
function tetradkata_testimonials_post_type() {
    $args = array(
        'public' => true,
        'label'  => 'Отзиви',
        'supports' => array('title', 'editor', 'thumbnail'),
        'menu_icon' => 'dashicons-format-quote',
        'has_archive' => true,
        'rewrite' => array('slug' => 'testimonials'),
    );
    register_post_type('testimonials', $args);
}
add_action('init', 'tetradkata_testimonials_post_type');
?>