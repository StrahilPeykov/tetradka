<?php
/**
 * Checkout Page Template - Updated for Unified Experience
 * 
 * @package TetradkataTheme
 */

get_header(); ?>

<div class="checkout-flow-container">
    <div class="container">
        <!-- Unified Step Header -->
        <?php 
            $is_thankyou = function_exists('is_order_received_page') && is_order_received_page();
            $is_order_pay = function_exists('is_checkout_pay_page') && is_checkout_pay_page();
        ?>
        <div class="checkout-header">
            <div class="checkout-steps">
                <div class="step completed clickable" data-step="1" data-url="<?php echo esc_url(wc_get_cart_url()); ?>">
                    <span class="step-number">✓</span>
                    <span class="step-title">Количка</span>
                </div>
                <div class="step-line completed"></div>
                <div class="step <?php echo $is_thankyou ? '' : 'active'; ?>" data-step="2">
                    <span class="step-number">2</span>
                    <span class="step-title">Плащане</span>
                </div>
                <div class="step-line <?php echo $is_thankyou ? 'completed' : ''; ?>"></div>
                <div class="step <?php echo $is_thankyou ? 'active' : ''; ?>" data-step="3">
                    <span class="step-number">3</span>
                    <span class="step-title">Завършено</span>
                </div>
            </div>
            <h1><?php echo $is_thankyou ? 'Благодарим за поръчката!' : 'Завършване на поръчката'; ?></h1>
            <p class="step-description">
                <?php echo $is_thankyou 
                    ? 'Поръчката е приета и се обработва. Детайли по-долу.' 
                    : ( $is_order_pay ? 'Пренасочване към сигурна страница за плащане…' : 'Въведете данните си за доставка и изберете начин на плащане' ); ?>
            </p>
        </div>

        <div class="checkout-content">
            <?php
            // Check if WooCommerce is active
            if (class_exists('WooCommerce')) {
                // Thank you / order received endpoint
                if ($is_thankyou) {
                    // Universal Thank You section with graceful order lookup
                    $order = false;
                    $order_id = absint(get_query_var('order-received'));
                    if ($order_id) {
                        $order = wc_get_order($order_id);
                    }
                    if (!$order && isset($_GET['key'])) {
                        $order_key = wc_clean(wp_unslash($_GET['key']));
                        $order_id_by_key = wc_get_order_id_by_order_key($order_key);
                        if ($order_id_by_key) {
                            $order = wc_get_order($order_id_by_key);
                        }
                    }
                    if (!$order && is_user_logged_in()) {
                        // Fallback: last customer order if available
                        $last = wc_get_customer_last_order(get_current_user_id(), 'any');
                        if ($last instanceof WC_Order) {
                            $order = $last;
                        }
                    }

                    wc_print_notices();
                    ?>
                    <div class="thankyou-wrapper">
                        <div class="thankyou-card">
                            <div class="thankyou-icon">✅</div>
                            <h2>Благодарим за поръчката!</h2>
                            <p class="thankyou-sub">
                                Ще получите имейл с потвърждение и детайли за поръчката.
                            </p>

                            <?php if ($order instanceof WC_Order) : ?>
                                <div class="thankyou-details">
                                    <div class="detail"><strong>Номер на поръчка:</strong> <?php echo esc_html($order->get_order_number()); ?></div>
                                    <?php if ($order->get_date_created()) : ?>
                                        <div class="detail"><strong>Дата:</strong> <?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></div>
                                    <?php endif; ?>
                                    <?php if ($order->get_billing_email()) : ?>
                                        <div class="detail"><strong>Имейл:</strong> <?php echo esc_html($order->get_billing_email()); ?></div>
                                    <?php endif; ?>
                                    <div class="detail"><strong>Обща сума:</strong> <?php echo wp_kses_post($order->get_formatted_order_total()); ?></div>
                                    <?php if ($order->get_payment_method_title()) : ?>
                                        <div class="detail"><strong>Метод на плащане:</strong> <?php echo esc_html($order->get_payment_method_title()); ?></div>
                                    <?php endif; ?>
                                </div>

                                <h3 class="items-title">Артикули</h3>
                                <ul class="thankyou-items">
                                    <?php foreach ($order->get_items() as $item_id => $item) : ?>
                                        <li class="thankyou-item">
                                            <span class="item-name"><?php echo esc_html($item->get_name()); ?></span>
                                            <span class="item-qty">× <?php echo esc_html($item->get_quantity()); ?></span>
                                            <span class="item-total"><?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>

                                <div class="thankyou-actions">
                                    <a class="btn btn-primary" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Към магазина</a>
                                    <?php if (is_user_logged_in()) : ?>
                                        <a class="btn btn-secondary" href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>">Моите поръчки</a>
                                    <?php endif; ?>
                                </div>
                            <?php else : ?>
                                <div class="thankyou-generic">
                                    <p>Ако не виждате детайли тук, проверете имейла си за потвърждение. При въпроси се свържете с нас и посочете имейла, с който направихте поръчката.</p>
                                    <div class="thankyou-actions">
                                        <a class="btn btn-primary" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Обратно в магазина</a>
                                        <?php if (is_user_logged_in()) : ?>
                                            <a class="btn btn-secondary" href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>">Моите поръчки</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                // Order payment endpoint (e.g. myPOS redirect target)
                } elseif ($is_order_pay) {
                    wc_print_notices();
                    // Attempt immediate gateway receipt rendering for redirect-based payments (e.g., myPOS)
                    $pay_order_id = absint(get_query_var('order-pay'));
                    $pay_order    = $pay_order_id ? wc_get_order($pay_order_id) : false;

                    if ($pay_order instanceof WC_Order) {
                        // Let the selected gateway render its receipt/redirect form
                        do_action('woocommerce_receipt_' . $pay_order->get_payment_method(), $pay_order->get_id());
                    } else {
                        // Fallback to WooCommerce pay form
                        echo do_shortcode('[woocommerce_checkout]');
                    }

                // Checkout form when cart has items
                } elseif (WC()->cart && !WC()->cart->is_empty()) {
                    ?>
                    <div class="checkout-wrapper">
                        <?php
                        // Output any WooCommerce notices
                        wc_print_notices();
                        
                        // Checkout form
                        $checkout = WC()->checkout();

                        // Allow plugins/gateways to hook before the checkout form (e.g. login prompt, extra fields)
                        do_action('woocommerce_before_checkout_form', $checkout);

                        // If registration is required and not enabled, ensure users are logged in.
                        if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
                            echo '<p class="woocommerce-error">' . esc_html__('Трябва да влезете в профила си, за да продължите с поръчката.', 'woocommerce') . '</p>';
                            // Stop rendering the checkout form
                        } else {
                        ?>
                        
                        <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

                            <div class="checkout-layout">
                                <div class="checkout-main">
                                    
                                    <!-- Coupon Code Section -->
                                    <?php if (wc_coupons_enabled()) : ?>
                                        <div class="checkout-section coupon-section">
                                            <h3 class="section-title">
                                                <span class="icon">🎫</span>
                                                Код за отстъпка
                                            </h3>
                                            
                                            <div class="coupon-wrapper">
                                                <?php woocommerce_checkout_coupon_form(); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Customer Details Section -->
                                    <div class="checkout-section customer-details">
                                        <h3 class="section-title">
                                            <span class="icon">👤</span>
                                            Данни за клиента
                                        </h3>
                                        
                                        <?php if ($checkout->get_checkout_fields()) : ?>
                                            <?php do_action('woocommerce_checkout_before_customer_details'); ?>

                                            <div class="billing-fields">
                                                <?php do_action('woocommerce_checkout_billing'); ?>
                                            </div>

                                            <?php do_action('woocommerce_checkout_after_customer_details'); ?>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Shipping Section -->
                                    <div class="checkout-section shipping-details">
                                        <h3 class="section-title">
                                            <span class="icon">📦</span>
                                            Доставка
                                        </h3>
                                        
                                        <div class="shipping-toggle">
                                            <label class="checkbox-label">
                                                <input type="checkbox" id="ship-to-different-address-checkbox" name="ship_to_different_address" value="1">
                                                <span class="checkbox-custom"></span>
                                                <span>Доставка на различен адрес?</span>
                                            </label>
                                        </div>
                                        
                                        <div class="shipping-fields" style="display: none;">
                                            <?php do_action('woocommerce_checkout_shipping'); ?>
                                        </div>
                                        
                                        <div class="shipping-methods">
                                            <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                                                <h4>Избор на доставка:</h4>
                                                <?php do_action('woocommerce_review_order_before_shipping'); ?>
                                                <?php wc_cart_totals_shipping_html(); ?>
                                                <?php do_action('woocommerce_review_order_after_shipping'); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Personalization Section -->
                                    <div class="checkout-section personalization-section">
                                        <h3 class="section-title">
                                            <span class="icon">✨</span>
                                            Персонализация
                                        </h3>
                                        
                                        <div class="personalization-wrapper">
                                            <label class="checkbox-label personalization-toggle">
                                                <input type="checkbox" id="add-personalization" name="add_personalization" value="yes">
                                                <span class="checkbox-custom"></span>
                                                <span>Желаете ли персонализация? (+5.00 лв.)</span>
                                            </label>
                                            
                                            <div class="personalization-fields" style="display: none;">
                                                <div class="form-row">
                                                    <label for="personalization_name">Име за корицата <abbr class="required" title="задължително">*</abbr></label>
                                                    <input type="text" 
                                                           class="input-text" 
                                                           name="personalization_name" 
                                                           id="personalization_name" 
                                                           placeholder="Въведете име (кирилица или латиница)"
                                                           maxlength="50">
                                                    <p class="form-row-description">Името ще бъде изписано на корицата на тетрадката</p>
                                                </div>
                                                
                                                <div class="personalization-note">
                                                    <span class="dashicons dashicons-info"></span>
                                                    <p>При избор на персонализация, наложен платеж не е наличен като опция за плащане.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Information -->
                                    <div class="checkout-section additional-info">
                                        <h3 class="section-title">
                                            <span class="icon">📝</span>
                                            Допълнителна информация
                                        </h3>
                                        
                                        <div class="order-notes-field">
                                            <?php do_action('woocommerce_before_order_notes', $checkout); ?>
                                            
                                            <?php if (apply_filters('woocommerce_enable_order_notes_field', 'yes' === get_option('woocommerce_enable_order_comments', 'yes'))) : ?>
                                                <div class="form-row notes">
                                                    <label for="order_comments">Забележки към поръчката <span class="optional">(незадължително)</span></label>
                                                    <textarea name="order_comments" 
                                                              class="input-text" 
                                                              id="order_comments" 
                                                              placeholder="Забележки към поръчката, напр. специални инструкции за доставка." 
                                                              rows="4"></textarea>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php do_action('woocommerce_after_order_notes', $checkout); ?>
                                        </div>
                                    </div>

                                </div>

                                <div class="checkout-sidebar">
                                    <div class="order-summary sticky-summary">
                                        <div class="section-header">
                                            <h3>
                                                <span class="section-icon">📋</span>
                                                Вашата поръчка
                                            </h3>
                                            <button type="button" class="edit-cart-btn" onclick="window.location.href='<?php echo esc_url(wc_get_cart_url()); ?>'" title="Редактиraj количката">
                                                <span class="dashicons dashicons-edit"></span>
                                                Редактирай
                                            </button>
                                        </div>
                                        
                                        <?php do_action('woocommerce_checkout_before_order_review'); ?>

                                        <div id="order_review" class="woocommerce-checkout-review-order">
                                            <?php do_action('woocommerce_checkout_order_review'); ?>
                                        </div>

                                        <?php do_action('woocommerce_checkout_after_order_review'); ?>
                                        
                                        <div class="checkout-benefits">
                                            <div class="benefit-item">
                                                <span class="dashicons dashicons-shield-alt"></span>
                                                <span>Сигурно плащане</span>
                                            </div>
                                            <div class="benefit-item">
                                                <span class="dashicons dashicons-update"></span>
                                                <span>14 дни за връщане</span>
                                            </div>
                                            <div class="benefit-item">
                                                <span class="dashicons dashicons-yes-alt"></span>
                                                <span>Безплатна доставка над 50 лв.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>

                        <?php 
                        // Allow plugins/gateways to hook after the checkout form
                        do_action('woocommerce_after_checkout_form', $checkout);
                        }
                        ?>
                        
                    </div>
                    <?php
                } else {
                    // Empty cart - redirect to shop
                    ?>
                    <div class="empty-checkout">
                        <div class="empty-checkout-content">
                            <div class="empty-icon">🛒</div>
                            <h2>Количката ви е празна</h2>
                            <p>Добавете продукти в количката, за да продължите с поръчката.</p>
                            <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="btn btn-primary">
                                <span class="dashicons dashicons-cart"></span>
                                Към магазина
                            </a>
                        </div>
                    </div>
                    
                    <script>
                    // Auto-redirect to shop if cart is empty
                    setTimeout(function() {
                        window.location.href = '<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>';
                    }, 3000);
                    </script>
                    <?php
                }
            } else {
                // WooCommerce not active
                ?>
                <div class="woocommerce-inactive">
                    <h2>Магазинът не е достъпен</h2>
                    <p>За момента магазинът не е активен. Моля, опитайте по-късно.</p>
                    <a href="<?php echo home_url(); ?>" class="btn btn-primary">Към началото</a>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<style>
/* Unified Checkout Flow Design */
.checkout-flow-container {
    background: #fafafa;
    padding: 100px 0 60px;
    min-height: calc(100vh - 200px);
}

/* Step Header */
.checkout-header {
    text-align: center;
    margin-bottom: 50px;
}

.checkout-steps {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 40px;
    padding: 30px;
    background: var(--white);
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.step {
    display: flex;
    align-items: center;
    gap: 10px;
    opacity: 0.5;
    transition: all 0.3s ease;
    padding: 8px 12px;
    border-radius: 10px;
    cursor: default;
    position: relative;
}

.step.active {
    opacity: 1;
    background: rgba(182, 129, 58, 0.1);
}

.step.completed {
    opacity: 1;
    background: rgba(34, 197, 94, 0.1);
}

.step.clickable {
    cursor: pointer;
}

.step.clickable:hover {
    background: rgba(182, 129, 58, 0.15);
    transform: translateY(-1px);
}

.step-number {
    width: 35px;
    height: 35px;
    background: var(--warm-beige);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: var(--charcoal);
    font-size: 16px;
    transition: all 0.3s ease;
}

.step.active .step-number {
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
    color: var(--white);
}

.step.completed .step-number {
    background: #22c55e;
    color: var(--white);
    font-size: 14px;
}

.step-title {
    font-weight: 600;
    color: var(--charcoal);
    font-size: 14px;
}

.step-line {
    width: 80px;
    height: 2px;
    background: var(--warm-beige);
    margin: 0 20px;
    transition: background 0.3s ease;
}

.step-line.completed {
    background: #22c55e;
}

.checkout-header h1 {
    color: var(--charcoal);
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 15px;
}

.step-description {
    color: #666;
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
}

/* Layout */
.checkout-wrapper {
    width: 100%;
}

.checkout-layout {
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: 30px;
}

/* Sections */
.checkout-section {
    background: var(--white);
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: box-shadow 0.3s ease;
}

.checkout-section:hover {
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.section-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 25px;
    color: var(--charcoal);
    font-size: 1.3rem;
    font-weight: 600;
}

.section-title .icon {
    font-size: 1.5rem;
    margin-right: 12px;
}

.edit-cart-btn {
    background: none;
    border: 2px solid var(--warm-beige);
    color: var(--charcoal);
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 13px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.edit-cart-btn:hover {
    border-color: var(--gold-start);
    color: var(--gold-start);
    transform: translateY(-1px);
}

/* Form Styling (same as existing) */
.form-row {
    margin-bottom: 20px;
}

.form-row label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--charcoal);
    font-size: 14px;
}

.form-row .optional {
    font-weight: 400;
    color: #999;
    font-size: 13px;
}

.form-row input[type="text"],
.form-row input[type="email"],
.form-row input[type="tel"],
.form-row select,
.form-row textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s ease;
    background: #fafafa;
}

