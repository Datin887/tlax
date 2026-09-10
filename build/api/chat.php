<?php
/**
 * API: Чат «Музыкальный продюсер» (AI)
 * Метод: POST
 * Тело:  JSON { message: string }
 * Ответ: JSON { success, reply, is_lead }
 *
 * Логика:
 * - Сессия $_SESSION['chat_history'] хранит последние 15 сообщений диалога
 * - Multi-provider: OpenRouter (основной) → DeepSeek (фолбэк) → статик-фолбэк
 * - SYSTEM_PROMPT — воронка продюсера (сбор брифа → тариф → контакты → лид)
 * - Тег [LEAD_CAPTURED] в ответе AI режется и превращается в флаг is_lead
 *
 * Ключи читаются из .env (env.config.php / tlax.env):
 *   OPENROUTER_API_KEY, OPENROUTER_MODEL
 *   DEEPSEEK_API_KEY,   DEEPSEEK_MODEL
 *
 * Путь: /api/chat.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';

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

// ─── Сессия (контекст диалога) ───
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['chat_history']) || !is_array($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// Фильтруем историю — только валидные записи user/assistant
$history = array_filter(
    $_SESSION['chat_history'],
    fn($m) =>
        is_array($m)
        && in_array((string)($m['role'] ?? ''), ['user', 'assistant'], true)
        && is_string($m['content'] ?? '')
);

// Добавляем сообщение пользователя
$history[] = ['role' => 'user', 'content' => $message];

// ─── Запрос к AI: OpenRouter → DeepSeek → null (фолбэк на статик) ───
$ai_reply = ai_chat_openrouter($history);
if ($ai_reply === null) {
    $ai_reply = ai_chat_deepseek($history);
}

// Фолбэк, если AI недоступен (или не настроены ключи)
if ($ai_reply === null) {
    log_error('ai_chat: все провайдеры недоступны — статик-фолбэк');
    $ai_reply = 'Отлично! Расскажите подробнее о главном герое праздника: как его зовут, сколько ему лет и кем он вам приходится?';
}

// ─── Сохраняем ответ в историю (лимит 15 сообщений) ───
$history[] = ['role' => 'assistant', 'content' => $ai_reply];
while (count($history) > 15) {
    array_shift($history);
}
$_SESSION['chat_history'] = $history;

// ─── Обработка [LEAD_CAPTURED] ───
$is_lead = false;
$reply = $ai_reply;

if (str_contains($reply, '[LEAD_CAPTURED]')) {
    $is_lead = true;
    // Внимание: в PHP 8.5 сигнатура str_replace(search, replace, subject)
    $reply = trim(str_replace('[LEAD_CAPTURED]', '', $reply));

    // ═══ ЛИД СОБРАН → сохраняем в БД (orders) ═══
    // Лид без телефона не сохраняем (save_ai_lead вернёт 0) — ждём номер.
    if (!($_SESSION['lead_saved'] ?? false)) {
        $lead_id = save_ai_lead($client_ip, $history);
        if ($lead_id > 0) {
            $_SESSION['lead_saved'] = true;
        }
    } else {
        log_error('save_ai_lead: повторный LEAD_CAPTURED в той же сессии — пропуск (уже сохранено)');
    }
}

send_json([
    'success' => true,
    'reply'   => $reply,
    'is_lead' => $is_lead,
]);


/**
 * SYSTEM_PROMPT «Музыкального продюсера» (общий для провайдеров).
 */
