<?php
/**
 * API: Чат «Музыкальный продюсер»
 * Метод: POST
 * Тело:  JSON { message: string }
 * Ответ: JSON { success, reply }
 *
 * ⚠️ Заглушка: отвечает статическим текстом.
 *    Задача №2 — подключить AI-продюсера (OpenRouter) и сбор брифа.
 *    Путь: /api/chat.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';

// ─── Только POST ───
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['success' => false, 'message' => 'Method not allowed'], 405);
}

// ─── Ожидаем JSON ───
$content_type = strtolower($_SERVER['CONTENT_TYPE'] ?? '');
if (!str_contains($content_type, 'application/json')) {
    send_json(['success' => false, 'message' => 'Expected JSON body'], 415);
}

// ─── Rate limiting: не более 30 сообщений в минуту с одного IP ───
$client_ip = get_client_ip();
if (!check_rate_limit('chat_' . $client_ip, 30, 60)) {
    send_json(['success' => false, 'message' => 'Слишком много сообщений. Попробуйте через минуту.'], 429);
}

// ─── Читаем и валидируем сообщение ───
$raw = file_get_contents('php://input');
if ($raw === false || $raw === '') {
    send_json(['success' => false, 'message' => 'Empty body'], 400);
}

$payload = json_decode($raw, true);
if (!is_array($payload)) {
    send_json(['success' => false, 'message' => 'Invalid JSON'], 400);
}

$message = sanitize_text((string)($payload['message'] ?? ''), 2000);
if ($message === '') {
    send_json(['success' => false, 'message' => 'Пустое сообщение'], 400);
}

// ─── TODO: Задача №2 — здесь будет AI-продюсер (диалог + сбор брифа) ───
// Пока возвращаем статический ответ, чтобы чат работал end-to-end.
$reply = 'Отлично! Расскажите подробнее о главном герое праздника: как его зовут, сколько ему лет и кем он вам приходится?';

send_json([
    'success' => true,
    'reply'   => $reply,
]);