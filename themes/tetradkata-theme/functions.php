<?php
/**
 * Tetradkata Theme Functions
 * 
 * @package TetradkataTheme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define theme version for cache busting
define('TETRADKATA_VERSION', '1.1.0');
define('TETRADKATA_THEME_DIR', get_template_directory());
define('TETRADKATA_THEME_URI', get_template_directory_uri());

/**
 * Theme Setup
 */
function tetradkata_theme_setup() {
    // Load theme textdomain for translations
    load_theme_textdomain('tetradkata', TETRADKATA_THEME_DIR . '/languages');
    
    // Add theme supports
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
        'style',
        'script',
    ));
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Основно меню', 'tetradkata'),
        'footer'  => __('Меню в долния колонтитул', 'tetradkata'),
    ));
}
add_action('after_setup_theme', 'tetradkata_theme_setup');

/**
 * Enqueue Scripts and Styles
 */
function tetradkata_scripts() {
    // Styles
    wp_enqueue_style('dashicons');
    wp_enqueue_style('tetradkata-style', get_stylesheet_uri(), array(), TETRADKATA_VERSION);
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css', array(), '8.0.0');
    wp_enqueue_style('tetradkata-custom', TETRADKATA_THEME_URI . '/assets/css/custom.css', array('tetradkata-style'), TETRADKATA_VERSION);
    
    // Page-specific styles
    if (is_page_template('page-checkout.php')) {
        wp_enqueue_style('tetradkata-checkout', TETRADKATA_THEME_URI . '/assets/css/page-checkout.css', array('tetradkata-style'), TETRADKATA_VERSION);
    }
    if (is_page_template('page-privacy.php')) {
        wp_enqueue_style('tetradkata-privacy', TETRADKATA_THEME_URI . '/assets/css/page-privacy.css', array('tetradkata-style'), TETRADKATA_VERSION);
    }
    if (is_page_template('page-terms.php')) {
        wp_enqueue_style('tetradkata-terms', TETRADKATA_THEME_URI . '/assets/css/page-terms.css', array('tetradkata-style'), TETRADKATA_VERSION);
    }
    if (is_single() && 'product' === get_post_type()) {
        wp_enqueue_style('tetradkata-single-product', TETRADKATA_THEME_URI . '/assets/css/single-product.css', array('tetradkata-style'), TETRADKATA_VERSION);
    }
    
    // Scripts
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array(), '8.0.0', true);
    wp_enqueue_script('tetradkata-scripts', TETRADKATA_THEME_URI . '/assets/js/scripts.js', array('jquery', 'swiper-js'), TETRADKATA_VERSION, true);
    
    // Localize script with secure data
    wp_localize_script('tetradkata-scripts', 'tetradkata_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('tetradkata_nonce'),
        'cart_url' => class_exists('WooCommerce') ? esc_url(wc_get_cart_url()) : '',
        'checkout_url' => class_exists('WooCommerce') ? esc_url(wc_get_checkout_url()) : '',
        'shop_url' => class_exists('WooCommerce') ? esc_url(get_permalink(wc_get_page_id('shop'))) : '',
        'wc_active' => class_exists('WooCommerce') ? 'yes' : 'no',
        'translations' => array(
            'loading' => __('Зарежда...', 'tetradkata'),
            'error' => __('Възникна грешка', 'tetradkata'),
            'added_to_cart' => __('Продуктът е добавен в количката!', 'tetradkata'),
            'removed_from_cart' => __('Продуктът е премахнат от количката', 'tetradkata'),
            'cart_empty' => __('Количката ви е празна', 'tetradkata'),
            'continue_shopping' => __('Продължи пазаруването', 'tetradkata'),
            'checkout' => __('Плащане', 'tetradkata'),
            'view_cart' => __('Виж количката', 'tetradkata'),
        ),
    ));
}
add_action('wp_enqueue_scripts', 'tetradkata_scripts');

/**
 * Bulgarian Translations for WooCommerce
 */
function tetradkata_load_translations() {
    $locale = determine_locale();
    
    // Load Bulgarian translations if needed
    if (strpos($locale, 'bg') !== false) {
        require_once TETRADKATA_THEME_DIR . '/inc/translations-bg.php';
    }
}
add_action('init', 'tetradkata_load_translations');

