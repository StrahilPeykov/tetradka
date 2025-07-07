/**
 * Tetradkata Theme JavaScript
 * Main functionality for the travel journal website
 */

(function($) {
    'use strict';
    
    // DOM Ready
    $(document).ready(function() {
        initializeTheme();
    });
    
    // Window Load
    $(window).on('load', function() {
        initializeAnimations();
        hideLoadingOverlay();
    });
    
    /**
     * Initialize main theme functionality
     */
    function initializeTheme() {
        initializeSwiper();
        initializeSmoothScrolling();
        initializeHeaderEffects();
        initializeFAQ();
        initializeCartFunctionality();
        initializeCookieBanner();
        initializeFormValidation();
        initializeAnimationObserver();
        initializeKeyboardNavigation();
        initializeAnalytics();
    }
    
    /**
     * Initialize Swiper Carousel
     */
    function initializeSwiper() {
        // Wait for Swiper to be available
        if (typeof Swiper !== 'undefined') {
            const swiper = new Swiper('.tetradkata-swiper', {
                // Basic settings
                slidesPerView: 1,
                spaceBetween: 0,
                loop: true,
                grabCursor: true,
                centeredSlides: true,
                
                // Autoplay
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },
                
                // Effect
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                
                // Navigation
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                
                // Pagination
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                    dynamicBullets: true,
                },
                
                // Responsive breakpoints
                breakpoints: {
                    640: {
                        effect: 'slide',
                    },
                    768: {
                        effect: 'fade',
                    }
                },
                
                // Callbacks
                on: {
                    init: function() {
                        console.log('Swiper initialized successfully');
                    },
                    slideChange: function() {
                        // Track slide changes for analytics
                        trackEvent('carousel_slide_change', {
                            slide_index: this.activeIndex
                        });
                    }
                }
            });
            
            // Pause autoplay on hover
            $('.tetradkata-swiper').hover(
                function() { if (swiper.autoplay) swiper.autoplay.stop(); },
                function() { if (swiper.autoplay) swiper.autoplay.start(); }
            );
            
            return swiper;
        } else {
            // Retry after a short delay if Swiper isn't loaded yet
            setTimeout(initializeSwiper, 500);
        }
    }
    
    /**
     * Initialize smooth scrolling
     */
    function initializeSmoothScrolling() {
        // Smooth scrolling for anchor links
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            
            const target = $(this.getAttribute('href'));
            if (target.length) {
                const offsetTop = target.offset().top - 80; // Account for fixed header
                
                $('html, body').animate({
                    scrollTop: offsetTop
                }, 800, 'easeInOutCubic');
                
                // Track navigation clicks
                trackEvent('navigation_click', {
                    target_section: this.getAttribute('href')
                });
            }
        });
        
        // Scroll to top button
        $('#scroll-to-top').on('click', function() {
            $('html, body').animate({
                scrollTop: 0
            }, 600, 'easeInOutCubic');
            
            trackEvent('scroll_to_top_click');
        });
    }
    
    /**
     * Initialize header effects
     */
    function initializeHeaderEffects() {
        const $header = $('.site-header');
        const $scrollToTop = $('#scroll-to-top');
        let lastScrollTop = 0;
        
        $(window).on('scroll', function() {
            const scrollTop = $(this).scrollTop();
            
            // Header scroll effects
            if (scrollTop > 100) {
                $header.addClass('header-scrolled');
                $scrollToTop.fadeIn();
            } else {
                $header.removeClass('header-scrolled');
                $scrollToTop.fadeOut();
            }
            
            // Hide header on scroll down, show on scroll up
            if (scrollTop > lastScrollTop && scrollTop > 200) {
                $header.addClass('header-hidden');
            } else {
                $header.removeClass('header-hidden');
            }
            
            lastScrollTop = scrollTop;
        });
    }
    
    /**
     * Initialize FAQ accordion
     */
    function initializeFAQ() {
        $('.faq-question').on('click', function() {
            const $faqItem = $(this).parent();
            const $faqAnswer = $faqItem.find('.faq-answer');
            const $faqIcon = $(this).find('.faq-icon');
            const isActive = $faqItem.hasClass('active');
            
            // Close all FAQ items
            $('.faq-item').removeClass('active');
            $('.faq-answer').slideUp(300);
            $('.faq-icon').text('+');
            
            // Open clicked item if it wasn't active
            if (!isActive) {
                $faqItem.addClass('active');
                $faqAnswer.slideDown(300);
                $faqIcon.text('−');
                
                // Track FAQ interactions
                trackEvent('faq_open', {
                    question: $(this).text().trim()
                });
            }
        });
    }
    
    /**
     * Initialize cart functionality
     */
    function initializeCartFunctionality() {
        // Add to cart buttons
        $(document).on('click', '.add-to-cart-btn', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const productId = $button.data('product-id');
            const productName = $button.data('product-name');
            const quantity = $button.data('quantity') || 1;
            const originalText = $button.text();
            
            // Show loading state
            $button.html('<span class="loading"></span> Добавя...')
                   .prop('disabled', true);
            
            // AJAX request
            $.ajax({
                url: tetradkata_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'tetradkata_add_to_cart',
                    product_id: productId,
                    quantity: quantity,
                    nonce: tetradkata_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Update cart count
                        updateCartCount(response.data.cart_count);
                        
                        // Show success message
                        showNotification('Продуктът е добавен в количката!', 'success');
                        
                        // Track successful add to cart
                        trackEvent('add_to_cart', {
                            item_id: productId,
                            item_name: productName,
                            quantity: quantity
                        });
                        
                        // Button success animation
                        $button.addClass('btn-success')
                               .text('✓ Добавено');
                        
                        setTimeout(function() {
                            $button.removeClass('btn-success')
                                   .text(originalText);
                        }, 2000);
                        
                    } else {
                        showNotification(response.data.message || 'Възникна грешка', 'error');
                    }
                },
                error: function() {
                    showNotification('Възникна грешка при добавяне на продукта', 'error');
                },
                complete: function() {
                    $button.prop('disabled', false);
                    if (!$button.hasClass('btn-success')) {
                        $button.text(originalText);
                    }
                }
            });
        });
        
        // Quantity selectors
        $(document).on('click', '.quantity-plus', function() {
            const $input = $(this).siblings('.quantity-input');
            const currentValue = parseInt($input.val()) || 1;
            $input.val(currentValue + 1).trigger('change');
        });
        
        $(document).on('click', '.quantity-minus', function() {
            const $input = $(this).siblings('.quantity-input');
            const currentValue = parseInt($input.val()) || 1;
            if (currentValue > 1) {
                $input.val(currentValue - 1).trigger('change');
            }
        });
        
        // Cart toggle
        $('#cart-toggle').on('click', function(e) {
            e.preventDefault();
            toggleCartModal();
        });
    }
    
    /**
     * Update cart count
     */
    function updateCartCount(count) {
        const $cartCount = $('#cart-count');
        $cartCount.text(count);
        
        // Animation
        $cartCount.addClass('cart-count-updated');
        setTimeout(function() {
            $cartCount.removeClass('cart-count-updated');
        }, 600);
    }
    
    /**
     * Toggle cart modal
     */
    function toggleCartModal() {
        const $modal = $('#cart-modal');
        
        if ($modal.is(':visible')) {
            $modal.fadeOut(300);
        } else {
            loadCartContents();
            $modal.fadeIn(300);
        }
    }
    
    /**
     * Load cart contents
     */
    function loadCartContents() {
        $.ajax({
            url: tetradkata_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'tetradkata_get_cart_contents',
                nonce: tetradkata_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('.cart-items').html(response.data.cart_html);
                    $('#cart-total-amount').text(response.data.cart_total);
                }
            }
        });
    }
    
    /**
     * Initialize cookie banner
     */
    function initializeCookieBanner() {
        const $cookieBanner = $('#cookie-banner');
        
        // Check if user has already made a choice
        if (!localStorage.getItem('cookieConsent')) {
            setTimeout(function() {
                $cookieBanner.fadeIn(500);
            }, 2000);
        }
        
        // Accept cookies
        $('#accept-cookies').on('click', function() {
            localStorage.setItem('cookieConsent', 'accepted');
            $cookieBanner.fadeOut(300);
            
            // Initialize tracking
            initializeTrackingScripts();
            
            trackEvent('cookie_consent', { status: 'accepted' });
        });
        
        // Decline cookies
        $('#decline-cookies').on('click', function() {
            localStorage.setItem('cookieConsent', 'declined');
            $cookieBanner.fadeOut(300);
            
            trackEvent('cookie_consent', { status: 'declined' });
        });
    }
    
    /**
     * Initialize tracking scripts after consent
     */
    function initializeTrackingScripts() {
        // Google Analytics
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'analytics_storage': 'granted',
                'ad_storage': 'granted'
            });
        }
        
        // Facebook Pixel
        if (typeof fbq !== 'undefined') {
            fbq('consent', 'grant');
        }
    }
    
    /**
     * Initialize form validation
     */
    function initializeFormValidation() {
        // Contact forms
        $('form').on('submit', function(e) {
            const $form = $(this);
            let isValid = true;
            
            // Validate required fields
            $form.find('[required]').each(function() {
                const $field = $(this);
                if (!$field.val().trim()) {
                    $field.addClass('error');
                    isValid = false;
                } else {
                    $field.removeClass('error');
                }
            });
            
            // Validate email fields
            $form.find('input[type="email"]').each(function() {
                const $field = $(this);
                const email = $field.val().trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (email && !emailRegex.test(email)) {
                    $field.addClass('error');
                    isValid = false;
                } else {
                    $field.removeClass('error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Моля, попълнете всички задължителни полета правилно.', 'error');
            }
        });
        
        // Remove error class on input
        $('input, textarea, select').on('input change', function() {
            $(this).removeClass('error');
        });
    }
    
    /**
     * Initialize animation observer
     */
    function initializeAnimationObserver() {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            // Observe elements for animation
            document.querySelectorAll('.product-card, .step, .faq-item').forEach(function(el) {
                observer.observe(el);
            });
        }
    }
    
    /**
     * Initialize keyboard navigation
     */
    function initializeKeyboardNavigation() {
        // ESC key to close modals
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('#cart-modal').fadeOut(300);
                $('.product-modal').fadeOut(300);
            }
        });
        
        // Focus management
        $('a, button, input, textarea, select').on('focus', function() {
            $(this).addClass('focused');
        }).on('blur', function() {
            $(this).removeClass('focused');
        });
    }
    
    /**
     * Initialize analytics
     */
    function initializeAnalytics() {
        // Track page view
        trackEvent('page_view', {
            page_title: document.title,
            page_location: window.location.href
        });
        
        // Track scroll depth
        let maxScroll = 0;
        $(window).on('scroll', throttle(function() {
            const scrollPercent = Math.round(($(window).scrollTop() / ($(document).height() - $(window).height())) * 100);
            
            if (scrollPercent > maxScroll) {
                maxScroll = scrollPercent;
                
                if (maxScroll % 25 === 0 && maxScroll > 0) {
                    trackEvent('scroll_depth', { percent: maxScroll });
                }
            }
        }, 1000));
        
        // Track time on page
        let startTime = Date.now();
        $(window).on('beforeunload', function() {
            const timeOnPage = Math.round((Date.now() - startTime) / 1000);
            trackEvent('time_on_page', { seconds: timeOnPage });
        });
    }
    
    /**
     * Track events for analytics
     */
    function trackEvent(eventName, parameters = {}) {
        // Only track if consent given
        if (localStorage.getItem('cookieConsent') !== 'accepted') {
            return;
        }
        
        // Google Analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, parameters);
        }
        
        // Facebook Pixel
        if (typeof fbq !== 'undefined') {
            fbq('trackCustom', eventName, parameters);
        }
        
        // Console log for debugging
        console.log('Event tracked:', eventName, parameters);
    }
    
    /**
     * Initialize animations
     */
    function initializeAnimations() {
        // Fade in elements
        $('.fade-in').each(function(index) {
            $(this).delay(100 * index).fadeIn(600);
        });
        
        // Count up numbers
        $('.count-up').each(function() {
            const $this = $(this);
            const countTo = parseInt($this.text());
            
            $({ countNum: 0 }).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'swing',
                step: function() {
                    $this.text(Math.floor(this.countNum));
                },
                complete: function() {
                    $this.text(countTo);
                }
            });
        });
    }
    
    /**
     * Hide loading overlay
     */
    function hideLoadingOverlay() {
        $('#loading-overlay').fadeOut(500);
    }
    
    /**
     * Show notification
     */
    function showNotification(message, type = 'info', duration = 5000) {
        const $notification = $(`
            <div class="notification notification-${type}">
                <span class="notification-message">${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `);
        
        $('#notification-container').append($notification);
        
        // Show notification
        setTimeout(function() {
            $notification.addClass('show');
        }, 100);
        
        // Auto hide
        setTimeout(function() {
            hideNotification($notification);
        }, duration);
        
        // Close button
        $notification.find('.notification-close').on('click', function() {
            hideNotification($notification);
        });
    }
    
    /**
     * Hide notification
     */
    function hideNotification($notification) {
        $notification.removeClass('show');
        setTimeout(function() {
            $notification.remove();
        }, 300);
    }
    
    /**
     * Throttle function
     */
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
    
    /**
     * Debounce function
     */
    function debounce(func, wait, immediate) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }
    
    /**
     * Utility functions
     */
    window.TetradkataTheme = {
        showNotification: showNotification,
        hideNotification: hideNotification,
        trackEvent: trackEvent,
        updateCartCount: updateCartCount,
        toggleCartModal: toggleCartModal
    };
    
})(jQuery);

