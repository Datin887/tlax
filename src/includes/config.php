<?php
/**
 * Конфигурация сайта "Хитовая Песня"
 * ОБЯЗАТЕЛЬНО заполните все настройки перед запуском!
 *
 * Путь: /includes/config.php
 */

declare(strict_types=1);

// ─── Защита от прямого вызова ───
if (!defined('APP_START')) {
    define('APP_START', microtime(true));
}

// ─── Режим работы ───
define('APP_ENV', getenv('APP_ENV') ?: 'production');

// ─── PHP настройки ───
error_reporting(APP_ENV === 'development' ? E_ALL : 0);
ini_set('display_errors', APP_ENV === 'development' ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/errors.log');

// ─── Кодировка ───
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
date_default_timezone_set('Europe/Moscow');

// ─── Пути ───
define('BASE_PATH',   realpath(__DIR__ . '/..'));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('LOG_PATH',    BASE_PATH . '/logs');
define('UPLOAD_PATH', BASE_PATH . '/uploads');

// Создаём директории если нет
foreach ([LOG_PATH, UPLOAD_PATH . '/tracks', UPLOAD_PATH . '/covers'] as $dir) {
    if (!is_dir($dir)) @mkdir($dir, 0750, true);
}

// ─── Сайт ───
define('SITE_NAME',   'Хитовая Песня');
define('SITE_SLOGAN', 'Исполнение ваших желаний');
define('SITE_URL',    rtrim(getenv('SITE_URL') ?: 'https://tlax.ru', '/'));

// ─── База данных ───
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'tlax_db');
define('DB_USER', getenv('DB_USER') ?: 'tlax_usr');
define('DB_PASS', getenv('DB_PASS') ?: 'CHANGE_ME_DB_PASSWORD');
define('DB_CHARSET', 'utf8mb4');

// ─── Email ───
define('ADMIN_EMAIL',   getenv('ADMIN_EMAIL')   ?: 'admin@tlax.ru');
define('SMTP_HOST',     getenv('SMTP_HOST')     ?: 'smtp.yandex.ru');
define('SMTP_PORT',     getenv('SMTP_PORT')     ?: '465');
define('SMTP_USER',     getenv('SMTP_USER')     ?: '');
define('SMTP_PASS',     getenv('SMTP_PASS')     ?: '');
define('SMTP_SECURE',   getenv('SMTP_SECURE')   ?: 'ssl');

// ─── Telegram ───
define('TELEGRAM_BOT_TOKEN', getenv('TELEGRAM_BOT_TOKEN') ?: '');
define('TELEGRAM_CHAT_ID',   getenv('TELEGRAM_CHAT_ID')   ?: '');
define('TELEGRAM_USERNAME',  getenv('TELEGRAM_USERNAME')  ?: '@hitpesnya');

// ─── Контакты ───
define('CONTACT_PHONE',   getenv('CONTACT_PHONE')   ?: '+7 (999) 999-99-99');
define('WHATSAPP_NUMBER', getenv('WHATSAPP_NUMBER') ?: '+79999999999');
define('VK_PAGE',         getenv('VK_PAGE')         ?: 'hitpesnya');
define('OK_PAGE',         getenv('OK_PAGE')         ?: 'hitpesnya');

// ─── Безопасность ───
define('CSRF_SECRET', getenv('CSRF_SECRET') ?: 'CHANGE_ME_TO_RANDOM_64_CHAR_STRING');