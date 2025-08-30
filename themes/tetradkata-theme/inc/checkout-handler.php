<?php
/**
 * Checkout Processing & Email System
 * Add to functions.php or create as separate include
 * 
 * @package TetradkataTheme
 */

/**
 * Enhanced Checkout Processing
 */
class Tetradkata_Checkout_Handler {
    
    public function __construct() {
        // Checkout hooks
        add_action('woocommerce_checkout_process', array($this, 'validate_checkout_fields'));
        add_action('woocommerce_checkout_update_order_meta', array($this, 'save_custom_checkout_data'));
        add_action('woocommerce_thankyou', array($this, 'after_checkout_success'));
        
        // Email hooks
        add_action('woocommerce_order_status_processing', array($this, 'send_order_confirmation_email'));
        add_action('woocommerce_order_status_on-hold', array($this, 'send_order_confirmation_email'));
        add_action('woocommerce_order_status_completed', array($this, 'send_completion_email'));
        
        // Payment hooks
        add_action('woocommerce_payment_complete', array($this, 'handle_payment_complete'));
        add_filter('woocommerce_cod_process_payment_order_status', array($this, 'cod_order_status'));
        
        // Admin hooks
        add_action('woocommerce_admin_order_data_after_billing_address', array($this, 'display_custom_order_data'));
        
        // AJAX handlers
        add_action('wp_ajax_apply_coupon', array($this, 'ajax_apply_coupon'));
        add_action('wp_ajax_nopriv_apply_coupon', array($this, 'ajax_apply_coupon'));
        
        // Order status transitions
        add_action('woocommerce_order_status_changed', array($this, 'handle_order_status_change'), 10, 4);
    }
    
    /**
     * Validate checkout fields
     */
    public function validate_checkout_fields() {
        // Validate personalization
        if (isset($_POST['add_personalization']) && $_POST['add_personalization'] === 'yes') {
            if (empty($_POST['personalization_name'])) {
                wc_add_notice('Моля въведете име за персонализация', 'error');
            } elseif (strlen($_POST['personalization_name']) > 20) {
                wc_add_notice('Името за персонализация не може да бъде по-дълго от 20 символа', 'error');
            }
            
            // Check if COD is selected with personalization
            if (isset($_POST['payment_method']) && $_POST['payment_method'] === 'cod') {
                wc_add_notice('Наложен платеж не е наличен за персонализирани продукти', 'error');
            }
        }
        
        // Validate required fields
        $required_fields = array(
            'billing_first_name' => 'Име',
            'billing_last_name' => 'Фамилия', 
            'billing_email' => 'Имейл адрес',
            'billing_phone' => 'Телефон',
            'billing_city' => 'Град',
            'billing_address_1' => 'Адрес'
        );
        
        foreach ($required_fields as $field => $label) {
            if (empty($_POST[$field])) {
                wc_add_notice(sprintf('Полето "%s" е задължително', $label), 'error');
            }
        }
        
        // Validate email
        if (!empty($_POST['billing_email']) && !is_email($_POST['billing_email'])) {
            wc_add_notice('Моля въведете валиден имейл адрес', 'error');
        }
        
        // Validate phone number
        if (!empty($_POST['billing_phone'])) {
            $phone = preg_replace('/\D/', '', $_POST['billing_phone']);
            if (strlen($phone) < 9) {
                wc_add_notice('Моля въведете валиден телефонен номер', 'error');
            }
        }
        
        // Validate terms acceptance
        if (!isset($_POST['terms']) || $_POST['terms'] !== '1') {
            wc_add_notice('Трябва да се съгласите с общите условия и политиката за поверителност', 'error');
        }
    }
    
    /**
     * Save custom checkout data
     */
    public function save_custom_checkout_data($order_id) {
        // Save personalization data
        if (isset($_POST['add_personalization']) && $_POST['add_personalization'] === 'yes') {
            update_post_meta($order_id, '_personalization_enabled', 'yes');
            
            if (!empty($_POST['personalization_name'])) {
                $name = sanitize_text_field($_POST['personalization_name']);
                update_post_meta($order_id, '_personalization_name', $name);
                
                // Add personalization fee if not already added
                $order = wc_get_order($order_id);
                $fee_added = false;
                foreach ($order->get_fees() as $fee) {
                    if ($fee->get_name() === 'Персонализация') {
                        $fee_added = true;
                        break;
                    }
                }
                
                if (!$fee_added) {
                    $fee = new WC_Order_Item_Fee();
                    $fee->set_name('Персонализация');
                    $fee->set_amount(5.00);
                    $fee->set_tax_class('');
                    $fee->set_tax_status('none');
                    $fee->set_total(5.00);
                    $order->add_item($fee);
                    $order->calculate_totals();
                    $order->save();
                }
                
                // Add order note
                $order->add_order_note(sprintf('Персонализация: %s', $name));
            }
        }
        
        // Save order notes if any
        if (!empty($_POST['order_comments'])) {
            $comments = sanitize_textarea_field($_POST['order_comments']);
            update_post_meta($order_id, '_order_comments', $comments);
            
            $order = wc_get_order($order_id);
            $order->add_order_note('Забележка на клиента: ' . $comments);
        }
        
        // Log checkout completion
        error_log(sprintf('Tetradkata Checkout: Order #%s created successfully', $order_id));
    }
    
