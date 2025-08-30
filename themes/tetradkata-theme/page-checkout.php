<?php
/**
 * Production-Ready Checkout Page Template
 * Simplified for Bulgaria-only shipping with robust payment processing
 * 
 * @package TetradkataTheme
 */

get_header(); ?>

<div class="checkout-flow-container">
    <div class="container">
        <!-- Progress Steps -->
        <div class="checkout-header">
            <div class="checkout-steps">
                <div class="step completed clickable" data-step="1" data-url="<?php echo esc_url(wc_get_cart_url()); ?>">
                    <span class="step-number">✓</span>
                    <span class="step-title">Количка</span>
                </div>
                <div class="step-line completed"></div>
                <div class="step active" data-step="2">
                    <span class="step-number">2</span>
                    <span class="step-title">Поръчка</span>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="3">
                    <span class="step-number">3</span>
                    <span class="step-title">Готово</span>
                </div>
            </div>
            <h1>Завършване на поръчката</h1>
            <p class="step-description">Попълнете данните си за доставка в България</p>
        </div>

        <?php if (class_exists('WooCommerce') && WC()->cart && !WC()->cart->is_empty()) : ?>
            
            <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data" novalidate>

                <div class="checkout-layout">
                    <!-- Main Form Section -->
                    <div class="checkout-main">
                        
                        <!-- Customer Details -->
                        <div class="checkout-section">
                            <h3 class="section-title">
                                <span class="icon">👤</span>
                                Данни за контакт
                            </h3>
                            
                            <div class="form-grid">
                                <div class="form-row form-row-first">
                                    <label for="billing_first_name">Име <span class="required">*</span></label>
                                    <input type="text" class="input-text" name="billing_first_name" id="billing_first_name" required>
                                </div>
                                
                                <div class="form-row form-row-last">
                                    <label for="billing_last_name">Фамилия <span class="required">*</span></label>
                                    <input type="text" class="input-text" name="billing_last_name" id="billing_last_name" required>
                                </div>
                                
                                <div class="form-row form-row-wide">
                                    <label for="billing_email">Имейл адрес <span class="required">*</span></label>
                                    <input type="email" class="input-text" name="billing_email" id="billing_email" required>
                                </div>
                                
                                <div class="form-row form-row-wide">
                                    <label for="billing_phone">Телефон <span class="required">*</span></label>
                                    <input type="tel" class="input-text" name="billing_phone" id="billing_phone" placeholder="+359 888 123 456" required>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Address (Bulgaria Only) -->
                        <div class="checkout-section">
                            <h3 class="section-title">
                                <span class="icon">📍</span>
                                Адрес за доставка в България
                            </h3>
                            
                            <div class="form-grid">
                                <div class="form-row form-row-first">
                                    <label for="billing_city">Град <span class="required">*</span></label>
                                    <input type="text" class="input-text" name="billing_city" id="billing_city" placeholder="София" required>
                                </div>
                                
                                <div class="form-row form-row-last">
                                    <label for="billing_postcode">Пощенски код</label>
                                    <input type="text" class="input-text" name="billing_postcode" id="billing_postcode" placeholder="1000">
                                </div>
                                
                                <div class="form-row form-row-wide">
                                    <label for="billing_address_1">Адрес <span class="required">*</span></label>
                                    <input type="text" class="input-text" name="billing_address_1" id="billing_address_1" placeholder="ул. Витоша 1" required>
                                </div>
                                
                                <div class="form-row form-row-wide">
                                    <label for="billing_address_2">Допълнителен адрес</label>
                                    <input type="text" class="input-text" name="billing_address_2" id="billing_address_2" placeholder="Апартамент, етаж, блок (незадължително)">
                                </div>
                            </div>
                            
                            <!-- Hidden country field set to Bulgaria -->
                            <input type="hidden" name="billing_country" value="BG">
                            <input type="hidden" name="shipping_country" value="BG">
                        </div>

                        <!-- Personalization -->
                        <div class="checkout-section personalization-section">
                            <h3 class="section-title">
                                <span class="icon">✨</span>
                                Персонализация
                                <span class="price-addon">+5.00 лв.</span>
                            </h3>
                            
                            <label class="checkbox-wrapper">
                                <input type="checkbox" id="add-personalization" name="add_personalization" value="yes">
                                <span class="checkmark"></span>
                                <span class="checkbox-text">Искам персонализация с име на корицата</span>
                            </label>
                            
                            <div class="personalization-field" style="display: none;">
                                <label for="personalization_name">Име за корицата <span class="required">*</span></label>
                                <input type="text" name="personalization_name" id="personalization_name" 
                                       placeholder="Въведете име (до 20 символа)" maxlength="20" class="input-text">
                                <small class="field-note">Името ще бъде изписано златисто на корицата</small>
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="checkout-section notes-section">
                            <h3 class="section-title">
                                <span class="icon">📝</span>
                                Забележки към поръчката
                            </h3>
                            
                            <div class="form-row">
                                <textarea name="order_comments" id="order_comments" class="input-text" rows="3" 
                                          placeholder="Специални инструкции за доставка (незадължително)"></textarea>
                            </div>
                        </div>

                        <!-- Coupon Code - Simplified -->
                        <div class="checkout-section coupon-section">
                            <h3 class="section-title">
                                <span class="icon">🎫</span>
                                Код за отстъпка
                            </h3>
                            
                            <div class="coupon-input-wrapper">
                                <input type="text" name="coupon_code" id="coupon_code" class="input-text" placeholder="Въведете код">
                                <button type="button" id="apply-coupon-btn" class="btn-coupon">Приложи</button>
                            </div>
                            <div id="coupon-message" class="coupon-message" style="display: none;"></div>
                        </div>
                    </div>

                    <!-- Order Summary Sidebar -->
                    <div class="checkout-sidebar">
                        <div class="order-summary">
                            <div class="section-header">
                                <h3>
                                    <span class="section-icon">📋</span>
                                    Вашата поръчка
                                </h3>
                            </div>
                            
                            <!-- Order Review -->
                            <div id="order_review" class="woocommerce-checkout-review-order">
                                <table class="shop_table woocommerce-checkout-review-order-table">
                                    <thead>
                                        <tr>
                                            <th class="product-name">Продукт</th>
                                            <th class="product-total">Сума</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                                            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                                            
                                            if ($_product && $_product->exists() && $cart_item['quantity'] > 0) {
                                                ?>
                                                <tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                                                    <td class="product-name">
                                                        <?php echo apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key); ?>
                                                        <strong class="product-quantity">&times; <?php echo $cart_item['quantity']; ?></strong>
                                                    </td>
                                                    <td class="product-total">
                                                        <?php echo WC()->cart->get_product_subtotal($_product, $cart_item['quantity']); ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="cart-subtotal">
                                            <th>Междинна сума</th>
                                            <td><?php wc_cart_totals_subtotal_html(); ?></td>
                                        </tr>

                                        <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
                                            <tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
                                                <th><?php wc_cart_totals_coupon_label($coupon); ?></th>
                                                <td><?php wc_cart_totals_coupon_html($coupon); ?></td>
                                            </tr>
                                        <?php endforeach; ?>

                                        <?php if (WC()->cart->needs_shipping()) : ?>
                                            <tr class="woocommerce-shipping-totals shipping">
                                                <th>Доставка</th>
                                                <td data-title="Доставка">
                                                    <div id="shipping-methods">
                                                        <?php wc_cart_totals_shipping_html(); ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php foreach (WC()->cart->get_fees() as $fee) : ?>
                                            <tr class="fee">
                                                <th><?php echo esc_html($fee->name); ?></th>
                                                <td><?php wc_cart_totals_fee_html($fee); ?></td>
                                            </tr>
                                        <?php endforeach; ?>

                                        <tr class="order-total">
                                            <th>Общо</th>
                                            <td><strong id="order-total-amount"><?php wc_cart_totals_order_total_html(); ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Payment Methods -->
                            <div class="payment-section">
                                <h4 class="payment-title">Начин на плащане</h4>
                                
                                <div id="payment" class="woocommerce-checkout-payment">
                                    <ul class="payment_methods">
                                        <li class="payment_method payment_method_cod">
                                            <input id="payment_method_cod" type="radio" class="input-radio" name="payment_method" value="cod" checked>
                                            <label for="payment_method_cod">
                                                <span class="payment-icon">💰</span>
                                                Наложен платеж
                                                <small>Плащане при получаване</small>
                                            </label>
                                        </li>
                                        
                                        <li class="payment_method payment_method_bacs">
                                            <input id="payment_method_bacs" type="radio" class="input-radio" name="payment_method" value="bacs">
                                            <label for="payment_method_bacs">
                                                <span class="payment-icon">🏦</span>
                                                Банков превод
                                                <small>Преди изпращане на поръчката</small>
                                            </label>
                                        </li>
                                    </ul>

                                    <div class="payment-note" id="cod-note">
                                        <p><strong>Наложен платеж:</strong> Плащате при получаване на пратката. Таксата за наложен платеж е 2.90 лв.</p>
                                    </div>
                                    
                                    <div class="payment-note" id="bacs-note" style="display: none;">
                                        <p><strong>Банков превод:</strong> Ще получите имейл с банковите ни данни за превода.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Privacy Policy Agreement -->
                            <div class="privacy-section">
                                <label class="checkbox-wrapper privacy-checkbox">
                                    <input type="checkbox" id="terms" name="terms" required>
                                    <span class="checkmark"></span>
                                    <span class="checkbox-text">
                                        Съгласявам се с 
                                        <a href="<?php echo get_privacy_policy_url(); ?>" target="_blank">политиката за поверителност</a>
                                        и 
                                        <a href="<?php echo home_url('/terms'); ?>" target="_blank">общите условия</a>
                                        <span class="required">*</span>
                                    </span>
                                </label>
                            </div>

                            <!-- Place Order Button -->
                            <button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="Завърши поръчката">
                                <span class="btn-text">Завърши поръчката</span>
                                <span class="btn-loading" style="display: none;">
                                    <div class="loading"></div>
                                    Обработва...
                                </span>
                            </button>

                            <!-- Trust Indicators -->
                            <div class="trust-indicators">
                                <div class="trust-item">
                                    <span class="dashicons dashicons-shield-alt"></span>
                                    <span>Сигурни плащания</span>
                                </div>
                                <div class="trust-item">
                                    <span class="dashicons dashicons-yes-alt"></span>
                                    <span>Безплатна доставка над 50 лв.</span>
                                </div>
                                <div class="trust-item">
                                    <span class="dashicons dashicons-update"></span>
                                    <span>14 дни за връщане</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
            </form>

        <?php else : ?>
            <!-- Empty Cart State -->
            <div class="empty-checkout">
                <div class="empty-checkout-content">
                    <div class="empty-icon">🛒</div>
                    <h2>Количката ви е празна</h2>
                    <p>Добавете продукти преди да продължите с поръчката.</p>
                    <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="btn btn-primary">Към магазина</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Streamlined Checkout Styles */
