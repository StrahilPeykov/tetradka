<?php
/**
 * Single Product Template
 *
 * @package TetradkataTheme
 */

get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
    <?php
    global $product;
    $product_id = get_the_ID();
    $is_featured = get_post_meta($product_id, '_tetradkata_featured', true);
    $gallery_images = $product->get_gallery_image_ids();
    ?>

    <div class="single-product-container">
        <div class="container">
            <!-- Breadcrumbs -->
            <nav class="breadcrumbs">
                <a href="<?php echo home_url(); ?>">Начало</a>
                <span class="separator">></span>
                <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>">Магазин</a>
                <span class="separator">></span>
                <span class="current"><?php the_title(); ?></span>
            </nav>

            <div class="product-layout">
                <!-- Product Gallery -->
                <div class="product-gallery">
                    <div class="main-image">
                        <?php if (has_post_thumbnail()) : ?>
                            <img id="main-product-image" 
                                 src="<?php echo get_the_post_thumbnail_url($product_id, 'large'); ?>" 
                                 alt="<?php the_title(); ?>"
                                 class="product-main-image">
                        <?php else : ?>
                            <img id="main-product-image" 
                                 src="<?php echo get_template_directory_uri(); ?>/assets/images/product-placeholder.jpg" 
                                 alt="<?php the_title(); ?>"
                                 class="product-main-image">
                        <?php endif; ?>
                        
                        <?php if ($product->is_on_sale()) : ?>
                            <div class="sale-badge">
                                <?php
                                if ($product->get_type() === 'simple') {
                                    $percentage = round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100);
                                    echo '-' . $percentage . '%';
                                } else {
                                    echo 'Намаление';
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($is_featured) : ?>
                            <div class="featured-badge">Хит продажби</div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($gallery_images) || has_post_thumbnail()) : ?>
                        <div class="gallery-thumbnails">
                            <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php echo get_the_post_thumbnail_url($product_id, 'thumbnail'); ?>" 
                                     alt="<?php the_title(); ?>"
                                     class="thumbnail-image active"
                                     data-large="<?php echo get_the_post_thumbnail_url($product_id, 'large'); ?>">
                            <?php endif; ?>
                            
                            <?php foreach ($gallery_images as $image_id) : ?>
                                <img src="<?php echo wp_get_attachment_image_url($image_id, 'thumbnail'); ?>" 
                                     alt="<?php echo get_post_meta($image_id, '_wp_attachment_image_alt', true); ?>"
                                     class="thumbnail-image"
                                     data-large="<?php echo wp_get_attachment_image_url($image_id, 'large'); ?>">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Product Info -->
                <div class="product-info">
                    <h1 class="product-title"><?php the_title(); ?></h1>
                    
                    <!-- Product Rating -->
                    <div class="product-rating">
                        <?php woocommerce_template_single_rating(); ?>
                    </div>
                    
                    <!-- Product Price -->
                    <div class="product-price">
                        <?php echo $product->get_price_html(); ?>
                    </div>
                    
                    <!-- Short Description -->
                    <div class="product-short-description">
                        <?php echo apply_filters('woocommerce_short_description', $post->post_excerpt); ?>
                    </div>
                    
                    <!-- Product Form -->
                    <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
                        
                        <!-- Quantity and Add to Cart -->
                        <div class="product-actions">
                            <?php if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) : ?>
                                
                                <div class="quantity-wrapper">
                                    <label for="quantity_<?php echo esc_attr($product_id); ?>">Количество:</label>
                                    <div class="quantity-controls">
                                        <button type="button" class="quantity-minus">-</button>
                                        <input type="number" 
                                               id="quantity_<?php echo esc_attr($product_id); ?>" 
                                               class="quantity-input" 
                                               name="quantity" 
                                               value="1" 
                                               min="1" 
                                               max="<?php echo esc_attr($product->get_max_purchase_quantity()); ?>"
                                               step="1">
                                        <button type="button" class="quantity-plus">+</button>
                                    </div>
                                </div>
                                
                                <button type="submit" 
                                        name="add-to-cart" 
                                        value="<?php echo esc_attr($product->get_id()); ?>" 
                                        class="btn btn-primary add-to-cart-single">
                                    <span class="btn-text">Добави в количката</span>
                                    <span class="btn-loading" style="display: none;">
                                        <span class="loading"></span> Добавя...
                                    </span>
                                </button>
                                
                            <?php elseif (!$product->is_in_stock()) : ?>
                                <div class="out-of-stock">
                                    <button class="btn btn-secondary" disabled>Няма в наличност</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Additional Product Info -->
                        <div class="product-meta">
                            <?php if ($product->get_sku()) : ?>
                                <div class="meta-item">
                                    <strong>Артикул:</strong> <?php echo $product->get_sku(); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php
                            $categories = get_the_terms($product_id, 'product_cat');
                            if ($categories && !is_wp_error($categories)) :
                            ?>
                                <div class="meta-item">
                                    <strong>Категория:</strong>
                                    <?php
                                    $category_names = array();
                                    foreach ($categories as $category) {
                                        if ($category->slug !== 'uncategorized') {
                                            $category_names[] = '<a href="' . get_term_link($category) . '">' . $category->name . '</a>';
                                        }
                                    }
                                    echo implode(', ', $category_names);
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php
                            $tags = get_the_terms($product_id, 'product_tag');
                            if ($tags && !is_wp_error($tags)) :
                            ?>
                                <div class="meta-item">
                                    <strong>Тагове:</strong>
                                    <?php
                                    $tag_names = array();
                                    foreach ($tags as $tag) {
                                        $tag_names[] = '<a href="' . get_term_link($tag) . '">' . $tag->name . '</a>';
                                    }
                                    echo implode(', ', $tag_names);
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                    
                    <!-- Trust Badges -->
                    <div class="trust-badges">
                        <div class="trust-item">
                            <span class="dashicons dashicons-yes-alt"></span>
                            <span>Безплатна доставка над 50 лв.</span>
                        </div>
                        <div class="trust-item">
                            <span class="dashicons dashicons-update"></span>
                            <span>14 дни за връщане</span>
                        </div>
                        <div class="trust-item">
                            <span class="dashicons dashicons-awards"></span>
                            <span>Качествена гаранция</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product Tabs -->
            <div class="product-tabs">
                <nav class="tabs-nav">
                    <button class="tab-button active" data-tab="description">Описание</button>
                    <button class="tab-button" data-tab="additional">Допълнителна информация</button>
                    <button class="tab-button" data-tab="reviews">Отзиви</button>
                    <button class="tab-button" data-tab="shipping">Доставка</button>
                </nav>
                
                <div class="tabs-content">
                    <!-- Description Tab -->
                    <div id="description" class="tab-content active">
                        <h3>Описание на продукта</h3>
                        <?php the_content(); ?>
                    </div>
                    
                    <!-- Additional Information Tab -->
                    <div id="additional" class="tab-content">
                        <h3>Допълнителна информация</h3>
                        <?php if ($product->has_attributes() || apply_filters('wc_product_enable_dimensions_display', $product->has_weight() || $product->has_dimensions())) : ?>
                            <table class="additional-info-table">
                                <?php if ($product->has_weight()) : ?>
                                    <tr>
                                        <td><strong>Тегло</strong></td>
                                        <td><?php echo esc_html($product->get_weight()); ?> кг</td>
                                    </tr>
                                <?php endif; ?>
                                
                                <?php if ($product->has_dimensions()) : ?>
                                    <tr>
                                        <td><strong>Размери</strong></td>
                                        <td><?php echo esc_html($product->get_dimensions(false)); ?></td>
                                    </tr>
                                <?php endif; ?>
                                
                                <?php foreach ($product->get_attributes() as $attribute) : ?>
                                    <tr>
                                        <td><strong><?php echo wc_attribute_label($attribute->get_name()); ?></strong></td>
                                        <td><?php echo $product->get_attribute($attribute->get_name()); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php else : ?>
                            <p>Няма допълнителна информация за този продукт.</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Reviews Tab -->
                    <div id="reviews" class="tab-content">
                        <?php comments_template(); ?>
                    </div>
                    
                    <!-- Shipping Tab -->
                    <div id="shipping" class="tab-content">
                        <h3>Информация за доставка</h3>
                        <div class="shipping-info">
                            <div class="shipping-option">
                                <h4>Speedy доставка</h4>
                                <ul>
                                    <li>До дома/офиса: 5.99 лв. (1-2 работни дни)</li>
                                    <li>До Speedy офис: 4.99 лв. (1-2 работни дни)</li>
                                    <li>До Speedy автомат: 3.99 лв. (1-2 работни дни)</li>
                                </ul>
                            </div>
                            <div class="shipping-option">
                                <h4>Econt доставка</h4>
                                <ul>
                                    <li>До дома/офиса: 5.99 лв. (1-3 работни дни)</li>
                                    <li>До Econt офис: 4.99 лв. (1-3 работни дни)</li>
                                    <li>До Econt бокс: 3.99 лв. (1-3 работни дни)</li>
                                </ul>
                            </div>
                            <div class="shipping-note">
                                <p><strong>Безплатна доставка при поръчки над 50 лв.!</strong></p>
                                <p>Наложен платеж: +2.99 лв. (само за адреси в България)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Related Products -->
            <?php
            $related_products = wc_get_related_products($product_id, 4);
            if (!empty($related_products)) :
            ?>
                <div class="related-products">
                    <h3>Свързани продукти</h3>
                    <div class="related-products-grid">
                        <?php foreach ($related_products as $related_id) : ?>
                            <?php
                            $related_product = wc_get_product($related_id);
                            if (!$related_product) continue;
                            ?>
                            <div class="related-product">
                                <a href="<?php echo get_permalink($related_id); ?>">
                                    <?php echo get_the_post_thumbnail($related_id, 'medium', array('class' => 'related-image')); ?>
                                    <h4><?php echo get_the_title($related_id); ?></h4>
                                    <div class="related-price"><?php echo $related_product->get_price_html(); ?></div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php endwhile; ?>

