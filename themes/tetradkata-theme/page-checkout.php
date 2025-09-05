<?php
/**
 * Template Name: Checkout
 * Template Post Type: page
 *
 * Custom Checkout Page Template with Unified Step Navigation
 *
 * This template renders the WooCommerce checkout form and provides a
 * consistent header/steps UI matching the cart page. It is designed
 * to work with the Bulgarisation for WooCommerce plugin, which will
 * enhance the shipping section (Econt/Speedy office selection, etc.)
 * when enabled and configured.
 *
 * @package TetradkataTheme
 */

get_header(); ?>

<div class="checkout-flow-container">
    <div class="container">
        <!-- Unified Step Header -->
        <div class="checkout-header">
            <div class="checkout-steps">
                <a class="step completed" data-step="1" href="<?php echo esc_url( function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart') ); ?>">
                    <span class="step-number">1</span>
                    <span class="step-title">Количка</span>
                </a>
                <div class="step-line"></div>
                <div class="step active" data-step="2">
                    <span class="step-number">2</span>
                    <span class="step-title">Плащане</span>
                </div>
                <div class="step-line"></div>
                <div class="step" data-step="3">
                    <span class="step-number">3</span>
                    <span class="step-title">Завършено</span>
                </div>
            </div>
            <h1>Завършете поръчката</h1>
            <p class="step-description">Въведете данни за доставка и изберете метод за доставка/плащане</p>
        </div>

        <div class="checkout-content">
            <?php if (class_exists('WooCommerce')) : ?>
                <?php if (WC()->cart && !WC()->cart->is_empty()) : ?>
                    <div class="checkout-wrapper">
                        <div class="checkout-main">
                            <?php
                            // Render WooCommerce Checkout
                            echo do_shortcode('[woocommerce_checkout]');
                            ?>
                            <script>
                            jQuery(function($){
                                // Compact: toggle order notes
                                var $notes = $('#order_comments_field');
                                if ($notes.length) {
                                    var $toggle = $('<a href="#" class="checkout-notes-toggle">+ Добави забележка към поръчката</a>');
                                    $toggle.insertBefore($notes);
                                    $notes.hide();
                                    $toggle.on('click', function(e){
                                        e.preventDefault();
                                        $notes.slideToggle(200);
                                        var opened = $(this).toggleClass('open').hasClass('open');
                                        $(this).text(opened ? '− Скрий забележките' : '+ Добави забележка към поръчката');
                                    });
                                }

                                // Ensure shipping recalculates when Region/City changes (keeps UI responsive)
                                $(document.body).on('change', '#billing_state, #billing_city, #shipping_state, #shipping_city', function(){
                                    $(document.body).trigger('update_checkout');
                                });
                            });
                            </script>
                        </div>
                        <aside class="checkout-sidebar">
                            <div class="order-summary">
                                <div class="section-header">
                                    <h3>
                                        <span class="section-icon">📦</span>
                                        Обобщение на поръчката
                                    </h3>
                                </div>
                                <?php
                                // Reuse core mini-order summary by triggering cart totals hooks
                                // The actual totals are part of the checkout form; this is
                                // a lightweight info block for visual balance.
                                if (function_exists('woocommerce_mini_cart')) {
                                    echo '<div class="mini-cart">';
                                    woocommerce_mini_cart();
                                    echo '</div>';
                                }
                                ?>
                                <div class="checkout-benefits">
                                    <div class="benefit-item">
                                        <span class="dashicons dashicons-shield-alt"></span>
                                        <span><?php esc_html_e('Сигурно плащане', 'tetradkata'); ?></span>
                                    </div>
                                    <div class="benefit-item">
                                        <span class="dashicons dashicons-yes-alt"></span>
                                        <span><?php esc_html_e('Безплатна доставка над 50 лв.', 'tetradkata'); ?></span>
                                    </div>
                                    <div class="benefit-item">
                                        <span class="dashicons dashicons-update"></span>
                                        <span><?php esc_html_e('14 дни за връщане', 'tetradkata'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>
                <?php else : ?>
                    <!-- Empty Cart on Checkout -->
                    <div class="empty-checkout">
                        <div class="empty-checkout-content">
                            <div class="empty-icon">🛒</div>
                            <h2><?php esc_html_e('Количката ви е празна', 'tetradkata'); ?></h2>
                            <p><?php esc_html_e('Моля, добавете продукти, за да продължите към плащане.', 'tetradkata'); ?></p>
                            <div class="empty-checkout-actions">
                                <a href="<?php echo esc_url( get_permalink( wc_get_page_id('shop') ) ); ?>" class="btn btn-primary">
                                    <?php esc_html_e('Към магазина', 'tetradkata'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <!-- WooCommerce not active -->
                <div class="woocommerce-inactive">
                    <div class="empty-checkout-content">
                        <h2><?php esc_html_e('WooCommerce не е активен', 'tetradkata'); ?></h2>
                        <p><?php esc_html_e('Моля активирайте WooCommerce, за да използвате страницата за плащане.', 'tetradkata'); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer();
