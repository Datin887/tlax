<?php
/**
 * Шапка сайта
 * Путь: /public/includes/header.php
 */
?>
<header class="header">
    <div class="container header__inner">
        <a href="/" class="logo"><?= h(APP_NAME) ?></a>
        
        <nav class="nav">
            <a href="/" class="nav__link <?= active_class('index.php') ?>">Главная</a>
            <a href="/portfolio.php" class="nav__link <?= active_class('portfolio.php') ?>">Портфолио</a>
            <a href="/pricing.php" class="nav__link <?= active_class('pricing.php') ?>">Тарифы</a>
            <a href="/order.php" class="nav__link nav__link--order btn btn--primary">Заказать песню</a>
            <a href="/contacts.php" class="nav__link <?= active_class('contacts.php') ?>">Контакты</a>
        </nav>
        
        <button class="header__menu-btn" aria-label="Меню">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>