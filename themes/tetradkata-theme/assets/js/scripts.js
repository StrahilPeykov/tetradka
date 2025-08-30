/**
 * Tetradkata Theme JavaScript - Consolidated and Improved
 * 
 * @package TetradkataTheme
 */

(function($) {
    'use strict';
    
    // Theme configuration
    const config = {
        ajaxUrl: tetradkata_ajax?.ajax_url || '',
        nonce: tetradkata_ajax?.nonce || '',
        translations: tetradkata_ajax?.translations || {},
        isWooCommerceActive: tetradkata_ajax?.wc_active === 'yes',
        animationDuration: 300,
        notificationDuration: 5000,
        throttleDelay: 250,
        debounceDelay: 500
    };

    // State management
    const state = {
        cartOpen: false,
        cartLoading: false,
        processingRequests: new Set(),
        scrollPosition: 0,
        headerHeight: 80
    };

    /**
     * Initialize on DOM ready
     */
    $(document).ready(function() {
        initializeTheme();
    });

    /**
     * Initialize on window load
     */
    $(window).on('load', function() {
        initializePostLoad();
    });

    /**
     * Main initialization
     */
    function initializeTheme() {
        // Core features
        initializeNavigation();
        initializeHeader();
        initializeSmoothScrolling();
        
        // Interactive elements
        initializeFAQ();
        initializeSwiper();
        
        // E-commerce features
        if (config.isWooCommerceActive) {
            initializeCart();
            initializeQuickView();
        }
        
        // User preferences
        initializeCookieBanner();
        
        // Forms
        initializeFormValidation();
        
        // Accessibility
        initializeKeyboardNavigation();
        
        // Performance
        initializeLazyLoading();
        
        console.log('Tetradkata theme initialized');
    }

    /**
     * Post-load initialization
     */
    function initializePostLoad() {
        initializeAnimations();
        hideLoadingOverlay();
        initializeAnalytics();
    }

    /**
     * Navigation
     */
    function initializeNavigation() {
        const $menuToggle = $('.menu-toggle');
        const $navMenu = $('.nav-menu');
        
        $menuToggle.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            $(this).toggleClass('active');
            $navMenu.toggleClass('active');
            $(this).attr('aria-expanded', $(this).hasClass('active'));
            
            // Prevent body scroll when menu is open
            if ($(this).hasClass('active')) {
                $('body').addClass('mobile-menu-open');
            } else {
                $('body').removeClass('mobile-menu-open');
            }
        });
        
        // Close menu on navigation link click
        $navMenu.find('a').on('click', function() {
            $menuToggle.removeClass('active');
            $navMenu.removeClass('active');
            $menuToggle.attr('aria-expanded', 'false');
            $('body').removeClass('mobile-menu-open');
        });
        
        // Close menu on outside click
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.main-navigation').length) {
                $menuToggle.removeClass('active');
                $navMenu.removeClass('active');
                $menuToggle.attr('aria-expanded', 'false');
                $('body').removeClass('mobile-menu-open');
            }
        });
        
        // Close menu on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $navMenu.hasClass('active')) {
                $menuToggle.removeClass('active');
                $navMenu.removeClass('active');
                $menuToggle.attr('aria-expanded', 'false');
                $('body').removeClass('mobile-menu-open');
            }
        });
        
        // Handle window resize
        $(window).on('resize', function() {
            if ($(window).width() > 768) {
                $menuToggle.removeClass('active');
                $navMenu.removeClass('active');
                $menuToggle.attr('aria-expanded', 'false');
                $('body').removeClass('mobile-menu-open');
            }
        });
    }

    /**
     * Header effects
     */
    function initializeHeader() {
        const $header = $('.site-header');
        const $scrollToTop = $('#scroll-to-top');
        let lastScrollTop = 0;
        let scrollTimer = null;
        
        const handleScroll = throttle(function() {
            const scrollTop = $(window).scrollTop();
            
            // Add/remove scrolled class
            if (scrollTop > 100) {
                $header.addClass('header-scrolled');
                $scrollToTop.fadeIn(config.animationDuration);
            } else {
                $header.removeClass('header-scrolled');
                $scrollToTop.fadeOut(config.animationDuration);
            }
            
            // Hide/show header on scroll
            if (scrollTop > lastScrollTop && scrollTop > 200) {
                $header.addClass('header-hidden');
            } else {
                $header.removeClass('header-hidden');
            }
            
            lastScrollTop = scrollTop;
            state.scrollPosition = scrollTop;
        }, config.throttleDelay);
        
        $(window).on('scroll', handleScroll);
        
        // Scroll to top button
        $scrollToTop.on('click', function() {
            $('html, body').animate({
                scrollTop: 0
            }, 600, 'easeInOutCubic');
            trackEvent('scroll_to_top_click');
        });
    }

    /**
     * Smooth scrolling for anchor links
     */
    function initializeSmoothScrolling() {
        $(document).on('click', 'a[href^="#"]', function(e) {
            const target = $(this.getAttribute('href'));
            
            if (target.length) {
                e.preventDefault();
                const offsetTop = target.offset().top - state.headerHeight;
                
                $('html, body').animate({
                    scrollTop: offsetTop
                }, 800, 'easeInOutCubic');
                
                // Update URL without jumping
                if (history.pushState) {
                    history.pushState(null, null, this.getAttribute('href'));
                }
                
                trackEvent('smooth_scroll', {
                    target: this.getAttribute('href')
                });
            }
        });
    }

    /**
     * FAQ Accordion
     */
    function initializeFAQ() {
        const $faqContainer = $('.faq-container');
        
        $faqContainer.on('click', '.faq-question', function(e) {
            e.preventDefault();
            
            const $faqItem = $(this).parent();
            const $faqAnswer = $faqItem.find('.faq-answer');
            const $faqIcon = $(this).find('.faq-icon');
            const isActive = $faqItem.hasClass('active');
            
            // Close all other items
            $('.faq-item.active').not($faqItem).each(function() {
                $(this).removeClass('active');
                $(this).find('.faq-answer').slideUp(config.animationDuration);
                $(this).find('.faq-icon').text('+');
            });
            
            // Toggle current item
            if (!isActive) {
                $faqItem.addClass('active');
                $faqAnswer.slideDown(config.animationDuration);
                $faqIcon.text('−');
                
                trackEvent('faq_open', {
                    question: $(this).text().trim()
                });
            } else {
                $faqItem.removeClass('active');
                $faqAnswer.slideUp(config.animationDuration);
                $faqIcon.text('+');
            }
        });
    }

    /**
     * Swiper Carousel
     */
    function initializeSwiper() {
        if (typeof Swiper === 'undefined' || !$('.tetradkata-swiper').length) {
            return;
        }
        
        const swiper = new Swiper('.tetradkata-swiper', {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true,
            grabCursor: true,
            centeredSlides: true,
            
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
                dynamicBullets: true,
            },
            
            on: {
                init: function() {
                    console.log('Swiper initialized');
                },
                slideChange: function() {
                    trackEvent('carousel_slide_change', {
                        slide_index: this.activeIndex
                    });
                }
            }
        });
        
        return swiper;
    }

    /**
     * Cart functionality
     */
    function initializeCart() {
        // Cart toggle
        $('#cart-toggle').on('click', function(e) {
            e.preventDefault();
            toggleCartModal();
        });
        
        // Close cart modal
        $(document).on('click', '.close-cart', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeCartModal();
        });
        
        // Cart modal background click
        $(document).on('click', '.cart-modal', function(e) {
            if (e.target === this) {
                closeCartModal();
            }
        });
        
        // Add to cart
        $(document).on('click', '.add-to-cart-btn', handleAddToCart);
        
        // Remove from cart
        $(document).on('click', '.remove-cart-item', handleRemoveFromCart);
        
        // Update cart on WooCommerce events
        $(document.body).on('added_to_cart removed_from_cart', loadCartContents);
    }

    /**
     * Handle add to cart
     */
    function handleAddToCart(e) {
        e.preventDefault();
        
        const $button = $(this);
        
        // Prevent double-click
        if ($button.hasClass('processing')) {
            return;
        }
        
        const productId = $button.data('product-id');
        const productName = $button.data('product-name');
        const quantity = $button.data('quantity') || 1;
        
        if (!productId) {
            console.error('No product ID found');
            showNotification(config.translations.error || 'Грешка', 'error');
            return;
        }
        
        // Set button state
        setButtonState($button, 'processing');
        
        // Make AJAX request
        $.ajax({
            url: config.ajaxUrl,
            type: 'POST',
            data: {
                action: 'tetradkata_add_to_cart',
                product_id: productId,
                quantity: quantity,
                nonce: config.nonce
            },
            success: function(response) {
                if (response.success) {
                    updateCartCount(response.data.cart_count);
                    showNotification(
                        config.translations.added_to_cart || 'Продуктът е добавен в количката!',
                        'success'
                    );
                    setButtonState($button, 'success');
                    
                    // Track event
                    trackEvent('add_to_cart', {
                        product_id: productId,
                        product_name: productName,
                        quantity: quantity
                    });
                } else {
                    showNotification(
                        response.data?.message || config.translations.error || 'Грешка',
                        'error'
                    );
                    setButtonState($button, 'default');
                }
            },
            error: function() {
                showNotification(config.translations.error || 'Грешка', 'error');
                setButtonState($button, 'default');
            }
        });
    }

    /**
     * Handle remove from cart
     */
    function handleRemoveFromCart(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $button = $(this);
        const cartItemKey = $button.data('cart-item-key');
        
        if (!cartItemKey) {
            return;
        }
        
        $button.prop('disabled', true);
        
        $.ajax({
            url: config.ajaxUrl,
            type: 'POST',
            data: {
                action: 'tetradkata_remove_cart_item',
                cart_item_key: cartItemKey,
                nonce: config.nonce
            },
            success: function(response) {
                if (response.success) {
                    loadCartContents();
                    updateCartCount(response.data.cart_count);
                    showNotification(
                        config.translations.removed_from_cart || 'Продуктът е премахнат от количката',
                        'success'
                    );
                } else {
                    showNotification(
                        response.data?.message || config.translations.error || 'Грешка',
                        'error'
                    );
                }
            },
            error: function() {
                showNotification(config.translations.error || 'Грешка', 'error');
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    }

    /**
     * Cart modal functions
     */
    function toggleCartModal() {
        if (state.cartOpen) {
            closeCartModal();
        } else {
            openCartModal();
        }
    }

    function openCartModal() {
        if (state.cartOpen) return;
        
        state.cartOpen = true;
        loadCartContents();
        
        const $modal = $('#cart-modal');
        $modal.addClass('show').fadeIn(config.animationDuration);
        $('body').addClass('modal-open');
        
        // Focus management
        setTimeout(function() {
            $modal.find('.close-cart').focus();
        }, config.animationDuration);
        
        trackEvent('cart_opened');
    }

    function closeCartModal() {
        if (!state.cartOpen) return;
        
        state.cartOpen = false;
        
        const $modal = $('#cart-modal');
        $modal.removeClass('show').fadeOut(config.animationDuration);
        $('body').removeClass('modal-open');
        
        trackEvent('cart_closed');
    }

    function loadCartContents() {
        if (state.cartLoading) return;
        
        state.cartLoading = true;
        
        const $cartItems = $('.cart-items');
        $cartItems.html('<div class="loading-cart"><div class="loading"></div><p>' + 
                        (config.translations.loading || 'Зарежда...') + '</p></div>');
        
        $.ajax({
            url: config.ajaxUrl,
            type: 'POST',
            data: {
                action: 'tetradkata_get_cart_contents',
                nonce: config.nonce
            },
            success: function(response) {
                if (response.success) {
                    $cartItems.html(response.data.cart_html);
                    $('#cart-total-amount').text(response.data.cart_total);
                    updateCartCount(response.data.cart_count);
                    
                    const $checkoutBtn = $('.cart-footer .btn-primary');
                    if (response.data.is_empty) {
                        $checkoutBtn.hide();
                    } else {
                        $checkoutBtn.show();
                    }
                } else {
                    $cartItems.html('<div class="cart-error">' + 
                                  (config.translations.error || 'Грешка при зареждане на количката') + '</div>');
                }
            },
            error: function() {
                $cartItems.html('<div class="cart-error">' + 
                              (config.translations.error || 'Грешка при зареждане на количката') + '</div>');
            },
            complete: function() {
                state.cartLoading = false;
            }
        });
    }

    function updateCartCount(count) {
        const $cartCount = $('.cart-count');
        $cartCount.text(count);
        
        // Animate count update
        $cartCount.addClass('cart-count-updated');
        setTimeout(function() {
            $cartCount.removeClass('cart-count-updated');
        }, 600);
    }

    /**
     * Quick View
     */
    function initializeQuickView() {
        $(document).on('click', '.quick-view-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = $(this).data('product-id');
            loadQuickView(productId);
        });
        
        $(document).on('click', '.modal-close, .modal-overlay', function() {
            $('#quick-view-modal').fadeOut(config.animationDuration);
            $('body').removeClass('modal-open');
        });
    }

    function loadQuickView(productId) {
        const $modal = $('#quick-view-modal');
        const $content = $('.quick-view-content');
        
        $content.html('<div class="loading-spinner"><div class="loading"></div></div>');
        $modal.fadeIn(config.animationDuration);
        $('body').addClass('modal-open');
        
        $.ajax({
            url: config.ajaxUrl,
            type: 'POST',
            data: {
                action: 'tetradkata_quick_view',
                product_id: productId,
                nonce: config.nonce
            },
            success: function(response) {
                if (response.success) {
                    $content.html(response.data.html);
                } else {
                    $content.html('<p>' + (config.translations.error || 'Грешка') + '</p>');
                }
            },
            error: function() {
                $content.html('<p>' + (config.translations.error || 'Грешка') + '</p>');
            }
        });
    }

    /**
     * Cookie Banner
     */
    function initializeCookieBanner() {
        const $cookieBanner = $('#cookie-banner');
        
        // Check if consent already given
        if (!localStorage.getItem('cookieConsent')) {
            setTimeout(function() {
                $cookieBanner.fadeIn(config.animationDuration);
            }, 2000);
        }
        
        $('#accept-cookies').on('click', function() {
            localStorage.setItem('cookieConsent', 'accepted');
            $cookieBanner.fadeOut(config.animationDuration);
            initializeTrackingScripts();
            trackEvent('cookie_consent', { status: 'accepted' });
        });
        
        $('#decline-cookies').on('click', function() {
            localStorage.setItem('cookieConsent', 'declined');
            $cookieBanner.fadeOut(config.animationDuration);
            trackEvent('cookie_consent', { status: 'declined' });
        });
    }

    function initializeTrackingScripts() {
        // Initialize GA4
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'analytics_storage': 'granted',
                'ad_storage': 'granted'
            });
        }
        
        // Initialize Facebook Pixel
        if (typeof fbq !== 'undefined') {
            fbq('consent', 'grant');
        }
    }

    /**
     * Form Validation
     */
    function initializeFormValidation() {
        $('form').on('submit', function(e) {
            const $form = $(this);
            
            // Skip WooCommerce forms
            if ($form.hasClass('cart') || $form.closest('.woocommerce').length) {
                return;
            }
            
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
        
        // Clear error on input
        $('input, textarea, select').on('input change', function() {
            $(this).removeClass('error');
        });
    }

    /**
     * Keyboard Navigation
     */
    function initializeKeyboardNavigation() {
        $(document).on('keydown', function(e) {
            // Escape key
            if (e.key === 'Escape') {
                closeCartModal();
                $('#quick-view-modal').fadeOut(config.animationDuration);
                $('body').removeClass('modal-open');
            }
            
            // Tab trap for modals
            if (state.cartOpen || $('#quick-view-modal').is(':visible')) {
                const $modal = state.cartOpen ? $('#cart-modal') : $('#quick-view-modal');
                const $focusableElements = $modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                const $firstElement = $focusableElements.first();
                const $lastElement = $focusableElements.last();
                
                if (e.key === 'Tab') {
                    if (e.shiftKey) {
                        if (document.activeElement === $firstElement[0]) {
                            e.preventDefault();
                            $lastElement.focus();
                        }
                    } else {
                        if (document.activeElement === $lastElement[0]) {
                            e.preventDefault();
                            $firstElement.focus();
                        }
                    }
                }
            }
        });
    }

    /**
     * Lazy Loading
     */
    function initializeLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            imageObserver.unobserve(img);
                        }
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(function(img) {
                imageObserver.observe(img);
            });
        }
    }

    /**
     * Animations
     */
    function initializeAnimations() {
        if ('IntersectionObserver' in window) {
            const animationObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        animationObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            document.querySelectorAll('.product-card, .step, .faq-item').forEach(function(el) {
                animationObserver.observe(el);
            });
        }
    }

    /**
     * Analytics
     */
    function initializeAnalytics() {
        if (localStorage.getItem('cookieConsent') !== 'accepted') {
            return;
        }
        
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
    }

    function trackEvent(eventName, parameters = {}) {
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
        
        console.log('Event tracked:', eventName, parameters);
    }

    /**
     * UI Helpers
     */
    function setButtonState($button, state) {
        const originalText = $button.data('original-text') || $button.find('.btn-text').text();
        
        if (!$button.data('original-text')) {
            $button.data('original-text', originalText);
        }
        
        switch (state) {
            case 'processing':
                $button.addClass('processing').prop('disabled', true);
                $button.find('.btn-text').hide();
                if ($button.find('.btn-loading').length === 0) {
                    $button.append('<span class="btn-loading"><span class="loading"></span> ' + 
                                 (config.translations.loading || 'Зарежда...') + '</span>');
                }
                $button.find('.btn-loading').show();
                break;
                
            case 'success':
                $button.removeClass('processing').addClass('success').prop('disabled', false);
                $button.find('.btn-text').text('✓ ' + (config.translations.added || 'Добавено')).show();
                $button.find('.btn-loading').hide();
                
                setTimeout(function() {
                    $button.removeClass('success');
                    $button.find('.btn-text').text(originalText);
                }, 2000);
                break;
                
            default:
                $button.removeClass('processing success').prop('disabled', false);
                $button.find('.btn-text').text(originalText).show();
                $button.find('.btn-loading').hide();
        }
    }

    function showNotification(message, type = 'info', duration = config.notificationDuration) {
        // Remove existing notifications
        $('.tetradkata-notification').remove();
        
        const $notification = $(`
            <div class="tetradkata-notification notification-${type}">
                <span class="notification-message">${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `);
        
        $('body').append($notification);
        
        setTimeout(function() {
            $notification.addClass('show');
        }, 100);
        
        const hideTimeout = setTimeout(function() {
            hideNotification($notification);
        }, duration);
        
        $notification.find('.notification-close').on('click', function() {
            clearTimeout(hideTimeout);
            hideNotification($notification);
        });
    }

    function hideNotification($notification) {
        $notification.removeClass('show');
        setTimeout(function() {
            $notification.remove();
        }, config.animationDuration);
    }

    function hideLoadingOverlay() {
        $('#loading-overlay').fadeOut(config.animationDuration);
    }

    /**
     * Utility Functions
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

    function debounce(func, wait, immediate) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
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
     * Public API
     */
    window.TetradkataTheme = {
        showNotification: showNotification,
        hideNotification: hideNotification,
        trackEvent: trackEvent,
        updateCartCount: updateCartCount,
        toggleCartModal: toggleCartModal,
        openCartModal: openCartModal,
        closeCartModal: closeCartModal,
        loadCartContents: loadCartContents,
        config: config,
        state: state
    };

})(jQuery);