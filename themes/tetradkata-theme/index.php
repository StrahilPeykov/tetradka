<?php
/**
 * The main template file
 *
 * @package TetradkataTheme
 */

get_header(); ?>

<section class="hero" id="home">
    <div class="container">
        <div class="hero-content">
            <h1><?php echo get_theme_mod('hero_title', 'Тетрадката'); ?></h1>
            <div class="script-text"><?php echo get_theme_mod('hero_subtitle', 'Колекция от спомени'); ?></div>
            <p><?php echo get_theme_mod('hero_description', 'Личният ви дневник за пътешествия, спомени и вдъхновение – събрани в една тетрадка.'); ?></p>
            <a href="#shop" class="btn btn-primary">Купи сега</a>
        </div>
    </div>
</section>

<section class="carousel-section" id="gallery">
    <div class="container">
        <div class="section-header">
            <div class="script-text">Вдъхновение</div>
            <h2>Споделете своята история</h2>
            <p>Всяко пътуване си заслужава да бъде разказано</p>
        </div>
        
        <div class="swiper tetradkata-swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/slide1.jpg');">
                    <div class="slide-overlay">
                        <div class="slide-content">
                            <h3 class="decorative-text">Събирайте спомени</h3>
                            <p>Всяка страница е ново приключение</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/slide2.jpg');">
                    <div class="slide-overlay">
                        <div class="slide-content">
                            <h3 class="decorative-text">Творете своята история</h3>
                            <p>Рисувайте, пишете, вдъхновявайте</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/slide3.jpg');">
                    <div class="slide-overlay">
                        <div class="slide-content">
                            <h3 class="decorative-text">Съхранявайте моментите</h3>
                            <p>За цял живот и още повече</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/slide4.jpg');">
                    <div class="slide-overlay">
                        <div class="slide-content">
                            <h3 class="decorative-text">Вашето творчество</h3>
                            <p>Нямаме граници в мечтите</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/slide5.jpg');">
                    <div class="slide-overlay">
                        <div class="slide-content">
                            <h3 class="decorative-text">Споделете света</h3>
                            <p>Всяка дестинация има своя история</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</section>