<style>
/* Single Product Styles */
.single-product-container {
    padding: 120px 0 80px;
    background: var(--paper-bg);
    min-height: 100vh;
}

.breadcrumbs {
    margin-bottom: 40px;
    font-size: 14px;
}

.breadcrumbs a {
    color: var(--charcoal);
    text-decoration: none;
}

.breadcrumbs a:hover {
    color: var(--gold-start);
}

.breadcrumbs .separator {
    margin: 0 10px;
    color: var(--primary-taupe);
}

.breadcrumbs .current {
    color: var(--gold-start);
    font-weight: 600;
}

.product-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    margin-bottom: 80px;
}

.product-gallery {
    position: sticky;
    top: 120px;
    height: fit-content;
}

.main-image {
    position: relative;
    margin-bottom: 20px;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.product-main-image {
    width: 100%;
    height: auto;
    max-height: 500px;
    object-fit: cover;
}

.sale-badge,
.featured-badge {
    position: absolute;
    top: 20px;
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    color: var(--white);
    z-index: 2;
}

.sale-badge {
    right: 20px;
    background: #ef4444;
}

.featured-badge {
    left: 20px;
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
}

.gallery-thumbnails {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.thumbnail-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 10px;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.3s ease;
}

.thumbnail-image:hover,
.thumbnail-image.active {
    border-color: var(--gold-start);
    transform: scale(1.05);
}

.product-info {
    padding-left: 20px;
}

.product-title {
    font-size: clamp(2rem, 4vw, 3rem);
    margin-bottom: 20px;
    color: var(--charcoal);
}

.product-rating {
    margin-bottom: 20px;
}

.product-price {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 30px;
}

.product-short-description {
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 40px;
    color: var(--charcoal);
}

.product-actions {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 40px;
    flex-wrap: wrap;
}

.quantity-wrapper {
    display: flex;
    align-items: center;
    gap: 15px;
}

.quantity-wrapper label {
    font-weight: 600;
    color: var(--charcoal);
}

.quantity-controls {
    display: flex;
    align-items: center;
    border: 2px solid var(--warm-beige);
    border-radius: 50px;
    overflow: hidden;
    background: var(--white);
}

.quantity-minus,
.quantity-plus {
    background: none;
    border: none;
    width: 40px;
    height: 40px;
    cursor: pointer;
    font-size: 18px;
    font-weight: 600;
    color: var(--charcoal);
    transition: background-color 0.3s ease;
}

.quantity-minus:hover,
.quantity-plus:hover {
    background: var(--warm-beige);
}

.quantity-input {
    width: 60px;
    height: 40px;
    border: none;
    text-align: center;
    font-size: 16px;
    font-weight: 600;
    color: var(--charcoal);
}

.quantity-input:focus {
    outline: none;
}

.add-to-cart-single {
    padding: 15px 40px;
    font-size: 1.1rem;
    font-weight: 600;
}

.product-meta {
    border-top: 1px solid var(--warm-beige);
    padding-top: 30px;
    margin-bottom: 40px;
}

.meta-item {
    margin-bottom: 15px;
    font-size: 14px;
}

.meta-item a {
    color: var(--gold-start);
    text-decoration: none;
}

.meta-item a:hover {
    text-decoration: underline;
}

.trust-badges {
    display: flex;
    flex-direction: column;
    gap: 15px;
    padding: 25px;
    background: var(--warm-beige);
    border-radius: 15px;
}

.trust-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 500;
}

