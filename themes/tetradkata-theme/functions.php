<?php
/**
 * Tetradkata Theme Functions - Production Ready Version
 * 
 * @package TetradkataTheme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define theme version and constants
define('TETRADKATA_VERSION', '1.3.0');
define('TETRADKATA_THEME_DIR', get_template_directory());
define('TETRADKATA_THEME_URI', get_template_directory_uri());

/**
 * Load Environment Management First
 */
require_once TETRADKATA_THEME_DIR . '/inc/environment.php';

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
    
    // Set image sizes
    add_image_size('product-thumbnail', 300, 300, true);
    add_image_size('product-single', 600, 600, true);
}
add_action('after_setup_theme', 'tetradkata_theme_setup');

/**
 * Enqueue Scripts and Styles with Environment Support
 */
function tetradkata_scripts() {
    $env = Tetradkata_Environment::getInstance();
    
    // Version for cache busting - use timestamp in development
    $version = $env->is('local') ? time() : TETRADKATA_VERSION;
    
    // Styles
    wp_enqueue_style('dashicons');
    wp_enqueue_style('tetradkata-style', get_stylesheet_uri(), array(), $version);
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css', array(), '8.0.0');
    wp_enqueue_style('tetradkata-custom', TETRADKATA_THEME_URI . '/assets/css/custom.css', array('tetradkata-style'), $version);
    
    // Page-specific styles
    if (is_page_template('page-checkout.php') || is_checkout()) {
        wp_enqueue_style('tetradkata-checkout', TETRADKATA_THEME_URI . '/assets/css/checkout.css', array('tetradkata-style'), $version);
    }
    if (is_cart()) {
        wp_enqueue_style('tetradkata-cart', TETRADKATA_THEME_URI . '/assets/css/cart.css', array('tetradkata-style'), $version);
    }
    if (is_shop() || is_product_category() || is_product_tag()) {
        wp_enqueue_style('tetradkata-shop', TETRADKATA_THEME_URI . '/assets/css/shop.css', array('tetradkata-style'), $version);
    }
    
    // Scripts
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array(), '8.0.0', true);
    wp_enqueue_script('tetradkata-scripts', TETRADKATA_THEME_URI . '/assets/js/scripts.js', array('jquery', 'swiper-js'), $version, true);
    
    // Environment-specific scripts
    if ($env->get('analytics_enabled')) {
        tetradkata_load_analytics();
    }
    
    // Localize script with secure data and environment info
    wp_localize_script('tetradkata-scripts', 'tetradkata_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('tetradkata_nonce'),
        'cart_url' => class_exists('WooCommerce') ? esc_url(wc_get_cart_url()) : '',
        'checkout_url' => class_exists('WooCommerce') ? esc_url(wc_get_checkout_url()) : '',
        'shop_url' => class_exists('WooCommerce') ? esc_url(get_permalink(wc_get_page_id('shop'))) : '',
        'wc_active' => class_exists('WooCommerce') ? 'yes' : 'no',
        'environment' => $env->get_environment(),
        'debug' => $env->get('debug') ? 'true' : 'false',
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
    
    // WooCommerce specific scripts
    if (class_exists('WooCommerce') && (is_checkout() || is_cart())) {
        // Add checkout specific parameters
        wp_localize_script('tetradkata-scripts', 'wc_checkout_params', array(
            'ajax_url' => WC()->ajax_url(),
            'wc_ajax_url' => WC_AJAX::get_endpoint("%%endpoint%%"),
            'update_order_review_nonce' => wp_create_nonce('update-order-review'),
            'apply_coupon_nonce' => wp_create_nonce('apply-coupon'),
            'remove_coupon_nonce' => wp_create_nonce('remove-coupon'),
            'option_guest_checkout' => get_option('woocommerce_enable_guest_checkout'),
            'checkout_url' => WC_AJAX::get_endpoint("checkout"),
            'is_checkout' => is_checkout() && empty($wp->query_vars['order-pay']) && !isset($wp->query_vars['order-received']) ? 1 : 0,
            'debug_mode' => $env->get('debug'),
            'i18n_checkout_error' => esc_attr__('Възникна грешка при обработката на поръчката. Моля, опитайте отново.', 'tetradkata'),
        ));
    }
}
add_action('wp_enqueue_scripts', 'tetradkata_scripts');

