<?php
/**
 * Дашборд — главная страница админки
 * Статистика, последние заявки, графики
 *
 * Путь: /admin/index.php
 */

declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/includes/auth.php';
require_auth();

$page_title = 'Дашборд';

try {
    $db = Database::getInstance();

    // ─── Статистика заявок ───
    $stats_orders = $db->fetchOne(
        "SELECT
            COUNT(*) AS total,
            SUM(status = 'new') AS new_count,
            SUM(status = 'in_progress') AS progress_count,
            SUM(status = 'done') AS done_count,
            SUM(DATE(created_at) = CURDATE()) AS today_count,
            SUM(created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) AS week_count,
            SUM(created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS month_count
         FROM orders"
    );

    // ─── Статистика треков ───
    $stats_tracks = $db->fetchOne(
        "SELECT COUNT(*) AS total, SUM(is_active = 1) AS active_count FROM tracks"
    );

    // ─── Посетители сегодня (из page_views) ───
    $stats_views = $db->fetchOne(
        "SELECT COUNT(*) AS today FROM page_views WHERE DATE(created_at) = CURDATE()"
    );

    // ─── Последние 5 заявок ───
    $recent_orders = $db->fetchAll(
        "SELECT id, order_number, client_name, occasion, tariff, status, created_at
         FROM orders
         ORDER BY created_at DESC
         LIMIT 5"
    );

    // ─── График заявок за 30 дней (данные для JS) ───
    $chart_data = $db->fetchAll(
        "SELECT DATE(created_at) AS date, COUNT(*) AS count
         FROM orders
         WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
         GROUP BY DATE(created_at)
         ORDER BY date ASC"
    );

    // ─── Топ категорий ───
    $top_occasions = $db->fetchAll(
        "SELECT occasion, COUNT(*) AS cnt
         FROM orders
         GROUP BY occasion
         ORDER BY cnt DESC
         LIMIT 5"
    );

} catch (Exception $e) {
    log_error('admin/index: ' . $e->getMessage());
    $stats_orders  = ['total'=>0,'new_count'=>0,'progress_count'=>0,'done_count'=>0,'today_count'=>0,'week_count'=>0,'month_count'=>0];
    $stats_tracks  = ['total'=>0,'active_count'=>0];
    $stats_views   = ['today'=>0];
    $recent_orders = [];
    $chart_data    = [];
    $top_occasions = [];
}

// Подготовка данных графика
$chart_labels = [];
$chart_values = [];
$chart_map    = [];
foreach ($chart_data as $row) {
    $chart_map[$row['date']] = (int)$row['count'];
}
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $chart_labels[] = date('d.m', strtotime($date));
    $chart_values[] = $chart_map[$date] ?? 0;
}

$extra_css = ['https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css'];

require_once __DIR__ . '/includes/admin-header.php';
?>

<!-- Виджеты статистики -->
<div class="dash-stats">

    <div class="dash-stat dash-stat--primary">
        <div class="dash-stat__icon" aria-hidden="true">📋</div>
        <div class="dash-stat__content">
            <div class="dash-stat__value"><?= (int)$stats_orders['total'] ?></div>
            <div class="dash-stat__label">Всего заявок</div>
            <div class="dash-stat__sub">Сегодня: <b><?= (int)$stats_orders['today_count'] ?></b></div>
        </div>
    </div>

    <div class="dash-stat dash-stat--warning">
        <div class="dash-stat__icon" aria-hidden="true">🆕</div>
        <div class="dash-stat__content">
            <div class="dash-stat__value"><?= (int)$stats_orders['new_count'] ?></div>
            <div class="dash-stat__label">Новых заявок</div>
            <div class="dash-stat__sub">Требуют ответа</div>
        </div>
    </div>

    <div class="dash-stat dash-stat--info">
        <div class="dash-stat__icon" aria-hidden="true">⚙️</div>
        <div class="dash-stat__content">
            <div class="dash-stat__value"><?= (int)$stats_orders['progress_count'] ?></div>
            <div class="dash-stat__label">В работе</div>
            <div class="dash-stat__sub">За неделю: <b><?= (int)$stats_orders['week_count'] ?></b></div>
        </div>
    </div>

    <div class="dash-stat dash-stat--success">
        <div class="dash-stat__icon" aria-hidden="true">✅</div>
        <div class="dash-stat__content">
            <div class="dash-stat__value"><?= (int)$stats_orders['done_count'] ?></div>
            <div class="dash-stat__label">Выполнено</div>
            <div class="dash-stat__sub">За месяц: <b><?= (int)$stats_orders['month_count'] ?></b></div>
        </div>
    </div>

    <div class="dash-stat">
        <div class="dash-stat__icon" aria-hidden="true">🎵</div>
        <div class="dash-stat__content">
            <div class="dash-stat__value"><?= (int)$stats_tracks['active_count'] ?></div>
            <div class="dash-stat__label">Треков в портфолио</div>
            <div class="dash-stat__sub">Всего: <b><?= (int)$stats_tracks['total'] ?></b></div>
        </div>
    </div>

    <div class="dash-stat">
        <div class="dash-stat__icon" aria-hidden="true">👁️</div>
        <div class="dash-stat__content">
            <div class="dash-stat__value"><?= (int)$stats_views['today'] ?></div>
            <div class="dash-stat__label">Посетителей сегодня</div>
            <div class="dash-stat__sub">&nbsp;</div>
        </div>
    </div>