    /**
     * After successful checkout
     */
    public function after_checkout_success($order_id) {
        if (!$order_id) return;
        
        $order = wc_get_order($order_id);
        if (!$order) return;
        
        // Clear any saved form data
        if (!is_admin()) {
            echo '<script>
                Object.keys(localStorage).forEach(key => {
                    if (key.startsWith("checkout_")) {
                        localStorage.removeItem(key);
                    }
                });
            </script>';
        }
        
        // Track successful order
        $this->track_order_event($order, 'order_completed');
    }
    
    /**
     * Send order confirmation email
     */
    public function send_order_confirmation_email($order_id) {
        $order = wc_get_order($order_id);
        if (!$order) return;
        
        $to = $order->get_billing_email();
        $subject = sprintf('Потвърждение за поръчка #%s - Тетрадката', $order->get_order_number());
        
        $message = $this->get_order_confirmation_email_template($order);
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: Тетрадката <noreply@' . parse_url(home_url(), PHP_URL_HOST) . '>'
        );
        
        $sent = wp_mail($to, $subject, $message, $headers);
        
        if ($sent) {
            $order->add_order_note('Имейл за потвърждение изпратен до: ' . $to);
        } else {
            $order->add_order_note('Грешка при изпращане на имейл за потвърждение');
        }
        
