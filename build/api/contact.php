<?php
/**
 * API: Обработка формы быстрой связи
 * Метод: POST
 *
 * Путь: /public/api/contact.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../../includes/mail.php';
require_once __DIR__ . '/../../includes/telegram.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['success' => false, 'message' => 'Method not allowed'], 405);
}
if (!is_ajax_request()) {
    send_json(['success' => false, 'message' => 'Forbidden'], 403);
}

// Rate limiting: 3 сообщения в час с одного IP
$client_ip = get_client_ip();
if (!check_rate_limit('contact_' . $client_ip, 3, 3600)) {
    send_json(['success' => false, 'message' => 'Слишком много запросов. Подождите немного.'], 429);
}

// Honeypot
if (!empty($_POST['website'])) {
    send_json(['success' => true]);
}

// Защита от быстрой отправки
$start = (int)($_POST['form_start_time'] ?? 0);
if ($start > 0 && ((int)(microtime(true) * 1000) - $start) < 5000) {
    log_error("contact: возможный бот, IP={$client_ip}");
}

// CSRF
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    send_json(['success' => false, 'message' => 'Ошибка безопасности. Обновите страницу.'], 403);
}

// Санитизация
$name    = sanitize_string($_POST['name']    ?? '', 100);
$contact = sanitize_string($_POST['contact'] ?? '', 100);
$message = sanitize_text($_POST['message']   ?? '', 2000);

// Валидация
$errors = [];
if (mb_strlen($name, 'UTF-8') < 2)    $errors[] = 'Введите имя';
if (mb_strlen($contact, 'UTF-8') < 5) $errors[] = 'Введите телефон или Telegram';
if (mb_strlen($message, 'UTF-8') < 10)$errors[] = 'Напишите сообщение';

if (!empty($errors)) {
    send_json(['success' => false, 'message' => implode('. ', $errors)], 422);
}

// Сохранение в БД
try {
    $db = Database::getInstance();
    $db->execute(
        "INSERT INTO contact_messages (name, contact, message, ip_address, created_at)
         VALUES (:name, :contact, :message, :ip, NOW())",
        [
            ':name'    => $name,
            ':contact' => $contact,
            ':message' => $message,
            ':ip'      => $client_ip,
        ]
    );
} catch (Exception $e) {
    log_error('contact: DB error: ' . $e->getMessage());
}

// Email
try {
    $subject = "Новое сообщение от {$name} | Хитовая Песня";
    $html = "<h2>Сообщение с сайта</h2>
             <p><b>Имя:</b> " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "</p>
             <p><b>Контакт:</b> " . htmlspecialchars($contact, ENT_QUOTES, 'UTF-8') . "</p>
             <p><b>Сообщение:</b><br>" . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . "</p>";
    send_html_email(ADMIN_EMAIL, $subject, $html);
} catch (Exception $e) {
    log_error('contact: email failed: ' . $e->getMessage());
}

// Telegram
try {
    $tg_text = "📨 *Новое сообщение с сайта*\n\n"
        . "*Имя:* " . escape_tg($name) . "\n"
        . "*Контакт:* " . escape_tg($contact) . "\n"
        . "*Сообщение:*\n" . escape_tg(mb_substr($message, 0, 500, 'UTF-8'));
    send_telegram_message($tg_text, ['parse_mode' => 'MarkdownV2']);
} catch (Exception $e) {
    log_error('contact: telegram failed: ' . $e->getMessage());
}

send_json(['success' => true, 'message' => 'Сообщение отправлено']);