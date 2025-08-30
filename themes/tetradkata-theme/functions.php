<?php
/**
 * Tetradkata Theme Functions - Updated with Environment Management
 * 
 * @package TetradkataTheme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define theme version for cache busting
define('TETRADKATA_VERSION', '1.2.0');
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
    if (is_page_template('page-checkout.php')) {
        wp_enqueue_style('tetradkata-checkout', TETRADKATA_THEME_URI . '/assets/css/page-checkout.css', array('tetradkata-style'), $version);
    }
    if (is_page_template('page-privacy.php')) {
        wp_enqueue_style('tetradkata-privacy', TETRADKATA_THEME_URI . '/assets/css/page-privacy.css', array('tetradkata-style'), $version);
    }
    if (is_page_template('page-terms.php')) {
        wp_enqueue_style('tetradkata-terms', TETRADKATA_THEME_URI . '/assets/css/page-terms.css', array('tetradkata-style'), $version);
    }
    if (is_single() && 'product' === get_post_type()) {
        wp_enqueue_style('tetradkata-single-product', TETRADKATA_THEME_URI . '/assets/css/single-product.css', array('tetradkata-style'), $version);
    }
    
    // Scripts
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array(), '8.0.0', true);
    wp_enqueue_script('tetradkata-scripts', TETRADKATA_THEME_URI . '/assets/js/scripts.js', array('jquery', 'swiper-js'), $version, true);
    
    // Environment-specific scripts
    if ($env->get('analytics_enabled')) {
        // Only load analytics in production
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
 * THEME ACTIVATION SETUP - Auto-create required content
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
            'template' => 'page-privacy.php'
        ),
        'terms' => array(
            'title' => 'Общи условия',
            'content' => tetradkata_get_terms_content(),
            'template' => 'page-terms.php'
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
            
            if (isset($page_data['template']) && $page_id) {
                update_post_meta($page_id, '_wp_page_template', $page_data['template']);
            }
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
    
    // Set front page to home page if using page display
    $home_page = get_page_by_path('home') ?: get_page_by_title('Начало');
    if ($home_page) {
        update_option('page_on_front', $home_page->ID);
    }
    
    // Set permalink structure
    if (!get_option('permalink_structure')) {
        update_option('permalink_structure', '/%postname%/');
    }
}

/**
 * WooCommerce Setup with Environment Support
 */
function tetradkata_setup_woocommerce() {
    $env = Tetradkata_Environment::getInstance();
    
    // Basic WooCommerce settings
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
        'woocommerce_enable_ajax_add_to_cart' => 'yes'
    );
    
    foreach ($wc_settings as $key => $value) {
        if (get_option($key) === false) {
            update_option($key, $value);
        }
    }
    
    // Environment-specific WooCommerce setup
    tetradkata_setup_woocommerce_by_environment($env);
    
    // Set up shipping zones
    tetradkata_setup_shipping_zones();
    
    // Set up payment gateways
    tetradkata_setup_payment_gateways($env);
}

/**
 * Environment-specific WooCommerce Settings
 */
function tetradkata_setup_woocommerce_by_environment($env) {
    if ($env->get('payment_mode') === 'test') {
        // Test mode settings
        update_option('woocommerce_cheque_settings', array(
            'enabled' => 'yes',
            'title' => 'TEST: Тестово плащане',
            'description' => 'ТЕСТОВ РЕЖИМ - Не се извършва реално плащане'
        ));
    }
    
    // Inventory settings
    if ($env->is('production')) {
        update_option('woocommerce_manage_stock', 'yes');
        update_option('woocommerce_hold_stock_minutes', '60');
        update_option('woocommerce_notify_low_stock', 'yes');
        update_option('woocommerce_notify_no_stock', 'yes');
    }
    
    // Email settings
    if ($env->get('email_mode') !== 'live') {
        add_filter('wp_mail', function($args) {
            if (!is_admin()) {
                $args['to'] = get_option('admin_email');
                $args['subject'] = '[' . strtoupper($env->get_environment()) . '] ' . $args['subject'];
            }
            return $args;
        });
    }
}

/**
 * Setup Shipping Zones
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
    
    $zone->add_shipping_method('flat_rate');
    $zone->add_shipping_method('free_shipping');
    
    // Configure methods
    $methods = $zone->get_shipping_methods(false);
    foreach ($methods as $method) {
        if ($method->id === 'flat_rate') {
            $method->update_option('title', 'Стандартна доставка');
            $method->update_option('cost', '5.99');
        } elseif ($method->id === 'free_shipping') {
            $method->update_option('title', 'Безплатна доставка');
            $method->update_option('min_amount', '50');
        }
    }
}

/**
 * Setup Payment Gateways
 */
function tetradkata_setup_payment_gateways($env) {
    // Cash on Delivery
    update_option('woocommerce_cod_settings', array(
        'enabled' => 'yes',
        'title' => $env->get('payment_mode') === 'test' ? 'TEST: Наложен платеж' : 'Наложен платеж',
        'description' => 'Плащане в брой при доставка на продуктите.',
        'instructions' => 'Платете в брой при получаване на поръчката.'
    ));
    
    // Bank Transfer
    update_option('woocommerce_bacs_settings', array(
        'enabled' => 'yes',
        'title' => 'Банков превод',
        'description' => 'Направете директен банков превод към нашата банкова сметка.',
        'instructions' => 'Използвайте номера на поръчката като референция за плащането.'
    ));
}

/**
 * Production Deployment Tasks
 */
function tetradkata_production_deployment() {
    // Clear caches
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    
    // Clear debug logs
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
 * Load other includes
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
<h3>Събирана информация</h3>
<p>Събираме информация, която ни предоставяте директно, като име, имейл адрес и адрес за доставка.</p>
<h3>Използване на информацията</h3>
<p>Използваме вашата информация за обработка на поръчки и подобряване на нашите услуги.</p>';
}

function tetradkata_get_terms_content() {
    return '<h2>Общи условия</h2>
<p>Тези общи условия регулират използването на нашия уебсайт и закупуването на продукти.</p>
<h3>Условия за поръчки</h3>
<p>Всички поръчки са предмет на наличност и потвърждение от наша страна.</p>
<h3>Плащания</h3>
<p>Приемаме плащания чрез банков превод и наложен платеж.</p>';
}

function tetradkata_get_delivery_content() {
    return '<h2>Условия за доставка</h2>
<p>Доставяме във всички градове в България чрез куриерски фирми.</p>
<h3>Срокове за доставка</h3>
<p>Стандартната доставка е 1-3 работни дни. За персонализирани продукти срокът е 5-7 работни дни.</p>
<h3>Цени за доставка</h3>
<p>Доставката е безплатна при поръчки над 50 лв. Под тази сума таксата е 5.99 лв.</p>';
}

function tetradkata_get_returns_content() {
    return '<h2>Условия за връщане</h2>
<p>Имате право да върнете продукти в рамките на 14 дни от получаването им.</p>
<h3>Условия за връщане</h3>
<p>Продуктите трябва да бъдат в оригинално състояние и опаковка.</p>
<p><strong>Забележка:</strong> Персонализирани продукти не подлежат на връщане.</p>';
}

/**
 * Show deployment notices
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

if (!isset($content_width)) {
    $content_width = 1200;
}
?>