<?php
/**
 * Отправка email-уведомлений
 * Использует встроенный mail() или SMTP через сокеты
 *
 * Путь: /includes/mail.php
 */

declare(strict_types=1);

/**
 * Отправить email об новой заявке администратору
 *
 * @param array $order — данные заявки
 * @return bool
 */
function send_order_email(array $order): bool
{
    $to      = ADMIN_EMAIL;
    $subject = sprintf(
        'Новая заявка %s — %s (%s)',
        $order['order_number'],
        get_occasion_label($order['occasion']),
        get_tariff_label($order['tariff'])
    );

    $html = build_order_email_html($order);

    return send_html_email($to, $subject, $html);
}

/**
 * Отправить HTML email
 *
 * @param string $to
 * @param string $subject
 * @param string $html_body
 * @return bool
 */
function send_html_email(string $to, string $subject, string $html_body): bool
{
    $from_name  = SITE_NAME;
    $from_email = 'noreply@' . parse_url(SITE_URL, PHP_URL_HOST);

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: =?UTF-8?B?" . base64_encode($from_name) . "?= <{$from_email}>\r\n";
    $headers .= "Reply-To: {$from_email}\r\n";
    $headers .= "X-Mailer: PHP/" . PHP_VERSION . "\r\n";

    $encoded_subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

    $result = @mail($to, $encoded_subject, $html_body, $headers);

    $log_msg = sprintf(
        '[%s] Email to %s: %s — %s',
        date('Y-m-d H:i:s'),
        $to,
        $subject,
        $result ? 'OK' : 'FAILED'
    );

    log_to_file(LOG_PATH . '/mail.log', $log_msg);

    return $result;
}

/**
 * Сгенерировать HTML-тело письма с заявкой
 *
 * @param array $order
 * @return string
 */
function build_order_email_html(array $order): string
{
    $admin_link = SITE_URL . '/admin/order-view.php?id=' . (int)($order['id'] ?? 0);

    $rows = build_email_rows([
        ['Номер заявки',    $order['order_number']],
        ['Повод',           get_occasion_label($order['occasion']) . (
            $order['occasion'] === 'other' && !empty($order['occasion_other'])
                ? ': ' . $order['occasion_other'] : ''
        )],
        ['Дата мероприятия', $order['event_date'] ?: 'Не указана'],
        ['Срочность',        get_urgency_label($order['urgency'])],
        ['─────────────', '─────────────────────────────'],
        ['Имя героя',        $order['hero_name']],
        ['Возраст',          $order['hero_age'] ? $order['hero_age'] . ' лет' : 'Не указан'],
        ['Кем приходится',   $order['hero_relation'] ?: 'Не указано'],
        ['Профессия',        $order['hero_profession'] ?: 'Не указана'],
        ['Хобби',            $order['hero_hobbies'] ?: 'Не указаны'],
        ['─────────────', '─────────────────────────────'],
        ['История',          nl2br(htmlspecialchars($order['story'], ENT_QUOTES, 'UTF-8'))],
        ['Упомянуть',        $order['must_include'] ?: 'Не указано'],
        ['Избегать',         $order['avoid'] ?: 'Не указано'],
        ['─────────────', '─────────────────────────────'],
        ['Настроение',       $order['mood'] ?: 'Не указано'],
        ['Стиль музыки',     $order['music_styles'] ?: 'Не указан'],
        ['Голос',            $order['voice_type'] ?: 'Не указан'],
        ['Длительность',     get_duration_label($order['duration'])],
        ['─────────────', '─────────────────────────────'],
        ['Тариф',            get_tariff_label($order['tariff'])],
        ['Доп. пожелания',   $order['extra_wishes'] ?: 'Нет'],
        ['─────────────', '─────────────────────────────'],
        ['Имя клиента',      $order['client_name']],
        ['Телефон',          $order['client_phone']],
        ['Telegram',         $order['client_telegram'] ?: 'Не указан'],
        ['WhatsApp',         $order['client_whatsapp'] ?: 'Не указан'],
        ['ВКонтакте',        $order['client_vk'] ?: 'Не указан'],
        ['Одноклассники',    $order['client_ok'] ?: 'Не указан'],
        ['Email',            $order['client_email'] ?: 'Не указан'],
        ['Время для связи',  get_contact_time_label($order['contact_time'])],
        ['Способ связи',     $order['contact_method']],
    ]);

    return <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Новая заявка {$order['order_number']}</title>
</head>
<body style="margin:0;padding:0;background:#F5EFE6;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#F5EFE6;padding:24px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">

  <!-- Шапка -->
  <tr>
    <td style="background:linear-gradient(135deg,#6B1230,#8B1E3F);padding:32px;text-align:center;">
      <h1 style="color:#fff;font-size:24px;margin:0 0 8px;">🎵 Хитовая Песня</h1>
      <p style="color:rgba(255,255,255,0.8);margin:0;font-size:14px;">Новая заявка на создание песни</p>
    </td>
  </tr>

  <!-- Номер заявки -->
  <tr>
    <td style="background:#F0E6D2;padding:16px 32px;text-align:center;">
      <p style="margin:0;font-size:20px;font-weight:bold;color:#8B1E3F;">
        Заявка #{$order['order_number']}
      </p>
    </td>
  </tr>

  <!-- Данные -->
  <tr>
    <td style="padding:24px 32px;">
      <table width="100%" cellpadding="8" cellspacing="0">
        {$rows}
      </table>
    </td>
  </tr>

  <!-- Кнопка -->
  <tr>
    <td style="padding:0 32px 32px;text-align:center;">
      <a href="{$admin_link}"
         style="display:inline-block;background:#8B1E3F;color:#fff;text-decoration:none;
                padding:14px 32px;border-radius:12px;font-size:16px;font-weight:bold;">
        Открыть в админке
      </a>
    </td>
  </tr>

  <!-- Подвал -->
  <tr>
    <td style="background:#2C1810;padding:16px 32px;text-align:center;">
      <p style="color:rgba(255,255,255,0.5);font-size:12px;margin:0;">
        © Хитовая Песня · {$_SERVER['HTTP_HOST']}
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>
</body>
</html>
HTML;
}