<section class="shop-section" id="shop">
    <div class="container">
        <div class="section-header">
            <div class="script-text">Магазин</div>
            <h2>Нашите продукти</h2>
            <p>Създадени с любов за всички пътешественици</p>
        </div>
        
        <div class="products-grid">
            <?php
            if (class_exists('WooCommerce')) {
                $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => 6,
                    'post_status' => 'publish',
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => '_stock_status',
                            'value' => 'instock',
                            'compare' => '='
                        )
                    )
                );
                
                $products = new WP_Query($args);
                
                if ($products->have_posts()) {
                    while ($products->have_posts()) {
                        $products->the_post();
                        global $product;
                        
                        if (!$product || !$product->is_visible()) {
                            continue;
                        }
                        
                        $product_image = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                        if (!$product_image) {
                            $product_image = get_template_directory_uri() . '/assets/images/product-placeholder.jpg';
                        }
                        ?>
                        <div class="product-card">
                            <div class="product-image" style="background-image: url('<?php echo esc_url($product_image); ?>');">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title"><?php the_title(); ?></h3>
                                <div class="product-price"><?php echo $product->get_price_html(); ?></div>
                                <div class="product-description"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></div>
                                
                                <div class="product-actions">
                                    <?php tetradkata_add_to_cart_button($product); ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    wp_reset_postdata();
                } else {
                    echo '<p>Няма налични продукти в момента.</p>';
                }
            } else {
                ?>
                <div class="product-card">
                    <div class="product-image" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/tetradka-main.jpg');">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">Тетрадката - Колекция от спомени</h3>
                        <div class="product-price">45.00 лв.</div>
                        <div class="product-description">Уникален дневник за пътешествия с карти, предизвикателства и пространство за творчество.</div>
                        <div class="product-actions">
                            <button class="btn btn-primary">Купи сега</button>
                        </div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/stickers.jpg');"></div>
                    <div class="product-info">
                        <h3 class="product-title">Комплект стикери</h3>
                        <div class="product-price">12.00 лв.</div>
                        <div class="product-description">Красиви стикери за декориране на вашата тетрадка и спомени.</div>
                        <div class="product-actions">
                            <button class="btn btn-primary">Купи сега</button>
                        </div>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-image" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/markers.jpg');"></div>
                    <div class="product-info">
                        <h3 class="product-title">Цветни маркери</h3>
                        <div class="product-price">18.00 лв.</div>
                        <div class="product-description">Професионални цветни маркери за рисуване и оцветяване.</div>
                        <div class="product-actions">
                            <button class="btn btn-primary">Купи сега</button>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        
        <?php if (class_exists('WooCommerce')) : ?>
            <div class="shop-cta" style="text-align: center; margin-top: 40px;">
                <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="btn btn-secondary">
                    Виж всички продукти
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="how-it-works" id="how-it-works">
    <div class="container">
        <div class="section-header">
            <div class="script-text">Лесно</div>
            <h2>Как работи</h2>
            <p>Три лесни стъпки до вашата мечтана тетрадка</p>
        </div>
        
        <div class="steps-grid">
            <div class="step">
                <div class="step-icon">1</div>
                <h3>Поръчайте</h3>
                <p>Изберете вашите любими продукти и завършете поръчката онлайн. Приемаме карти и наложен платеж.</p>
            </div>
            <div class="step">
                <div class="step-icon">2</div>
                <h3>Получете</h3>
                <p>Вашата поръчка ще бъде доставена до дома ви или до избран от вас адрес в рамките на 1-3 работни дни.</p>
            </div>
            <div class="step">
                <div class="step-icon">3</div>
                <h3>Попълвайте</h3>
                <p>Започнете да създавате вашата колекция от спомени! Рисувайте, пишете и съхранявайте моментите.</p>
            </div>
        </div>
    </div>
</section>

<section class="about-section" id="about">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <div class="script-text">Нашата история</div>
                <h2>За Тетрадката</h2>
                <p>Тетрадката е създадена от любовта към пътуванията и желанието да съхраним най-ценните спомени. Всяка страница е грижливо проектирана, за да ви вдъхнови да творите и споделяте вашите приключения.</p>
                
                <p>Независимо дали сте начинаещ пътешественик или опитен изследовател, Тетрадката ще стане вашият верен спътник в откриването на света и себе си.</p>
                
                <blockquote style="border-left: 3px solid var(--gold-start); padding-left: 20px; margin: 30px 0; font-style: italic;">
                    "Всяко пътуване остава за цял живот! Тетрадката ви помага да запечатате тези моменти завинаги."
                </blockquote>
                
                <a href="#shop" class="btn btn-secondary">Разгледайте продуктите</a>
            </div>
            <div class="about-image" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/about-tetradka.jpg');"></div>
        </div>
    </div>
</section>

<section class="faq-section" id="faq">
    <div class="container">
        <div class="section-header">
            <div class="script-text">Отговори</div>
            <h2>Често задавани въпроси</h2>
            <p>Всичко, което трябва да знаете</p>
        </div>
        
        <div class="faq-container">
            <div class="faq-item">
                <button type="button" class="faq-question">
                    Как мога да направя поръчка?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>Можете да направите поръчка директно от нашия уебсайт. Изберете желаните продукти, добавете ги в количката и следвайте стъпките за завършване на поръчката. Приемаме плащания с банкови карти и наложен платеж.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button type="button" class="faq-question">
                    Какви са възможностите за доставка?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>Доставяме до цяла България чрез Speedy и Econt. Можете да изберете доставка до дома или офис, или до Econt/Speedy офис или автомат. Доставката обикновено отнема 1-3 работни дни.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button type="button" class="faq-question">
                    Каква е цената на доставката?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>Цената на доставката зависи от избраната опция и местоназначението. При поръчки над 50 лв. доставката е безплатна до офис на куриерска фирма.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button type="button" class="faq-question">
                    Мога ли да върна продукт?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>Да, имате право на връщане в рамките на 14 дни от получаването, ако продуктът е в оригиналната си опаковка и състояние. Свържете се с нас за инструкции за връщане.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button type="button" class="faq-question">
                    Предлагате ли опаковка за подарък?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>Да! При поръчката можете да изберете опция за подарък опаковка. Ще опаковаме красиво вашата поръчка и ще приложим картичка за поздрави, ако желаете.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button type="button" class="faq-question">
                    Как мога да се свържа с вас?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>Можете да се свържете с нас по имейл на <?php echo get_theme_mod('contact_email', 'info@tetradkata.bg'); ?> или по телефон <?php echo get_theme_mod('contact_phone', '+359 888 123 456'); ?>. Отговаряме в рамките на 24 часа.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>