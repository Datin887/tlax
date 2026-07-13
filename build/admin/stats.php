<?php
/**
 * Страница статистики
 * Путь: /admin/stats.php
 */

declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/includes/auth.php';
require_auth();

$page_title = 'Статистика';

// ─── Период ───
$period = in_array($_GET['period'] ?? '', ['7', '30', '90'], true)
    ? (int)$_GET['period']
    : 30;

try {
    $db = Database::getInstance();

    // ─── Заявки по дням ───
    $orders_by_day = $db->fetchAll(
        "SELECT DATE(created_at) AS date, COUNT(*) AS count
         FROM orders
         WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
         GROUP BY DATE(created_at)
         ORDER BY date ASC",
        [':days' => $period]
    );

    // ─── Заявки по категориям ───
    $orders_by_occasion = $db->fetchAll(
        "SELECT occasion, COUNT(*) AS count
         FROM orders
         GROUP BY occasion
         ORDER BY count DESC"
    );

    // ─── Заявки по тарифам ───
    $orders_by_tariff = $db->fetchAll(
        "SELECT tariff, COUNT(*) AS count
         FROM orders
         GROUP BY tariff
         ORDER BY count DESC"
    );

    // ─── Топ-10 треков по прослушиваниям ───
    $top_tracks = $db->fetchAll(
        "SELECT t.title, t.plays_count, c.name AS category_name
         FROM tracks t
         LEFT JOIN track_categories c ON t.category_id = c.id
         WHERE t.is_active = 1
         ORDER BY t.plays_count DESC
         LIMIT 10"
    );

    // ─── Общие цифры ───
    $totals = $db->fetchOne(
        "SELECT
            COUNT(*) AS total_orders,
            SUM(status = 'done') AS done_orders,
            SUM(status = 'new') AS new_orders,
            SUM(status = 'in_progress') AS progress_orders,
            SUM(DATE(created_at) = CURDATE()) AS today_orders,
            SUM(created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) AS week_orders,
            SUM(created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS month_orders
         FROM orders"
    );

    // ─── Посещаемость по дням ───
    $views_by_day = $db->fetchAll(
        "SELECT DATE(created_at) AS date, COUNT(*) AS count
         FROM page_views
         WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
         GROUP BY DATE(created_at)
         ORDER BY date ASC",
        [':days' => $period]
    );

    // ─── Конверсия ───
    $total_views  = $db->fetchOne(
        "SELECT COUNT(*) AS cnt FROM page_views WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)",
        [':days' => $period]
    );
    $total_orders_period = $db->fetchOne(
        "SELECT COUNT(*) AS cnt FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)",
        [':days' => $period]
    );

    $views_cnt  = (int)($total_views['cnt']  ?? 0);
    $orders_cnt = (int)($total_orders_period['cnt'] ?? 0);
    $conversion = $views_cnt > 0 ? round($orders_cnt / $views_cnt * 100, 2) : 0;

} catch (Exception $e) {
    log_error('admin/stats: ' . $e->getMessage());
    $orders_by_day = $orders_by_occasion = $orders_by_tariff = $top_tracks = [];
    $totals = [];
    $views_by_day = [];
    $views_cnt = $orders_cnt = 0;
    $conversion = 0;
}

// ─── Подготовка данных для графиков ───
$day_labels = [];
$day_orders = [];
$day_views  = [];

$orders_map = [];
foreach ($orders_by_day as $row) {
    $orders_map[$row['date']] = (int)$row['count'];
}

$views_map = [];
foreach ($views_by_day as $row) {
    $views_map[$row['date']] = (int)$row['count'];
}

for ($i = $period - 1; $i >= 0; $i--) {
    $date        = date('Y-m-d', strtotime("-{$i} days"));
    $day_labels[]= date('d.m', strtotime($date));
    $day_orders[]= $orders_map[$date] ?? 0;
    $day_views[] = $views_map[$date]  ?? 0;
}

// Данные для pie-графика поводов
$occasion_labels = array_map(fn($r) => get_occasion_label($r['occasion']), $orders_by_occasion);
$occasion_counts = array_map(fn($r) => (int)$r['count'], $orders_by_occasion);

// Данные для bar-графика тарифов
$tariff_labels = array_map(fn($r) => get_tariff_label($r['tariff']), $orders_by_tariff);
$tariff_counts = array_map(fn($r) => (int)$r['count'], $orders_by_tariff);

require_once __DIR__ . '/includes/admin-header.php';
?>

<!-- Переключатель периода -->
<div class="stats-period-bar">
    <?php foreach ([7 => '7 дней', 30 => '30 дней', 90 => '90 дней'] as $days => $label): ?>
        <a
            href="/admin/stats.php?period=<?= $days ?>"
            class="admin-tab<?= $period === $days ? ' active' : '' ?>"
        ><?= $label ?></a>
    <?php endforeach; ?>
</div>

<!-- Сводные показатели -->
<div class="dash-stats" style="margin-bottom: var(--space-lg);">
    <div class="dash-stat dash-stat--primary">
        <div class="dash-stat__icon">📋</div>
        <div class="dash-stat__content">
            <div class="dash-stat__value"><?= (int)($totals['total_orders'] ?? 0) ?></div>
            <div class="dash-stat__label">Всего заявок</div>
            <div class="dash-stat__sub">Сегодня: <b><?= (int)($totals['today_orders'] ?? 0) ?></b></div>
        </div>
    </div>
    <div class="dash-stat dash-stat--success">
        <div class="dash-stat__icon">✅</div>
        <div class="dash-stat__content">
            <div class="dash-stat__value"><?= (int)($totals['done_orders'] ?? 0) ?></div>
            <div class="dash-stat__label">Выполнено</div>
            <div class="dash-stat__sub">За месяц: <b><?= (int)($totals['month_orders'] ?? 0) ?></b></div>
        </div>
    </div>
    <div class="dash-stat dash-stat--info">
        <div class="dash-stat__icon">👁️</div>
        <div class="dash-stat__content">
            <div class="dash-stat__value"><?= number_format($views_cnt) ?></div>
            <div class="dash-stat__label">Просмотров за период</div>
        </div>
    </div>
    <div class="dash-stat">
        <div class="dash-stat__icon">📊</div>
        <div class="dash-stat__content">
            <div class="dash-stat__value"><?= $conversion ?>%</div>
            <div class="dash-stat__label">Конверсия</div>
            <div class="dash-stat__sub">посетители → заявки</div>
        </div>
    </div>
</div>

<!-- График динамики -->
<div class="admin-card">
    <div class="admin-card__header">
        <h2 class="admin-card__title">Динамика за <?= $period ?> дней</h2>
    </div>
    <div class="admin-card__body">
        <canvas id="dynamicsChart" height="80"></canvas>
    </div>
</div>

<!-- Pie + Bar графики -->
<div class="dash-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: var(--space-md);">
    <div class="admin-card">
        <div class="admin-card__header">
            <h2 class="admin-card__title">По поводам</h2>
        </div>
        <div class="admin-card__body" style="display:flex;justify-content:center;">
            <canvas id="occasionChart" style="max-height:280px;max-width:280px;"></canvas>
        </div>
    </div>
    <div class="admin-card">
        <div class="admin-card__header">
            <h2 class="admin-card__title">По тарифам</h2>
        </div>
        <div class="admin-card__body">
            <canvas id="tariffChart" height="120"></canvas>
        </div>
    </div>
</div>

<!-- Топ треков -->
<div class="admin-card">
    <div class="admin-card__header">
        <h2 class="admin-card__title">Топ-10 треков по прослушиваниям</h2>
    </div>
    <div class="admin-card__body admin-card__body--no-pad">
        <?php if (empty($top_tracks)): ?>
            <p class="admin-empty" style="padding:var(--space-lg);">Нет данных</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Трек</th>
                        <th>Категория</th>
                        <th>Прослушиваний</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_tracks as $i => $track): ?>
                        <tr>
                            <td><b style="color:var(--color-primary);"><?= $i + 1 ?></b></td>
                            <td><?= h($track['title']) ?></td>
                            <td><?= h($track['category_name'] ?? '—') ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="
                                        width: <?= min(100, (int)($track['plays_count'] / max(1, (int)$top_tracks[0]['plays_count']) * 100)) ?>%;
                                        height: 6px;
                                        background: var(--color-primary);
                                        border-radius: 3px;
                                        min-width: 4px;
                                        max-width: 120px;
                                    "></div>
                                    <b><?= number_format((int)$track['plays_count']) ?></b>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const COLORS = [
        '#8B1E3F','#D4A574','#4A7C59','#3B82F6','#F59E0B',
        '#EF4444','#8B5CF6','#06B6D4','#10B981','#F97316'
    ];

    // ─── Динамика ───
    new Chart(document.getElementById('dynamicsChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($day_labels, JSON_UNESCAPED_UNICODE) ?>,
            datasets: [
                {
                    label: 'Заявки',
                    data: <?= json_encode($day_orders) ?>,
                    borderColor: '#8B1E3F',
                    backgroundColor: 'rgba(139,30,63,0.08)',
                    borderWidth: 2,
                    pointRadius: 3,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y',
                },
                {
                    label: 'Просмотры',
                    data: <?= json_encode($day_views) ?>,
                    borderColor: '#D4A574',
                    backgroundColor: 'rgba(212,165,116,0.06)',
                    borderWidth: 2,
                    pointRadius: 2,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y1',
                },
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top' },
            },
            scales: {
                y:  { beginAtZero: true, ticks: { stepSize: 1 }, position: 'left',  grid: { color: 'rgba(0,0,0,0.04)' } },
                y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } },
                x:  { grid: { display: false }, ticks: { maxTicksLimit: 12, maxRotation: 0 } },
            },
        },
    });

    // ─── Поводы (pie) ───
    const occasionData = <?= json_encode($occasion_counts) ?>;
    if (occasionData.length) {
        new Chart(document.getElementById('occasionChart'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($occasion_labels, JSON_UNESCAPED_UNICODE) ?>,
                datasets: [{ data: occasionData, backgroundColor: COLORS, borderWidth: 2, borderColor: '#fff' }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } },
                },
            },
        });
    }

    // ─── Тарифы (bar) ───
    const tariffData = <?= json_encode($tariff_counts) ?>;
    if (tariffData.length) {
        new Chart(document.getElementById('tariffChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($tariff_labels, JSON_UNESCAPED_UNICODE) ?>,
                datasets: [{
                    label: 'Заявок',
                    data: tariffData,
                    backgroundColor: COLORS.slice(0, tariffData.length),
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    x: { grid: { display: false } },
                },
            },
        });
    }
})();
</script>

<?php
$extra_css = [];
require_once __DIR__ . '/includes/admin-footer.php';
?>