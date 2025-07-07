</main><!-- #main -->

<!-- Footer -->
<footer id="colophon" class="site-footer">
    <div class="container">
        <div class="footer-content">
            <!-- About Section -->
            <div class="footer-section">
                <h3>Тетрадката</h3>
                <p>Личният ви дневник за пътешествия, спомени и вдъхновение – събрани в една тетрадка.</p>
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

            <!-- Quick Links -->
            <div class="footer-section">
                <h3>Бързи връзки</h3>
                <ul class="footer-menu">
                    <li><a href="#home">Начало</a></li>
                    <li><a href="#shop">Магазин</a></li>
                    <li><a href="#about">За нас</a></li>
                    <li><a href="#faq">Въпроси</a></li>
                    <?php if (class_exists('WooCommerce')) : ?>
                        <li><a href="<?php echo wc_get_cart_url(); ?>">Количка</a></li>
                        <li><a href="<?php echo wc_get_checkout_url(); ?>">Плащане</a></li>
                        <li><a href="<?php echo wc_get_account_url(); ?>">Моят профил</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Customer Service -->
            <div class="footer-section">
                <h3>Обслужване</h3>
                <ul class="footer-menu">
                    <li><a href="#delivery">Доставка</a></li>
                    <li><a href="#returns">Връщания</a></li>
                    <li><a href="#payment">Плащане</a></li>
                    <li><a href="#size-guide">Ръководство</a></li>
                    <li><a href="#terms">Условия</a></li>
                    <li><a href="#privacy">Поверителност</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="footer-section">
                <h3>Контакт</h3>
                <div class="contact-info">
                    <p>
                        <span class="dashicons dashicons-email"></span>
                        <a href="mailto:<?php echo get_theme_mod('contact_email', 'info@tetradkata.bg'); ?>">
                            <?php echo get_theme_mod('contact_email', 'info@tetradkata.bg'); ?>
                        </a>
                    </p>
                    <p>
                        <span class="dashicons dashicons-phone"></span>
                        <a href="tel:<?php echo str_replace(' ', '', get_theme_mod('contact_phone', '+359888123456')); ?>">
                            <?php echo get_theme_mod('contact_phone', '+359 888 123 456'); ?>
                        </a>
                    </p>
                    <p>
                        <span class="dashicons dashicons-clock"></span>
                        Пон-Пет: 9:00 - 18:00
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="copyright">
                    <p>&copy; <?php echo date('Y'); ?> Тетрадката. Всички права запазени.</p>
                    <p>Автор: Мирослава Инджова | Графичен дизайн: Мария Иванова, Доротея Марева, Даниела Динева</p>
                </div>
                
                <div class="payment-methods">
                    <span>Приемаме:</span>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment-visa.png" alt="Visa" class="payment-icon">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment-mastercard.png" alt="Mastercard" class="payment-icon">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/payment-viva.png" alt="Viva Wallet" class="payment-icon">
                    <span class="payment-cod">Наложен платеж</span>
                </div>
            </div>
        </div>
    </div>
</footer><!-- #colophon -->

<!-- GDPR Cookie Banner -->
<div id="cookie-banner" class="cookie-banner" style="display: none;">
    <div class="cookie-content">
        <div class="cookie-message">
            <p>Използваме бисквитки за да подобрим вашето изживяване. Продължавайки да използвате сайта, вие се съгласявате с нашата 
            <a href="#privacy-policy" class="cookie-link">политика за поверителност</a>.</p>
        </div>
        <div class="cookie-actions">
            <button id="accept-cookies" class="btn btn-primary btn-small">Приемам</button>
            <button id="decline-cookies" class="btn btn-secondary btn-small">Отказвам</button>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
<div id="notification-container" class="notification-container"></div>

<?php wp_footer(); ?>