.checkout-flow-container {
    background: #fafafa;
    padding: 100px 0 60px;
    min-height: calc(100vh - 200px);
}

/* Progress Header */
.checkout-header {
    text-align: center;
    margin-bottom: 40px;
}

.checkout-steps {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px;
    background: var(--white);
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.step {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 15px;
    border-radius: 25px;
    opacity: 0.5;
    transition: all 0.3s ease;
}

.step.active {
    opacity: 1;
    background: rgba(182, 129, 58, 0.1);
}

.step.completed {
    opacity: 1;
    background: rgba(34, 197, 94, 0.1);
    cursor: pointer;
}

.step-number {
    width: 28px;
    height: 28px;
    background: var(--warm-beige);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.step.active .step-number {
    background: var(--gold-start);
    color: var(--white);
}

.step.completed .step-number {
    background: #22c55e;
    color: var(--white);
}

.step-line {
    width: 60px;
    height: 2px;
    background: var(--warm-beige);
    margin: 0 15px;
}

.step-line.completed {
    background: #22c55e;
}

/* Layout */
.checkout-layout {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 40px;
}

/* Form Sections */
.checkout-section {
    background: var(--white);
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    color: var(--charcoal);
    font-size: 1.1rem;
    font-weight: 600;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--warm-beige);
}

.section-title .icon {
    font-size: 1.2rem;
}

.price-addon {
    margin-left: auto;
    background: var(--gold-start);
    color: var(--white);
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 12px;
}

/* Form Grid */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-row-wide {
    grid-column: 1 / -1;
}

.form-row {
    margin-bottom: 0;
}

.form-row label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: var(--charcoal);
    font-size: 14px;
}

