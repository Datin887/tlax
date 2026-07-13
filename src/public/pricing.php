<?php
/**
 * Страница тарифов
 * Путь: /public/pricing.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
$tariffs = TARIFFS;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Тарифы — Хитовая Песня</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <main>
        <section class="section section--primary" style="padding: 60px 0;">
            <div class="container">
                <h1 style="color:#fff; text-align:center;">Тарифы и услуги</h1>
                <p style="color:#fff; text-align:center; margin-top:20px;">Оплата только после прослушивания готовой песни</p>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="grid grid--3">
                    <?php foreach ($tariffs as $slug => $tariff): ?>
                    <div class="tariff-card">
                        <div class="tariff-card__name"><?= $tariff['name'] ?></div>
                        <div class="tariff-card__price"><?= $tariff['label'] ?></div>
                        <a href="/order.php?t=<?= $slug ?>" class="btn btn--primary">Выбрать</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>