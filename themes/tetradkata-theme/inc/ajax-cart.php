<?php
/**
 * AJAX Cart Functions
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize WooCommerce Session for AJAX
 */
function tetradkata_init_woocommerce_session() {
    if (class_exists('WooCommerce') && (wp_doing_ajax() || is_admin())) {
        if (WC()->session === null) {
            WC()->frontend_includes();
            WC()->session = new WC_Session_Handler();
            WC()->session->init();
        }
        
        if (WC()->cart === null) {
            WC()->cart = new WC_Cart();
        }
        
        if (WC()->customer === null) {
            WC()->customer = new WC_Customer(get_current_user_id(), true);
        }
    }
}
add_action('init', 'tetradkata_init_woocommerce_session');
add_action('wp_ajax_nopriv_tetradkata_add_to_cart', 'tetradkata_init_woocommerce_session', 5);
add_action('wp_ajax_tetradkata_add_to_cart', 'tetradkata_init_woocommerce_session', 5);
add_action('wp_ajax_nopriv_tetradkata_get_cart_contents', 'tetradkata_init_woocommerce_session', 5);
add_action('wp_ajax_tetradkata_get_cart_contents', 'tetradkata_init_woocommerce_session', 5);

/**
 * AJAX Add to Cart Handler
 */
function tetradkata_ajax_add_to_cart() {
    if (!class_exists('WooCommerce')) {
        wp_send_json_error(array('message' => 'WooCommerce not active'));
        return;
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tetradkata_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed'));
        return;
    }
    
    if (WC()->cart === null) {
        WC()->frontend_includes();
        WC()->session = new WC_Session_Handler();
        WC()->session->init();
        WC()->cart = new WC_Cart();
        WC()->customer = new WC_Customer(get_current_user_id(), true);
    }
    
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;
    
    if (!$product_id) {
        wp_send_json_error(array('message' => 'Invalid product ID'));
        return;
    }
    
    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error(array('message' => 'Product not found'));
        return;
    }
    
    if (!$product->is_purchasable()) {
        wp_send_json_error(array('message' => 'This product cannot be purchased'));
        return;
    }
    
    if (!$product->is_in_stock() || ($product->managing_stock() && $product->get_stock_quantity() < $quantity)) {
        wp_send_json_error(array('message' => 'Insufficient stock'));
        return;
    }
    
    try {
        wc_clear_notices();
        
        $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity);
        
        if ($cart_item_key) {
            WC()->cart->calculate_totals();
            $cart_count     = WC()->cart->get_cart_contents_count();
            $cart_total_raw = WC()->cart->get_total('raw');
            $cart_total     = number_format($cart_total_raw, 2, ',', ' ') . ' лв.';
            
            wp_send_json_success(array(
                'message' => 'Product added to cart successfully',
                'cart_count' => $cart_count,
                'cart_total' => $cart_total,
                'cart_item_key' => $cart_item_key,
                'fragments' => array(
                    '.cart-count' => '<span class="cart-count">' . $cart_count . '</span>',
                    '#cart-total-amount' => '<span id="cart-total-amount">' . $cart_total . '</span>'
                )
            ));
        } else {
            $notices = wc_get_notices('error');
            $error_message = 'Could not add product to cart';
            
            if (!empty($notices)) {
                $error_message = $notices[0]['notice'];
                wc_clear_notices();
            }
            
            wp_send_json_error(array('message' => $error_message));
        }
    } catch (Exception $e) {
        wp_send_json_error(array('message' => 'Error: ' . $e->getMessage()));
    }
}
add_action('wp_ajax_tetradkata_add_to_cart', 'tetradkata_ajax_add_to_cart');
add_action('wp_ajax_nopriv_tetradkata_add_to_cart', 'tetradkata_ajax_add_to_cart');

/**
 * AJAX Get Cart Contents
 */