<!-- Custom Footer Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Header scroll effect
    const header = document.querySelector('.site-header');
    const scrollToTopBtn = document.getElementById('scroll-to-top');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 100) {
            header.classList.add('header-scrolled');
            scrollToTopBtn.style.display = 'block';
        } else {
            header.classList.remove('header-scrolled');
            scrollToTopBtn.style.display = 'none';
        }
    });

    // Scroll to top functionality
    scrollToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // FAQ accordion
    document.querySelectorAll('.faq-question').forEach(button => {
        button.addEventListener('click', function() {
            const faqItem = this.parentElement;
            const isActive = faqItem.classList.contains('active');
            
            // Close all FAQ items
            document.querySelectorAll('.faq-item').forEach(item => {
                item.classList.remove('active');
                item.querySelector('.faq-icon').textContent = '+';
            });
            
            // Open clicked item if it wasn't active
            if (!isActive) {
                faqItem.classList.add('active');
                this.querySelector('.faq-icon').textContent = '−';
            }
        });
    });

    // Cookie banner functionality
    const cookieBanner = document.getElementById('cookie-banner');
    const acceptCookies = document.getElementById('accept-cookies');
    const declineCookies = document.getElementById('decline-cookies');
    
    // Check if user has already made a choice
    if (!localStorage.getItem('cookieConsent')) {
        setTimeout(() => {
            cookieBanner.style.display = 'block';
        }, 2000);
    }
    
    acceptCookies.addEventListener('click', function() {
        localStorage.setItem('cookieConsent', 'accepted');
        cookieBanner.style.display = 'none';
        
        // Initialize tracking scripts here if accepted
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'analytics_storage': 'granted'
            });
        }
        
        if (typeof fbq !== 'undefined') {
            fbq('consent', 'grant');
        }
    });
    
    declineCookies.addEventListener('click', function() {
        localStorage.setItem('cookieConsent', 'declined');
        cookieBanner.style.display = 'none';
    });

    // Add to cart functionality
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const originalText = this.textContent;
            
            // Show loading state
            this.innerHTML = '<span class="loading"></span> Добавя...';
            this.disabled = true;
            
            // Simulate AJAX request (replace with actual WooCommerce AJAX)
            const formData = new FormData();
            formData.append('action', 'tetradkata_add_to_cart');
            formData.append('product_id', productId);
            formData.append('quantity', 1);
            formData.append('nonce', tetradkata_ajax.nonce);
            
            fetch(tetradkata_ajax.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count
                    document.getElementById('cart-count').textContent = data.data.cart_count;
                    document.getElementById('cart-count').classList.add('cart-count');
                    
                    // Show success message
                    showNotification('Продуктът е добавен в количката!', 'success');
                    
                    // Track event for analytics
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'add_to_cart', {
                            'currency': 'BGN',
                            'value': 0, // Add actual price
                            'items': [{
                                'item_id': productId,
                                'item_name': productName,
                                'quantity': 1
                            }]
                        });
                    }
                    
                    if (typeof fbq !== 'undefined') {
                        fbq('track', 'AddToCart', {
                            content_name: productName,
                            content_ids: [productId],
                            content_type: 'product',
                            value: 0, // Add actual price
                            currency: 'BGN'
                        });
                    }
                } else {
                    showNotification(data.data.message || 'Възникна грешка', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Възникна грешка при добавяне на продукта', 'error');
            })
            .finally(() => {
                // Restore button state
                this.textContent = originalText;
                this.disabled = false;
            });
        });
    });
});

