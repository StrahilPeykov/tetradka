/**
 * Tetradkata Theme JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        console.log('Tetradkata scripts loaded');
        initializeTheme();
    });
    
    $(window).on('load', function() {
        initializeAnimations();
        hideLoadingOverlay();
    });
    
    /**
     * Initialize Theme
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
     * Swiper Carousel
     */
    function initializeSwiper() {
        if (typeof Swiper !== 'undefined') {
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
                
                breakpoints: {
                    640: {
                        effect: 'slide',
                    },
                    768: {
                        effect: 'fade',
                    }
                },
                
                on: {
                    init: function() {
                        console.log('Swiper initialized successfully');
                    },
                    slideChange: function() {
                        trackEvent('carousel_slide_change', {
                            slide_index: this.activeIndex
                        });
                    }
                }
            });
            
            $('.tetradkata-swiper').hover(
                function() { if (swiper.autoplay) swiper.autoplay.stop(); },
                function() { if (swiper.autoplay) swiper.autoplay.start(); }
            );
            
            return swiper;
        } else {
            setTimeout(initializeSwiper, 500);
        }
    }
    
    /**
     * Smooth Scrolling
     */
    function initializeSmoothScrolling() {
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            
            const target = $(this.getAttribute('href'));
            if (target.length) {
                const offsetTop = target.offset().top - 80;
                
                $('html, body').animate({
                    scrollTop: offsetTop
                }, 800, 'easeInOutCubic');
                
                trackEvent('navigation_click', {
                    target_section: this.getAttribute('href')
                });
            }
        });
        
        $('#scroll-to-top').on('click', function() {
            $('html, body').animate({
                scrollTop: 0
            }, 600, 'easeInOutCubic');
            
            trackEvent('scroll_to_top_click');
        });
    }
    
    /**
     * Header Effects
     */
    function initializeHeaderEffects() {
        const $header = $('.site-header');
        const $scrollToTop = $('#scroll-to-top');
        let lastScrollTop = 0;
        
        $(window).on('scroll', function() {
            const scrollTop = $(this).scrollTop();
            
            if (scrollTop > 100) {
                $header.addClass('header-scrolled');
                $scrollToTop.fadeIn();
            } else {
                $header.removeClass('header-scrolled');
                $scrollToTop.fadeOut();
            }
            
            if (scrollTop > lastScrollTop && scrollTop > 200) {
                $header.addClass('header-hidden');
            } else {
                $header.removeClass('header-hidden');
            }
            
            lastScrollTop = scrollTop;
        });
    }
    
    /**
     * FAQ Accordion
     */
    function initializeFAQ() {
        $(document).on('click', '.faq-question', function(e) {
            e.preventDefault();
            const $faqItem = $(this).parent();
            const $faqAnswer = $faqItem.find('.faq-answer');
            const $faqIcon = $(this).find('.faq-icon');
            const isActive = $faqItem.hasClass('active');
            
            $('.faq-item').removeClass('active');
            $('.faq-answer').slideUp(300);
            $('.faq-icon').text('+');
            
            if (!isActive) {
                $faqItem.addClass('active');
                $faqAnswer.slideDown(300);
                $faqIcon.text('−');
                
                trackEvent('faq_open', {
                    question: $(this).text().trim()
                });
            }
        });
    }
    
    /**
     * Cart Functionality
     */
    function initializeCartFunctionality() {
        console.log('Initializing cart functionality');
        
        $(document).off('click.tetradkata', '.add-to-cart-btn').on('click.tetradkata', '.add-to-cart-btn', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            
            if ($button.hasClass('processing')) {
                return;
            }
            
            const productId = $button.data('product-id');
            const productName = $button.data('product-name');
            const quantity = $button.data('quantity') || 1;
            
            if (!productId) {
                console.error('No product ID found');
                return;
            }
            
            console.log('Add to cart clicked:', {productId, productName, quantity});
            
            setButtonProcessing($button, true);
            
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
                    console.log('Add to cart response:', response);
                    
                    if (response.success) {
                        updateCartCount(response.data.cart_count);
                        showNotification('Продуктът е добавен в количката!', 'success');
                        setButtonSuccess($button);
                        
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'add_to_cart', {
                                currency: 'BGN',
                                value: parseFloat($button.data('product-price')) || 0,
                                items: [{
                                    item_id: productId,
                                    item_name: productName,
                                    quantity: quantity
                                }]
                            });
                        }
                        
                    } else {
                        showNotification(response.data.message || 'Възникна грешка', 'error');
                        setButtonProcessing($button, false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Add to cart error:', {xhr, status, error});
                    showNotification('Възникна грешка при добавяне на продукта', 'error');
                    setButtonProcessing($button, false);
                }
            });
        });
        
        $(document).off('click.tetradkata', '#cart-toggle').on('click.tetradkata', '#cart-toggle', function(e) {
            e.preventDefault();
            console.log('Cart toggle clicked');
            toggleCartModal();
        });
        
        $(document).off('click.tetradkata', '.close-cart, .cart-modal').on('click.tetradkata', '.close-cart, .cart-modal', function(e) {
            if (e.target === this) {
                console.log('Closing cart modal');
                closeCartModal();
            }
        });
        
        $(document).off('click.tetradkata', '.cart-modal-content').on('click.tetradkata', '.cart-modal-content', function(e) {
            e.stopPropagation();
        });
        
        $(document).off('click.tetradkata', '.remove-cart-item').on('click.tetradkata', '.remove-cart-item', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const cartItemKey = $(this).data('cart-item-key');
            removeCartItem(cartItemKey);
        });
        
        $(document).off('keydown.tetradkata').on('keydown.tetradkata', function(e) {
            if (e.key === 'Escape') {
                closeCartModal();
            }
        });
    }
    
    /**
     * Button States
     */
    function setButtonProcessing($button, processing) {
        if (!$button.data('original-text')) {
            const originalText = $button.find('.btn-text').text() || 'Добави в количката';
            $button.data('original-text', originalText);
        }
        
        if (processing) {
            $button.addClass('processing').prop('disabled', true);
            $button.find('.btn-text').hide();
            
            if ($button.find('.btn-loading').length === 0) {
                $button.append('<span class="btn-loading"><span class="loading"></span> Добавя...</span>');
            }
            
            $button.find('.btn-loading').show();
            
        } else {
            $button.removeClass('processing').prop('disabled', false);
            
            const originalText = $button.data('original-text') || 'Добави в количката';
            $button.find('.btn-text').text(originalText).show();
            $button.find('.btn-loading').hide();
        }
    }
    
    function setButtonSuccess($button) {
        if (!$button.data('original-text')) {
            const originalText = $button.find('.btn-text').text() || 'Добави в количката';
            $button.data('original-text', originalText);
        }
        
        $button.removeClass('processing').addClass('success').prop('disabled', false);
        $button.find('.btn-text').text('✓ Добавено').show();
        $button.find('.btn-loading').hide();
        
        setTimeout(function() {
            $button.removeClass('success');
            const originalText = $button.data('original-text') || 'Добави в количката';
            $button.find('.btn-text').text(originalText);
        }, 2000);
    }
    
    /**
     * Cart Count
     */
    function updateCartCount(count) {
        const $cartCount = $('.cart-count');
        $cartCount.text(count);
        
        $cartCount.addClass('cart-count-updated');
        setTimeout(function() {
            $cartCount.removeClass('cart-count-updated');
        }, 600);
    }
    
    /**
     * Cart Modal
     */
    function toggleCartModal() {
        const $modal = $('#cart-modal');
        
        if ($modal.is(':visible')) {
            closeCartModal();
        } else {
            openCartModal();
        }
    }
    
    function openCartModal() {
        console.log('Opening cart modal');
        loadCartContents();
        $('#cart-modal').fadeIn(300);
        $('body').addClass('modal-open');
    }
    
    function closeCartModal() {
        console.log('Closing cart modal');
        $('#cart-modal').fadeOut(300);
        $('body').removeClass('modal-open');
    }
    
    function loadCartContents() {
        console.log('Loading cart contents');
        
        const $cartItems = $('.cart-items');
        $cartItems.html('<div class="loading-cart"><div class="loading"></div><p>Зарежда...</p></div>');
        
        $.ajax({
            url: tetradkata_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'tetradkata_get_cart_contents',
                nonce: tetradkata_ajax.nonce
            },
            success: function(response) {
                console.log('Cart contents loaded:', response);
                
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
                    $cartItems.html('<div class="cart-error">Грешка при зареждане на количката</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading cart contents:', {xhr, status, error});
                $cartItems.html('<div class="cart-error">Грешка при зареждане на количката</div>');
            }
        });
    }
    
    function removeCartItem(cartItemKey) {
        if (!cartItemKey) {
            console.error('No cart item key provided');
            return;
        }
        
        console.log('Removing cart item:', cartItemKey);
        
        $.ajax({
            url: tetradkata_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'tetradkata_remove_cart_item',
                cart_item_key: cartItemKey,
                nonce: tetradkata_ajax.nonce
            },
            success: function(response) {
                console.log('Remove cart item response:', response);
                
                if (response.success) {
                    loadCartContents();
                    updateCartCount(response.data.cart_count);
                    showNotification('Продуктът е премахнат от количката', 'success');
                } else {
                    showNotification(response.data.message || 'Грешка при премахване', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error removing cart item:', {xhr, status, error});
                showNotification('Грешка при премахване на продукта', 'error');
            }
        });
    }
    
    /**
     * Cookie Banner
     */
    function initializeCookieBanner() {
        const $cookieBanner = $('#cookie-banner');
        
        if (!localStorage.getItem('cookieConsent')) {
            setTimeout(function() {
                $cookieBanner.fadeIn(500);
            }, 2000);
        }
        
        $('#accept-cookies').on('click', function() {
            localStorage.setItem('cookieConsent', 'accepted');
            $cookieBanner.fadeOut(300);
            initializeTrackingScripts();
            trackEvent('cookie_consent', { status: 'accepted' });
        });
        
        $('#decline-cookies').on('click', function() {
            localStorage.setItem('cookieConsent', 'declined');
            $cookieBanner.fadeOut(300);
            trackEvent('cookie_consent', { status: 'declined' });
        });
    }
    
    function initializeTrackingScripts() {
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'analytics_storage': 'granted',
                'ad_storage': 'granted'
            });
        }
        
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
            let isValid = true;
            
            if ($form.hasClass('cart') || $form.closest('.woocommerce').length) {
                return;
            }
            
            $form.find('[required]').each(function() {
                const $field = $(this);
                if (!$field.val().trim()) {
                    $field.addClass('error');
                    isValid = false;
                } else {
                    $field.removeClass('error');
                }
            });
            
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
        
        $('input, textarea, select').on('input change', function() {
            $(this).removeClass('error');
        });
    }
    
    /**
     * Animation Observer
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
            
            document.querySelectorAll('.product-card, .step, .faq-item').forEach(function(el) {
                observer.observe(el);
            });
        }
    }
    
    /**
     * Keyboard Navigation
     */
    function initializeKeyboardNavigation() {
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('#cart-modal').fadeOut(300);
                $('.product-modal').fadeOut(300);
                $('#quick-view-modal').fadeOut(300);
            }
        });
        
        $('a, button, input, textarea, select').on('focus', function() {
            $(this).addClass('focused');
        }).on('blur', function() {
            $(this).removeClass('focused');
        });
    }
    
    /**
     * Analytics
     */
    function initializeAnalytics() {
        trackEvent('page_view', {
            page_title: document.title,
            page_location: window.location.href
        });
        
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
        
        let startTime = Date.now();
        $(window).on('beforeunload', function() {
            const timeOnPage = Math.round((Date.now() - startTime) / 1000);
            trackEvent('time_on_page', { seconds: timeOnPage });
        });
    }
    
    function trackEvent(eventName, parameters = {}) {
        if (localStorage.getItem('cookieConsent') !== 'accepted') {
            return;
        }
        
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, parameters);
        }
        
        if (typeof fbq !== 'undefined') {
            fbq('trackCustom', eventName, parameters);
        }
        
        console.log('Event tracked:', eventName, parameters);
    }
    
    /**
     * Animations
     */
    function initializeAnimations() {
        $('.fade-in').each(function(index) {
            $(this).delay(100 * index).fadeIn(600);
        });
        
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
    
    function hideLoadingOverlay() {
        $('#loading-overlay').fadeOut(500);
    }
    
    /**
     * Notifications
     */
    function showNotification(message, type = 'info', duration = 5000) {
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
        
        setTimeout(function() {
            hideNotification($notification);
        }, duration);
        
        $notification.find('.notification-close').on('click', function() {
            hideNotification($notification);
        });
    }
    
    function hideNotification($notification) {
        $notification.removeClass('show');
        setTimeout(function() {
            $notification.remove();
        }, 300);
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
     * Global API
     */
    window.TetradkataTheme = {
        showNotification: showNotification,
        hideNotification: hideNotification,
        trackEvent: trackEvent,
        updateCartCount: updateCartCount,
        toggleCartModal: toggleCartModal,
        openCartModal: openCartModal,
        closeCartModal: closeCartModal,
        loadCartContents: loadCartContents
    };
    
})(jQuery);