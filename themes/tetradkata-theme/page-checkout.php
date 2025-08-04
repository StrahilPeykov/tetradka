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
            <h1>Завършване на поръчката</h1>
            <p>Само няколко стъпки до вашата тетрадка</p>
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
                        
                        // Check if checkout form should be displayed
                        if (WC()->cart->needs_payment()) {
                            woocommerce_checkout_login_form();
                            woocommerce_checkout_coupon_form();
                        }
                        
                        // Checkout form
                        $checkout = WC()->checkout();
                        ?>
                        
                        <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

                            <div class="checkout-layout">
                                <div class="checkout-billing">
                                    <h3>Данни за фактуриране</h3>
                                    
                                    <?php if ($checkout->get_checkout_fields()) : ?>
                                        <?php do_action('woocommerce_checkout_before_customer_details'); ?>

                                        <div class="col2-set" id="customer_details">
                                            <div class="col-1">
                                                <?php do_action('woocommerce_checkout_billing'); ?>
                                            </div>

                                            <div class="col-2">
                                                <?php do_action('woocommerce_checkout_shipping'); ?>
                                            </div>
                                        </div>

                                        <?php do_action('woocommerce_checkout_after_customer_details'); ?>
                                    <?php endif; ?>
                                </div>

                                <div class="checkout-review">
                                    <h3 id="order_review_heading">Вашата поръчка</h3>
                                    
                                    <?php do_action('woocommerce_checkout_before_order_review'); ?>

                                    <div id="order_review" class="woocommerce-checkout-review-order">
                                        <?php do_action('woocommerce_checkout_order_review'); ?>
                                    </div>

                                    <?php do_action('woocommerce_checkout_after_order_review'); ?>
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
.checkout-container {
    background: var(--paper-bg);
    padding: 120px 0 60px;
    min-height: calc(100vh - 200px);
}

.checkout-header {
    text-align: center;
    margin-bottom: 50px;
    padding: 40px 0;
    background: var(--white);
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.checkout-header h1 {
    color: var(--charcoal);
    margin-bottom: 15px;
}

.checkout-header p {
    color: var(--charcoal);
    opacity: 0.8;
    font-size: 1.1rem;
}

.checkout-wrapper {
    background: var(--white);
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    overflow: hidden;
}

.checkout-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
}

.checkout-billing,
.checkout-review {
    padding: 40px;
}

.checkout-billing {
    border-right: 1px solid var(--warm-beige);
}

.checkout-billing h3,
.checkout-review h3 {
    color: var(--charcoal);
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--warm-beige);
}

.col2-set .col-1,
.col2-set .col-2 {
    width: 100%;
}

.woocommerce-checkout .form-row {
    margin-bottom: 20px;
}

.woocommerce-checkout .form-row label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--charcoal);
}

.woocommerce-checkout .form-row input,
.woocommerce-checkout .form-row select,
.woocommerce-checkout .form-row textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid var(--warm-beige);
    border-radius: 10px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.woocommerce-checkout .form-row input:focus,
.woocommerce-checkout .form-row select:focus,
.woocommerce-checkout .form-row textarea:focus {
    outline: none;
    border-color: var(--gold-start);
}

.empty-checkout,
.woocommerce-inactive {
    text-align: center;
    padding: 80px 20px;
    background: var(--white);
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.empty-checkout h2,
.woocommerce-inactive h2 {
    color: var(--charcoal);
    margin-bottom: 20px;
}

.empty-checkout p,
.woocommerce-inactive p {
    color: var(--charcoal);
    opacity: 0.8;
    margin-bottom: 30px;
    font-size: 1.1rem;
}

/* Order review table styling */
.woocommerce-checkout-review-order-table {
    width: 100%;
    margin-bottom: 25px;
    border-collapse: collapse;
}

.woocommerce-checkout-review-order-table th,
.woocommerce-checkout-review-order-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid var(--warm-beige);
}

.woocommerce-checkout-review-order-table .order-total {
    font-weight: bold;
    background: var(--paper-bg);
    font-size: 1.2rem;
}

.woocommerce-checkout-review-order-table .order-total .woocommerce-Price-amount {
    color: var(--gold-start);
    font-weight: 700;
}

/* Payment methods */
.woocommerce-checkout .payment_methods {
    background: var(--paper-bg);
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 25px;
}

.woocommerce-checkout .payment_method_cod,
.woocommerce-checkout .payment_method_bacs {
    margin-bottom: 15px;
}

.woocommerce-checkout .payment_method_cod label,
.woocommerce-checkout .payment_method_bacs label {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    background: var(--white);
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.woocommerce-checkout .payment_method_cod label:hover,
.woocommerce-checkout .payment_method_bacs label:hover {
    background: var(--warm-beige);
    border-color: var(--gold-start);
}

.woocommerce-checkout .payment_method_cod input[type="radio"],
.woocommerce-checkout .payment_method_bacs input[type="radio"] {
    width: auto;
    margin: 0;
}

/* Place order button */
#place_order {
    width: 100%;
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end)) !important;
    color: var(--white) !important;
    border: none !important;
    padding: 15px 30px !important;
    font-size: 16px !important;
    font-weight: 600 !important;
    border-radius: 50px !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    margin-top: 20px;
}

#place_order:hover {
    background: linear-gradient(135deg, var(--gold-end), var(--gold-start)) !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(182, 129, 58, 0.4);
}

/* Mobile responsive */
@media (max-width: 768px) {
    .checkout-container {
        padding: 100px 0 40px;
    }
    
    .checkout-layout {
        grid-template-columns: 1fr;
    }
    
    .checkout-billing {
        border-right: none;
        border-bottom: 1px solid var(--warm-beige);
    }
    
    .checkout-billing,
    .checkout-review {
        padding: 30px 20px;
    }
}

/* Loading state */
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
    background: rgba(255,255,255,0.8);
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
    padding: 20px 30px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    font-weight: 600;
    color: var(--charcoal);
}
</style>

<?php get_footer(); ?>