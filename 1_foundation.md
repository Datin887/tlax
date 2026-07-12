# Изучил ТЗ полностью ✅

Отличный проект — продуманная архитектура, чёткие требования. Разбираюсь в структуре:

**Что строим:** Сайт студии персонализированных песен с портфолио, многошаговой анкетой и полноценной админкой.

**Стек:** PHP 8.1+ нативный, MySQL, PDO, Vanilla JS/CSS — без фреймворков.

**Объём:** 8 этапов, ~40+ файлов.

---

Начинаю с **ЭТАПА 1 — Фундамент**.

---

## Файл 1: `.htaccess`
**Путь:** `/public/.htaccess`

```apache
# ======================================================
# .htaccess — Хитовая Песня
# Редиректы, безопасность, кэш, gzip, URL-правила
# Путь: /public/.htaccess
# ======================================================

# --- Кодировка ---
AddDefaultCharset UTF-8

# --- Включаем mod_rewrite ---
Options -Indexes
Options -MultiViews

RewriteEngine On
RewriteBase /

# --- HTTPS принудительно ---
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# --- www → без www ---
RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
RewriteRule ^ https://%1%{REQUEST_URI} [L,R=301]

# --- Убираем .php из URL (опционально) ---
# RewriteCond %{REQUEST_FILENAME} !-d
# RewriteCond %{REQUEST_FILENAME} !-f
# RewriteCond %{REQUEST_FILENAME}.php -f
# RewriteRule ^([^.]+)$ $1.php [L]

# --- Защита системных директорий ---
RewriteRule ^includes/ - [F,L]
RewriteRule ^logs/ - [F,L]
RewriteRule ^database/ - [F,L]
RewriteRule ^uploads/tracks/ - [F,L]

# --- Блокируем прямой доступ к конфигам ---
<FilesMatch "\.(sql|log|md|sh|env|ini|conf)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# --- Защита .htaccess ---
<Files ".htaccess">
    Order Allow,Deny
    Deny from all
</Files>

# ======================================================
# ЗАГОЛОВКИ БЕЗОПАСНОСТИ
# ======================================================

<IfModule mod_headers.c>
    # Запрет встраивания в iframe с других сайтов
    Header always set X-Frame-Options "SAMEORIGIN"

    # Запрет MIME-сниффинга
    Header always set X-Content-Type-Options "nosniff"

    # XSS-защита для старых браузеров
    Header always set X-XSS-Protection "1; mode=block"

    # Политика реферера
    Header always set Referrer-Policy "strict-origin-when-cross-origin"

    # Content Security Policy
    Header always set Content-Security-Policy "default-src 'self'; script-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net 'unsafe-inline'; style-src 'self' https://fonts.googleapis.com 'unsafe-inline'; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; media-src 'self'; connect-src 'self'; frame-ancestors 'self';"

    # Permissions Policy
    Header always set Permissions-Policy "geolocation=(), microphone=(), camera=()"

    # Скрываем версию Apache
    Header always unset X-Powered-By
    Header always unset Server

    # HSTS (только если HTTPS настроен!)
    # Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
</IfModule>

# ======================================================
# СЖАТИЕ GZIP
# ======================================================

<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE text/javascript
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/json
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE image/svg+xml
    AddOutputFilterByType DEFLATE text/plain

    # Не сжимать уже сжатые форматы
    SetEnvIfNoCase Request_URI \.(?:gif|jpe?g|png|webp|mp3|mp4|ogg|zip|gz)$ no-gzip dont-vary
</IfModule>

# ======================================================
# КЭШИРОВАНИЕ БРАУЗЕРОМ
# ======================================================

<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresDefault "access plus 1 month"

    # HTML — не кэшируем (динамика)
    ExpiresByType text/html "access plus 0 seconds"

    # CSS и JS — кэш 1 неделя
    ExpiresByType text/css "access plus 1 week"
    ExpiresByType application/javascript "access plus 1 week"

    # Шрифты — кэш 1 год
    ExpiresByType font/woff2 "access plus 1 year"
    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType font/ttf "access plus 1 year"

    # Изображения — кэш 1 месяц
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/webp "access plus 1 month"
    ExpiresByType image/svg+xml "access plus 1 month"
    ExpiresByType image/x-icon "access plus 1 year"

    # Аудио — кэш 1 месяц
    ExpiresByType audio/mpeg "access plus 1 month"
    ExpiresByType audio/ogg "access plus 1 month"
</IfModule>

<IfModule mod_headers.c>
    # Добавляем vary для корректного кэша
    <FilesMatch "\.(css|js)$">
        Header append Vary Accept-Encoding
    </FilesMatch>
</IfModule>

# ======================================================
# КАСТОМНЫЕ СТРАНИЦЫ ОШИБОК
# ======================================================

ErrorDocument 400 /400.php
ErrorDocument 403 /403.php
ErrorDocument 404 /404.php
ErrorDocument 500 /500.php

# ======================================================
# MIME-ТИПЫ
# ======================================================

<IfModule mod_mime.c>
    AddType audio/mpeg .mp3
    AddType audio/ogg .ogg
    AddType audio/wav .wav
    AddType image/webp .webp
    AddType font/woff2 .woff2
    AddType font/woff .woff
    AddType image/svg+xml .svg
    AddType application/json .json
</IfModule>

# ======================================================
# СКРЫВАЕМ ВЕРСИЮ PHP
# ======================================================

<IfModule mod_php.c>
    php_flag expose_php Off
</IfModule>

# --- Запрет выполнения скриптов в uploads ---
<IfModule mod_rewrite.c>
    RewriteRule ^uploads/.*\.(php|php3|php4|php5|phtml|pl|py|cgi|sh)$ - [F,L]
</IfModule>
```

---

## Файл 2: `includes/config.php`
**Путь:** `/includes/config.php`