.trust-item .dashicons {
    color: var(--gold-start);
    font-size: 20px;
}

/* Product Tabs */
.product-tabs {
    background: var(--white);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin-bottom: 80px;
}

.tabs-nav {
    display: flex;
    background: var(--warm-beige);
    border-bottom: 1px solid var(--primary-taupe);
}

.tab-button {
    flex: 1;
    padding: 20px;
    background: none;
    border: none;
    font-size: 16px;
    font-weight: 600;
    color: var(--charcoal);
    cursor: pointer;
    transition: all 0.3s ease;
}

.tab-button:hover,
.tab-button.active {
    background: var(--white);
    color: var(--gold-start);
}

.tabs-content {
    padding: 40px;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.tab-content h3 {
    margin-bottom: 25px;
    color: var(--charcoal);
}

.additional-info-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.additional-info-table td {
    padding: 15px;
    border-bottom: 1px solid var(--warm-beige);
}

.additional-info-table td:first-child {
    width: 30%;
    background: var(--paper-bg);
}

.shipping-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.shipping-option {
    padding: 25px;
    background: var(--paper-bg);
    border-radius: 15px;
}

.shipping-option h4 {
    margin-bottom: 15px;
    color: var(--gold-start);
}

.shipping-option ul {
    list-style: none;
    padding: 0;
}

.shipping-option li {
    padding: 8px 0;
    border-bottom: 1px solid var(--warm-beige);
}

.shipping-note {
    grid-column: 1 / -1;
    padding: 25px;
    background: linear-gradient(135deg, var(--gold-start), var(--gold-end));
    color: var(--white);
    border-radius: 15px;
    text-align: center;
}

/* Related Products */
.related-products {
    margin-top: 80px;
}

.related-products h3 {
    text-align: center;
    margin-bottom: 40px;
    font-size: 2.5rem;
    color: var(--charcoal);
}

.related-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
}

