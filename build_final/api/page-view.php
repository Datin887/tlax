<?php
/**
 * API: Счётчик просмотров страниц (вызывается через Beacon API)
 * Путь: /public/api/page-view.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$page    = mb_substr(trim($_POST['page'] ?? $_SERVER['HTTP_REFERER'] ?? ''), 0, 255, 'UTF-8');
$ip      = get_client_ip();
$referer = mb_substr($_SERVER['HTTP_REFERER'] ?? '', 0, 500, 'UTF-8');
$ua      = mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500, 'UTF-8');

// Rate limiting: не более 60 просмотров в минуту с одного IP
if (!check_rate_limit('pageview_' . $ip, 60, 60)) {
    http_response_code(429);
    exit;
}

// Фильтруем ботов
$bot_patterns = ['bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 'python', 'php'];
$ua_lower = strtolower($ua);
foreach ($bot_patterns as $pattern) {
    if (str_contains($ua_lower, $pattern)) {
        http_response_code(204);
        exit;
    }
}

try {
    $db = Database::getInstance();
    $db->execute(
        "INSERT INTO page_views (page, ip_address, referer, user_agent, created_at)
         VALUES (:page, :ip, :ref, :ua, NOW())",
        [':page' => $page, ':ip' => $ip, ':ref' => $referer, ':ua' => $ua]
    );
} catch (Exception $e) {
    log_error('page-view: ' . $e->getMessage());
}

http_response_code(204);
exit;