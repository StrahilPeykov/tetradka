<?php
/**
 * WooCommerce Shop Page Template
 *
 * @package TetradkataTheme
 */

get_header(); ?>

<div class="woocommerce-container">
    <div class="container">
        <!-- Shop Header -->
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

        <!-- Shop Filters and Sorting -->
        <div class="shop-controls">
            <div class="shop-filters">
                <?php
                // Product categories filter
                $product_categories = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'hide_empty' => true,
                    'exclude' => array(15), // Exclude uncategorized
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
                
                <!-- Price filter -->
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

        <!-- Products Grid -->
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
                                
                                <!-- Quick view button -->
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
                                    <?php if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) : ?>
                                        <button class="btn btn-primary add-to-cart-btn" 
                                                data-product-id="<?php echo get_the_ID(); ?>"
                                                data-product-name="<?php echo esc_attr(get_the_title()); ?>"
                                                data-product-price="<?php echo esc_attr($product->get_price()); ?>">
                                            <span class="btn-text">Добави в количката</span>
                                            <span class="btn-loading" style="display: none;">
                                                <span class="loading"></span> Добавя...
                                            </span>
                                        </button>
                                    <?php else : ?>
                                        <a href="<?php the_permalink(); ?>" class="btn btn-secondary">
                                            Виж детайли
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                    <?php endwhile; ?>
                </div>
                
                <?php woocommerce_product_loop_end(); ?>
                
                <!-- Pagination -->
                <div class="shop-pagination">
                    <?php woocommerce_pagination(); ?>
                </div>
                
            <?php else : ?>
                
                <!-- No products found -->
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

<!-- Cart Modal (same as main page) -->
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

<!-- Quick View Modal -->
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
/* Shop page styles */
.woocommerce-container {
    padding: 120px 0 80px;
    background: var(--paper-bg);
    min-height: 100vh;
}

.shop-header {
    text-align: center;
    margin-bottom: 60px;
}

.shop-title {
    font-size: clamp(2.5rem, 5vw, 4rem);
    margin-bottom: 20px;
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.shop-description {
    font-size: 1.2rem;
    color: var(--charcoal);
    max-width: 600px;
    margin: 0 auto;
}

.shop-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    padding: 20px;
    background: var(--white);
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.shop-filters {
    display: flex;
    gap: 20px;
    align-items: center;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-group label {
    font-weight: 600;
    color: var(--charcoal);
}

.filter-group select {
    padding: 8px 15px;
    border: 2px solid var(--warm-beige);
    border-radius: 25px;
    background: var(--white);
    color: var(--charcoal);
    font-size: 14px;
}

.filter-group select:focus {
    outline: none;
    border-color: var(--gold-start);
}

.products-grid.woocommerce-products {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 60px;
}

.product-card.woocommerce-product {
    background: var(--white);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
}

.product-card.woocommerce-product:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.product-image {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.product-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-thumbnail {
    transform: scale(1.05);
}

.product-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: var(--white);
    z-index: 2;
}

.product-badge.featured {
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
}

.product-badge.sale {
    background: #ef4444;
    top: 15px;
    right: auto;
    left: 15px;
}

.quick-view-btn {
    position: absolute;
    bottom: 15px;
    left: 50%;
    transform: translateX(-50%) translateY(100%);
    background: rgba(0,0,0,0.8);
    color: var(--white);
    border: none;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    opacity: 0;
}

.product-card:hover .quick-view-btn {
    transform: translateX(-50%) translateY(0);
    opacity: 1;
}

.quick-view-btn:hover {
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
}

.product-info {
    padding: 25px;
}

.product-title a {
    color: var(--charcoal);
    text-decoration: none;
    font-size: 1.3rem;
    font-weight: 600;
    line-height: 1.3;
}

.product-title a:hover {
    color: var(--gold-start);
}

.product-rating {
    margin: 10px 0;
}

.product-price {
    font-size: 1.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 15px 0;
}

.product-description {
    font-size: 14px;
    line-height: 1.5;
    color: var(--charcoal);
    margin-bottom: 20px;
    opacity: 0.8;
}

.product-actions {
    display: flex;
    gap: 10px;
}

.no-products-found {
    text-align: center;
    padding: 80px 20px;
}

.no-products-content h2 {
    margin-bottom: 20px;
    color: var(--charcoal);
}

.no-products-content p {
    margin-bottom: 30px;
    font-size: 1.1rem;
    opacity: 0.8;
}

.shop-pagination {
    text-align: center;
    margin-top: 60px;
}

.shop-pagination .page-numbers {
    display: inline-block;
    padding: 10px 15px;
    margin: 0 5px;
    background: var(--white);
    border: 2px solid var(--warm-beige);
    border-radius: 10px;
    color: var(--charcoal);
    text-decoration: none;
    transition: all 0.3s ease;
}

.shop-pagination .page-numbers:hover,
.shop-pagination .page-numbers.current {
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
    border-color: var(--gold-start);
    color: var(--white);
}

/* Quick View Modal */
.quick-view-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10000;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: var(--white);
    border-radius: 20px;
    max-width: 800px;
    width: 90%;
    max-height: 90%;
    overflow-y: auto;
}

.modal-close {
    position: absolute;
    top: 20px;
    right: 20px;
    background: none;
    border: none;
    font-size: 2rem;
    cursor: pointer;
    color: var(--charcoal);
    z-index: 10001;
}

.quick-view-content {
    padding: 40px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .shop-controls {
        flex-direction: column;
        gap: 20px;
        align-items: stretch;
    }
    
    .shop-filters {
        flex-direction: column;
        gap: 15px;
    }
    
    .filter-group {
        justify-content: space-between;
    }
    
    .products-grid.woocommerce-products {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .product-info {
        padding: 20px;
    }
    
    .modal-content {
        width: 95%;
        margin: 20px;
    }
    
    .quick-view-content {
        padding: 20px;
    }
}

/* WooCommerce specific overrides */
.woocommerce .star-rating {
    color: var(--gold-start);
}

.woocommerce .star-rating::before {
    color: var(--warm-beige);
}

.woocommerce-ordering select {
    padding: 8px 15px;
    border: 2px solid var(--warm-beige);
    border-radius: 25px;
    background: var(--white);
    color: var(--charcoal);
}

/* Loading states */
.btn-loading {
    display: flex;
    align-items: center;
    gap: 8px;
}

.add-to-cart-btn.loading .btn-text {
    display: none;
}

.add-to-cart-btn.loading .btn-loading {
    display: flex;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Quick view button click
    $('.quick-view-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const productId = $(this).data('product-id');
        loadQuickView(productId);
    });
    
    // Close modal events
    $(document).on('click', '.modal-close, .modal-overlay', function() {
        $('#quick-view-modal').fadeOut(300);
    });
    
    // Close modal with ESC key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('#quick-view-modal').fadeOut(300);
            $('#cart-modal').fadeOut(300);
        }
    });
    
    // Filter functionality
    $('.category-filter, .price-filter').on('change', function() {
        // Basic filter functionality - can be enhanced
        const category = $('.category-filter').val();
        const price = $('.price-filter').val();
        
        if (category || price) {
            console.log('Filtering by:', { category, price });
            // Here you would implement AJAX filtering
            // For now, just redirect to category page if category is selected
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
    
    // AJAX call to load product details
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