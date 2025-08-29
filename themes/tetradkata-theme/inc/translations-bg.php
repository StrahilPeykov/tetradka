<?php
/**
 * Bulgarian Translations for WooCommerce
 * 
 * @package TetradkataTheme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Translate WooCommerce strings to Bulgarian
 */
function tetradkata_translate_woocommerce_strings($translation, $text, $domain) {
    if ($domain !== 'woocommerce') {
        return $translation;
    }
    
    $translations = array(
        // Checkout page
        'Billing details' => 'Данни за фактуриране',
        'Shipping details' => 'Данни за доставка',
        'Ship to a different address?' => 'Доставка на различен адрес?',
        'Additional information' => 'Допълнителна информация',
        'Your order' => 'Вашата поръчка',
        'Place order' => 'Завърши поръчката',
        
        // Form fields
        'First name' => 'Име',
        'Last name' => 'Фамилия',
        'Company name' => 'Име на фирма',
        'Company name (optional)' => 'Име на фирма (незадължително)',
        'Country / Region' => 'Държава',
        'Street address' => 'Адрес',
        'House number and street name' => 'Улица и номер',
        'Apartment, suite, unit, etc.' => 'Апартамент, етаж, вход и др.',
        'Apartment, suite, unit, etc. (optional)' => 'Апартамент, етаж, вход и др. (незадължително)',
        'Town / City' => 'Град',
        'State / County' => 'Област',
        'State / County (optional)' => 'Област (незадължително)',
        'Postcode / ZIP' => 'Пощенски код',
        'Phone' => 'Телефон',
        'Phone (optional)' => 'Телефон (незадължително)',
        'Email address' => 'Имейл адрес',
        'Order notes (optional)' => 'Забележки към поръчката (незадължително)',
        'Notes about your order, e.g. special notes for delivery.' => 'Забележки към поръчката, напр. специални инструкции за доставка.',
        
        // Required field
        'Required' => 'Задължително',
        'Optional' => 'Незадължително',
        '(optional)' => '(незадължително)',
        'required' => 'задължително',
        
        // Table headers
        'Product' => 'Продукт',
        'Price' => 'Цена',
        'Quantity' => 'Количество',
        'Subtotal' => 'Междинна сума',
        'Total' => 'Общо',
        
        // Payment
        'Payment method' => 'Начин на плащане',
        'Cash on delivery' => 'Наложен платеж',
        'Pay with cash upon delivery.' => 'Плащане при доставка на продуктите.',
        'Direct bank transfer' => 'Банков превод',
        'Bank transfer' => 'Банков превод',
        'Credit Card' => 'Кредитна карта',
        'Debit Card' => 'Дебитна карта',
        
        // Shipping
        'Shipping' => 'Доставка',
        'Free shipping' => 'Безплатна доставка',
        'Flat rate' => 'Фиксирана цена',
        'Local pickup' => 'Вземане от място',
        
        // Coupons
        'Have a coupon?' => 'Имате код за отстъпка?',
        'Click here to enter your code' => 'Кликнете тук, за да въведете вашия код',
        'Coupon code' => 'Код за отстъпка',
        'Apply coupon' => 'Приложи код',
        'Remove coupon' => 'Премахни код',
        'Coupon:' => 'Код за отстъпка:',
        
        // Messages
        'Please enter a valid email address.' => 'Моля, въведете валиден имейл адрес.',
        'Please enter a valid phone number.' => 'Моля, въведете валиден телефонен номер.',
        'This field is required.' => 'Това поле е задължително.',
        'Please select a valid option.' => 'Моля, изберете валидна опция.',
        'Billing %s is a required field.' => 'Полето %s е задължително.',
        'Shipping %s is a required field.' => 'Полето %s за доставка е задължително.',
        
        // Cart/Account
        'Cart' => 'Количка',
        'View cart' => 'Виж количката',
        'Checkout' => 'Плащане',
        'My account' => 'Моят профил',
        'Dashboard' => 'Табло',
        'Orders' => 'Поръчки',
        'Downloads' => 'Изтегляния',
        'Addresses' => 'Адреси',
        'Account details' => 'Данни за профила',
        'Logout' => 'Изход',
        'Login' => 'Вход',
        'Register' => 'Регистрация',
        'Remember me' => 'Запомни ме',
        'Lost your password?' => 'Забравена парола?',
        'Username or email address' => 'Потребителско име или имейл',
        'Password' => 'Парола',
        
        // Product page
        'In stock' => 'В наличност',
        'Out of stock' => 'Няма в наличност',
        '%s in stock' => '%s в наличност',
        'Add to cart' => 'Добави в количката',
        'Select options' => 'Избери опции',
        'Read more' => 'Научи повече',
        'Description' => 'Описание',
        'Reviews' => 'Отзиви',
        'Related products' => 'Свързани продукти',
        'SKU:' => 'Артикул:',
        'Category:' => 'Категория:',
        'Categories:' => 'Категории:',
        'Tag:' => 'Етикет:',
        'Tags:' => 'Етикети:',
        
        // Shop page
        'Shop' => 'Магазин',
        'Showing %1$d–%2$d of %3$d results' => 'Показване на %1$d–%2$d от %3$d резултата',
        'Showing the single result' => 'Показване на единствения резултат',
        'Sort by popularity' => 'Подреди по популярност',
        'Sort by average rating' => 'Подреди по оценка',
        'Sort by latest' => 'Подреди по дата',
        'Sort by price: low to high' => 'Подреди по цена: ниска към висока',
        'Sort by price: high to low' => 'Подреди по цена: висока към ниска',
        'Default sorting' => 'Стандартно подреждане',
        'Sale!' => 'Разпродажба!',
        'New!' => 'Ново!',
        
        // Cart page
        'Cart totals' => 'Обща сума',
        'Cart subtotal' => 'Междинна сума',
        'Proceed to checkout' => 'Към плащане',
        'Update cart' => 'Обнови количката',
        'Continue shopping' => 'Продължи пазаруването',
        'Apply coupon' => 'Приложи купон',
        'Coupon discount' => 'Отстъпка с купон',
        'Remove' => 'Премахни',
        'Your cart is currently empty.' => 'Количката ви е празна в момента.',
        'Return to shop' => 'Обратно към магазина',
        
        // Order received
        'Thank you. Your order has been received.' => 'Благодарим ви. Вашата поръчка е получена.',
        'Order number:' => 'Номер на поръчка:',
        'Date:' => 'Дата:',
        'Email:' => 'Имейл:',
        'Total:' => 'Общо:',
        'Payment method:' => 'Начин на плащане:',
        
        // Account pages
        'From your account dashboard you can view your recent orders, manage your shipping and billing addresses, and edit your password and account details.' => 
            'От таблото на вашия профил можете да видите последните си поръчки, да управлявате адресите за доставка и фактуриране и да редактирате паролата и данните за профила си.',
        'No order has been made yet.' => 'Все още няма направени поръчки.',
        'Browse products' => 'Разгледай продуктите',
        
        // Countries
        'Bulgaria' => 'България',
        'United States (US)' => 'Съединени щати',
        'United Kingdom (UK)' => 'Великобритания',
        'Germany' => 'Германия',
        'France' => 'Франция',
        'Italy' => 'Италия',
        'Spain' => 'Испания',
        'Greece' => 'Гърция',
        'Romania' => 'Румъния',
        'Serbia' => 'Сърбия',
        'Turkey' => 'Турция',
        
        // Error messages
        'Sorry, we do not have enough "%1$s" in stock to fulfill your order (%2$s available). We apologize for any inconvenience caused.' => 
            'За съжаление нямаме достатъчно "%1$s" в наличност, за да изпълним поръчката ви (%2$s налични). Извиняваме се за причиненото неудобство.',
        'Invalid payment method.' => 'Невалиден метод на плащане.',
        'Something went wrong. Please try again.' => 'Нещо се обърка. Моля, опитайте отново.',
        'Please enter a valid postcode / ZIP.' => 'Моля, въведете валиден пощенски код.',
        'Please enter a valid phone number.' => 'Моля, въведете валиден телефонен номер.',
        
        // Success messages
        'Coupon code applied successfully.' => 'Кодът за отстъпка е приложен успешно.',
        'Coupon code removed successfully.' => 'Кодът за отстъпка е премахнат успешно.',
        'Cart updated.' => 'Количката е обновена.',
        '"%s" has been added to your cart.' => '"%s" беше добавен към вашата количка.',
        
        // Validation
        '%s is a required field.' => '%s е задължително поле.',
        '%s is not a valid email address.' => '%s не е валиден имейл адрес.',
        '%s is not a valid phone number.' => '%s не е валиден телефонен номер.',
        
        // Privacy
        'Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our %s.' => 
            'Вашите лични данни ще бъдат използвани за обработка на поръчката, подобряване на вашето изживяване в този уебсайт и за други цели, описани в нашата %s.',
        'privacy policy' => 'политика за поверителност',
        'terms and conditions' => 'общи условия',
        'I have read and agree to the website %s' => 'Прочетох и приемам %s на уебсайта',
    );
    
    if (isset($translations[$text])) {
        return $translations[$text];
    }
    
    return $translation;
}
add_filter('gettext', 'tetradkata_translate_woocommerce_strings', 20, 3);

