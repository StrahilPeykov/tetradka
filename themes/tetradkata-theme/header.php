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
    <meta name="description" content="<?php echo esc_attr(get_bloginfo('description', 'display')); ?>">
    
    <?php
    // SEO Meta Tags
    $site_name = get_bloginfo('name', 'display');
    $site_description = get_bloginfo('description', 'display');
    $current_url = home_url(add_query_arg(array(), $GLOBALS['wp']->request));
    $og_image = TETRADKATA_THEME_URI . '/assets/images/og-image.jpg';
    ?>
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo esc_attr(wp_get_document_title()); ?>">
    <meta property="og:description" content="<?php echo esc_attr($site_description); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo esc_url($current_url); ?>">
    <meta property="og:image" content="<?php echo esc_url($og_image); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr($site_name); ?>">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr(wp_get_document_title()); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($site_description); ?>">
    <meta name="twitter:image" content="<?php echo esc_url($og_image); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo esc_url(TETRADKATA_THEME_URI . '/assets/images/favicon.ico'); ?>">
    <link rel="apple-touch-icon" href="<?php echo esc_url(TETRADKATA_THEME_URI . '/assets/images/apple-touch-icon.png'); ?>">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main">
    <?php esc_html_e('Към съдържанието', 'tetradkata'); ?>
</a>

<header id="masthead" class="site-header">
    <div class="container">
        <div class="header-content">
            <div class="site-branding">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="logo" rel="home">
                        <?php bloginfo('name'); ?>
                    </a>
                <?php endif; ?>
            </div>

            <nav id="site-navigation" class="main-navigation">
                <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                    <span class="sr-only"><?php esc_html_e('Меню', 'tetradkata'); ?></span>
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
                    'fallback_cb'    => 'tetradkata_primary_menu_fallback',
                ));
                ?>
            </nav>

            <div class="header-cta">
                <?php if (class_exists('WooCommerce')) : ?>
                    <button type="button" class="cart-icon" id="cart-toggle" aria-label="<?php esc_attr_e('Отвори количката', 'tetradkata'); ?>">
                        <span class="dashicons dashicons-cart"></span>
                        <span class="cart-count" id="cart-count"><?php echo esc_html(tetradkata_get_cart_count()); ?></span>
                    </button>
                <?php else : ?>
                    <button type="button" class="cart-icon" id="cart-toggle" aria-label="<?php esc_attr_e('Отвори количката', 'tetradkata'); ?>">
                        <span class="dashicons dashicons-cart"></span>
                        <span class="cart-count" id="cart-count">0</span>
                    </button>
                <?php endif; ?>
                
                <a href="<?php echo esc_url(home_url('/#shop')); ?>" class="btn btn-primary">
                    <?php esc_html_e('Купи сега', 'tetradkata'); ?>
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Loading Overlay -->
<div id="loading-overlay" class="loading-overlay" style="display: none;">
    <div class="loading-spinner">
        <div class="loading"></div>
        <p><?php esc_html_e('Зарежда...', 'tetradkata'); ?></p>
    </div>
</div>

<main id="main" class="site-main">

<?php
/**
 * Fallback menu if no menu is assigned
 */
function tetradkata_primary_menu_fallback() {
    ?>
    <ul class="nav-menu">
        <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Начало', 'tetradkata'); ?></a></li>
        <?php if (class_exists('WooCommerce')) : ?>
            <li><a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"><?php esc_html_e('Магазин', 'tetradkata'); ?></a></li>
        <?php endif; ?>
        <li><a href="<?php echo esc_url(home_url('/#about')); ?>"><?php esc_html_e('За нас', 'tetradkata'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/#contact')); ?>"><?php esc_html_e('Контакти', 'tetradkata'); ?></a></li>
    </ul>
    <?php
}
?>