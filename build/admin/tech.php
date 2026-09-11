<?php
/**
 * Админка «Хитовая Песня» — Техничка (мониторинг системы)
 * Путь: /admin/tech.php
 *
 * Читает storage/health_status.json (пишет cron/healthcheck.php) и
 * показывает: пилюли здоровья БД/AI/WEB, красный баннер сбоя, блок
 * «Расход AI» (баланс OpenRouter) и «Диагностику Системы».
 */

declare(strict_types=1);

define('IN_ADMIN', true);
require_once __DIR__ . '/includes/auth.php';
require_auth();

$page_title = 'Техничка';

// ─── Читаем статус ───
$health = [];
$statusFile = APP_ROOT . '/storage/health_status.json';
if (file_exists($statusFile)) {
    $raw = file_get_contents($statusFile);
    if ($raw !== false) {
        $dec = json_decode($raw, true);
        if (is_array($dec)) $health = $dec;
    }
}

// Свежесть: статус обновляется каждые 5 мин → старше 12 мин = проблема cron
$checkedTs = (int) ($health['checked_ts'] ?? 0);
$fresh = ($checkedTs > 0) && (time() - $checkedTs) < 720;
$stale = ($checkedTs > 0) && (time() - $checkedTs) >= 720;

$db  = $health['db']  ?? ['ok' => null, 'ping_ms' => 0];
$ai  = $health['ai']  ?? ['ok' => null, 'ping_ms' => 0];
$web = $health['web'] ?? ['ok' => null, 'ping_ms' => 0];

$prices = $health['prices'] ?? [];
$orderStats = [
    'orders_total' => (int) ($health['orders_total'] ?? 0),
    'orders_today' => (int) ($health['orders_today'] ?? 0),
    'tracks'       => (int) ($health['tracks'] ?? 0),
];

// Баланс OpenRouter
$bal = (float) ($health['ai_balance'] ?? 0.0);
$balAvail = (bool) ($health['ai_balance_avail'] ?? false);
$balStale = (bool) ($health['ai_balance_stale'] ?? false);
$totalCredits = $health['ai_total_credits'] ?? null;
$totalUsage   = $health['ai_total_usage'] ?? null;
$usageDaily   = (float) ($health['ai_usage_daily'] ?? 0.0);
$usageMonthly = (float) ($health['ai_usage_monthly'] ?? 0.0);
$model = (string) ($health['ai_model'] ?? 'google/gemini-2.5-flash');

// Прогресс-бар расхода
$spentPct = 0;
if ($totalCredits !== null && $totalCredits > 0) {
    $used = ($totalUsage !== null) ? $totalUsage : ($totalCredits - $bal);
    $spentPct = min(100, max(0, (float) ($used / $totalCredits * 100)));
}

// Релей-токен для кнопки «Проверить сейчас»
$relayToken = (string) env('RELAY_TOKEN', '');

require_once __DIR__ . '/includes/admin-header.php';
?>