/**
 * Translate WooCommerce strings with context
 */
function tetradkata_translate_woocommerce_strings_with_context($translation, $text, $context, $domain) {
    if ($domain !== 'woocommerce') {
        return $translation;
    }
    
    $translations = array(
        'Billing details' => 'Данни за фактуриране',
        'Shipping details' => 'Данни за доставка',
        'Order notes' => 'Забележки към поръчката',
    );
    
    if (isset($translations[$text])) {
        return $translations[$text];
    }
    
    return $translation;
}
add_filter('gettext_with_context', 'tetradkata_translate_woocommerce_strings_with_context', 20, 4);

/**
 * Customize Bulgarian state/province names
 */
function tetradkata_bulgarian_states($states) {
    $states['BG'] = array(
        'BLG' => 'Благоевград',
        'BGS' => 'Бургас',
        'DOB' => 'Добрич',
        'GAB' => 'Габрово',
        'HAS' => 'Хасково',
        'KRZ' => 'Кърджали',
        'KUS' => 'Кюстендил',
        'LOV' => 'Ловеч',
        'MON' => 'Монтана',
        'PAZ' => 'Пазарджик',
        'PER' => 'Перник',
        'PVN' => 'Плевен',
        'PDV' => 'Пловдив',
        'RAZ' => 'Разград',
        'RSE' => 'Русе',
        'SHU' => 'Шумен',
        'SLS' => 'Силистра',
        'SLV' => 'Сливен',
        'SMO' => 'Смолян',
        'SFO' => 'София област',
        'SOF' => 'София град',
        'SZR' => 'Стара Загора',
        'TGV' => 'Търговище',
        'VAR' => 'Варна',
        'VTR' => 'Велико Търново',
        'VID' => 'Видин',
        'VRC' => 'Враца',
        'YAM' => 'Ямбол',
    );
    
    return $states;
}
add_filter('woocommerce_states', 'tetradkata_bulgarian_states');

