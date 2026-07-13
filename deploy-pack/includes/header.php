<?php
/**
 * Хедер сайта
 * Путь: /includes/header.php
 */
?>
<header class="header">
    <div class="container header__container">
        <a href="/" class="logo"><?= e(APP_NAME) ?></a>
        <nav class="nav">
            <a href="/" class="<?= active_class('index.php') ?>">Главная</a>
            <a href="/portfolio.php" class="<?= active_class('portfolio.php') ?>">Портфолио</a>
            <a href="/pricing.php" class="<?= active_class('pricing.php') ?>">Тарифы</a>
            <a href="/contacts.php" class="<?= active_class('contacts.php') ?>">Контакты</a>
        </nav>
    </div>
</header>