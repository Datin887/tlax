<?php
/**
 * Конфигурация проекта "Хитовая Песня"
 * Все константы, настройки окружения, параметры приложения
 * 
 * Путь: /includes/config.php
 */

declare(strict_types=1);

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// ======================================================
// ОКРУЖЕНИЕ
// ======================================================

define('APP_ENV', 'development');

if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL);
    ini_set('log_errors', '1');
}

ini_set('error_log', APP_ROOT . '/logs/errors.log');

// ======================================================
// ПРИЛОЖЕНИЕ
// ======================================================

define('APP_NAME', 'Хитовая Песня');
define('APP_SLOGAN', 'Исполнение ваших желаний');
define('APP_DOMAIN', 'tlax.ru');
define('APP_URL', 'https://' . APP_DOMAIN);
define('APP_VERSION', '1.0.0');

define('APP_LANG', 'ru');
define('APP_TIMEZONE', 'Europe/Moscow');
date_default_timezone_set(APP_TIMEZONE);
define('APP_CHARSET', 'UTF-8');
mb_internal_encoding(APP_CHARSET);

// ======================================================
// БАЗА ДАННЫХ
// ======================================================

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'tlax_db');
define('DB_USER', 'tlax_usr');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ======================================================
// СЕССИИ
// ======================================================

define('SESSION_NAME', 'hp_session');
define('SESSION_LIFETIME', 7200);
define('SESSION_ADMIN_LIFETIME', 3600);

ini_set('session.name', SESSION_NAME);
ini_set('session.gc_maxlifetime', (string)SESSION_LIFETIME);
ini_set('session.cookie_lifetime', '0');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

// ======================================================
// ПУТИ К ДИРЕКТОРИЯМ
// ======================================================

define('PATH_PUBLIC', APP_ROOT . '/public');
define('PATH_UPLOADS', APP_ROOT . '/uploads');
define('PATH_UPLOADS_TRACKS', PATH_UPLOADS . '/tracks');
define('PATH_UPLOADS_COVERS', PATH_UPLOADS . '/covers');
define('PATH_LOGS', APP_ROOT . '/logs');
define('PATH_ASSETS', PATH_PUBLIC . '/assets');
define('PATH_AUDIO_PREVIEWS', PATH_PUBLIC . '/assets/audio/previews');
define('PATH_AUDIO_COVERS', PATH_PUBLIC . '/assets/audio/covers');

// ======================================================
// НАСТРОЙКИ ЗАГРУЗКИ
// ======================================================

define('UPLOAD_AUDIO_MAX_SIZE', 20 * 1024 * 1024);
define('UPLOAD_COVER_MAX_SIZE', 5 * 1024 * 1024);
define('UPLOAD_AUDIO_TYPES', ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg']);
define('UPLOAD_COVER_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('UPLOAD_AUDIO_EXT', ['mp3', 'wav', 'ogg']);
define('UPLOAD_COVER_EXT', ['jpg', 'jpeg', 'png', 'webp']);

// ======================================================
// ТАРИФЫ
// ======================================================

define('TARIFFS', [
    'basic' => ['name' => 'Базовый', 'price' => 2500, 'label' => '2 500 ₽'],
    'standard' => ['name' => 'Стандарт', 'price' => 5000, 'label' => '5 000 ₽'],
    'premium' => ['name' => 'Премиум', 'price' => 10000, 'label' => '10 000 ₽'],
    'corporate' => ['name' => 'Корпоративный', 'price' => 15000, 'label' => 'от 15 000 ₽'],
]);

// ======================================================
// КАТЕГОРИИ ПЕСЕН
// ======================================================

define('SONG_CATEGORIES', [
    'wedding' => ['name' => 'Свадьба', 'emoji' => '💒'],
    'birthday' => ['name' => 'День рождения', 'emoji' => '🎉'],
    'anniversary' => ['name' => 'Юбилей', 'emoji' => '🎂'],
    'corporate' => ['name' => 'Корпоратив', 'emoji' => '🏢'],
    'holiday' => ['name' => 'Праздник', 'emoji' => '🎄'],
    'zastolnaya' => ['name' => 'Застольная', 'emoji' => '🍷'],
    'special' => ['name' => 'Особый повод', 'emoji' => '💕'],
    'children' => ['name' => 'Детская', 'emoji' => '🧸'],
]);

// ======================================================
// ПАГИНАЦИЯ
// ======================================================

define('TRACKS_PER_PAGE', 12);
define('ORDERS_PER_PAGE', 20);
define('FEATURED_TRACKS_LIMIT', 6);

// ======================================================
// SEO
// ======================================================

define('SEO_DEFAULT_TITLE', 'Хитовая Песня — персонализированные песни для праздников');
define('SEO_DEFAULT_DESCRIPTION', 'Создаём уникальные песни для свадеб, юбилеев, дней рождения и корпоративов. Оплата после результата. От 2500 ₽. Срок от 1 дня.');

// ======================================================
// КОНТАКТЫ
// ======================================================

define('CONTACT_PHONE', '+7 (999) 999-99-99');
define('CONTACT_PHONE_RAW', '+79999999999');
define('CONTACT_TELEGRAM', '@hitpesnya');
define('CONTACT_EMAIL', 'info@' . APP_DOMAIN);
define('WORK_HOURS', 'Пн–Вс: 9:00 – 22:00 (МСК)');

// ======================================================
// БЕЗОПАСНОСТЬ
// ======================================================

define('RATE_LIMIT_ORDERS', 5);
define('RATE_LIMIT_PERIOD', 3600);
define('RATE_LIMIT_CONTACT', 10);
define('RATE_LIMIT_CONTACT_PERIOD', 3600);
define('FORM_MIN_FILL_TIME', 3);
define('AUTH_MAX_ATTEMPTS', 5);
define('AUTH_BLOCK_TIME', 900);

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        if (!isset($_SESSION['_initiated'])) {
            session_regenerate_id(true);
            $_SESSION['_initiated'] = true;
            $_SESSION['_created_at'] = time();
        }
        if (isset($_SESSION['_created_at'])) {
            $lifetime = str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/')
                ? SESSION_ADMIN_LIFETIME : SESSION_LIFETIME;
            if (time() - $_SESSION['_created_at'] > $lifetime) {
                session_destroy();
                session_start();
                session_regenerate_id(true);
                $_SESSION['_initiated'] = true;
                $_SESSION['_created_at'] = time();
            }
        }
    }
}

start_secure_session();