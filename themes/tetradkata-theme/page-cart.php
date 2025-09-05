<?php
/**
 * Cart Page Template with Unified Step Navigation
 * 
 * @package TetradkataTheme
 */

get_header(); ?>

<div class="checkout-flow-container">
    <div class="container">
        <!-- Unified Step Header -->
        <div class="checkout-header">
            <div class="checkout-steps">
                <div class="step active" data-step="1">
                    <span class="step-number">1</span>
                    <span class="step-title">Количка</span>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="2">
                    <span class="step-number">2</span>
                    <span class="step-title">Плащане</span>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="3">
                    <span class="step-number">3</span>
                    <span class="step-title">Завършено</span>
                </div>
            </div>
            <h1>Прегледайте поръчката си</h1>
            <p class="step-description">Прегледайте продуктите в количката преди да продължите към плащане</p>
        </div>

        <div class="cart-content">
            <?php if (class_exists('WooCommerce')) : ?>
                
                <?php if (WC()->cart && !WC()->cart->is_empty()) : ?>
                    
                    <div class="cart-wrapper">
                        <div class="cart-main">
                            <!-- Cart Items Section -->
                            <div class="cart-items-section">
                                <div class="section-header">
                                    <h2>
                                        <span class="section-icon">🛒</span>
                                        Продукти в количката
                                    </h2>
                                    <span class="items-count">
                                        <?php 
                                        $cart_count = WC()->cart->get_cart_contents_count();
                                        printf(
                                            _n('%s продукт', '%s продукта', $cart_count, 'tetradkata'), 
                                            $cart_count
                                        ); 
                                        ?>
                                    </span>
                                </div>

                                <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
                                    <?php do_action('woocommerce_before_cart_table'); ?>

                                    <div class="cart-items-grid">
                                        <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) : ?>
                                            <?php
                                            $product = $cart_item['data'];
                                            $product_id = $cart_item['product_id'];
                                            
                                            if ($product && $product->exists() && $cart_item['quantity'] > 0) :
                                                $product_permalink = $product->get_permalink();
                                                $product_name = $product->get_name();
                                                $line_total = $cart_item['line_total'] + $cart_item['line_tax'];
                                                
                                                $product_image = get_the_post_thumbnail_url($product_id, 'thumbnail');
                                                if (!$product_image) {
                                                    $product_image = wc_placeholder_img_src('thumbnail');
                                                }
                                            ?>
                                            
                                            <div class="cart-item-card" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
                                                <div class="cart-item-image">
                                                    <a href="<?php echo esc_url($product_permalink); ?>">
                                                        <img src="<?php echo esc_url($product_image); ?>" 
                                                             alt="<?php echo esc_attr($product_name); ?>" 
                                                             class="cart-item-img">
                                                    </a>
                                                </div>
                                                
                                                <div class="cart-item-details">
                                                    <h3 class="cart-item-name">
                                                        <a href="<?php echo esc_url($product_permalink); ?>">
                                                            <?php echo esc_html($product_name); ?>
                                                        </a>
                                                    </h3>
                                                    
                                                    <div class="cart-item-price-per-unit">
                                                        <span class="price-label">Цена:</span>
                                                        <span class="price"><?php echo wc_price($product->get_price()); ?></span>
                                                    </div>
                                                    
                                                    <div class="cart-item-meta">
                                                        <?php echo wc_get_formatted_cart_item_data($cart_item); ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="cart-item-controls">
                                                    <div class="quantity-section">
                                                        <label class="quantity-label">Количество:</label>
                                                        <div class="quantity-controls">
                                                            <button type="button" class="quantity-minus" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">−</button>
                                                            <input type="number" 
                                                                   class="quantity-input" 
                                                                   name="cart[<?php echo esc_attr($cart_item_key); ?>][qty]" 
                                                                   value="<?php echo esc_attr($cart_item['quantity']); ?>" 
                                                                   min="0" 
                                                                   max="<?php echo esc_attr($product->get_max_purchase_quantity()); ?>"
                                                                   step="1" 
                                                                   data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
                                                            <button type="button" class="quantity-plus" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">+</button>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="cart-item-total">
                                                        <span class="total-label">Общо:</span>
                                                        <span class="total-price"><?php echo wc_price($line_total); ?></span>
                                                    </div>
                                                    
                                                    <button type="button" 
                                                            class="remove-item-btn" 
                                                            data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>"
                                                            title="<?php esc_attr_e('Премахни от количката', 'tetradkata'); ?>">
                                                        <span class="dashicons dashicons-trash"></span>
                                                        Премахни
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="cart-actions">
                                        <div class="cart-update-actions">
                                            <button type="submit" class="btn btn-secondary" name="update_cart" value="<?php esc_attr_e('Обнови количката', 'tetradkata'); ?>">
                                                <span class="dashicons dashicons-update"></span>
                                                <?php esc_html_e('Обнови количката', 'tetradkata'); ?>
                                            </button>
                                            
                                            <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="btn btn-outline">
                                                <span class="dashicons dashicons-arrow-left-alt"></span>
                                                <?php esc_html_e('Продължи пазаруването', 'tetradkata'); ?>
                                            </a>
                                        </div>
                                    </div>

                                    <?php do_action('woocommerce_after_cart_table'); ?>
                                    <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                                </form>
                            </div>
                            
                            <!-- Coupon Section -->
                            <?php if (wc_coupons_enabled()) : ?>
                                <div class="coupon-section">
                                    <div class="section-header">
                                        <h3>
                                            <span class="section-icon">🎫</span>
                                            Код за отстъпка
                                        </h3>
                                    </div>
                                    <form class="coupon-form" method="post">
                                        <div class="coupon-wrapper">
                                            <input type="text" 
                                                   name="coupon_code" 
                                                   class="coupon-input" 
                                                   placeholder="<?php esc_attr_e('Въведете код за отстъпка', 'tetradkata'); ?>" 
                                                   value="" />
                                            <button type="submit" 
                                                    class="btn btn-secondary" 
                                                    name="apply_coupon" 
                                                    value="<?php esc_attr_e('Приложи код', 'tetradkata'); ?>">
                                                <span class="dashicons dashicons-yes"></span>
                                                <?php esc_html_e('Приложи', 'tetradkata'); ?>
                                            </button>
                                        </div>
                                        <?php do_action('woocommerce_cart_coupon'); ?>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Cart Summary Sidebar -->
                        <div class="cart-sidebar">
                            <div class="cart-summary-card">
                                <div class="section-header">
                                    <h3>
                                        <span class="section-icon">📊</span>
                                        Обобщение на поръчката
                                    </h3>
                                </div>
                                
                                <?php do_action('woocommerce_before_cart_collaterals'); ?>
                                
                                <div class="cart_totals <?php echo (WC()->customer->has_calculated_shipping()) ? 'calculated_shipping' : ''; ?>">
                                    <?php do_action('woocommerce_before_cart_totals'); ?>

                                    <div class="cart-totals-inner">
                                        <div class="cart-subtotal">
                                            <span class="label"><?php esc_html_e('Междинна сума:', 'tetradkata'); ?></span>
                                            <span class="amount"><?php wc_cart_totals_subtotal_html(); ?></span>
                                        </div>

                                        <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
                                            <div class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
                                                <span class="label"><?php wc_cart_totals_coupon_label($coupon); ?>:</span>
                                                <span class="amount"><?php wc_cart_totals_coupon_html($coupon); ?></span>
                                            </div>
                                        <?php endforeach; ?>

                                        <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                                            <div class="shipping-section">
                                                <span class="label"><?php esc_html_e('Доставка:', 'tetradkata'); ?></span>
                                                <?php do_action('woocommerce_cart_totals_before_shipping'); ?>
                                                <?php wc_cart_totals_shipping_html(); ?>
                                                <?php do_action('woocommerce_cart_totals_after_shipping'); ?>
                                            </div>
                                        <?php elseif (WC()->cart->needs_shipping()) : ?>
                                            <div class="shipping-section">
                                                <span class="label"><?php esc_html_e('Доставка:', 'tetradkata'); ?></span>
                                                <span class="amount"><?php esc_html_e('Изчислява се при плащане', 'tetradkata'); ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php foreach (WC()->cart->get_fees() as $fee) : ?>
                                            <div class="fee">
                                                <span class="label"><?php echo esc_html($fee->name); ?>:</span>
                                                <span class="amount"><?php wc_cart_totals_fee_html($fee); ?></span>
                                            </div>
                                        <?php endforeach; ?>

                                        <?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
                                            <?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
                                                <?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : ?>
                                                    <div class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?>">
                                                        <span class="label"><?php echo esc_html($tax->label); ?>:</span>
                                                        <span class="amount"><?php echo wp_kses_post($tax->formatted_amount); ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <div class="tax-total">
                                                    <span class="label"><?php echo esc_html(WC()->countries->tax_or_vat()); ?>:</span>
                                                    <span class="amount"><?php wc_cart_totals_taxes_total_html(); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php 
                                        // Calculate products total only (exclude shipping/fees), incl. tax
                                        $products_total = 0;
                                        foreach (WC()->cart->get_cart() as $ci) {
                                            $products_total += (float) $ci['line_total'] + (float) $ci['line_tax'];
                                        }
                                        ?>
                                        <div class="order-total">
                                            <span class="label"><?php esc_html_e('Общо:', 'tetradkata'); ?></span>
                                            <span class="amount"><?php echo wc_price($products_total); ?></span>
                                        </div>
                                    </div>

                                    <?php do_action('woocommerce_after_cart_totals'); ?>
                                </div>
                                
                                <!-- Checkout Button -->
                                <div class="checkout-actions">
                                    <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn btn-primary btn-large proceed-to-checkout">
                                        <span>Продължи към плащане</span>
                                        <span class="btn-arrow">→</span>
                                    </a>
                                </div>
                                
                                <!-- Trust Badges -->
                                <div class="cart-benefits">
                                    <div class="benefit-item">
                                        <span class="dashicons dashicons-yes-alt"></span>
                                        <span><?php esc_html_e('Безплатна доставка над 50 лв.', 'tetradkata'); ?></span>
                                    </div>
                                    <div class="benefit-item">
                                        <span class="dashicons dashicons-update"></span>
                                        <span><?php esc_html_e('14 дни за връщане', 'tetradkata'); ?></span>
                                    </div>
                                    <div class="benefit-item">
                                        <span class="dashicons dashicons-shield-alt"></span>
                                        <span><?php esc_html_e('Сигурно плащане', 'tetradkata'); ?></span>
                                    </div>
                                </div>
                                
                                <?php do_action('woocommerce_after_cart_collaterals'); ?>
                            </div>
                        </div>
                    </div>
                    
                <?php else : ?>
                    
                    <!-- Empty Cart -->
                    <div class="empty-cart">
                        <div class="empty-cart-content">
                            <div class="empty-icon">🛒</div>
                            <h2><?php esc_html_e('Количката ви е празна', 'tetradkata'); ?></h2>
                            <p><?php esc_html_e('Добавете продукти в количката, за да продължите с поръчката.', 'tetradkata'); ?></p>
                            <div class="empty-cart-actions">
                                <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="btn btn-primary">
                                    <span class="dashicons dashicons-cart"></span>
                                    <?php esc_html_e('Към магазина', 'tetradkata'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                <?php endif; ?>
                
            <?php else : ?>
                
                <!-- WooCommerce not active -->
                <div class="woocommerce-inactive">
                    <div class="empty-cart-content">
                        <h2><?php esc_html_e('Магазинът не е достъпен', 'tetradkata'); ?></h2>
                        <p><?php esc_html_e('За момента магазинът не е активен. Моля, опитайте по-късно.', 'tetradkata'); ?></p>
                        <a href="<?php echo home_url(); ?>" class="btn btn-primary"><?php esc_html_e('Към началото', 'tetradkata'); ?></a>
                    </div>
                </div>
                
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Unified Checkout Flow Styles */
.checkout-flow-container {
    background: #fafafa;
    padding: 100px 0 60px;
    min-height: calc(100vh - 200px);
}

/* Step Header (same as checkout) */
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
    cursor: default;
    padding: 8px 12px;
    border-radius: 10px;
}

