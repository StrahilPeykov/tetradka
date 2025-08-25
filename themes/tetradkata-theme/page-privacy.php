<?php
/**
 * Template Name: Privacy Policy
 * 
 * @package TetradkataTheme
 */

get_header(); ?>

<div class="privacy-container">
    <div class="container">
        <div class="privacy-header">
            <h1>Политика за поверителност</h1>
            <p class="last-updated">Последна актуализация: <?php echo date('d.m.Y'); ?></p>
        </div>

        <div class="privacy-content">
            <section class="privacy-section">
                <h2>1. Въведение</h2>
                <p>В "Тетрадката" се отнасяме сериозно към защитата на вашите лични данни. Тази политика за поверителност обяснява как събираме, използваме, съхраняваме и защитаваме вашата информация, когато използвате нашия уебсайт и услуги.</p>
                <p>Обработваме личните ви данни в съответствие с Общия регламент за защита на данните (GDPR) и българското законодателство за защита на личните данни.</p>
            </section>

            <section class="privacy-section">
                <h2>2. Данни за контакт на администратора</h2>
                <div class="contact-box">
                    <p><strong>Администратор на лични данни:</strong> Тетрадката</p>
                    <p><strong>Адрес:</strong> Варна, жк Възраждане, бл. 30, вх. 2, ап. 33</p>
                    <p><strong>Имейл:</strong> thenotebook.sales@gmail.com</p>
                    <p><strong>Телефон:</strong> <?php echo get_theme_mod('contact_phone', '+359 888 123 456'); ?></p>
                </div>
            </section>

            <section class="privacy-section">
                <h2>3. Какви данни събираме</h2>
                <p>При използването на нашия онлайн магазин, ние събираме следните категории лични данни:</p>
                
                <h3>3.1. Данни при поръчка:</h3>
                <ul>
                    <li>Име и фамилия</li>
                    <li>Адрес за доставка</li>
                    <li>Телефонен номер</li>
                    <li>Имейл адрес</li>
                    <li>Данни за фактуриране (ако е приложимо)</li>
                    <li>ЕИК/БУЛСТАТ (при фирмени поръчки)</li>
                    <li>Име за персонализация (ако е избрана услугата)</li>
                    <li>Кодове за отстъпка (ако се използват)</li>
                </ul>

                <h3>3.2. Технически данни:</h3>
                <ul>
                    <li>IP адрес</li>
                    <li>Тип и версия на браузъра</li>
                    <li>Операционна система</li>
                    <li>Данни за използването на сайта (чрез бисквитки)</li>
                </ul>

                <h3>3.3. Данни за плащане:</h3>
                <p>При плащане с карта, данните за вашата карта се обработват директно от нашия платежен процесор Viva.com. Ние не съхраняваме данни за банкови карти на нашите сървъри.</p>
            </section>

            <section class="privacy-section">
                <h2>4. Цели на обработката</h2>
                <p>Използваме вашите лични данни за следните цели:</p>
                <ul>
                    <li><strong>Изпълнение на поръчки</strong> - обработка, доставка и проследяване на вашите поръчки</li>
                    <li><strong>Персонализация на продукти</strong> - изписване на име върху корицата, ако е заявено</li>
                    <li><strong>Комуникация</strong> - изпращане на потвърждения за поръчки, информация за доставка</li>
                    <li><strong>Счетоводство</strong> - издаване на фактури и изпълнение на законови задължения</li>
                    <li><strong>Подобряване на услугите</strong> - анализ на използването на сайта за подобряване на потребителското изживяване</li>
                    <li><strong>Маркетинг</strong> - изпращане на промоционални съобщения (само със съгласие)</li>
                    <li><strong>Кодове за отстъпка</strong> - управление и проследяване на благодарствени купони</li>
                </ul>
            </section>

            <section class="privacy-section">
                <h2>5. Правно основание</h2>
                <p>Обработваме вашите лични данни на следните правни основания:</p>
                <ul>
                    <li><strong>Изпълнение на договор</strong> - за обработка и доставка на поръчки</li>
                    <li><strong>Законово задължение</strong> - за счетоводни и данъчни цели</li>
                    <li><strong>Легитимен интерес</strong> - за подобряване на услугите и предотвратяване на измами</li>
                    <li><strong>Съгласие</strong> - за маркетингови комуникации и бисквитки</li>
                </ul>
            </section>

            <section class="privacy-section">
                <h2>6. Споделяне на данни</h2>
                <p>Споделяме вашите данни само когато е необходимо за изпълнение на услугите:</p>
                <ul>
                    <li><strong>Куриерски фирми</strong> - Speedy и Econt (име, адрес, телефон за доставка)</li>
                    <li><strong>Платежни процесори</strong> - Viva.com (за обработка на плащания)</li>
                    <li><strong>Счетоводни услуги</strong> - за издаване на фактури и данъчна отчетност</li>
                    <li><strong>Държавни органи</strong> - при законово изискване</li>
                </ul>
                <p>Не продаваме или предоставяме вашите лични данни на трети страни за маркетингови цели.</p>
            </section>

            <section class="privacy-section">
                <h2>7. Съхранение на данни</h2>
                <p>Съхраняваме вашите лични данни само за периода, необходим за целите, за които са събрани:</p>
                <ul>
                    <li><strong>Данни за поръчки</strong> - 5 години за счетоводни цели</li>
                    <li><strong>Данни за персонализация</strong> - до изпълнение на поръчката + 6 месеца</li>
                    <li><strong>Кодове за отстъпка</strong> - до изтичане на валидността им + 1 година за счетоводни цели</li>
                    <li><strong>Маркетингови данни</strong> - до оттегляне на съгласието</li>
                    <li><strong>Технически данни</strong> - 12 месеца</li>
                </ul>
            </section>

            <section class="privacy-section">
                <h2>8. Сигурност на данните</h2>
                <p>Прилагаме подходящи технически и организационни мерки за защита на вашите лични данни:</p>
                <ul>
                    <li>SSL криптиране на всички страници</li>
                    <li>Ограничен достъп до личните данни</li>
                    <li>Редовни актуализации на сигурността</li>
                    <li>Защитени сървъри и бази данни</li>
                    <li>Криптирано съхранение на чувствителна информация</li>
                </ul>
            </section>

            <section class="privacy-section">
                <h2>9. Вашите права</h2>
                <p>Съгласно GDPR, имате следните права относно вашите лични данни:</p>
                <div class="rights-grid">
                    <div class="right-item">
                        <h4>Право на достъп</h4>
                        <p>Можете да поискате копие от личните си данни, които обработваме</p>
                    </div>
                    <div class="right-item">
                        <h4>Право на корекция</h4>
                        <p>Можете да поискате корекция на неточни или непълни данни</p>
                    </div>
                    <div class="right-item">
                        <h4>Право на изтриване</h4>
                        <p>Можете да поискате изтриване на личните си данни при определени условия</p>
                    </div>
                    <div class="right-item">
                        <h4>Право на ограничаване</h4>
                        <p>Можете да поискате ограничаване на обработката при определени обстоятелства</p>
                    </div>
                    <div class="right-item">
                        <h4>Право на преносимост</h4>
                        <p>Можете да получите данните си в структуриран, машинно четим формат</p>
                    </div>
                    <div class="right-item">
                        <h4>Право на възражение</h4>
                        <p>Можете да възразите срещу обработката за директен маркетинг</p>
                    </div>
                </div>
                <p>За да упражните някое от тези права, моля свържете се с нас на thenotebook.sales@gmail.com</p>
            </section>

            <section class="privacy-section">
                <h2>10. Бисквитки</h2>
                <p>Използваме бисквитки за подобряване на вашето изживяване на сайта. Видове бисквитки:</p>
                <ul>
                    <li><strong>Необходими бисквитки</strong> - за функциониране на сайта и количката</li>
                    <li><strong>Аналитични бисквитки</strong> - за анализ на трафика (Google Analytics)</li>
                    <li><strong>Маркетингови бисквитки</strong> - за персонализирани реклами (Facebook Pixel)</li>
                    <li><strong>Функционални бисквитки</strong> - за съхранение на предпочитания (кодове за отстъпка, настройки)</li>
                </ul>
                <p>Можете да управлявате бисквитките чрез настройките на вашия браузър или чрез нашия банер за бисквитки.</p>
            </section>

            <section class="privacy-section">
                <h2>11. Деца</h2>
                <p>Нашият уебсайт не е предназначен за лица под 16 години. Не събираме съзнателно лични данни от деца под тази възраст.</p>
            </section>

            <section class="privacy-section">
                <h2>12. Промени в политиката</h2>
                <p>Запазваме си правото да актуализираме тази политика за поверителност. При съществени промени ще ви уведомим по имейл или чрез известие на сайта.</p>
            </section>

            <section class="privacy-section">
                <h2>13. Жалби</h2>
                <p>Ако имате притеснения относно начина, по който обработваме вашите данни, моля свържете се първо с нас. Имате също право да подадете жалба до:</p>
                <div class="contact-box">
                    <p><strong>Комисия за защита на личните данни</strong></p>
                    <p>Адрес: София 1592, бул. "Проф. Цветан Лазаров" № 2</p>
                    <p>Уебсайт: www.cpdp.bg</p>
                </div>
            </section>
        </div>

        <div class="privacy-footer">
            <p>Ако имате въпроси относно тази политика за поверителност, моля свържете се с нас.</p>
            <a href="<?php echo home_url(); ?>" class="btn btn-primary">Обратно към сайта</a>
        </div>
    </div>
