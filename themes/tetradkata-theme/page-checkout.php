<?php
/**
 * Checkout Page Template
 * 
 * @package TetradkataTheme
 */

get_header(); ?>

<div class="checkout-container">
    <div class="container">
        <div class="checkout-header">
            <div class="checkout-steps">
                <div class="step active">
                    <span class="step-number">1</span>
                    <span class="step-title">Количка</span>
                </div>
                <div class="step-line"></div>
                <div class="step active">
                    <span class="step-number">2</span>
                    <span class="step-title">Доставка</span>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <span class="step-number">3</span>
                    <span class="step-title">Завършено</span>
                </div>
            </div>
            <h1>Завършване на поръчката</h1>
        </div>

        <div class="checkout-content">
            <?php
            // Check if WooCommerce is active
            if (class_exists('WooCommerce')) {
                // Check if cart is not empty
                if (WC()->cart && !WC()->cart->is_empty()) {
                    ?>
                    <div class="checkout-wrapper">
                        <?php
                        // Output any WooCommerce notices
                        wc_print_notices();
                        
                        // Checkout form
                        $checkout = WC()->checkout();
                        ?>
                        
                        <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

                            <div class="checkout-layout">
                                <div class="checkout-main">
                                    
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
                                        <h3 class="section-title">
                                            <span class="icon">🛒</span>
                                            Вашата поръчка
                                        </h3>
                                        
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
                        
                    </div>
                    <?php
                } else {
                    // Empty cart
                    ?>
                    <div class="empty-checkout">
                        <div class="empty-checkout-content">
                            <div class="empty-icon">🛒</div>
                            <h2>Количката ви е празна</h2>
                            <p>Добавете продукти в количката, за да продължите с поръчката.</p>
                            <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="btn btn-primary">
                                Към магазина
                            </a>
                        </div>
                    </div>
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
/* Modern Checkout Design */
.checkout-container {
    background: #fafafa;
    padding: 100px 0 60px;
    min-height: calc(100vh - 200px);
}

/* Checkout Header with Steps */
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
    transition: opacity 0.3s ease;
}

.step.active {
    opacity: 1;
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
}

.step.active .step-number {
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
    color: var(--white);
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
}

.checkout-header h1 {
    color: var(--charcoal);
    font-size: 2.5rem;
    font-weight: 700;
}

/* Main Layout */
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
    gap: 12px;
    margin-bottom: 25px;
    color: var(--charcoal);
    font-size: 1.3rem;
    font-weight: 600;
}

.section-title .icon {
    font-size: 1.5rem;
}

/* Form Styling */
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

.form-row-description {
    font-size: 13px;
    color: #666;
    margin-top: 5px;
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
    top: 80px;
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

.woocommerce-checkout-review-order-table .cart-subtotal th,
.woocommerce-checkout-review-order-table .shipping th,
.woocommerce-checkout-review-order-table .order-total th {
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

/* Personalization fee in order review */
.fee.personalization-fee th,
.fee.personalization-fee td {
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

.payment_method.disabled {
    opacity: 0.5;
    pointer-events: none;
}

.payment_method.disabled label {
    cursor: not-allowed;
}

.payment_box {
    padding: 15px 20px;
    margin: 10px 0;
    background: #f9f9f9;
    border-radius: 8px;
    font-size: 14px;
    color: #666;
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
}

#place_order:hover {
    background: linear-gradient(135deg, var(--gold-end), var(--gold-start)) !important;
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(182, 129, 58, 0.4);
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
    font-size: 20px;
}

/* Empty Cart */
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

/* Shipping Fields */
.shipping-fields {
    margin-top: 20px;
    padding: 20px;
    background: #f5f5f5;
    border-radius: 10px;
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
    .checkout-container {
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
    }
    
    .checkbox-label {
        padding: 12px 15px;
        font-size: 14px;
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

/* Error States */
.woocommerce-invalid input {
    border-color: #e74c3c !important;
    background: #fff5f5 !important;
}

.woocommerce-invalid-required-field::after {
    content: 'Това поле е задължително';
    display: block;
    color: #e74c3c;
    font-size: 13px;
    margin-top: 5px;
}
</style>

<script>
jQuery(document).ready(function($) {
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
                    // Trigger checkout refresh
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
                    // Trigger checkout refresh
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
    
    // Smooth scroll to sections on click
    $('.section-title').on('click', function() {
        const section = $(this).closest('.checkout-section');
        $('html, body').animate({
            scrollTop: section.offset().top - 100
        }, 500);
    });
    
    // Add visual feedback for required fields
    $('abbr.required').parent('label').css('font-weight', '600');
    
    // Auto-format phone number
    $('input[type="tel"]').on('input', function() {
        let value = $(this).val().replace(/\s/g, '');
        if (value.length > 0 && !value.startsWith('+')) {
            value = '+359' + value;
        }
        $(this).val(value);
    });
});
</script>

<?php get_footer(); ?>