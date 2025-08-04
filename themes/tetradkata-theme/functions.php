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
 * Bulgarian Translations for WooCommerce
 */
function tetradkata_translate_woocommerce_strings($translation, $text, $domain) {
    if ($domain === 'woocommerce') {
        $translations = array(
            // Checkout page
            'Billing details' => 'Данни за фактуриране',
            'Shipping details' => 'Данни за доставка',
            'Additional information' => 'Допълнителна информация',
            'Your order' => 'Вашата поръчка',
            
            // Form fields
            'First name' => 'Име',
            'Last name' => 'Фамилия',
            'Company name' => 'Име на фирма',
            'Company name (optional)' => 'Име на фирма (незадължително)',
            'Country / Region' => 'Държава',
            'Street address' => 'Адрес',
            'Apartment, suite, unit, etc. (optional)' => 'Апартамент, етаж, вход и др. (незадължително)',
            'Town / City' => 'Град',
            'State / County' => 'Област',
            'State / County (optional)' => 'Област (незадължително)',
            'Postcode / ZIP' => 'Пощенски код',
            'Phone' => 'Телефон',
            'Phone (optional)' => 'Телефон (незадължително)',
            'Email address' => 'Имейл адрес',
            'Order notes (optional)' => 'Забележки към поръчката (незадължително)',
            
            // Table headers
            'Product' => 'Продукт',
            'Subtotal' => 'Междинна сума',
            'Total' => 'Общо',
            'Quantity' => 'Количество',
            
            // Payment
            'Payment method' => 'Начин на плащане',
            'Place order' => 'Завърши поръчката',
            'Cash on delivery' => 'Наложен платеж',
            'Pay with cash upon delivery.' => 'Плащане при доставка на продуктите.',
            'Direct bank transfer' => 'Банков превод',
            'Bank transfer' => 'Банков превод',
            
            // Shipping
            'Ship to a different address?' => 'Доставка на различен адрес?',
            'Shipping' => 'Доставка',
            'Free shipping' => 'Безплатна доставка',
            
            // Messages
            'Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our privacy policy.' => 'Вашите лични данни ще бъдат използвани за обработка на поръчката, подобряване на вашето изживяване в този уебсайт и за други цели, описани в нашата политика за поверителност.',
            
            // Validation messages
            'Please enter a valid email address.' => 'Моля, въведете валиден имейл адрес.',
            'This field is required.' => 'Това поле е задължително.',
            'Please select a valid option.' => 'Моля, изберете валидна опция.',
            'Please enter a valid phone number.' => 'Моля, въведете валиден телефонен номер.',
            
            // Cart/Account
            'My account' => 'Моят профил',
            'Dashboard' => 'Табло',
            'Orders' => 'Поръчки',
            'Downloads' => 'Изтегляния',
            'Addresses' => 'Адреси',
            'Account details' => 'Данни за профила',
            'Logout' => 'Изход',
            'Login' => 'Вход',
            'Register' => 'Регистрация',
            'Lost password' => 'Забравена парола',
            'View cart' => 'Виж количката',
            'Checkout' => 'Плащане',
            
            // Product page
            'In stock' => 'В наличност',
            'Out of stock' => 'Няма в наличност',
            'Add to cart' => 'Добави в количката',
            'Select options' => 'Избери опции',
            'Read more' => 'Научи повече',
            'Description' => 'Описание',
            'Additional information' => 'Допълнителна информация',
            'Reviews' => 'Отзиви',
            
            // Shop page
            'Sort by popularity' => 'Подреди по популярност',
            'Sort by average rating' => 'Подреди по оценка',
            'Sort by latest' => 'Подреди по дата',
            'Sort by price: low to high' => 'Подреди по цена: ниска към висока',
            'Sort by price: high to low' => 'Подреди по цена: висока към ниска',
            'Default sorting' => 'Стандартно подреждане',
            
            // Countries and states
            'Select a country / region…' => 'Изберете държава...',
            'Select an option…' => 'Изберете опция...',
            'Bulgaria' => 'България',
            
            // Other common text
            'Required' => 'Задължително',
            'Optional' => 'Незадължително',
            'Update' => 'Обнови',
            'Apply' => 'Приложи',
            'Remove' => 'Премахни',
            'Continue' => 'Продължи',
            'Back' => 'Назад',
            'Next' => 'Напред',
        );
        
        if (isset($translations[$text])) {
            return $translations[$text];
        }
    }
    
    return $translation;
}
add_filter('gettext', 'tetradkata_translate_woocommerce_strings', 20, 3);

