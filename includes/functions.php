<?php
/**
 * Вспомогательные функции (хелперы) проекта "Хитовая Песня"
 * 
 * Путь: /includes/functions.php
 */

declare(strict_types=1);

if (!defined('APP_ROOT')) {
    http_response_code(403);
    exit('Forbidden');
}

// ======================================================
// ЭКРАНИРОВАНИЕ И ВЫВОД
// ======================================================

function e(mixed $value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        APP_CHARSET
    );
}

function ee(mixed $value): void
{
    echo e($value);
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, APP_CHARSET);
}

// ======================================================
// ФОРМАТИРОВАНИЕ
// ======================================================

function format_price(int|float $amount, bool $short = true): string
{
    $formatted = number_format($amount, 0, ',', ' ');
    return $formatted . ($short ? ' ₽' : ' рублей');
}

function format_duration(int $seconds): string
{
    $minutes = (int)($seconds / 60);
    $secs = $seconds % 60;
    return sprintf('%d:%02d', $minutes, $secs);
}

function format_date(string $date, bool $with_time = false): string
{
    $months = [
        1 => 'января', 2 => 'февраля', 3 => 'марта',
        4 => 'апреля', 5 => 'мая', 6 => 'июня',
        7 => 'июля', 8 => 'августа', 9 => 'сентября',
        10 => 'октября', 11 => 'ноября', 12 => 'декабря',
    ];

    try {
        $dt = new DateTime($date);
        $day   = $dt->format('j');
        $month = $months[(int)$dt->format('n')];
        $year  = $dt->format('Y');

        $result = "{$day} {$month} {$year}";

        if ($with_time) {
            $result .= ' в ' . $dt->format('H:i');
        }

        return $result;
    } catch (Exception) {
        return $date;
    }
}

// ======================================================
// ГЕНЕРАЦИЯ НОМЕРА ЗАКАЗА
// ======================================================

function generate_order_number(int $order_id): string
{
    return 'HP-' . str_pad((string)$order_id, 5, '0', STR_PAD_LEFT);
}

// ======================================================
// URL И РЕДИРЕКТЫ
// ======================================================

function redirect(string $url, int $code = 302): never
{
    if (!str_starts_with($url, APP_URL) && !str_starts_with($url, '/')) {
        $url = '/';
    }
    $url = str_replace(["\r", "\n"], '', $url);
    header('Location: ' . $url, true, $code);
    exit();
}

// ======================================================
// РАБОТА С ФАЙЛАМИ
// ======================================================

function ensure_directory(string $path, int $mode = 0755): bool
{
    if (!is_dir($path)) {
        return mkdir($path, $mode, true);
    }
    return true;
}

function generate_filename(string $original_name, string $prefix = ''): string
{
    $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $ext = preg_replace('/[^a-z0-9]/', '', $ext);
    $unique = bin2hex(random_bytes(8));
    $timestamp = date('Ymd');

    return ($prefix ? $prefix . '_' : '') . $timestamp . '_' . $unique . '.' . $ext;
}

// ======================================================
// ЛОГИРОВАНИЕ
// ======================================================

function write_log(string $message, string $level = 'INFO', string $log_file = 'errors.log'): void
{
    $log_path = PATH_LOGS . '/' . $log_file;
    ensure_directory(PATH_LOGS);

    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
    $uri = $_SERVER['REQUEST_URI'] ?? 'CLI';
    $user_agent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 100);

    $line = sprintf(
        "[%s] [%s] [%s] [%s] %s | UA: %s\n",
        $timestamp,
        $level,
        $ip,
        $uri,
        $message,
        $user_agent
    );

    if (file_exists($log_path) && filesize($log_path) > 10 * 1024 * 1024) {
        rename($log_path, $log_path . '.' . date('Ymd_His') . '.bak');
    }

    file_put_contents($log_path, $line, FILE_APPEND | LOCK_EX);
}

function log_error(string $message): void
{
    write_log($message, 'ERROR', 'errors.log');
}

// ======================================================
// ПОЛУЧЕНИЕ IP
// ======================================================

function get_client_ip(): string
{
    $headers = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_REAL_IP',
        'HTTP_X_FORWARDED_FOR',
        'REMOTE_ADDR',
    ];

    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = trim(explode(',', $_SERVER[$header])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }

    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// ======================================================
// ВАЛИДАЦИЯ
// ======================================================

function validate_phone(string $phone): bool
{
    $clean = preg_replace('/[\s\(\)\-\+]/', '', $phone);
    return (bool)preg_match('/^[78]\d{10}$/', $clean);
}

function validate_email(string $email): bool
{
    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
}

// ======================================================
// JSON ОТВЕТ
// ======================================================

function json_response(array $data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

// ======================================================
// САНИТАЙЗ
// ======================================================

function sanitize_string(string $input, int $max_length = 0): string
{
    $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
    $clean = trim($clean ?? '');

    if ($max_length > 0 && mb_strlen($clean) > $max_length) {
        $clean = mb_substr($clean, 0, $max_length);
    }

    return $clean;
}

function post(string $key, mixed $default = '', int $max_length = 0): string
{
    $value = $_POST[$key] ?? $default;
    return sanitize_string((string)$value, $max_length);
}

function get(string $key, mixed $default = '', int $max_length = 0): string
{
    $value = $_GET[$key] ?? $default;
    return sanitize_string((string)$value, $max_length);
}

// ======================================================
// CSRF
// ======================================================

function generate_csrf_token(string $context = 'default'): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $token_key = '_csrf_' . $context;

    if (!isset($_SESSION[$token_key])
        || !isset($_SESSION[$token_key . '_time'])
        || time() - $_SESSION[$token_key . '_time'] > 3600) {
        $_SESSION[$token_key] = bin2hex(random_bytes(32));
        $_SESSION[$token_key . '_time'] = time();
    }

    return $_SESSION[$token_key];
}

function verify_csrf_token(string $token, string $context = 'default'): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $token_key = '_csrf_' . $context;

    if (!isset($_SESSION[$token_key])) {
        return false;
    }

    return hash_equals($_SESSION[$token_key], $token);
}

function csrf_input(string $context = 'default'): string
{
    $token = generate_csrf_token($context);
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, APP_CHARSET) . '">';
}