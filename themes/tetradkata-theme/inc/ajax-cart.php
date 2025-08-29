<?php
/**
 * AJAX Cart Functions
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize WooCommerce Session for AJAX - Improved version
 */
class Tetradkata_AJAX_Cart {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Initialize session for AJAX requests
        add_action('init', array($this, 'init_wc_session_handler'), 1);
        add_action('woocommerce_init', array($this, 'init_wc_session'));
        
        // AJAX actions
        add_action('wp_ajax_tetradkata_add_to_cart', array($this, 'ajax_add_to_cart'));
        add_action('wp_ajax_nopriv_tetradkata_add_to_cart', array($this, 'ajax_add_to_cart'));
        
        add_action('wp_ajax_tetradkata_get_cart_contents', array($this, 'ajax_get_cart_contents'));
        add_action('wp_ajax_nopriv_tetradkata_get_cart_contents', array($this, 'ajax_get_cart_contents'));
        
        add_action('wp_ajax_tetradkata_remove_cart_item', array($this, 'ajax_remove_cart_item'));
        add_action('wp_ajax_nopriv_tetradkata_remove_cart_item', array($this, 'ajax_remove_cart_item'));
        
        add_action('wp_ajax_tetradkata_quick_view', array($this, 'ajax_quick_view'));
        add_action('wp_ajax_nopriv_tetradkata_quick_view', array($this, 'ajax_quick_view'));
        