```php
<?php
/**
 * Конфигурация проекта "Хитовая Песня"
 * Все константы, настройки окружения, параметры приложения
 * 
 * Путь: /includes/config.php
 * 
 * ВАЖНО: Этот файл НЕ должен быть доступен из браузера напрямую.
 * Папка /includes/ защищена через .htaccess
 */

declare(strict_types=1);

// --- Защита от прямого доступа ---
if (!defined('APP_ROOT')) {
    // Определяем корень приложения (папка выше /public/)
    define('APP_ROOT', dirname(__DIR__));
}

// ======================================================
// ОКРУЖЕНИЕ
// ======================================================

// Режим: 'development' или 'production'
define('APP_ENV', 'development');

// Отображать ли ошибки (только для разработки!)
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

// Лог ошибок PHP
ini_set('error_log', APP_ROOT . '/logs/errors.log');

// ======================================================
// ПРИЛОЖЕНИЕ
// ======================================================

define('APP_NAME', 'Хитовая Песня');
define('APP_SLOGAN', 'Исполнение ваших желаний');
define('APP_DOMAIN', 'hit.owlex.top');
define('APP_URL', 'https://' . APP_DOMAIN);
define('APP_VERSION', '1.0.0');

// Язык и таймзона
define('APP_LANG', 'ru');
define('APP_TIMEZONE', 'Europe/Moscow');
date_default_timezone_set(APP_TIMEZONE);

// Кодировка
define('APP_CHARSET', 'UTF-8');
mb_internal_encoding(APP_CHARSET);

// ======================================================
// БАЗА ДАННЫХ
// ======================================================

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'hitpesnya');
define('DB_USER', 'hitpesnya_user');
define('DB_PASS', 'СЮДА_ПАРОЛЬ_БД');       // ← Заменить!
define('DB_CHARSET', 'utf8mb4');

// ======================================================
// EMAIL (SMTP)
// ======================================================

define('SMTP_HOST', 'smtp.gmail.com');        // ← Заменить
define('SMTP_PORT', 587);
define('SMTP_USER', 'your@gmail.com');         // ← Заменить
define('SMTP_PASS', 'app_password_here');      // ← Заменить
define('SMTP_FROM_NAME', APP_NAME);
define('SMTP_FROM_EMAIL', 'noreply@' . APP_DOMAIN);
define('SMTP_ENCRYPTION', 'tls');             // tls или ssl

// Email куда приходят заявки
define('ADMIN_EMAIL', 'admin@' . APP_DOMAIN); // ← Заменить

// ======================================================
// TELEGRAM BOT
// ======================================================

define('TELEGRAM_BOT_TOKEN', 'YOUR_BOT_TOKEN_HERE');  // ← Заменить
define('TELEGRAM_CHAT_ID', 'YOUR_CHAT_ID_HERE');       // ← Заменить
define('TELEGRAM_ENABLED', false); // true после настройки бота

// ======================================================
// СЕССИИ
// ======================================================

define('SESSION_NAME', 'hp_session');
define('SESSION_LIFETIME', 7200);     // 2 часа в секундах
define('SESSION_ADMIN_LIFETIME', 3600); // 1 час для админки

// Настройки cookie сессии
ini_set('session.name', SESSION_NAME);
ini_set('session.gc_maxlifetime', (string)SESSION_LIFETIME);
ini_set('session.cookie_lifetime', '0');    // До закрытия браузера
ini_set('session.cookie_httponly', '1');    // Недоступно JS
ini_set('session.cookie_secure', '0');      // 1 только на HTTPS!
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

// ======================================================
// БЕЗОПАСНОСТЬ
// ======================================================

// Rate limiting: максимум заявок с одного IP за период
define('RATE_LIMIT_ORDERS', 5);              // Заявок
define('RATE_LIMIT_PERIOD', 3600);           // За 1 час (секунды)
define('RATE_LIMIT_CONTACT', 10);           // Сообщений с контакт-формы
define('RATE_LIMIT_CONTACT_PERIOD', 3600);

// Минимальное время заполнения формы (защита от ботов)
define('FORM_MIN_FILL_TIME', 3);            // Секунды

// Блокировка после N неудачных попыток входа в админку
define('AUTH_MAX_ATTEMPTS', 5);
define('AUTH_BLOCK_TIME', 900);             // 15 минут

// Соль для дополнительного хэширования (не для паролей!)
define('APP_SECRET', 'hitpesnya_secret_key_2024_change_me'); // ← Заменить!

// ======================================================
// ФАЙЛЫ И ЗАГРУЗКИ
// ======================================================

// Пути к директориям (абсолютные)
define('PATH_PUBLIC', APP_ROOT . '/public');
define('PATH_UPLOADS', APP_ROOT . '/uploads');
define('PATH_UPLOADS_TRACKS', PATH_UPLOADS . '/tracks');
define('PATH_UPLOADS_COVERS', PATH_UPLOADS . '/covers');
define('PATH_LOGS', APP_ROOT . '/logs');
define('PATH_ASSETS', PATH_PUBLIC . '/assets');
define('PATH_AUDIO_PREVIEWS', PATH_PUBLIC . '/assets/audio/previews');
define('PATH_AUDIO_COVERS', PATH_PUBLIC . '/assets/audio/covers');

// URL пути к загрузкам (относительные от корня сайта)
define('URL_UPLOADS', APP_URL . '/uploads');
define('URL_ASSETS', APP_URL . '/assets');

// Настройки загрузки аудио
define('UPLOAD_AUDIO_MAX_SIZE', 20 * 1024 * 1024);   // 20 МБ в байтах
define('UPLOAD_COVER_MAX_SIZE', 5 * 1024 * 1024);    // 5 МБ
define('UPLOAD_AUDIO_TYPES', ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg']);
define('UPLOAD_COVER_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('UPLOAD_AUDIO_EXT', ['mp3', 'wav', 'ogg']);
define('UPLOAD_COVER_EXT', ['jpg', 'jpeg', 'png', 'webp']);

// ======================================================
// ТАРИФЫ
// ======================================================

define('TARIFFS', [
    'basic' => [
        'name'  => 'Базовый',
        'price' => 2500,
        'label' => '2 500 ₽',
    ],
    'standard' => [
        'name'  => 'Стандарт',
        'price' => 5000,
        'label' => '5 000 ₽',
    ],
    'premium' => [
        'name'  => 'Премиум',
        'price' => 10000,
        'label' => '10 000 ₽',
    ],
    'corporate' => [
        'name'  => 'Корпоративный',
        'price' => 15000,
        'label' => 'от 15 000 ₽',
    ],
]);

// Коэффициенты срочности
define('URGENCY_COEFFICIENTS', [
    'normal'     => 1.0,   // 5+ дней
    'fast'       => 1.0,   // 3-4 дня (без доплаты)
    'urgent'     => 1.5,   // 1-2 дня (+50%)
    'very_urgent' => 2.0,  // Сегодня-завтра (+100%)
]);

// ======================================================
// КАТЕГОРИИ ПЕСЕН
// ======================================================

define('SONG_CATEGORIES', [
    'wedding'       => ['name' => 'Свадьба',           'emoji' => '💒'],
    'birthday'      => ['name' => 'День рождения',     'emoji' => '🎉'],
    'anniversary'   => ['name' => 'Юбилей',            'emoji' => '🎂'],
    'corporate'     => ['name' => 'Корпоратив',        'emoji' => '🏢'],
    'holiday'       => ['name' => 'Праздник',          'emoji' => '🎄'],
    'застольная'    => ['name' => 'Застольная',        'emoji' => '🍷'],
    'special'       => ['name' => 'Особый повод',      'emoji' => '💕'],
    'children'      => ['name' => 'Детская',           'emoji' => '🧸'],
]);

// ======================================================
// ПАГИНАЦИЯ
// ======================================================

define('TRACKS_PER_PAGE', 12);
define('ORDERS_PER_PAGE', 20);
define('FEATURED_TRACKS_LIMIT', 6);

// ======================================================
// SEO ПО УМОЛЧАНИЮ
// ======================================================

define('SEO_DEFAULT_TITLE', 'Хитовая Песня — персонализированные песни для праздников');
define('SEO_DEFAULT_DESCRIPTION', 'Создаём уникальные песни для свадеб, юбилеев, дней рождения и корпоративов. Оплата после результата. От 2500 ₽. Срок от 1 дня.');
define('SEO_DEFAULT_KEYWORDS', 'заказать песню, персональная песня, песня на заказ, песня на свадьбу, песня на день рождения, песня на юбилей');
define('SEO_DEFAULT_OG_IMAGE', APP_URL . '/assets/img/og-image.jpg');

// ======================================================
// КОНТАКТЫ (отображаются на сайте)
// ======================================================

define('CONTACT_PHONE', '+7 (999) 999-99-99');       // ← Заменить
define('CONTACT_PHONE_RAW', '+79999999999');           // ← Заменить
define('CONTACT_TELEGRAM', '@hitpesnya');
define('CONTACT_WHATSAPP', '+79999999999');            // ← Заменить
define('CONTACT_VK', 'https://vk.com/hitpesnya');
define('CONTACT_OK', 'https://ok.ru/hitpesnya');
define('CONTACT_EMAIL', 'info@' . APP_DOMAIN);

define('WORK_HOURS', 'Пн–Вс: 9:00 – 22:00 (МСК)');

// ======================================================
// ВСПОМОГАТЕЛЬНАЯ ФУНКЦИЯ ИНИЦИАЛИЗАЦИИ
// ======================================================

/**
 * Запуск сессии с настройками безопасности
 * Вызывается один раз в начале каждого запроса
 */
function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();

        // Регенерация ID если сессия новая или истекла
        if (!isset($_SESSION['_initiated'])) {
            session_regenerate_id(true);
            $_SESSION['_initiated'] = true;
            $_SESSION['_created_at'] = time();
        }

        // Проверяем время жизни сессии
        if (isset($_SESSION['_created_at'])) {
            $lifetime = str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/')
                ? SESSION_ADMIN_LIFETIME
                : SESSION_LIFETIME;

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

// Автоматически стартуем сессию при подключении конфига
start_secure_session();
```

