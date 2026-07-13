<?php
/**
 * API: Приём и сохранение заявки на песню
 * Метод: POST
 * Возвращает JSON
 *
 * Путь: /public/api/submit-order.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../../includes/mail.php';
require_once __DIR__ . '/../../includes/telegram.php';

// ─── Только AJAX POST ───
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['success' => false, 'message' => 'Method not allowed'], 405);
}

if (!is_ajax_request()) {
    send_json(['success' => false, 'message' => 'Forbidden'], 403);
}

// ─── Rate limiting: 5 заявок с одного IP в час ───
$client_ip = get_client_ip();
if (!check_rate_limit('submit_order_' . $client_ip, 5, 3600)) {
    send_json([
        'success' => false,
        'message' => 'Слишком много заявок. Пожалуйста, подождите немного.',
        'errors'  => [],
    ], 429);
}

// ─── Honeypot: поле "website" должно быть пустым ───
if (!empty($_POST['website'])) {
    // Бот — молча возвращаем "успех"
    send_json(['success' => true, 'order_number' => 'HP-00000', 'redirect' => '/thank-you.php?order=HP-00000']);
}

// ─── Защита от слишком быстрой отправки (< 10 секунд) ───
$form_start_time = (int)($_POST['form_start_time'] ?? 0);
if ($form_start_time > 0) {
    $elapsed_ms = (int)(microtime(true) * 1000) - $form_start_time;
    if ($elapsed_ms < 10000) { // < 10 секунд
        log_error("submit-order: подозрение на бота, IP={$client_ip}, время={$elapsed_ms}ms");
        // Не отказываем явно, но логируем
    }
}

// ─── CSRF-валидация ───
$csrf_token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($csrf_token)) {
    send_json(['success' => false, 'message' => 'Недействительный токен безопасности. Обновите страницу.'], 403);
}

// ─── Сбор и санитизация данных ───
$data = [
    // Шаг 1
    'occasion'         => sanitize_string($_POST['occasion']       ?? '', 50),
    'occasion_other'   => sanitize_string($_POST['occasion_other'] ?? '', 100),
    'event_date'       => sanitize_date($_POST['event_date']       ?? ''),
    'urgency'          => sanitize_string($_POST['urgency']        ?? 'normal', 20),

    // Шаг 2
    'hero_name'        => sanitize_string($_POST['hero_name']      ?? '', 100),
    'hero_age'         => (int)($_POST['hero_age'] ?? 0),
    'hero_relation'    => sanitize_string($_POST['hero_relation']  ?? '', 100),
    'hero_profession'  => sanitize_string($_POST['hero_profession']?? '', 100),
    'hero_hobbies'     => sanitize_string($_POST['hero_hobbies']   ?? '', 200),

    // Шаг 3
    'story'            => sanitize_text($_POST['story']            ?? '', 3000),
    'must_include'     => sanitize_text($_POST['must_include']     ?? '', 1000),
    'avoid'            => sanitize_text($_POST['avoid']            ?? '', 500),

    // Шаг 4
    'mood'             => sanitize_string($_POST['mood']           ?? '', 30),
    'music_styles'     => sanitize_array($_POST['music_styles']    ?? [], 11, 50),
    'voice_type'       => sanitize_string($_POST['voice_type']     ?? '', 20),
    'duration'         => sanitize_string($_POST['duration']       ?? 'standard', 20),

    // Шаг 5
    'tariff'           => sanitize_string($_POST['tariff']         ?? '', 20),
    'extra_wishes'     => sanitize_text($_POST['extra_wishes']     ?? '', 1000),

    // Шаг 6
    'client_name'      => sanitize_string($_POST['client_name']    ?? '', 100),
    'client_phone'     => sanitize_phone($_POST['client_phone']    ?? ''),
    'client_telegram'  => sanitize_string($_POST['client_telegram']?? '', 100),
    'client_whatsapp'  => sanitize_phone($_POST['client_whatsapp'] ?? ''),
    'client_vk'        => sanitize_url($_POST['client_vk']         ?? ''),
    'client_ok'        => sanitize_url($_POST['client_ok']         ?? ''),
    'client_email'     => sanitize_email($_POST['client_email']    ?? ''),
    'contact_time'     => sanitize_string($_POST['contact_time']   ?? 'any', 20),
    'contact_method'   => sanitize_string($_POST['contact_method'] ?? 'phone', 20),

    // Системные
    'ip_address'       => $client_ip,
    'user_agent'       => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
];

// ─── Валидация ───
$errors = [];

// Повод
$allowed_occasions = [
    'wedding','anniversary','birthday','love','corporate',
    'march8','feb23','newyear','proposal','birth','retirement','other'
];
if (empty($data['occasion']) || !in_array($data['occasion'], $allowed_occasions, true)) {
    $errors['occasion'] = 'Выберите повод для песни';
}

// Если "другое" — нужен текст
if ($data['occasion'] === 'other' && empty($data['occasion_other'])) {
    $errors['occasion_other'] = 'Опишите повод';
}

// Дата мероприятия (если указана — должна быть в будущем)
if (!empty($data['event_date'])) {
    $event_ts = strtotime($data['event_date']);
    if ($event_ts === false || $event_ts < strtotime('today')) {
        $errors['event_date'] = 'Укажите дату в будущем';
    }
}

// Имя героя
if (mb_strlen($data['hero_name'], 'UTF-8') < 2) {
    $errors['hero_name'] = 'Введите имя героя';
}

// История
if (mb_strlen($data['story'], 'UTF-8') < 50) {
    $errors['story'] = 'Расскажите историю подробнее (минимум 50 символов)';
}

// Имя клиента
if (mb_strlen($data['client_name'], 'UTF-8') < 2) {
    $errors['client_name'] = 'Введите ваше имя';
}

// Телефон
$phone_digits = preg_replace('/\D/', '', $data['client_phone']);
if (strlen($phone_digits) < 11) {
    $errors['client_phone'] = 'Введите корректный номер телефона';
}

// Email (если указан)
if (!empty($data['client_email']) && !filter_var($data['client_email'], FILTER_VALIDATE_EMAIL)) {
    $errors['client_email'] = 'Введите корректный email';
}

// Согласие с политикой
if (empty($_POST['agree_policy'])) {
    $errors['agree_policy'] = 'Необходимо согласие с политикой конфиденциальности';
}

if (!empty($errors)) {
    send_json(['success' => false, 'message' => 'Исправьте ошибки в форме', 'errors' => $errors], 422);
}

// ─── Сохранение в БД ───
try {
    $db = Database::getInstance();

    // Генерируем номер заявки
    $order_number = generate_order_number();

    // Сериализуем массивы
    $music_styles_str = implode(', ', $data['music_styles']);

    $order_id = $db->insert(
        "INSERT INTO orders (
            order_number, occasion, occasion_other, event_date, urgency,
            hero_name, hero_age, hero_relation, hero_profession, hero_hobbies,
            story, must_include, avoid,
            mood, music_styles, voice_type, duration,
            tariff, extra_wishes,
            client_name, client_phone, client_telegram, client_whatsapp,
            client_vk, client_ok, client_email,
            contact_time, contact_method,
            status, ip_address, user_agent, created_at
        ) VALUES (
            :order_number, :occasion, :occasion_other, :event_date, :urgency,
            :hero_name, :hero_age, :hero_relation, :hero_profession, :hero_hobbies,
            :story, :must_include, :avoid,
            :mood, :music_styles, :voice_type, :duration,
            :tariff, :extra_wishes,
            :client_name, :client_phone, :client_telegram, :client_whatsapp,
            :client_vk, :client_ok, :client_email,
            :contact_time, :contact_method,
            'new', :ip_address, :user_agent, NOW()
        )",
        [
            ':order_number'   => $order_number,
            ':occasion'       => $data['occasion'],
            ':occasion_other' => $data['occasion_other'],
            ':event_date'     => $data['event_date'] ?: null,
            ':urgency'        => $data['urgency'],
            ':hero_name'      => $data['hero_name'],
            ':hero_age'       => $data['hero_age'] ?: null,
            ':hero_relation'  => $data['hero_relation'],
            ':hero_profession'=> $data['hero_profession'],
            ':hero_hobbies'   => $data['hero_hobbies'],
            ':story'          => $data['story'],
            ':must_include'   => $data['must_include'],
            ':avoid'          => $data['avoid'],
            ':mood'           => $data['mood'],
            ':music_styles'   => $music_styles_str,
            ':voice_type'     => $data['voice_type'],
            ':duration'       => $data['duration'],
            ':tariff'         => $data['tariff'],
            ':extra_wishes'   => $data['extra_wishes'],
            ':client_name'    => $data['client_name'],
            ':client_phone'   => $data['client_phone'],
            ':client_telegram'=> $data['client_telegram'],
            ':client_whatsapp'=> $data['client_whatsapp'],
            ':client_vk'      => $data['client_vk'],
            ':client_ok'      => $data['client_ok'],
            ':client_email'   => $data['client_email'],
            ':contact_time'   => $data['contact_time'],
            ':contact_method' => $data['contact_method'],
            ':ip_address'     => $data['ip_address'],
            ':user_agent'     => $data['user_agent'],
        ]
    );

    // ─── Логируем создание ───
    $db->execute(
        "INSERT INTO order_logs (order_id, action, new_status, note, created_at)
         VALUES (:order_id, 'created', 'new', 'Заявка создана через сайт', NOW())",
        [':order_id' => $order_id]
    );

    // ─── Уведомления (не блокируем ответ при ошибке) ───
    $order_data = array_merge($data, [
        'id'           => $order_id,
        'order_number' => $order_number,
    ]);

    try {
        send_order_email($order_data);
    } catch (Exception $e) {
        log_error('submit-order: email failed: ' . $e->getMessage());
    }

    try {
        send_order_telegram($order_data);
    } catch (Exception $e) {
        log_error('submit-order: telegram failed: ' . $e->getMessage());
    }

    // ─── Успех ───
    send_json([
        'success'      => true,
        'order_number' => $order_number,
        'redirect'     => '/thank-you.php?order=' . urlencode($order_number),
        'message'      => 'Заявка принята!',
    ]);

} catch (Exception $e) {
    log_error('submit-order: DB error: ' . $e->getMessage());
    send_json([
        'success' => false,
        'message' => 'Произошла ошибка при сохранении заявки. Пожалуйста, попробуйте снова или свяжитесь с нами напрямую.',
    ], 500);
}