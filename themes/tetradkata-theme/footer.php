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
                <h3>Тетрадката</h3>
                <p>Личният ви дневник за пътешествия, спомени и вдъхновение - събрани в една тетрадка.</p>
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
                <h3>Бързи връзки</h3>
                <ul class="footer-menu">
                    <li><a href="#home">Начало</a></li>
                    <li><a href="#shop">Магазин</a></li>
                    <li><a href="#about">За нас</a></li>
                    <li><a href="#faq">Въпроси</a></li>
                    <?php if (class_exists('WooCommerce')) : ?>
                        <li><a href="<?php echo wc_get_cart_url(); ?>">Количка</a></li>
                        <li><a href="<?php echo wc_get_checkout_url(); ?>">Плащане</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Обслужване</h3>
                <ul class="footer-menu">
                    <li><a href="/dostavka">Доставка</a></li>
                    <li><a href="/vrashtania">Връщания</a></li>
                    <li><a href="/usloviya">Условия за ползване</a></li>
                    <li><a href="/poveritelnost">Политика за поверителност</a></li>
                </ul>
            </div>

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

        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="copyright">
                    <p>&copy; <?php echo date('Y'); ?> Тетрадката. Всички права запазени.</p>
                    <p>Автор: Мирослава Инджова | Графичен дизайн: Мария Иванова, Доротея Марева, Даниела Динева</p>
                </div>
                
                <div class="payment-methods">
                    <span>Приемаме карти и наложен платеж</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Cart Modal -->
<div id="cart-modal" class="cart-modal" style="display: none;">
    <div class="cart-modal-content">
        <div class="cart-header">
            <h3>Количка</h3>
            <button class="close-cart" aria-label="Затвори количката">&times;</button>
        </div>
        <div class="cart-items">
            <!-- Cart items will be loaded here -->
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <strong>Общо: <span id="cart-total-amount">0.00 лв.</span></strong>
            </div>
            <?php if (class_exists('WooCommerce')) : ?>
                <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn btn-primary">Към плащане</a>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/checkout')); ?>" class="btn btn-primary">Към плащане</a>
            <?php endif; ?>
        </div>
    </div>
</div>

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

<div id="notification-container" class="notification-container"></div>

<?php wp_footer(); ?>

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

    // Header scroll effects
    const header = document.querySelector('.site-header');
    const scrollToTopBtn = document.getElementById('scroll-to-top');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 100) {
            header.classList.add('header-scrolled');
            if (scrollToTopBtn) {
                scrollToTopBtn.style.display = 'block';
            }
        } else {
            header.classList.remove('header-scrolled');
            if (scrollToTopBtn) {
                scrollToTopBtn.style.display = 'none';
            }
        }
    });

    // Scroll to top functionality
    if (scrollToTopBtn) {
        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Cookie Banner functionality
    const cookieBanner = document.getElementById('cookie-banner');
    const acceptCookies = document.getElementById('accept-cookies');
    const declineCookies = document.getElementById('decline-cookies');
    
    // Show cookie banner if consent not given
    if (!localStorage.getItem('cookieConsent')) {
        setTimeout(() => {
            cookieBanner.style.display = 'block';
            // Add class for smooth animation
            setTimeout(() => {
                cookieBanner.classList.add('cookie-show');
            }, 50);
        }, 2000);
    }
    
    // Accept cookies
    acceptCookies.addEventListener('click', function() {
        localStorage.setItem('cookieConsent', 'accepted');
        hideCookieBanner();
        
        // Initialize tracking scripts
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'analytics_storage': 'granted'
            });
        }
        
        if (typeof fbq !== 'undefined') {
            fbq('consent', 'grant');
        }
    });
    
    // Decline cookies
    declineCookies.addEventListener('click', function() {
        localStorage.setItem('cookieConsent', 'declined');
        hideCookieBanner();
    });
    
    // Function to hide banner with animation
    function hideCookieBanner() {
        cookieBanner.classList.remove('cookie-show');
        setTimeout(() => {
            cookieBanner.style.display = 'none';
        }, 400); // Match the CSS transition duration
    }
});

// Notification system
function showNotification(message, type = 'info') {
    const container = document.getElementById('notification-container');
    if (!container) return;
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <span class="notification-message">${message}</span>
        <button class="notification-close">&times;</button>
    `;
    
    container.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        hideNotification(notification);
    }, 5000);
    
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

// Swiper initialization
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

// Initialize Swiper with multiple attempts
document.addEventListener('DOMContentLoaded', function() {
    let swiperInstance = initSwiper();
    
    if (!swiperInstance) {
        window.addEventListener('load', function() {
            setTimeout(initSwiper, 100);
        });
    }
    
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

// Global functions for theme integration
window.TetradkataTheme = window.TetradkataTheme || {};
window.TetradkataTheme.showNotification = showNotification;
window.TetradkataTheme.hideNotification = hideNotification;
</script>

</body>
</html>