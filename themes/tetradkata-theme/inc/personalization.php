<?php
/**
 * Personalization Functions for WooCommerce
 * 
 * @package TetradkataTheme
 */

if (!defined('ABSPATH')) {
    exit;
}

class Tetradkata_Personalization {
    
    /**
     * Constructor
     */
    public function __construct() {
        // AJAX actions for personalization
        add_action('wp_ajax_add_personalization_fee', array($this, 'ajax_add_personalization_fee'));
        add_action('wp_ajax_nopriv_add_personalization_fee', array($this, 'ajax_add_personalization_fee'));
        
        add_action('wp_ajax_remove_personalization_fee', array($this, 'ajax_remove_personalization_fee'));
        add_action('wp_ajax_nopriv_remove_personalization_fee', array($this, 'ajax_remove_personalization_fee'));
        
        // Cart fees
        add_action('woocommerce_cart_calculate_fees', array($this, 'add_cart_personalization_fee'));
        
        // Checkout
        add_action('woocommerce_checkout_update_order_meta', array($this, 'save_personalization_data'));
        add_action('woocommerce_thankyou', array($this, 'clear_personalization_session'));
        
        // Admin
        add_action('woocommerce_admin_order_data_after_order_details', array($this, 'display_admin_order_personalization'));
        
        // Emails
        add_action('woocommerce_email_order_meta', array($this, 'add_personalization_to_emails'), 10, 4);
        
        // Payment gateways
        add_filter('woocommerce_available_payment_gateways', array($this, 'disable_cod_for_personalization'));
        
        // Display in order items
        add_action('woocommerce_order_item_meta_end', array($this, 'display_order_item_personalization'), 10, 3);
    }
    
