<?php
/**
 * Подвал сайта
 * Путь: /public/includes/footer.php
 */
?>
<footer class="footer">
    <div class="container">
        <div class="grid grid--3">
            <div>
                <div class="footer__logo"><?= h(APP_NAME) ?></div>
                <p class="footer__text">
                    Персонализированные песни для любого повода.
                    Создаём то, что остаётся в сердце.
                </p>
            </div>
            
            <div>
                <h4 class="footer__title">Навигация</h4>
                <nav class="footer__nav">
                    <a href="/portfolio.php">Портфолио</a>
                    <a href="/pricing.php">Тарифы</a>
                    <a href="/order.php">Заказать песню</a>
                    <a href="/contacts.php">Контакты</a>
                </nav>
            </div>
            
            <div>
                <h4 class="footer__title">Контакты</h4>
                <div class="footer__contacts">
                    <p>📱 <?= h(CONTACT_PHONE) ?></p>
                    <p>✉️ <?= h(CONTACT_EMAIL) ?></p>
                    <p>🕐 <?= h(WORK_HOURS) ?></p>
                </div>
            </div>
        </div>
        
        <div class="footer__copyright">
            © <?= date('Y') ?> <?= h(APP_NAME) ?>. Все права защищены.
        </div>
    </div>
</footer>

<script src="/assets/js/main.js"></script>
<script src="/assets/js/player.js"></script>
<script src="/assets/js/notifications.js"></script>
</body>
</html>