.step.active {
    opacity: 1;
    background: rgba(182, 129, 58, 0.1);
}

.step.completed {
    opacity: 1;
    cursor: pointer;
}

.step.completed:hover {
    background: rgba(182, 129, 58, 0.1);
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

.step.active .step-number,
.step.completed .step-number {
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
    color: var(--white);
}

.step.completed .step-number {
    background: #22c55e;
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
    margin-bottom: 15px;
}

.step-description {
    color: #666;
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
}

/* Cart Layout */
.cart-wrapper {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 40px;
}

.cart-main {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.cart-items-section {
    background: var(--white);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--warm-beige);
}

.section-header h2,
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

.items-count {
    background: var(--paper-bg);
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    color: var(--charcoal);
}

/* Cart Items Grid */
.cart-items-grid {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.cart-item-card {
    display: grid;
    grid-template-columns: 100px 1fr auto;
    gap: 20px;
    align-items: flex-start;
    padding: 20px;
    background: var(--paper-bg);
    border-radius: 15px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.cart-item-card:hover {
    border-color: var(--warm-beige);
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.cart-item-image {
    text-align: center;
}

.cart-item-img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 12px;
    border: 2px solid var(--warm-beige);
    transition: transform 0.3s ease;
}

.cart-item-img:hover {
    transform: scale(1.02);
}

.cart-item-details {
    min-width: 0;
}

.cart-item-name {
    margin-bottom: 10px;
    font-size: 1.2rem;
    line-height: 1.3;
}

.cart-item-name a {
    color: var(--charcoal);
    text-decoration: none;
    font-weight: 600;
}

.cart-item-name a:hover {
    color: var(--gold-start);
}

.cart-item-price-per-unit {
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.price-label {
    font-size: 14px;
    color: #666;
}

.price {
    color: var(--gold-start);
    font-weight: 700;
    font-size: 1.1rem;
}

.cart-item-meta {
    font-size: 13px;
    color: #666;
    line-height: 1.4;
}

.cart-item-controls {
    display: flex;
    flex-direction: column;
    gap: 15px;
    align-items: flex-end;
    min-width: 160px;
}

.quantity-section {
    text-align: center;
}

.quantity-label {
    display: block;
    font-size: 13px;
    color: #666;
    margin-bottom: 5px;
}

.quantity-controls {
    display: flex;
    align-items: center;
    border: 2px solid var(--warm-beige);
    border-radius: 25px;
    overflow: hidden;
    background: var(--white);
}

.quantity-minus,
.quantity-plus {
    background: var(--warm-beige);
    border: none;
    width: 35px;
    height: 35px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    color: var(--charcoal);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quantity-minus:hover,
.quantity-plus:hover {
    background: var(--gold-start);
    color: var(--white);
}

.quantity-input {
    border: none;
    width: 50px;
    height: 35px;
    text-align: center;
    font-size: 14px;
    font-weight: 600;
    background: var(--white);
}

.quantity-input:focus {
    outline: none;
}

.cart-item-total {
    text-align: center;
}

.total-label {
    display: block;
    font-size: 13px;
    color: #666;
    margin-bottom: 5px;
}

.total-price {
    font-weight: 700;
    color: var(--charcoal);
    font-size: 1.2rem;
}

.remove-item-btn {
    background: none;
    border: 2px solid #ef4444;
    color: #ef4444;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.remove-item-btn:hover {
    background: #ef4444;
    color: white;
}

/* Cart Actions */
.cart-actions {
    padding-top: 20px;
    border-top: 2px solid var(--warm-beige);
}

.cart-update-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

/* Coupon Section */
.coupon-section {
    background: var(--white);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.coupon-wrapper {
    display: flex;
    gap: 15px;
    align-items: end;
}

.coupon-input {
    flex: 1;
    padding: 12px 16px;
    border: 2px solid var(--warm-beige);
    border-radius: 25px;
    font-size: 15px;
    background: var(--paper-bg);
    transition: border-color 0.3s ease;
}

.coupon-input:focus {
    outline: none;
    border-color: var(--gold-start);
    background: var(--white);
}

/* Cart Sidebar */
.cart-sidebar {
    display: flex;
    flex-direction: column;
}

.cart-summary-card {
    background: var(--white);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    position: sticky;
    top: 100px;
}

.cart-totals-inner > div {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid rgba(189, 176, 165, 0.3);
}

.cart-totals-inner > div:last-child {
    border-bottom: none;
}

.cart-totals-inner .label {
    font-weight: 600;
    color: var(--charcoal);
}

.cart-totals-inner .amount {
    font-weight: 700;
    color: var(--charcoal);
}

.order-total {
    border-top: 2px solid var(--gold-start) !important;
    padding-top: 20px !important;
    margin-top: 15px;
    font-size: 1.1rem;
}

.order-total .amount {
    color: var(--gold-start);
    font-size: 1.4rem;
}

.checkout-actions {
    margin: 30px 0;
}

.proceed-to-checkout {
    width: 100%;
    padding: 18px 30px;
    font-size: 1.1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    position: relative;
    overflow: hidden;
}

.btn-arrow {
    font-size: 1.2rem;
    transition: transform 0.3s ease;
}

.proceed-to-checkout:hover .btn-arrow {
    transform: translateX(5px);
}

.cart-benefits {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding-top: 25px;
    border-top: 1px solid var(--warm-beige);
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

/* Empty Cart */
.empty-cart {
    background: var(--white);
    border-radius: 20px;
    padding: 60px 40px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    text-align: center;
}

.empty-cart-content {
    max-width: 400px;
    margin: 0 auto;
}

.empty-icon {
    font-size: 5rem;
    margin-bottom: 20px;
    opacity: 0.3;
}

.empty-cart h2 {
    color: var(--charcoal);
    margin-bottom: 15px;
}

.empty-cart p {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 30px;
    line-height: 1.6;
}

.empty-cart-actions {
    margin-top: 30px;
}

/* Button Enhancements */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border: none;
    border-radius: 25px;
    font-family: var(--font-body);
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 15px;
    white-space: nowrap;
}

.btn-primary {
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
    color: var(--white);
    box-shadow: 0 4px 15px rgba(182, 129, 58, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--gold-end), var(--gold-start));
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(182, 129, 58, 0.4);
}

.btn-secondary {
    background: var(--primary-taupe);
    color: var(--white);
    box-shadow: 0 4px 15px rgba(189, 176, 165, 0.3);
}

.btn-secondary:hover {
    background: var(--charcoal);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(94, 87, 81, 0.4);
}

.btn-outline {
    background: transparent;
    color: var(--charcoal);
    border: 2px solid var(--primary-taupe);
}

.btn-outline:hover {
    background: var(--primary-taupe);
    color: var(--white);
    transform: translateY(-1px);
}

.btn-large {
    padding: 18px 40px;
    font-size: 1.1rem;
    font-weight: 700;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .cart-wrapper {
        grid-template-columns: 1fr;
    }
    
    .cart-summary-card {
        position: relative;
        top: auto;
    }
}

@media (max-width: 768px) {
    .checkout-flow-container {
        padding: 80px 0 40px;
    }
    
    .checkout-steps {
        padding: 20px 15px;
        flex-wrap: wrap;
        gap: 15px;
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
    
    .cart-items-section,
    .coupon-section,
    .cart-summary-card {
        padding: 20px;
        margin: 0 15px 20px;
    }
    
    .cart-item-card {
        grid-template-columns: 80px 1fr;
        gap: 15px;
        padding: 15px;
    }
    
    .cart-item-controls {
        grid-column: 1 / -1;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid var(--warm-beige);
        min-width: auto;
    }
    
    .cart-item-img {
        width: 80px;
        height: 80px;
    }
    
    .cart-update-actions {
        flex-direction: column;
    }
    
    .coupon-wrapper {
        flex-direction: column;
        gap: 10px;
    }
    
    .section-header {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
}

/* Loading and Success States */
.cart-item-card.updating {
    opacity: 0.6;
    pointer-events: none;
}

.cart-item-card.removing {
    animation: fadeOut 0.5s ease-out forwards;
}

@keyframes fadeOut {
    to {
        opacity: 0;
        transform: translateX(-20px);
    }
}

/* Focus States for Accessibility */
.step.completed:focus,
.remove-item-btn:focus,
.quantity-minus:focus,
.quantity-plus:focus,
.quantity-input:focus,
.proceed-to-checkout:focus {
    outline: 2px solid var(--gold-start);
    outline-offset: 2px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Step navigation - only allow going back to previous steps
    $('.step.completed').on('click', function() {
        const step = $(this).data('step');
        if (step === 1) {
            // Already on cart, no need to navigate
            return;
        }
    });
    
    // Quantity controls
    $('.quantity-plus').on('click', function() {
        const $input = $(this).siblings('.quantity-input');
        const currentValue = parseInt($input.val()) || 1;
        const maxValue = parseInt($input.attr('max')) || 999;
        
        if (currentValue < maxValue) {
            $input.val(currentValue + 1);
            updateCartItem($input);
        }
    });
    
    $('.quantity-minus').on('click', function() {
        const $input = $(this).siblings('.quantity-input');
        const currentValue = parseInt($input.val()) || 1;
        const minValue = parseInt($input.attr('min')) || 0;
        
        if (currentValue > minValue) {
            $input.val(currentValue - 1);
            updateCartItem($input);
        }
    });
    
    $('.quantity-input').on('change', function() {
        updateCartItem($(this));
    });
    
    // Remove item functionality
    $('.remove-item-btn').on('click', function(e) {
        e.preventDefault();
        
        const cartItemKey = $(this).data('cart-item-key');
        const $cartItem = $(this).closest('.cart-item-card');
        
        if (confirm('<?php esc_html_e("Сигурни ли сте, че искате да премахнете този продукт от количката?", "tetradkata"); ?>')) {
            $cartItem.addClass('removing');
            
            setTimeout(function() {
                $.ajax({
                    url: tetradkata_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'tetradkata_remove_cart_item',
                        cart_item_key: cartItemKey,
                        nonce: tetradkata_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            $cartItem.removeClass('removing');
                            alert(response.data?.message || '<?php esc_html_e("Възникна грешка", "tetradkata"); ?>');
                        }
                    },
                    error: function() {
                        $cartItem.removeClass('removing');
                        alert('<?php esc_html_e("Възникна грешка при премахване на продукта", "tetradkata"); ?>');
                    }
                });
            }, 300);
        }
    });
    
    function updateCartItem($input) {
        const $cartItem = $input.closest('.cart-item-card');
        $cartItem.addClass('updating');
        
        // Simple debounce
        clearTimeout(window.updateCartTimer);
        window.updateCartTimer = setTimeout(function() {
            $('form.woocommerce-cart-form').submit();
        }, 1000);
    }
    
    // Smooth animations on page load
    $('.cart-item-card').each(function(index) {
        $(this).css({
            'opacity': '0',
            'transform': 'translateY(20px)'
        }).delay(index * 100).animate({
            'opacity': '1',
            'transform': 'translateY(0)'
        }, 300);
    });
});
</script>

<?php get_footer(); ?>