/**
 * Translate more WooCommerce text
 */
function tetradkata_translate_woocommerce_strings_with_context($translation, $text, $context, $domain) {
    if ($domain === 'woocommerce') {
        $translations = array(
            'Billing details' => 'Данни за фактуриране',
            'Ship to a different address?' => 'Доставка на различен адрес?',
            'Create an account?' => 'Създаване на профил?',
            'Order notes' => 'Забележки към поръчката',
            'Notes about your order, e.g. special notes for delivery.' => 'Забележки към поръчката, напр. специални инструкции за доставка.',
        );
        
        if (isset($translations[$text])) {
            return $translations[$text];
        }
    }
    
    return $translation;
}
add_filter('gettext_with_context', 'tetradkata_translate_woocommerce_strings_with_context', 20, 4);

/**
 * Custom checkout field labels
 */
function tetradkata_override_checkout_fields($fields) {
    // Billing fields
    $fields['billing']['billing_first_name']['label'] = 'Име';
    $fields['billing']['billing_last_name']['label'] = 'Фамилия';
    $fields['billing']['billing_company']['label'] = 'Име на фирма (незадължително)';
    $fields['billing']['billing_country']['label'] = 'Държава';
    $fields['billing']['billing_address_1']['label'] = 'Адрес';
    $fields['billing']['billing_address_2']['label'] = 'Апартамент, етаж, вход и др. (незадължително)';
    $fields['billing']['billing_city']['label'] = 'Град';
    $fields['billing']['billing_state']['label'] = 'Област';
    $fields['billing']['billing_postcode']['label'] = 'Пощенски код';
    $fields['billing']['billing_phone']['label'] = 'Телефон';
    $fields['billing']['billing_email']['label'] = 'Имейл адрес';
    
    // Shipping fields
    $fields['shipping']['shipping_first_name']['label'] = 'Име';
    $fields['shipping']['shipping_last_name']['label'] = 'Фамилия';
    $fields['shipping']['shipping_company']['label'] = 'Име на фирма (незадължително)';
    $fields['shipping']['shipping_country']['label'] = 'Държава';
    $fields['shipping']['shipping_address_1']['label'] = 'Адрес';
    $fields['shipping']['shipping_address_2']['label'] = 'Апартамент, етаж, вход и др. (незадължително)';
    $fields['shipping']['shipping_city']['label'] = 'Град';
    $fields['shipping']['shipping_state']['label'] = 'Област';
    $fields['shipping']['shipping_postcode']['label'] = 'Пощенски код';
    
    // Order notes
    $fields['order']['order_comments']['label'] = 'Забележки към поръчката';
    $fields['order']['order_comments']['placeholder'] = 'Забележки към поръчката, напр. специални инструкции за доставка.';
    
    // Placeholders
    $fields['billing']['billing_first_name']['placeholder'] = 'Име';
    $fields['billing']['billing_last_name']['placeholder'] = 'Фамилия';
    $fields['billing']['billing_email']['placeholder'] = 'example@email.com';
    $fields['billing']['billing_phone']['placeholder'] = '+359 888 123 456';
    $fields['billing']['billing_address_1']['placeholder'] = 'ул. Име на улица, No';
    $fields['billing']['billing_city']['placeholder'] = 'София';
    $fields['billing']['billing_postcode']['placeholder'] = '1000';
    
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'tetradkata_override_checkout_fields');

/**
 * Remove coupon functionality completely
 */
remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
add_filter('woocommerce_coupons_enabled', '__return_false');

/**
 * Customize checkout button text
 */
function tetradkata_place_order_button_text($button_text) {
    return 'Завърши поръчката';
}
add_filter('woocommerce_order_button_text', 'tetradkata_place_order_button_text');

/**
 * Override default country
 */
function tetradkata_change_default_checkout_country() {
    return 'BG'; // Bulgaria
}
add_filter('default_checkout_billing_country', 'tetradkata_change_default_checkout_country');
add_filter('default_checkout_shipping_country', 'tetradkata_change_default_checkout_country');

/**
 * Personalization Functions
 */

// Add personalization fee via AJAX
function tetradkata_add_personalization_fee() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tetradkata_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed'));
        return;
    }
    
    WC()->session->set('add_personalization', true);
    
    wp_send_json_success(array('message' => 'Personalization fee added'));
}
add_action('wp_ajax_add_personalization_fee', 'tetradkata_add_personalization_fee');
add_action('wp_ajax_nopriv_add_personalization_fee', 'tetradkata_add_personalization_fee');

// Remove personalization fee via AJAX
function tetradkata_remove_personalization_fee() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tetradkata_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed'));
        return;
    }
    
    WC()->session->set('add_personalization', false);
    
    wp_send_json_success(array('message' => 'Personalization fee removed'));
}
add_action('wp_ajax_remove_personalization_fee', 'tetradkata_remove_personalization_fee');
add_action('wp_ajax_nopriv_remove_personalization_fee', 'tetradkata_remove_personalization_fee');

// Add personalization fee to cart
function tetradkata_add_cart_personalization_fee($cart) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }
    
    if (!WC()->session) {
        return;
    }
    
    $add_personalization = WC()->session->get('add_personalization');
    
    if ($add_personalization) {
        $fee = 5.00; // 5 BGN
        $cart->add_fee('Персонализация', $fee, true);
    }
}
add_action('woocommerce_cart_calculate_fees', 'tetradkata_add_cart_personalization_fee');

// Save personalization data with order
function tetradkata_save_personalization_data($order_id) {
    if (isset($_POST['add_personalization']) && $_POST['add_personalization'] === 'yes') {
        update_post_meta($order_id, '_personalization_enabled', 'yes');
        
        if (isset($_POST['personalization_name'])) {
            $personalization_name = sanitize_text_field($_POST['personalization_name']);
            update_post_meta($order_id, '_personalization_name', $personalization_name);
        }
    }
}
add_action('woocommerce_checkout_update_order_meta', 'tetradkata_save_personalization_data');

// Display personalization info in admin order page
function tetradkata_display_admin_order_personalization($order) {
    $personalization_enabled = get_post_meta($order->get_id(), '_personalization_enabled', true);
    
    if ($personalization_enabled === 'yes') {
        $personalization_name = get_post_meta($order->get_id(), '_personalization_name', true);
        ?>
        <div class="order_data_column" style="width: 100%; margin-top: 20px;">
            <h3>Персонализация</h3>
            <p><strong>Име за корицата:</strong> <?php echo esc_html($personalization_name); ?></p>
        </div>
        <?php
    }
}
add_action('woocommerce_admin_order_data_after_order_details', 'tetradkata_display_admin_order_personalization');

// Add personalization info to order emails
function tetradkata_add_personalization_to_emails($order, $sent_to_admin, $plain_text, $email) {
    $personalization_enabled = get_post_meta($order->get_id(), '_personalization_enabled', true);
    
    if ($personalization_enabled === 'yes') {
        $personalization_name = get_post_meta($order->get_id(), '_personalization_name', true);
        
        if ($plain_text) {
            echo "\n\nПерсонализация:\n";
            echo "Име за корицата: " . $personalization_name . "\n";
        } else {
            echo '<h2>Персонализация</h2>';
            echo '<p><strong>Име за корицата:</strong> ' . esc_html($personalization_name) . '</p>';
        }
    }
}
add_action('woocommerce_email_order_meta', 'tetradkata_add_personalization_to_emails', 10, 4);

