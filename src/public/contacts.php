<?php
/**
 * Страница контактов
 * Путь: /public/contacts.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Контакты — Хитовая Песня</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <main>
        <section class="section section--primary" style="padding: 60px 0;">
            <div class="container">
                <h1 style="color:#fff; text-align:center;">Контакты</h1>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div style="max-width: 600px; margin: 0 auto;">
                    <h3>Свяжитесь с нами</h3>
                    <p><strong>📱 Телефон:</strong> <?= CONTACT_PHONE ?></p>
                    <p><strong>✈️ Telegram:</strong> <a href="https://t.me/<?= ltrim(CONTACT_TELEGRAM, '@') ?>"><?= CONTACT_TELEGRAM ?></a></p>
                    <p><strong>📧 Email:</strong> <a href="mailto:<?= CONTACT_EMAIL ?>"><?= CONTACT_EMAIL ?></a></p>
                    <p><strong>🕒 Часы работы:</strong> <?= WORK_HOURS ?></p>
                    
                    <form method="POST" action="/contact-process.php" style="margin-top: 40px;">
                        <?= csrf_field() ?>
                        <input type="text" name="name" placeholder="Ваше имя" required style="width:100%; padding:12px; margin:10px 0;">
                        <input type="email" name="email" placeholder="Email" required style="width:100%; padding:12px; margin:10px 0;">
                        <textarea name="message" placeholder="Сообщение" required style="width:100%; padding:12px; margin:10px 0; min-height:120px;"></textarea>
                        <button type="submit" class="btn btn--primary">Отправить</button>
                    </form>
                </div>
            </div>
        </section>
    </main>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>