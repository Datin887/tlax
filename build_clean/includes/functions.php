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