.related-product {
    background: var(--white);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.related-product:hover {
    transform: translateY(-5px);
}

.related-product a {
    display: block;
    text-decoration: none;
    color: var(--charcoal);
}

.related-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.related-product h4 {
    padding: 20px 20px 10px;
    font-size: 1.1rem;
}

.related-price {
    padding: 0 20px 20px;
    font-weight: 700;
    color: var(--gold-start);
}

/* Responsive Design */
@media (max-width: 768px) {
    .product-layout {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    
    .product-info {
        padding-left: 0;
    }
    
    .product-gallery {
        position: static;
    }
    
    .product-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .quantity-wrapper {
        justify-content: space-between;
    }
    
    .tabs-nav {
        flex-direction: column;
    }
    
    .tabs-content {
        padding: 20px;
    }
    
    .shipping-info {
        grid-template-columns: 1fr;
    }
    
    .trust-badges {
        padding: 20px;
    }
    
    .related-products-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }
}

/* Out of stock styles */
.out-of-stock .btn {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Gallery thumbnails
    $('.thumbnail-image').on('click', function() {
        const largeImage = $(this).data('large');
        $('#main-product-image').attr('src', largeImage);
        
        $('.thumbnail-image').removeClass('active');
        $(this).addClass('active');
    });
    
    // Quantity controls
    $('.quantity-plus').on('click', function() {
        const $input = $(this).siblings('.quantity-input');
        const currentValue = parseInt($input.val()) || 1;
        const maxValue = parseInt($input.attr('max')) || 999;
        
        if (currentValue < maxValue) {
            $input.val(currentValue + 1);
        }
    });
    
    $('.quantity-minus').on('click', function() {
        const $input = $(this).siblings('.quantity-input');
        const currentValue = parseInt($input.val()) || 1;
        const minValue = parseInt($input.attr('min')) || 1;
        
        if (currentValue > minValue) {
            $input.val(currentValue - 1);
        }
    });
    
    // Product tabs
    $('.tab-button').on('click', function() {
        const tabId = $(this).data('tab');
        
        $('.tab-button').removeClass('active');
        $('.tab-content').removeClass('active');
        
        $(this).addClass('active');
        $('#' + tabId).addClass('active');
    });
    
    // Add to cart form
    $('.cart').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $button = $form.find('.add-to-cart-single');
        const productId = $button.closest('form').find('[name="add-to-cart"]').val();
        const quantity = $form.find('.quantity-input').val();
        
        // Show loading state
        $button.find('.btn-text').hide();
        $button.find('.btn-loading').show();
        $button.prop('disabled', true);
        
        // AJAX add to cart
        $.ajax({
            url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
            type: 'POST',
            data: {
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                if (response.error) {
                    // Handle error
                    if (window.TetradkataTheme) {
                        window.TetradkataTheme.showNotification(response.error_message || 'Възникна грешка', 'error');
                    }
                } else {
                    // Success
                    if (window.TetradkataTheme) {
                        window.TetradkataTheme.showNotification('Продуктът е добавен в количката!', 'success');
                        window.TetradkataTheme.updateCartCount(response.fragments['span.cart-count'] || '1');
                    }
                    
                    // Track add to cart
                    if (window.TetradkataTheme) {
                        window.TetradkataTheme.trackEvent('add_to_cart', {
                            item_id: productId,
                            quantity: quantity
                        });
                    }
                }
            },
            error: function() {
                if (window.TetradkataTheme) {
                    window.TetradkataTheme.showNotification('Възникна грешка при добавяне на продукта', 'error');
                }
            },
            complete: function() {
                // Restore button state
                $button.find('.btn-text').show();
                $button.find('.btn-loading').hide();
                $button.prop('disabled', false);
            }
        });
    });
});
</script>

<?php get_footer(); ?>