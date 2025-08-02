<?php
/**
 * WooCommerce Shop Page Template
 *
 * @package TetradkataTheme
 */

get_header(); ?>

<div class="woocommerce-container">
    <div class="container">
        <div class="shop-header">
            <h1 class="shop-title">
                <?php if (is_shop()) : ?>
                    Нашите продукти
                <?php elseif (is_product_category()) : ?>
                    <?php single_cat_title(); ?>
                <?php elseif (is_product_tag()) : ?>
                    <?php single_tag_title(); ?>
                <?php else : ?>
                    <?php the_archive_title(); ?>
                <?php endif; ?>
            </h1>
            
            <?php if (is_shop()) : ?>
                <p class="shop-description">Открийте нашата колекция от уникални продукти за пътешественици</p>
            <?php else : ?>
                <?php the_archive_description('<div class="shop-description">', '</div>'); ?>
            <?php endif; ?>
        </div>

        <div class="shop-controls">
            <div class="shop-filters">
                <?php
                $product_categories = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'hide_empty' => true,
                    'exclude' => array(15),
                ));
                
                if (!empty($product_categories)) :
                ?>
                    <div class="filter-group">
                        <label for="category-filter">Категория:</label>
                        <select id="category-filter" class="category-filter">
                            <option value="">Всички категории</option>
                            <?php foreach ($product_categories as $category) : ?>
                                <option value="<?php echo esc_attr($category->slug); ?>" 
                                        <?php selected(is_product_category($category->slug)); ?>>
                                    <?php echo esc_html($category->name); ?> (<?php echo $category->count; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                
                <div class="filter-group">
                    <label for="price-filter">Цена:</label>
                    <select id="price-filter" class="price-filter">
                        <option value="">Всички цени</option>
                        <option value="0-20">До 20 лв.</option>
                        <option value="20-40">20 - 40 лв.</option>
                        <option value="40-60">40 - 60 лв.</option>
                        <option value="60+">Над 60 лв.</option>
                    </select>
                </div>
            </div>
            
            <div class="shop-sorting">
                <?php woocommerce_catalog_ordering(); ?>
            </div>
        </div>

        <div class="shop-content">
            <?php if (woocommerce_product_loop()) : ?>
                
                <?php woocommerce_product_loop_start(); ?>
                
                <?php if (wc_get_loop_prop('is_shortcode')) : ?>
                    <?php woocommerce_maybe_show_product_subcategories(); ?>
                <?php endif; ?>
                
                <div class="products-grid woocommerce-products">
                    <?php while (have_posts()) : ?>
                        <?php the_post(); ?>
                        <?php
                        global $product;
                        $is_featured = get_post_meta(get_the_ID(), '_tetradkata_featured', true);
                        $product_image = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                        if (!$product_image) {
                            $product_image = get_template_directory_uri() . '/assets/images/product-placeholder.jpg';
                        }
                        ?>
                        
                        <div class="product-card woocommerce-product">
                            <div class="product-image">
                                <a href="<?php the_permalink(); ?>">
                                    <img src="<?php echo esc_url($product_image); ?>" 
                                         alt="<?php echo esc_attr(get_the_title()); ?>" 
                                         class="product-thumbnail">
                                </a>
                                
                                <?php if ($is_featured) : ?>
                                    <div class="product-badge featured">Хит продажби</div>
                                <?php endif; ?>
                                
                                <?php if ($product->is_on_sale()) : ?>
                                    <div class="product-badge sale">Намаление</div>
                                <?php endif; ?>
                                
                                <button class="quick-view-btn" data-product-id="<?php echo get_the_ID(); ?>">
                                    Бърз преглед
                                </button>
                            </div>
                            
                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <div class="product-rating">
                                    <?php woocommerce_template_loop_rating(); ?>
                                </div>
                                
                                <div class="product-price">
                                    <?php echo $product->get_price_html(); ?>
                                </div>
                                
                                <div class="product-description">
                                    <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                                </div>
                                
                                <div class="product-actions">
                                    <?php tetradkata_add_to_cart_button($product); ?>
                                </div>
                            </div>
                        </div>
                        
                    <?php endwhile; ?>
                </div>
                
                <?php woocommerce_product_loop_end(); ?>
                
                <div class="shop-pagination">
                    <?php woocommerce_pagination(); ?>
                </div>
                
            <?php else : ?>
                
                <div class="no-products-found">
                    <div class="no-products-content">
                        <h2>Няма намерени продукти</h2>
                        <p>За съжаление не намерихме продукти, които отговарят на вашите критерии.</p>
                        <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="btn btn-primary">
                            Разгледайте всички продукти
                        </a>
                    </div>
                </div>
                
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="cart-modal" class="cart-modal" style="display: none;">
    <div class="cart-modal-content">
        <div class="cart-header">
            <h3>Количка</h3>
            <button class="close-cart">&times;</button>
        </div>
        <div class="cart-items">
            <!-- Cart items will be loaded here -->
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <strong>Общо: <span id="cart-total-amount">0.00 лв.</span></strong>
            </div>
            <a href="<?php echo wc_get_checkout_url(); ?>" class="btn btn-primary">Към плащане</a>
        </div>
    </div>
</div>

<div id="quick-view-modal" class="quick-view-modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <button class="modal-close">&times;</button>
        <div class="quick-view-content">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.quick-view-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const productId = $(this).data('product-id');
        loadQuickView(productId);
    });
    
    $(document).on('click', '.modal-close, .modal-overlay', function() {
        $('#quick-view-modal').fadeOut(300);
    });
    
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('#quick-view-modal').fadeOut(300);
            $('#cart-modal').fadeOut(300);
        }
    });
    
    $('.category-filter, .price-filter').on('change', function() {
        const category = $('.category-filter').val();
        const price = $('.price-filter').val();
        
        if (category || price) {
            console.log('Filtering by:', { category, price });
            if (category) {
                window.location.href = '/product-category/' + category;
            }
        }
    });
});

function loadQuickView(productId) {
    const $modal = $('#quick-view-modal');
    const $content = $('.quick-view-content');
    
    $content.html('<div class="loading-spinner" style="text-align: center; padding: 40px;"><div class="loading"></div><p>Зарежда...</p></div>');
    $modal.fadeIn(300);
    
    jQuery.ajax({
        url: tetradkata_ajax.ajax_url,
        type: 'POST',
        data: {
            action: 'tetradkata_quick_view',
            product_id: productId,
            nonce: tetradkata_ajax.nonce
        },
        success: function(response) {
            if (response.success) {
                $content.html(response.data.html);
            } else {
                $content.html('<div style="text-align: center; padding: 40px;"><p>Възникна грешка при зареждане на продукта.</p></div>');
            }
        },
        error: function() {
            $content.html('<div style="text-align: center; padding: 40px;"><p>Възникна грешка при зареждане на продукта.</p></div>');
        }
    });
}
</script>

<?php get_footer(); ?>