---

## Файл 3: `includes/db.php`
**Путь:** `/includes/db.php`

```php
<?php
/**
 * Класс для работы с базой данных
 * Singleton-паттерн, PDO, prepared statements
 * 
 * Путь: /includes/db.php
 */

declare(strict_types=1);

// Защита от прямого доступа
if (!defined('APP_ROOT')) {
    http_response_code(403);
    exit('Forbidden');
}

class Database
{
    // Единственный экземпляр класса (Singleton)
    private static ?Database $instance = null;

    // PDO-соединение
    private PDO $pdo;

    // Счётчик запросов (для отладки)
    private int $query_count = 0;

    /**
     * Приватный конструктор — запрещает создание через new Database()
     * Используй Database::getInstance()
     */
    private function __construct()
    {
        $this->connect();
    }

    /**
     * Клонирование запрещено (паттерн Singleton)
     */
    private function __clone() {}

    /**
     * Получение единственного экземпляра базы данных
     */
    public static function getInstance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Создание PDO-соединения с базой данных
     * @throws RuntimeException при ошибке подключения
     */
    private function connect(): void
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            // Выбрасывать исключения при ошибках
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

            // Возвращать результат как ассоциативный массив
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            // Отключаем эмуляцию prepared statements (безопаснее)
            PDO::ATTR_EMULATE_PREPARES   => false,

            // Получать числовые поля как числа, а не строки
            PDO::ATTR_STRINGIFY_FETCHES  => false,

            // Кодировка соединения
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",

            // Persistent connections (осторожно в production!)
            PDO::ATTR_PERSISTENT         => false,
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Логируем ошибку (без чувствительных данных)
            $this->logError('Ошибка подключения к БД: ' . $e->getMessage());

            // В production — показываем общее сообщение
            if (APP_ENV === 'production') {
                throw new RuntimeException('Сервис временно недоступен. Попробуйте позже.');
            }
            throw new RuntimeException('Ошибка подключения к БД: ' . $e->getMessage());
        }
    }

    /**
     * Выполнение SELECT-запроса, возвращает все строки
     * 
     * @param string $sql   SQL с плейсхолдерами (:name или ?)
     * @param array  $params Параметры для подстановки
     * @return array Массив строк результата
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->prepare($sql, $params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logError('fetchAll error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            throw new RuntimeException('Ошибка при получении данных: ' . $e->getMessage());
        }
    }

    /**
     * Выполнение SELECT-запроса, возвращает одну строку
     * 
     * @param string $sql
     * @param array  $params
     * @return array|null Одна строка или null
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        try {
            $stmt = $this->prepare($sql, $params);
            $result = $stmt->fetch();
            return $result !== false ? $result : null;
        } catch (PDOException $e) {
            $this->logError('fetchOne error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            throw new RuntimeException('Ошибка при получении записи: ' . $e->getMessage());
        }
    }

    /**
     * Получение одного значения из запроса (первая колонка первой строки)
     * Удобно для COUNT(*), MAX(), и т.д.
     * 
     * @param string $sql
     * @param array  $params
     * @return mixed Значение или null
     */
    public function fetchColumn(string $sql, array $params = []): mixed
    {
        try {
            $stmt = $this->prepare($sql, $params);
            $result = $stmt->fetchColumn();
            return $result !== false ? $result : null;
        } catch (PDOException $e) {
            $this->logError('fetchColumn error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            throw new RuntimeException('Ошибка при получении значения: ' . $e->getMessage());
        }
    }

    /**
     * Выполнение INSERT / UPDATE / DELETE запроса
     * 
     * @param string $sql
     * @param array  $params
     * @return int Количество затронутых строк
     */
    public function execute(string $sql, array $params = []): int
    {
        try {
            $stmt = $this->prepare($sql, $params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->logError('execute error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            throw new RuntimeException('Ошибка при выполнении запроса: ' . $e->getMessage());
        }
    }

    /**
     * Вставка записи с возвратом ID
     * 
     * @param string $sql
     * @param array  $params
     * @return string ID последней вставленной записи
     */
    public function insert(string $sql, array $params = []): string
    {
        try {
            $this->prepare($sql, $params);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->logError('insert error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            throw new RuntimeException('Ошибка при добавлении записи: ' . $e->getMessage());
        }
    }

    /**
     * Подготовка и выполнение запроса
     * Внутренний метод — используется всеми публичными методами
     * 
     * @param string $sql
     * @param array  $params
     * @return PDOStatement
     */
    private function prepare(string $sql, array $params = []): PDOStatement
    {
        $this->query_count++;

        $stmt = $this->pdo->prepare($sql);

        // Привязываем параметры с правильными типами PDO
        foreach ($params as $key => $value) {
            $param_key = is_int($key) ? $key + 1 : ':' . ltrim((string)$key, ':');
            $param_type = match (true) {
                is_int($value)   => PDO::PARAM_INT,
                is_bool($value)  => PDO::PARAM_BOOL,
                is_null($value)  => PDO::PARAM_NULL,
                default          => PDO::PARAM_STR,
            };
            $stmt->bindValue($param_key, $value, $param_type);
        }

        $stmt->execute();
        return $stmt;
    }

    // ======================================================
    // ТРАНЗАКЦИИ
    // ======================================================

    /**
     * Начать транзакцию
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Подтвердить транзакцию
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Откатить транзакцию
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * Выполнить callable внутри транзакции с автоматическим rollback при ошибке
     * 
     * Пример использования:
     * $db->transaction(function() use ($db) {
     *     $db->execute("INSERT ...");
     *     $db->execute("UPDATE ...");
     * });
     * 
     * @param callable $callback
     * @return mixed Результат callback
     */
    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();

        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Throwable $e) {
            $this->rollback();
            $this->logError('Транзакция откатилась: ' . $e->getMessage());
            throw $e;
        }
    }

    // ======================================================
    // ВСПОМОГАТЕЛЬНЫЕ МЕТОДЫ
    // ======================================================

    /**
     * Получить количество строк в таблице с условием
     * 
     * @param string $table  Имя таблицы
     * @param string $where  WHERE условие (без слова WHERE)
     * @param array  $params Параметры
     * @return int
     */
    public function count(string $table, string $where = '1=1', array $params = []): int
    {
        // Whitelist для имён таблиц (защита от SQL-инъекций в имени таблицы)
        $allowed_tables = [
            'orders', 'tracks', 'reviews', 'admins', 'admin_sessions',
            'order_logs', 'track_plays', 'page_views', 'contact_messages',
        ];

        if (!in_array($table, $allowed_tables, true)) {
            throw new InvalidArgumentException("Недопустимое имя таблицы: $table");
        }

        $sql = "SELECT COUNT(*) FROM `{$table}` WHERE {$where}";
        return (int)$this->fetchColumn($sql, $params);
    }

    /**
     * Проверить, существует ли запись
     * 
     * @param string $table
     * @param string $where
     * @param array  $params
     * @return bool
     */
    public function exists(string $table, string $where, array $params = []): bool
    {
        return $this->count($table, $where, $params) > 0;
    }

    /**
     * Получить количество выполненных запросов (для отладки)
     */
    public function getQueryCount(): int
    {
        return $this->query_count;
    }

    /**
     * Получить прямой доступ к PDO (для нестандартных случаев)
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Логирование ошибок в файл
     * 
     * @param string $message
     */
    private function logError(string $message): void
    {
        $log_file = APP_ROOT . '/logs/errors.log';
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
        $uri = $_SERVER['REQUEST_URI'] ?? 'CLI';

        $log_line = "[{$timestamp}] [DB] [{$ip}] [{$uri}] {$message}" . PHP_EOL;

        // Создаём директорию логов если не существует
        $log_dir = dirname($log_file);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0750, true);
        }

        error_log($log_line, 3, $log_file);
    }
}

/**
 * Глобальная функция-хелпер для получения экземпляра БД
 * Удобно использовать вместо Database::getInstance() в шаблонах
 * 
 * Пример: $db = db();
 *         $tracks = db()->fetchAll("SELECT * FROM tracks");
 */
function db(): Database
{
    return Database::getInstance();
}
```

