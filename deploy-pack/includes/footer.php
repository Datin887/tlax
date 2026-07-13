<?php
/**
 * Футер сайта
 * Путь: /includes/footer.php
 */
?>
<footer class="footer">
    <div class="container">
        <div class="footer__top">
            <div>
                <a href="/" class="logo"><?= e(APP_NAME) ?></a>
                <p><?= e(APP_SLOGAN) ?></p>
            </div>
            <div>
                <h4>Навигация</h4>
                <a href="/">Главная</a><br>
                <a href="/portfolio.php">Портфолио</a><br>
                <a href="/pricing.php">Тарифы</a><br>
                <a href="/contacts.php">Контакты</a>
            </div>
            <div>
                <h4>Контакты</h4>
                <span><?= e(CONTACT_PHONE) ?></span><br>
                <span><a href="https://t.me/<?= ltrim(CONTACT_TELEGRAM, '@') ?>"><?= e(CONTACT_TELEGRAM) ?></a></span><br>
                <span><?= e(CONTACT_EMAIL) ?></span>
            </div>
        </div>
        <div class="footer__bottom">
            <span>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. Все права защищены.</span>
        </div>
    </div>
</footer>