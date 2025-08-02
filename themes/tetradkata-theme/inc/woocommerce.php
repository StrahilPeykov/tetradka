<?php
/**
 * WooCommerce Customizations
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Remove WooCommerce Default Styles
 */
add_filter('woocommerce_enqueue_styles', '__return_false');

/**
 * Shop Layout
 */
function tetradkata_loop_columns() {
    return 3;
}
add_filter('loop_shop_columns', 'tetradkata_loop_columns');

function tetradkata_products_per_page() {
    return 12;
}
add_filter('loop_shop_per_page', 'tetradkata_products_per_page');

/**
 * Button Text
 */
function tetradkata_add_to_cart_text() {
    return __('Добави в количката', 'tetradkata');
}
add_filter('woocommerce_product_add_to_cart_text', 'tetradkata_add_to_cart_text');
add_filter('woocommerce_product_single_add_to_cart_text', 'tetradkata_add_to_cart_text');

/**
 * Currency Symbol
 */
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
 * Price Formatting
 */
add_filter('woocommerce_get_price_thousand_separator', function() { return ' '; });
add_filter('woocommerce_get_price_decimal_separator', function() { return ','; });

/**
 * Product Placeholder Image
 */
function tetradkata_custom_woocommerce_placeholder_img_src($src) {
    return get_template_directory_uri() . '/assets/images/product-placeholder.jpg';
}
add_filter('woocommerce_placeholder_img_src', 'tetradkata_custom_woocommerce_placeholder_img_src');

/**
 * AJAX Add to Cart for Shop Pages
 */
add_filter('woocommerce_loop_add_to_cart_link', function($button, $product) {
    if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) {
        $button = str_replace('add_to_cart_button', 'add_to_cart_button ajax_add_to_cart', $button);
    }
    return $button;
}, 10, 2);

/**
 * Featured Products Meta Box
 */
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
    if (!isset($_POST['tetradkata_featured_nonce']) || !wp_verify_nonce($_POST['tetradkata_featured_nonce'], 'tetradkata_featured_nonce')) {
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
 * WooCommerce Button Overrides
 */
function tetradkata_woocommerce_button_classes($classes) {
    return 'btn btn-primary';
}
add_filter('woocommerce_button_classes', 'tetradkata_woocommerce_button_classes');
?>