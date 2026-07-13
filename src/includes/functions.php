<?php
/**
 * Вспомогательные функции проекта "Хитовая Песня"
 *
 * Путь: /includes/functions.php
 */

declare(strict_types=1);

// ─── Логирование ошибок ───
function log_error(string $message): void
{
    $log_file = defined('LOG_PATH') ? LOG_PATH . '/errors.log' : sys_get_temp_dir() . '/hitsong_errors.log';
    $dir = dirname($log_file);
    if (!is_dir($dir)) @mkdir($dir, 0750, true);
    $timestamp = date('Y-m-d H:i:s');
    $line = "[{$timestamp}] {$message}" . PHP_EOL;
    @file_put_contents($log_file, $line, FILE_APPEND | LOCK_EX);
}

// ─── Экранирование HTML ───
function h(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// ─── Анализطرف───
function escHtml(string $str): string
{
    if (!$str) return '';
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Проверка: является ли запрос AJAX
 */
function is_ajax_request(): bool
{
    return (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) || (
        !empty($_SERVER['HTTP_ACCEPT']) &&
        str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')
    );
}

/**
 * Отправить JSON-ответ и завершить скрипт
 */
function send_json(array $data, int $status = 200): never
{
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

/**
 * Получить IP-адрес клиента
 */
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

    return trim($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

/**
 * Rate limiting через файловый кэш
 */
function check_rate_limit(string $key, int $limit, int $window): bool
{
    $cache_dir  = defined('LOG_PATH') ? LOG_PATH . '/rate_limit' : sys_get_temp_dir() . '/hitsong_rl';
    $cache_file = $cache_dir . '/' . md5($key) . '.json';

    if (!is_dir($cache_dir)) {
        @mkdir($cache_dir, 0750, true);
    }

    $now  = time();
    $data = ['count' => 0, 'window_start' => $now];

    if (file_exists($cache_file)) {
        $raw = @file_get_contents($cache_file);
        if ($raw) {
            $saved = json_decode($raw, true);
            if (is_array($saved)) {
                if ($now - $saved['window_start'] < $window) {
                    $data = $saved;
                }
            }
        }
    }

    $data['count']++;
    @file_put_contents($cache_file, json_encode($data), LOCK_EX);

    return $data['count'] <= $limit;
}

/**
 * Получить параметр из URL ($_GET)
 */
function get_url_param(string $name, string $default = ''): string
{
    return isset($_GET[$name]) ? trim((string)$_GET[$name]) : $default;
}

/**
 * Форматировать длительность в секундах в MM:SS
 */
function format_duration(int $seconds): string
{
    $m = (int)floor($seconds / 60);
    $s = $seconds % 60;
    return sprintf('%d:%02d', $m, $s);
}

/**
 * Санитизация строки
 */
function sanitize_string(string $value, int $max_length = 255): string
{
    $value = trim($value);
    $value = mb_substr($value, 0, $max_length, 'UTF-8');
    return $value;
}

/**
 * Санитизация текста (multi-line)
 */
function sanitize_text(string $value, int $max_length = 10000): string
{
    $value = trim($value);
    $value = mb_substr($value, 0, $max_length, 'UTF-8');
    return $value;
}

/**
 * Транслитерация русского текста в латиницу
 */
function transliterate(string $text): string
{
    $map = [
        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo',
        'ж'=>'zh','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m',
        'н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u',
        'ф'=>'f','х'=>'kh','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'shch',
        'ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
    ];
    return strtr(strtolower($text), $map);
}

/**
 * Проверка email
 */
function is_valid_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Генерация порядкового номера заявки
 */
function generate_order_number(): string
{
    $prefix = date('Ymd');
    $suffix = random_int(1000, 9999);
    return "HP-{$prefix}-{$suffix}";
}

// ─── Метки для поводов ───
function get_occasion_label(string $occasion): string
{
    $labels = [
        'wedding'     => '💒 Свадьба',
        'anniversary' => '🎂 Юбилей',
        'birthday'    => '🎉 День рождения',
        'love'        => '💕 Годовщина',
        'corporate'   => '🏢 Корпоратив',
        'march8'      => '🌸 8 Марта',
        'feb23'       => '⚔️ 23 Февраля',
        'newyear'     => '🎄 Новый год',
        'proposal'    => '💍 Предложение',
        'birth'       => '👶 Рождение',
        'retirement'  => '🏆 Выход на пенсию',
        'other'       => '✨ Другое',
    ];
    return $labels[$occasion] ?? h($occasion);
}

// ─── Метки для тарифов ───
function get_tariff_label(string $tariff): string
{
    $labels = [
        'basic'    => 'Базовый',
        'standard' => 'Стандарт',
        'premium'  => 'Премиум',
        'help'     => 'Помогите выбрать',
    ];
    return $labels[$tariff] ?? h($tariff);
}

// ─── Метки для срочности ───
function get_urgency_label(string $urgency): string
{
    $labels = [
        'normal' => 'Не срочно',
        'fast'   => 'Быстро',
        'urgent' => 'Срочно',
        'asap'   => 'Очень срочно',
    ];
    return $labels[$urgency] ?? h($urgency);
}

// ─── Рендер статуса ───
function render_status_badge(string $status): string
{
    $labels = [
        'new'         => ['label' => 'Новая',       'color' => 'warning'],
        'in_progress' => ['label' => 'В работе',    'color' => 'info'],
        'review'      => ['label' => 'На проверке', 'color' => 'accent'],
        'done'        => ['label' => 'Выполнено',   'color' => 'success'],
        'cancelled'   => ['label' => 'Отменено',    'color' => 'muted'],
    ];
    $info = $labels[$status] ?? ['label' => $status, 'color' => 'muted'];
    return '<span class="badge badge--' . h($info['color']) . '">' . h($info['label']) . '</span>';
}

/**
 * Загрузка аудио-файла
 */
function upload_audio_file(array $file): array
{
    $allowed_types = ['audio/mpeg', 'audio/mp3', 'audio/x-mpeg'];
    $allowed_exts  = ['mp3'];
    $max_size      = 25 * 1024 * 1024; // 25MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Ошибка загрузки файла'];
    }
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'Файл слишком большой (макс. 25MB)'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_exts, true)) {
        return ['success' => false, 'error' => 'Недопустимый формат файла (только MP3)'];
    }

    $filename = uniqid('track_', true) . '.' . $ext;
    $dest     = UPLOAD_PATH . '/tracks/' . $filename;

    if (!is_dir(UPLOAD_PATH . '/tracks')) @mkdir(UPLOAD_PATH . '/tracks', 0750, true);

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['success' => false, 'error' => 'Не удалось сохранить файл'];
    }

    return ['success' => true, 'filename' => $filename];
}