/**
 * Построить строки таблицы для письма
 *
 * @param array $rows — массив [label, value]
 * @return string HTML
 */
function build_email_rows(array $rows): string
{
    $html = '';
    foreach ($rows as [$label, $value]) {
        if (str_starts_with((string)$label, '─')) {
            $html .= '<tr><td colspan="2" style="padding:4px 0;"><hr style="border:none;border-top:1px solid #E8D5B7;"></td></tr>';
            continue;
        }
        $html .= sprintf(
            '<tr>
                <td style="width:160px;color:#6B5D54;font-size:13px;vertical-align:top;padding:6px 12px 6px 0;white-space:nowrap;">%s</td>
                <td style="color:#2C1810;font-size:14px;vertical-align:top;padding:6px 0;">%s</td>
             </tr>',
            htmlspecialchars($label, ENT_QUOTES, 'UTF-8'),
            $value // value уже может содержать HTML (nl2br)
        );
    }
    return $html;
}

/* ─── Хелперы лейблов ─── */

function get_occasion_label(string $val): string
{
    return [
        'wedding'     => '💒 Свадьба',
        'anniversary' => '🎂 Юбилей',
        'birthday'    => '🎉 День рождения',
        'love'        => '💕 Годовщина',
        'corporate'   => '🏢 Корпоратив',
        'march8'      => '🌸 8 Марта',
        'feb23'       => '⚔️ 23 Февраля',
        'newyear'     => '🎄 Новый год',
        'proposal'    => '💍 Предложение',
        'birth'       => '👶 Рождение ребёнка',
        'retirement'  => '🏆 Выход на пенсию',
        'other'       => '✨ Другое',
    ][$val] ?? $val;
}

function get_tariff_label(string $val): string
{
    return [
        'basic'    => 'Базовый (2 500 ₽)',
        'standard' => 'Стандарт (5 000 ₽)',
        'premium'  => 'Премиум (10 000 ₽)',
        'help'     => 'Помогите выбрать',
    ][$val] ?? $val;
}

function get_urgency_label(string $val): string
{
    return [
        'normal' => 'Не срочно (5+ дней)',
        'fast'   => 'Быстро (3–4 дня)',
        'urgent' => 'Срочно (1–2 дня, +50%)',
        'asap'   => 'Очень срочно (сегодня–завтра, +100%)',
    ][$val] ?? $val;
}

function get_duration_label(string $val): string
{
    return [
        'short'    => 'Короткая (1.5–2 мин)',
        'standard' => 'Стандартная (2.5–3.5 мин)',
        'long'     => 'Длинная (4+ мин)',
    ][$val] ?? $val;
}

function get_contact_time_label(string $val): string
{
    return [
        'any'     => 'В любое время',
        'morning' => 'Утро (9–12)',
        'day'     => 'День (12–18)',
        'evening' => 'Вечер (18–22)',
    ][$val] ?? $val;
}