<?php
/**
 * Cart Modal Template Part
 * 
 * @package TetradkataTheme
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<!-- Cart Modal -->
<div id="cart-modal" class="cart-modal" style="display: none;">
    <div class="cart-modal-content">
        <div class="cart-header">
            <h3><?php esc_html_e('Количка', 'tetradkata'); ?></h3>
            <button class="close-cart" aria-label="<?php esc_attr_e('Затвори количката', 'tetradkata'); ?>">&times;</button>
        </div>
        <div class="cart-items">
            <!-- Cart items will be loaded here via AJAX -->
            <div class="loading-cart" style="display: none;">
                <div class="loading"></div>
                <p><?php esc_html_e('Зарежда...', 'tetradkata'); ?></p>
            </div>
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <strong><?php esc_html_e('Общо:', 'tetradkata'); ?> <span id="cart-total-amount">0.00 <?php esc_html_e('лв.', 'tetradkata'); ?></span></strong>
            </div>
            <?php if (class_exists('WooCommerce')) : ?>
                <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn btn-primary">
                    <?php esc_html_e('Плащане', 'tetradkata'); ?>
                </a>
                <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="btn btn-secondary">
                    <?php esc_html_e('Виж количката', 'tetradkata'); ?>
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/checkout')); ?>" class="btn btn-primary">
                    <?php esc_html_e('Плащане', 'tetradkata'); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>