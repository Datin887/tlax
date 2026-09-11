<?php
/**
 * cron/healthcheck.php — агент мониторинга «Хитовая Песня» (tlax.ru)
 *
 * Запускается раз в 5 минут:
 *   - внешним cron (OpenClaw gateway): curl "https://tlax.ru/cron/healthcheck.php?token=..."
 *   - или вручную по HTTP (кнопка «Проверить сейчас» в админке)
 *   - или из CLI: /usr/bin/php /var/www/tlax_ru_usr/data/www/tlax.ru/cron/healthcheck.php
 *
 * Проверяет:
 *   1. MySQL БД (PDO, SELECT 1) — сервер жив, БД доступна.
 *   2. Веб-приложение (curl главной tlax.ru) — сайт отдаёт 200 + HTML.
 *   3. AI «Музыкальный продюсер» — POST-пинг релея на owlex.top
 *      (OpenRouter банит IP tlax.ru 5.35.100.174, поэтому напрямую нельзя).
 *      Релей отвечает {success, reply} → AI OK. Ошибка баланса/даун → СБОЙ.
 *   4. Баланс OpenRouter ($) — читает из storage/ai_balance.json
 *      (его обновляет внешний скрипт с gateway, чей IP не забанен).
 *
 * Пишет:
 *   - storage/health_status.json — статус для админки /admin/tech.php
 *   - storage/health_alerts.log   — только реальные сбои
 *
 * Скрипт самодостаточный (vanilla PHP). По HTTP защищён токеном
 * (равен RELAY_TOKEN из env) — чтобы его не дёргали чужие.
 */

declare(strict_types=1);

date_default_timezone_set('Europe/Moscow');

// ─── Вход в проект ───
require_once dirname(__DIR__) . '/includes/config.php';

const STATUS_FILE = APP_ROOT . '/storage/health_status.json';
const ALERT_LOG   = APP_ROOT . '/storage/health_alerts.log';
const BALANCE_FILE = APP_ROOT . '/storage/ai_balance.json';

// ─── Защита по HTTP: токен обязателен, если задан RELAY_TOKEN ───
$isCli = (PHP_SAPI === 'cli');
$guardToken = (string) env('RELAY_TOKEN', '');
if (!$isCli && $guardToken !== '') {
    $given = (string) ($_GET['token'] ?? '');
    if (!hash_equals($guardToken, $given)) {
        http_response_code(403);
        header('Content-Type: text/plain; charset=utf-8');
        echo "Forbidden\n";
        exit;
    }
}

/**
 * 1. Проверка БД (MySQL PDO, SELECT 1)
 */
function checkDb(): array
{
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s',
        DB_HOST, (int) DB_PORT, DB_NAME, DB_CHARSET);
    $t0 = microtime(true);
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 4,
        ]);
        $row = $pdo->query('SELECT 1')->fetch();
        return [
            'ok'      => $row !== false,
            'code'    => 'DB_OK',
            'error'   => null,
            'ping_ms' => round((microtime(true) - $t0) * 1000, 1),
        ];
    } catch (\Throwable $e) {
        return [
            'ok'      => false,
            'code'    => 'DB_DOWN',
            'error'   => $e->getMessage(),
            'ping_ms' => round((microtime(true) - $t0) * 1000, 1),
        ];
    }
}

/**
 * 2. Проверка веб-приложения: главная отдаёт 200 + HTML
 */
function checkWeb(): array
{
    $url = 'https://' . APP_DOMAIN . '/';
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 12,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT      => 'Tlax-Healthcheck/1.0',
    ]);
    $t0 = microtime(true);
    $resp = curl_exec($ch);
    $pingMs = round((microtime(true) - $t0) * 1000);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    $ok = ($code === 200) && is_string($resp) && str_contains($resp, '<!DOCTYPE html');
    return [
        'ok'      => $ok,
        'code'    => $ok ? 'WEB_OK' : 'WEB_FAIL',
        'error'   => $ok ? null : ('HTTP ' . $code . ($err !== '' ? ' / ' . $err : '')),
        'ping_ms' => $pingMs,
    ];
}

/**
 * 3. Проверка AI через релей (OpenRouter банит IP tlax.ru)
 *    Делаем минимальный запрос (max_tokens=1) — реальный E2E-пинг.
 */