---

## Файл 4: `includes/functions.php`
**Путь:** `/includes/functions.php`

```php
<?php
/**
 * Вспомогательные функции (хелперы) проекта "Хитовая Песня"
 * Функции для работы с данными, форматирования, вывода
 * 
 * Путь: /includes/functions.php
 */

declare(strict_types=1);

// Защита от прямого доступа
if (!defined('APP_ROOT')) {
    http_response_code(403);
    exit('Forbidden');
}

// ======================================================
// ЭКРАНИРОВАНИЕ И ВЫВОД
// ======================================================

/**
 * Безопасный вывод строки в HTML (защита от XSS)
 * Основная функция экранирования — использовать ВЕЗДЕ при выводе в HTML
 * 
 * @param mixed $value Входное значение
 * @return string Экранированная строка
 */
function e(mixed $value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        APP_CHARSET
    );
}

/**
 * Вывод с echo (echo e($val) → ee($val))
 */
function ee(mixed $value): void
{
    echo e($value);
}

/**
 * Безопасный вывод HTML (только для доверенного контента!)
 * Очищает HTML через допустимые теги
 * 
 * @param string $html
 * @param array  $allowed_tags Допустимые теги
 * @return string
 */
function safe_html(string $html, array $allowed_tags = ['p', 'br', 'strong', 'em', 'ul', 'ol', 'li']): string
{
    $allowed = '<' . implode('><', $allowed_tags) . '>';
    return strip_tags($html, $allowed);
}

// ======================================================
// ФОРМАТИРОВАНИЕ
// ======================================================

/**
 * Форматирование цены в рублях
 * 
 * @param int|float $amount Сумма
 * @param bool      $short  Короткий формат (2 500 ₽ vs 2 500 рублей)
 * @return string
 */
function format_price(int|float $amount, bool $short = true): string
{
    $formatted = number_format($amount, 0, ',', ' ');
    return $formatted . ($short ? ' ₽' : ' рублей');
}

/**
 * Форматирование длительности трека из секунд в mm:ss
 * 
 * @param int $seconds Длительность в секундах
 * @return string Формат 3:45
 */
function format_duration(int $seconds): string
{
    $minutes = (int)($seconds / 60);
    $secs = $seconds % 60;
    return sprintf('%d:%02d', $minutes, $secs);
}

/**
 * Форматирование даты на русском языке
 * 
 * @param string $date     Дата в формате Y-m-d или datetime
 * @param bool   $with_time Включать время
 * @return string
 */
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

/**
 * Относительное время "5 минут назад", "2 дня назад"
 * 
 * @param string $datetime
 * @return string
 */
function time_ago(string $datetime): string
{
    try {
        $now  = new DateTime();
        $then = new DateTime($datetime);
        $diff = $now->diff($then);
        $seconds = (int)abs($now->getTimestamp() - $then->getTimestamp());

        return match (true) {
            $seconds < 60      => 'только что',
            $seconds < 3600    => pluralize((int)($seconds / 60), 'минуту', 'минуты', 'минут') . ' назад',
            $seconds < 86400   => pluralize((int)($seconds / 3600), 'час', 'часа', 'часов') . ' назад',
            $diff->days < 7    => pluralize($diff->days, 'день', 'дня', 'дней') . ' назад',
            $diff->days < 30   => pluralize((int)($diff->days / 7), 'неделю', 'недели', 'недель') . ' назад',
            $diff->days < 365  => pluralize((int)($diff->days / 30), 'месяц', 'месяца', 'месяцев') . ' назад',
            default            => pluralize((int)($diff->days / 365), 'год', 'года', 'лет') . ' назад',
        };
    } catch (Exception) {
        return $datetime;
    }
}

/**
 * Склонение числительных
 * Пример: pluralize(5, 'яблоко', 'яблока', 'яблок') → 'яблок'
 * 
 * @param int    $n     Число
 * @param string $form1 Форма для 1 (1 яблоко)
 * @param string $form2 Форма для 2-4 (2 яблока)
 * @param string $form5 Форма для 5+ (5 яблок)
 * @return string Число + нужная форма слова
 */
function pluralize(int $n, string $form1, string $form2, string $form5): string
{
    $n = abs($n) % 100;
    $n1 = $n % 10;

    $word = match (true) {
        $n > 10 && $n < 20 => $form5,
        $n1 > 1 && $n1 < 5 => $form2,
        $n1 === 1          => $form1,
        default            => $form5,
    };

    return abs((int)$n) . ' ' . $word;
}

/**
 * Форматирование числа прослушиваний
 * 1234 → "1.2K", 1000000 → "1M"
 * 
 * @param int $count
 * @return string
 */
function format_plays_count(int $count): string
{
    return match (true) {
        $count >= 1_000_000 => round($count / 1_000_000, 1) . 'M',
        $count >= 1_000     => round($count / 1_000, 1) . 'K',
        default             => (string)$count,
    };
}

// ======================================================
// ГЕНЕРАЦИЯ НОМЕРА ЗАКАЗА
// ======================================================

/**
 * Генерация уникального номера заявки
 * Формат: HP-00001
 * 
 * @param int $order_id ID заявки из БД
 * @return string
 */
function generate_order_number(int $order_id): string
{
    return 'HP-' . str_pad((string)$order_id, 5, '0', STR_PAD_LEFT);
}

/**
 * Парсинг номера заявки в ID
 * 'HP-00042' → 42
 * 
 * @param string $order_number
 * @return int|null
 */
function parse_order_number(string $order_number): ?int
{
    if (preg_match('/^HP-(\d+)$/', $order_number, $matches)) {
        return (int)$matches[1];
    }
    return null;
}

// ======================================================
// URL И РЕДИРЕКТЫ
// ======================================================

/**
 * Безопасный редирект с защитой от header injection
 * 
 * @param string $url  URL для редиректа
 * @param int    $code HTTP-код (301, 302, 303...)
 * @return never
 */
function redirect(string $url, int $code = 302): never
{
    // Защита от open redirect — разрешаем только внутренние URL
    if (!str_starts_with($url, APP_URL) && !str_starts_with($url, '/')) {
        $url = '/';
    }

    // Удаляем переносы строк из URL (защита от header injection)
    $url = str_replace(["\r", "\n"], '', $url);

    header('Location: ' . $url, true, $code);
    exit();
}

/**
 * Получить текущий URL
 * 
 * @return string
 */
function current_url(): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? APP_DOMAIN;
    $uri  = $_SERVER['REQUEST_URI'] ?? '/';
    return $protocol . '://' . $host . $uri;
}

/**
 * Построить URL с параметрами
 * 
 * @param string $path   Путь (/order.php)
 * @param array  $params GET-параметры
 * @return string
 */
function build_url(string $path, array $params = []): string
{
    $url = APP_URL . '/' . ltrim($path, '/');
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    return $url;
}

/**
 * Активна ли данная страница (для навигации)
 * 
 * @param string $page  Имя файла (index.php, portfolio.php...)
 * @param string $class CSS-класс для активного элемента
 * @return string
 */
function active_class(string $page, string $class = 'active'): string
{
    $current = basename($_SERVER['PHP_SELF'] ?? '');
    return $current === $page ? $class : '';
}

// ======================================================
// РАБОТА С ФАЙЛАМИ
// ======================================================

/**
 * Безопасное создание директории
 * 
 * @param string $path
 * @param int    $mode
 * @return bool
 */
function ensure_directory(string $path, int $mode = 0755): bool
{
    if (!is_dir($path)) {
        return mkdir($path, $mode, true);
    }
    return true;
}

/**
 * Генерация уникального имени файла при загрузке
 * Избегаем коллизий и XSS через имя файла
 * 
 * @param string $original_name Оригинальное имя файла
 * @param string $prefix        Префикс
 * @return string Безопасное имя файла
 */
function generate_filename(string $original_name, string $prefix = ''): string
{
    $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $ext = preg_replace('/[^a-z0-9]/', '', $ext); // Только безопасные символы
    $unique = bin2hex(random_bytes(8));
    $timestamp = date('Ymd');

    return ($prefix ? $prefix . '_' : '') . $timestamp . '_' . $unique . '.' . $ext;
}

/**
 * Получить расширение файла (безопасно)
 * 
 * @param string $filename
 * @return string Расширение в нижнем регистре
 */
function get_file_ext(string $filename): string
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Форматирование размера файла в читаемый вид
 * 
 * @param int $bytes
 * @return string "1.5 МБ"
 */
function format_filesize(int $bytes): string
{
    $units = ['Б', 'КБ', 'МБ', 'ГБ'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 1) . ' ' . $units[$i];
}

// ======================================================
// ЛОГИРОВАНИЕ
// ======================================================

/**
 * Запись в лог-файл
 * 
 * @param string $message  Сообщение
 * @param string $level    Уровень: INFO, WARNING, ERROR
 * @param string $log_file Имя файла лога (без пути)
 */
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

    // Ограничиваем размер лога (10 МБ — ротация)
    if (file_exists($log_path) && filesize($log_path) > 10 * 1024 * 1024) {
        rename($log_path, $log_path . '.' . date('Ymd_His') . '.bak');
    }

    file_put_contents($log_path, $line, FILE_APPEND | LOCK_EX);
}

/**
 * Лог ошибок
 */
function log_error(string $message): void
{
    write_log($message, 'ERROR', 'errors.log');
}

/**
 * Лог доступа/событий
 */
function log_access(string $message): void
{
    write_log($message, 'INFO', 'access.log');
}

/**
 * Лог почты
 */
function log_mail(string $message): void
{
    write_log($message, 'INFO', 'mail.log');
}

// ======================================================
// ПОЛУЧЕНИЕ IP
// ======================================================

/**
 * Получение реального IP-адреса клиента
 * Учитывает прокси-заголовки (с проверкой подлинности)
 * 
 * @return string IP-адрес
 */
function get_client_ip(): string
{
    // Заголовки, которые могут содержать реальный IP за прокси
    $headers = [
        'HTTP_CF_CONNECTING_IP',    // Cloudflare
        'HTTP_X_REAL_IP',           // Nginx proxy
        'HTTP_X_FORWARDED_FOR',     // Standard proxy
        'REMOTE_ADDR',              // Direct connection
    ];

    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            // X-Forwarded-For может содержать несколько IP через запятую
            $ip = trim(explode(',', $_SERVER[$header])[0]);

            // Проверяем валидность IP
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }

    // Fallback на REMOTE_ADDR (может быть приватным IP за NAT)
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// ======================================================
// ПАГИНАЦИЯ
// ======================================================

/**
 * Расчёт параметров пагинации
 * 
 * @param int $total_items  Всего элементов
 * @param int $per_page     Элементов на странице
 * @param int $current_page Текущая страница (из GET)
 * @return array ['offset' => 0, 'total_pages' => 5, 'current_page' => 1, 'per_page' => 12]
 */
function paginate(int $total_items, int $per_page, int $current_page = 1): array
{
    $total_pages = max(1, (int)ceil($total_items / $per_page));
    $current_page = max(1, min($current_page, $total_pages));
    $offset = ($current_page - 1) * $per_page;

    return [
        'total_items'  => $total_items,
        'per_page'     => $per_page,
        'current_page' => $current_page,
        'total_pages'  => $total_pages,
        'offset'       => $offset,
        'has_prev'     => $current_page > 1,
        'has_next'     => $current_page < $total_pages,
        'prev_page'    => $current_page - 1,
        'next_page'    => $current_page + 1,
    ];
}

// ======================================================
// ВАЛИДАЦИЯ
// ======================================================

/**
 * Валидация российского номера телефона
 * Принимает: +7..., 8..., 7...
 * 
 * @param string $phone
 * @return bool
 */
function validate_phone(string $phone): bool
{
    // Очищаем от пробелов, скобок, дефисов
    $clean = preg_replace('/[\s\(\)\-\+]/', '', $phone);
    
    // Российский номер: 7XXXXXXXXXX или 8XXXXXXXXXX (11 цифр)
    return (bool)preg_match('/^[78]\d{10}$/', $clean);
}

/**
 * Нормализация телефона к формату +7XXXXXXXXXX
 * 
 * @param string $phone
 * @return string
 */
function normalize_phone(string $phone): string
{
    $clean = preg_replace('/[^\d]/', '', $phone);

    // Заменяем ведущую 8 на 7
    if (strlen($clean) === 11 && str_starts_with($clean, '8')) {
        $clean = '7' . substr($clean, 1);
    }

    // Добавляем +7 если только 10 цифр
    if (strlen($clean) === 10) {
        $clean = '7' . $clean;
    }

    return '+' . $clean;
}

/**
 * Валидация email
 * 
 * @param string $email
 * @return bool
 */
function validate_email(string $email): bool
{
    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Валидация Telegram username
 * Формат: @username (5-32 символа, буквы/цифры/подчёркивание)
 * 
 * @param string $username
 * @return bool
 */
function validate_telegram(string $username): bool
{
    $clean = ltrim($username, '@');
    return (bool)preg_match('/^[a-zA-Z0-9_]{5,32}$/', $clean);
}

/**
 * Очистка строки для безопасного использования
 * 
 * @param string $input
 * @param int    $max_length Максимальная длина (0 = без ограничений)
 * @return string
 */
function sanitize_string(string $input, int $max_length = 0): string
{
    // Убираем нулевые байты и управляющие символы (кроме переносов строк)
    $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
    $clean = trim($clean ?? '');

    if ($max_length > 0 && mb_strlen($clean) > $max_length) {
        $clean = mb_substr($clean, 0, $max_length);
    }

    return $clean;
}

// ======================================================
// ВСПОМОГАТЕЛЬНЫЕ
// ======================================================

/**
 * Получить категорию по slug с данными
 * 
 * @param string $slug
 * @return array|null
 */
function get_category(string $slug): ?array
{
    $categories = SONG_CATEGORIES;
    return $categories[$slug] ?? null;
}

/**
 * Получить тариф по slug
 * 
 * @param string $slug
 * @return array|null
 */
function get_tariff(string $slug): ?array
{
    $tariffs = TARIFFS;
    return $tariffs[$slug] ?? null;
}

/**
 * Генерация цвета по категории (для градиента обложки трека)
 * 
 * @param string $category
 * @return array ['from' => '#...', 'to' => '#...']
 */
function get_category_gradient(string $category): array
{
    return match ($category) {
        'wedding'    => ['from' => '#8B1E3F', 'to' => '#D4A574'],
        'birthday'   => ['from' => '#6B2FBE', 'to' => '#D4A574'],
        'anniversary' => ['from' => '#1E4D8B', 'to' => '#74B4D4'],
        'corporate'  => ['from' => '#1E3F4A', 'to' => '#74D4C8'],
        'holiday'    => ['from' => '#1A5C2A', 'to' => '#D4A574'],
        'children'   => ['from' => '#8B651E', 'to' => '#F0D074'],
        'special'    => ['from' => '#8B1E6B', 'to' => '#D474C8'],
        default      => ['from' => '#8B1E3F', 'to' => '#6B1230'],
    };
}

/**
 * Проверка, является ли запрос AJAX
 * 
 * @return bool
 */
function is_ajax(): bool
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Отправка JSON-ответа и завершение скрипта
 * Используется в API-эндпоинтах
 * 
 * @param array $data    Данные для JSON
 * @param int   $code    HTTP-код ответа
 */
function json_response(array $data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

/**
 * Получить статус заявки на русском языке
 * 
 * @param string $status
 * @return array ['label' => 'Новая', 'class' => 'status-new']
 */
function get_order_status(string $status): array
{
    return match ($status) {
        'new'         => ['label' => 'Новая',       'class' => 'status-new'],
        'in_progress' => ['label' => 'В работе',    'class' => 'status-progress'],
        'review'      => ['label' => 'На проверке', 'class' => 'status-review'],
        'completed'   => ['label' => 'Выполнена',   'class' => 'status-completed'],
        'cancelled'   => ['label' => 'Отменена',    'class' => 'status-cancelled'],
        default       => ['label' => $status,       'class' => 'status-unknown'],
    };
}

/**
 * Обрезка текста до нужной длины с добавлением многоточия
 * 
 * @param string $text
 * @param int    $limit   Максимальная длина в символах
 * @param string $suffix  Суффикс (по умолчанию ...)
 * @return string
 */
function truncate(string $text, int $limit = 100, string $suffix = '...'): string
{
    if (mb_strlen($text) <= $limit) {
        return $text;
    }

    // Обрезаем по словам, чтобы не разрывать слово
    $truncated = mb_substr($text, 0, $limit);
    $last_space = mb_strrpos($truncated, ' ');

    if ($last_space !== false) {
        $truncated = mb_substr($truncated, 0, $last_space);
    }

    return $truncated . $suffix;
}

/**
 * Транслитерация строки (для slug/filename)
 * 
 * @param string $text
 * @return string
 */
function transliterate(string $text): string
{
    $table = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        ' ' => '-', '_' => '-',
    ];

    $text = mb_strtolower($text);
    $text = strtr($text, $table);
    $text = preg_replace('/[^a-z0-9\-]/', '', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

/**
 * Генерация slug из строки
 * "Моя свадьба" → "moya-svadba"
 * 
 * @param string $text
 * @return string
 */
function make_slug(string $text): string
{
    return transliterate($text);
}
```

