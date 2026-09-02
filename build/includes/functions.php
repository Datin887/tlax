<?php
/**
 * Site functions
 */

/**
 * Get client IP address
 */
function get_client_ip(): string {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

/**
 * HTML Escape
 */
function h($val): string {
    return htmlspecialchars($val, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * AJAX check alias
 */
function is_ajax_request(): bool {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Rate limit wrapper
 */
function check_rate_limit(string $key, int $limit = 10, int $period = 60): bool {
    return rate_limit_check($key, $limit, $period);
}

/**
 * Ensure directory exists
 */
function ensure_directory(string $path, int $mode = 0755): bool {
    if (is_dir($path)) return true;
    return mkdir($path, $mode, true);
}

/**
 * URL param helper
 */
function get_url_param(string $key, $default = null) {
    return $_GET[$key] ?? $default;
}

/**
 * JSON response
 */
function send_json(array $data, int $code = 200): never {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode($data);
    exit;
}

/**
 * CSRF Verify wrapper
 */
function verify_csrf_token(string $token): bool {
    return isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);
}

/**
 * Log error to file
 */
function log_error(string $message): void {
    $log_file = __DIR__ . '/../logs/errors.log';
    ensure_directory(dirname($log_file));
    $entry = date('[Y-m-d H:i:s e]') . ' ' . $message . "\n";
    file_put_contents($log_file, $entry, FILE_APPEND | LOCK_EX);
}

/**
 * Format duration (seconds → mm:ss)
 */
function format_duration(int $seconds): string {
    $m = intdiv($seconds, 60);
    $s = $seconds % 60;
    return sprintf('%d:%02d', $m, $s);
}

/**
 * Sanitize string
 */
function sanitize_string(string $input, int $max_length = 0): string {
    $input = trim($input);
    $input = strip_tags($input);
    if ($max_length > 0) {
        $input = mb_substr($input, 0, $max_length, 'UTF-8');
    }
    return $input;
}

/**
 * Sanitize text (allow line breaks)
 */
function sanitize_text(string $input, int $max_length = 0): string {
    $input = trim($input);
    $input = strip_tags($input);
    if ($max_length > 0) {
        $input = mb_substr($input, 0, $max_length, 'UTF-8');
    }
    return $input;
}

/**
 * Transliterate (русский → транслит)
 */
function transliterate(string $str): string {
    $map = [
        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo',
        'ж'=>'zh','з'=>'z','и'=>'i','й'=>'j','к'=>'k','л'=>'l','м'=>'m',
        'н'=>'н','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u',
        'ф'=>'f','х'=>'h','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch',
        'ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
        'А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D','Е'=>'E','Ё'=>'Yo',
        'Ж'=>'Zh','З'=>'Z','И'=>'I','Й'=>'J','К'=>'K','Л'=>'L','М'=>'M',
        'Н'=>'N','О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U',
        'Ф'=>'F','Х'=>'H','Ц'=>'Ts','Ч'=>'Ch','Ш'=>'Sh','Щ'=>'Sch',
        'Ъ'=>'','Ы'=>'Y','Ь'=>'','Э'=>'E','Ю'=>'Yu','Я'=>'Ya',' '=>'-'
    ];
    
    $result = strtr($str, $map);
    $result = preg_replace('/[^a-zA-Z0-9\-_]/', '', $result);
    return strtolower($result);
}
