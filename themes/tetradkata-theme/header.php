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
    
    <meta property="og:title" content="<?php wp_title('|', true, 'right'); ?>Тетрадката - Колекция от спомени">
    <meta property="og:description" content="Личният ви дневник за пътешествия, спомени и вдъхновение – събрани в една тетрадка.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo home_url(); ?>">
    <meta property="og:image" content="<?php echo get_template_directory_uri(); ?>/assets/images/og-image.jpg">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Тетрадката - Колекция от спомени">
    <meta name="twitter:description" content="Личният ви дневник за пътешествия, спомени и вдъхновение.">
    <meta name="twitter:image" content="<?php echo get_template_directory_uri(); ?>/assets/images/og-image.jpg">
    
    <link rel="icon" type="image/x-icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.ico">
    <link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/apple-touch-icon.png">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e('Skip to content', 'tetradkata'); ?></a>

<header id="masthead" class="site-header">
    <div class="container">
        <div class="header-content">
            <div class="site-branding">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="logo" rel="home">
                        <?php echo get_bloginfo('name', 'display'); ?>
                    </a>
                <?php endif; ?>
            </div>

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
            </nav>

            <div class="header-cta">
                <?php if (class_exists('WooCommerce')) : ?>
                    <button type="button" class="cart-icon" id="cart-toggle" aria-label="Отвори количката">
                        <span class="dashicons dashicons-cart"></span>
                        <span class="cart-count" id="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                    </button>
                <?php else : ?>
                    <a href="#" class="cart-icon" id="cart-toggle" aria-label="Отвори количката">
                        <span class="dashicons dashicons-cart"></span>
                        <span class="cart-count" id="cart-count">0</span>
                    </a>
                <?php endif; ?>
                
                <a href="#shop" class="btn btn-primary">Купи сега</a>
            </div>
        </div>
    </div>
</header>

<button id="scroll-to-top" class="scroll-to-top" style="display: none;" aria-label="Scroll to top">
    <span class="dashicons dashicons-arrow-up-alt"></span>
</button>

<div id="loading-overlay" class="loading-overlay" style="display: none;">
    <div class="loading-spinner">
        <div class="loading"></div>
        <p>Зарежда...</p>
    </div>
</div>

<style>
/* Header specific styles */
.site-header {
    transition: all 0.3s ease;
}

.site-header.header-scrolled {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 20px rgba(0,0,0,0.15);
}

.site-header.header-hidden {
    transform: translateY(-100%);
}

.cart-icon {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.cart-icon:focus {
    outline: 2px solid var(--gold-start);
    outline-offset: 2px;
    border-radius: 50%;
}

.scroll-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
    color: var(--white);
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(182, 129, 58, 0.3);
    transition: all 0.3s ease;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.scroll-to-top:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(182, 129, 58, 0.4);
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(248, 246, 244, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(5px);
}

.loading-spinner {
    text-align: center;
    color: var(--charcoal);
}

.loading-spinner .loading {
    display: inline-block;
    width: 40px;
    height: 40px;
    border: 4px solid var(--warm-beige);
    border-radius: 50%;
    border-top-color: var(--gold-start);
    animation: spin 1s ease-in-out infinite;
    margin-bottom: 15px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Ensure proper header spacing */
@media (max-width: 768px) {
    .scroll-to-top {
        bottom: 20px;
        right: 20px;
        width: 45px;
        height: 45px;
    }
}
</style>

<main id="main" class="site-main">