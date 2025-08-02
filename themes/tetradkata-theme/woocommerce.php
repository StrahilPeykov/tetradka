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

<!-- Cart Modal - moved outside container -->
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

<div id="quick-view-modal" class="quick-view-modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <button class="modal-close">&times;</button>
        <div class="quick-view-content">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<style>
/* Shop-specific styles */
.woocommerce-container {
    background: var(--paper-bg);
    padding: 120px 0 60px;
    min-height: calc(100vh - 200px);
}

.shop-header {
    text-align: center;
    margin-bottom: 60px;
    padding: 40px 0;
    background: var(--white);
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.shop-title {
    color: var(--charcoal);
    margin-bottom: 20px;
    font-family: var(--font-heading);
}

.shop-description {
    font-size: 1.1rem;
    color: var(--charcoal);
    opacity: 0.8;
    max-width: 600px;
    margin: 0 auto;
}

.shop-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    padding: 20px 30px;
    background: var(--white);
    border-radius: 15px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    flex-wrap: wrap;
    gap: 20px;
}

.shop-filters {
    display: flex;
    gap: 20px;
    align-items: center;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-group label {
    font-weight: 600;
    color: var(--charcoal);
    white-space: nowrap;
}

.filter-group select {
    padding: 8px 15px;
    border: 2px solid var(--warm-beige);
    border-radius: 25px;
    background: var(--white);
    color: var(--charcoal);
    font-size: 14px;
    min-width: 160px;
    transition: border-color 0.3s ease;
}

.filter-group select:focus {
    outline: none;
    border-color: var(--gold-start);
}

.shop-sorting {
    display: flex;
    align-items: center;
}

.woocommerce-ordering select {
    padding: 8px 15px;
    border: 2px solid var(--warm-beige);
    border-radius: 25px;
    background: var(--white);
    color: var(--charcoal);
    font-size: 14px;
    min-width: 160px;
}

.products-grid.woocommerce-products {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.quick-view-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(182, 129, 58, 0.9);
    color: var(--white);
    border: none;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    opacity: 0;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.product-card:hover .quick-view-btn {
    opacity: 1;
}

.quick-view-btn:hover {
    background: var(--gold-start);
    transform: translate(-50%, -50%) scale(1.05);
}

.no-products-found {
    text-align: center;
    padding: 80px 20px;
    background: var(--white);
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.no-products-content h2 {
    color: var(--charcoal);
    margin-bottom: 20px;
}

.no-products-content p {
    color: var(--charcoal);
    opacity: 0.8;
    margin-bottom: 30px;
    font-size: 1.1rem;
}

.shop-pagination {
    text-align: center;
    margin-top: 40px;
}

.shop-pagination .page-numbers {
    display: inline-block;
    padding: 10px 15px;
    margin: 0 5px;
    background: var(--white);
    border: 2px solid var(--warm-beige);
    border-radius: 50px;
    color: var(--charcoal);
    text-decoration: none;
    transition: all 0.3s ease;
}

.shop-pagination .page-numbers:hover,
.shop-pagination .page-numbers.current {
    background: var(--gold-start);
    color: var(--white);
    border-color: var(--gold-start);
}

/* Quick View Modal */
.quick-view-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10001;
    display: none;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    backdrop-filter: blur(5px);
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: var(--white);
    border-radius: 20px;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    font-size: 2rem;
    cursor: pointer;
    color: var(--charcoal);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    z-index: 10;
}

.modal-close:hover {
    background: var(--warm-beige);
    color: var(--gold-start);
}

.quick-view-content {
    padding: 30px;
}

.quick-view-product {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    align-items: start;
}

.quick-view-images img {
    width: 100%;
    height: auto;
    border-radius: 10px;
}

.quick-view-details h2 {
    margin-bottom: 15px;
}

.quick-view-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gold-start);
    margin-bottom: 15px;
}

.quick-view-description {
    margin-bottom: 25px;
    line-height: 1.6;
}

.quick-view-actions {
    display: flex;
    gap: 15px;
}

/* Responsive */
@media (max-width: 768px) {
    .woocommerce-container {
        padding: 100px 0 40px;
    }
    
    .shop-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .shop-filters {
        flex-direction: column;
        align-items: stretch;
        gap: 15px;
    }
    
    .filter-group {
        flex-direction: column;
        align-items: stretch;
        gap: 5px;
    }
    
    .filter-group select {
        min-width: auto;
        width: 100%;
    }
    
    .products-grid.woocommerce-products {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .quick-view-product {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .modal-content {
        width: 95%;
        margin: 20px;
    }
    
    .quick-view-content {
        padding: 20px;
    }
    
    .quick-view-actions {
        flex-direction: column;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Quick view functionality
    $('.quick-view-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const productId = $(this).data('product-id');
        loadQuickView(productId);
    });
    
    $(document).on('click', '.modal-close, .modal-overlay', function() {
        $('#quick-view-modal').fadeOut(300);
        $('body').removeClass('modal-open');
    });
    
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('#quick-view-modal').fadeOut(300);
            $('#cart-modal').removeClass('show').fadeOut(300);
            $('body').removeClass('modal-open');
        }
    });
    
    // Filter functionality
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
    
    // Improved cart modal handling for shop page
    $('.add-to-cart-btn').on('click', function() {
        // Show notification after successful add to cart
        setTimeout(function() {
            if (window.TetradkataTheme && window.TetradkataTheme.updateCartCount) {
                // Update the cart count in the header
                const currentCount = parseInt($('.cart-count').text()) || 0;
                window.TetradkataTheme.updateCartCount(currentCount + 1);
            }
        }, 500);
    });
});

function loadQuickView(productId) {
    const $modal = $('#quick-view-modal');
    const $content = $('.quick-view-content');
    
    $content.html('<div class="loading-spinner" style="text-align: center; padding: 40px;"><div class="loading"></div><p>Зарежда...</p></div>');
    $modal.fadeIn(300);
    $('body').addClass('modal-open');
    
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