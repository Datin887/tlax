<?php
/**
 * Админка - вход
 * Путь: /admin/index.php
 */

declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Админка — Хитовая Песня</title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <style>
        .admin-login { max-width: 400px; margin: 100px auto; padding: 40px; background: #fff; border-radius: var(--radius-lg); }
        .admin-login input { width: 100%; padding: 12px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="admin-login">
        <h2>Вход в админку</h2>
        <form method="POST" action="/admin/auth.php">
            <?= csrf_field('admin') ?>
            <input type="text" name="login" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit" class="btn btn--primary">Войти</button>
        </form>
    </div>
</body>
</html>