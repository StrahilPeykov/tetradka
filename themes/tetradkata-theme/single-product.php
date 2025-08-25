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
    $gallery_images = $product->get_gallery_image_ids();
    ?>

    <div class="single-product-container">
        <div class="container">
            <nav class="breadcrumbs">
                <a href="<?php echo home_url(); ?>">Начало</a>
                <span class="separator">></span>
                <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>">Магазин</a>
                <span class="separator">></span>
                <span class="current"><?php the_title(); ?></span>
            </nav>

            <div class="product-layout">
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

                <div class="product-info">
                    <h1 class="product-title"><?php the_title(); ?></h1>
                    
                    <div class="product-rating">
                        <?php woocommerce_template_single_rating(); ?>
                    </div>
                    
                    <div class="product-price">
                        <?php echo $product->get_price_html(); ?>
                    </div>
                    
                    <div class="product-short-description">
                        <?php echo apply_filters('woocommerce_short_description', $post->post_excerpt); ?>
                    </div>
                    
                    <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
                        
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
            
            <div class="product-tabs">
                <nav class="tabs-nav">
                    <button class="tab-button active" data-tab="description">Описание</button>
                    <button class="tab-button" data-tab="additional">Допълнителна информация</button>
                    <button class="tab-button" data-tab="reviews">Отзиви</button>
                    <button class="tab-button" data-tab="shipping">Доставка</button>
                </nav>
                
                <div class="tabs-content">
                    <div id="description" class="tab-content active">
                        <h3>Описание на продукта</h3>
                        <?php the_content(); ?>
                    </div>
                    
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
                    
                    <div id="reviews" class="tab-content">
                        <?php comments_template(); ?>
                    </div>
                    
                    <div id="shipping" class="tab-content">
                        <h3>Информация за доставка</h3>
                        <div class="shipping-info">
                            <div class="shipping-option">
                                <h4>Speedy доставка</h4>
                                <ul>
                                    <li>До дома/офиса: 5.99 лв. (до 3 работни дни)</li>
                                    <li>До Speedy офис: 4.99 лв. (до 3 работни дни)</li>
                                    <li>До Speedy автомат: 3.99 лв. (до 3 работни дни)</li>
                                </ul>
                            </div>
                            <div class="shipping-option">
                                <h4>Econt доставка</h4>
                                <ul>
                                    <li>До дома/офиса: 5.99 лв. (до 3 работни дни)</li>
                                    <li>До Econt офис: 4.99 лв. (до 3 работни дни)</li>
                                    <li>До Econt бокс: 3.99 лв. (до 3 работни дни)</li>
                                </ul>
                            </div>
                            <div class="shipping-note">
                                <p><strong>Безплатна доставка при поръчки над 50 лв.!</strong></p>
                                <p>Наложен платеж: +2.99 лв. (само за адреси в България)</p>
                                <p><em>За персонализирани продукти срокът за изпълнение е 5-7 работни дни.</em></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
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
    background: var(--paper-bg);
    padding: 120px 0 60px;
    min-height: calc(100vh - 200px);
}

.breadcrumbs {
    margin-bottom: 30px;
    font-size: 14px;
    color: var(--charcoal);
}

.breadcrumbs a {
    color: var(--gold-start);
    text-decoration: none;
}

.breadcrumbs a:hover {
    text-decoration: underline;
}

.separator {
    margin: 0 10px;
    opacity: 0.5;
}

.current {
    font-weight: 600;
}

