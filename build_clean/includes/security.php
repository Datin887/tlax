<?php
/**
 * Функции безопасности: CSRF, rate limiting, honeypot, sanitize
 * 
 * Путь: /includes/security.php
 */

declare(strict_types=1);

// Защита от прямого доступа
if (!defined('APP_ROOT')) {
    http_response_code(403);
    exit('Forbidden');
}

// ======================================================
// CSRF ТОКЕНЫ
// ======================================================

/**
 * Генерация CSRF-токена и сохранение в сессию
 * Использует отдельный токен для каждого "контекста" (формы)
 * 
 * @param string $context Контекст формы: 'order', 'contact', 'admin_login'
 * @return string Токен для подстановки в скрытое поле формы
 */
function csrf_generate(string $context = 'default'): string
{
    // Убеждаемся что сессия запущена
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $token_key = '_csrf_' . $context;

    // Генерируем новый токен если нет или истёк (1 час)
    if (
        !isset($_SESSION[$token_key])
        || !isset($_SESSION[$token_key . '_time'])
        || time() - $_SESSION[$token_key . '_time'] > 3600
    ) {
        $_SESSION[$token_key] = bin2hex(random_bytes(32));
        $_SESSION[$token_key . '_time'] = time();
    }

    return $_SESSION[$token_key];
}

/**
 * Проверка CSRF-токена
 * Использует hash_equals() для защиты от timing-атак
 * 
 * @param string $token   Токен из формы
 * @param string $context Контекст формы
 * @return bool
 */
function csrf_verify(string $token, string $context = 'default'): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $token_key = '_csrf_' . $context;

    if (!isset($_SESSION[$token_key])) {
        return false;
    }

    // hash_equals — константное время сравнения (защита от timing attacks)
    return hash_equals($_SESSION[$token_key], $token);
}

/**
 * Вывод скрытого поля CSRF для HTML-формы
 * 
 * @param string $context
 * @return string HTML скрытого поля
 */
function csrf_field(string $context = 'default'): string
{
    $token = csrf_generate($context);
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, APP_CHARSET) . '">';
}

/**
 * Получить CSRF токен для использования в JS (AJAX-запросы)
 * 
 * @param string $context
 * @return string
 */
function csrf_token(string $context = 'default'): string
{
    return csrf_generate($context);
}

// ======================================================
// RATE LIMITING
// ======================================================

/**
 * Проверка rate limit через файловый кэш (без Redis/Memcache)
 * 
 * @param string $key     Уникальный ключ (ip_action)
 * @param int    $limit   Максимум действий
 * @param int    $period  Период в секундах
 * @return bool true = разрешено, false = заблокировано
 */
function rate_limit_check(string $key, int $limit, int $period): bool
{
    $cache_dir = PATH_LOGS . '/rate_cache';
    ensure_directory($cache_dir, 0750);

    // Безопасное имя файла кэша
    $cache_file = $cache_dir . '/' . md5($key) . '.json';

    $now = time();
    $data = [];

    // Читаем существующие данные
    if (file_exists($cache_file)) {
        $content = file_get_contents($cache_file);
        if ($content !== false) {
            $data = json_decode($content, true) ?? [];
        }
    }

    // Очищаем устаревшие записи
    $data = array_filter($data, fn($timestamp) => ($now - $timestamp) < $period);
    $data = array_values($data);

    // Проверяем лимит
    if (count($data) >= $limit) {
        return false; // Заблокировано
    }

    // Добавляем текущий запрос
    $data[] = $now;

    // Сохраняем
    file_put_contents($cache_file, json_encode($data), LOCK_EX);

    return true; // Разрешено
}

/**
 * Rate limit для заявок
 * 
 * @param string $ip
 * @return bool
 */
function rate_limit_orders(string $ip): bool
{
    return rate_limit_check(
        'order_' . $ip,
        RATE_LIMIT_ORDERS,
        RATE_LIMIT_PERIOD
    );
}

/**
 * Rate limit для контактной формы
 * 
 * @param string $ip
 * @return bool
 */
function rate_limit_contact(string $ip): bool
{
    return rate_limit_check(
        'contact_' . $ip,
        RATE_LIMIT_CONTACT,
        RATE_LIMIT_CONTACT_PERIOD
    );
}

