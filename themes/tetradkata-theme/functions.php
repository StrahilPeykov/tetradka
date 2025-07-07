<?php
/**
 * Tetradkata Theme Functions
 * 
 * @package TetradkataTheme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function tetradkata_theme_setup() {
    // Add theme support for WooCommerce
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    
    // Add theme support for post thumbnails
    add_theme_support('post-thumbnails');
    
    // Add theme support for title tag
    add_theme_support('title-tag');
    
    // Add theme support for HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // Add theme support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'tetradkata'),
        'footer'  => __('Footer Menu', 'tetradkata'),
    ));
}
add_action('after_setup_theme', 'tetradkata_theme_setup');

/**
 * Enqueue Scripts and Styles
 */
function tetradkata_scripts() {
    // Enqueue main stylesheet
    wp_enqueue_style('tetradkata-style', get_stylesheet_uri(), array(), '1.0.0');
    
    // Enqueue Swiper CSS for carousel
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css', array(), '8.0.0');
    
    // Enqueue custom CSS
    wp_enqueue_style('tetradkata-custom', get_template_directory_uri() . '/assets/css/custom.css', array('tetradkata-style'), '1.0.0');
    
    // Enqueue Swiper JS
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array(), '8.0.0', true);
    
    // Enqueue custom JavaScript
    wp_enqueue_script('tetradkata-scripts', get_template_directory_uri() . '/assets/js/scripts.js', array('jquery', 'swiper-js'), '1.0.0', true);
    
    // Localize script for AJAX
    wp_localize_script('tetradkata-scripts', 'tetradkata_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('tetradkata_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'tetradkata_scripts');

/**
 * Register Widget Areas
 */