---

## Файл 5: `includes/security.php`
**Путь:** `/includes/security.php`

```php
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
```

---

## Файл 6: `database/schema.sql`
**Путь:** `/database/schema.sql`

```sql
-- ======================================================
-- СХЕМА БАЗЫ ДАННЫХ "ХИТОВАЯ ПЕСНЯ"
-- MySQL 5.7+ / MariaDB 10+
-- Кодировка: utf8mb4 (полная поддержка Unicode и emoji)
-- 
-- Путь: /database/schema.sql
-- Применение: mysql -u user -p hitpesnya < schema.sql
-- ======================================================

-- Создание базы данных (если не существует)
CREATE DATABASE IF NOT EXISTS `hitpesnya`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `hitpesnya`;

-- Отключаем проверки внешних ключей при создании таблиц
SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;
SET time_zone = '+03:00';

-- ======================================================
-- ТАБЛИЦА: admins
-- Администраторы системы
-- ======================================================

CREATE TABLE IF NOT EXISTS `admins` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(50)  NOT NULL UNIQUE COMMENT 'Логин',
    `password_hash` VARCHAR(255) NOT NULL COMMENT 'Хэш пароля (Argon2id)',
    `email`         VARCHAR(100) NOT NULL UNIQUE,
    `display_name`  VARCHAR(100) NOT NULL DEFAULT '' COMMENT 'Отображаемое имя',
    `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
    `last_login_at` DATETIME     NULL     COMMENT 'Последний вход',
    `last_login_ip` VARCHAR(45)  NULL     COMMENT 'IP последнего входа',
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_username` (`username`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Администраторы системы';

-- ======================================================
-- ТАБЛИЦА: admin_sessions
-- Сессии администраторов
-- ======================================================

CREATE TABLE IF NOT EXISTS `admin_sessions` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `admin_id`    INT UNSIGNED    NOT NULL,
    `session_id`  VARCHAR(128)    NOT NULL UNIQUE COMMENT 'ID сессии PHP',
    `ip_address`  VARCHAR(45)     NOT NULL,
    `user_agent`  VARCHAR(255)    NOT NULL DEFAULT '',
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at`  DATETIME        NOT NULL,
    `is_active`   TINYINT(1)      NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    INDEX `idx_session_id` (`session_id`),
    INDEX `idx_admin_id` (`admin_id`),
    INDEX `idx_expires` (`expires_at`),
    FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Сессии администраторов';

