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
                    Какво представлява Тетрадката?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>Тетрадката е травел планер, дневник и албум в едно – създаден, за да събира спомени, планове и мечти.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button type="button" class="faq-question">
                    За кои пътувания е подходяща?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>За всякакви – от уикенд бягства до околосветски приключения.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button type="button" class="faq-question">
                    Само за пътувания в Европа ли е предназначена?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>Съвсем не! Тетрадката няма граници - можеш да я използваш за пътешествия навсякъде по света.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button type="button" class="faq-question">
                    Само на български ли я има?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>В момента – да, но работим и по версия на английски език.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button type="button" class="faq-question">
                    Защо да избера Тетрадката?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>Тетрадката е авторски продукт създаден с много мисъл, внимание и любов.</p>
                    <p><strong>Притежава уникални характеристики:</strong></p>
                    <ul style="margin-top: 10px;">
                        <li>Възможност за персонализация на корицата</li>
                        <li>Луксозна опаковка</li>
                        <li>Твърди корици и специално скрепване на тялото, което позволява да лежи напълно отворена на 180° за максимален комфорт при използване</li>
                        <li>Специално подбрана плътна, висококачествена арт хартия подходяща както за писане, така и за рисуване</li>
                    </ul>
                    <p><strong>Сигурни сме, че ще я обикнеш ❤️</strong></p>
                </div>
            </div>
            
            <div class="faq-item">
                <button type="button" class="faq-question">
                    Как да персонализирам корицата с име?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>Просто следвай стъпките в сайта и той сам ще те отведе до опцията за персонализация. Въведи името по начина, по който искаш да е изписано и ние ще свършим останалото, за да бъде твоята Тетрадка уникална, точно като теб.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button type="button" class="faq-question">
                    Как е опакована Тетрадката и подходяща ли е за подарък?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>Всяка Тетрадка пристига в луксозна кутия с панделка, която я превръща в изискан подарък – за теб или за специален човек.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button type="button" class="faq-question">
                    Как да поръчам?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>Просто избери „Поръчай сега" на сайта, въведи данните си и потвърди или ни пиши на лично съобщение в социалните мрежи.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button type="button" class="faq-question">
                    Какъв е срокът за доставка?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>Винаги се стараем се да обработваме поръчките възможно най-бързо. Стандартния срок за доставка е до 3 работни дни, за продукти без персонализация и от 5 до 7 работни дни за персонализирани такива.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button type="button" class="faq-question">
                    Мога ли да върна тетрадката, ако не ми хареса?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>Да – имаш 14 дни да я върнеш във вида, в който е получена (без следи от употреба и в оригиналната опаковка), но сме сигурни, че няма да искаш.</p>
                    <div style="margin-top: 15px; padding: 15px; background: #fff3e0; border-left: 3px solid #ff9800; border-radius: 0 8px 8px 0;">
                        <p><strong>ВАЖНО:</strong></p>
                        <ul style="margin: 5px 0;">
                            <li><strong>Персонализирани продукти НЕ подлежат на връщане</strong></li>
                            <li>Куриерските такси за връщане на артикула са за сметка на купувача</li>
                            <li>В случай на допусната грешка от наша страна куриерските такси се поемат изцяло от нас</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>