function tetradkata_get_cart_contents() {
    if (!class_exists('WooCommerce')) {
        wp_send_json_error(array('message' => 'WooCommerce not active'));
        return;
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tetradkata_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed'));
        return;
    }
    
    if (WC()->cart === null) {
        WC()->frontend_includes();
        WC()->session = new WC_Session_Handler();
        WC()->session->init();
        WC()->cart = new WC_Cart();
        WC()->customer = new WC_Customer(get_current_user_id(), true);
    }
    
    try {
        ob_start();
        
        if (WC()->cart->is_empty()) {
            $shop_page_url = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : home_url('/shop');
            ?>
            <div class="empty-cart">
                <p>Количката е празна</p>
                <a href="<?php echo esc_url($shop_page_url); ?>" class="btn btn-primary">
                    Продължи пазаруването
                </a>
            </div>
            <?php
        } else {
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $product = $cart_item['data'];
                $product_id = $cart_item['product_id'];
                
                if ($product && $product->exists() && $cart_item['quantity'] > 0) {
                    $product_name = $product->get_name();
                    $product_permalink = $product->get_permalink();
                    
                    $line_total = $cart_item['line_total'] + $cart_item['line_tax'];
                    $formatted_price = number_format($line_total, 2, ',', ' ') . ' лв.';
                    
                    $product_image_id = $product->get_image_id();
                    if ($product_image_id) {
                        $product_image_url = wp_get_attachment_image_url($product_image_id, 'thumbnail');
                    } else {
                        $product_image_url = wc_placeholder_img_src('thumbnail');
                    }
                    
                    ?>
                    <div class="cart-item" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
                        <div class="cart-item-image">
                            <a href="<?php echo esc_url($product_permalink); ?>">
                                <img src="<?php echo esc_url($product_image_url); ?>" 
                                     alt="<?php echo esc_attr($product_name); ?>" 
                                     class="cart-item-img">
                            </a>
                        </div>
                        <div class="cart-item-details">
                            <div class="cart-item-name">
                                <a href="<?php echo esc_url($product_permalink); ?>">
                                    <?php echo esc_html($product_name); ?>
                                </a>
                            </div>
                            <div class="cart-item-price"><?php echo $formatted_price; ?></div>
                            <div class="cart-item-quantity">
                                <span>Количество: <?php echo $cart_item['quantity']; ?></span>
                                <button class="remove-cart-item" 
                                        data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>"
                                        title="Премахни от количката">
                                    ×
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
        }
        
        $cart_html = ob_get_clean();
        
        WC()->cart->calculate_totals();
        
        $cart_total_raw = WC()->cart->get_total('raw');
        $cart_total = number_format($cart_total_raw, 2, ',', ' ') . ' лв.';
        
        wp_send_json_success(array(
            'cart_html' => $cart_html,
            'cart_total' => $cart_total,
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'is_empty' => WC()->cart->is_empty()
        ));
        
    } catch (Exception $e) {
        wp_send_json_error(array('message' => 'Error loading cart: ' . $e->getMessage()));
    }
}
add_action('wp_ajax_tetradkata_get_cart_contents', 'tetradkata_get_cart_contents');
add_action('wp_ajax_nopriv_tetradkata_get_cart_contents', 'tetradkata_get_cart_contents');

/**
 * AJAX Remove Cart Item
 */
function tetradkata_remove_cart_item() {
    if (!class_exists('WooCommerce')) {
        wp_send_json_error(array('message' => 'WooCommerce not active'));
        return;
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tetradkata_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed'));
        return;
    }
    
    if (WC()->cart === null) {
        WC()->frontend_includes();
        WC()->session = new WC_Session_Handler();
        WC()->session->init();
        WC()->cart = new WC_Cart();
        WC()->customer = new WC_Customer(get_current_user_id(), true);
    }
    
    $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field($_POST['cart_item_key']) : '';
    
    if (!$cart_item_key) {
        wp_send_json_error(array('message' => 'Invalid cart item'));
        return;
    }
    
    try {
        $removed = WC()->cart->remove_cart_item($cart_item_key);
        
        if ($removed) {
            WC()->cart->calculate_totals();
            
            $cart_total_raw = WC()->cart->get_total('raw');
            $cart_total = number_format($cart_total_raw, 2, ',', ' ') . ' лв.';
            
            wp_send_json_success(array(
                'message' => 'Item removed from cart',
                'cart_count' => WC()->cart->get_cart_contents_count(),
                'cart_total' => $cart_total,
                'is_empty' => WC()->cart->is_empty()
            ));
        } else {
            wp_send_json_error(array('message' => 'Could not remove item'));
        }
    } catch (Exception $e) {
        wp_send_json_error(array('message' => 'Error removing item: ' . $e->getMessage()));
    }
}
add_action('wp_ajax_tetradkata_remove_cart_item', 'tetradkata_remove_cart_item');
add_action('wp_ajax_nopriv_tetradkata_remove_cart_item', 'tetradkata_remove_cart_item');

/**
 * AJAX Quick View
 */
function tetradkata_quick_view() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tetradkata_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed'));
        return;
    }
    
    $product_id = absint($_POST['product_id']);
    
    if (!$product_id) {
        wp_send_json_error(array('message' => 'Invalid product ID'));
        return;
    }
    
    $product = wc_get_product($product_id);
    
    if (!$product) {
        wp_send_json_error(array('message' => 'Product not found'));
        return;
    }
    
    ob_start();
    ?>
    <div class="quick-view-product">
        <div class="quick-view-images">
            <?php echo $product->get_image('medium'); ?>
        </div>
        <div class="quick-view-details">
            <h2><?php echo $product->get_name(); ?></h2>
            <div class="quick-view-price"><?php echo $product->get_price_html(); ?></div>
            <div class="quick-view-description">
                <?php echo $product->get_short_description(); ?>
            </div>
            
            <?php if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) : ?>
                <div class="quick-view-actions">
                    <button class="btn btn-primary add-to-cart-btn" 
                            data-product-id="<?php echo $product_id; ?>"
                            data-product-name="<?php echo esc_attr($product->get_name()); ?>">
                        <span class="btn-text">Добави в количката</span>
                        <span class="btn-loading" style="display: none;">
                            <span class="loading"></span> Добавя...
                        </span>
                    </button>
                    <a href="<?php echo $product->get_permalink(); ?>" class="btn btn-secondary">
                        Виж детайли
                    </a>
                </div>
            <?php else : ?>
                <div class="quick-view-actions">
                    <a href="<?php echo $product->get_permalink(); ?>" class="btn btn-primary">
                        Виж детайли
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    
    $html = ob_get_clean();
    
    wp_send_json_success(array('html' => $html));
}
add_action('wp_ajax_tetradkata_quick_view', 'tetradkata_quick_view');
add_action('wp_ajax_nopriv_tetradkata_quick_view', 'tetradkata_quick_view');

/**
 * Cart Fragments
 */
function tetradkata_add_to_cart_fragments($fragments) {
    $cart_count = WC()->cart->get_cart_contents_count();
    $cart_total_raw = WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_tax();
    $cart_total = number_format($cart_total_raw, 2, ',', ' ') . ' лв.';
    
    $fragments['.cart-count'] = '<span class="cart-count">' . $cart_count . '</span>';
    $fragments['#cart-total-amount'] = '<span id="cart-total-amount">' . $cart_total . '</span>';
    
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'tetradkata_add_to_cart_fragments');
?>