<style>
    /* ─── Техничка: блоки в светлой теме ТЛAX ─── */
    .tech-pills { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
    .t-pill { display: inline-flex; align-items: center; gap: 7px; padding: 9px 16px; border-radius: var(--radius-full); font-size: 14px; font-weight: 700; }
    .t-pill--ok    { background: var(--color-success-light); color: var(--color-success); border: 1px solid rgba(74,124,89,.3); }
    .t-pill--err   { background: var(--color-error-light);   color: var(--color-error);   border: 1px solid rgba(179,57,81,.35); }
    .t-pill--na    { background: #EEF0F3;                    color: #7C858E;              border: 1px solid #E1E5EA; }

    .alert-banner {
        background: linear-gradient(90deg, var(--color-error), #8B1E3F);
        color: #fff; border-radius: var(--radius-lg); padding: 16px 20px; margin-bottom: 20px;
        box-shadow: 0 8px 30px rgba(139,30,63,.3);
    }
    .alert-banner__title { font-family: var(--font-heading); font-size: 16px; font-weight: 800; }
    .alert-banner__sub   { font-size: 14px; margin-top: 6px; color: #FADDE4; }
    .alert-banner__time  { font-size: 12px; margin-top: 4px; color: #FADDE4; }

    .stale-note {
        background: var(--color-warning-light); color: var(--color-warning);
        border: 1px solid rgba(193,123,46,.35); border-radius: var(--radius-md);
        padding: 12px 16px; font-size: 13px; margin-bottom: 20px;
    }

    .tech-card { background: #fff; border: 1px solid var(--color-border-light); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 20px; box-shadow: var(--shadow-xs); }
    .tech-card__head { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
    .tech-card__title { font-family: var(--font-heading); font-size: 15px; font-weight: 700; color: var(--color-text); }
    .tech-card__badge { font-size: 12px; color: var(--color-text-light); }

    .balance { display: flex; align-items: baseline; gap: 8px; }
    .balance__num { font-family: var(--font-heading); font-size: 26px; font-weight: 800; }
    .balance__ok   { color: var(--color-success); }
    .balance__warn { color: var(--color-warning); }
    .balance__err  { color: var(--color-error); }
    .balance__lbl  { font-size: 12px; color: var(--color-text-light); }

    .spend-bar { height: 8px; border-radius: 6px; background: #F0E6D2; margin: 12px 0 16px; overflow: hidden; }
    .spend-bar__fill { height: 100%; border-radius: 6px; background: linear-gradient(90deg, var(--color-success), var(--color-primary)); transition: width .4s; }

    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; }
    .item { background: var(--color-bg-section); border: 1px solid var(--color-border-light); border-radius: var(--radius-md); padding: 12px 14px; font-size: 13px; line-height: 1.6; }
    .item b { color: var(--color-text); }
    .item .ok   { color: var(--color-success); font-weight: 700; }
    .item .err  { color: var(--color-error); font-weight: 700; }
    .item .warn { color: var(--color-warning); font-weight: 700; }
    .item .dim  { color: var(--color-text-light); }

    .check-now { display: inline-flex; align-items: center; gap: 8px; padding: 11px 20px; background: var(--color-primary); color: #fff; border: none; border-radius: var(--radius-md); font-size: 14px; font-weight: 700; cursor: pointer; }
    .check-now:hover { background: var(--color-primary-dark); }
    .check-now[disabled] { opacity: .6; cursor: wait; }
    .check-hint { font-size: 12px; color: var(--color-text-light); }
</style>

<?php if (($health['ok_total'] ?? true) === false): ?>
    <div class="alert-banner">
        <div class="alert-banner__title">🚨 ХИТОВАЯ ПЕСНЯ — СБОЙ: <?= htmlspecialchars((string)($health['ai']['error'] ?? ($health['db']['error'] ?? ($health['web']['error'] ?? 'СИСТЕМА')))) ?></div>
        <div class="alert-banner__sub">
            <?php
                $parts = [];
                if (!($db['ok'] ?? false)) $parts[] = 'БД';
                if (!($ai['ok'] ?? false)) $parts[] = 'AI';
                if (!($web['ok'] ?? false)) $parts[] = 'WEB';
                echo 'Не работают: ' . implode(', ', $parts ?: ['?']);
            ?>
        </div>
        <div class="alert-banner__time">⏱ Зафиксировано: <?= htmlspecialchars((string)($health['checked_at'] ?? '')) ?> (МСК)</div>
    </div>
<?php endif; ?>
<?php if ($stale): ?>
    <div class="stale-note">⚠️ Мониторинг не обновлялся более 12 минут — проверьте, что cron healthcheck.php запущен (внешний cron или FastPanel).</div>
<?php endif; ?>

<div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:20px;">
    <button class="check-now" id="btnCheck">🔄 Проверить сейчас</button>
    <span class="check-hint">healthcheck обновляется каждые 5 мин по cron; «Проверить сейчас» запустит мгновенно.</span>
    <span class="check-hint" id="checkResult"></span>
</div>

<div class="tech-pills">
    <?php
        $mk = fn($ok) => $ok === true ? 'ok' : ($ok === false ? 'err' : 'na');
        $lb = fn($ok) => $ok === true ? 'OK' : ($ok === false ? 'СБОЙ' : '—');
        $ic = fn($ok) => $ok === true ? '🟢' : ($ok === false ? '🔴' : '⚪');
    ?>
    <span class="t-pill t-pill--<?= $mk($db['ok'] ?? null) ?>"><?= $ic($db['ok'] ?? null) ?> БД: <?= $lb($db['ok'] ?? null) ?></span>
    <span class="t-pill t-pill--<?= $mk($ai['ok'] ?? null) ?>"><?= $ic($ai['ok'] ?? null) ?> AI API: <?= $lb($ai['ok'] ?? null) ?></span>
    <span class="t-pill t-pill--<?= $mk($web['ok'] ?? null) ?>"><?= $ic($web['ok'] ?? null) ?> WEB: <?= $lb($web['ok'] ?? null) ?></span>
</div>

<!-- 💰 Расход AI -->
<?php
    $balClass = $bal > 5 ? 'ok' : ($bal > 1 ? 'warn' : 'err');
    $balTxt = $balAvail ? ('$' . number_format($bal, 2, '.', ' ')) : ($balStale ? 'устарел' : 'нет данных');
?>
<div class="tech-card">
    <div class="tech-card__head">
        <div class="tech-card__title">💰 OpenRouter · Расход AI <span class="tech-card__badge">(через релей owlex.top)</span></div>
        <div class="balance">
            <span class="balance__num <?= $balAvail ? 'balance__' . $balClass : 'balance__err' ?>"><?= $balTxt ?></span>
            <span class="balance__lbl">остаток</span>
        </div>
    </div>
    <div class="spend-bar"><div class="spend-bar__fill" style="width:<?= $spentPct ?>%"></div></div>
    <div class="grid">
        <div class="item"><b>Пополнено всего:</b> <span class="dim"><?= $totalCredits !== null ? '$' . number_format((float)$totalCredits, 2, '.', ' ') : '—' ?></span></div>
        <div class="item"><b>Потрачено аккаунтом:</b> <span class="dim"><?= $totalUsage !== null ? '$' . number_format((float)$totalUsage, 2, '.', ' ') : '—' ?></span></div>
        <div class="item"><b>Расход за сутки:</b> <span class="dim">$<?= number_format($usageDaily, 3, '.', ' ') ?></span> <span class="dim">· месяц: $<?= number_format($usageMonthly, 2, '.', ' ') ?></span></div>
        <div class="item"><b>Модель:</b> <span class="dim"><?= htmlspecialchars($model) ?></span></div>
        <div class="item"><b>Бизнес:</b> заявок всего <span class="ok"><?= $orderStats['orders_total'] ?></span> · сегодня <span class="ok"><?= $orderStats['orders_today'] ?></span> · треков <span class="ok"><?= $orderStats['tracks'] ?></span></div>
    </div>
    <?php if (!$balAvail && !$balStale): ?>
        <div style="margin-top:12px;font-size:12px;color:var(--color-text-light);">
            ℹ️ Баланс OpenRouter недоступен: IP tlax.ru заблокирован OpenRouter (403). Внешний скрипт с gateway обновляет баланс каждые 5 мин.
        </div>
    <?php endif; ?>
</div>

<!-- 🩺 Диагностика -->
<div class="tech-card">
    <div class="tech-card__head">
        <div class="tech-card__title">🩺 Диагностика Системы <span class="tech-card__badge">Live Status</span></div>
        <span class="tech-card__badge"><?= $fresh ? 'статус свежий · крон 5 мин' : ($stale ? '⚠️ статус устарел' : 'нет данных') ?></span>
    </div>
    <div class="grid">
        <div class="item"><b>MySQL БД:</b> <?= ($db['ok'] ?? null) === true ? '<span class="ok">🟢 OK</span>' : (($db['ok'] ?? null) === false ? '<span class="err">🔴 FAIL</span>' : '<span class="dim">—</span>') ?> <span class="dim">(пинг <?= (float)($db['ping_ms'] ?? 0) ?> ms)</span></div>
        <div class="item"><b>AI (релей):</b> <?= ($ai['ok'] ?? null) === true ? '<span class="ok">🟢 OK</span>' : (($ai['ok'] ?? null) === false ? '<span class="err">🔴 FAIL</span>' : '<span class="dim">—</span>') ?> <span class="dim">(пинг <?= (float)($ai['ping_ms'] ?? 0) ?> ms · <?= htmlspecialchars($model) ?>)</span></div>
        <div class="item"><b>Веб-приложение:</b> <?= ($web['ok'] ?? null) === true ? '<span class="ok">🟢 OK</span>' : (($web['ok'] ?? null) === false ? '<span class="err">🔴 FAIL</span>' : '<span class="dim">—</span>') ?> <span class="dim">(главная HTML · пинг <?= (float)($web['ping_ms'] ?? 0) ?> ms)</span></div>
        <div class="item"><b>Тарифы (env):</b> <span class="dim">
            <?php
                $first = true;
                foreach ($prices as $t) {
                    if (!$first) echo ' · ';
                    $first = false;
                    echo htmlspecialchars((string)($t['name'] ?? '')) . '=';
                    echo number_format((int)($t['price'] ?? 0), 0, '', ' ') . '₽';
                }
            ?>
        </span></div>
    </div>
</div>

<script>
    var btnCheck = document.getElementById('btnCheck');
    var checkResult = document.getElementById('checkResult');
    btnCheck.addEventListener('click', function () {
        btnCheck.disabled = true;
        btnCheck.textContent = '⏳ Проверка...';
        checkResult.textContent = '';
        var token = <?= json_encode($relayToken) ?>;
        var url = '/cron/healthcheck.php' + (token ? ('?token=' + encodeURIComponent(token)) : '');
        fetch(url, { method: 'GET' })
            .then(function (r) { return r.text(); })
            .then(function (txt) {
                var ok = /HEALTHCHECK Хитовая Песня OK/.test(txt);
                checkResult.textContent = ok ? '✅ Проверка завершена — всё OK' : '❌ Обнаружен сбой (см. баннер). Перезагружаю...';
                btnCheck.disabled = false;
                btnCheck.textContent = '🔄 Проверить сейчас';
                setTimeout(function () { location.reload(); }, 600);
            })
            .catch(function () {
                checkResult.textContent = '⚠️ Не удалось запустить проверку';
                btnCheck.disabled = false;
                btnCheck.textContent = '🔄 Проверить сейчас';
            });
    });
</script>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