function checkAi(): array
{
    $relayUrl   = (string) env('RELAY_URL', 'https://owlex.top/tlax-relay/relay.php');
    $relayToken = (string) env('RELAY_TOKEN', '');
    $model      = (string) env('OPENROUTER_MODEL', 'google/gemini-2.5-flash');

    if ($relayUrl === '' || $relayToken === '') {
        return ['ok' => false, 'code' => 'RELAY_CFG', 'error' => 'RELAY_URL/RELAY_TOKEN не заданы в env', 'ping_ms' => 0];
    }

    $body = json_encode([
        'model'    => $model,
        'messages' => [['role' => 'user', 'content' => 'ping. answer with one word: pong']],
        'temperature' => 0,
        'max_tokens'  => 2,
    ], JSON_UNESCAPED_UNICODE);

    $ch = curl_init($relayUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'X-Relay-Token: ' . $relayToken,
            'HTTP-Referer: https://tlax.ru',
            'X-Title: Hitovaya Pesnya',
        ],
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0 Safari/537.36',
        CURLOPT_CONNECTTIMEOUT => 8,
    ]);
    $t0 = microtime(true);
    $resp = curl_exec($ch);
    $pingMs = round((microtime(true) - $t0) * 1000);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($resp === false || $code < 200 || $code >= 300) {
        $reason = $curlErr !== '' ? $curlErr : 'HTTP ' . $code;
        // 402/недостаточно средств — ключевой сигнал «бюджет закончился»
        if ($code === 402 || stripos((string)$resp, 'insufficient') !== false || stripos((string)$resp, 'balance') !== false) {
            return ['ok' => false, 'code' => 'AI_BALANCE', 'error' => 'OpenRouter: недостаточно средств (бюджет исчерпан)', 'ping_ms' => $pingMs, 'via' => 'relay'];
        }
        return ['ok' => false, 'code' => 'AI_DOWN', 'error' => 'Релей AI не ответил: ' . $reason, 'ping_ms' => $pingMs, 'via' => 'relay'];
    }

    $data = json_decode((string)$resp, true);
    if (!is_array($data) || ($data['success'] ?? false) !== true) {
        return ['ok' => false, 'code' => 'AI_REPLY', 'error' => 'Релей вернул ошибку: ' . mb_substr((string)$resp, 0, 150), 'ping_ms' => $pingMs, 'via' => 'relay'];
    }

    return ['ok' => true, 'code' => 'AI_OK', 'error' => null, 'ping_ms' => $pingMs, 'via' => 'relay'];
}

/**
 * 4. Баланс OpenRouter ($) — из ai_balance.json (обновляет gateway).
 */
function readBalance(): array
{
    if (!file_exists(BALANCE_FILE)) {
        return ['available' => false, 'balance' => null];
    }
    $raw = file_get_contents(BALANCE_FILE);
    if ($raw === false) {
        return ['available' => false, 'balance' => null];
    }
    $d = json_decode($raw, true);
    if (!is_array($d)) {
        return ['available' => false, 'balance' => null];
    }
    $ts = (int) ($d['checked_ts'] ?? 0);
    $fresh = $ts > 0 && (time() - $ts) < 900; // свежее 15 мин
    return [
        'available' => $fresh,
        'stale'     => !$fresh,
        'balance'   => isset($d['balance']) ? (float) $d['balance'] : null,
        'total_credits' => isset($d['total_credits']) ? (float) $d['total_credits'] : null,
        'total_usage'   => isset($d['total_usage']) ? (float) $d['total_usage'] : null,
        'usage_daily'   => (float) ($d['usage_daily'] ?? 0.0),
        'usage_monthly' => (float) ($d['usage_monthly'] ?? 0.0),
        'checked_at'    => (string) ($d['checked_at'] ?? ''),
    ];
}

/**
 * Статистика бизнеса для «Технички» (заявки, треки).
 */
function readBizStats(): array
{
    try {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', DB_HOST, (int) DB_PORT, DB_NAME, DB_CHARSET);
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 4]);
        $o = $pdo->query("SELECT COUNT(*) c FROM orders")->fetch();
        $oToday = $pdo->query("SELECT COUNT(*) c FROM orders WHERE DATE(created_at) = CURDATE()")->fetch();
        $t = $pdo->query("SELECT COUNT(*) c FROM tracks")->fetch();
        return [
            'orders_total' => (int) ($o['c'] ?? 0),
            'orders_today' => (int) ($oToday['c'] ?? 0),
            'tracks'       => (int) ($t['c'] ?? 0),
        ];
    } catch (\Throwable $e) {
        return ['orders_total' => 0, 'orders_today' => 0, 'tracks' => 0];
    }
}

/**
 * Запись статуса на диск
 */