.required {
    color: #e74c3c;
}

.input-text,
textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s ease;
    background: #fafafa;
}

.input-text:focus,
textarea:focus {
    outline: none;
    border-color: var(--gold-start);
    background: var(--white);
}

/* Checkbox Styling */
.checkbox-wrapper {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    cursor: pointer;
    line-height: 1.5;
}

.checkbox-wrapper input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid var(--primary-taupe);
    border-radius: 4px;
    position: relative;
    transition: all 0.3s ease;
    flex-shrink: 0;
    margin-top: 2px;
}

.checkbox-wrapper input[type="checkbox"]:checked ~ .checkmark {
    background: var(--gold-start);
    border-color: var(--gold-start);
}

.checkbox-wrapper input[type="checkbox"]:checked ~ .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: var(--white);
    font-size: 12px;
    font-weight: bold;
}

.checkbox-text {
    font-size: 14px;
    color: var(--charcoal);
}

.checkbox-text a {
    color: var(--gold-start);
    text-decoration: underline;
}

/* Personalization */
.personalization-field {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid var(--warm-beige);
    animation: slideDown 0.3s ease-out;
}

.field-note {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 12px;
    font-style: italic;
}

/* Coupon Section */
.coupon-input-wrapper {
    display: flex;
    gap: 10px;
}

