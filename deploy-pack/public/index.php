<?php
/**
 * Главная страница "Хитовая Песня"
 * Путь: /public/index.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$page_meta = [
    'title' => 'Хитовая Песня — персонализированные песни для праздников',
    'description' => SEO_DEFAULT_DESCRIPTION,
];

$categories = SONG_CATEGORIES;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_meta['title']) ?></title>
    <meta name="description" content="<?= e($page_meta['description']) ?>">
    <link rel="stylesheet" href="/assets/css/variables.css">
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="icon" href="/assets/img/favicon.ico" type="image/x-icon">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>
        <!-- HERO -->
        <section class="hero">
            <div class="container">
                <h1>Персональные песни<br><span style="color:var(--color-primary)">для ваших праздников</span></h1>
                <p class="hero__subtitle">Свадьба, день рождения, юбилей — мы создадим уникальную песню, которая станет подарком на всю жизнь</p>
                <div class="hero__actions">
                    <a href="/order.php" class="btn btn--primary btn--lg">Заказать песню</a>
                    <a href="/portfolio.php" class="btn btn--secondary btn--lg">Портфолио</a>
                </div>
            </div>
        </section>

        <!-- КАТЕГОРИИ -->
        <section class="section">
            <div class="container">
                <h2 class="section-title">Выберите повод</h2>
                <div class="grid grid--4">
                    <?php foreach ($categories as $slug => $cat): ?>
                    <a href="/order.php?occasion=<?= $slug ?>" class="category-card">
                        <div class="category-card__emoji"><?= $cat['emoji'] ?></div>
                        <div class="category-card__name"><?= $cat['name'] ?></div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- ТАРИФЫ -->
        <section class="section section--primary">
            <div class="container">
                <h2 class="section-title" style="color:#fff">Тарифы</h2>
                <div class="grid grid--3">
                    <?php foreach (TARIFFS as $slug => $tariff): ?>
                    <div class="tariff-card">
                        <div class="tariff-card__name"><?= $tariff['name'] ?></div>
                        <div class="tariff-card__price"><?= $tariff['label'] ?></div>
                        <a href="/order.php?t=<?= $slug ?>" class="btn btn--white">Выбрать</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script src="/assets/js/main.js"></script>
</body>
</html>