/**
 * Загрузка изображения
 */
function upload_image_file(array $file): array
{
    $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
    $max_size     = 5 * 1024 * 1024; // 5MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Ошибка загрузки изображения'];
    }
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'Файл слишком большой (макс. 5MB)'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_exts, true)) {
        return ['success' => false, 'error' => 'Недопустимый формат (JPG, PNG, WEBP)'];
    }

    $filename = uniqid('cover_', true) . '.' . $ext;
    $dest     = UPLOAD_PATH . '/covers/' . $filename;

    if (!is_dir(UPLOAD_PATH . '/covers')) @mkdir(UPLOAD_PATH . '/covers', 0750, true);

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['success' => false, 'error' => 'Не удалось сохранить изображение'];
    }

    return ['success' => true, 'filename' => $filename];
}

/**
 * Получить длительность аудио (в секундах)
 */
function get_audio_duration(string $filepath): int
{
    if (!file_exists($filepath)) return 0;
    // Попытка через getID3 / ffmpeg
    if (function_exists('getid3_lib')) {
        $id3 = new getID3();
        $info = $id3->analyze($filepath);
        return (int)($info['playtime_seconds'] ?? 0);
    }
    // Фолбэк: используем ffprobe
    $cmd = 'ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 ' . escapeshellarg($filepath) . ' 2>/dev/null';
    $dur = (int)shell_exec($cmd);
    return max(0, $dur);
}