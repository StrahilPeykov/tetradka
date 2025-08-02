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
                    <a href="<?php echo wc_get_cart_url(); ?>" class="cart-icon" id="cart-toggle">
                        <span class="dashicons dashicons-cart"></span>
                        <span class="cart-count" id="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
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

<main id="main" class="site-main">