        // Send copy to admin
        $admin_email = get_option('admin_email');
        if ($admin_email && $admin_email !== $to) {
            wp_mail(
                $admin_email, 
                '[ADMIN] Нова поръчка #' . $order->get_order_number(),
                $this->get_admin_order_notification_template($order),
                $headers
            );
        }
    }
    
    /**
     * Send completion email
     */
    public function send_completion_email($order_id) {
        $order = wc_get_order($order_id);
        if (!$order) return;
        
        $to = $order->get_billing_email();
        $subject = sprintf('Вашата поръчка #%s е завършена - Тетрадката', $order->get_order_number());
        
        $message = $this->get_completion_email_template($order);
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: Тетрадката <noreply@' . parse_url(home_url(), PHP_URL_HOST) . '>'
        );
        
        wp_mail($to, $subject, $message, $headers);
        $order->add_order_note('Имейл за завършване на поръчка изпратен');
    }
    
    /**
     * Handle payment completion
     */
    public function handle_payment_complete($order_id) {
        $order = wc_get_order($order_id);
        if (!$order) return;
        
        // Update order status based on payment method
        $payment_method = $order->get_payment_method();
        
        if ($payment_method === 'bacs') {
            $order->update_status('on-hold', 'Очаква банков превод');
        } elseif ($payment_method === 'cod') {
            $order->update_status('processing', 'Поръчка с наложен платеж');
        }
        
        // Log payment completion
        error_log(sprintf('Tetradkata Payment: Order #%s payment completed via %s', $order_id, $payment_method));
    }
    
    /**
     * Set COD order status
     */
    public function cod_order_status($status) {
        return 'processing';
    }
    
    /**
     * Display custom order data in admin
     */
    public function display_custom_order_data($order) {
        $personalization = get_post_meta($order->get_id(), '_personalization_enabled', true);
        $personalization_name = get_post_meta($order->get_id(), '_personalization_name', true);
        $order_comments = get_post_meta($order->get_id(), '_order_comments', true);
        
        if ($personalization === 'yes') {
            echo '<div class="address">';
            echo '<p><strong>Персонализация:</strong></p>';
            echo '<div class="edit_address">';
            echo '<p><strong>Име за корицата:</strong> ' . esc_html($personalization_name) . '</p>';
            echo '<p style="color: #e67e22;"><strong>⚠️ ВАЖНО:</strong> Тази поръчка включва персонализация. Срок за изпълнение: 5-7 работни дни.</p>';
            echo '</div>';
            echo '</div>';
        }
        
        if ($order_comments) {
            echo '<div class="address">';
            echo '<p><strong>Забележки на клиента:</strong></p>';
            echo '<div class="edit_address">';
            echo '<p>' . esc_html($order_comments) . '</p>';
            echo '</div>';
            echo '</div>';
        }
    }
    
    /**
     * AJAX apply coupon
     */
    public function ajax_apply_coupon() {
        if (!wp_verify_nonce($_POST['nonce'], 'apply_coupon')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        $coupon_code = sanitize_text_field($_POST['coupon_code']);
        
        if (empty($coupon_code)) {
            wp_send_json_error('Моля въведете код за отстъпка');
            return;
        }
        
        // Check if coupon exists
        $coupon = new WC_Coupon($coupon_code);
        
        if (!$coupon->is_valid()) {
            wp_send_json_error('Невалиден или изтекъл код за отстъпка');
            return;
        }
        
        // Apply coupon
        if (WC()->cart->apply_coupon($coupon_code)) {
            wp_send_json_success('Кодът за отстъпка е приложен успешно!');
        } else {
            wp_send_json_error('Кодът не може да бъде приложен');
        }
    }
    
    /**
     * Handle order status changes
     */
    public function handle_order_status_change($order_id, $old_status, $new_status, $order) {
        // Log status changes
        error_log(sprintf('Tetradkata Order: #%s status changed from %s to %s', $order_id, $old_status, $new_status));
        
        // Send specific emails for status changes
        switch ($new_status) {
            case 'processing':
                if ($old_status === 'pending') {
                    // Order is being processed
                    $this->send_processing_email($order);
                }
                break;
                
            case 'shipped':
                // Custom status for shipped orders
                $this->send_shipping_email($order);
                break;
                
            case 'cancelled':
                $this->send_cancellation_email($order);
                break;
        }
    }
    
    /**
     * Send processing email
     */
    private function send_processing_email($order) {
        $to = $order->get_billing_email();
        $subject = sprintf('Поръчката ви #%s се обработва - Тетрадката', $order->get_order_number());
        
        $message = $this->get_processing_email_template($order);
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: Тетрадката <noreply@' . parse_url(home_url(), PHP_URL_HOST) . '>'
        );
        
        wp_mail($to, $subject, $message, $headers);
    }
    
    /**
     * Track order events for analytics
     */
    private function track_order_event($order, $event) {
        // Track with Google Analytics if available
        if (function_exists('gtag')) {
            // This would be handled client-side in production
        }
        
        // Log for internal analytics
        error_log(sprintf('Tetradkata Analytics: %s - Order #%s - Amount: %s', $event, $order->get_id(), $order->get_total()));
    }
    
    /**
     * Email Templates
     */
    private function get_order_confirmation_email_template($order) {
        $personalization_name = get_post_meta($order->get_id(), '_personalization_name', true);
        $has_personalization = !empty($personalization_name);
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Потвърждение за поръчка</title>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f8f8f8; }
                .container { max-width: 600px; margin: 0 auto; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #B6813A, #E9C887); color: white; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 300; }
                .content { padding: 30px; }
                .order-details { background: #f9f9f9; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .order-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                .order-table th, .order-table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
                .order-table th { background: #f5f5f5; font-weight: 600; }
                .total-row { font-weight: bold; font-size: 16px; background: #fff5e6; }
                .personalization-notice { background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 15px; margin: 20px 0; }
                .personalization-notice h3 { margin-top: 0; color: #856404; }
                .footer { background: #f8f8f8; padding: 20px; text-align: center; font-size: 14px; color: #666; }
                .button { display: inline-block; background: linear-gradient(135deg, #B6813A, #E9C887); color: white; padding: 12px 25px; text-decoration: none; border-radius: 25px; font-weight: 600; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Благодарим за поръчката!</h1>
                    <p>Поръчка #<?php echo $order->get_order_number(); ?></p>
                </div>
                
                <div class="content">
                    <p>Здравейте <strong><?php echo $order->get_billing_first_name(); ?></strong>,</p>
                    
                    <p>Благодарим ви за поръчката! Ето детайлите:</p>
                    
                    <?php if ($has_personalization) : ?>
                    <div class="personalization-notice">
                        <h3>✨ Персонализация</h3>
                        <p><strong>Име за корицата:</strong> <?php echo esc_html($personalization_name); ?></p>
                        <p><strong>⏱️ Забележка:</strong> Поради персонализацията, вашата поръчка ще бъде изпълнена в рамките на 5-7 работни дни.</p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="order-details">
                        <h3>Детайли на поръчката</h3>
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>Продукт</th>
                                    <th>Количество</th>
                                    <th>Цена</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order->get_items() as $item) : ?>
                                <tr>
                                    <td><?php echo $item->get_name(); ?></td>
                                    <td><?php echo $item->get_quantity(); ?></td>
                                    <td><?php echo wc_price($item->get_total()); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                
                                <?php foreach ($order->get_fees() as $fee) : ?>
                                <tr>
                                    <td colspan="2"><?php echo $fee->get_name(); ?></td>
                                    <td><?php echo wc_price($fee->get_total()); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                
                                <tr class="total-row">
                                    <td colspan="2"><strong>Общо:</strong></td>
                                    <td><strong><?php echo wc_price($order->get_total()); ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="order-details">
                        <h3>Информация за доставка</h3>
                        <p><strong>Адрес:</strong><br>
                        <?php echo $order->get_formatted_billing_address(); ?></p>
                        <p><strong>Телефон:</strong> <?php echo $order->get_billing_phone(); ?></p>
                        <p><strong>Имейл:</strong> <?php echo $order->get_billing_email(); ?></p>
                    </div>
                    
                    <div class="order-details">
                        <h3>Начин на плащане</h3>
                        <p><?php echo $order->get_payment_method_title(); ?></p>
                        
                        <?php if ($order->get_payment_method() === 'bacs') : ?>
                        <p><strong>Важно:</strong> Поръчката ви ще бъде обработена след получаване на плащането.</p>
                        <?php endif; ?>
                    </div>
                    
                    <p style="margin-top: 30px;">
                        <a href="<?php echo $order->get_view_order_url(); ?>" class="button">Прегледай поръчката</a>
                    </p>
                    
                    <p>Ако имате въпроси, не се колебайте да се свържете с нас.</p>
                    
                    <p>С благодарност,<br><strong>Екипът на Тетрадката</strong></p>
                </div>
                
                <div class="footer">
                    <p>Тетрадката - Личният ви дневник за пътешествия</p>
                    <p>thenotebook.sales@gmail.com</p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    private function get_admin_order_notification_template($order) {
        $personalization_name = get_post_meta($order->get_id(), '_personalization_name', true);
        $has_personalization = !empty($personalization_name);
        
        ob_start();
        ?>
        <h2>Нова поръчка #<?php echo $order->get_order_number(); ?></h2>
        
        <p><strong>Клиент:</strong> <?php echo $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(); ?></p>
        <p><strong>Имейл:</strong> <?php echo $order->get_billing_email(); ?></p>
        <p><strong>Телефон:</strong> <?php echo $order->get_billing_phone(); ?></p>
        <p><strong>Общо:</strong> <?php echo wc_price($order->get_total()); ?></p>
        <p><strong>Плащане:</strong> <?php echo $order->get_payment_method_title(); ?></p>
        
        <?php if ($has_personalization) : ?>
        <div style="background: #fff3cd; padding: 15px; border-radius: 6px; margin: 15px 0;">
            <h3 style="margin-top: 0; color: #856404;">⚡ ПЕРСОНАЛИЗАЦИЯ НЕОБХОДИМА</h3>
            <p><strong>Име за корицата:</strong> <?php echo esc_html($personalization_name); ?></p>
            <p><strong>Срок:</strong> 5-7 работни дни</p>
        </div>
        <?php endif; ?>
        
        <p><strong>Адрес за доставка:</strong><br><?php echo $order->get_formatted_billing_address(); ?></p>
        
        <p><a href="<?php echo admin_url('post.php?post=' . $order->get_id() . '&action=edit'); ?>">Виж пълната поръчка в администрацията</a></p>
        <?php
        return ob_get_clean();
    }
    
    private function get_completion_email_template($order) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .header { background: linear-gradient(135deg, #B6813A, #E9C887); color: white; padding: 30px; text-align: center; }
                .content { padding: 30px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>🎉 Поръчката ви е завършена!</h1>
            </div>
            <div class="content">
                <p>Здравейте <strong><?php echo $order->get_billing_first_name(); ?></strong>,</p>
                
                <p>Радваме се да ви съобщим, че поръчка #<?php echo $order->get_order_number(); ?> е успешно завършена и изпратена!</p>
                
                <p>Надяваме се да се насладите на вашата Тетрадка и да създадете незабравими спомени.</p>
                
                <p>Ако имате въпроси или се нуждаете от помощ, винаги можете да се свържете с нас.</p>
                
                <p>Благодарим ви отново за доверието!</p>
                
                <p>С уважение,<br><strong>Екипът на Тетрадката</strong></p>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    private function get_processing_email_template($order) {
        $personalization_name = get_post_meta($order->get_id(), '_personalization_name', true);
        $has_personalization = !empty($personalization_name);
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .header { background: linear-gradient(135deg, #B6813A, #E9C887); color: white; padding: 30px; text-align: center; }
                .content { padding: 30px; }
                .processing-notice { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 6px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>⚙️ Обработваме поръчката ви</h1>
            </div>
            <div class="content">
                <p>Здравейте <strong><?php echo $order->get_billing_first_name(); ?></strong>,</p>
                
                <p>Поръчка #<?php echo $order->get_order_number(); ?> се обработва в момента.</p>
                
                <?php if ($has_personalization) : ?>
                <div class="processing-notice">
                    <h3>✨ Персонализация в процес</h3>
                    <p>Вашата тетрадка се персонализира с името: <strong><?php echo esc_html($personalization_name); ?></strong></p>
                    <p>Очаквайте готовността в рамките на 5-7 работни дни.</p>
                </div>
                <?php else : ?>
                <div class="processing-notice">
                    <p><strong>Очакван срок за доставка:</strong> 1-3 работни дни</p>
                </div>
                <?php endif; ?>
                
                <p>Ще получите нотификация, когато поръчката ви бъде изпратена.</p>
                
                <p>Благодарим за търпението!</p>
                
                <p>Екипът на Тетрадката</p>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}

// Initialize the checkout handler
new Tetradkata_Checkout_Handler();

/**
 * Add personalization fee to cart when checkbox is selected
 */
function tetradkata_add_personalization_fee() {
    if (is_admin() && !defined('DOING_AJAX')) return;
    
    if (WC()->session && WC()->session->get('add_personalization')) {
        WC()->cart->add_fee('Персонализация', 5.00);
    }
}
add_action('woocommerce_cart_calculate_fees', 'tetradkata_add_personalization_fee');

/**
 * AJAX handlers for personalization fee
 */
function tetradkata_ajax_add_personalization_fee() {
    if (!wp_verify_nonce($_POST['nonce'], 'tetradkata_nonce')) {
        wp_send_json_error();
        return;
    }
    
    if (WC()->session) {
        WC()->session->set('add_personalization', true);
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_add_personalization_fee', 'tetradkata_ajax_add_personalization_fee');
add_action('wp_ajax_nopriv_add_personalization_fee', 'tetradkata_ajax_add_personalization_fee');

function tetradkata_ajax_remove_personalization_fee() {
    if (!wp_verify_nonce($_POST['nonce'], 'tetradkata_nonce')) {
        wp_send_json_error();
        return;
    }
    
    if (WC()->session) {
        WC()->session->set('add_personalization', false);
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_remove_personalization_fee', 'tetradkata_ajax_remove_personalization_fee');
add_action('wp_ajax_nopriv_remove_personalization_fee', 'tetradkata_ajax_remove_personalization_fee');

/**
 * Disable COD for personalized orders
 */
function tetradkata_disable_cod_for_personalization($available_gateways) {
    if (is_admin()) return $available_gateways;
    
    if (WC()->session && WC()->session->get('add_personalization')) {
        if (isset($available_gateways['cod'])) {
            unset($available_gateways['cod']);
        }
    }
    
    return $available_gateways;
}
add_filter('woocommerce_available_payment_gateways', 'tetradkata_disable_cod_for_personalization');

/**
 * Custom order statuses for better workflow
 */
function tetradkata_register_custom_order_statuses() {
    register_post_status('wc-shipped', array(
        'label'                     => 'Изпратена',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Изпратена <span class="count">(%s)</span>', 'Изпратена <span class="count">(%s)</span>')
    ));
}
add_action('init', 'tetradkata_register_custom_order_statuses');

function tetradkata_add_custom_order_statuses($order_statuses) {
    $order_statuses['wc-shipped'] = 'Изпратена';
    return $order_statuses;
}
add_filter('wc_order_statuses', 'tetradkata_add_custom_order_statuses');

/**
 * Order meta display improvements
 */
function tetradkata_add_order_meta_admin_display($order) {
    $personalization = get_post_meta($order->get_id(), '_personalization_enabled', true);
    if ($personalization === 'yes') {
        echo '<div class="order_data_column" style="background: #fff3cd; padding: 15px; border-radius: 5px; margin-top: 10px;">';
        echo '<h3 style="color: #856404; margin-top: 0;">⚡ Персонализация необходима</h3>';
        echo '<p><strong>Име:</strong> ' . esc_html(get_post_meta($order->get_id(), '_personalization_name', true)) . '</p>';
        echo '<p><strong>Срок:</strong> 5-7 работни дни</p>';
        echo '</div>';
    }
}
add_action('woocommerce_admin_order_data_after_order_details', 'tetradkata_add_order_meta_admin_display');