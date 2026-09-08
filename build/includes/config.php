<?php
declare(strict_types=1);

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

if (!defined('APP_ENV')) {
    define('APP_ENV', 'development');
}

// ─── Load .env (DB only) ───
$envFile = '/var/www/tlax_ru_usr/tlax.env';
if (!is_readable($envFile)) {
    // Фолбэк: если внешний .env не читается (root:root 600), берём локальный env.config.php из корня сайта
    $envFile = dirname(__DIR__) . '/env.config.php';
}
if (!file_exists($envFile) || !is_readable($envFile)) {
    http_response_code(500);
    exit('Configuration file not found.');
}

$envLines = @file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if ($envLines === false) {
    http_response_code(500);
    exit('Configuration file unreadable.');
}
foreach ($envLines as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;
    $parts = explode('=', $line, 2);
    if (count($parts) !== 2) continue;
    $key = trim($parts[0]);
    $value = trim($parts[1]);
    if (strlen($value) >= 2 && $value[0] === '"' && $value[strlen($value) - 1] === '"') {
        $value = substr($value, 1, -1);
    } elseif (strlen($value) >= 2 && $value[0] === "'" && $value[strlen($value) - 1] === "'") {
        $value = substr($value, 1, -1);
    }
    $_ENV[$key] = $value;
    putenv("$key=$value");
}

function env(string $key, mixed $default = null): mixed {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

// ─── Error handling ───
if (env('APP_DEBUG', 'true') === 'true') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}
error_log(APP_ROOT . '/logs/errors.log');

// ─── App constants ───
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

// ─── Database (from .env) ───
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_NAME', env('DB_NAME', 'tlax'));
define('DB_USER', env('DB_USER', 'tlax_usr'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

// ─── Admin ───
define('ADMIN_EMAIL', 'datinvasy@gmail.com');
define('TELEGRAM_ENABLED', false);

// ─── Session ───
define('SESSION_NAME', 'hp_session');
define('SESSION_LIFETIME', 7200);

// ─── Paths ───
define('PATH_UPLOADS', APP_ROOT . '/assets/uploads');
define('UPLOAD_PATH', PATH_UPLOADS);
define('PATH_LOGS', APP_ROOT . '/logs');

// ─── Rate limiting ───
define('RATE_LIMIT_ORDERS', 5);
define('RATE_LIMIT_PERIOD', 3600);
define('FORM_MIN_FILL_TIME', 3);

// ─── Tariffs ───
define('TARIFFS', [
    'basic'    => ['name' => 'Базовый',    'price' => 2500,  'label' => '2 500 ₽'],
    'standard' => ['name' => 'Стандарт',   'price' => 5000,  'label' => '5 000 ₽'],
    'premium'  => ['name' => 'Премиум',    'price' => 10000, 'label' => '10 000 ₽'],
]);

// ─── Song categories ───
define('SONG_CATEGORIES', [
    'wedding'  => ['name' => 'Свадьба',       'emoji' => '💒'],
    'birthday' => ['name' => 'День рождения', 'emoji' => '🎉'],
]);

// ─── Social / Contacts ───
define('VK_PAGE', 'tlax');
define('TELEGRAM_USERNAME', '@tlax');
define('OK_PAGE', 'tlax');
define('WHATSAPP_NUMBER', '+79999961648');

define('CONTACT_PHONE', '+7 (999) 996-16-48');
define('CONTACT_EMAIL', 'datinvasy@gmail.com');
define('CONTACT_TELEGRAM', '@tlax');
define('WORK_HOURS', 'Пн-Вс 09:00-22:00');
define('CONTACT_PHONE_RAW', '79999961648');

// ─── ID3-теги для MP3 (одинаковые для всех треков) ───
// Название трека (TIT2) и описание (COMM) подставляются из формы админки автоматически.
// Остальные теги — общие для всех файлов, меняй здесь.
define('ID3_ARTIST', 'Хитовая Песня');            // Исполнитель (TPE1)
define('ID3_ALBUM', 'Хитовая Песня — песни на заказ'); // Альбом (TALB)
define('ID3_GENRE', 'Песня на заказ');            // Жанр (TCON)
define('ID3_YEAR', '2026');                       // Год (TYER)
define('ID3_DEFAULT_COVER', APP_ROOT . '/assets/uploads/covers/default_cover_300.jpg'); // Одна обложка для всех файлов (JPEG 300x300)