// Disable COD when personalization is selected
function tetradkata_disable_cod_for_personalization($available_gateways) {
    if (is_admin()) {
        return $available_gateways;
    }
    
    if (WC()->session && WC()->session->get('add_personalization')) {
        if (isset($available_gateways['cod'])) {
            unset($available_gateways['cod']);
        }
    }
    
    return $available_gateways;
}
add_filter('woocommerce_available_payment_gateways', 'tetradkata_disable_cod_for_personalization');

/**
 * Enqueue Scripts and Styles
 */
function tetradkata_scripts() {
    wp_enqueue_style('dashicons');
    wp_enqueue_style('tetradkata-style', get_stylesheet_uri(), array(), '1.0.5');
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css', array(), '8.0.0');
    wp_enqueue_style('tetradkata-custom', get_template_directory_uri() . '/assets/css/custom.css', array('tetradkata-style'), '1.0.5');
    
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array(), '8.0.0', true);
    wp_enqueue_script('tetradkata-scripts', get_template_directory_uri() . '/assets/js/scripts.js', array('jquery', 'swiper-js'), '1.0.5', true);
    
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
 * Add custom checkout fields for Bulgarian addresses
 */
function tetradkata_add_checkout_fields($fields) {
    // Add Bulgarian-specific fields if needed
    $fields['billing']['billing_company_vat'] = array(
        'label'    => 'ЕИК/БУЛСТАТ (при поискана фактура)',
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
 * Customize state options for Bulgaria
 */
function tetradkata_custom_states($states) {
    $states['BG'] = array(
        'BLG' => 'Благоевград',
        'BGS' => 'Бургас',
        'DOB' => 'Добрич',
        'GAB' => 'Габрово',
        'HAS' => 'Хасково',
        'KRZ' => 'Кърджали',
        'KUS' => 'Кюстендил',
        'LOV' => 'Ловеч',
        'MON' => 'Монтана',
        'PAZ' => 'Пазарджик',
        'PER' => 'Перник',
        'PVN' => 'Плевен',
        'PDV' => 'Пловдив',
        'RAZ' => 'Разград',
        'RSE' => 'Русе',
        'SHU' => 'Шумен',
        'SLS' => 'Силистра',
        'SLV' => 'Сливен',
        'SMO' => 'Смолян',
        'SFO' => 'София област',
        'SOF' => 'София град',
        'SZR' => 'Стара Загора',
        'TGV' => 'Търговище',
        'VAR' => 'Варна',
        'VTR' => 'Велико Търново',
        'VID' => 'Видин',
        'VRC' => 'Враца',
        'YAM' => 'Ямбол',
    );
    
    return $states;
}
add_filter('woocommerce_states', 'tetradkata_custom_states');

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
 * Custom payment gateway labels
 */
function tetradkata_payment_gateway_titles($title, $gateway_id) {
    switch ($gateway_id) {
        case 'cod':
            return 'Наложен платеж';
        case 'bacs':
            return 'Банков превод';
        case 'viva':
            return 'Плащане с карта (Viva.com)';
        default:
            return $title;
    }
}
add_filter('woocommerce_gateway_title', 'tetradkata_payment_gateway_titles', 10, 2);

/**
 * Custom payment gateway descriptions
 */
function tetradkata_payment_gateway_descriptions($description, $gateway_id) {
    switch ($gateway_id) {
        case 'cod':
            return 'Плащане при доставка на продуктите.';
        case 'bacs':
            return 'Направете директен банков превод. Поръчката ще бъде обработена след получаване на плащането.';
        case 'viva':
            return 'Платете безопасно с карта чрез Viva.com. Приемаме всички основни банкови карти.';
        default:
            return $description;
    }
}
add_filter('woocommerce_gateway_description', 'tetradkata_payment_gateway_descriptions', 10, 2);

/**
 * Change privacy policy text
 */
function tetradkata_checkout_privacy_policy_text($text) {
    return 'Вашите лични данни ще бъдат използвани за обработка на поръчката и подобряване на вашето изживяване в този уебсайт. Прочетете нашата <a href="/privacy-policy" target="_blank">политика за поверителност</a>.';
}
add_filter('woocommerce_checkout_privacy_policy_text', 'tetradkata_checkout_privacy_policy_text');

/**
 * Add custom CSS for checkout improvements
 */
function tetradkata_checkout_inline_styles() {
    if (is_checkout()) {
        ?>
        <style>
        /* Hide English text and improve checkout styling */
        .woocommerce-checkout h3:has-text("Billing details"),
        .woocommerce-checkout h3:contains("Billing details") {
            display: none !important;
        }
        
        /* Ensure privacy policy text is styled properly */
        .woocommerce-privacy-policy-text {
            font-size: 12px;
            color: var(--charcoal);
            opacity: 0.7;
            line-height: 1.4;
            margin-top: 15px;
            padding: 15px;
            background: var(--paper-bg);
            border-radius: 8px;
            border-left: 3px solid var(--gold-start);
        }
        
        .woocommerce-privacy-policy-text a {
            color: var(--gold-start);
            text-decoration: underline;
        }
        
        /* Better field styling */
        .woocommerce-checkout .form-row .required {
            color: #e74c3c;
        }
        
        .woocommerce-checkout .validate-required.woocommerce-invalid input {
            border-color: #e74c3c !important;
            box-shadow: 0 0 0 2px rgba(231, 76, 60, 0.2);
        }
        
        /* Hide coupon related elements */
        .woocommerce-form-coupon-toggle,
        .checkout_coupon,
        .woocommerce-remove-coupon {
            display: none !important;
        }
        
        /* Better order review styling */
        .woocommerce-checkout-review-order-table .product-name {
            font-weight: 600;
        }
        
        .woocommerce-checkout-review-order-table .product-total {
            text-align: right;
            font-weight: 600;
        }
        
        /* Loading overlay for form submission */
        .woocommerce-checkout.processing {
            pointer-events: none;
            position: relative;
        }
        
        .woocommerce-checkout.processing::before {
            content: 'Обработва поръчката...';
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 999999;
            background: var(--white);
            padding: 25px 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            font-weight: 600;
            color: var(--charcoal);
            font-size: 16px;
        }
        
        .woocommerce-checkout.processing::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(248, 246, 244, 0.9);
            z-index: 999998;
            backdrop-filter: blur(3px);
        }
        </style>
        <?php
    }
}
add_action('wp_head', 'tetradkata_checkout_inline_styles');

/**
 * Force WooCommerce to use Bulgarian locale
 */
function tetradkata_force_bulgarian_locale($locale) {
    if (is_checkout() || is_cart() || is_account_page()) {
        return 'bg_BG';
    }
    return $locale;
}
add_filter('locale', 'tetradkata_force_bulgarian_locale');

/**
 * Clear personalization session data after order is placed
 */
function tetradkata_clear_personalization_session($order_id) {
    if (WC()->session) {
        WC()->session->set('add_personalization', false);
    }
}
add_action('woocommerce_thankyou', 'tetradkata_clear_personalization_session');

/**
 * Display personalization in order item meta
 */
function tetradkata_display_order_item_personalization($item_id, $item, $order) {
    if ($order && method_exists($order, 'get_meta')) {
        $personalization_enabled = $order->get_meta('_personalization_enabled');
        $personalization_name = $order->get_meta('_personalization_name');
        
        if ($personalization_enabled === 'yes' && $personalization_name) {
            echo '<div class="personalization-info" style="margin-top: 10px; padding: 10px; background: #f5f5f5; border-radius: 5px;">';
            echo '<strong>Персонализация:</strong> ' . esc_html($personalization_name);
            echo '</div>';
        }
    }
}
add_action('woocommerce_order_item_meta_end', 'tetradkata_display_order_item_personalization', 10, 3);

?>