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
                $button.find('.btn-text').show();
                $button.find('.btn-loading').hide();
                $button.prop('disabled', false);
            }
        });
    });
});
</script>

<?php get_footer(); ?>