/**
 * Load Analytics (Production Only)
 */
function tetradkata_load_analytics() {
    // Google Analytics 4
    $ga_id = get_option('tetradkata_ga4_id');
    if ($ga_id) {
        wp_enqueue_script('google-analytics', "https://www.googletagmanager.com/gtag/js?id={$ga_id}", array(), null, false);
        wp_add_inline_script('google-analytics', "
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{$ga_id}');
        ");
    }
    
    // Facebook Pixel
    $fb_pixel_id = get_option('tetradkata_fb_pixel_id');
    if ($fb_pixel_id) {
        wp_add_inline_script('tetradkata-scripts', "
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{$fb_pixel_id}');
            fbq('track', 'PageView');
        ");
    }
}

/**
 * THEME ACTIVATION SETUP
 */
function tetradkata_theme_activation() {
    // Mark deployment
    Tetradkata_Deployment::mark_deployed();
    
    // Create required pages
    tetradkata_create_required_pages();
    
    // Create default menu
    tetradkata_create_default_menu();
    
    // Set default customizer values
    tetradkata_set_default_customizer_values();
    
    // Set up WordPress settings
    tetradkata_setup_wordpress_settings();
    
    // Set up WooCommerce if active
    if (class_exists('WooCommerce')) {
        tetradkata_setup_woocommerce();
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Run health check
    $issues = Tetradkata_Deployment::check_deployment_health();
    
    if (empty($issues)) {
        set_transient('tetradkata_deployment_success', true, 300);
    } else {
        set_transient('tetradkata_deployment_issues', $issues, 300);
    }
    
    // Environment-specific setup
    $env = Tetradkata_Environment::getInstance();
    if ($env->is('production')) {
        tetradkata_production_deployment();
    }
}
add_action('after_switch_theme', 'tetradkata_theme_activation');

/**
 * Create Required Pages
 */
function tetradkata_create_required_pages() {
    $pages = array(
        'privacy-policy' => array(
            'title' => 'Политика за поверителност',
            'content' => tetradkata_get_privacy_policy_content(),
        ),
        'terms' => array(
            'title' => 'Общи условия',
            'content' => tetradkata_get_terms_content(),
        ),
        'delivery' => array(
            'title' => 'Доставка',
            'content' => tetradkata_get_delivery_content()
        ),
        'returns' => array(
            'title' => 'Връщания',
            'content' => tetradkata_get_returns_content()
        )
    );

    foreach ($pages as $slug => $page_data) {
        $existing_page = get_page_by_path($slug);
        
        if (!$existing_page) {
            $page_id = wp_insert_post(array(
                'post_title' => $page_data['title'],
                'post_content' => $page_data['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => $slug
            ));
        }
    }
    
    // Set privacy policy page
    $privacy_page = get_page_by_path('privacy-policy');
    if ($privacy_page) {
        update_option('wp_page_for_privacy_policy', $privacy_page->ID);
    }
}

/**
 * Create Default Menu
 */
function tetradkata_create_default_menu() {
    $menu_name = 'Основно меню';
    $menu_exists = wp_get_nav_menu_object($menu_name);
    
    if (!$menu_exists) {
        $menu_id = wp_create_nav_menu($menu_name);
        
        $menu_items = array(
            array(
                'title' => 'Начало',
                'url' => home_url('/#home'),
                'menu_order' => 1
            ),
            array(
                'title' => 'Магазин',
                'url' => class_exists('WooCommerce') ? get_permalink(wc_get_page_id('shop')) : home_url('/#shop'),
                'menu_order' => 2
            ),
            array(
                'title' => 'За нас',
                'url' => home_url('/#about'),
                'menu_order' => 3
            ),
            array(
                'title' => 'Често задавани въпроси',
                'url' => home_url('/#faq'),
                'menu_order' => 4
            )
        );
        
        foreach ($menu_items as $item) {
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => $item['title'],
                'menu-item-url' => $item['url'],
                'menu-item-status' => 'publish',
                'menu-item-type' => 'custom',
                'menu-item-position' => $item['menu_order']
            ));
        }
        
        // Assign menu to location
        $locations = get_theme_mod('nav_menu_locations');
        $locations['primary'] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);
    }
}

/**
 * Set Default Customizer Values
 */
function tetradkata_set_default_customizer_values() {
    $defaults = array(
        'hero_title' => 'Тетрадката',
        'hero_subtitle' => 'Колекция от спомени',
        'hero_description' => 'Личният ви дневник за пътешествия, спомени и вдъхновение – събрани в една тетрадка.',
        'contact_phone' => '+359 888 123 456',
        'contact_email' => 'thenotebook.sales@gmail.com'
    );
    
    foreach ($defaults as $key => $value) {
        if (!get_theme_mod($key)) {
            set_theme_mod($key, $value);
        }
    }
}

/**
 * WordPress Settings Setup
 */
function tetradkata_setup_wordpress_settings() {
    $env = Tetradkata_Environment::getInstance();
    
    $wp_settings = array(
        'blogname' => 'Тетрадката',
        'blogdescription' => 'Личният ви дневник за пътешествия, спомени и вдъхновение',
        'date_format' => 'd.m.Y',
        'time_format' => 'H:i',
        'timezone_string' => 'Europe/Sofia',
        'start_of_week' => '1',
        'show_on_front' => 'page',
        'default_ping_status' => 'closed',
        'default_comment_status' => 'closed',
        'users_can_register' => '0'
    );
    
    foreach ($wp_settings as $key => $value) {
        if (get_option($key) === false) {
            update_option($key, $value);
        }
    }
    
    // Set search engine visibility based on environment
    update_option('blog_public', $env->get('search_engines') ? '1' : '0');
    
    // Set permalink structure
    if (!get_option('permalink_structure')) {
        update_option('permalink_structure', '/%postname%/');
    }
}

/**
 * WooCommerce Setup with Environment Support
 */
function tetradkata_setup_woocommerce() {
    if (!class_exists('WooCommerce')) {
        return;
    }
    
    $env = Tetradkata_Environment::getInstance();
    
    // Basic WooCommerce settings for Bulgaria
    $wc_settings = array(
        'woocommerce_store_address' => 'бул. Възраждане 30, вх. 2, ап. 33',
        'woocommerce_store_city' => 'Варна',
        'woocommerce_store_postcode' => '9000',
        'woocommerce_default_country' => 'BG',
        'woocommerce_currency' => 'BGN',
        'woocommerce_price_thousand_sep' => ' ',
        'woocommerce_price_decimal_sep' => ',',
        'woocommerce_price_num_decimals' => '2',
        'woocommerce_currency_pos' => 'right_space',
        'woocommerce_enable_guest_checkout' => 'yes',
        'woocommerce_enable_checkout_login_reminder' => 'no',
        'woocommerce_enable_signup_and_login_from_checkout' => 'yes',
        'woocommerce_cart_redirect_after_add' => 'no',
        'woocommerce_enable_ajax_add_to_cart' => 'yes',
        'woocommerce_checkout_highlight_required_fields' => 'yes',
        'woocommerce_checkout_terms_and_conditions_checkbox_text' => 'Прочел/а съм и се съгласявам с {terms_link} и {privacy_link}',
        
        // Inventory settings
        'woocommerce_manage_stock' => 'yes',
        'woocommerce_hold_stock_minutes' => '60',
        'woocommerce_notify_low_stock' => 'yes',
        'woocommerce_notify_no_stock' => 'yes',
        'woocommerce_stock_format' => 'no_amount',
        
        // Email settings
        'woocommerce_email_from_name' => 'Тетрадката',
        'woocommerce_email_from_address' => 'noreply@' . parse_url(home_url(), PHP_URL_HOST),
        
        // Additional settings
        'woocommerce_registration_privacy_policy_text' => 'Вашите лични данни ще бъдат използвани в съответствие с нашата {privacy_policy_link}.',
        'woocommerce_checkout_privacy_policy_text' => 'Вашите лични данни ще бъдат използвани за обработка на поръчката в съответствие с нашата {privacy_policy_link}.',
        
        // Disable reviews by default (can be enabled later)
        'woocommerce_enable_reviews' => 'no',
        'woocommerce_review_rating_required' => 'yes',
        'woocommerce_review_rating_verification_required' => 'no',
    );
    
    foreach ($wc_settings as $key => $value) {
        update_option($key, $value);
    }
    
    // Set up shipping zones for Bulgaria only
    tetradkata_setup_shipping_zones();
    
    // Set up payment gateways
    tetradkata_setup_payment_gateways($env);
    
    // Create WooCommerce pages if they don't exist
    WC_Install::create_pages();
    
    // Set up email templates
    tetradkata_setup_email_templates();
}

/**
 * Setup Shipping Zones for Bulgaria Only
 */
function tetradkata_setup_shipping_zones() {
    if (!class_exists('WC_Shipping_Zones')) {
        return;
    }
    
    $zones = WC_Shipping_Zones::get_zones();
    if (!empty($zones)) {
        return; // Already configured
    }
    
    // Create Bulgaria shipping zone
    $zone = new WC_Shipping_Zone();
    $zone->set_zone_name('България');
    $zone->set_zone_order(1);
    $zone->save();
    
    $zone->add_location('BG', 'country');
    
    // Add shipping methods
    $flat_rate_id = $zone->add_shipping_method('flat_rate');
    $free_shipping_id = $zone->add_shipping_method('free_shipping');
    
    // Configure flat rate
    $flat_rate = WC_Shipping_Zones::get_shipping_method($flat_rate_id);
    if ($flat_rate) {
        $flat_rate->update_option('title', 'Стандартна доставка');
        $flat_rate->update_option('cost', '5.99');
        $flat_rate->update_option('calculation_type', 'order');
    }
    
    // Configure free shipping
    $free_shipping = WC_Shipping_Zones::get_shipping_method($free_shipping_id);
    if ($free_shipping) {
        $free_shipping->update_option('title', 'Безплатна доставка');
        $free_shipping->update_option('min_amount', '50');
        $free_shipping->update_option('requires', 'min_amount');
    }
}

/**
 * Setup Payment Gateways
 */
function tetradkata_setup_payment_gateways($env) {
    // Cash on Delivery
    update_option('woocommerce_cod_settings', array(
        'enabled' => 'yes',
        'title' => 'Наложен платеж',
        'description' => 'Плащане в брой при доставка на продуктите.',
        'instructions' => 'Платете в брой при получаване на поръчката. Включена е такса за наложен платеж от 2.90 лв.',
        'enable_for_methods' => array(),
        'enable_for_virtual' => 'no'
    ));
    
    // Bank Transfer
    update_option('woocommerce_bacs_settings', array(
        'enabled' => 'yes',
        'title' => 'Банков превод',
        'description' => 'Направете директен банков превод към нашата банкова сметка.',
        'instructions' => 'Направете банков превод на посочената сметка. Поръчката ви ще бъде обработена след получаване на плащането.',
        'account_details' => array(
            array(
                'account_name' => 'Тетрадката',
                'account_number' => 'BG18 UNCR 7630 1000 1234 56',
                'bank_name' => 'Уникредит Булбанк',
                'sort_code' => '',
                'iban' => 'BG18 UNCR 7630 1000 1234 56',
                'bic' => 'UNCRBGSF'
            )
        )
    ));
    
    // Disable other payment methods
    update_option('woocommerce_cheque_settings', array('enabled' => 'no'));
    update_option('woocommerce_paypal_settings', array('enabled' => 'no'));
}

/**
 * Setup Email Templates
 */
function tetradkata_setup_email_templates() {
    // Customize email settings
    $email_settings = array(
        'woocommerce_email_header_image' => '',
        'woocommerce_email_footer_text' => 'Тетрадката - Личният ви дневник за пътешествия, спомени и вдъхновение',
        'woocommerce_email_base_color' => '#B6813A',
        'woocommerce_email_background_color' => '#f7f7f7',
        'woocommerce_email_body_background_color' => '#ffffff',
        'woocommerce_email_text_color' => '#3c3c3c',
    );
    
    foreach ($email_settings as $key => $value) {
        update_option($key, $value);
    }
}

/**
 * Production Deployment Tasks
 */
function tetradkata_production_deployment() {
    // Clear caches
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    
    // Clear debug logs in production
    $log_files = array(
        WP_CONTENT_DIR . '/debug.log',
        WP_CONTENT_DIR . '/wp-errors.log'
    );
    
    foreach ($log_files as $log_file) {
        if (file_exists($log_file) && filesize($log_file) > 1024 * 1024) { // Clear if > 1MB
            file_put_contents($log_file, '');
        }
    }
    
    // Send deployment notification
    wp_mail(
        get_option('admin_email'),
        'Tetradkata Production Deployment Successful',
        sprintf(
            "Tetradkata theme v%s has been deployed successfully to production.\n\nDeployment Time: %s\nEnvironment: Production\n\nPlease verify all functionality is working correctly.",
            TETRADKATA_VERSION,
            current_time('mysql')
        )
    );
}

/**
 * Load Theme Includes
 */
function tetradkata_load_includes() {
    $includes = array(
        '/inc/customizer.php',
        '/inc/post-types.php', 
        '/inc/security.php',
        '/inc/translations-bg.php'
    );
    
    if (class_exists('WooCommerce')) {
        $includes[] = '/inc/woocommerce.php';
        $includes[] = '/inc/ajax-cart.php';
        $includes[] = '/inc/checkout-handler.php';  // Add checkout handler
        $includes[] = '/inc/personalization.php';
    }
    
    foreach ($includes as $include) {
        $file = TETRADKATA_THEME_DIR . $include;
        if (file_exists($file)) {
            require_once $file;
        }
    }
}
tetradkata_load_includes();

/**
 * Register Widget Areas
 */
function tetradkata_widgets_init() {
    register_sidebar(array(
        'name'          => __('Footer Widget Area 1', 'tetradkata'),
        'id'            => 'footer-1',
        'description'   => __('Widgets for footer area 1', 'tetradkata'),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'tetradkata_widgets_init');

/**
 * WooCommerce Customizations
 */

// Remove WooCommerce default styles
add_filter('woocommerce_enqueue_styles', '__return_false');

// Shop Layout
function tetradkata_loop_columns() {
    return 3;
}
add_filter('loop_shop_columns', 'tetradkata_loop_columns');

function tetradkata_products_per_page() {
    return 12;
}
add_filter('loop_shop_per_page', 'tetradkata_products_per_page');

// Currency Symbol for BGN
function tetradkata_currency_symbol($currency_symbol, $currency) {
    switch ($currency) {
        case 'BGN':
            $currency_symbol = 'лв.';
            break;
    }
    return $currency_symbol;
}
add_filter('woocommerce_currency_symbol', 'tetradkata_currency_symbol', 10, 2);

// Price Formatting
add_filter('woocommerce_get_price_thousand_separator', function() { return ' '; });
add_filter('woocommerce_get_price_decimal_separator', function() { return ','; });

// Custom price display for shop pages
function tetradkata_get_regular_price_html($product) {
    if (!$product || !is_object($product)) {
        return '';
    }
    
    $regular_price = $product->get_regular_price();
    
    if (empty($regular_price)) {
        return '';
    }
    
    return wc_price($regular_price);
}

function tetradkata_custom_price_html($price_html, $product) {
    // Only apply this to shop/category pages, not single product pages
    if (is_product()) {
        return $price_html;
    }
    
    return tetradkata_get_regular_price_html($product);
}
add_filter('woocommerce_get_price_html', 'tetradkata_custom_price_html', 10, 2);

// Product placeholder image
function tetradkata_custom_woocommerce_placeholder_img_src($src) {
    return get_template_directory_uri() . '/assets/images/product-placeholder.jpg';
}
add_filter('woocommerce_placeholder_img_src', 'tetradkata_custom_woocommerce_placeholder_img_src');

// Remove WooCommerce breadcrumbs
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

// Modify checkout fields for Bulgaria only
function tetradkata_checkout_fields($fields) {
    // Remove unnecessary fields
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_state']);
    unset($fields['shipping']['shipping_company']);
    unset($fields['shipping']['shipping_address_2']);
    unset($fields['shipping']['shipping_state']);
    
    // Set Bulgaria as default and hide country field
    $fields['billing']['billing_country']['default'] = 'BG';
    $fields['billing']['billing_country']['type'] = 'hidden';
    $fields['shipping']['shipping_country']['default'] = 'BG';
    $fields['shipping']['shipping_country']['type'] = 'hidden';
    
    // Modify field labels and placeholders
    $fields['billing']['billing_first_name']['placeholder'] = 'Иван';
    $fields['billing']['billing_last_name']['placeholder'] = 'Петров';
    $fields['billing']['billing_city']['placeholder'] = 'София';
    $fields['billing']['billing_postcode']['placeholder'] = '1000';
    $fields['billing']['billing_address_1']['placeholder'] = 'ул. Витоша 1';
    $fields['billing']['billing_phone']['placeholder'] = '+359 888 123 456';
    $fields['billing']['billing_email']['placeholder'] = 'ivan@example.com';
    
    // Make phone required
    $fields['billing']['billing_phone']['required'] = true;
    
    // Reorder fields
    $fields['billing']['billing_first_name']['priority'] = 10;
    $fields['billing']['billing_last_name']['priority'] = 20;
    $fields['billing']['billing_email']['priority'] = 30;
    $fields['billing']['billing_phone']['priority'] = 40;
    $fields['billing']['billing_city']['priority'] = 50;
    $fields['billing']['billing_postcode']['priority'] = 60;
    $fields['billing']['billing_address_1']['priority'] = 70;
    
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'tetradkata_checkout_fields');

// Add COD fee
function tetradkata_add_cod_fee() {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }
    
    $chosen_payment_method = WC()->session->get('chosen_payment_method');
    
    if ($chosen_payment_method === 'cod') {
        WC()->cart->add_fee('Такса наложен платеж', 2.90);
    }
}
add_action('woocommerce_cart_calculate_fees', 'tetradkata_add_cod_fee');

/**
 * Helper Functions
 */
function tetradkata_get_cart_count() {
    if (class_exists('WooCommerce') && WC()->cart) {
        return absint(WC()->cart->get_cart_contents_count());
    }
    return 0;
}

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
 * Body Classes with Environment Info
 */
function tetradkata_body_classes($classes) {
    $env = Tetradkata_Environment::getInstance();
    
    // Add environment class
    $classes[] = 'env-' . $env->get_environment();
    
    // Add WooCommerce classes
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
 * Content Templates for Auto-Created Pages
 */
function tetradkata_get_privacy_policy_content() {
    return '<h2>Политика за поверителност</h2>
<p>Тази политика за поверителност описва как събираме, използваме и защитаваме вашата лична информация.</p>

<h3>1. Събирана информация</h3>
<p>Събираме информация, която ни предоставяте директно при поръчване:</p>
<ul>
<li>Име и фамилия</li>
<li>Имейл адрес</li>
<li>Телефонен номер</li>
<li>Адрес за доставка</li>
<li>Данни за плащане (обработвани сигурно)</li>
</ul>

<h3>2. Използване на информацията</h3>
<p>Използваме вашата информация за:</p>
<ul>
<li>Обработка и изпълнение на поръчки</li>
<li>Комуникация относно вашата поръчка</li>
<li>Подобряване на нашите услуги</li>
<li>Изпращане на важни актуализации (по желание)</li>
</ul>

<h3>3. Защита на данните</h3>
<p>Предприемаме всички необходими мерки за защита на вашите данни и не ги споделяме с трети страни без ваше съгласие.</p>

<h3>4. Вашите права</h3>
<p>Имате право да заявите достъп, поправка или изтриване на вашите лични данни.</p>

<h3>5. Контакт</h3>
<p>За въпроси относно тази политика се свържете с нас на: thenotebook.sales@gmail.com</p>';
}

function tetradkata_get_terms_content() {
    return '<h2>Общи условия</h2>
<p>Тези общи условия регулират използването на нашия уебсайт и закупуването на продукти.</p>

<h3>1. Условия за поръчки</h3>
<p>Всички поръчки са предмет на наличност и потвърждение от наша страна. Запазваме си правото да отменим поръчка в случай на грешка в цената или липса на наличност.</p>

<h3>2. Цени и плащания</h3>
<p>Всички цени са в български лева (BGN) с включен ДДС. Приемаме следните методи за плащане:</p>
<ul>
<li>Банков превод</li>
<li>Наложен платеж (с такса 2.90 лв.)</li>
</ul>

<h3>3. Доставка</h3>
<p>Доставяме само в територията на България. Стандартната доставка е 5.99 лв., безплатна за поръчки над 50 лв.</p>

<h3>4. Връщания</h3>
<p>Имате право да върнете продукти в рамките на 14 дни от получаването им в оригинално състояние. Персонализирани продукти НЕ подлежат на връщане.</p>

<h3>5. Гаранция</h3>
<p>Предлагаме гаранция за качество на всички наши продукти. При дефект ще заменим или възстановим продукта.</p>

<h3>6. Контакт</h3>
<p>За въпроси се свържете с нас на: thenotebook.sales@gmail.com или +359 888 123 456</p>';
}

function tetradkata_get_delivery_content() {
    return '<h2>Условия за доставка</h2>

<h3>Зони за доставка</h3>
<p>Доставяме само в територията на Република България чрез куриерски фирми.</p>

<h3>Срокове за доставка</h3>
<ul>
<li><strong>Стандартни продукти:</strong> 1-3 работни дни</li>
<li><strong>Персонализирани продукти:</strong> 5-7 работни дни</li>
</ul>

<h3>Цени за доставка</h3>
<ul>
<li><strong>Стандартна доставка:</strong> 5.99 лв.</li>
<li><strong>Безплатна доставка:</strong> при поръчки над 50 лв.</li>
</ul>

<h3>Начин на доставка</h3>
<p>Продуктите се доставят чрез куриерска фирма до адреса, посочен от вас при поръчването.</p>

<h3>Получаване на пратката</h3>
<p>При получаване на пратката, моля проверете състоянието ѝ. Ако има повреди, отбележете това в документа на куриера.</p>

<h3>Контакт</h3>
<p>За въпроси относно доставката: thenotebook.sales@gmail.com</p>';
}

function tetradkata_get_returns_content() {
    return '<h2>Условия за връщане</h2>

<h3>Право на връщане</h3>
<p>Имате право да върнете продукти в рамките на 14 дни от получаването им, без да посочвате причина.</p>

<h3>Условия за връщане</h3>
<ul>
<li>Продуктите трябва да бъдат в оригинално състояние</li>
<li>В оригинална опаковка</li>
<li>Без следи от употреба</li>
<li>Със всички придружаващи материали</li>
</ul>

<h3>Изключения</h3>
<p><strong>⚠️ ВАЖНО:</strong> Персонализирани продукти НЕ подлежат на връщане, тъй като са изработени специално според вашите изисквания.</p>

<h3>Процедура за връщане</h3>
<ol>
<li>Свържете се с нас на thenotebook.sales@gmail.com</li>
<li>Посочете номера на поръчката и причината за връщането</li>
<li>Ще получите инструкции за връщане</li>
<li>Изпратете продукта на посочения адрес</li>
</ol>

<h3>Възстановяване на средства</h3>
<p>След получаване и проверка на върнатия продукт, ще възстановим заплатената сума в рамките на 14 работни дни.</p>

<h3>Разходи за връщане</h3>
<p>Разходите за връщане на продукта са за ваша сметка, освен ако грешката не е наша.</p>';
}

/**
 * Show deployment notices in admin
 */
function tetradkata_deployment_notices() {
    if (get_transient('tetradkata_deployment_success')) {
        echo '<div class="notice notice-success is-dismissible">
            <p><strong>Успешна активация!</strong> Тетрадката тема е конфигурирана автоматично. Проверете <a href="' . admin_url('customize.php') . '">персонализирането</a> за допълнителни настройки.</p>
        </div>';
        delete_transient('tetradkata_deployment_success');
    }
    
    if ($issues = get_transient('tetradkata_deployment_issues')) {
        echo '<div class="notice notice-warning is-dismissible">
            <p><strong>Тема активирана с предупреждения:</strong></p>
            <ul>';
        foreach ($issues as $issue) {
            echo '<li>' . esc_html($issue) . '</li>';
        }
        echo '</ul>
        </div>';
        delete_transient('tetradkata_deployment_issues');
    }
}
add_action('admin_notices', 'tetradkata_deployment_notices');

/**
 * Disable file editing in production
 */
if (!defined('DISALLOW_FILE_EDIT')) {
    $env = Tetradkata_Environment::getInstance();
    if ($env->is('production')) {
        define('DISALLOW_FILE_EDIT', true);
    }
}

/**
 * Set content width
 */
if (!isset($content_width)) {
    $content_width = 1200;
}

/**
 * Admin-only functions
 */
if (is_admin()) {
    /**
     * Add theme options to admin menu
     */
    function tetradkata_add_admin_menu() {
        add_theme_page(
            'Настройки на темата',
            'Тетрадката',
            'manage_options',
            'tetradkata-settings',
            'tetradkata_admin_page'
        );
    }
    add_action('admin_menu', 'tetradkata_add_admin_menu');
    
    /**
     * Theme admin page
     */
    function tetradkata_admin_page() {
        $env = Tetradkata_Environment::getInstance();
        $deployment_info = Tetradkata_Deployment::get_deployment_info();
        ?>
        <div class="wrap">
            <h1>Настройки на Тетрадката</h1>
            
            <div class="notice notice-info">
                <p><strong>Среда:</strong> <?php echo strtoupper($env->get_environment()); ?></p>
                <?php if ($deployment_info): ?>
                <p><strong>Версия:</strong> <?php echo esc_html($deployment_info['version']); ?> 
                   (активирана на <?php echo esc_html($deployment_info['deployed_at']); ?>)</p>
                <?php endif; ?>
            </div>
            
            <div class="card">
                <h2>Бързи линкове</h2>
                <p><a href="<?php echo admin_url('customize.php'); ?>" class="button button-primary">Персонализиране на темата</a></p>
                
                <?php if (class_exists('WooCommerce')): ?>
                <p><a href="<?php echo admin_url('admin.php?page=wc-settings'); ?>" class="button">Настройки на WooCommerce</a></p>
                <p><a href="<?php echo admin_url('edit.php?post_type=product'); ?>" class="button">Управление на продукти</a></p>
                <p><a href="<?php echo admin_url('edit.php?post_type=shop_order'); ?>" class="button">Поръчки</a></p>
                <?php endif; ?>
            </div>
            
            <div class="card">
                <h2>Системна информация</h2>
                <table class="widefat">
                    <tr><td><strong>WordPress:</strong></td><td><?php echo get_bloginfo('version'); ?></td></tr>
                    <tr><td><strong>PHP:</strong></td><td><?php echo PHP_VERSION; ?></td></tr>
                    <tr><td><strong>Тема:</strong></td><td><?php echo wp_get_theme()->get('Version'); ?></td></tr>
                    <?php if (class_exists('WooCommerce')): ?>
                    <tr><td><strong>WooCommerce:</strong></td><td><?php echo WC()->version; ?></td></tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        <?php
    }
}
?>