    /**
     * Add personalization fee via AJAX
     */
    public function ajax_add_personalization_fee() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tetradkata_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed', 'tetradkata')));
            return;
        }
        
        if (!WC()->session) {
            wp_send_json_error(array('message' => __('Session not available', 'tetradkata')));
            return;
        }
        
        WC()->session->set('add_personalization', true);
        
        wp_send_json_success(array('message' => __('Personalization fee added', 'tetradkata')));
    }
    
    /**
     * Remove personalization fee via AJAX
     */
    public function ajax_remove_personalization_fee() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tetradkata_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed', 'tetradkata')));
            return;
        }
        
        if (!WC()->session) {
            wp_send_json_error(array('message' => __('Session not available', 'tetradkata')));
            return;
        }
        
        WC()->session->set('add_personalization', false);
        
        wp_send_json_success(array('message' => __('Personalization fee removed', 'tetradkata')));
    }
    
    /**
     * Add personalization fee to cart
     */
    public function add_cart_personalization_fee($cart) {
        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }
        
        if (!WC()->session) {
            return;
        }
        
        $add_personalization = WC()->session->get('add_personalization');
        
        if ($add_personalization) {
            $fee = apply_filters('tetradkata_personalization_fee', 5.00);
            $fee_label = __('Personalization', 'tetradkata');
            
            $cart->add_fee($fee_label, $fee, true);
        }
    }
    
    /**
     * Save personalization data with order
     */
    public function save_personalization_data($order_id) {
        // Check if personalization was selected
        if (isset($_POST['add_personalization']) && $_POST['add_personalization'] === 'yes') {
            update_post_meta($order_id, '_personalization_enabled', 'yes');
            
            // Save personalization name
            if (isset($_POST['personalization_name'])) {
                $personalization_name = sanitize_text_field($_POST['personalization_name']);
                
                // Validate name
                if (strlen($personalization_name) > 50) {
                    $personalization_name = substr($personalization_name, 0, 50);
                }
                
                update_post_meta($order_id, '_personalization_name', $personalization_name);
            }
            
            // Save personalization type if applicable
            if (isset($_POST['personalization_type'])) {
                $personalization_type = sanitize_text_field($_POST['personalization_type']);
                update_post_meta($order_id, '_personalization_type', $personalization_type);
            }
            
            // Log personalization request
            $order = wc_get_order($order_id);
            if ($order) {
                $order->add_order_note(sprintf(
                    __('Personalization requested: %s', 'tetradkata'),
                    $personalization_name
                ));
            }
        }
    }
    
    /**
     * Clear personalization session after order
     */
    public function clear_personalization_session($order_id) {
        if (WC()->session) {
            WC()->session->set('add_personalization', false);
        }
    }
    
    /**
     * Display personalization info in admin order page
     */
    public function display_admin_order_personalization($order) {
        $personalization_enabled = get_post_meta($order->get_id(), '_personalization_enabled', true);
        
        if ($personalization_enabled === 'yes') {
            $personalization_name = get_post_meta($order->get_id(), '_personalization_name', true);
            $personalization_type = get_post_meta($order->get_id(), '_personalization_type', true);
            ?>
            <div class="order_data_column" style="width: 100%; margin-top: 20px;">
                <h3><?php esc_html_e('Personalization', 'tetradkata'); ?></h3>
                <p>
                    <strong><?php esc_html_e('Name for cover:', 'tetradkata'); ?></strong> 
                    <?php echo esc_html($personalization_name); ?>
                </p>
                <?php if ($personalization_type) : ?>
                    <p>
                        <strong><?php esc_html_e('Type:', 'tetradkata'); ?></strong> 
                        <?php echo esc_html($personalization_type); ?>
                    </p>
                <?php endif; ?>
                <p style="color: #ff9800;">
                    <strong><?php esc_html_e('Note:', 'tetradkata'); ?></strong> 
                    <?php esc_html_e('This order includes personalization. Processing time: 5-7 business days.', 'tetradkata'); ?>
                </p>
            </div>
            <?php
        }
    }
    
    /**
     * Add personalization info to order emails
     */
    public function add_personalization_to_emails($order, $sent_to_admin, $plain_text, $email) {
        $personalization_enabled = get_post_meta($order->get_id(), '_personalization_enabled', true);
        
        if ($personalization_enabled === 'yes') {
            $personalization_name = get_post_meta($order->get_id(), '_personalization_name', true);
            $personalization_type = get_post_meta($order->get_id(), '_personalization_type', true);
            
            if ($plain_text) {
                echo "\n\n" . __('Personalization:', 'tetradkata') . "\n";
                echo __('Name for cover:', 'tetradkata') . ' ' . $personalization_name . "\n";
                if ($personalization_type) {
                    echo __('Type:', 'tetradkata') . ' ' . $personalization_type . "\n";
                }
                echo __('Processing time: 5-7 business days', 'tetradkata') . "\n";
            } else {
                ?>
                <div style="margin: 20px 0; padding: 15px; background: #fff5e6; border-left: 4px solid #ff9800; border-radius: 0 5px 5px 0;">
                    <h2 style="color: #ff9800; margin-top: 0;"><?php esc_html_e('Personalization', 'tetradkata'); ?></h2>
                    <p>
                        <strong><?php esc_html_e('Name for cover:', 'tetradkata'); ?></strong> 
                        <?php echo esc_html($personalization_name); ?>
                    </p>
                    <?php if ($personalization_type) : ?>
                        <p>
                            <strong><?php esc_html_e('Type:', 'tetradkata'); ?></strong> 
                            <?php echo esc_html($personalization_type); ?>
                        </p>
                    <?php endif; ?>
                    <p style="font-style: italic; color: #666;">
                        <?php esc_html_e('Processing time: 5-7 business days', 'tetradkata'); ?>
                    </p>
                </div>
                <?php
            }
        }
    }
    
    /**
     * Disable COD when personalization is selected
     */
    public function disable_cod_for_personalization($available_gateways) {
        if (is_admin()) {
            return $available_gateways;
        }
        
        if (WC()->session && WC()->session->get('add_personalization')) {
            if (isset($available_gateways['cod'])) {
                unset($available_gateways['cod']);
                
                // Add notice explaining why COD is not available
                if (!wc_has_notice(__('Cash on delivery is not available for personalized products.', 'tetradkata'), 'notice')) {
                    wc_add_notice(__('Cash on delivery is not available for personalized products.', 'tetradkata'), 'notice');
                }
            }
        }
        
        return $available_gateways;
    }
    
    /**
     * Display personalization in order item meta
     */
    public function display_order_item_personalization($item_id, $item, $order) {
        if ($order && method_exists($order, 'get_meta')) {
            $personalization_enabled = $order->get_meta('_personalization_enabled');
            $personalization_name = $order->get_meta('_personalization_name');
            
            if ($personalization_enabled === 'yes' && $personalization_name) {
                ?>
                <div class="personalization-info" style="margin-top: 10px; padding: 10px; background: #f5f5f5; border-radius: 5px;">
                    <strong><?php esc_html_e('Personalization:', 'tetradkata'); ?></strong> 
                    <?php echo esc_html($personalization_name); ?>
                </div>
                <?php
            }
        }
    }
    
    /**
     * Add personalization option to product page
     */
    public function add_product_personalization_option() {
        global $product;
        
        // Check if product supports personalization
        $supports_personalization = get_post_meta($product->get_id(), '_supports_personalization', true);
        
        if ($supports_personalization === 'yes') {
            ?>
            <div class="product-personalization" style="margin: 20px 0; padding: 15px; background: #f5f5f5; border-radius: 5px;">
                <label>
                    <input type="checkbox" name="add_personalization" id="add_personalization" value="yes">
                    <strong><?php esc_html_e('Add personalization (+5.00 BGN)', 'tetradkata'); ?></strong>
                </label>
                <div class="personalization-fields" style="display: none; margin-top: 15px;">
                    <label for="personalization_name">
                        <?php esc_html_e('Name for personalization:', 'tetradkata'); ?>
                        <input type="text" 
                               name="personalization_name" 
                               id="personalization_name" 
                               maxlength="50" 
                               placeholder="<?php esc_attr_e('Enter name (max 50 characters)', 'tetradkata'); ?>"
                               style="width: 100%; margin-top: 5px;">
                    </label>
                </div>
            </div>
            <script>
            jQuery(document).ready(function($) {
                $('#add_personalization').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('.personalization-fields').slideDown();
                        $('#personalization_name').attr('required', true);
                    } else {
                        $('.personalization-fields').slideUp();
                        $('#personalization_name').attr('required', false).val('');
                    }
                });
            });
            </script>
            <?php
        }
    }
}

// Initialize personalization
new Tetradkata_Personalization();