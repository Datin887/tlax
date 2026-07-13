<?php
/**
 * Многошаговая форма заказа песни
 * Путь: /public/order.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';

$step = $_GET['step'] ?? 1;
$step = max(1, min(8, (int)$step));
$occasion = $_GET['occasion'] ?? '';
$categories = SONG_CATEGORIES;
$tariffs = TARIFFS;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Заказать песню — Хитовая Песня</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <main>
        <section class="section">
            <div class="container">
                <h1>Заказ персональной песны</h1>
                <div class="progress-bar" style="margin: 40px 0;">
                    Шаг <?= $step ?> из 8
                </div>

                <form method="POST" action="/order-process.php" id="order-form" style="max-width: 600px;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="step" value="<?= $step ?>">
                    
                    <?php if ($step === 1): ?>
                    <!-- Шаг 1: Повод -->
                    <div class="form-step">
                        <h3>Для какого повода песню?</h3>
                        <?php foreach ($categories as $slug => $cat): ?>
                        <label style="display:block; margin:10px 0;">
                            <input type="radio" name="occasion" value="<?= $slug ?>" required>
                            <?= $cat['emoji'] ?> <?= $cat['name'] ?>
                        </label>
                        <?php endforeach; ?>
                        <label style="display:block; margin:10px 0;">
                            <input type="radio" name="occasion" value="other">
                            Другой повод
                        </label>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($step >= 2 && $step <= 7): ?>
                    <!-- Заглушка для остальных шагов -->
                    <p>Шаг <?= $step ?> — в разработке</p>
                    <?php endif; ?>
                    
                    <?php if ($step === 8): ?>
                    <!-- Финал -->
                    <div class="form-step">
                        <h3>Готово! Оставьте контакты</h3>
                        <p>Мы свяжемся в течение 5 минут</p>
                        <input type="text" name="name" placeholder="Ваше имя" required style="width:100%; padding:12px; margin:10px 0;">
                        <input type="tel" name="phone" placeholder="Телефон" required style="width:100%; padding:12px; margin:10px 0;">
                    </div>
                    <?php endif; ?>
                    
                    <button type="submit" class="btn btn--primary" style="margin-top:20px;">
                        <?= $step >= 8 ? 'Отправить заявку' : 'Далее' ?>
                    </button>
                </form>
            </div>
        </section>
    </main>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>