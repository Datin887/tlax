<?php
/**
 * Шапка сайта — навигация, логотип
 * Подключается на всех публичных страницах
 * 
 * Путь: /includes/header.php
 */

// Определяем текущую страницу для подсветки активного пункта
$current_page = basename($_SERVER['PHP_SELF'], '.php');

$nav_items = [
    ['href' => '/',            'slug' => 'index',     'label' => 'Главная'],
    ['href' => '/portfolio.php', 'slug' => 'portfolio', 'label' => 'Портфолио'],
    ['href' => '/pricing.php',   'slug' => 'pricing',   'label' => 'Цены'],
    ['href' => '/contacts.php',  'slug' => 'contacts',  'label' => 'Контакты'],
];
?>

<header class="header" role="banner">
    <div class="container">
        <div class="header__inner">

            <!-- Логотип -->
            <a href="/" class="logo" aria-label="Хитовая Песня — на главную">
                <div class="logo__icon" aria-hidden="true">🎵</div>
                <div class="logo__text">
                    <span class="logo__name">Хитовая Песня</span>
                    <span class="logo__slogan">Исполнение ваших желаний</span>
                </div>
            </a>

            <!-- Навигация (десктоп) -->
            <nav class="nav" aria-label="Основная навигация">
                <?php foreach ($nav_items as $item): ?>
                    <a
                        href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                        class="nav__link<?= $current_page === $item['slug'] ? ' active' : '' ?>"
                        <?= $current_page === $item['slug'] ? 'aria-current="page"' : '' ?>
                    >
                        <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <!-- CTA-кнопка -->
            <div class="header__cta">
                <a href="/order.php" class="btn btn--primary btn--sm">
                    Заказать песню
                </a>
            </div>

            <!-- Бургер (мобильный) -->
            <button
                class="burger"
                aria-label="Открыть меню"
                aria-expanded="false"
                aria-controls="mobile-menu"
            >
                <span class="burger__line"></span>
                <span class="burger__line"></span>
                <span class="burger__line"></span>
            </button>

        </div><!-- /.header__inner -->
    </div><!-- /.container -->
</header>

<!-- Мобильное меню -->
<nav class="mobile-menu" id="mobile-menu" aria-label="Мобильная навигация">
    <?php foreach ($nav_items as $item): ?>
        <a
            href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
            class="mobile-menu__link<?= $current_page === $item['slug'] ? ' active' : '' ?>"
            <?= $current_page === $item['slug'] ? 'aria-current="page"' : '' ?>
        >
            <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
        </a>
    <?php endforeach; ?>

    <div class="mobile-menu__cta">
        <a href="/order.php" class="btn btn--primary btn--full btn--lg">
            🎵 Заказать песню
        </a>
    </div>
</nav>