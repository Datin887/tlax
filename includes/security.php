<?php
/**
 * Функции безопасности: CSRF, rate limiting, honeypot
 * 
 * Путь: /includes/security.php
 */

declare(strict_types=1);

if (!defined('APP_ROOT')) {
    http_response_code(403);
    exit('Forbidden');
}

// ======================================================
// RATE LIMITING
// ======================================================

function rate_limit_check(string $key, int $limit, int $period): bool
{
    $cache_dir = PATH_LOGS . '/rate_cache';
    ensure_directory($cache_dir, 0750);

    $cache_file = $cache_dir . '/' . md5($key) . '.json';

    $now = time();
    $data = [];

    if (file_exists($cache_file)) {
        $content = file_get_contents($cache_file);
        if ($content !== false) {
            $data = json_decode($content, true) ?? [];
        }
    }

    $data = array_filter($data, fn($timestamp) => ($now - $timestamp) < $period);
    $data = array_values($data);

    if (count($data) >= $limit) {
        return false;
    }

    $data[] = $now;
    file_put_contents($cache_file, json_encode($data), LOCK_EX);

    return true;
}

function rate_limit_orders(string $ip): bool
{
    return rate_limit_check('order_' . $ip, RATE_LIMIT_ORDERS, RATE_LIMIT_PERIOD);
}

function rate_limit_contact(string $ip): bool
{
    return rate_limit_check('contact_' . $ip, RATE_LIMIT_CONTACT, RATE_LIMIT_CONTACT_PERIOD);
}

// ======================================================
// HONEYPOT
// ======================================================

function honeypot_field(string $field_name = 'website'): string
{
    return sprintf(
        '<div class="hp-trap" aria-hidden="true" tabindex="-1" style="position:absolute;left:-9999px;top:-9999px;opacity:0;height:0;overflow:hidden;">
            <label for="%s">Не заполняйте это поле</label>
            <input type="text" id="%s" name="%s" value="" autocomplete="off" tabindex="-1">
        </div>',
        e($field_name),
        e($field_name),
        e($field_name)
    );
}

function honeypot_check(string $field_name = 'website'): bool
{
    $value = $_POST[$field_name] ?? '';
    return empty($value);
}

// ======================================================
// ФОРМА ТАЙМ
// ======================================================

function form_time_field(string $context = 'form'): string
{
    $key = '_form_time_' . $context;

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION[$key] = time();

    return sprintf(
        '<input type="hidden" name="form_opened_at" value="%d">',
        $_SESSION[$key]
    );
}

function form_time_check(string $context = 'form', int $min_seconds = 3): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $key = '_form_time_' . $context;
    $opened_at = $_SESSION[$key] ?? 0;

    if ($opened_at === 0) {
        return false;
    }

    $elapsed = time() - (int)$opened_at;
    return $elapsed >= $min_seconds;
}

// ======================================================
// ЗАГОЛОВКИ БЕЗОПАСНОСТИ
// ======================================================

function set_security_headers(): void
{
    if (!headers_sent()) {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}

set_security_headers();

// ======================================================
// ПРОВЕРКА МЕТОДА
// ======================================================

function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function verify_post_request(string $context = 'default'): bool
{
    if (!is_post()) {
        return false;
    }

    $token = $_POST['csrf_token'] ?? '';
    return verify_csrf_token($token, $context);
}