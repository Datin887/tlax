<?php
/**
 * Вспомогательные функции
 * Путь: /includes/functions.php
 */

declare(strict_types=1);

if (!defined('APP_ROOT')) {
    http_response_code(403);
    exit('Forbidden');
}

// Экранирование
function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, APP_CHARSET);
}

function ee(mixed $value): void { echo e($value); }

// Форматирование цены
function format_price(int|float $amount): string
{
    return number_format($amount, 0, ',', ' ') . ' ₽';
}

// Форматирование даты
function format_date(string $date): string
{
    $months = [1=>'янв','2=>'фев','3=>'мар','4=>'апр','5=>'май','6=>'июн','7=>'июл','8=>'авг','9=>'сен','10=>'окт','11=>'ноя','12=>'дек'];
    try {
        $dt = new DateTime($date);
        return (int)$dt->format('j') . ' ' . $months[(int)$dt->format('n')] . ' ' . $dt->format('Y');
    } catch (Exception) {
        return $date;
    }
}

// Активный класс
function active_class(string $page): string
{
    $current = basename($_SERVER['PHP_SELF'] ?? '');
    return $current === $page ? 'active' : '';
}

// Редирект
function redirect(string $url, int $code = 302): never
{
    header('Location: ' . $url, true, $code);
    exit();
}

// Лог
function write_log(string $message, string $level = 'INFO'): void
{
    $log_dir = PATH_LOGS;
    if (!is_dir($log_dir)) mkdir($log_dir, 0750, true);
    $line = sprintf("[%s] [%s] %s\n", date('Y-m-d H:i:s'), $level, $message);
    file_put_contents($log_dir . '/access.log', $line, FILE_APPEND | LOCK_EX);
}