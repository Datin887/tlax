<?php
/**
 * Страница 500 — ошибка сервера
 * Путь: /public/500.php
 */

http_response_code(500);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Ошибка сервера | Хитовая Песня</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:sans-serif;background:#F5EFE6;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px;}
        .card{background:#fff;border-radius:20px;padding:48px;text-align:center;max-width:480px;width:100%;box-shadow:0 15px 40px rgba(139,30,63,.15);}
        .code{font-size:72px;font-weight:900;color:#8B1E3F;line-height:1;}
        .title{font-size:24px;font-weight:700;color:#2C1810;margin:16px 0 8px;}
        .desc{color:#6B5D54;font-size:15px;line-height:1.6;margin-bottom:32px;}
        .btn{display:inline-block;padding:14px 28px;background:#8B1E3F;color:#fff;text-decoration:none;border-radius:12px;font-weight:600;margin:4px;}
        .btn-outline{background:transparent;color:#8B1E3F;border:2px solid #8B1E3F;}
    </style>
</head>
<body>
    <div class="card">
        <div class="code">500</div>
        <div style="font-size:48px;margin:12px 0;">⚙️</div>
        <h1 class="title">Ошибка сервера</h1>
        <p class="desc">
            Что-то пошло не так. Мы уже знаем об этой проблеме
            и работаем над её исправлением.
        </p>
        <a href="/" class="btn">На главную</a>
        <a href="/contacts.php" class="btn btn-outline">Написать нам</a>
    </div>
</body>
</html>