</div>

<style>
.privacy-container {
    background: var(--paper-bg);
    padding: 120px 0 60px;
    min-height: calc(100vh - 200px);
}

.privacy-header {
    text-align: center;
    margin-bottom: 50px;
    padding: 40px;
    background: var(--white);
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.privacy-header h1 {
    color: var(--charcoal);
    margin-bottom: 15px;
}

.last-updated {
    color: #666;
    font-style: italic;
}

.privacy-content {
    background: var(--white);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    max-width: 900px;
    margin: 0 auto;
}

.privacy-section {
    margin-bottom: 40px;
    padding-bottom: 40px;
    border-bottom: 1px solid var(--warm-beige);
}

.privacy-section:last-child {
    border-bottom: none;
}

.privacy-section h2 {
    color: var(--charcoal);
    margin-bottom: 20px;
    font-size: 1.8rem;
}

.privacy-section h3 {
    color: var(--charcoal);
    margin: 20px 0 15px;
    font-size: 1.3rem;
}

.privacy-section h4 {
    color: var(--gold-start);
    margin-bottom: 10px;
}

.privacy-section p {
    line-height: 1.8;
    margin-bottom: 15px;
    color: #333;
}

.privacy-section ul {
    list-style: none;
    padding-left: 0;
}

.privacy-section ul li {
    position: relative;
    padding-left: 25px;
    margin-bottom: 10px;
    line-height: 1.6;
}

.privacy-section ul li::before {
    content: '•';
    position: absolute;
    left: 8px;
    color: var(--gold-start);
    font-weight: bold;
}

.contact-box {
    background: var(--paper-bg);
    padding: 20px;
    border-radius: 10px;
    margin: 20px 0;
}

.contact-box p {
    margin: 5px 0;
}

.rights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 30px 0;
}

.right-item {
    background: var(--paper-bg);
    padding: 20px;
    border-radius: 10px;
    border-left: 3px solid var(--gold-start);
}

.right-item h4 {
    margin-top: 0;
}

.privacy-footer {
    text-align: center;
    margin-top: 50px;
    padding: 40px;
    background: var(--white);
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.privacy-footer p {
    font-size: 1.1rem;
    margin-bottom: 30px;
    color: var(--charcoal);
}

@media (max-width: 768px) {
    .privacy-container {
        padding: 100px 0 40px;
    }
    
    .privacy-content {
        padding: 25px 20px;
    }
    
    .privacy-section h2 {
        font-size: 1.5rem;
    }
    
    .rights-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php get_footer(); ?>