function ai_system_prompt(): string
{
    return <<<'PROMPT'
Ты — Музыкальный Продюсер сервиса «Хитовая Песня». Твоя задача — выяснить пожелания клиента для написания персональной песни на заказ (на свадьбу, юбилей, корпоратив) и закрыть его на заявку.
Ты общаешься живо, харизматично, с эмоциями, но не затягиваешь диалог.

ТВОЯ ВОРОНКА (СТРОГИЙ ПОРЯДОК):

ШАГ 1. Сбор фактов.
Задавай вопросы СТРОГО ПО ОДНОМУ за раз. Не вываливай список.
Что тебе нужно узнать (постепенно):
- Повод и Имя героя.
- 1-2 яркие детали (смешной случай, прозвище, любимая фраза), чтобы песня не была шаблонной.
- Жанр (поп, рок, рэп, шансон, лирика) и голос (мужской/женский).

ШАГ 2. Тарифы и УТП.
Как только собрал всю базу из Шага 1, похвали идею и предложи выбрать тариф:
- Базовый (2 500 ₽) — 1 трек, 1 правка.
- Стандарт (5 000 ₽) — 2-3 трека на выбор, 2 правки (Хит продаж).
- Премиум (10 000 ₽) — 5 вариантов, безлимит правок + видео с текстом.
ГЛАВНОЕ УТП (всегда упоминай): "Вы ничем не рискуете. У нас оплата только ПОСЛЕ того, как вы послушаете результат и он вам понравится!"

ШАГ 3. Контакты.
После выбора тарифа (или если клиент готов), запроси номер телефона или Telegram/WhatsApp. Скажи, что это нужно, чтобы скинуть демо-версию песни.

ШАГ 4. Финал.
Когда клиент написал свой номер телефона (ЦИФРЫ, например +7 999 123-45-67), поблагодари его и в КОНЦЕ своего ответа ОБЯЗАТЕЛЬНО напиши системную фразу (прямо в тексте): [LEAD_CAPTURED]

ВАЖНО ПРО ТЕГ [LEAD_CAPTURED]:
- НИКОГДА не используй [LEAD_CAPTURED], пока клиент НЕ дал номер телефона цифрами.
- Выбор тарифа — это ещё НЕ лид. Сначала спроси номер: "Чтобы скинуть демо, подскажите номер телефона или Telegram/WhatsApp" — и жди ответа.
- Только после получения номера (цифр) благодари и ставь тег. Один тег — один раз.

ЗАПРЕТЫ:
- Не задавай больше одного вопроса за раз.
- Не придумывай сам текст песни в чате (ты собираешь бриф, песню сделают в студии).
- Не меняй цены.
PROMPT;
}

/**
 * Общий вызов OpenAI-совместимого API.
 *
 * @param string $url       Endpoint
 * @param string $api_key   Ключ
 * @param array  $history   История [{role, content}]
 * @param string $model     Модель
 * @param string $provider  Название провайдера (для логов)
 * @return ?string Ответ ассистента, или null при ошибке
 */
function ai_call(string $url, string $api_key, array $history, string $model, string $provider): ?string
{
    if ($api_key === '') {
        log_error('ai_call: ' . $provider . ' — ключ не задан в .env');
        return null;
    }

    $messages = [['role' => 'system', 'content' => ai_system_prompt()]];
    foreach ($history as $m) {
        $messages[] = $m;
    }

    $body = json_encode([
        'model'       => $model,
        'messages'    => $messages,
        'temperature' => 0.7,
        'max_tokens'  => 400,
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    // Cloudflare (перед OpenRouter) блокирует запросы без браузерного User-Agent
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
        'HTTP-Referer: https://tlax.ru',
        'X-Title: Hitovaya Pesnya',
    ]);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');

    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $curl_err = curl_errno($ch);
    curl_close($ch);

    if ($resp === false || $code < 200 || $code >= 300) {
        log_error(sprintf(
            'ai_call: %s HTTP %d, curl_errno=%d, body=%s',
            $provider,
            $code,
            $curl_err,
            is_string($resp) ? mb_substr((string)$resp, 0, 200) : 'no response'
        ));
        return null;
    }

    $data = json_decode((string)$resp, true);
    if (!is_array($data)) {
        log_error('ai_call: ' . $provider . ' — некорректный JSON');
        return null;
    }

    $reply = $data['choices'][0]['message']['content'] ?? null;
    if (!is_string($reply) || trim($reply) === '') {
        log_error('ai_call: ' . $provider . ' — пустой ответ');
        return null;
    }

    return trim($reply);
}

/**
 * Запрос к OpenRouter AI.
 *
 * ⚠ Cloudflare блокирует прямой доступ к openrouter.ai с IP tlax.ru (5.35.100.174).
 * Запрос идёт через релей на owlex.top (из каталога /tlax-relay/relay.php):
 *   POST { model, messages, temperature, max_tokens } → { success, reply }
 * Релей добавляет ключ API сам (хранит его в relay_env.php на owlex.top).
 *
 * Адрес релея и токен — из .env:
 *   RELAY_URL   (по умолчанию https://owlex.top/tlax-relay/relay.php)
 *   RELAY_TOKEN (токен доступа к релею)
 *
 * Если релей недоступен — фолбэк на ai_call (прямой OpenRouter) и далее DeepSeek.
 *
 * @param array $history История диалога
 * @return ?string
 */
