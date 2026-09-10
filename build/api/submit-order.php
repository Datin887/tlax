<?php
/**
 * API: Приём заявки
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!is_ajax_request()) {
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

// CSRF проверка (единый механизм: токен формы из _csrf_token)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$csrf_token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($csrf_token)) {
    echo json_encode(['success' => false, 'message' => 'Недействительный токен безопасности']);
    exit;
}

// Rate limiting
$client_ip = get_client_ip();
if (!check_rate_limit('submit_order_' . $client_ip, 5, 3600)) {
    echo json_encode(['success' => false, 'message' => 'Too many requests'], 429);
    exit;
}

// ─── Хелперы ───
function dv(mixed $val, mixed $default = null): mixed
{
    $s = trim((string)($val ?? ''));
    return ($s === '') ? $default : $s;
}

function dv_int(mixed $val): ?int
{
    $s = trim((string)($val ?? ''));
    if ($s === '' || !is_numeric($s)) return null;
    return (int)$s;
}

// Обработка заявки
try {
    $db = Database::getInstance();

    // ─── Собираем все поля формы ───
    $occasion       = preg_replace('/[^a-z_]/', '', dv($_POST['occasion'], ''));
    $occasion_other = mb_substr(dv($_POST['occasion_other'], ''), 0, 255);
    $event_date     = dv($_POST['event_date'], null);
    if ($event_date !== null && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $event_date)) {
        $event_date = null;
    }

    $urgency_map = ['normal' => 'normal', 'fast' => 'fast', 'urgent' => 'urgent', 'very_urgent' => 'very_urgent'];
    $urgency     = $urgency_map[dv($_POST['urgency'], 'normal')] ?? 'normal';

    $hero_name       = mb_substr(dv($_POST['hero_name'], ''), 0, 255);
    $hero_age        = dv_int($_POST['hero_age']);
    $hero_relation   = mb_substr(dv($_POST['hero_relation'], ''), 0, 100);
    $hero_profession = mb_substr(dv($_POST['hero_profession'], ''), 0, 255);
    $hero_hobbies    = mb_substr(dv($_POST['hero_hobbies'], ''), 0, 500);
    $story           = dv($_POST['story'], '');
    $must_include    = dv($_POST['must_include'], null);
    $must_exclude    = dv($_POST['avoid'], null); // форма шлёт "avoid"
    $mood            = mb_substr(dv($_POST['mood'], ''), 0, 50);
    $voice_type      = mb_substr(dv($_POST['voice_type'], ''), 0, 50);
    $extra_wishes    = dv($_POST['extra_wishes'], null);

    // Стили музыки — массив → JSON
    $music_styles = $_POST['music_styles'] ?? [];
    if (!is_array($music_styles)) {
        $music_styles = [$music_styles];
    }
    $music_styles_json = empty($music_styles)
        ? null
        : json_encode(array_map(fn($s) => mb_substr(dv($s, ''), 0, 50), $music_styles), JSON_UNESCAPED_UNICODE);

    $tariff = in_array(dv($_POST['tariff'], 'basic'), ['basic', 'standard', 'premium', 'corporate', 'unknown'], true)
        ? dv($_POST['tariff'], 'basic')
        : 'basic';

    $client_name     = mb_substr(dv($_POST['client_name'], ''), 0, 255);
    $client_phone    = mb_substr(dv($_POST['client_phone'], ''), 0, 20);
    $client_telegram = mb_substr(dv($_POST['client_telegram'], ''), 0, 100);
    $client_whatsapp = mb_substr(dv($_POST['client_whatsapp'], ''), 0, 20);
    $client_ok       = mb_substr(dv($_POST['client_ok'], ''), 0, 255);
    $client_vk       = mb_substr(dv($_POST['client_vk'], ''), 0, 255);
    $client_email    = mb_substr(dv($_POST['client_email'], ''), 0, 150);
    $contact_time    = mb_substr(dv($_POST['contact_time'], ''), 0, 50);
    // форма шлёт "contact_method" → в БД "preferred_contact"
    $preferred_contact = mb_substr(dv($_POST['contact_method'], ''), 0, 50);

    // Технические поля
    $user_agent = mb_substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500);
    $referrer   = mb_substr((string)($_SERVER['HTTP_REFERER'] ?? ''), 0, 500); // null → '' (PHP8 TypeError!)
    $utm_source   = mb_substr((string)(dv($_POST['utm_source']   ?? $_GET['utm_source']   ?? null, '')), 0, 100);
    $utm_medium   = mb_substr((string)(dv($_POST['utm_medium']   ?? $_GET['utm_medium']   ?? null, '')), 0, 100);
    $utm_campaign = mb_substr((string)(dv($_POST['utm_campaign'] ?? $_GET['utm_campaign'] ?? null, '')), 0, 100);

    // ─── Вставка: временный уникальный номер, после — реальный по id ───
    $tmp_number = 'TMP-' . bin2hex(random_bytes(6));
    $stmt = $db->getPdo()->prepare("
        INSERT INTO orders
        (order_number, occasion, occasion_other, event_date, urgency,
         hero_name, hero_age, hero_relation, hero_profession, hero_hobbies,
         story, must_include, must_exclude, mood, music_styles, voice_type,
         tariff, extra_wishes,
         client_name, client_phone, client_telegram, client_whatsapp, client_ok, client_vk, client_email,
         contact_time, preferred_contact,
         ip_address, user_agent, referrer,
         utm_source, utm_medium, utm_campaign)
        VALUES
        (:order_number, :occasion, :occasion_other, :event_date, :urgency,
         :hero_name, :hero_age, :hero_relation, :hero_profession, :hero_hobbies,
         :story, :must_include, :must_exclude, :mood, :music_styles, :voice_type,
         :tariff, :extra_wishes,
         :client_name, :client_phone, :client_telegram, :client_whatsapp, :client_ok, :client_vk, :client_email,
         :contact_time, :preferred_contact,
         :ip_address, :user_agent, :referrer,
         :utm_source, :utm_medium, :utm_campaign)
    ");

    $stmt->execute([
        ':order_number'      => $tmp_number,
        ':occasion'          => $occasion,
        ':occasion_other'    => $occasion_other ?: null,
        ':event_date'        => $event_date,
        ':urgency'           => $urgency,
        ':hero_name'         => $hero_name,
        ':hero_age'          => $hero_age,
        ':hero_relation'     => $hero_relation ?: null,
        ':hero_profession'   => $hero_profession ?: null,
        ':hero_hobbies'      => $hero_hobbies ?: null,
        ':story'             => $story,
        ':must_include'      => $must_include,
        ':must_exclude'      => $must_exclude,
        ':mood'              => $mood ?: null,
        ':music_styles'      => $music_styles_json,
        ':voice_type'        => $voice_type ?: null,
        ':tariff'            => $tariff,
        ':extra_wishes'      => $extra_wishes,
        ':client_name'       => $client_name,
        ':client_phone'      => $client_phone,
        ':client_telegram'   => $client_telegram ?: null,
        ':client_whatsapp'   => $client_whatsapp ?: null,
        ':client_ok'         => $client_ok ?: null,
        ':client_vk'         => $client_vk ?: null,
        ':client_email'      => $client_email ?: null,
        ':contact_time'      => $contact_time ?: null,
        ':preferred_contact' => $preferred_contact ?: null,
        ':ip_address'        => $client_ip,
        ':user_agent'        => $user_agent,
        ':referrer'          => $referrer,
        ':utm_source'        => $utm_source,
        ':utm_medium'        => $utm_medium,
        ':utm_campaign'      => $utm_campaign,
    ]);

    $order_id = (int)$db->getPdo()->lastInsertId();

    // Номер заказа: HP-00001 (по id, гарантированно уникальный)
    $order_number = 'HP-' . str_pad((string)$order_id, 5, '0', STR_PAD_LEFT);
    $db->execute(
        "UPDATE orders SET order_number = :num WHERE id = :id",
        [':num' => $order_number, ':id' => $order_id]
    );

    echo json_encode([
        'success' => true,
        'message' => 'Заявка принята! Мы свяжемся в ближайшее время.',
        'order_number' => $order_number,
        'redirect' => '/thank-you.php?order=' . urlencode($order_number),
    ]);

} catch (Throwable $e) {
    log_error('api/submit-order: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
}