.form-row input:focus,
.form-row select:focus,
.form-row textarea:focus {
    outline: none;
    border-color: var(--gold-start);
    background: var(--white);
    box-shadow: 0 0 0 3px rgba(182, 129, 58, 0.1);
}

/* Checkbox Styling */
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    font-weight: 600;
    color: var(--charcoal);
    padding: 15px 20px;
    background: #f5f5f5;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.checkbox-label:hover {
    background: var(--warm-beige);
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkbox-custom {
    width: 22px;
    height: 22px;
    border: 2px solid var(--primary-taupe);
    border-radius: 5px;
    position: relative;
    transition: all 0.3s ease;
}

.checkbox-label input[type="checkbox"]:checked ~ .checkbox-custom {
    background: var(--gold-start);
    border-color: var(--gold-start);
}

.checkbox-label input[type="checkbox"]:checked ~ .checkbox-custom::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: var(--white);
    font-weight: bold;
    font-size: 14px;
}

/* Coupon Section */
.coupon-section {
    background: linear-gradient(135deg, #e8f5e8 0%, #fff 100%);
    border: 2px solid #c8e6c9;
}

.coupon-wrapper .woocommerce-form-coupon {
    background: none;
    border: none;
    padding: 0;
    margin: 0;
}

.coupon-wrapper .form-row {
    display: flex;
    gap: 15px;
    align-items: end;
    margin: 0;
}

.coupon-wrapper .form-row .input-text {
    flex: 1;
    padding: 12px 16px;
    border: 2px solid #4caf50;
    border-radius: 10px;
    font-size: 15px;
    background: #f1f8e9;
}

.coupon-wrapper .form-row .input-text:focus {
    border-color: #2e7d32;
    background: var(--white);
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
}

.coupon-wrapper .form-row .button {
    background: #4caf50 !important;
    color: var(--white) !important;
    border: none !important;
    padding: 12px 25px !important;
    border-radius: 10px !important;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.coupon-wrapper .form-row .button:hover {
    background: #2e7d32 !important;
    transform: translateY(-1px);
}

/* Personalization Section */
.personalization-section {
    background: linear-gradient(135deg, #fff5e6 0%, #fff 100%);
    border: 2px solid #ffe0b3;
}

.personalization-toggle {
    background: #fff9f0;
    border: 1px solid #ffcc80;
}

.personalization-fields {
    margin-top: 20px;
    padding: 20px;
    background: rgba(255,255,255,0.8);
    border-radius: 10px;
    animation: slideDown 0.3s ease-out;
}

.personalization-note {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-top: 15px;
    padding: 15px;
    background: #fff3e0;
    border-radius: 8px;
    border-left: 3px solid #ff9800;
}

.personalization-note .dashicons {
    color: #ff9800;
    font-size: 20px;
    flex-shrink: 0;
}

.personalization-note p {
    margin: 0;
    font-size: 13px;
    color: #666;
}

/* Order Summary Sidebar */
.order-summary {
    background: var(--white);
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.sticky-summary {
    position: sticky;
    top: 100px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--warm-beige);
}

.section-header h3 {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--charcoal);
    margin: 0;
}

.section-icon {
    font-size: 1.3rem;
}

/* Order Review Table */
.woocommerce-checkout-review-order-table {
    width: 100%;
    margin-bottom: 25px;
}

.woocommerce-checkout-review-order-table th,
.woocommerce-checkout-review-order-table td {
    padding: 15px 0;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
    font-size: 14px;
}

.woocommerce-checkout-review-order-table .product-name {
    font-weight: 600;
    color: var(--charcoal);
}

.woocommerce-checkout-review-order-table .product-total {
    text-align: right;
    font-weight: 600;
}

.woocommerce-checkout-review-order-table .order-total {
    border-bottom: none;
    border-top: 2px solid var(--gold-start);
}

.woocommerce-checkout-review-order-table .order-total th,
.woocommerce-checkout-review-order-table .order-total td {
    padding-top: 20px;
    font-size: 18px;
    font-weight: 700;
    color: var(--charcoal);
}

.woocommerce-checkout-review-order-table .order-total .woocommerce-Price-amount {
    color: var(--gold-start);
}

/* Payment Methods */
.woocommerce-checkout-payment {
    margin-top: 30px;
    padding-top: 30px;
    border-top: 1px solid #e0e0e0;
}

.woocommerce-checkout-payment .payment_methods {
    list-style: none;
    padding: 0;
    margin: 0 0 20px;
}

.payment_method {
    margin-bottom: 15px;
}

.payment_method label {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 20px;
    background: #f5f5f5;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.payment_method input[type="radio"] {
    width: auto;
    margin: 0;
}

.payment_method label:hover {
    background: var(--warm-beige);
}

.payment_method input[type="radio"]:checked + label {
    background: #fff5e6;
    border-color: var(--gold-start);
}

/* Place Order Button */
#place_order {
    width: 100%;
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end)) !important;
    color: var(--white) !important;
    border: none !important;
    padding: 18px 30px !important;
    font-size: 17px !important;
    font-weight: 700 !important;
    border-radius: 50px !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    margin-top: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    overflow: hidden;
}