-- ======================================================
-- ТАБЛИЦА: orders
-- Заявки на создание песен
-- ======================================================

CREATE TABLE IF NOT EXISTS `orders` (
    `id`                    INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `order_number`          VARCHAR(20)     NOT NULL UNIQUE COMMENT 'Номер HP-00001',

    -- ШАГ 1: Повод
    `occasion`              VARCHAR(50)     NOT NULL COMMENT 'Повод (wedding, birthday...)',
    `occasion_other`        VARCHAR(255)    NULL     COMMENT 'Другой повод (текст)',
    `event_date`            DATE            NULL     COMMENT 'Дата мероприятия',
    `urgency`               ENUM('normal','fast','urgent','very_urgent')
                                            NOT NULL DEFAULT 'normal',

    -- ШАГ 2: Герой
    `hero_name`             VARCHAR(255)    NOT NULL COMMENT 'Имя героя',
    `hero_age`              TINYINT UNSIGNED NULL    COMMENT 'Возраст',
    `hero_relation`         VARCHAR(100)    NULL     COMMENT 'Кем приходится',
    `hero_profession`       VARCHAR(255)    NULL     COMMENT 'Профессия',
    `hero_hobbies`          VARCHAR(500)    NULL     COMMENT 'Хобби и увлечения',

    -- ШАГ 3: История
    `story`                 TEXT            NOT NULL COMMENT 'Основная история',
    `must_include`          TEXT            NULL     COMMENT 'Что обязательно упомянуть',
    `must_exclude`          TEXT            NULL     COMMENT 'Чего избегать',

    -- ШАГ 4: Стиль
    `mood`                  VARCHAR(50)     NULL     COMMENT 'Настроение песни',
    `music_styles`          JSON            NULL     COMMENT 'Стили музыки (массив)',
    `voice_type`            VARCHAR(50)     NULL     COMMENT 'Тип голоса',
    `duration_type`         ENUM('short','standard','long')
                                            NOT NULL DEFAULT 'standard',

    -- ШАГ 5: Тариф
    `tariff`                ENUM('basic','standard','premium','corporate','unknown')
                                            NOT NULL DEFAULT 'standard',
    `extra_wishes`          TEXT            NULL     COMMENT 'Дополнительные пожелания',

    -- ШАГ 6: Контакты
    `client_name`           VARCHAR(255)    NOT NULL COMMENT 'Имя клиента',
    `client_phone`          VARCHAR(20)     NOT NULL COMMENT 'Телефон',
    `client_telegram`       VARCHAR(100)    NULL     COMMENT 'Telegram',
    `client_whatsapp`       VARCHAR(20)     NULL     COMMENT 'WhatsApp',
    `client_ok`             VARCHAR(255)    NULL     COMMENT 'Одноклассники (ссылка)',
    `client_vk`             VARCHAR(255)    NULL     COMMENT 'ВКонтакте (ссылка)',
    `client_email`          VARCHAR(150)    NULL     COMMENT 'Email',
    `contact_time`          VARCHAR(50)     NULL     COMMENT 'Удобное время связи',
    `preferred_contact`     VARCHAR(50)     NULL     COMMENT 'Предпочтительный способ',

    -- Мета
    `status`                ENUM('new','in_progress','review','completed','cancelled')
                                            NOT NULL DEFAULT 'new',
    `manager_notes`         TEXT            NULL     COMMENT 'Заметки менеджера',
    `admin_id`              INT UNSIGNED    NULL     COMMENT 'Менеджер, взявший заявку',
    `price_calculated`      DECIMAL(10,2)   NULL     COMMENT 'Рассчитанная стоимость',
    `paid_at`               DATETIME        NULL     COMMENT 'Время оплаты',

    -- Техническое
    `ip_address`            VARCHAR(45)     NOT NULL COMMENT 'IP при отправке',
    `user_agent`            VARCHAR(500)    NOT NULL DEFAULT '',
    `referrer`              VARCHAR(500)    NULL     COMMENT 'Источник (UTM)',
    `utm_source`            VARCHAR(100)    NULL,
    `utm_medium`            VARCHAR(100)    NULL,
    `utm_campaign`          VARCHAR(100)    NULL,
    `notification_sent`     TINYINT(1)      NOT NULL DEFAULT 0 COMMENT 'Уведомление отправлено',
    `created_at`            DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`            DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE INDEX `idx_order_number` (`order_number`),
    INDEX `idx_status` (`status`),
    INDEX `idx_occasion` (`occasion`),
    INDEX `idx_tariff` (`tariff`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_client_phone` (`client_phone`),
    INDEX `idx_admin_id` (`admin_id`),
    FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Заявки на создание песен';

-- ======================================================
-- ТАБЛИЦА: order_logs
-- История изменений статусов заявок
-- ======================================================

CREATE TABLE IF NOT EXISTS `order_logs` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id`    INT UNSIGNED    NOT NULL,
    `admin_id`    INT UNSIGNED    NULL     COMMENT 'Кто изменил (NULL = система)',
    `action`      VARCHAR(100)    NOT NULL COMMENT 'Тип действия',
    `old_value`   TEXT            NULL     COMMENT 'Старое значение',
    `new_value`   TEXT            NULL     COMMENT 'Новое значение',
    `comment`     TEXT            NULL     COMMENT 'Комментарий',
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_order_id` (`order_id`),
    INDEX `idx_created_at` (`created_at`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='История изменений заявок';

-- ======================================================
-- ТАБЛИЦА: tracks
-- Треки для портфолио
-- ======================================================

CREATE TABLE IF NOT EXISTS `tracks` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `title`         VARCHAR(255)    NOT NULL COMMENT 'Название трека',
    `description`   TEXT            NULL     COMMENT 'Описание',
    `category`      VARCHAR(50)     NOT NULL COMMENT 'Категория (wedding, birthday...)',
    `subcategory`   VARCHAR(100)    NULL,
    `music_style`   VARCHAR(100)    NULL     COMMENT 'Стиль музыки',
    `mood`          VARCHAR(100)    NULL     COMMENT 'Настроение',
    `voice_type`    VARCHAR(50)     NULL     COMMENT 'Голос (male, female, duet)',
    `duration`      SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Длительность в секундах',
    `language`      VARCHAR(10)     NOT NULL DEFAULT 'ru',
    `audio_file`    VARCHAR(500)    NULL     COMMENT 'Путь к аудиофайлу',
    `cover_image`   VARCHAR(500)    NULL     COMMENT 'Путь к обложке',
    `lyrics`        LONGTEXT        NULL     COMMENT 'Текст песни',
    `plays_count`   INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT 'Число прослушиваний',
    `is_featured`   TINYINT(1)      NOT NULL DEFAULT 0 COMMENT 'На главной',
    `is_active`     TINYINT(1)      NOT NULL DEFAULT 1 COMMENT 'Активен/скрыт',
    `sort_order`    SMALLINT        NOT NULL DEFAULT 0 COMMENT 'Порядок сортировки',
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_category` (`category`),
    INDEX `idx_is_featured` (`is_featured`),
    INDEX `idx_is_active` (`is_active`),
    INDEX `idx_sort_order` (`sort_order`),
    INDEX `idx_plays_count` (`plays_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Треки портфолио';

-- ======================================================
-- ТАБЛИЦА: track_plays
-- Статистика прослушиваний (детальная)
-- ======================================================

CREATE TABLE IF NOT EXISTS `track_plays` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `track_id`    INT UNSIGNED    NOT NULL,
    `ip_address`  VARCHAR(45)     NOT NULL,
    `user_agent`  VARCHAR(255)    NOT NULL DEFAULT '',
    `page`        VARCHAR(50)     NOT NULL DEFAULT 'unknown' COMMENT 'Где слушали (index, portfolio)',
    `played_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_track_id` (`track_id`),
    INDEX `idx_played_at` (`played_at`),
    INDEX `idx_ip` (`ip_address`),
    FOREIGN KEY (`track_id`) REFERENCES `tracks`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Детальная статистика прослушиваний';

-- ======================================================
-- ТАБЛИЦА: reviews
-- Отзывы клиентов
-- ======================================================

CREATE TABLE IF NOT EXISTS `reviews` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `author_name` VARCHAR(100)  NOT NULL COMMENT 'Имя клиента',
    `author_city` VARCHAR(100)  NULL     COMMENT 'Город',
    `rating`      TINYINT       NOT NULL DEFAULT 5 COMMENT '1-5 звёзд',
    `text`        TEXT          NOT NULL COMMENT 'Текст отзыва',
    `occasion_tag` VARCHAR(100) NULL     COMMENT 'Тэг повода (Свадьба, Юбилей...)',
    `is_featured` TINYINT(1)    NOT NULL DEFAULT 0 COMMENT 'На главной',
    `is_active`   TINYINT(1)    NOT NULL DEFAULT 1,
    `sort_order`  SMALLINT      NOT NULL DEFAULT 0,
    `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_is_featured` (`is_featured`),
    INDEX `idx_is_active` (`is_active`),
    INDEX `idx_rating` (`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Отзывы клиентов';

-- ======================================================
-- ТАБЛИЦА: contact_messages
-- Сообщения из формы контактов
-- ======================================================

CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(255)  NOT NULL,
    `contact`     VARCHAR(255)  NOT NULL COMMENT 'Телефон или Telegram',
    `message`     TEXT          NOT NULL,
    `ip_address`  VARCHAR(45)   NOT NULL,
    `is_read`     TINYINT(1)    NOT NULL DEFAULT 0,
    `read_at`     DATETIME      NULL,
    `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_is_read` (`is_read`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Сообщения из формы контактов';

-- ======================================================
-- ТАБЛИЦА: page_views
-- Простая аналитика посещений
-- ======================================================

CREATE TABLE IF NOT EXISTS `page_views` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `page`        VARCHAR(100)    NOT NULL COMMENT 'Страница (index, portfolio...)',
    `ip_address`  VARCHAR(45)     NOT NULL,
    `user_agent`  VARCHAR(255)    NOT NULL DEFAULT '',
    `referer`     VARCHAR(500)    NULL,
    `viewed_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_page` (`page`),
    INDEX `idx_viewed_at` (`viewed_at`),
    INDEX `idx_ip` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Статистика посещений страниц';

-- ======================================================
-- ТАБЛИЦА: settings
-- Настройки сайта (key-value)
-- ======================================================

CREATE TABLE IF NOT EXISTS `settings` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `key`         VARCHAR(100)  NOT NULL UNIQUE,
    `value`       TEXT          NULL,
    `description` VARCHAR(255)  NULL,
    `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `idx_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Настройки сайта';

-- ======================================================
-- Включаем обратно проверки внешних ключей
-- ======================================================

SET FOREIGN_KEY_CHECKS = 1;

-- ======================================================
-- НАЧАЛЬНЫЕ ДАННЫЕ
-- ======================================================

-- --- Администратор по умолчанию ---
-- Пароль: Admin123! (ОБЯЗАТЕЛЬНО сменить после установки!)
-- Хэш сгенерирован через password_hash('Admin123!', PASSWORD_ARGON2ID)
INSERT INTO `admins` (`username`, `password_hash`, `email`, `display_name`) VALUES
(
    'admin',
    '$argon2id$v=19$m=65536,t=4,p=1$PLACEHOLDER_HASH',
    'admin@hit.owlex.top',
    'Администратор'
);
-- ВАЖНО: После установки запустить скрипт setup/generate-password.php
-- или вручную обновить хэш через PHP:
-- UPDATE admins SET password_hash = '<hash>' WHERE username = 'admin';

-- --- Демо-треки для портфолио ---
INSERT INTO `tracks` 
    (`title`, `description`, `category`, `music_style`, `mood`, `voice_type`, `duration`, `is_featured`, `is_active`, `sort_order`) 
VALUES
(
    'Свадебный вальс',
    'Трогательная песня о первом танце молодожёнов. История любви, рассказанная в музыке.',
    'wedding', 'Поп', 'Романтичное', 'female', 198, 1, 1, 1
),
(
    'Маме в день рождения',
    'Тёплая песня-признание для самого близкого человека. Лирика о маминой любви и заботе.',
    'birthday', 'Бардовская', 'Трогательное, лирическое', 'male', 215, 1, 1, 2
),
(
    'Корпоративный гимн',
    'Зажигательная песня для команды! Объединяет коллектив и поднимает дух.',
    'corporate', 'Поп', 'Весёлое, зажигательное', 'duet', 187, 1, 1, 3
),
(
    'Дедушке — 70 лет!',
    'Торжественная юбилейная песня с юмором и теплотой. Вся семья пела хором!',
    'anniversary', 'Ретро (советская эстрада)', 'Торжественное, величественное', 'male', 223, 1, 1, 4
),
(
    'Малышке 3 годика',
    'Весёлая детская песенка с любимыми персонажами и именем ребёнка.',
    'children', 'Поп', 'Весёлое, зажигательное', 'female', 165, 1, 1, 5
),
(
    'Любимой на 8 Марта',
    'Романтичная песня-признание. Нежная, как весенний ветер.',
    'special', 'Джаз/Блюз', 'Романтичное', 'male', 204, 1, 1, 6
),
(
    'Застольная на юбилей',
    'Весёлая песня для банкета. Все гости подхватили припев!',
    'anniversary', 'Шансон', 'Весёлое, зажигательное', 'male', 232, 0, 1, 7
),
(
    'Новогодний корпоратив',
    'Праздничная песня с упоминанием всех отделов компании и весёлыми куплетами.',
    'corporate', 'Поп', 'Весёлое, зажигательное', 'duet', 196, 0, 1, 8
);

-- --- Отзывы клиентов ---
INSERT INTO `reviews` 
    (`author_name`, `author_city`, `rating`, `text`, `occasion_tag`, `is_featured`, `sort_order`) 
VALUES
(
    'Анна и Дмитрий',
    'Москва',
    5,
    'Заказали песню на свадьбу — не могли оторваться когда услышали! Все гости плакали от умиления. Ребята всё учли: и как мы познакомились, и наши любимые места. Это было лучшее украшение торжества!',
    '💒 Свадьба',
    1, 1
),
(
    'Ольга М.',
    'Санкт-Петербург',
    5,
    'Заказывала песню маме на 60-летие. Мама плакала и смеялась одновременно — всё было написано с такой теплотой и точностью! Даже упомянули её любимые пироги и дачу. Спасибо огромное!',
    '🎂 Юбилей',
    1, 2
),
(
    'Команда "АльфаТех"',
    'Казань',
    5,
    'Заказали корпоративный гимн для нашей компании. Получилось настолько круто, что теперь поём его на каждом корпоративе! Ребята вникли в специфику нашей работы и создали что-то по-настоящему уникальное.',
    '🏢 Корпоратив',
    1, 3
),
(
    'Сергей К.',
    'Екатеринбург',
    5,
    'Делал сюрприз жене на день рождения. Написал все подробности, и песня получилась — будто кто-то прочитал мои мысли и положил их на музыку. Жена в полном восторге!',
    '🎉 День рождения',
    0, 4
),
(
    'Елена Васильева',
    'Новосибирск',
    5,
    'Дочке заказала песню на 5-летие. Малышка слушает её каждый день, знает наизусть и поёт вместе! Столько радости в одной песне — это бесценно.',
    '🧸 Детская',
    0, 5
);

-- --- Настройки сайта ---
INSERT INTO `settings` (`key`, `value`, `description`) VALUES
('site_name',           'Хитовая Песня',                'Название сайта'),
('site_slogan',         'Исполнение ваших желаний',     'Слоган'),
('orders_total',        '500',                          'Показываемое число заказов (обновляется вручную)'),
('years_work',          '5',                            'Лет работы'),
('maintenance_mode',    '0',                            '1 = режим обслуживания'),
('notify_email',        '1',                            'Email-уведомления включены'),
('notify_telegram',     '0',                            'Telegram-уведомления включены');

-- ======================================================
-- ПРЕДСТАВЛЕНИЕ: v_orders_summary
-- Сводная таблица заявок для дашборда
-- ======================================================

CREATE OR REPLACE VIEW `v_orders_summary` AS
SELECT
    DATE(`created_at`) AS order_date,
    COUNT(*)           AS total,
    SUM(CASE WHEN `status` = 'new'         THEN 1 ELSE 0 END) AS new_count,
    SUM(CASE WHEN `status` = 'in_progress' THEN 1 ELSE 0 END) AS in_progress_count,
    SUM(CASE WHEN `status` = 'completed'   THEN 1 ELSE 0 END) AS completed_count,
    SUM(CASE WHEN `status` = 'cancelled'   THEN 1 ELSE 0 END) AS cancelled_count
FROM `orders`
GROUP BY DATE(`created_at`)
ORDER BY order_date DESC;

-- ======================================================
-- ПРОЦЕДУРА: generate_order_number
-- Автоматическая генерация номера заявки
-- ======================================================

DELIMITER //

CREATE PROCEDURE IF NOT EXISTS `generate_order_number`(IN order_id INT, OUT order_num VARCHAR(20))
BEGIN
    SET order_num = CONCAT('HP-', LPAD(order_id, 5, '0'));
END//

DELIMITER ;

-- ======================================================
-- ТРИГГЕР: after_order_insert
-- Автоматически устанавливает номер заявки после вставки
-- ======================================================

DELIMITER //

CREATE TRIGGER IF NOT EXISTS `after_order_insert`
AFTER INSERT ON `orders`
FOR EACH ROW
BEGIN
    DECLARE order_num VARCHAR(20);
    CALL generate_order_number(NEW.id, order_num);
    UPDATE `orders` SET `order_number` = order_num WHERE `id` = NEW.id;
END//

DELIMITER ;

-- ======================================================
-- ТРИГГЕР: after_order_status_update
-- Автоматически логирует изменение статуса
-- ======================================================

DELIMITER //

CREATE TRIGGER IF NOT EXISTS `after_order_status_update`
AFTER UPDATE ON `orders`
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO `order_logs` (`order_id`, `action`, `old_value`, `new_value`)
        VALUES (NEW.id, 'status_changed', OLD.status, NEW.status);
    END IF;
END//

DELIMITER ;

-- ======================================================
-- КОНЕЦ СХЕМЫ
-- ======================================================

-- Проверяем что всё создалось
SELECT 'База данных успешно инициализирована!' AS message;
SELECT TABLE_NAME, TABLE_ROWS, TABLE_COMMENT
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'hitpesnya'
ORDER BY TABLE_NAME;
```

---

## ✅ Этап 1 завершён

Созданы все 6 файлов фундамента:

| Файл | Статус | Описание |
|------|--------|----------|
| `/public/.htaccess` | ✅ | Безопасность, gzip, кэш, редиректы |
| `/includes/config.php` | ✅ | Все константы и настройки |
| `/includes/db.php` | ✅ | Database class, Singleton, PDO |
| `/includes/functions.php` | ✅ | ~25 хелпер-функций |
| `/includes/security.php` | ✅ | CSRF, rate limit, honeypot, sanitize |
| `/database/schema.sql` | ✅ | 9 таблиц + триггеры + демо-данные |

---

**Что нужно сделать перед Этапом 2:**

1. Создать БД: `mysql -u root -p < database/schema.sql`
2. В `config.php` заполнить: `DB_PASS`, `ADMIN_EMAIL`, `APP_SECRET`
3. Создать директории: `logs/`, `uploads/tracks/`, `uploads/covers/`
4. Дать права: `chmod 755 logs/ uploads/`