// Additional CSS animations and styles
const additionalStyles = `
    <style>
    /* Animation classes */
    .animate-in {
        opacity: 1;
        transform: translateY(0);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
    
    .product-card,
    .step,
    .faq-item {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
    
    /* Button animations */
    .btn-success {
        background: #22c55e !important;
        transform: scale(1.05);
    }
    
    .cart-count-updated {
        animation: cartBounce 0.6s ease-in-out;
    }
    
    @keyframes cartBounce {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.3); }
    }
    
    /* Focus styles for accessibility */
    .focused,
    *:focus {
        outline: 2px solid var(--gold-start);
        outline-offset: 2px;
    }
    
    /* Header hide animation */
    .header-hidden {
        transform: translateY(-100%);
        transition: transform 0.3s ease;
    }
    
    /* Error styles */
    .error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
    }
    
    /* Loading states */
    .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .notification {
            margin: 0 10px 10px;
            min-width: auto;
        }
        
        .cookie-banner {
            padding: 15px;
        }
        
        .cookie-content {
            flex-direction: column;
            gap: 15px;
        }
    }
    
    /* Print optimizations */
    @media print {
        .btn,
        .notification,
        .cookie-banner,
        .swiper-button-next,
        .swiper-button-prev,
        .swiper-pagination {
            display: none !important;
        }
    }
    </style>
`;

// Inject additional styles
document.head.insertAdjacentHTML('beforeend', additionalStyles);