.coupon-input-wrapper .input-text {
    flex: 1;
}

.btn-coupon {
    background: var(--gold-start);
    color: var(--white);
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    white-space: nowrap;
}

.btn-coupon:hover {
    background: var(--gold-end);
}

.coupon-message {
    margin-top: 10px;
    padding: 10px;
    border-radius: 6px;
    font-size: 13px;
}

.coupon-message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.coupon-message.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Order Summary */
.order-summary {
    background: var(--white);
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    position: sticky;
    top: 100px;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--warm-beige);
}

.section-header h3 {
    margin: 0;
    color: var(--charcoal);
    font-size: 1.1rem;
}

/* Order Review Table */
.shop_table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.shop_table th,
.shop_table td {
    padding: 12px 0;
    text-align: left;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

.product-name {
    font-weight: 600;
}

.product-quantity {
    font-weight: normal;
    color: #666;
    font-size: 13px;
}

.product-total {
    text-align: right;
    font-weight: 600;
}

.order-total th,
.order-total td {
    border-bottom: none;
    border-top: 2px solid var(--gold-start);
    padding-top: 15px;
    font-size: 16px;
    font-weight: 700;
}

.order-total .amount {
    color: var(--gold-start);
}

/* Payment Methods */
.payment-section {
    margin: 25px 0;
}

.payment-title {
    margin-bottom: 15px;
    font-size: 16px;
    font-weight: 600;
    color: var(--charcoal);
}

.payment_methods {
    list-style: none;
    padding: 0;
    margin: 0 0 15px;
}

.payment_method {
    margin-bottom: 12px;
}

.payment_method label {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 15px;
    background: #f8f8f8;
    border: 2px solid transparent;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
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

.payment-icon {
    font-size: 1.2rem;
}

.payment_method small {
    display: block;
    color: #666;
    font-size: 12px;
    margin-left: 32px;
}

.payment-note {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 12px;
    font-size: 13px;
    margin-bottom: 15px;
}

/* Place Order Button */
#place_order {
    width: 100%;
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end)) !important;
    color: var(--white) !important;
    border: none !important;
    padding: 18px 25px !important;
    font-size: 16px !important;
    font-weight: 700 !important;
    border-radius: 10px !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    margin: 20px 0;
}