.product-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    margin-bottom: 60px;
    background: var(--white);
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.product-gallery {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.main-image {
    width: 100%;
    overflow: hidden;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.product-main-image {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 0.3s ease;
}

.product-main-image:hover {
    transform: scale(1.02);
}

.gallery-thumbnails {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.thumbnail-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    opacity: 0.7;
}

.thumbnail-image:hover,
.thumbnail-image.active {
    opacity: 1;
    border-color: var(--gold-start);
    transform: scale(1.05);
}

.product-info {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.product-title {
    color: var(--charcoal);
    margin: 0;
    font-size: 2.2rem;
}

.product-price {
    font-size: 2rem;
    font-weight: 700;
    color: var(--gold-start);
}

.product-short-description {
    color: var(--charcoal);
    line-height: 1.7;
    opacity: 0.9;
}

.product-actions {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-top: 20px;
}

.quantity-wrapper {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.quantity-wrapper label {
    font-weight: 600;
    color: var(--charcoal);
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 0;
    width: fit-content;
    border: 2px solid var(--warm-beige);
    border-radius: 25px;
    overflow: hidden;
}

.quantity-minus,
.quantity-plus {
    background: var(--warm-beige);
    border: none;
    width: 40px;
    height: 40px;
    cursor: pointer;
    font-size: 18px;
    font-weight: 600;
    color: var(--charcoal);
    transition: all 0.3s ease;
}

.quantity-minus:hover,
.quantity-plus:hover {
    background: var(--gold-start);
    color: var(--white);
}

.quantity-input {
    border: none;
    width: 60px;
    height: 40px;
    text-align: center;
    font-size: 16px;
    font-weight: 600;
    background: var(--white);
}

.quantity-input:focus {
    outline: none;
}

.add-to-cart-single {
    width: 100%;
    padding: 15px 30px;
    font-size: 1.1rem;
    font-weight: 700;
}

.product-meta {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--warm-beige);
}

.meta-item {
    font-size: 14px;
    color: var(--charcoal);
}

.meta-item strong {
    color: var(--charcoal);
    margin-right: 8px;
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
    gap: 12px;
    margin-top: 30px;
    padding: 20px;
    background: var(--paper-bg);
    border-radius: 10px;
}

.trust-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    color: var(--charcoal);
}

.trust-item .dashicons {
    color: var(--gold-start);
    font-size: 18px;
}

/* Product Tabs */
.product-tabs {
    background: var(--white);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    margin-bottom: 60px;
}

.tabs-nav {
    display: flex;
    gap: 0;
    margin-bottom: 30px;
    border-bottom: 2px solid var(--warm-beige);
    overflow-x: auto;
}

.tab-button {
    background: none;
    border: none;
    padding: 15px 25px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    color: var(--charcoal);
    opacity: 0.7;
    transition: all 0.3s ease;
    border-bottom: 3px solid transparent;
    white-space: nowrap;
}

.tab-button:hover,
.tab-button.active {
    opacity: 1;
    color: var(--gold-start);
    border-bottom-color: var(--gold-start);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
    animation: fadeIn 0.3s ease-out;
}

.tab-content h3 {
    color: var(--charcoal);
    margin-bottom: 20px;
}

.additional-info-table {
    width: 100%;
    border-collapse: collapse;
}

.additional-info-table td {
    padding: 12px 15px;
    border-bottom: 1px solid var(--warm-beige);
}

.additional-info-table td:first-child {
    font-weight: 600;
    color: var(--charcoal);
    width: 30%;
}

.shipping-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
}

.shipping-option {
    background: var(--paper-bg);
    padding: 20px;
    border-radius: 10px;
}

.shipping-option h4 {
    color: var(--charcoal);
    margin-bottom: 15px;
}

.shipping-option ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.shipping-option li {
    padding: 8px 0;
    color: var(--charcoal);
    border-bottom: 1px solid rgba(189, 176, 165, 0.3);
}

.shipping-option li:last-child {
    border-bottom: none;
}

.shipping-note {
    grid-column: 1 / -1;
    background: #f0fdf4;
    border: 1px solid #4caf50;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
}

.shipping-note p {
    margin: 5px 0;
    color: #2e7d32;
}

.shipping-note strong {
    color: #1b5e20;
}

.shipping-note em {
    color: #ff9800;
    font-weight: 600;
}

/* Related Products */
.related-products {
    background: var(--white);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.related-products h3 {
    color: var(--charcoal);
    margin-bottom: 30px;
    text-align: center;
}

.related-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
}

.related-product {
    text-align: center;
    transition: transform 0.3s ease;
}

.related-product:hover {
    transform: translateY(-5px);
}

.related-product a {
    text-decoration: none;
    color: var(--charcoal);
}

.related-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 15px;
}

.related-product h4 {
    margin: 10px 0;
    font-size: 1.1rem;
}

.related-price {
    color: var(--gold-start);
    font-weight: 700;
    font-size: 1.1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .single-product-container {
        padding: 100px 0 40px;
    }
    
    .product-layout {
        grid-template-columns: 1fr;
        gap: 30px;
        padding: 25px;
    }
    
    .product-title {
        font-size: 1.8rem;
    }
    
    .product-price {
        font-size: 1.5rem;
    }
    
    .tabs-nav {
        flex-wrap: wrap;
    }
    
    .tab-button {
        flex: 1;
        min-width: 120px;
        padding: 12px 15px;
        font-size: 14px;
    }
    
    .shipping-info {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .related-products-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
    }
    
    .trust-badges {
        padding: 15px;
    }
    
    .product-tabs,
    .related-products {
        padding: 25px;
    }
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Loading states */
.add-to-cart-single.processing {
    pointer-events: none;
    opacity: 0.8;
}

.add-to-cart-single.success {
    background: #22c55e !important;
    transform: scale(1.02);
}

/* Focus styles for accessibility */
.tab-button:focus,
.quantity-minus:focus,
.quantity-plus:focus,
.add-to-cart-single:focus {
    outline: 2px solid var(--gold-start);
    outline-offset: 2px;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('.thumbnail-image').on('click', function() {
        const largeImage = $(this).data('large');
        $('#main-product-image').attr('src', largeImage);
        
        $('.thumbnail-image').removeClass('active');
        $(this).addClass('active');
    });
    
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
    
    $('.tab-button').on('click', function() {
        const tabId = $(this).data('tab');
        
        $('.tab-button').removeClass('active');
        $('.tab-content').removeClass('active');
        
        $(this).addClass('active');
        $('#' + tabId).addClass('active');
    });
    
    $('.cart').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $button = $form.find('.add-to-cart-single');
        const productId = $button.closest('form').find('[name="add-to-cart"]').val();
        const quantity = $form.find('.quantity-input').val();
        
        $button.find('.btn-text').hide();
        $button.find('.btn-loading').show();
        $button.prop('disabled', true);
        
        $.ajax({
            url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
            type: 'POST',
            data: {
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                if (response.error) {
                    if (window.TetradkataTheme) {
                        window.TetradkataTheme.showNotification(response.error_message || 'Възникна грешка', 'error');
                    }
                } else {
                    if (window.TetradkataTheme) {
                        window.TetradkataTheme.showNotification('Продуктът е добавен в количката!', 'success');
                        window.TetradkataTheme.updateCartCount(response.fragments['span.cart-count'] || '1');
                    }
                    
                    $button.addClass('success');
                    $button.find('.btn-text').text('✓ Добавено').show();
                    
                    setTimeout(function() {
                        $button.removeClass('success');
                        $button.find('.btn-text').text('Добави в количката');
                    }, 2000);
                    
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
                $button.find('.btn-loading').hide();
                $button.prop('disabled', false);
                
                if (!$button.hasClass('success')) {
                    $button.find('.btn-text').show();
                }
            }
        });
    });
});
</script>

<?php get_footer(); ?>