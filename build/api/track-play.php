<?php
/**
 * API: Счётчик прослушиваний трека
 * Метод: POST
 * Тело запроса:
 *   track_id — ID трека
 *   action   — 'play' (зарезервировано для расширения)
 *
 * Путь: /public/api/track-play.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';

// ─── Только POST ───
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['success' => false, 'message' => 'Method not allowed'], 405);
}

// ─── Rate limiting — не более 30 запросов в минуту с одного IP ───
$client_ip = get_client_ip();
if (!check_rate_limit('track_play_' . $client_ip, 30, 60)) {
    // Не возвращаем ошибку пользователю — аналитика некритична
    send_json(['success' => true, 'skipped' => true]);
}

// ─── Получаем данные ───
$track_id = (int)($_POST['track_id'] ?? 0);
$action   = trim($_POST['action'] ?? 'play');

if ($track_id <= 0) {
    send_json(['success' => false, 'message' => 'Invalid track_id'], 400);
}

// Разрешённые действия
$allowed_actions = ['play'];
if (!in_array($action, $allowed_actions, true)) {
    send_json(['success' => false, 'message' => 'Invalid action'], 400);
}

try {
    $db = Database::getInstance();

    // ─── Проверяем, что трек существует и активен ───
    $track = $db->fetchOne(
        "SELECT id FROM tracks WHERE id = :id AND is_active = 1",
        [':id' => $track_id]
    );

    if (!$track) {
        send_json(['success' => false, 'message' => 'Track not found'], 404);
    }

    // ─── Защита от накрутки: одно прослушивание с IP за 5 минут ───
    $rate_key   = 'play_' . $track_id . '_' . $client_ip;
    $recent_key = md5($rate_key);

    // Проверяем через track_plays — было ли прослушивание с этого IP за последние 5 мин
    $recent = $db->fetchOne(
        "SELECT id FROM track_plays
         WHERE track_id = :track_id
           AND ip_address = :ip
           AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)",
        [
            ':track_id' => $track_id,
            ':ip'       => $client_ip,
        ]
    );

    if ($recent) {
        // Уже считали — возвращаем успех без повторного счёта
        send_json(['success' => true, 'counted' => false]);
    }

    // ─── Записываем прослушивание ───
    $user_agent = mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500, 'UTF-8');

    $db->execute(
        "INSERT INTO track_plays (track_id, ip_address, user_agent, created_at)
         VALUES (:track_id, :ip, :ua, NOW())",
        [
            ':track_id' => $track_id,
            ':ip'       => $client_ip,
            ':ua'       => $user_agent,
        ]
    );

    // ─── Обновляем счётчик в таблице треков ───
    $db->execute(
        "UPDATE tracks SET plays_count = plays_count + 1 WHERE id = :id",
        [':id' => $track_id]
    );

    send_json(['success' => true, 'counted' => true]);

} catch (Exception $e) {
    log_error('api/track-play: ' . $e->getMessage());
    // Аналитика некритична — не показываем ошибку пользователю
    send_json(['success' => true, 'counted' => false]);
}