function ai_chat_openrouter(array $history): ?string
{
    $relay_url = (string)env('RELAY_URL', 'https://owlex.top/tlax-relay/relay.php');
    $relay_token = (string)env('RELAY_TOKEN', '');

    if ($relay_token === '') {
        log_error('ai_chat_openrouter: RELAY_TOKEN не задан в env');
        return ai_call(
            'https://openrouter.ai/api/v1/chat/completions',
            (string)env('OPENROUTER_API_KEY', ''),
            $history,
            (string)env('OPENROUTER_MODEL', 'google/gemini-2.5-flash'),
            'OpenRouter'
        );
    }

    // Собираем историю (system + диалог) — relay проксирует в OpenRouter as-is
    $messages = [['role' => 'system', 'content' => ai_system_prompt()]];
    foreach ($history as $m) {
        $messages[] = $m;
    }

    $body = json_encode([
        'model'       => (string)env('OPENROUTER_MODEL', 'google/gemini-2.5-flash'),
        'messages'    => $messages,
        'temperature' => 0.7,
        'max_tokens'  => 500,
    ], JSON_UNESCAPED_UNICODE);

    $ch = curl_init($relay_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Relay-Token: ' . $relay_token,
        'HTTP-Referer: https://tlax.ru',
        'X-Title: Hitovaya Pesnya',
    ]);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
    curl_setopt($ch, CURLOPT_TIMEOUT, 45);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');

    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $curl_err = curl_errno($ch);
    curl_close($ch);

    if ($resp === false || $code < 200 || $code >= 300) {
        log_error(sprintf(
            'ai_chat_openrouter: relay HTTP %d, curl_errno=%d, body=%s',
            $code,
            $curl_err,
            is_string($resp) ? mb_substr((string)$resp, 0, 200) : 'no response'
        ));
        return null;
    }

    $data = json_decode((string)$resp, true);
    $reply = $data['reply'] ?? null;
    if (!is_string($reply) || trim($reply) === '') {
        log_error('ai_chat_openrouter: relay вернул пустой/некорректный ответ');
        return null;
    }

    return trim($reply);
}

/**
 * Запрос к DeepSeek AI (фолбэк; endpoint доступен с этого сервера).
 *
 * @param array $history История диалога
 * @return ?string
 */
function ai_chat_deepseek(array $history): ?string
{
    return ai_call(
        'https://api.deepseek.com/chat/completions',
        (string)env('DEEPSEEK_API_KEY', ''),
        $history,
        (string)env('DEEPSEEK_MODEL', 'deepseek-chat'),
        'DeepSeek'
    );
}

/**
 * Сохранение лида из AI-чата в таблицу orders.
 * Парсит из истории диалога: телефон, повод, имя героя, жанр, тариф, полный диалог.
 *
 * @param string $ip      IP клиента
 * @param array  $history Итоговая история диалога [{role, content}]
 * @return int id новой записи, или 0 при ошибке
 */
