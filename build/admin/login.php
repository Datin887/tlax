<?php
/**
 * Страница входа в админку
 *
 * Путь: /admin/login.php
 */

declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/includes/auth.php';

// Уже авторизован — редирект
if (is_authenticated()) {
    header('Location: /admin/');
    exit;
}

$error   = '';
$success = '';
$ip      = get_client_ip();

// ─── Обработка формы входа ───
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Ошибка безопасности. Обновите страницу.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Rate limiting: 5 попыток за 15 минут с одного IP
        $rate_key = 'admin_login_' . $ip;
        if (!check_rate_limit($rate_key, 5, 900)) {
            $error = 'Слишком много неудачных попыток. Подождите 15 минут.';
            log_error("admin/login: блокировка IP {$ip}");
        } else {
            try {
                $db = Database::getInstance();

                $admin = $db->fetchOne(
                    "SELECT id, username, password_hash, is_active, display_name
                     FROM admins
                     WHERE username = :u1 OR email = :u2
                     LIMIT 1",
                    [':u1' => $username, ':u2' => $username]
                );

                if ($admin && $admin['is_active'] && password_verify($password, $admin['password_hash'])) {

                    // Успешный вход
                    $token = bin2hex(random_bytes(32));
                    login_admin((int)$admin['id'], $token);

                    // Логируем успешный вход
                    $db->execute(
                        "INSERT INTO admin_sessions (admin_id, session_id, ip_address, user_agent, created_at, expires_at, is_active)
                         VALUES (:admin_id, :session_id, :ip, :ua, NOW(), DATE_ADD(NOW(), INTERVAL 2 HOUR), 1)",
                        [
                            ':admin_id' => $admin['id'],
                            ':session_id' => hash('sha256', $token),
                            ':ip'       => $ip,
                            ':ua'       => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
                        ]
                    );

                    // Нужна смена пароля (если пароль слабый)?
                    if (password_needs_rehash($admin['password_hash'], PASSWORD_DEFAULT)) {
                        $new_hash = password_hash($password, PASSWORD_DEFAULT);
                        $db->execute(
                            "UPDATE admins SET password_hash = :hash WHERE id = :id",
                            [':hash' => $new_hash, ':id' => $admin['id']]
                        );
                    }

                    $redirect = '/admin/';
                    $redirect_param = $_GET['redirect'] ?? '';
                    if ($redirect_param && str_starts_with($redirect_param, '/admin/')) {
                        $redirect = $redirect_param;
                    }

                    header('Location: ' . $redirect);
                    exit;

                } else {
                    // Неверные данные
                    $error = 'Неверный логин или пароль';
                    log_error("admin/login: неудача для '{$username}', IP={$ip}");
                }

            } catch (Exception $e) {
                log_error('admin/login: ' . $e->getMessage());
                $error = 'Произошла ошибка. Попробуйте позже.';
            }
        }
    }
}

$csrf_token = generate_csrf_token();
$reason = $_GET['reason'] ?? '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админку — Хитовая Песня</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/assets/css/variables.css">
    <link rel="stylesheet" href="/assets/css/reset.css">
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body class="admin-login-page">

<div class="login-wrap">

    <div class="login-card">

        <!-- Логотип -->
        <div class="login-card__logo">
            <div class="logo__icon" aria-hidden="true">🎵</div>
            <div>
                <div class="logo__name" style="font-size:18px;">Хитовая Песня</div>
                <div class="logo__slogan">Панель управления</div>
            </div>
        </div>

        <h1 class="login-card__title">Вход</h1>

        <?php if ($reason === 'timeout'): ?>
            <div class="alert alert--warning">⚠️ Сессия истекла. Войдите снова.</div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert--error" role="alert">❌ <?= h($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/admin/login.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">

            <div class="form-group">
                <label class="form-label" for="username">Логин или Email</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-input"
                    value="<?= h($_POST['username'] ?? '') ?>"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="admin"
                >
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Пароль</label>
                <div class="password-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    >
                    <button
                        type="button"
                        class="password-toggle"
                        aria-label="Показать/скрыть пароль"
                        onclick="togglePassword()"
                    >👁</button>
                </div>
            </div>

            <button type="submit" class="btn btn--primary btn--full btn--lg" style="margin-top: var(--space-md);">
                Войти
            </button>
        </form>

        <div class="login-card__back">
            <a href="/" class="btn btn--ghost btn--sm">← На сайт</a>
        </div>

    </div>

</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>