/**
 * Сколько секунд осталось до сброса блокировки
 * 
 * @param string $key    Ключ (тот же что в rate_limit_check)
 * @param int    $period Период
 * @return int Секунды до разблокировки (0 = не заблокирован)
 */
function rate_limit_retry_after(string $key, int $period): int
{
    $cache_dir = PATH_LOGS . '/rate_cache';
    $cache_file = $cache_dir . '/' . md5($key) . '.json';

    if (!file_exists($cache_file)) {
        return 0;
    }

    $content = file_get_contents($cache_file);
    if ($content === false) {
        return 0;
    }

    $data = json_decode($content, true) ?? [];
    if (empty($data)) {
        return 0;
    }

    // Самая старая запись + период = когда освободится слот
    $oldest = min($data);
    $retry_at = $oldest + $period;
    return max(0, $retry_at - time());
}

// ======================================================
// HONEYPOT
// ======================================================

/**
 * Вывод honeypot-поля (ловушка для ботов)
 * Боты заполняют все поля → мы проверяем что это поле пустое
 * Поле скрыто через CSS (не через display:none — боты игнорируют)
 * 
 * @param string $field_name Имя поля (должно звучать привлекательно для бота)
 * @return string HTML
 */
function honeypot_field(string $field_name = 'website'): string
{
    // Ловушка: поле должно быть ПУСТЫМ при отправке реальным пользователем
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

/**
 * Проверка honeypot-поля
 * 
 * @param string $field_name
 * @return bool true = похоже на человека (поле пустое), false = бот
 */
function honeypot_check(string $field_name = 'website'): bool
{
    $value = $_POST[$field_name] ?? '';
    return empty($value);
}

// ======================================================
// ПРОВЕРКА ВРЕМЕНИ ЗАПОЛНЕНИЯ ФОРМЫ
// ======================================================

/**
 * Генерация скрытого поля со временем открытия формы
 * 
 * @param string $context
 * @return string HTML
 */
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

/**
 * Проверка времени заполнения формы
 * Если форма заполнена слишком быстро — это бот
 * 
 * @param string $context
 * @param int    $min_seconds Минимальное время заполнения
 * @return bool true = нормально, false = слишком быстро (бот)
 */
function form_time_check(string $context = 'form', int $min_seconds = 3): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $key = '_form_time_' . $context;
    $opened_at = $_SESSION[$key] ?? 0;

    if ($opened_at === 0) {
        // Если время не сохранено — считаем подозрительным
        return false;
    }

    $elapsed = time() - (int)$opened_at;
    return $elapsed >= $min_seconds;
}

// ======================================================
// БЛОКИРОВКА ВХОДА В АДМИНКУ
// ======================================================

/**
 * Проверить, заблокирован ли IP при входе в админку
 * 
 * @param string $ip
 * @return bool true = заблокирован
 */
function auth_is_blocked(string $ip): bool
{
    $cache_dir = PATH_LOGS . '/auth_blocks';
    ensure_directory($cache_dir, 0750);

    $block_file = $cache_dir . '/' . md5($ip) . '.json';

    if (!file_exists($block_file)) {
        return false;
    }

    $content = file_get_contents($block_file);
    if ($content === false) {
        return false;
    }

    $data = json_decode($content, true) ?? [];
    $attempts  = $data['attempts'] ?? 0;
    $blocked_at = $data['blocked_at'] ?? 0;

    // Проверяем: если заблокирован и время блокировки не истекло
    if ($attempts >= AUTH_MAX_ATTEMPTS && $blocked_at > 0) {
        if (time() - $blocked_at < AUTH_BLOCK_TIME) {
            return true; // Заблокирован
        } else {
            // Блокировка истекла — сбрасываем
            auth_reset_attempts($ip);
            return false;
        }
    }

    return false;
}

/**
 * Сколько секунд осталось до разблокировки
 * 
 * @param string $ip
 * @return int
 */
function auth_blocked_seconds(string $ip): int
{
    $cache_dir = PATH_LOGS . '/auth_blocks';
    $block_file = $cache_dir . '/' . md5($ip) . '.json';

    if (!file_exists($block_file)) {
        return 0;
    }

    $data = json_decode(file_get_contents($block_file) ?: '', true) ?? [];
    $blocked_at = $data['blocked_at'] ?? 0;

    if ($blocked_at === 0) {
        return 0;
    }

    return max(0, AUTH_BLOCK_TIME - (time() - $blocked_at));
}