function save_ai_lead(string $ip, array $history): int
{
    // ─── Собираем текст от пользователя и полный текст ───
    $user_msgs = [];
    $full_text = '';
    foreach ($history as $m) {
        $content = (string)($m['content'] ?? '');
        if ($content === '') continue;
        $full_text .= ' ' . $content;
        if (($m['role'] ?? '') === 'user') {
            $user_msgs[] = $content;
        }
    }
    $all = mb_strtolower($full_text);
    $user_joined = implode('. ', $user_msgs);
    // Для фактов о заказе (жанр, голос, тариф) — ТОЛЬКО слова клиента,
    // иначе AI сам перечислит варианты и получим ложные срабатывания
    $user_all = mb_strtolower($user_joined);

    // ─── Телефон (последний найденный) ───
    $phone = '';
    if (preg_match('/(\\+?[7-8][0-9\\s\\-()]{9,14})/u', $user_joined, $pm) === 1) {
        $phone = mb_substr(preg_replace('/\\D+/', '', $pm[1]), 0, 20);
    }

    // ─── Telegram / WhatsApp ───
    $telegram = '';
    $whatsapp = '';
    if (preg_match('/(?:@|t\\.me/)([a-zA-Z0-9_]{4,})/u', $user_joined, $pm) === 1) {
        $tg = $pm[1];
        if (preg_match('/^[0-9]+$/', $tg) !== 1) $telegram = $tg;
    }
    if (preg_match('/(?:wa\\.me/|whatsapp\\s*)([0-9\\+\\s-]{7,})/iu', $user_joined, $pm) === 1) {
        $whatsapp = mb_substr(preg_replace('/\\D+/', '', $pm[1]), 0, 20);
    }

    // ─── Повод ───
    $occasion = 'other';
    $occasion_order = [
        ['wedding',     ['свадьб', 'жених', 'невест', 'свадебн']],
        ['anniversary', ['годовщин', 'юбил', 'юбилей', 'год вместе']],
        ['birthday',    ['день рожд', 'др ', 'праздник']],
        ['corporate',   ['корпорат', 'коллектив', 'офис']],
        ['new_year',    ['новый год']],
        ['graduation',  ['выпускн', 'выпускн']],
    ];
    foreach ($occasion_order as $pair) {
        foreach ($pair[1] as $kw) {
            if (str_contains($all, $kw)) {
                $occasion = $pair[0];
                break 2; // break оба foreach
            }
        }
    }

    // ─── Имя героя: "зовут Сергей" / "брата Сергей" ───
    $hero_name = '';
    if (preg_match('/(?:зовут|звать|героя зовут)\\s+([А-ЯЁ][а-яё]+)/u', $full_text, $pm) === 1) {
        $hero_name = $pm[1];
    }
    if ($hero_name === '' && preg_match('/(?:брат|сестр|мам|пап|жен|муж|сын|доч|дяд|тёт|дед|бабушк|невест|жених|друг|коллег)\\s+([А-ЯЁ][а-яё]+)/u', $full_text, $pm) === 1) {
        $hero_name = $pm[1];
    }

    // ─── Возраст: "ему 35" / "ей 30" / "35 лет" ───
    $hero_age = 0;
    if (preg_match('/(\\d{1,2})\\s*(?:лет|год|года)/u', $all, $pm) === 1) {
        $hero_age = (int)$pm[1];
    }
    if ($hero_age === 0 && preg_match('/(?:ей|ему|ему)\\s+(\\d{1,2})/u', $user_all, $pm) === 1) {
        $hero_age = (int)$pm[1];
    }

    // ─── Кем приходится (ТОЛЬКО слова клиента; корень + русские окончания + граница слева) ───
    // Убираем "мужской/женский голос" — иначе "муж" от "мужской голос" даёт ложную связь
    $rel_text = str_replace('мужской голос', '', $user_all);
    $rel_text = str_replace('женский голос', '', $rel_text);
    $rel_map = [
        ['свекров', 'свекровь'], ['бабушк', 'бабушка'], ['дедушк', 'дедушка'],
        ['невест', 'невеста'],   ['коллег', 'коллега'], ['подруг', 'подруга'],
        ['сестр', 'сестра'],     ['жених', 'жених'],    ['мам', 'мама'],
        ['пап', 'папа'],         ['жен', 'жена'],       ['муж', 'муж'],
        ['сын', 'сын'],          ['доч', 'дочь'],       ['дяд', 'дядя'],
        ['тёт', 'тётя'],         ['брат', 'брат'],      ['друг', 'друг'],
    ];
    $hero_relation = '';
    foreach ($rel_map as $pair) {
        // Корень + до 4 букв окончания; слева НЕ буква (защита "обра..."→"брат")
        $pattern = '/(^|[^а-яё])' . $pair[0] . '[а-яё]{0,4}([^а-яё]|$)/u';
        if (preg_match($pattern, $rel_text) === 1) {
            $hero_relation = $pair[1];
            break;
        }
    }

    // ─── Хобби: "любит ..." / "увлекается ..." (только клиент) ───
    $hero_hobbies = '';
    if (preg_match('/(?:любит|увлекается|занимается) ([^.?!]{5,80})/u', $user_all, $pm) === 1) {
        $hero_hobbies = trim(mb_substr($pm[1], 0, 200));
    }
    if ($hero_hobbies === '' && preg_match('/(?:рокер|рыбак|спортсмен|танцор|музыкант)[^.,!?]{0,40}/u', $user_all, $pm) === 1) {
        $hero_hobbies = trim($pm[0]);
    }
    // Чистим хвост "...мужской голос" / "...женский голос"
    $hero_hobbies = str_replace('мужской голос', '', $hero_hobbies);
    $hero_hobbies = str_replace('женский голос', '', $hero_hobbies);
    $hero_hobbies = trim(preg_replace('/[,;\s]+$/', '', $hero_hobbies));

    // ─── Жанр (музыкальные стили, массив) — по тексту клиента ───
    $style_map = [
        ['рок',     'rock'],     ['рэп',  'rap'],
        ['хип-хоп','hip-hop'],   ['поп',  'pop'],
        ['шансон', 'chanson'],   ['лирик','lyrical'],
        ['джаз',   'jazz'],      ['фолк', 'folk'],
    ];
    $styles = [];
    foreach ($style_map as $pair) {
        if (str_contains($user_all, $pair[0]) && !in_array($pair[1], $styles, true)) {
            $styles[] = $pair[1];
        }
    }
    $music_styles = empty($styles) ? null : json_encode($styles);

    // ─── Голос (по тексту клиента) ───
    $voice_type = '';
    if (str_contains($user_all, 'муж')) $voice_type = 'male';
    elseif (str_contains($user_all, 'жен')) $voice_type = 'female';

    // ─── Тариф (по тексту клиента) ───
    $tariff = 'unknown';
    if (str_contains($user_all, 'корпорат')) $tariff = 'corporate';
    elseif (str_contains($user_all, 'преми') || str_contains($user_all, '10000') || str_contains($user_all, '10 000')) $tariff = 'premium';
    elseif (str_contains($user_all, 'стандарт') || str_contains($user_all, '5000') || str_contains($user_all, '5 000')) $tariff = 'standard';
    elseif (str_contains($user_all, 'базов') || str_contains($user_all, '2500') || str_contains($user_all, '2 500')) $tariff = 'basic';

    // ─── История (краткое изложение из сообщений клиента) ───
    $story = mb_substr($user_joined, 0, 3000);
    if ($story === '') $story = 'Заявка из AI-чата (без текста) ' . date('Y-m-d H:i');

    // ─── Полный диалог JSON ───
    $chat_dialog = json_encode(['history' => $history, 'saved_at' => date('Y-m-d H:i:s')]);

    // ─── Технические ───
    $ua = mb_substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500);
    $referrer = mb_substr((string)($_SERVER['HTTP_REFERER'] ?? ''), 0, 500);

    // ─── Принимаем лид ТОЛЬКО если есть телефон ───
    if ($phone === '') {
        log_error('save_ai_lead: [LEAD_CAPTURED] без телефона — лид не сохранён, ждём номер');
        return 0;
    }

    $tmp_number = 'TMP-C-' . bin2hex(random_bytes(6));

    try {
        $db = Database::getInstance();
        $order_id = $db->insert(
            "INSERT INTO orders
            (order_number, occasion, hero_name, hero_age, hero_relation,
             hero_hobbies, story, music_styles, voice_type, tariff,
             client_name, client_phone, client_telegram, client_whatsapp,
             chat_dialog, chat_source,
             ip_address, user_agent, referrer)
            VALUES
            (:order_number, :occasion, :hero_name, :hero_age, :hero_relation,
             :hero_hobbies, :story, :music_styles, :voice_type, :tariff,
             :client_name, :client_phone, :client_telegram, :client_whatsapp,
             :chat_dialog, 'ai_chat',
             :ip_address, :user_agent, :referrer)",
            [
                ':order_number'    => $tmp_number,
                ':occasion'        => $occasion,
                ':hero_name'       => $hero_name !== '' ? $hero_name : 'Не указан',
                ':hero_age'        => $hero_age > 0 ? $hero_age : null,
                ':hero_relation'   => $hero_relation !== '' ? $hero_relation : null,
                ':hero_hobbies'    => $hero_hobbies !== '' ? $hero_hobbies : null,
                ':story'           => $story,
                ':music_styles'    => $music_styles,
                ':voice_type'      => $voice_type !== '' ? $voice_type : null,
                ':tariff'          => $tariff,
                ':client_name'     => 'Клиент AI-чата',
                ':client_phone'    => $phone,
                ':client_telegram' => $telegram !== '' ? $telegram : null,
                ':client_whatsapp' => $whatsapp !== '' ? $whatsapp : null,
                ':chat_dialog'     => $chat_dialog,
                ':ip_address'      => $ip,
                ':user_agent'      => $ua,
                ':referrer'        => $referrer,
            ]
        );

        // Номер HP-XXXXX по id (как в submit-order)
        $db->execute(
            "UPDATE orders SET order_number = :num WHERE id = :id",
            [':num' => 'HP-' . str_pad((string)$order_id, 5, '0', STR_PAD_LEFT), ':id' => $order_id]
        );

        log_error(sprintf('save_ai_lead: OK id=%s phone=%s occasion=%s tariff=%s', $order_id, $phone, $occasion, $tariff));
        return (int)$order_id;
    } catch (Throwable $e) {
        log_error('save_ai_lead: ОШИБКА: ' . $e->getMessage());

        // Фолбэк — пишем в leads.log, чтобы не потерять лид
        $entry = json_encode([
            'ts'      => date('Y-m-d H:i:s'),
            'ip'      => $ip,
            'history' => $history,
        ]);
        $log_dir = APP_ROOT . '/logs';
        ensure_directory($log_dir, 0750);
        file_put_contents($log_dir . '/leads.log', $entry . "\n", FILE_APPEND | LOCK_EX);
        return 0;
    }
}