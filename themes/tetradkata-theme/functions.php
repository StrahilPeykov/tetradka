<?php
/**
 * Tetradkata Theme Functions
 * 
 * @package TetradkataTheme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function tetradkata_theme_setup() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    
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
    wp_enqueue_style('dashicons');
    wp_enqueue_style('tetradkata-style', get_stylesheet_uri(), array(), '1.0.4');
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css', array(), '8.0.0');
    wp_enqueue_style('tetradkata-custom', get_template_directory_uri() . '/assets/css/custom.css', array('tetradkata-style'), '1.0.4');
    
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array(), '8.0.0', true);
    wp_enqueue_script('tetradkata-scripts', get_template_directory_uri() . '/assets/js/scripts.js', array('jquery', 'swiper-js'), '1.0.4', true);
    
    wp_localize_script('tetradkata-scripts', 'tetradkata_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('tetradkata_nonce'),
        'cart_url' => class_exists('WooCommerce') ? wc_get_cart_url() : '',
        'checkout_url' => class_exists('WooCommerce') ? wc_get_checkout_url() : '',
        'wc_active' => class_exists('WooCommerce') ? 'yes' : 'no',
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
 * Helper Functions
 */
function tetradkata_get_cart_count() {
    if (class_exists('WooCommerce')) {
        return WC()->cart->get_cart_contents_count();
    }
    return 0;
}

function tetradkata_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'tetradkata_excerpt_length');

function tetradkata_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'tetradkata_excerpt_more');

/**
 * Theme Customizer
 */
require_once get_template_directory() . '/inc/customizer.php';

/**
 * WooCommerce Functions
 */
if (class_exists('WooCommerce')) {
    require_once get_template_directory() . '/inc/woocommerce.php';
    require_once get_template_directory() . '/inc/ajax-cart.php';
}

/**
 * Custom Post Types
 */
require_once get_template_directory() . '/inc/post-types.php';

/**
 * Security and Performance
 */
require_once get_template_directory() . '/inc/security.php';

/**
 * Theme Initialization
 */
function tetradkata_init() {
    load_theme_textdomain('tetradkata', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'tetradkata_init');

if (!isset($content_width)) {
    $content_width = 1200;
}

/**
 * Body Classes
 */
function tetradkata_body_classes($classes) {
    if (class_exists('WooCommerce')) {
        if (is_shop() || is_product_category() || is_product_tag()) {
            $classes[] = 'tetradkata-shop-page';
        }
        if (is_product()) {
            $classes[] = 'tetradkata-single-product';
        }
        if (is_cart()) {
            $classes[] = 'tetradkata-cart-page';
        }
        if (is_checkout()) {
            $classes[] = 'tetradkata-checkout-page';
        }
    }
    return $classes;
}
add_filter('body_class', 'tetradkata_body_classes');

/**
 * Add to Cart Button Helper
 */
function tetradkata_add_to_cart_button($product, $classes = 'btn btn-primary', $text = 'Добави в количката') {
    if (!$product || !$product->is_purchasable()) {
        return;
    }
    
    $product_id = $product->get_id();
    $product_name = $product->get_name();
    $product_price = $product->get_price();
    
    if ($product->is_type('simple') && $product->is_in_stock()) {
        ?>
        <button class="<?php echo esc_attr($classes); ?> add-to-cart-btn" 
                data-product-id="<?php echo esc_attr($product_id); ?>"
                data-product-name="<?php echo esc_attr($product_name); ?>"
                data-product-price="<?php echo esc_attr($product_price); ?>"
                data-quantity="1">
            <span class="btn-text"><?php echo esc_html($text); ?></span>
            <span class="btn-loading" style="display: none;">
                <span class="loading"></span>
                <span>Добавя...</span>
            </span>
        </button>
        <?php
    } else {
        ?>
        <a href="<?php echo esc_url($product->get_permalink()); ?>" 
           class="<?php echo esc_attr(str_replace('btn-primary', 'btn-secondary', $classes)); ?>">
            Виж детайли
        </a>
        <?php
    }
}
?>