/**
 * Записать неудачную попытку входа
 * 
 * @param string $ip
 */
function auth_record_failed_attempt(string $ip): void
{
    $cache_dir = PATH_LOGS . '/auth_blocks';
    ensure_directory($cache_dir, 0750);

    $block_file = $cache_dir . '/' . md5($ip) . '.json';

    $data = [];
    if (file_exists($block_file)) {
        $data = json_decode(file_get_contents($block_file) ?: '', true) ?? [];
    }

    $attempts = ($data['attempts'] ?? 0) + 1;
    $data['attempts'] = $attempts;
    $data['last_attempt'] = time();
    $data['ip'] = $ip;

    // Если превышен лимит — записываем время блокировки
    if ($attempts >= AUTH_MAX_ATTEMPTS) {
        $data['blocked_at'] = time();

        // Логируем подозрительную активность
        write_log(
            "Блокировка IP {$ip} после {$attempts} неудачных попыток входа в админку",
            'WARNING',
            'access.log'
        );
    }

    file_put_contents($block_file, json_encode($data), LOCK_EX);
}

/**
 * Сбросить счётчик неудачных попыток (после успешного входа)
 * 
 * @param string $ip
 */
function auth_reset_attempts(string $ip): void
{
    $cache_dir = PATH_LOGS . '/auth_blocks';
    $block_file = $cache_dir . '/' . md5($ip) . '.json';

    if (file_exists($block_file)) {
        unlink($block_file);
    }
}

// ======================================================
// SANITIZE INPUT
// ======================================================

/**
 * Очистка всего массива POST/GET от опасных данных
 * Возвращает очищенный массив (не изменяет глобальные)
 * 
 * @param array $data
 * @return array
 */
function sanitize_input(array $data): array
{
    $clean = [];

    foreach ($data as $key => $value) {
        // Ключ — только буквы, цифры, подчёркивание, дефис
        $clean_key = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string)$key);

        if (is_array($value)) {
            $clean[$clean_key] = sanitize_input($value);
        } else {
            $clean[$clean_key] = sanitize_string((string)$value);
        }
    }

    return $clean;
}

/**
 * Получить очищенное значение из POST
 * 
 * @param string $key          Имя поля
 * @param mixed  $default      Значение по умолчанию
 * @param int    $max_length   Максимальная длина (0 = без ограничений)
 * @return string
 */
function post(string $key, mixed $default = '', int $max_length = 0): string
{
    $value = $_POST[$key] ?? $default;
    return sanitize_string((string)$value, $max_length);
}

/**
 * Получить очищенное значение из GET
 * 
 * @param string $key
 * @param mixed  $default
 * @param int    $max_length
 * @return string
 */
function get(string $key, mixed $default = '', int $max_length = 0): string
{
    $value = $_GET[$key] ?? $default;
    return sanitize_string((string)$value, $max_length);
}

/**
 * Получить целочисленное значение из POST/GET
 * 
 * @param string $key
 * @param array  $source $_POST или $_GET
 * @param int    $default
 * @return int
 */
function get_int(string $key, array $source = [], int $default = 0): int
{
    if (empty($source)) {
        $source = array_merge($_GET, $_POST);
    }
    return (int)($source[$key] ?? $default);
}

// ======================================================
// ПРОВЕРКА ЗАПРОСА
// ======================================================

/**
 * Проверить что метод запроса = POST
 * 
 * @return bool
 */
function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Проверить что метод запроса = GET
 * 
 * @return bool
 */
function is_get(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Комплексная проверка POST-запроса с CSRF
 * Удобная функция: проверяет метод + CSRF одновременно
 * 
 * @param string $context CSRF-контекст
 * @return bool
 */
function verify_post_request(string $context = 'default'): bool
{
    if (!is_post()) {
        return false;
    }

    $token = $_POST['csrf_token'] ?? '';
    return csrf_verify($token, $context);
}

// ======================================================
// ЗАГОЛОВКИ БЕЗОПАСНОСТИ (PHP-сторона)
// ======================================================

/**
 * Установить заголовки безопасности через PHP
 * (Дублирует .htaccess на случай если модуль headers недоступен)
 */
function set_security_headers(): void
{
    if (!headers_sent()) {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}

// Автоматически устанавливаем заголовки при подключении файла
set_security_headers();