function writeStatus(array $status): void
{
    try {
        if (!is_dir(dirname(STATUS_FILE))) {
            @mkdir(dirname(STATUS_FILE), 0775, true);
        }
        file_put_contents(STATUS_FILE, json_encode($status, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    } catch (\Throwable $e) {
        error_log('[Healthcheck] writeStatus: ' . $e->getMessage());
    }
}

/**
 * Лог только реальных сбоев
 */
function appendAlert(string $line): void
{
    try {
        if (!is_dir(dirname(ALERT_LOG))) {
            @mkdir(dirname(ALERT_LOG), 0775, true);
        }
        $fh = @fopen(ALERT_LOG, 'a');
        if ($fh !== false) {
            fwrite($fh, $line . "\n");
            fclose($fh);
        }
    } catch (\Throwable $e) {
        // не критично
    }
}

// ─── MAIN ───
$failMode = (($_GET['fail'] ?? '') === '1'); // ?fail=1 — симулировать сбой AI (тест)

$db  = checkDb();
$ai  = checkAi();
$web = checkWeb();
$bal = readBalance();
$biz = readBizStats();

if ($failMode) {
    $ai = ['ok' => false, 'code' => 'AI_DOWN', 'error' => 'Тест: смоделированный сбой AI', 'ping_ms' => 0, 'via' => 'relay'];
}

$hadError = (!$db['ok']) || (!$ai['ok']) || (!$web['ok']);

$status = [
    'ok_total'   => !$hadError,
    'db'         => $db,
    'ai'         => $ai,
    'web'        => $web,
    'checked_at' => date('Y-m-d H:i:s'),
    'checked_ts' => time(),
    // Live
    'db_ping_ms' => (float) ($db['ping_ms'] ?? 0.0),
    'ai_ping_ms' => (float) ($ai['ping_ms'] ?? 0.0),
    'ai_model'   => (string) env('OPENROUTER_MODEL', 'google/gemini-2.5-flash'),
    'ai_via'     => 'relay',
    // Баланс OpenRouter (из gateway ai_balance.json)
    'ai_balance'        => $bal['balance'],
    'ai_balance_avail'  => $bal['available'],
    'ai_balance_stale'  => (bool) ($bal['stale'] ?? false),
    'ai_balance_at'     => $bal['checked_at'] ?? '',
    'ai_total_credits'  => $bal['total_credits'] ?? null,
    'ai_total_usage'    => $bal['total_usage'] ?? null,
    'ai_usage_daily'    => $bal['usage_daily'] ?? 0.0,
    'ai_usage_monthly'  => $bal['usage_monthly'] ?? 0.0,
    // Бизнес-статистика
    'orders_total' => $biz['orders_total'],
    'orders_today' => $biz['orders_today'],
    'tracks'       => $biz['tracks'],
    // Тарифы из config
    'prices' => defined('TARIFFS') ? TARIFFS : [],
    'tg_configured' => false,
];

writeStatus($status);

if ($hadError) {
    appendAlert(date('Y-m-d H:i:s') . ' | ' . ($db['ok'] ? 'DB=OK' : 'DB=FAIL:' . ($db['code'] ?? '')) . ' | ' . ($ai['ok'] ? 'AI=OK' : 'AI=FAIL:' . ($ai['code'] ?? '')) . ' | ' . ($web['ok'] ? 'WEB=OK' : 'WEB=FAIL:' . ($web['code'] ?? '')));
}

// ─── Вывод (виден в логах cron / при ручном запуске) ───
if (!$isCli) {
    header('Content-Type: text/plain; charset=utf-8');
}
echo 'HEALTHCHECK Хитовая Песня ' . ($hadError ? 'FAIL' : 'OK') . ' @ ' . $status['checked_at'] . "\n";
echo '  DB:  ' . ($db['ok'] ? 'OK' : 'FAIL [' . ($db['code'] ?? '') . '] ' . (string) $db['error']) . "\n";
echo '  AI:  ' . ($ai['ok'] ? 'OK (via relay, ' . (int) $ai['ping_ms'] . ' ms)' : 'FAIL [' . ($ai['code'] ?? '') . '] ' . (string) $ai['error']) . "\n";
echo '  WEB: ' . ($web['ok'] ? 'OK (' . (int) $web['ping_ms'] . ' ms)' : 'FAIL [' . ($web['code'] ?? '') . '] ' . (string) ($web['error'] ?? '')) . "\n";
echo '  BAL: ' . ($bal['available'] ? '$' . number_format((float) $bal['balance'], 2) : ($bal['stale'] ? 'устарел' : 'нет данных')) . "\n";