</div><!-- /.dash-stats -->


<!-- График + Топ -->
<div class="dash-grid">

    <!-- График заявок -->
    <div class="admin-card admin-card--chart">
        <div class="admin-card__header">
            <h2 class="admin-card__title">Заявки за 30 дней</h2>
        </div>
        <div class="admin-card__body">
            <canvas id="ordersChart" height="100" aria-label="График заявок за 30 дней"></canvas>
        </div>
    </div>

    <!-- Топ поводов -->
    <div class="admin-card">
        <div class="admin-card__header">
            <h2 class="admin-card__title">Топ поводов</h2>
        </div>
        <div class="admin-card__body">
            <?php if (empty($top_occasions)): ?>
                <p class="admin-empty">Заявок пока нет</p>
            <?php else: ?>
                <ul class="top-list">
                    <?php foreach ($top_occasions as $occ): ?>
                        <li class="top-list__item">
                            <span class="top-list__label">
                                <?= h(get_occasion_label($occ['occasion'])) ?>
                            </span>
                            <span class="top-list__count"><?= (int)$occ['cnt'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

</div><!-- /.dash-grid -->


<!-- Последние заявки -->
<div class="admin-card">
    <div class="admin-card__header">
        <h2 class="admin-card__title">Последние заявки</h2>
        <a href="/admin/orders.php" class="btn btn--outline btn--sm">Все заявки</a>
    </div>
    <div class="admin-card__body admin-card__body--no-pad">

        <?php if (empty($recent_orders)): ?>
            <p class="admin-empty" style="padding: var(--space-lg);">Заявок пока нет</p>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Номер</th>
                            <th>Клиент</th>
                            <th>Повод</th>
                            <th>Тариф</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                            <tr class="admin-table__row" data-href="/admin/order-view.php?id=<?= (int)$order['id'] ?>">
                                <td>
                                    <span class="order-number"><?= h($order['order_number']) ?></span>
                                </td>
                                <td><?= h($order['client_name']) ?></td>
                                <td><?= h(get_occasion_label($order['occasion'])) ?></td>
                                <td><?= h(get_tariff_label($order['tariff'])) ?></td>
                                <td><?= render_status_badge($order['status']) ?></td>
                                <td>
                                    <span class="admin-date">
                                        <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/admin/order-view.php?id=<?= (int)$order['id'] ?>" class="btn btn--sm btn--outline">
                                        Открыть
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const ctx = document.getElementById('ordersChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_labels, JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{
                label: 'Заявки',
                data: <?= json_encode($chart_values) ?>,
                borderColor: '#8B1E3F',
                backgroundColor: 'rgba(139,30,63,0.08)',
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#8B1E3F',
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: 'rgba(0,0,0,0.05)' },
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        maxTicksLimit: 10,
                        maxRotation: 0,
                    },
                },
            },
        },
    });
})();
</script>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>