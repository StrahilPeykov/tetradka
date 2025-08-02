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
 * Custom function to display only regular price (no sale price)
 */
function tetradkata_get_regular_price_html($product) {
    if (!$product || !is_object($product)) {
        return '';
    }
    
    $regular_price = $product->get_regular_price();
    
    if (empty($regular_price)) {
        return '';
    }
    
    $price_html = wc_price($regular_price);
    
    return $price_html;
}

/**
 * Override WooCommerce price display to show only regular price
 */
function tetradkata_custom_price_html($price_html, $product) {
    // Only apply this to shop/category pages and product cards, not single product pages
    if (is_product()) {
        return $price_html; // Keep original behavior on single product page
    }
    
    return tetradkata_get_regular_price_html($product);
}
add_filter('woocommerce_get_price_html', 'tetradkata_custom_price_html', 10, 2);

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
 * WooCommerce Button Overrides
 */
function tetradkata_woocommerce_button_classes($classes) {
    return 'btn btn-primary';
}
add_filter('woocommerce_button_classes', 'tetradkata_woocommerce_button_classes');
?>