#place_order:hover {
    background: linear-gradient(135deg, var(--gold-end), var(--gold-start)) !important;
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(182, 129, 58, 0.4);
}

#place_order::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.6s ease;
}

#place_order:hover::before {
    left: 100%;
}

/* Checkout Benefits */
.checkout-benefits {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 25px;
    padding-top: 25px;
    border-top: 1px solid #e0e0e0;
}

.benefit-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    color: var(--charcoal);
}

.benefit-item .dashicons {
    color: var(--gold-start);
    font-size: 18px;
}

/* Empty States */
.empty-checkout {
    text-align: center;
    padding: 100px 20px;
    background: var(--white);
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.empty-icon {
    font-size: 5rem;
    margin-bottom: 20px;
    opacity: 0.3;
}

.empty-checkout h2 {
    color: var(--charcoal);
    margin-bottom: 15px;
}

.empty-checkout p {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 30px;
}

/* Privacy Policy Text */
.woocommerce-privacy-policy-text {
    font-size: 13px;
    color: #666;
    line-height: 1.6;
    margin-top: 20px;
    padding: 15px;
    background: #f5f5f5;
    border-radius: 8px;
}

.woocommerce-privacy-policy-text a {
    color: var(--gold-start);
    text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .checkout-layout {
        grid-template-columns: 1fr;
    }
    
    .sticky-summary {
        position: relative;
        top: 0;
    }
    
    .order-summary {
        margin-top: 30px;
    }
}

@media (max-width: 768px) {
    .checkout-flow-container {
        padding: 80px 0 40px;
    }
    
    .checkout-steps {
        padding: 20px 15px;
        gap: 10px;
    }
    
    .step-line {
        width: 40px;
        margin: 0 10px;
    }
    
    .step-title {
        display: none;
    }
    
    .checkout-header h1 {
        font-size: 1.8rem;
    }
    
    .checkout-section {
        padding: 20px;
        margin-bottom: 15px;
    }
    
    .section-title {
        font-size: 1.1rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .edit-cart-btn {
        align-self: flex-end;
    }
    
    .checkbox-label {
        padding: 12px 15px;
        font-size: 14px;
    }
}

/* Animations */
@keyframes slideDown {
    from {
        opacity: 0;
        max-height: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        max-height: 300px;
        transform: translateY(0);
    }
}

/* Loading State */
.woocommerce-checkout.processing {
    position: relative;
    pointer-events: none;
}

.woocommerce-checkout.processing::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.9);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.woocommerce-checkout.processing::before {
    content: 'Обработва поръчката...';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 10000;
    background: var(--white);
    padding: 25px 35px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    font-weight: 700;
    color: var(--charcoal);
    font-size: 16px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Step navigation - allow clicking on previous steps
    $('.step.clickable').on('click', function() {
        const url = $(this).data('url');
        if (url) {
            window.location.href = url;
        }
    });
    
    // Coupon toggle functionality
    $('.woocommerce-form-coupon-toggle .showcoupon').on('click', function(e) {
        e.preventDefault();
        $('.checkout_coupon').slideToggle(300);
    });
    
    // Ship to different address toggle
    $('#ship-to-different-address-checkbox').on('change', function() {
        if ($(this).is(':checked')) {
            $('.shipping-fields').slideDown(300);
        } else {
            $('.shipping-fields').slideUp(300);
        }
    });
    
    // Personalization toggle
    $('#add-personalization').on('change', function() {
        const isChecked = $(this).is(':checked');
        
        if (isChecked) {
            $('.personalization-fields').slideDown(300);
            $('#personalization_name').attr('required', true);
            
            // Disable COD payment method
            $('.payment_method_cod').addClass('disabled');
            $('#payment_method_cod').prop('disabled', true);
            
            // If COD was selected, select the first available payment method
            if ($('#payment_method_cod').is(':checked')) {
                $('input[name="payment_method"]:not(#payment_method_cod):first').prop('checked', true).trigger('change');
            }
            
            // Add personalization fee to the order
            addPersonalizationFee();
        } else {
            $('.personalization-fields').slideUp(300);
            $('#personalization_name').attr('required', false).val('');
            
            // Enable COD payment method
            $('.payment_method_cod').removeClass('disabled');
            $('#payment_method_cod').prop('disabled', false);
            
            // Remove personalization fee
            removePersonalizationFee();
        }
    });
    
    // Function to add personalization fee via AJAX
    function addPersonalizationFee() {
        $.ajax({
            url: tetradkata_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'add_personalization_fee',
                nonce: tetradkata_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $(document.body).trigger('update_checkout');
                }
            }
        });
    }
    
    // Function to remove personalization fee via AJAX
    function removePersonalizationFee() {
        $.ajax({
            url: tetradkata_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'remove_personalization_fee',
                nonce: tetradkata_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $(document.body).trigger('update_checkout');
                }
            }
        });
    }
    
    // Validate personalization name before submitting
    $('form.checkout').on('checkout_place_order', function() {
        if ($('#add-personalization').is(':checked')) {
            const personalizationName = $('#personalization_name').val().trim();
            
            if (!personalizationName) {
                alert('Моля, въведете име за персонализация');
                $('#personalization_name').focus();
                return false;
            }
            
            if (personalizationName.length > 50) {
                alert('Името за персонализация не може да бъде по-дълго от 50 символа');
                $('#personalization_name').focus();
                return false;
            }
        }
        return true;
    });
    
    // Smooth scroll to sections on title click
    $('.section-title').on('click', function() {
        const section = $(this).closest('.checkout-section');
        if (section.length) {
            $('html, body').animate({
                scrollTop: section.offset().top - 100
            }, 500);
        }
    });
    
    // Auto-format phone number
    $('input[type="tel"]').on('input', function() {
        let value = $(this).val().replace(/\s/g, '');
        if (value.length > 0 && !value.startsWith('+')) {
            value = '+359' + value;
        }
        $(this).val(value);
    });
    
    // Add visual feedback for form completion
    $('input, select, textarea').on('blur', function() {
        if ($(this).val()) {
            $(this).addClass('completed');
        } else {
            $(this).removeClass('completed');
        }
    });
});
</script>

<?php get_footer(); ?>
