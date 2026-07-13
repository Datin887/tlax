<?php
/**
 * Функции безопасности: CSRF, sanitize, валидация
 * Путь: /includes/security.php
 */

declare(strict_types=1);

// Запускаем сессию если не запущена
if (session_status() === PHP_SESSION_NONE && !defined('IN_ADMIN')) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

function generate_csrf_token(): string
{
    if (empty($_SESSION['csrf_tokens'])) {
        $_SESSION['csrf_tokens'] = [];
    }

    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_tokens'][$token] = time();
    if (count($_SESSION['csrf_tokens']) > 10) {
        asort($_SESSION['csrf_tokens']);
        array_shift($_SESSION['csrf_tokens']);
    }

    return $token;
}

function verify_csrf_token(string $token, int $max_age = 3600): bool
{
    if (empty($token) || empty($_SESSION['csrf_tokens'])) {
        return false;
    }

    if (!isset($_SESSION['csrf_tokens'][$token])) {
        return false;
    }

    $created = $_SESSION['csrf_tokens'][$token];

    if (time() - $created > $max_age) {
        unset($_SESSION['csrf_tokens'][$token]);
        return false;
    }

    unset($_SESSION['csrf_tokens'][$token]);
    return true;
}