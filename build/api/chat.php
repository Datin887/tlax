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

    // ═══ ЛИД СОБРАН (заглушка) ═══
    // TODO: Задача №3 — сохранять бриф в таблицу orders/CRM + уведомление менеджеру.
    log_lead_captured($client_ip, $history);
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
Когда клиент написал свой номер телефона, поблагодари его и в КОНЦЕ своего ответа ОБЯЗАТЕЛЬНО напиши системную фразу (прямо в тексте): [LEAD_CAPTURED]

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
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
        'HTTP-Referer: https://tlax.ru',
        'X-Title: Hitovaya Pesnya',
    ]);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'tlax.ru-chat-bot');

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
 * @param array $history История диалога
 * @return ?string
 */
function ai_chat_openrouter(array $history): ?string
{
    return ai_call(
        'https://openrouter.ai/api/v1/chat/completions',
        (string)env('OPENROUTER_API_KEY', ''),
        $history,
        (string)env('OPENROUTER_MODEL', 'google/gemini-2.5-flash'),
        'OpenRouter'
    );
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
 * Лог собранного лида (заглушка под CRM).
 *
 * @param string $ip      IP клиента
 * @param array  $history Итоговая история диалога
 */
function log_lead_captured(string $ip, array $history): void
{
    $entry = json_encode([
        'ts'      => date('Y-m-d H:i:s'),
        'ip'      => $ip,
        'history' => $history,
    ]);

    $log_dir = APP_ROOT . '/logs';
    ensure_directory($log_dir, 0750);

    file_put_contents($log_dir . '/leads.log', $entry . "\n", FILE_APPEND | LOCK_EX);

    log_error('LEAD_CAPTURED: ip=' . $ip . ' msgs=' . count($history));
}