function tetradkata_widgets_init() {
    register_sidebar(array(
        'name'          => __('Footer Widget Area 1', 'tetradkata'),
        'id'            => 'footer-1',
        'description'   => __('Add widgets here to appear in your footer.', 'tetradkata'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Widget Area 2', 'tetradkata'),
        'id'            => 'footer-2',
        'description'   => __('Add widgets here to appear in your footer.', 'tetradkata'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Widget Area 3', 'tetradkata'),
        'id'            => 'footer-3',
        'description'   => __('Add widgets here to appear in your footer.', 'tetradkata'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'tetradkata_widgets_init');

/**
 * Customize WooCommerce
 */

// Remove WooCommerce default styles
add_filter('woocommerce_enqueue_styles', '__return_false');

// Change number of products per row
function tetradkata_loop_columns() {
    return 3;
}
add_filter('loop_shop_columns', 'tetradkata_loop_columns');

// Change number of products per page
function tetradkata_products_per_page() {
    return 6;
}
add_filter('loop_shop_per_page', 'tetradkata_products_per_page');

// Customize Add to Cart button text
function tetradkata_add_to_cart_text() {
    return __('Добави в количката', 'tetradkata');
}
add_filter('woocommerce_product_add_to_cart_text', 'tetradkata_add_to_cart_text');
add_filter('woocommerce_product_single_add_to_cart_text', 'tetradkata_add_to_cart_text');

// Custom WooCommerce currency symbol for BGN
function tetradkata_currency_symbol($currency_symbol, $currency) {
    switch ($currency) {
        case 'BGN':
            $currency_symbol = 'лв.';
            break;
    }
    return $currency_symbol;
}
add_filter('woocommerce_currency_symbol', 'tetradkata_currency_symbol', 10, 2);

/**
 * Custom Post Types and Fields
 */

// Register custom post type for testimonials
function tetradkata_testimonials_post_type() {
    $args = array(
        'public' => true,
        'label'  => 'Отзиви',
        'supports' => array('title', 'editor', 'thumbnail'),
        'menu_icon' => 'dashicons-format-quote',
    );
    register_post_type('testimonials', $args);
}
add_action('init', 'tetradkata_testimonials_post_type');

/**
 * Theme Customizer
 */
function tetradkata_customize_register($wp_customize) {
    // Add hero section
    $wp_customize->add_section('hero_section', array(
        'title' => __('Hero Section', 'tetradkata'),
        'priority' => 30,
    ));
    
    // Hero title
    $wp_customize->add_setting('hero_title', array(
        'default' => 'Тетрадката',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hero_title', array(
        'label' => __('Hero Title', 'tetradkata'),
        'section' => 'hero_section',
        'type' => 'text',
    ));
    
    // Hero subtitle
    $wp_customize->add_setting('hero_subtitle', array(
        'default' => 'Колекция от спомени',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hero_subtitle', array(
        'label' => __('Hero Subtitle', 'tetradkata'),
        'section' => 'hero_section',
        'type' => 'text',
    ));
    
    // Hero description
    $wp_customize->add_setting('hero_description', array(
        'default' => 'Личният ви дневник за пътешествия, спомени и вдъхновение – събрани в една тетрадка.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('hero_description', array(
        'label' => __('Hero Description', 'tetradkata'),
        'section' => 'hero_section',
        'type' => 'textarea',
    ));
    
    // Contact section
    $wp_customize->add_section('contact_section', array(
        'title' => __('Contact Information', 'tetradkata'),
        'priority' => 40,
    ));
    
    // Phone
    $wp_customize->add_setting('contact_phone', array(
        'default' => '+359 888 123 456',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('contact_phone', array(
        'label' => __('Phone', 'tetradkata'),
        'section' => 'contact_section',
        'type' => 'text',
    ));
    
    // Email
    $wp_customize->add_setting('contact_email', array(
        'default' => 'info@tetradkata.bg',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('contact_email', array(
        'label' => __('Email', 'tetradkata'),
        'section' => 'contact_section',
        'type' => 'email',
    ));
}
add_action('customize_register', 'tetradkata_customize_register');

/**
 * AJAX Handlers
 */

// Add to cart AJAX handler
function tetradkata_ajax_add_to_cart() {
    if (!wp_verify_nonce($_POST['nonce'], 'tetradkata_nonce')) {
        wp_die();
    }
    
    $product_id = absint($_POST['product_id']);
    $quantity = absint($_POST['quantity']);
    
    $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity);
    
    if ($cart_item_key) {
        wp_send_json_success(array(
            'message' => 'Продуктът е добавен в количката',
            'cart_count' => WC()->cart->get_cart_contents_count(),
        ));
    } else {
        wp_send_json_error(array(
            'message' => 'Възникна грешка при добавяне на продукта',
        ));
    }
}
add_action('wp_ajax_tetradkata_add_to_cart', 'tetradkata_ajax_add_to_cart');
add_action('wp_ajax_nopriv_tetradkata_add_to_cart', 'tetradkata_ajax_add_to_cart');

/**
 * Helper Functions
 */

// Get cart count
function tetradkata_get_cart_count() {
    if (class_exists('WooCommerce')) {
        return WC()->cart->get_cart_contents_count();
    }
    return 0;
}

// Get cart total
function tetradkata_get_cart_total() {
    if (class_exists('WooCommerce')) {
        return WC()->cart->get_cart_total();
    }
    return '';
}

// Custom excerpt length
function tetradkata_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'tetradkata_excerpt_length');

// Custom excerpt more
function tetradkata_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'tetradkata_excerpt_more');

/**
 * Security and Performance
 */

// Remove WordPress version from head
remove_action('wp_head', 'wp_generator');

// Remove unnecessary WordPress features
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_shortlink_wp_head');

// Disable XML-RPC
add_filter('xmlrpc_enabled', '__return_false');

// Hide WordPress admin bar for non-admins
if (!current_user_can('administrator') && !is_admin()) {
    show_admin_bar(false);
}

/**
 * WooCommerce Product Settings
 */

// Set default product image placeholder
function tetradkata_custom_woocommerce_placeholder_img_src($src) {
    return get_template_directory_uri() . '/assets/images/product-placeholder.jpg';
}
add_filter('woocommerce_placeholder_img_src', 'tetradkata_custom_woocommerce_placeholder_img_src');

// Configure Bulgarian locale for WooCommerce
function tetradkata_woocommerce_bulgarian_locale($locale) {
    $locale['currency_pos'] = 'right_space';
    $locale['thousand_sep'] = ' ';
    $locale['decimal_sep'] = ',';
    $locale['num_decimals'] = 2;
    return $locale;
}
add_filter('woocommerce_get_price_thousand_separator', function() { return ' '; });
add_filter('woocommerce_get_price_decimal_separator', function() { return ','; });

/**
 * Custom Meta Boxes
 */

// Add custom meta box for featured products
function tetradkata_add_featured_meta_box() {
    add_meta_box(
        'tetradkata_featured',
        'Препоръчан продукт',
        'tetradkata_featured_meta_box_callback',
        'product',
        'side'
    );
}
add_action('add_meta_boxes', 'tetradkata_add_featured_meta_box');

function tetradkata_featured_meta_box_callback($post) {
    wp_nonce_field('tetradkata_featured_nonce', 'tetradkata_featured_nonce');
    $value = get_post_meta($post->ID, '_tetradkata_featured', true);
    echo '<label for="tetradkata_featured">';
    echo '<input type="checkbox" id="tetradkata_featured" name="tetradkata_featured" value="1" ' . checked(1, $value, false) . '>';
    echo ' Маркирай като препоръчан продукт</label>';
}

function tetradkata_save_featured_meta_box($post_id) {
    if (!wp_verify_nonce($_POST['tetradkata_featured_nonce'], 'tetradkata_featured_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (isset($_POST['tetradkata_featured'])) {
        update_post_meta($post_id, '_tetradkata_featured', 1);
    } else {
        delete_post_meta($post_id, '_tetradkata_featured');
    }
}
add_action('save_post', 'tetradkata_save_featured_meta_box');

/**
 * Initialize theme after plugins are loaded
 */
function tetradkata_init() {
    // Load text domain for translations
    load_theme_textdomain('tetradkata', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'tetradkata_init');

// Set content width
if (!isset($content_width)) {
    $content_width = 1200;
}
?>