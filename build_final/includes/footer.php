<?php
/**
 * Подвал сайта
 * Контакты, ссылки, копирайт
 * 
 * Путь: /includes/footer.php
 */
$current_year = date('Y');
?>

<footer class="footer" role="contentinfo">
    <div class="container">

        <div class="footer__grid">

            <!-- Бренд -->
            <div class="footer__brand">
                <a href="/" class="footer__logo" aria-label="Хитовая Песня">
                    <div class="footer__logo-icon" aria-hidden="true">🎵</div>
                    <span class="footer__logo-name">Хитовая Песня</span>
                </a>

                <p class="footer__desc">
                    Создаём уникальные персональные песни для&nbsp;ваших
                    праздников. Оплата только после результата.
                </p>

                <!-- Соцсети -->
                <div class="footer__social">
                    <a
                        href="https://vk.com/<?= htmlspecialchars(VK_PAGE, ENT_QUOTES, 'UTF-8') ?>"
                        class="footer__social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="ВКонтакте"
                    >🔵</a>
                    <a
                        href="https://t.me/<?= htmlspecialchars(ltrim(TELEGRAM_USERNAME, '@'), ENT_QUOTES, 'UTF-8') ?>"
                        class="footer__social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Telegram"
                    >✈️</a>
                    <a
                        href="https://ok.ru/<?= htmlspecialchars(OK_PAGE, ENT_QUOTES, 'UTF-8') ?>"
                        class="footer__social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Одноклассники"
                    >🟠</a>
                    <a
                        href="https://wa.me/<?= htmlspecialchars(preg_replace('/\D/', '', WHATSAPP_NUMBER), ENT_QUOTES, 'UTF-8') ?>"
                        class="footer__social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="WhatsApp"
                    >💚</a>
                </div>
            </div><!-- /.footer__brand -->

            <!-- Навигация -->
            <div>
                <h3 class="footer__col-title">Навигация</h3>
                <ul class="footer__links">
                    <li><a href="/"              class="footer__link">Главная</a></li>
                    <li><a href="/portfolio.php" class="footer__link">Портфолио</a></li>
                    <li><a href="/pricing.php"   class="footer__link">Тарифы и цены</a></li>
                    <li><a href="/order.php"     class="footer__link">Заказать песню</a></li>
                    <li><a href="/contacts.php"  class="footer__link">Контакты</a></li>
                </ul>
            </div>

            <!-- Поводы -->
            <div>
                <h3 class="footer__col-title">Поводы</h3>
                <ul class="footer__links">
                    <li><a href="/order.php?occasion=wedding"     class="footer__link">💒 Свадьба</a></li>
                    <li><a href="/order.php?occasion=birthday"    class="footer__link">🎂 День рождения</a></li>
                    <li><a href="/order.php?occasion=anniversary" class="footer__link">💕 Юбилей</a></li>
                    <li><a href="/order.php?occasion=corporate"   class="footer__link">🏢 Корпоратив</a></li>
                    <li><a href="/order.php?occasion=newyear"     class="footer__link">🎄 Новый год</a></li>
                    <li><a href="/order.php?occasion=other"       class="footer__link">✨ Другой повод</a></li>
                </ul>
            </div>

            <!-- Контакты -->
            <div>
                <h3 class="footer__col-title">Контакты</h3>
                <div class="footer__contacts">
                    <div class="footer__contact-item">
                        <span class="footer__contact-icon">📱</span>
                        <a
                            href="tel:<?= htmlspecialchars(preg_replace('/\D/', '', CONTACT_PHONE), ENT_QUOTES, 'UTF-8') ?>"
                            class="footer__link"
                        ><?= htmlspecialchars(CONTACT_PHONE, ENT_QUOTES, 'UTF-8') ?></a>
                    </div>
                    <div class="footer__contact-item">
                        <span class="footer__contact-icon">✉️</span>
                        <a
                            href="mailto:<?= htmlspecialchars(ADMIN_EMAIL, ENT_QUOTES, 'UTF-8') ?>"
                            class="footer__link"
                        ><?= htmlspecialchars(ADMIN_EMAIL, ENT_QUOTES, 'UTF-8') ?></a>
                    </div>
                    <div class="footer__contact-item">
                        <span class="footer__contact-icon">✈️</span>
                        <a
                            href="https://t.me/<?= htmlspecialchars(ltrim(TELEGRAM_USERNAME, '@'), ENT_QUOTES, 'UTF-8') ?>"
                            class="footer__link"
                            target="_blank"
                            rel="noopener noreferrer"
                        ><?= htmlspecialchars(TELEGRAM_USERNAME, ENT_QUOTES, 'UTF-8') ?></a>
                    </div>
                    <div class="footer__contact-item">
                        <span class="footer__contact-icon">🕐</span>
                        <span>Пн–Вс: 9:00 – 22:00 МСК</span>
                    </div>
                </div>
            </div>

        </div><!-- /.footer__grid -->

        <!-- Нижняя строка -->
        <div class="footer__bottom">
            <p class="footer__copy">
                © <?= $current_year ?> Хитовая Песня. Все права защищены.
            </p>
            <div class="footer__copy-links">
                <a href="/privacy.php"  class="footer__copy-link">Политика конфиденциальности</a>
                <a href="/sitemap.xml"  class="footer__copy-link">Карта сайта</a>
            </div>
        </div>

    </div><!-- /.container -->
</footer>

<!-- ─── JavaScript ─── -->
<script src="/assets/js/notifications.js" defer></script>
<script src="/assets/js/player.js" defer></script>
<script src="/assets/js/main.js" defer></script>

<?php if (isset($extra_js)): ?>
    <?php foreach ($extra_js as $js): ?>
        <script src="<?= htmlspecialchars($js, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" defer></script>
    <?php endforeach; ?>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var cards = document.querySelectorAll('.pricing-card');
    var badge = document.querySelector('.pricing-card__badge--popular');
    if (!badge || !cards.length) return;
    for (var i = 0; i < cards.length; i++) {
        cards[i].addEventListener('mouseenter', function() { badge.style.opacity = '0'; });
        cards[i].addEventListener('mouseleave', function() { badge.style.opacity = '1'; });
    }
});
</script>

</body>
</html>