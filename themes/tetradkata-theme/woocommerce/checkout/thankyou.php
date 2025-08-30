<?php
/**
 * Order Received (Thank You) Page Template
 * Create as: themes/tetradkata-theme/woocommerce/checkout/thankyou.php
 * 
 * @package TetradkataTheme
 */

defined('ABSPATH') || exit;

$order = wc_get_order($order_id);

if (!$order) {
    echo '<p>Поръчката не е намерена.</p>';
    return;
}

$personalization_name = get_post_meta($order->get_id(), '_personalization_name', true);
$has_personalization = !empty($personalization_name);
$payment_method = $order->get_payment_method();
?>

<div class="checkout-flow-container">
    <div class="container">
        <!-- Success Step Header -->
        <div class="checkout-header">
            <div class="checkout-steps">
                <div class="step completed" data-step="1">
                    <span class="step-number">✓</span>
                    <span class="step-title">Количка</span>
                </div>
                <div class="step-line completed"></div>
                <div class="step completed" data-step="2">
                    <span class="step-number">✓</span>
                    <span class="step-title">Поръчка</span>
                </div>
                <div class="step-line completed"></div>
                <div class="step active completed" data-step="3">
                    <span class="step-number">✓</span>
                    <span class="step-title">Готово</span>
                </div>
            </div>
            
            <div class="success-icon">
                <div class="checkmark-circle">
                    <div class="checkmark"></div>
                </div>
            </div>
            
            <h1>Благодарим ви за поръчката!</h1>
            <p class="success-subtitle">Вашата поръчка е получена успешно</p>
        </div>

        <div class="thank-you-content">
            <div class="thank-you-main">
                
                <!-- Order Summary Card -->
                <div class="order-summary-card">
                    <div class="card-header">
                        <h3>
                            <span class="icon">📋</span>
                            Детайли на поръчката
                        </h3>
                        <span class="order-number">#<?php echo $order->get_order_number(); ?></span>
                    </div>
                    
                    <div class="order-info-grid">
                        <div class="info-item">
                            <span class="label">Дата на поръчка:</span>
                            <span class="value"><?php echo $order->get_date_created()->format('d.m.Y в H:i'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Статус:</span>
                            <span class="value status-<?php echo $order->get_status(); ?>">
                                <?php echo wc_get_order_status_name($order->get_status()); ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">Общо:</span>
                            <span class="value total"><?php echo $order->get_formatted_order_total(); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Начин на плащане:</span>
                            <span class="value"><?php echo $order->get_payment_method_title(); ?></span>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="ordered-items">
                        <h4>Поръчани продукти</h4>
                        <div class="items-list">
                            <?php
                            foreach ($order->get_items() as $item_id => $item) {
                                $product = $item->get_product();
                                $product_image = wp_get_attachment_image_url($product->get_image_id(), 'thumbnail');
                                if (!$product_image) {
                                    $product_image = wc_placeholder_img_src('thumbnail');
                                }
                                ?>
                                <div class="order-item">
                                    <div class="item-image">
                                        <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($item->get_name()); ?>">
                                    </div>
                                    <div class="item-details">
                                        <h5><?php echo $item->get_name(); ?></h5>
                                        <div class="item-meta">
                                            <span>Количество: <?php echo $item->get_quantity(); ?></span>
                                            <span>Цена: <?php echo wc_price($item->get_total()); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            
                            // Show personalization fee if exists
                            foreach ($order->get_fees() as $fee) {
                                if ($fee->get_name() === 'Персонализация') {
                                    ?>
                                    <div class="order-item personalization-fee">
                                        <div class="item-image">
                                            <div class="fee-icon">✨</div>
                                        </div>
                                        <div class="item-details">
                                            <h5>Персонализация</h5>
                                            <div class="item-meta">
                                                <span>Име: <?php echo esc_html($personalization_name); ?></span>
                                                <span>Цена: <?php echo wc_price($fee->get_total()); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Personalization Notice -->
                <?php if ($has_personalization) : ?>
                <div class="personalization-notice">
                    <div class="notice-header">
                        <span class="icon">✨</span>
                        <h3>Персонализация в процес</h3>
                    </div>
                    <div class="notice-content">
                        <p><strong>Име за корицата:</strong> <?php echo esc_html($personalization_name); ?></p>
                        <div class="timeline">
                            <div class="timeline-item">
                                <span class="timeline-dot active"></span>
                                <span class="timeline-text">Поръчката е получена</span>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-dot"></span>
                                <span class="timeline-text">Персонализация (3-5 дни)</span>
                            </div>
                            <div class="timeline-item">
                                <span class="timeline-dot"></span>
                                <span class="timeline-text">Изпращане (1-2 дни)</span>
                            </div>
                        </div>
                        <div class="estimated-delivery">
                            <strong>Очаквана доставка: 5-7 работни дни</strong>
                        </div>
                    </div>
                </div>
                <?php else : ?>
                <!-- Standard Delivery Timeline -->
                <div class="delivery-timeline">
                    <div class="timeline-header">
                        <span class="icon">📦</span>
                        <h3>Статус на доставката</h3>
                    </div>
                    <div class="timeline">
                        <div class="timeline-item">
                            <span class="timeline-dot active"></span>
                            <span class="timeline-text">Поръчката е получена</span>
                        </div>
                        <div class="timeline-item">
                            <span class="timeline-dot"></span>
                            <span class="timeline-text">Подготовка (1 ден)</span>
                        </div>
                        <div class="timeline-item">
                            <span class="timeline-dot"></span>
                            <span class="timeline-text">Изпращане (1-2 дни)</span>
                        </div>
                    </div>
                    <div class="estimated-delivery">
                        <strong>Очаквана доставка: 1-3 работни дни</strong>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Payment Instructions -->
                <?php if ($payment_method === 'bacs') : ?>
                <div class="payment-instructions">
                    <div class="instruction-header">
                        <span class="icon">🏦</span>
                        <h3>Инструкции за банков превод</h3>
                    </div>
                    <div class="instruction-content">
                        <p><strong>Важно:</strong> Поръчката ви ще бъде обработена след получаване на плащането.</p>
                        
                        <div class="bank-details">
                            <h4>Банкови данни за превод:</h4>
                            <div class="bank-info">
                                <div class="bank-row">
                                    <span class="label">Банка:</span>
                                    <span class="value">Уникредит Булбанк</span>
                                </div>
                                <div class="bank-row">
                                    <span class="label">IBAN:</span>
                                    <span class="value">BG18 UNCR 7630 1000 1234 56</span>
                                    <button class="copy-btn" onclick="copyToClipboard('BG18 UNCR 7630 1000 1234 56')">Копирай</button>
                                </div>
                                <div class="bank-row">
                                    <span class="label">Основание:</span>
                                    <span class="value">Поръчка #<?php echo $order->get_order_number(); ?></span>
                                    <button class="copy-btn" onclick="copyToClipboard('Поръчка #<?php echo $order->get_order_number(); ?>')">Копирай</button>
                                </div>
                                <div class="bank-row">
                                    <span class="label">Сума:</span>
                                    <span class="value"><?php echo $order->get_formatted_order_total(); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="payment-note">
                            <p>След извършване на превода, моля изпратете ни потвърждение на <strong>thenotebook.sales@gmail.com</strong></p>
                        </div>
                    </div>
                </div>
                <?php elseif ($payment_method === 'cod') : ?>
                <div class="payment-instructions cod-instructions">
                    <div class="instruction-header">
                        <span class="icon">💰</span>
                        <h3>Плащане при получаване</h3>
                    </div>
                    <div class="instruction-content">
                        <p>Ще платите <strong><?php echo $order->get_formatted_order_total(); ?></strong> в брой при получаване на пратката.</p>
                        <div class="cod-note">
                            <p><strong>Забележка:</strong> Включена е такса за наложен платеж от 2.90 лв.</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="thank-you-sidebar">
                
                <!-- Contact Card -->
                <div class="contact-card">
                    <div class="card-header">
                        <h3>
                            <span class="icon">📞</span>
                            Нужна е помощ?
                        </h3>
                    </div>
                    <div class="contact-info">
                        <div class="contact-method">
                            <span class="method-icon">✉️</span>
                            <div class="method-details">
                                <span class="method-label">Имейл</span>
                                <a href="mailto:thenotebook.sales@gmail.com">thenotebook.sales@gmail.com</a>
                            </div>
                        </div>
                        <div class="contact-method">
                            <span class="method-icon">📱</span>
                            <div class="method-details">
                                <span class="method-label">Телефон</span>
                                <a href="tel:+359888123456">+359 888 123 456</a>
                            </div>
                        </div>
                        <div class="contact-method">
                            <span class="method-icon">🕒</span>
                            <div class="method-details">
                                <span class="method-label">Работно време</span>
                                <span>Пон-Пет: 9:00-18:00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="action-buttons">
                    <a href="<?php echo $order->get_view_order_url(); ?>" class="btn btn-primary">
                        <span class="dashicons dashicons-visibility"></span>
                        Виж поръчката
                    </a>
                    <a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="btn btn-secondary">
                        <span class="dashicons dashicons-cart"></span>
                        Продължи пазаруването
                    </a>
                </div>

                <!-- Order Updates -->
                <div class="order-updates">
                    <h4>Актуализации за поръчката</h4>
                    <div class="update-methods">
                        <div class="update-method">
                            <span class="dashicons dashicons-email"></span>
                            <span>Имейл известия</span>
                        </div>
                        <div class="update-method">
                            <span class="dashicons dashicons-smartphone"></span>
                            <span>SMS известия</span>
                        </div>
                    </div>
                </div>

                <!-- Return Policy -->
                <div class="return-policy">
                    <h4>Политика за връщане</h4>
                    <div class="policy-items">
                        <div class="policy-item">
                            <span class="policy-icon">✅</span>
                            <span>14 дни за връщане</span>
                        </div>
                        <div class="policy-item">
                            <span class="policy-icon">📦</span>
                            <span>Оригинална опаковка</span>
                        </div>
                        <div class="policy-item">
                            <span class="policy-icon">⚠️</span>
                            <span>Персонализирани продукти НЕ се връщат</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Animation -->
<div id="success-animation" class="success-overlay" style="display: none;">
    <div class="success-content">
        <div class="success-checkmark">
            <div class="check-icon">
                <span class="icon-line line-tip"></span>
                <span class="icon-line line-long"></span>
                <div class="icon-circle"></div>
                <div class="icon-fix"></div>
            </div>
        </div>
        <h2>Поръчката е успешна!</h2>
    </div>
</div>

<style>
/* Thank You Page Styles */
.checkout-flow-container {
    background: #fafafa;
    padding: 80px 0 60px;
    min-height: calc(100vh - 200px);
}

/* Success Header */
.checkout-header {
    text-align: center;
    margin-bottom: 40px;
}

.success-icon {
    margin: 20px 0 30px;
}

.checkmark-circle {
    width: 80px;
    height: 80px;
    position: relative;
    display: inline-block;
    vertical-align: top;
    margin: 0 auto;
}

.checkmark-circle .checkmark {
    border-radius: 50%;
    display: block;
    stroke-width: 3;
    stroke: #22c55e;
    stroke-miterlimit: 10;
    box-shadow: inset 0px 0px 0px #22c55e;
    animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
    position: relative;
    top: 50%;
    left: 50%;
    width: 80px;
    height: 80px;
    transform: translate(-50%, -50%);
    background: #22c55e;
    border-radius: 50%;
}

.checkmark-circle .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 32px;
    font-weight: bold;
    animation: checkmark 0.3s ease-in-out 0.9s both;
}

/* Layout */
.thank-you-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 40px;
}

/* Cards */
.order-summary-card,
.personalization-notice,
.delivery-timeline,
.payment-instructions,
.contact-card {
    background: var(--white);
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.card-header,
.notice-header,
.timeline-header,
.instruction-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--warm-beige);
}

.card-header h3,
.notice-header h3,
.timeline-header h3,
.instruction-header h3 {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    color: var(--charcoal);
}

.order-number {
    background: var(--gold-start);
    color: var(--white);
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

/* Order Info Grid */
.order-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 25px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.info-item .label {
    font-weight: 600;
    color: var(--charcoal);
}

.info-item .value {
    font-weight: 500;
}

.info-item .total {
    font-size: 18px;
    font-weight: 700;
    color: var(--gold-start);
}

.status-processing { color: #f39c12; }
.status-completed { color: #22c55e; }
.status-on-hold { color: #e74c3c; }
.status-pending { color: #95a5a6; }

/* Order Items */
.ordered-items h4 {
    margin-bottom: 15px;
    color: var(--charcoal);
}

.items-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.order-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 10px;
}

.order-item.personalization-fee {
    background: linear-gradient(135deg, #fff5e6, #f8f9fa);
    border: 1px solid #ffe0b3;
}

.item-image {
    flex-shrink: 0;
}

.item-image img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px;
}

.fee-icon {
    width: 50px;
    height: 50px;
    background: var(--gold-start);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
}

.item-details h5 {
    margin-bottom: 5px;
    color: var(--charcoal);
}

.item-meta {
    display: flex;
    gap: 20px;
    font-size: 13px;
    color: #666;
}

/* Personalization Notice */
.personalization-notice {
    background: linear-gradient(135deg, #fff5e6, #fff);
    border: 2px solid #ffe0b3;
}

.personalization-notice .notice-content p {
    margin-bottom: 15px;
    font-weight: 600;
}

/* Timeline */
.timeline {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin: 20px 0;
}

.timeline-item {
    display: flex;
    align-items: center;
    gap: 15px;
}

.timeline-dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #e0e0e0;
    position: relative;
}

.timeline-dot.active {
    background: var(--gold-start);
}

.timeline-dot.active::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 10px;
    font-weight: bold;
}

.timeline-text {
    font-size: 14px;
    color: var(--charcoal);
}

.estimated-delivery {
    background: #e8f5e8;
    padding: 12px 15px;
    border-radius: 8px;
    border-left: 4px solid #22c55e;
    margin-top: 15px;
}

/* Payment Instructions */
.payment-instructions {
    background: linear-gradient(135deg, #e8f4fd, #fff);
    border: 2px solid #bee5eb;
}

.bank-details {
    margin: 20px 0;
}

.bank-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.bank-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.bank-row:last-child {
    border-bottom: none;
}

.bank-row .label {
    font-weight: 600;
    color: var(--charcoal);
    min-width: 100px;
}

.bank-row .value {
    flex: 1;
    text-align: center;
    font-family: monospace;
    background: white;
    padding: 4px 8px;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.copy-btn {
    background: var(--gold-start);
    color: white;
    border: none;
    padding: 4px 12px;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.copy-btn:hover {
    background: var(--gold-end);
}

.payment-note {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    padding: 12px;
    border-radius: 6px;
    margin-top: 15px;
}

/* COD Instructions */
.cod-instructions {
    background: linear-gradient(135deg, #f0fdf4, #fff);
    border: 2px solid #bbf7d0;
}

.cod-note {
    background: #ecfdf5;
    border: 1px solid #86efac;
    padding: 12px;
    border-radius: 6px;
    margin-top: 15px;
}

/* Sidebar */
.thank-you-sidebar {
    display: flex;
    flex-direction: column;
}

/* Contact Card */
.contact-info {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.contact-method {
    display: flex;
    align-items: center;
    gap: 12px;
}

.method-icon {
    font-size: 18px;
    width: 30px;
    text-align: center;
}

.method-details {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.method-label {
    font-size: 12px;
    color: #666;
    font-weight: 600;
    text-transform: uppercase;
}

.method-details a {
    color: var(--gold-start);
    text-decoration: none;
    font-weight: 500;
}

.method-details a:hover {
    text-decoration: underline;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 25px;
}

.action-buttons .btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 15px;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

/* Order Updates & Return Policy */
.order-updates,
.return-policy {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.order-updates h4,
.return-policy h4 {
    margin-bottom: 15px;
    color: var(--charcoal);
    font-size: 16px;
}

.update-methods,
.policy-items {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.update-method,
.policy-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
}

.policy-icon {
    width: 20px;
    text-align: center;
}

/* Success Animation Overlay */
.success-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.success-content {
    text-align: center;
    color: white;
}

.success-checkmark {
    margin-bottom: 20px;
}

/* Animations */
@keyframes fill {
    100% { box-shadow: inset 0px 0px 0px 60px #22c55e; }
}

@keyframes scale {
    0%, 100% { transform: translate(-50%, -50%) scale(1); }
    50% { transform: translate(-50%, -50%) scale(1.1); }
}

@keyframes checkmark {
    0% { opacity: 0; }
    100% { opacity: 1; }
}

/* Responsive Design */
@media (max-width: 768px) {
    .checkout-flow-container {
        padding: 60px 0 40px;
    }
    
    .thank-you-content {
        grid-template-columns: 1fr;
        gap: 25px;
    }
    
    .order-info-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .bank-row {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
    }
    
    .bank-row .value {
        text-align: left;
    }
    
    .action-buttons {
        order: -1;
    }
    
    .contact-card,
    .order-summary-card,
    .personalization-notice,
    .payment-instructions {
        padding: 20px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Show success animation on page load
    setTimeout(function() {
        $('#success-animation').fadeIn(500);
        setTimeout(function() {
            $('#success-animation').fadeOut(500);
        }, 2500);
    }, 500);
    
    // Copy to clipboard functionality
    window.copyToClipboard = function(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                showCopySuccess();
            });
        } else {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showCopySuccess();
        }
    };
    
    function showCopySuccess() {
        const $notification = $('<div class="copy-notification">Копирано в клипборда!</div>');
        $('body').append($notification);
        
        setTimeout(function() {
            $notification.addClass('show');
        }, 100);
        
        setTimeout(function() {
            $notification.removeClass('show');
            setTimeout(function() {
                $notification.remove();
            }, 300);
        }, 2000);
    }
    
    // Add smooth scrolling to any anchor links
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $($(this).attr('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 500);
        }
    });
    
    // Auto-refresh order status (optional)
    if (typeof tetradkata_ajax !== 'undefined') {
        setTimeout(function() {
            // Could implement periodic status checking here
            console.log('Order status check available');
        }, 5000);
    }
});
</script>

<style>
.copy-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #22c55e;
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    z-index: 10001;
    transform: translateX(100%);
    transition: transform 0.3s ease;
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
}

.copy-notification.show {
    transform: translateX(0);
}
</style>