#place_order:hover {
    background: linear-gradient(135deg, var(--gold-end), var(--gold-start)) !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(182, 129, 58, 0.4);
}

#place_order.processing {
    opacity: 0.7;
    cursor: not-allowed;
    pointer-events: none;
}

#place_order .btn-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

/* Trust Indicators */
.trust-indicators {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.trust-item {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-size: 13px;
    color: var(--charcoal);
}

.trust-item .dashicons {
    color: var(--gold-start);
    font-size: 16px;
}

/* Privacy Section */
.privacy-section {
    margin: 20px 0;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.privacy-checkbox .checkbox-text {
    font-size: 13px;
    line-height: 1.4;
}

/* Responsive */
@media (max-width: 768px) {
    .checkout-flow-container {
        padding: 80px 0 40px;
    }
    
    .checkout-steps {
        padding: 15px;
        gap: 10px;
    }
    
    .step-line {
        width: 30px;
        margin: 0 8px;
    }
    
    .step-title {
        display: none;
    }
    
    .checkout-layout {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .checkout-section {
        padding: 20px;
    }
    
    .coupon-input-wrapper {
        flex-direction: column;
    }
    
    .order-summary {
        position: relative;
        top: 0;
    }
}

/* Animations */
@keyframes slideDown {
    from {
        opacity: 0;
        max-height: 0;
    }
    to {
        opacity: 1;
        max-height: 200px;
    }
}

.loading {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,.3);
    border-radius: 50%;
    border-top-color: var(--white);
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<script>
jQuery(document).ready(function($) {
    'use strict';
    
    // Form validation and interaction
    const form = $('.woocommerce-checkout');
    
    // Personalization toggle
    $('#add-personalization').on('change', function() {
        if ($(this).is(':checked')) {
            $('.personalization-field').slideDown(300);
            $('#personalization_name').prop('required', true);
            
            // Disable COD for personalized orders
            $('#payment_method_cod').prop('disabled', true);
            $('#payment_method_bacs').prop('checked', true).trigger('change');
            $('.payment_method_cod').addClass('disabled').append('<small style="color: #e74c3c;">Не е налично за персонализирани продукти</small>');
            
            updateOrderTotal();
        } else {
            $('.personalization-field').slideUp(300);
            $('#personalization_name').prop('required', false).val('');
            
            // Re-enable COD
            $('#payment_method_cod').prop('disabled', false);
            $('#payment_method_cod').prop('checked', true).trigger('change');
            $('.payment_method_cod').removeClass('disabled').find('small').last().remove();
            
            updateOrderTotal();
        }
    });
    
    // Payment method toggle
    $('input[name="payment_method"]').on('change', function() {
        $('.payment-note').hide();
        $('#' + $(this).val() + '-note').show();
    });
    
    // Coupon application
    $('#apply-coupon-btn').on('click', function() {
        const $btn = $(this);
        const $input = $('#coupon_code');
        const $message = $('#coupon-message');
        const couponCode = $input.val().trim();
        
        if (!couponCode) {
            showCouponMessage('Моля въведете код за отстъпка', 'error');
            return;
        }
        
        $btn.prop('disabled', true).text('Проверява...');
        
        $.ajax({
            url: wc_checkout_params.ajax_url,
            type: 'POST',
            data: {
                action: 'apply_coupon',
                coupon_code: couponCode,
                nonce: wc_checkout_params.apply_coupon_nonce
            },
            success: function(response) {
                if (response.success) {
                    showCouponMessage('Кодът е приложен успешно!', 'success');
                    $('body').trigger('update_checkout');
                } else {
                    showCouponMessage(response.data || 'Невалиден код', 'error');
                }
            },
            error: function() {
                showCouponMessage('Възникна грешка при проверката', 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).text('Приложи');
            }
        });
    });
    
    function showCouponMessage(message, type) {
        $('#coupon-message')
            .removeClass('success error')
            .addClass(type)
            .text(message)
            .show();
    }
    
    // Phone number formatting
    $('#billing_phone').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length > 0 && !value.startsWith('359')) {
            if (value.startsWith('0')) {
                value = '359' + value.substring(1);
            } else {
                value = '359' + value;
            }
        }
        
        if (value.length >= 3) {
            let formatted = '+' + value.substring(0, 3);
            if (value.length > 3) {
                formatted += ' ' + value.substring(3, 6);
            }
            if (value.length > 6) {
                formatted += ' ' + value.substring(6, 9);
            }
            if (value.length > 9) {
                formatted += ' ' + value.substring(9, 12);
            }
            $(this).val(formatted);
        }
    });
    
    // Form submission handling
    form.on('submit', function(e) {
        const $submitBtn = $('#place_order');
        
        // Validate required fields
        let isValid = true;
        $('.input-text[required], input[type="checkbox"][required]').each(function() {
            if (!$(this).val() && $(this).attr('type') !== 'checkbox') {
                isValid = false;
                $(this).focus();
                return false;
            }
            if ($(this).attr('type') === 'checkbox' && !$(this).is(':checked')) {
                isValid = false;
                $(this).focus();
                return false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showNotification('Моля попълнете всички задължителни полета', 'error');
            return;
        }
        
        // Show loading state
        $submitBtn.addClass('processing');
        $submitBtn.find('.btn-text').hide();
        $submitBtn.find('.btn-loading').show();
    });
    
    // Auto-save form data in localStorage for recovery
    const formInputs = $('.input-text, textarea');
    formInputs.on('blur', function() {
        const field = $(this).attr('name');
        const value = $(this).val();
        if (field && value) {
            localStorage.setItem('checkout_' + field, value);
        }
    });
    
    // Restore form data on page load
    formInputs.each(function() {
        const field = $(this).attr('name');
        const saved = localStorage.getItem('checkout_' + field);
        if (saved && !$(this).val()) {
            $(this).val(saved);
        }
    });
    
    // Clear saved data on successful submission
    if (window.location.search.includes('order-received')) {
        Object.keys(localStorage).forEach(key => {
            if (key.startsWith('checkout_')) {
                localStorage.removeItem(key);
            }
        });
    }
    
    // Update order total when personalization changes
    function updateOrderTotal() {
        $('body').trigger('update_checkout');
    }
    
    // Notification helper
    function showNotification(message, type) {
        const $notification = $('<div class="checkout-notification ' + type + '">' + message + '</div>');
        $('body').append($notification);
        setTimeout(() => $notification.remove(), 4000);
    }
    
    // Real-time validation
    $('.input-text[required]').on('blur', function() {
        if (!$(this).val()) {
            $(this).addClass('error');
        } else {
            $(this).removeClass('error');
        }
    });
    
    $('input[type="email"]').on('blur', function() {
        const email = $(this).val();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && !emailRegex.test(email)) {
            $(this).addClass('error');
        } else {
            $(this).removeClass('error');
        }
    });
});
</script>

<?php get_footer(); ?>