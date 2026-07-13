<?php
/**
 * Шапка и sidebar админки
 * Путь: /admin/includes/admin-header.php
 */

$current_admin = get_current_admin();
$admin_name    = $current_admin['name'] ?? $current_admin['username'] ?? 'Администратор';
$current_file  = basename($_SERVER['PHP_SELF'], '.php');
$admin_csrf    = generate_csrf_token();

$nav_items = [
    ['file' => 'index',      'icon' => '📊', 'label' => 'Дашборд',  'href' => '/admin/'],
    ['file' => 'orders',     'icon' => '📋', 'label' => 'Заявки',   'href' => '/admin/orders.php'],
    ['file' => 'tracks',     'icon' => '🎵', 'label' => 'Треки',    'href' => '/admin/tracks.php'],
    ['file' => 'stats',      'icon' => '📈', 'label' => 'Статистика','href' => '/admin/stats.php'],
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($page_title ?? 'Админка') ?> — Хитовая Песня</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/assets/css/variables.css">
    <link rel="stylesheet" href="/assets/css/reset.css">
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/admin/assets/admin.css">
    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="<?= h($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="admin-body">

<!-- ─── Sidebar ─── -->
<aside class="admin-sidebar" id="admin-sidebar" role="navigation" aria-label="Навигация админки">

    <div class="admin-sidebar__logo">
        <div class="logo__icon" aria-hidden="true">🎵</div>
        <div>
            <div class="admin-sidebar__logo-name">Хитовая Песня</div>
            <div class="admin-sidebar__logo-sub">Панель управления</div>
        </div>
    </div>

    <nav class="admin-nav">
        <?php foreach ($nav_items as $item): ?>
            <a
                href="<?= h($item['href']) ?>"
                class="admin-nav__item<?= $current_file === $item['file'] || ($item['file'] === 'index' && $current_file === 'index') ? ' active' : '' ?>"
            >
                <span class="admin-nav__icon" aria-hidden="true"><?= $item['icon'] ?></span>
                <span class="admin-nav__label"><?= h($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="admin-sidebar__bottom">
        <a href="/" class="admin-nav__item" target="_blank" rel="noopener">
            <span class="admin-nav__icon" aria-hidden="true">🌐</span>
            <span class="admin-nav__label">На сайт</span>
        </a>
        <form method="POST" action="/admin/logout.php">
            <input type="hidden" name="csrf_token" value="<?= h($admin_csrf) ?>">
            <button type="submit" class="admin-nav__item admin-nav__item--btn">
                <span class="admin-nav__icon" aria-hidden="true">🚪</span>
                <span class="admin-nav__label">Выйти</span>
            </button>
        </form>
    </div>

</aside>

<!-- ─── Основной контент ─── -->
<div class="admin-main">

    <!-- Topbar -->
    <header class="admin-topbar">
        <button class="admin-burger" id="admin-burger" aria-label="Меню">
            <span></span><span></span><span></span>
        </button>

        <h1 class="admin-topbar__title"><?= h($page_title ?? 'Админка') ?></h1>

        <div class="admin-topbar__right">
            <span class="admin-topbar__user">
                👤 <?= h($admin_name) ?>
            </span>
            <a href="/" target="_blank" class="btn btn--outline btn--sm">🌐 Сайт</a>
        </div>
    </header>

    <!-- Контент -->
    <div class="admin-content">