// Notification system
function showNotification(message, type = 'info') {
    const container = document.getElementById('notification-container');
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <span class="notification-message">${message}</span>
        <button class="notification-close">&times;</button>
    `;
    
    container.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        hideNotification(notification);
    }, 5000);
    
    // Close button functionality
    notification.querySelector('.notification-close').addEventListener('click', () => {
        hideNotification(notification);
    });
}

function hideNotification(notification) {
    notification.classList.remove('show');
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
}

// Function to initialize Swiper
function initSwiper() {
    if (typeof Swiper !== 'undefined' && document.querySelector('.tetradkata-swiper')) {
        const swiper = new Swiper('.tetradkata-swiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            on: {
                init: function() {
                    console.log('Swiper carousel initialized successfully');
                }
            }
        });
        
        return swiper;
    }
    return null;
}

// Initialize Swiper when available
document.addEventListener('DOMContentLoaded', function() {
    // Try to initialize immediately
    let swiperInstance = initSwiper();
    
    // If Swiper isn't loaded yet, try again after scripts load
    if (!swiperInstance) {
        window.addEventListener('load', function() {
            setTimeout(initSwiper, 100);
        });
    }
    
    // Additional fallback - retry every 500ms for up to 5 seconds
    if (!swiperInstance) {
        let attempts = 0;
        const maxAttempts = 10;
        const retryInterval = setInterval(function() {
            attempts++;
            swiperInstance = initSwiper();
            
            if (swiperInstance || attempts >= maxAttempts) {
                clearInterval(retryInterval);
                if (!swiperInstance) {
                    console.warn('Swiper could not be initialized after multiple attempts');
                }
            }
        }, 500);
    }
});
</script>

<style>
/* Footer Styles */
.site-footer {
    background: var(--charcoal);
    color: var(--white);
    padding: 60px 0 30px;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 40px;
    margin-bottom: 40px;
}

.footer-section h3 {
    color: var(--gold-end);
    margin-bottom: 20px;
    font-family: var(--font-heading);
}

.footer-menu {
    list-style: none;
    padding: 0;
}

.footer-menu li {
    margin-bottom: 10px;
}

.footer-menu a {
    color: var(--white);
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-menu a:hover {
    color: var(--gold-end);
}

.social-links {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.social-link {
    display: inline-block;
    width: 40px;
    height: 40px;
    background: var(--primary-taupe);
    border-radius: 50%;
    text-align: center;
    line-height: 40px;
    color: var(--white);
    transition: all 0.3s ease;
}

.social-link:hover {
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
    transform: translateY(-2px);
}

.contact-info p {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.contact-info .dashicons {
    margin-right: 10px;
    color: var(--gold-end);
}

.contact-info a {
    color: var(--white);
    text-decoration: none;
}

.contact-info a:hover {
    color: var(--gold-end);
}

.footer-bottom {
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 30px;
}

.footer-bottom-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.copyright p {
    margin: 5px 0;
    font-size: 14px;
    opacity: 0.8;
}

.payment-methods {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
}

.payment-icon {
    height: 25px;
    width: auto;
    opacity: 0.8;
}

.payment-cod {
    background: var(--warm-beige);
    color: var(--charcoal);
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}

/* Cookie Banner */
.cookie-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--charcoal);
    color: var(--white);
    padding: 20px;
    z-index: 10000;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.2);
}

.cookie-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    gap: 20px;
}

.cookie-message p {
    margin: 0;
    font-size: 14px;
}

.cookie-link {
    color: var(--gold-end);
    text-decoration: underline;
}

.cookie-actions {
    display: flex;
    gap: 10px;
    flex-shrink: 0;
}

.btn-small {
    padding: 8px 16px;
    font-size: 14px;
}

/* Notifications */
.notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
}

.notification {
    background: var(--white);
    border-radius: 10px;
    padding: 15px 20px;
    margin-bottom: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-width: 300px;
    transform: translateX(100%);
    transition: transform 0.3s ease;
}

.notification.show {
    transform: translateX(0);
}

.notification-success {
    border-left: 4px solid #22c55e;
}

.notification-error {
    border-left: 4px solid #ef4444;
}

.notification-info {
    border-left: 4px solid #3b82f6;
}

.notification-close {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: var(--charcoal);
    margin-left: 15px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 30px;
    }
    
    .footer-bottom-content {
        flex-direction: column;
        text-align: center;
    }
    
    .cookie-content {
        flex-direction: column;
        text-align: center;
    }
    
    .payment-methods {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .notification {
        min-width: auto;
        margin: 0 20px 10px;
    }
}

/* Print styles */
@media print {
    .site-footer,
    .cookie-banner,
    .notification-container,
    .scroll-to-top {
        display: none !important;
    }
}
</style>

</body>
</html>