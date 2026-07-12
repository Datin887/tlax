<?php http_response_code(404); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>404 — Страница не найдена</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    <main style="padding: 100px 0; text-align: center;">
        <h1>404 — Страница не найдена</h1>
        <p>Такой страницы не существует</p>
        <a href="/" class="btn btn--primary">На главную</a>
    </main>
</body>
</html>