/**
 * Add custom checkout fields for Bulgarian addresses
 */
function tetradkata_add_checkout_fields($fields) {
    // Add Bulgarian-specific fields if needed
    $fields['billing']['billing_company_vat'] = array(
        'label'    => __('ЕИК/БУЛСТАТ (за фактура)', 'tetradkata'),
        'required' => false,
        'class'    => array('form-row-wide'),
        'clear'    => true,
        'type'     => 'text',
        'priority' => 35,
    );
    
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'tetradkata_add_checkout_fields');

/**
 * Override default country for checkout
 */
function tetradkata_change_default_checkout_country() {
    return 'BG'; // Bulgaria
}
add_filter('default_checkout_billing_country', 'tetradkata_change_default_checkout_country');
add_filter('default_checkout_shipping_country', 'tetradkata_change_default_checkout_country');

/**
 * Personalization Functions - Moved to separate file for better organization
 */
if (class_exists('WooCommerce')) {
    require_once TETRADKATA_THEME_DIR . '/inc/personalization.php';
}

/**
 * Register Widget Areas
 */
function tetradkata_widgets_init() {
    register_sidebar(array(
        'name'          => __('Област за джаджи в долния колонтитул 1', 'tetradkata'),
        'id'            => 'footer-1',
        'description'   => __('Добавете джаджи тук, за да се появят в долния колонтитул.', 'tetradkata'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Област за джаджи в долния колонтитул 2', 'tetradkata'),
        'id'            => 'footer-2',
        'description'   => __('Добавете джаджи тук, за да се появят в долния колонтитул.', 'tetradkata'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Област за джаджи в долния колонтитул 3', 'tetradkata'),
        'id'            => 'footer-3',
        'description'   => __('Добавете джаджи тук, за да се появят в долния колонтитул.', 'tetradkata'),
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
    if (class_exists('WooCommerce') && WC()->cart) {
        return absint(WC()->cart->get_cart_contents_count());
    }
    return 0;
}

/**
 * Safe get theme mod with proper escaping
 */
function tetradkata_get_theme_mod($mod_name, $default = '', $escape = 'esc_html') {
    $value = get_theme_mod($mod_name, $default);
    
    switch ($escape) {
        case 'esc_html':
            return esc_html($value);
        case 'esc_attr':
            return esc_attr($value);
        case 'esc_url':
            return esc_url($value);
        case 'esc_textarea':
            return esc_textarea($value);
        case 'wp_kses_post':
            return wp_kses_post($value);
        default:
            return esc_html($value);
    }
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
require_once TETRADKATA_THEME_DIR . '/inc/customizer.php';

/**
 * WooCommerce Functions
 */
if (class_exists('WooCommerce')) {
    require_once TETRADKATA_THEME_DIR . '/inc/woocommerce.php';
    require_once TETRADKATA_THEME_DIR . '/inc/ajax-cart.php';
}

/**
 * Custom Post Types - Should be in a plugin
 * Leaving here for compatibility but recommend moving to plugin
 */
require_once TETRADKATA_THEME_DIR . '/inc/post-types.php';

/**
 * Security and Performance
 */
require_once TETRADKATA_THEME_DIR . '/inc/security.php';

/**
 * Theme Initialization
 */
function tetradkata_init() {
    // Already loaded in theme_setup
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
 * Add to Cart Button Helper with proper escaping
 */
function tetradkata_add_to_cart_button($product, $classes = 'btn btn-primary', $text = '') {
    if (empty($text)) {
        $text = __('Добави в количката', 'tetradkata');
    }
    
    if (!$product || !$product->is_purchasable()) {
        return;
    }
    
    $product_id = absint($product->get_id());
    $product_name = esc_attr($product->get_name());
    $product_price = esc_attr($product->get_price());
    
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
                <span><?php esc_html_e('Добавя...', 'tetradkata'); ?></span>
            </span>
        </button>
        <?php
    } else {
        ?>
        <a href="<?php echo esc_url($product->get_permalink()); ?>" 
           class="<?php echo esc_attr(str_replace('btn-primary', 'btn-secondary', $classes)); ?>">
            <?php esc_html_e('Виж детайли', 'tetradkata'); ?>
        </a>
        <?php
    }
}

/**
 * Remove checkout login/coupon notices
 */
function tetradkata_remove_checkout_notices() {
    if (is_admin() || !is_checkout()) {
        return;
    }
    
    // Remove returning customer notice
    remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10);
}
add_action('init', 'tetradkata_remove_checkout_notices');

/**
 * Custom payment gateway labels with translations
 */
function tetradkata_payment_gateway_titles($title, $gateway_id) {
    $titles = array(
        'cod' => __('Наложен платеж', 'tetradkata'),
        'bacs' => __('Банков превод', 'tetradkata'),
        'viva' => __('Плащане с кредитна карта (Viva.com)', 'tetradkata'),
    );
    
    return isset($titles[$gateway_id]) ? $titles[$gateway_id] : $title;
}
add_filter('woocommerce_gateway_title', 'tetradkata_payment_gateway_titles', 10, 2);

/**
 * Custom payment gateway descriptions with translations
 */
function tetradkata_payment_gateway_descriptions($description, $gateway_id) {
    $descriptions = array(
        'cod' => __('Плащане в брой при доставка.', 'tetradkata'),
        'bacs' => __('Направете директен банков превод. Поръчката ще бъде обработена след получаване на плащането.', 'tetradkata'),
        'viva' => __('Платете сигурно с кредитна карта чрез Viva.com. Приемаме всички основни кредитни карти.', 'tetradkata'),
    );
    
    return isset($descriptions[$gateway_id]) ? $descriptions[$gateway_id] : $description;
}
add_filter('woocommerce_gateway_description', 'tetradkata_payment_gateway_descriptions', 10, 2);

/**
 * Change privacy policy text
 */
function tetradkata_checkout_privacy_policy_text($text) {
    $privacy_url = get_privacy_policy_url();
    if ($privacy_url) {
        return sprintf(
            __('Вашите лични данни ще бъдат използвани за обработка на поръчката и подобряване на вашето изживяване в този уебсайт. Прочетете нашата <a href="%s" target="_blank">политика за поверителност</a>.', 'tetradkata'),
            esc_url($privacy_url)
        );
    }
    return $text;
}
add_filter('woocommerce_checkout_privacy_policy_text', 'tetradkata_checkout_privacy_policy_text');

/**
 * Force Bulgarian locale for specific pages
 */
function tetradkata_force_locale($locale) {
    // Check if we should force Bulgarian locale
    $force_bg = get_option('tetradkata_force_bulgarian', false);
    
    if ($force_bg && (is_checkout() || is_cart() || is_account_page())) {
        return 'bg_BG';
    }
    
    return $locale;
}
add_filter('locale', 'tetradkata_force_locale');

/**
 * Display personalization in order item meta
 */
function tetradkata_display_order_item_personalization($item_id, $item, $order) {
    if ($order && method_exists($order, 'get_meta')) {
        $personalization_enabled = $order->get_meta('_personalization_enabled');
        $personalization_name = $order->get_meta('_personalization_name');
        
        if ($personalization_enabled === 'yes' && $personalization_name) {
            echo '<div class="personalization-info" style="margin-top: 10px; padding: 10px; background: #f5f5f5; border-radius: 5px;">';
            echo '<strong>' . esc_html__('Персонализация:', 'tetradkata') . '</strong> ' . esc_html($personalization_name);
            echo '</div>';
        }
    }
}
add_action('woocommerce_order_item_meta_end', 'tetradkata_display_order_item_personalization', 10, 3);

/**
 * Add theme options page (for future use)
 */
function tetradkata_add_theme_options() {
    add_theme_page(
        __('Настройки на Тетрадката', 'tetradkata'),
        __('Настройки на темата', 'tetradkata'),
        'manage_options',
        'tetradkata-options',
        'tetradkata_theme_options_page'
    );
}
add_action('admin_menu', 'tetradkata_add_theme_options');

function tetradkata_theme_options_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('tetradkata_options');
            do_settings_sections('tetradkata_options');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

?>