/**
 * Customize checkout field labels for Bulgarian
 */
function tetradkata_bulgarian_checkout_fields($fields) {
    // Billing fields
    $fields['billing']['billing_first_name']['label'] = 'Име';
    $fields['billing']['billing_last_name']['label'] = 'Фамилия';
    $fields['billing']['billing_company']['label'] = 'Име на фирма';
    $fields['billing']['billing_country']['label'] = 'Държава';
    $fields['billing']['billing_address_1']['label'] = 'Адрес';
    $fields['billing']['billing_address_2']['label'] = 'Апартамент, етаж, вход и др.';
    $fields['billing']['billing_city']['label'] = 'Град';
    $fields['billing']['billing_state']['label'] = 'Област';
    $fields['billing']['billing_postcode']['label'] = 'Пощенски код';
    $fields['billing']['billing_phone']['label'] = 'Телефон';
    $fields['billing']['billing_email']['label'] = 'Имейл адрес';
    
    // Shipping fields
    $fields['shipping']['shipping_first_name']['label'] = 'Име';
    $fields['shipping']['shipping_last_name']['label'] = 'Фамилия';
    $fields['shipping']['shipping_company']['label'] = 'Име на фирма';
    $fields['shipping']['shipping_country']['label'] = 'Държава';
    $fields['shipping']['shipping_address_1']['label'] = 'Адрес';
    $fields['shipping']['shipping_address_2']['label'] = 'Апартамент, етаж, вход и др.';
    $fields['shipping']['shipping_city']['label'] = 'Град';
    $fields['shipping']['shipping_state']['label'] = 'Област';
    $fields['shipping']['shipping_postcode']['label'] = 'Пощенски код';
    
    // Order notes
    $fields['order']['order_comments']['label'] = 'Забележки към поръчката';
    $fields['order']['order_comments']['placeholder'] = 'Забележки към поръчката, напр. специални инструкции за доставка.';
    
    // Placeholders
    $fields['billing']['billing_first_name']['placeholder'] = 'Име';
    $fields['billing']['billing_last_name']['placeholder'] = 'Фамилия';
    $fields['billing']['billing_email']['placeholder'] = 'example@email.com';
    $fields['billing']['billing_phone']['placeholder'] = '+359 888 123 456';
    $fields['billing']['billing_address_1']['placeholder'] = 'ул. Име на улица №';
    $fields['billing']['billing_city']['placeholder'] = 'София';
    $fields['billing']['billing_postcode']['placeholder'] = '1000';
    
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'tetradkata_bulgarian_checkout_fields', 20);