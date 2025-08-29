<?php
/**
 * The footer for the theme
 *
 * @package TetradkataTheme
 */
?>
</main>

<footer id="colophon" class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3><?php esc_html_e('Tetradkata', 'tetradkata'); ?></h3>
                <p><?php esc_html_e('Your personal travel journal for adventures, memories and inspiration - all in one notebook.', 'tetradkata'); ?></p>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Facebook">
                        <span class="dashicons dashicons-facebook-alt"></span>
                    </a>
                    <a href="#" class="social-link" aria-label="Instagram">
                        <span class="dashicons dashicons-instagram"></span>
                    </a>
                    <a href="#" class="social-link" aria-label="YouTube">
                        <span class="dashicons dashicons-youtube"></span>
                    </a>
                </div>
            </div>

            <div class="footer-section">
                <h3><?php esc_html_e('Quick Links', 'tetradkata'); ?></h3>
                <ul class="footer-menu">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>#home"><?php esc_html_e('Home', 'tetradkata'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/')); ?>#shop"><?php esc_html_e('Shop', 'tetradkata'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/')); ?>#about"><?php esc_html_e('About', 'tetradkata'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/')); ?>#faq"><?php esc_html_e('FAQ', 'tetradkata'); ?></a></li>
                    <?php if (class_exists('WooCommerce')) : ?>
                        <li><a href="<?php echo esc_url(wc_get_cart_url()); ?>"><?php esc_html_e('Cart', 'tetradkata'); ?></a></li>
                        <li><a href="<?php echo esc_url(wc_get_checkout_url()); ?>"><?php esc_html_e('Checkout', 'tetradkata'); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="footer-section">
                <h3><?php esc_html_e('Customer Service', 'tetradkata'); ?></h3>
                <ul class="footer-menu">
                    <li><a href="<?php echo esc_url(home_url('/delivery')); ?>"><?php esc_html_e('Delivery', 'tetradkata'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/returns')); ?>"><?php esc_html_e('Returns', 'tetradkata'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/terms')); ?>"><?php esc_html_e('Terms & Conditions', 'tetradkata'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/privacy')); ?>"><?php esc_html_e('Privacy Policy', 'tetradkata'); ?></a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3><?php esc_html_e('Contact', 'tetradkata'); ?></h3>
                <div class="contact-info">
                    <p>
                        <span class="dashicons dashicons-email"></span>
                        <a href="mailto:<?php echo esc_attr(antispambot('thenotebook.sales@gmail.com')); ?>">
                            <?php echo esc_html(antispambot('thenotebook.sales@gmail.com')); ?>
                        </a>
                    </p>
                    <?php 
                    $phone = get_theme_mod('contact_phone', '+359888123456');
                    $phone_display = get_theme_mod('contact_phone', '+359 888 123 456');
                    ?>
                    <p>
                        <span class="dashicons dashicons-phone"></span>
                        <a href="tel:<?php echo esc_attr(str_replace(' ', '', $phone)); ?>">
                            <?php echo esc_html($phone_display); ?>
                        </a>
                    </p>
                    <p>
                        <span class="dashicons dashicons-location"></span>
                        <?php esc_html_e('Varna, Vazrazhdane, bl. 30, ent. 2, apt. 33', 'tetradkata'); ?>
                    </p>
                    <p>
                        <span class="dashicons dashicons-clock"></span>
                        <?php esc_html_e('Mon-Fri: 9:00 - 18:00', 'tetradkata'); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="copyright">
                    <p>&copy; <?php echo esc_html(date('Y')); ?> <?php esc_html_e('Tetradkata. All rights reserved.', 'tetradkata'); ?></p>
                    <p><?php esc_html_e('Author: Miroslava Indzhova | Design: Maria Ivanova, Doroteya Mareva, Daniela Dineva', 'tetradkata'); ?></p>
                </div>
                
                <div class="payment-methods">
                    <span><?php esc_html_e('We accept cards and cash on delivery', 'tetradkata'); ?></span>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Single Cart Modal Instance -->
<?php get_template_part('template-parts/cart', 'modal'); ?>

<!-- Cookie Banner -->
<div id="cookie-banner" class="cookie-banner" style="display: none;">
    <div class="cookie-content">
        <div class="cookie-message">
            <p>
                <?php 
                printf(
                    /* translators: %s: Privacy policy link */
                    esc_html__('We use cookies to improve your experience. By continuing to use the site, you agree to our %s.', 'tetradkata'),
                    '<a href="' . esc_url(get_privacy_policy_url()) . '" class="cookie-link">' . esc_html__('privacy policy', 'tetradkata') . '</a>'
                );
                ?>
            </p>
        </div>
        <div class="cookie-actions">
            <button id="accept-cookies" class="btn btn-primary btn-small"><?php esc_html_e('Accept', 'tetradkata'); ?></button>
            <button id="decline-cookies" class="btn btn-secondary btn-small"><?php esc_html_e('Decline', 'tetradkata'); ?></button>
        </div>
    </div>
</div>

<!-- Notification Container -->
<div id="notification-container" class="notification-container"></div>

<!-- Scroll to Top Button -->
<button id="scroll-to-top" class="scroll-to-top" style="display: none;" aria-label="<?php esc_attr_e('Scroll to top', 'tetradkata'); ?>">
    <span class="dashicons dashicons-arrow-up-alt"></span>
</button>

<?php wp_footer(); ?>

</body>
</html>