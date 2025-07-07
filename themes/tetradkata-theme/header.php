<?php
/**
 * The header for the theme
 *
 * @package TetradkataTheme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Тетрадката - Личният ви дневник за пътешествия, спомени и вдъхновение. Купете уникална тетрадка за събиране на спомени от пътувания.">
    <meta name="keywords" content="тетрадка, пътешествия, дневник, спомени, България, travel journal">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php wp_title('|', true, 'right'); ?>Тетрадката - Колекция от спомени">
    <meta property="og:description" content="Личният ви дневник за пътешествия, спомени и вдъхновение – събрани в една тетрадка.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo home_url(); ?>">
    <meta property="og:image" content="<?php echo get_template_directory_uri(); ?>/assets/images/og-image.jpg">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Тетрадката - Колекция от спомени">
    <meta name="twitter:description" content="Личният ви дневник за пътешествия, спомени и вдъхновение.">
    <meta name="twitter:image" content="<?php echo get_template_directory_uri(); ?>/assets/images/og-image.jpg">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.ico">
    <link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/apple-touch-icon.png">
    
    <?php wp_head(); ?>
    
    <!-- Google Analytics / Meta Pixel - Add your tracking codes here -->
    <!-- 
    <script async src="https://www.googletagmanager.com/gtag/js?id=YOUR_GA_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'YOUR_GA_ID');
    </script>
    -->
    
    <!-- Meta Pixel Code -->
    <!--
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', 'YOUR_PIXEL_ID');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=YOUR_PIXEL_ID&ev=PageView&noscript=1"
    /></noscript>
    -->
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Skip Link for Accessibility -->
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e('Skip to content', 'tetradkata'); ?></a>

<!-- Header -->
<header id="masthead" class="site-header">
    <div class="container">
        <div class="header-content">
            <!-- Logo -->
            <div class="site-branding">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="logo" rel="home">
                        <?php echo get_bloginfo('name', 'display'); ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Navigation Menu (for mobile/admin purposes) -->
            <nav id="site-navigation" class="main-navigation" style="display: none;">
                <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                    <span class="sr-only"><?php esc_html_e('Menu', 'tetradkata'); ?></span>
                    <span class="menu-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
                
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'menu_class'     => 'nav-menu',
                    'fallback_cb'    => false,
                ));
                ?>
            </nav><!-- #site-navigation -->

            <!-- Header CTA -->
            <div class="header-cta">
                <?php if (class_exists('WooCommerce')) : ?>
                    <!-- Cart Icon -->
                    <a href="<?php echo wc_get_cart_url(); ?>" class="cart-icon" id="cart-toggle">
                        <span class="dashicons dashicons-cart"></span>
                        <span class="cart-count" id="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                    </a>
                <?php endif; ?>
                
                <!-- CTA Button -->
                <a href="#shop" class="btn btn-primary">Купи сега</a>
            </div>
        </div>
    </div>
</header><!-- #masthead -->

<!-- Scroll to Top Button -->
<button id="scroll-to-top" class="scroll-to-top" style="display: none;" aria-label="Scroll to top">
    <span class="dashicons dashicons-arrow-up-alt"></span>
</button>

<!-- Loading Overlay -->
<div id="loading-overlay" class="loading-overlay" style="display: none;">
    <div class="loading-spinner">
        <div class="loading"></div>
        <p>Зарежда...</p>
    </div>
</div>

<main id="main" class="site-main"><?php
/**
 * Additional CSS for header elements
 */
?>
<style>
/* Additional header styles */
.skip-link {
    position: absolute;
    left: -9999px;
    top: auto;
    width: 1px;
    height: 1px;
    overflow: hidden;
}

.skip-link:focus {
    position: fixed;
    top: 0;
    left: 6px;
    z-index: 999999;
    width: auto;
    height: auto;
    padding: 8px 16px;
    background: var(--charcoal);
    color: var(--white);
    text-decoration: none;
    border-radius: 0 0 5px 5px;
}

.menu-toggle {
    display: none;
    background: none;
    border: none;
    padding: 10px;
    cursor: pointer;
}

.menu-icon {
    display: block;
    width: 25px;
    height: 18px;
    position: relative;
}

.menu-icon span {
    display: block;
    width: 100%;
    height: 3px;
    background: var(--charcoal);
    margin: 3px 0;
    transition: 0.3s;
}

.scroll-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
    color: var(--white);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    z-index: 1000;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.scroll-to-top:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.9);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.loading-spinner {
    text-align: center;
}

.loading-spinner p {
    margin-top: 20px;
    font-weight: 600;
    color: var(--charcoal);
}

/* Responsive header */
@media (max-width: 768px) {
    .header-content {
        padding: 10px 0;
    }
    
    .logo {
        font-size: 2rem;
    }
    
    .header-cta {
        gap: 10px;
    }
    
    .btn {
        padding: 10px 20px;
        font-size: 14px;
    }
    
    .scroll-to-top {
        width: 45px;
        height: 45px;
        bottom: 20px;
        right: 20px;
    }
}

/* Smooth header scroll effect */
.header-scrolled {
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 20px rgba(0,0,0,0.15);
}

.header-scrolled .header-content {
    padding: 10px 0;
}

.header-scrolled .logo {
    font-size: 2rem;
}

/* Cart count animation */
.cart-count {
    animation: cartBounce 0.3s ease-in-out;
}

@keyframes cartBounce {
    0% { transform: scale(1); }
    50% { transform: scale(1.3); }
    100% { transform: scale(1); }
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
    
    html {
        scroll-behavior: auto;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .btn-primary {
        background: #000;
        color: #fff;
        border: 2px solid #fff;
    }
    
    .logo {
        color: #000;
    }
}
</style>