<?php
declare(strict_types=1);

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

if (!defined('APP_ENV')) {
    define('APP_ENV', 'development');
}

ini_set('display_errors', '1');
error_reporting(E_ALL);
ini_set('error_log', APP_ROOT . '/logs/errors.log');

define('APP_NAME', 'Хитовая Песня');
define('SITE_NAME', APP_NAME);
define('APP_SLOGAN', 'Исполнение ваших желаний');
define('APP_DOMAIN', 'tlax.ru');
define('APP_URL', 'https://' . APP_DOMAIN);
define('SITE_URL', APP_URL);
define('APP_VERSION', '1.0.0');
define('APP_LANG', 'ru');
define('APP_TIMEZONE', 'Europe/Moscow');
date_default_timezone_set(APP_TIMEZONE);
define('APP_CHARSET', 'UTF-8');
mb_internal_encoding(APP_CHARSET);

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'tlax');
define('DB_USER', 'tlax_usr');
define('DB_PASS', '&-6WP7{JPp]O[6S8');
define('DB_CHARSET', 'utf8mb4');

define('ADMIN_EMAIL', 'admin@tlax.ru');
define('TELEGRAM_ENABLED', false);

define('SESSION_NAME', 'hp_session');
define('SESSION_LIFETIME', 7200);
define('PATH_UPLOADS', APP_ROOT . '/assets/uploads');
define('PATH_LOGS', APP_ROOT . '/logs');
define('RATE_LIMIT_ORDERS', 5);
define('RATE_LIMIT_PERIOD', 3600);
define('FORM_MIN_FILL_TIME', 3);

define('TARIFFS', [
    'basic' => ['name' => 'Базовый', 'price' => 2500, 'label' => '2 500 ₽'],
    'standard' => ['name' => 'Стандарт', 'price' => 5000, 'label' => '5 000 ₽'],
    'premium' => ['name' => 'Премиум', 'price' => 10000, 'label' => '10 000 ₽'],
]);

define('SONG_CATEGORIES', [
    'wedding' => ['name' => 'Свадьба', 'emoji' => '💒'],
    'birthday' => ['name' => 'День рождения', 'emoji' => '🎉'],
]);

define('VK_PAGE', 'tlax');
define('TELEGRAM_USERNAME', '@tlax');
define('OK_PAGE', 'tlax');
define('WHATSAPP_NUMBER', '+79999999999');

// Contact settings
define('CONTACT_PHONE', '+7 (999) 999-99-99');
define('CONTACT_EMAIL', 'admin@tlax.ru');
define('CONTACT_TELEGRAM', '@tlax');
define('WORK_HOURS', 'Пн-Вс 09:00-22:00');

// For orders
define('CONTACT_PHONE_RAW', '79999999999');
