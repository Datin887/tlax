<?php
/**
 * Отправка уведомлений через Telegram Bot API
 *
 * Путь: /includes/telegram.php
 */

declare(strict_types=1);

/**
 * Отправить уведомление о заявке в Telegram
 *
 * @param array $order
 * @return bool
 */
function send_order_telegram(array $order): bool
{
    if (empty(TELEGRAM_BOT_TOKEN) || empty(TELEGRAM_CHAT_ID)) {
        return false;
    }

    $admin_link = SITE_URL . '/admin/order-view.php?id=' . (int)($order['id'] ?? 0);

    $occasion  = get_occasion_label($order['occasion']);
    if ($order['occasion'] === 'other' && !empty($order['occasion_other'])) {
        $occasion .= ': ' . $order['occasion_other'];
    }

    $tariff   = get_tariff_label($order['tariff']);
    $urgency  = get_urgency_label($order['urgency']);

    // Формируем сообщение в формате MarkdownV2
    $lines = [
        '🎵 *Новая заявка ' . escape_tg($order['order_number']) . '*',
        '',
        '📋 *Повод:* ' . escape_tg($occasion),
        '⚡ *Срочность:* ' . escape_tg($urgency),
        '💼 *Тариф:* ' . escape_tg($tariff),
        '',
        '👤 *Герой:* ' . escape_tg($order['hero_name'])
            . ($order['hero_age'] ? ', ' . (int)$order['hero_age'] . ' лет' : ''),
        '',
        '📞 *Клиент:* ' . escape_tg($order['client_name']),
        '📱 *Телефон:* ' . escape_tg($order['client_phone']),
    ];

    if (!empty($order['client_telegram'])) {
        $lines[] = '✈️ *Telegram:* ' . escape_tg($order['client_telegram']);
    }
    if (!empty($order['client_whatsapp'])) {
        $lines[] = '💚 *WhatsApp:* ' . escape_tg($order['client_whatsapp']);
    }
    if (!empty($order['event_date'])) {
        $lines[] = '📅 *Дата:* ' . escape_tg(date('d.m.Y', strtotime($order['event_date'])));
    }

    $lines[] = '';
    $lines[] = '🔗 [Открыть в админке](' . $admin_link . ')';

    $text = implode("\n", $lines);

    return send_telegram_message($text, [
        'parse_mode'              => 'MarkdownV2',
        'disable_web_page_preview'=> true,
    ]);
}

/**
 * Отправить сообщение через Telegram Bot API
 *
 * @param string $text
 * @param array  $extra — доп. параметры
 * @return bool
 */
function send_telegram_message(string $text, array $extra = []): bool
{
    $url = 'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage';

    $payload = array_merge([
        'chat_id' => TELEGRAM_CHAT_ID,
        'text'    => $text,
    ], $extra);

    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

    // Используем cURL если доступен
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $json,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        $err      = curl_errno($ch);
        curl_close($ch);

        if ($err) {
            log_error('telegram: cURL error ' . $err);
            return false;
        }
    } else {
        // Фолбэк: file_get_contents
        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => $json,
                'timeout' => 10,
            ],
        ]);
        $response = @file_get_contents($url, false, $context);
    }

    if (!$response) {
        log_error('telegram: нет ответа от API');
        return false;
    }

    $result = json_decode($response, true);
    $ok     = (bool)($result['ok'] ?? false);

    if (!$ok) {
        log_error('telegram: ошибка API: ' . ($result['description'] ?? 'unknown'));
    }

    $log = sprintf('[%s] Telegram: %s', date('Y-m-d H:i:s'), $ok ? 'OK' : 'FAILED');
    log_to_file(LOG_PATH . '/mail.log', $log);

    return $ok;
}

/**
 * Экранирование спецсимволов для MarkdownV2
 *
 * @param string $text
 * @return string
 */
function escape_tg(string $text): string
{
    $chars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
    foreach ($chars as $char) {
        $text = str_replace($char, '\\' . $char, $text);
    }
    return $text;
}