        // Cart fragments
        add_filter('woocommerce_add_to_cart_fragments', array($this, 'add_to_cart_fragments'));
    }
    
    /**
     * Initialize WooCommerce session handler
     */
    public function init_wc_session_handler() {
        if (wp_doing_ajax() && class_exists('WooCommerce')) {
            if (!WC()->session || !WC()->session->has_session()) {
                WC()->frontend_includes();
                if (!WC()->session) {
                    WC()->session = new WC_Session_Handler();
                    WC()->session->init();
                }
            }
        }
    }
    
    /**
     * Initialize WooCommerce session
     */
    public function init_wc_session() {
        if (wp_doing_ajax() && class_exists('WooCommerce')) {
            if (!WC()->cart) {
                WC()->cart = new WC_Cart();
            }
            if (!WC()->customer) {
                WC()->customer = new WC_Customer(get_current_user_id(), true);
            }
        }
    }
    
    /**
     * Verify nonce for security
     */
    private function verify_nonce() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tetradkata_nonce')) {
            wp_send_json_error(array(
                'message' => __('Security check failed', 'tetradkata')
            ));
            return false;
        }
        return true;
    }
    
    /**
     * AJAX Add to Cart Handler
     */
    public function ajax_add_to_cart() {
        if (!class_exists('WooCommerce')) {
            wp_send_json_error(array('message' => __('WooCommerce not active', 'tetradkata')));
            return;
        }

        if (!$this->verify_nonce()) {
            return;
        }
        
        // Ensure session is initialized
        $this->init_wc_session_handler();
        $this->init_wc_session();
        
        $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;
        
        if (!$product_id) {
            wp_send_json_error(array('message' => __('Invalid product ID', 'tetradkata')));
            return;
        }
        
        $product = wc_get_product($product_id);
        if (!$product) {
            wp_send_json_error(array('message' => __('Product not found', 'tetradkata')));
            return;
        }
        
        if (!$product->is_purchasable()) {
            wp_send_json_error(array('message' => __('This product cannot be purchased', 'tetradkata')));
            return;
        }
        
        if (!$product->is_in_stock() || ($product->managing_stock() && $product->get_stock_quantity() < $quantity)) {
            wp_send_json_error(array('message' => __('Insufficient stock', 'tetradkata')));
            return;
        }
        
        try {
            wc_clear_notices();
            
            $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity);
            
            if ($cart_item_key) {
                WC()->cart->calculate_totals();
                $cart_count = absint(WC()->cart->get_cart_contents_count());
                $cart_total = $this->format_price(WC()->cart->get_total('raw'));
                
                wp_send_json_success(array(
                    'message' => __('Product added to cart successfully', 'tetradkata'),
                    'cart_count' => $cart_count,
                    'cart_total' => $cart_total,
                    'cart_item_key' => sanitize_key($cart_item_key),
                    'fragments' => array(
                        '.cart-count' => '<span class="cart-count">' . esc_html($cart_count) . '</span>',
                        '#cart-total-amount' => '<span id="cart-total-amount">' . esc_html($cart_total) . '</span>'
                    )
                ));
            } else {
                $notices = wc_get_notices('error');
                $error_message = __('Could not add product to cart', 'tetradkata');
                
                if (!empty($notices)) {
                    $error_message = wp_strip_all_tags($notices[0]['notice']);
                    wc_clear_notices();
                }
                
                wp_send_json_error(array('message' => $error_message));
            }
        } catch (Exception $e) {
            wp_send_json_error(array('message' => sprintf(__('Error: %s', 'tetradkata'), $e->getMessage())));
        }
    }
    
    /**
     * AJAX Get Cart Contents
     */
    public function ajax_get_cart_contents() {
        if (!class_exists('WooCommerce')) {
            wp_send_json_error(array('message' => __('WooCommerce not active', 'tetradkata')));
            return;
        }

        if (!$this->verify_nonce()) {
            return;
        }
        
        // Ensure session is initialized
        $this->init_wc_session_handler();
        $this->init_wc_session();
        
        try {
            ob_start();
            
            if (WC()->cart->is_empty()) {
                $shop_page_url = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : home_url('/shop');
                ?>
                <div class="empty-cart">
                    <p><?php esc_html_e('Your cart is empty', 'tetradkata'); ?></p>
                    <a href="<?php echo esc_url($shop_page_url); ?>" class="btn btn-primary">
                        <?php esc_html_e('Continue Shopping', 'tetradkata'); ?>
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
                        $formatted_price = $this->format_price($line_total);
                        
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
                                <div class="cart-item-price"><?php echo esc_html($formatted_price); ?></div>
                                <div class="cart-item-quantity">
                                    <span><?php echo esc_html(sprintf(__('Quantity: %s', 'tetradkata'), $cart_item['quantity'])); ?></span>
                                    <button class="remove-cart-item" 
                                            data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>"
                                            title="<?php esc_attr_e('Remove from cart', 'tetradkata'); ?>">
                                        &times;
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
            
            $cart_total = $this->format_price(WC()->cart->get_total('raw'));
            
            wp_send_json_success(array(
                'cart_html' => $cart_html,
                'cart_total' => $cart_total,
                'cart_count' => absint(WC()->cart->get_cart_contents_count()),
                'is_empty' => WC()->cart->is_empty()
            ));
            
        } catch (Exception $e) {
            wp_send_json_error(array('message' => sprintf(__('Error loading cart: %s', 'tetradkata'), $e->getMessage())));
        }
    }
    
    /**
     * AJAX Remove Cart Item
     */
    public function ajax_remove_cart_item() {
        if (!class_exists('WooCommerce')) {
            wp_send_json_error(array('message' => __('WooCommerce not active', 'tetradkata')));
            return;
        }

        if (!$this->verify_nonce()) {
            return;
        }
        
        // Ensure session is initialized
        $this->init_wc_session_handler();
        $this->init_wc_session();
        
        $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field($_POST['cart_item_key']) : '';
        
        if (!$cart_item_key) {
            wp_send_json_error(array('message' => __('Invalid cart item', 'tetradkata')));
            return;
        }
        
        try {
            $removed = WC()->cart->remove_cart_item($cart_item_key);
            
            if ($removed) {
                WC()->cart->calculate_totals();
                
                $cart_total = $this->format_price(WC()->cart->get_total('raw'));
                
                wp_send_json_success(array(
                    'message' => __('Item removed from cart', 'tetradkata'),
                    'cart_count' => absint(WC()->cart->get_cart_contents_count()),
                    'cart_total' => $cart_total,
                    'is_empty' => WC()->cart->is_empty()
                ));
            } else {
                wp_send_json_error(array('message' => __('Could not remove item', 'tetradkata')));
            }
        } catch (Exception $e) {
            wp_send_json_error(array('message' => sprintf(__('Error removing item: %s', 'tetradkata'), $e->getMessage())));
        }
    }
    
    /**
     * AJAX Quick View
     */
    public function ajax_quick_view() {
        if (!$this->verify_nonce()) {
            return;
        }
        
        $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
        
        if (!$product_id) {
            wp_send_json_error(array('message' => __('Invalid product ID', 'tetradkata')));
            return;
        }
        
        $product = wc_get_product($product_id);
        
        if (!$product) {
            wp_send_json_error(array('message' => __('Product not found', 'tetradkata')));
            return;
        }
        
        ob_start();
        ?>
        <div class="quick-view-product">
            <div class="quick-view-images">
                <?php echo $product->get_image('medium'); ?>
            </div>
            <div class="quick-view-details">
                <h2><?php echo esc_html($product->get_name()); ?></h2>
                <div class="quick-view-price"><?php echo $product->get_price_html(); ?></div>
                <div class="quick-view-description">
                    <?php echo wp_kses_post($product->get_short_description()); ?>
                </div>
                
                <?php if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) : ?>
                    <div class="quick-view-actions">
                        <button class="btn btn-primary add-to-cart-btn" 
                                data-product-id="<?php echo esc_attr($product_id); ?>"
                                data-product-name="<?php echo esc_attr($product->get_name()); ?>">
                            <span class="btn-text"><?php esc_html_e('Add to Cart', 'tetradkata'); ?></span>
                            <span class="btn-loading" style="display: none;">
                                <span class="loading"></span> <?php esc_html_e('Adding...', 'tetradkata'); ?>
                            </span>
                        </button>
                        <a href="<?php echo esc_url($product->get_permalink()); ?>" class="btn btn-secondary">
                            <?php esc_html_e('View Details', 'tetradkata'); ?>
                        </a>
                    </div>
                <?php else : ?>
                    <div class="quick-view-actions">
                        <a href="<?php echo esc_url($product->get_permalink()); ?>" class="btn btn-primary">
                            <?php esc_html_e('View Details', 'tetradkata'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        
        $html = ob_get_clean();
        
        wp_send_json_success(array('html' => $html));
    }
    
    /**
     * Cart Fragments
     */
    public function add_to_cart_fragments($fragments) {
        $cart_count = absint(WC()->cart->get_cart_contents_count());
        $cart_total = $this->format_price(WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_tax());
        
        $fragments['.cart-count'] = '<span class="cart-count">' . esc_html($cart_count) . '</span>';
        $fragments['#cart-total-amount'] = '<span id="cart-total-amount">' . esc_html($cart_total) . '</span>';
        
        return $fragments;
    }
    
    /**
     * Format price for Bulgarian currency
     */
    private function format_price($price) {
        return number_format((float)$price, 2, ',', ' ') . ' ' . __('BGN', 'tetradkata');
    }
}

// Initialize the class
new Tetradkata_AJAX_Cart();