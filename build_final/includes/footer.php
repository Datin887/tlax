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
                    ><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M15.684 0H8.316C1.592 0 0 1.592 0 8.316v7.368C0 22.408 1.592 24 8.316 24h7.368C22.408 24 24 22.408 24 15.684V8.316C24 1.592 22.391 0 15.684 0zm3.692 17.123h-1.744c-.66 0-.862-.525-2.049-1.714-1.033-1.01-1.49-1.135-1.744-1.135-.356 0-.458.102-.458.593v1.575c0 .424-.135.678-1.253.678-1.846 0-3.896-1.118-5.335-3.202C4.624 10.857 4.03 8.57 4.03 8.096c0-.254.102-.491.593-.491h1.744c.44 0 .61.203.78.678.863 2.49 2.303 4.675 2.896 4.675.22 0 .322-.102.322-.66V9.721c-.068-1.186-.695-1.287-.695-1.71 0-.203.17-.407.44-.407h2.744c.373 0 .508.203.508.643v3.473c0 .372.17.508.271.508.22 0 .407-.136.813-.542 1.254-1.406 2.152-3.574 2.152-3.574.119-.254.322-.491.763-.491h1.744c.525 0 .644.27.525.643-.22 1.017-2.354 4.031-2.354 4.031-.186.305-.254.44 0 .78.186.254.796.779 1.203 1.253.745.847 1.32 1.558 1.473 2.049.17.49-.085.744-.576.744z"/></svg></a>
                    <a
                        href="https://t.me/<?= htmlspecialchars(ltrim(TELEGRAM_USERNAME, '@'), ENT_QUOTES, 'UTF-8') ?>"
                        class="footer__social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Telegram"
                    ><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg></a>
                    <a
                        href="https://ok.ru/<?= htmlspecialchars(OK_PAGE, ENT_QUOTES, 'UTF-8') ?>"
                        class="footer__social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Одноклассники"
                    ><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 3.6c2.28 0 4.2 1.92 4.2 4.2S14.28 12 12 12s-4.2-1.92-4.2-4.2S9.72 3.6 12 3.6zm0 14.4c-3.06 0-5.76-1.56-7.38-3.9.06-2.52 4.92-3.9 7.38-3.9s7.32 1.38 7.38 3.9c-1.62 2.34-4.32 3.9-7.38 3.9z"/></svg></a>
                    <a
                        href="https://wa.me/<?= htmlspecialchars(preg_replace('/\D/', '', WHATSAPP_NUMBER), ENT_QUOTES, 'UTF-8') ?>"
                        class="footer__social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="WhatsApp"
                    ><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>
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
                        <span class="footer__contact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg></span>
                        <a
                            href="tel:<?= htmlspecialchars(preg_replace('/\D/', '', CONTACT_PHONE), ENT_QUOTES, 'UTF-8') ?>"
                            class="footer__link"
                        ><?= htmlspecialchars(CONTACT_PHONE, ENT_QUOTES, 'UTF-8') ?></a>
                    </div>
                    <div class="footer__contact-item">
                        <span class="footer__contact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg></span>
                        <a
                            href="mailto:<?= htmlspecialchars(ADMIN_EMAIL, ENT_QUOTES, 'UTF-8') ?>"
                            class="footer__link"
                        ><?= htmlspecialchars(ADMIN_EMAIL, ENT_QUOTES, 'UTF-8') ?></a>
                    </div>
                    <div class="footer__contact-item">
                        <span class="footer__contact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg></span>
                        <a
                            href="https://t.me/<?= htmlspecialchars(ltrim(TELEGRAM_USERNAME, '@'), ENT_QUOTES, 'UTF-8') ?>"
                            class="footer__link"
                            target="_blank"
                            rel="noopener noreferrer"
                        ><?= htmlspecialchars(TELEGRAM_USERNAME, ENT_QUOTES, 'UTF-8') ?></a>
                    </div>
                    <div class="footer__contact-item">
                        <span class="footer__contact-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg></span>
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