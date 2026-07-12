# ЭТАП 8: Финализация

Создаём все финальные файлы без остановок.

---

## Файл 1: `admin/stats.php`

```php
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
```

---

## Файл 2: `admin/track-edit.php`

```php
<?php
/**
 * Редактирование трека
 * Путь: /admin/track-edit.php
 */

declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/includes/auth.php';
require_auth();

$track_id = (int)($_GET['id'] ?? 0);
if (!$track_id) {
    header('Location: /admin/tracks.php');
    exit;
}

$page_title = 'Редактировать трек';
$csrf_token = generate_csrf_token();
$errors     = [];

try {
    $db         = Database::getInstance();
    $track      = $db->fetchOne("SELECT * FROM tracks WHERE id = :id", [':id' => $track_id]);
    $categories = $db->fetchAll("SELECT * FROM track_categories ORDER BY sort_order ASC");
} catch (Exception $e) {
    log_error('admin/track-edit: ' . $e->getMessage());
    header('Location: /admin/tracks.php');
    exit;
}

if (!$track) {
    header('Location: /admin/tracks.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Ошибка CSRF';
    } else {

        $title       = sanitize_string($_POST['title']       ?? '', 200);
        $description = sanitize_text($_POST['description']   ?? '', 1000);
        $category_id = (int)($_POST['category_id'] ?? 0);
        $style       = sanitize_string($_POST['style']       ?? '', 100);
        $mood        = sanitize_string($_POST['mood']        ?? '', 50);
        $voice_type  = sanitize_string($_POST['voice_type']  ?? '', 30);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_active   = isset($_POST['is_active'])   ? 1 : 0;
        $lyrics      = sanitize_text($_POST['lyrics']        ?? '', 10000);
        $sort_order  = (int)($_POST['sort_order']  ?? 0);

        if (mb_strlen($title, 'UTF-8') < 2) {
            $errors[] = 'Введите название трека';
        }

        // Новое аудио (опционально)
        $audio_filename = $track['audio_file'];
        if (!empty($_FILES['audio_file']['name'])) {
            $audio_upload = upload_audio_file($_FILES['audio_file']);
            if ($audio_upload['success']) {
                // Удаляем старый файл
                if ($audio_filename) {
                    @unlink(UPLOAD_PATH . '/tracks/' . $audio_filename);
                }
                $audio_filename = $audio_upload['filename'];
                // Обновляем длительность
                $track['duration'] = get_audio_duration(UPLOAD_PATH . '/tracks/' . $audio_filename);
            } else {
                $errors[] = $audio_upload['error'];
            }
        }

        // Новая обложка (опционально)
        $cover_filename = $track['cover_image'];
        if (!empty($_FILES['cover_image']['name'])) {
            $cover_upload = upload_image_file($_FILES['cover_image']);
            if ($cover_upload['success']) {
                if ($cover_filename) {
                    @unlink(UPLOAD_PATH . '/covers/' . $cover_filename);
                }
                $cover_filename = $cover_upload['filename'];
            } else {
                $errors[] = $cover_upload['error'];
            }
        }

        if (empty($errors)) {
            try {
                $db->execute(
                    "UPDATE tracks SET
                        title = :title, description = :desc, category_id = :cat_id,
                        audio_file = :audio, cover_image = :cover, duration = :dur,
                        style = :style, mood = :mood, voice_type = :voice,
                        is_featured = :featured, is_active = :active,
                        lyrics = :lyrics, sort_order = :sort,
                        updated_at = NOW()
                     WHERE id = :id",
                    [
                        ':title'    => $title,
                        ':desc'     => $description,
                        ':cat_id'   => $category_id ?: null,
                        ':audio'    => $audio_filename,
                        ':cover'    => $cover_filename,
                        ':dur'      => (int)$track['duration'],
                        ':style'    => $style,
                        ':mood'     => $mood,
                        ':voice'    => $voice_type,
                        ':featured' => $is_featured,
                        ':active'   => $is_active,
                        ':lyrics'   => $lyrics,
                        ':sort'     => $sort_order,
                        ':id'       => $track_id,
                    ]
                );
                header('Location: /admin/tracks.php?saved=1');
                exit;
            } catch (Exception $e) {
                log_error('admin/track-edit: ' . $e->getMessage());
                $errors[] = 'Ошибка при сохранении';
            }
        }
    }
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<?php if (!empty($errors)): ?>
    <div class="alert alert--error">
        <?php foreach ($errors as $err): ?>
            <div>❌ <?= h($err) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="admin-breadcrumb">
    <a href="/admin/tracks.php">Треки</a> › Редактировать
</div>

<form method="POST" action="/admin/track-edit.php?id=<?= $track_id ?>" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">

    <div class="track-form-grid">

        <div class="admin-card">
            <div class="admin-card__header"><h2 class="admin-card__title">Основное</h2></div>
            <div class="admin-card__body">

                <div class="form-group">
                    <label class="form-label" for="title">Название <span class="required">*</span></label>
                    <input type="text" id="title" name="title" class="form-input"
                           value="<?= h($_POST['title'] ?? $track['title']) ?>" required maxlength="200">
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Описание</label>
                    <textarea id="description" name="description" class="form-textarea" rows="3" maxlength="1000"><?= h($_POST['description'] ?? $track['description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="category_id">Категория</label>
                    <select id="category_id" name="category_id" class="form-input">
                        <option value="">— Выберите —</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>"
                                <?= (int)($_POST['category_id'] ?? $track['category_id']) === (int)$cat['id'] ? 'selected' : '' ?>>
                                <?= h($cat['icon'] ?? '') ?> <?= h($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:var(--space-sm);">
                    <div class="form-group">
                        <label class="form-label" for="style">Стиль</label>
                        <input type="text" id="style" name="style" class="form-input"
                               value="<?= h($_POST['style'] ?? $track['style']) ?>" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="mood">Настроение</label>
                        <input type="text" id="mood" name="mood" class="form-input"
                               value="<?= h($_POST['mood'] ?? $track['mood']) ?>" maxlength="50">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="voice_type">Голос</label>
                        <input type="text" id="voice_type" name="voice_type" class="form-input"
                               value="<?= h($_POST['voice_type'] ?? $track['voice_type']) ?>" maxlength="30">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="sort_order">Порядок сортировки</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-input"
                           value="<?= (int)($_POST['sort_order'] ?? $track['sort_order']) ?>"
                           min="0" style="max-width:120px;">
                </div>

                <div style="display:flex;gap:var(--space-lg);">
                    <label class="form-check">
                        <input type="checkbox" name="is_active" class="form-check__input" value="1"
                               <?= ($_POST['is_active'] ?? $track['is_active']) ? 'checked' : '' ?>>
                        <span class="form-check__label">Активен</span>
                    </label>
                    <label class="form-check">
                        <input type="checkbox" name="is_featured" class="form-check__input" value="1"
                               <?= ($_POST['is_featured'] ?? $track['is_featured']) ? 'checked' : '' ?>>
                        <span class="form-check__label">⭐ На главной</span>
                    </label>
                </div>

            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card__header"><h2 class="admin-card__title">Файлы</h2></div>
            <div class="admin-card__body">

                <!-- Текущее аудио -->
                <?php if ($track['audio_file']): ?>
                    <div class="current-file">
                        <span class="current-file__label">🎵 Текущий файл:</span>
                        <audio controls style="width:100%;margin:8px 0;">
                            <source src="/uploads/tracks/<?= h($track['audio_file']) ?>" type="audio/mpeg">
                        </audio>
                        <span class="form-hint"><?= h($track['audio_file']) ?></span>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label" for="audio_file">
                        Новый аудио файл
                        <span class="form-hint">(оставьте пустым для сохранения текущего)</span>
                    </label>
                    <input type="file" id="audio_file" name="audio_file" class="form-input"
                           accept="audio/mpeg,audio/mp3,.mp3">
                </div>

                <!-- Текущая обложка -->
                <?php if ($track['cover_image']): ?>
                    <div class="current-file">
                        <span class="current-file__label">🖼 Текущая обложка:</span>
                        <img src="/uploads/covers/<?= h($track['cover_image']) ?>"
                             alt="Обложка" style="width:100%;max-height:150px;object-fit:cover;border-radius:8px;margin:8px 0;">
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label" for="cover_image">
                        Новая обложка
                        <span class="form-hint">(оставьте пустым для сохранения текущей)</span>
                    </label>
                    <input type="file" id="cover_image" name="cover_image" class="form-input"
                           accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp">
                </div>

                <div class="form-group">
                    <label class="form-label" for="lyrics">Текст песни</label>
                    <textarea id="lyrics" name="lyrics" class="form-textarea" rows="8"
                              maxlength="10000"><?= h($_POST['lyrics'] ?? $track['lyrics']) ?></textarea>
                </div>

            </div>
        </div>

    </div>

    <div style="display:flex;gap:var(--space-sm);margin-top:var(--space-lg);">
        <button type="submit" class="btn btn--primary btn--lg">💾 Сохранить изменения</button>
        <a href="/admin/tracks.php" class="btn btn--outline btn--lg">Отмена</a>
        <a href="/portfolio.php" target="_blank" class="btn btn--ghost btn--lg">🌐 Посмотреть на сайте</a>
    </div>

</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
```

---

## Файл 3: `public/.htaccess`

```apache
# ═══════════════════════════════════════════════════════════
# Главный .htaccess — Хитовая Песня
# ═══════════════════════════════════════════════════════════

Options -Indexes
Options -MultiViews

# ─── Движок перезаписи ───
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    # HTTPS редирект
    RewriteCond %{HTTPS} off
    RewriteCond %{HTTP_HOST} !^localhost [NC]
    RewriteCond %{HTTP_HOST} !^127\.0\.0\.1
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # www → без www
    RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
    RewriteRule ^ https://%1%{REQUEST_URI} [L,R=301]

    # Убираем .php из URL (index → index.php)
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME}.php -f
    RewriteRule ^([^.]+)$ $1.php [L]

    # Блокировка доступа к скрытым файлам
    RewriteRule (^|/)\.(?!well-known) - [F,L]
</IfModule>

# ─── Заголовки безопасности ───
<IfModule mod_headers.c>
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Permissions-Policy "geolocation=(), microphone=(), camera=()"

    # Content-Security-Policy
    Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.gstatic.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; media-src 'self'; connect-src 'self'; frame-ancestors 'none';"

    # Убираем X-Powered-By
    Header unset X-Powered-By
    Header unset Server

    # HSTS (только для HTTPS)
    # Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains" env=HTTPS
</IfModule>

# ─── Кэширование статики ───
<IfModule mod_expires.c>
    ExpiresActive On

    ExpiresByType text/css                  "access plus 1 month"
    ExpiresByType application/javascript    "access plus 1 month"
    ExpiresByType image/jpeg                "access plus 6 months"
    ExpiresByType image/png                 "access plus 6 months"
    ExpiresByType image/webp                "access plus 6 months"
    ExpiresByType image/svg+xml             "access plus 6 months"
    ExpiresByType image/x-icon              "access plus 1 year"
    ExpiresByType audio/mpeg                "access plus 1 month"
    ExpiresByType font/woff2                "access plus 1 year"
    ExpiresByType application/font-woff     "access plus 1 year"
</IfModule>

<IfModule mod_headers.c>
    <FilesMatch "\.(css|js)$">
        Header set Cache-Control "public, max-age=2592000, must-revalidate"
    </FilesMatch>
    <FilesMatch "\.(jpg|jpeg|png|webp|gif|ico|svg)$">
        Header set Cache-Control "public, max-age=15552000, immutable"
    </FilesMatch>
    <FilesMatch "\.(php)$">
        Header set Cache-Control "no-store, no-cache, must-revalidate"
        Header set Pragma "no-cache"
    </FilesMatch>
</IfModule>

# ─── Gzip сжатие ───
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css text/javascript
    AddOutputFilterByType DEFLATE application/javascript application/json
    AddOutputFilterByType DEFLATE image/svg+xml
    AddOutputFilterByType DEFLATE font/woff2
</IfModule>

# ─── Защита чувствительных файлов ───
<FilesMatch "\.(sql|log|env|htpasswd|htaccess|bak|swp|git|md)$">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order allow,deny
        Deny from all
    </IfModule>
</FilesMatch>

# ─── PHP настройки ───
<IfModule mod_php.c>
    php_value upload_max_filesize  25M
    php_value post_max_size        26M
    php_value max_execution_time   60
    php_value memory_limit         128M
    php_flag  display_errors       Off
    php_flag  log_errors           On
    php_value session.cookie_httponly 1
    php_value session.cookie_samesite Strict
    php_value session.use_strict_mode 1
</IfModule>

# ─── Кастомные страницы ошибок ───
ErrorDocument 404 /404.php
ErrorDocument 500 /500.php
ErrorDocument 403 /404.php
```

---

## Файл 4: `public/404.php`

```php
<?php
/**
 * Страница 404 — не найдено
 * Путь: /public/404.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

http_response_code(404);

$page_meta = [
    'title'       => '404 — Страница не найдена | Хитовая Песня',
    'description' => 'Запрашиваемая страница не найдена.',
    'canonical'   => SITE_URL . '/404',
];

require_once __DIR__ . '/../includes/head-meta.php';
require_once __DIR__ . '/../includes/header.php';
?>

<main>
    <section class="error-section">
        <div class="container">
            <div class="error-card">
                <div class="error-card__code" aria-hidden="true">404</div>
                <div class="error-card__icon" aria-hidden="true">🎵</div>
                <h1 class="error-card__title">Страница не найдена</h1>
                <p class="error-card__desc">
                    Эта страница пропала, как несыгранная нота.
                    Возможно, адрес изменился или страница была удалена.
                </p>
                <div class="error-card__actions">
                    <a href="/" class="btn btn--primary btn--lg">На главную</a>
                    <a href="/portfolio.php" class="btn btn--outline btn--lg">Примеры работ</a>
                    <a href="/order.php" class="btn btn--outline btn--lg">Заказать песню</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
```

---

## Файл 5: `public/500.php`

```php
<?php
/**
 * Страница 500 — ошибка сервера
 * Путь: /public/500.php
 */

http_response_code(500);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Ошибка сервера | Хитовая Песня</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:sans-serif;background:#F5EFE6;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px;}
        .card{background:#fff;border-radius:20px;padding:48px;text-align:center;max-width:480px;width:100%;box-shadow:0 15px 40px rgba(139,30,63,.15);}
        .code{font-size:72px;font-weight:900;color:#8B1E3F;line-height:1;}
        .title{font-size:24px;font-weight:700;color:#2C1810;margin:16px 0 8px;}
        .desc{color:#6B5D54;font-size:15px;line-height:1.6;margin-bottom:32px;}
        .btn{display:inline-block;padding:14px 28px;background:#8B1E3F;color:#fff;text-decoration:none;border-radius:12px;font-weight:600;margin:4px;}
        .btn-outline{background:transparent;color:#8B1E3F;border:2px solid #8B1E3F;}
    </style>
</head>
<body>
    <div class="card">
        <div class="code">500</div>
        <div style="font-size:48px;margin:12px 0;">⚙️</div>
        <h1 class="title">Ошибка сервера</h1>
        <p class="desc">
            Что-то пошло не так. Мы уже знаем об этой проблеме
            и работаем над её исправлением.
        </p>
        <a href="/" class="btn">На главную</a>
        <a href="/contacts.php" class="btn btn-outline">Написать нам</a>
    </div>
</body>
</html>
```

---

## Файл 6: `public/sitemap.xml` (динамический)

```php
<?php
/**
 * Динамический sitemap.xml
 * Путь: /public/sitemap.php → переименовать в sitemap.xml
 * или настроить через .htaccess: RewriteRule ^sitemap\.xml$ sitemap.php [L]
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/xml; charset=UTF-8');

$base_url = rtrim(SITE_URL, '/');
$today    = date('Y-m-d');

// Статичные страницы
$static_pages = [
    ['loc' => '/',             'priority' => '1.0', 'changefreq' => 'daily'],
    ['loc' => '/portfolio.php','priority' => '0.9', 'changefreq' => 'weekly'],
    ['loc' => '/pricing.php',  'priority' => '0.8', 'changefreq' => 'monthly'],
    ['loc' => '/order.php',    'priority' => '0.9', 'changefreq' => 'monthly'],
    ['loc' => '/contacts.php', 'priority' => '0.7', 'changefreq' => 'monthly'],
];

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

foreach ($static_pages as $page) {
    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . htmlspecialchars($base_url . $page['loc'], ENT_QUOTES, 'UTF-8') . '</loc>' . PHP_EOL;
    echo '    <lastmod>' . $today . '</lastmod>' . PHP_EOL;
    echo '    <changefreq>' . $page['changefreq'] . '</changefreq>' . PHP_EOL;
    echo '    <priority>' . $page['priority'] . '</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

// Категории портфолио
try {
    $db         = Database::getInstance();
    $categories = $db->fetchAll("SELECT slug FROM track_categories WHERE id IN (SELECT DISTINCT category_id FROM tracks WHERE is_active=1)");
    foreach ($categories as $cat) {
        echo '  <url>' . PHP_EOL;
        echo '    <loc>' . htmlspecialchars($base_url . '/portfolio.php?category=' . $cat['slug'], ENT_QUOTES, 'UTF-8') . '</loc>' . PHP_EOL;
        echo '    <lastmod>' . $today . '</lastmod>' . PHP_EOL;
        echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
        echo '    <priority>0.7</priority>' . PHP_EOL;
        echo '  </url>' . PHP_EOL;
    }
} catch (Exception $e) {
    // Игнорируем
}

echo '</urlset>' . PHP_EOL;
```

---

## Файл 7: `public/robots.txt`

```
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /api/
Disallow: /uploads/tracks/
Disallow: /logs/
Disallow: /includes/
Disallow: /database/

Sitemap: https://hit.owlex.top/sitemap.xml
```

---

## Файл 8: `includes/security.php` — полная версия

```php
<?php
/**
 * Функции безопасности: CSRF, sanitize, валидация
 * Путь: /includes/security.php
 */

declare(strict_types=1);

// Запускаем сессию если не запущена
if (session_status() === PHP_SESSION_NONE && !defined('IN_ADMIN')) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

/**
 * Генерировать CSRF-токен и сохранить в сессии
 *
 * @return string
 */
function generate_csrf_token(): string
{
    if (empty($_SESSION['csrf_tokens'])) {
        $_SESSION['csrf_tokens'] = [];
    }

    $token = bin2hex(random_bytes(32));

    // Сохраняем не более 10 токенов
    $_SESSION['csrf_tokens'][$token] = time();
    if (count($_SESSION['csrf_tokens']) > 10) {
        // Удаляем самый старый
        asort($_SESSION['csrf_tokens']);
        array_shift($_SESSION['csrf_tokens']);
    }

    return $token;
}

/**
 * Проверить CSRF-токен
 *
 * @param string $token
 * @param int    $max_age — максимальный возраст токена в секундах
 * @return bool
 */
function verify_csrf_token(string $token, int $max_age = 3600): bool
{
    if (empty($token) || empty($_SESSION['csrf_tokens'])) {
        return false;
    }

    if (!isset($_SESSION['csrf_tokens'][$token])) {
        return false;
    }

    $created = $_SESSION['csrf_tokens'][$token];

    // Токен слишком старый
    if (time() - $created > $max_age) {
        unset($_SESSION['csrf_tokens'][$token]);
        return false;
    }

    // Удаляем использованный токен (one-time use)
    unset($_SESSION['csrf_tokens'][$token]);

    return true;
}

/**
 * Экранирование для HTML вывода
 * Алиас для htmlspecialchars с правильными флагами
 *
 * @param mixed $value
 * @return string
 */
function h(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
```

---

## Файл 9: `includes/db.php` — метод `insert()`

Добавьте в класс `Database` метод `insert`, если его ещё нет:

```php
/**
 * Выполнить INSERT и вернуть lastInsertId
 *
 * @param string $sql
 * @param array  $params
 * @return int
 * @throws PDOException
 */
public function insert(string $sql, array $params = []): int
{
    $this->execute($sql, $params);
    return (int)$this->pdo->lastInsertId();
}
```

---

## Файл 10: Дополнения к `database/schema.sql`

```sql
-- ═══════════════════════════════════════════════════════════
-- Полная схема БД — Хитовая Песня
-- ═══════════════════════════════════════════════════════════

SET NAMES utf8mb4;
SET time_zone = '+03:00';

-- ─── Категории треков ───
CREATE TABLE IF NOT EXISTS `track_categories` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100)    NOT NULL,
    `slug`       VARCHAR(100)    NOT NULL,
    `icon`       VARCHAR(10)     NOT NULL DEFAULT '🎵',
    `sort_order` INT             NOT NULL DEFAULT 0,
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Треки ───
CREATE TABLE IF NOT EXISTS `tracks` (
    `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `title`       VARCHAR(200)    NOT NULL,
    `description` TEXT            NULL,
    `category_id` INT UNSIGNED    NULL,
    `audio_file`  VARCHAR(255)    NOT NULL DEFAULT '',
    `cover_image` VARCHAR(255)    NOT NULL DEFAULT '',
    `duration`    INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT 'В секундах',
    `style`       VARCHAR(100)    NOT NULL DEFAULT '',
    `mood`        VARCHAR(50)     NOT NULL DEFAULT '',
    `voice_type`  VARCHAR(30)     NOT NULL DEFAULT '',
    `lyrics`      TEXT            NULL,
    `plays_count` INT UNSIGNED    NOT NULL DEFAULT 0,
    `is_featured` TINYINT(1)      NOT NULL DEFAULT 0,
    `is_active`   TINYINT(1)      NOT NULL DEFAULT 1,
    `sort_order`  INT             NOT NULL DEFAULT 0,
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME        NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_active_featured` (`is_active`, `is_featured`),
    KEY `idx_category`        (`category_id`),
    KEY `idx_sort`            (`sort_order`),
    CONSTRAINT `fk_track_category`
        FOREIGN KEY (`category_id`) REFERENCES `track_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Заявки ───
CREATE TABLE IF NOT EXISTS `orders` (
    `id`               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `order_number`     VARCHAR(20)     NOT NULL,
    `occasion`         VARCHAR(50)     NOT NULL DEFAULT '',
    `occasion_other`   VARCHAR(100)    NOT NULL DEFAULT '',
    `event_date`       DATE            NULL,
    `urgency`          VARCHAR(20)     NOT NULL DEFAULT 'normal',
    `hero_name`        VARCHAR(100)    NOT NULL DEFAULT '',
    `hero_age`         TINYINT UNSIGNED NULL,
    `hero_relation`    VARCHAR(100)    NOT NULL DEFAULT '',
    `hero_profession`  VARCHAR(100)    NOT NULL DEFAULT '',
    `hero_hobbies`     VARCHAR(200)    NOT NULL DEFAULT '',
    `story`            TEXT            NOT NULL,
    `must_include`     TEXT            NULL,
    `avoid`            TEXT            NULL,
    `mood`             VARCHAR(30)     NOT NULL DEFAULT '',
    `music_styles`     VARCHAR(300)    NOT NULL DEFAULT '',
    `voice_type`       VARCHAR(20)     NOT NULL DEFAULT '',
    `duration`         VARCHAR(20)     NOT NULL DEFAULT 'standard',
    `tariff`           VARCHAR(20)     NOT NULL DEFAULT '',
    `extra_wishes`     TEXT            NULL,
    `client_name`      VARCHAR(100)    NOT NULL DEFAULT '',
    `client_phone`     VARCHAR(20)     NOT NULL DEFAULT '',
    `client_telegram`  VARCHAR(100)    NOT NULL DEFAULT '',
    `client_whatsapp`  VARCHAR(20)     NOT NULL DEFAULT '',
    `client_vk`        VARCHAR(255)    NOT NULL DEFAULT '',
    `client_ok`        VARCHAR(255)    NOT NULL DEFAULT '',
    `client_email`     VARCHAR(150)    NOT NULL DEFAULT '',
    `contact_time`     VARCHAR(20)     NOT NULL DEFAULT 'any',
    `contact_method`   VARCHAR(20)     NOT NULL DEFAULT 'phone',
    `status`           ENUM('new','in_progress','review','done','cancelled') NOT NULL DEFAULT 'new',
    `ip_address`       VARCHAR(45)     NOT NULL DEFAULT '',
    `user_agent`       VARCHAR(500)    NOT NULL DEFAULT '',
    `created_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME        NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_order_number` (`order_number`),
    KEY `idx_status`     (`status`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_phone`      (`client_phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Лог заявок ───
CREATE TABLE IF NOT EXISTS `order_logs` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id`   INT UNSIGNED NOT NULL,
    `action`     VARCHAR(50)  NOT NULL DEFAULT '',
    `old_status` VARCHAR(20)  NOT NULL DEFAULT '',
    `new_status` VARCHAR(20)  NOT NULL DEFAULT '',
    `note`       TEXT         NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_order_id` (`order_id`),
    CONSTRAINT `fk_log_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Прослушивания ───
CREATE TABLE IF NOT EXISTS `track_plays` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `track_id`   INT UNSIGNED    NOT NULL,
    `ip_address` VARCHAR(45)     NOT NULL DEFAULT '',
    `user_agent` VARCHAR(500)    NOT NULL DEFAULT '',
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_track_id`  (`track_id`),
    KEY `idx_ip_track`  (`ip_address`, `track_id`),
    KEY `idx_created_at`(`created_at`),
    CONSTRAINT `fk_play_track` FOREIGN KEY (`track_id`) REFERENCES `tracks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Отзывы ───
CREATE TABLE IF NOT EXISTS `reviews` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `author_name`  VARCHAR(100) NOT NULL DEFAULT '',
    `author_city`  VARCHAR(100) NOT NULL DEFAULT '',
    `rating`       TINYINT      NOT NULL DEFAULT 5,
    `text`         TEXT         NOT NULL,
    `occasion_tag` VARCHAR(50)  NOT NULL DEFAULT '',
    `is_featured`  TINYINT(1)   NOT NULL DEFAULT 0,
    `is_active`    TINYINT(1)   NOT NULL DEFAULT 1,
    `sort_order`   INT          NOT NULL DEFAULT 0,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_active_featured` (`is_active`, `is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Сессии админов ───
CREATE TABLE IF NOT EXISTS `admin_sessions` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `admin_id`      INT UNSIGNED NOT NULL,
    `token`         VARCHAR(64)  NOT NULL,
    `ip_address`    VARCHAR(45)  NOT NULL DEFAULT '',
    `user_agent`    VARCHAR(500) NOT NULL DEFAULT '',
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_activity` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_token`    (`token`),
    KEY `idx_admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Администраторы ───
CREATE TABLE IF NOT EXISTS `admins` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(50)  NOT NULL,
    `email`         VARCHAR(150) NOT NULL DEFAULT '',
    `password_hash` VARCHAR(255) NOT NULL,
    `name`          VARCHAR(100) NOT NULL DEFAULT '',
    `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_username` (`username`),
    UNIQUE KEY `uq_email`    (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Сообщения контактной формы ───
CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100) NOT NULL DEFAULT '',
    `contact`    VARCHAR(100) NOT NULL DEFAULT '',
    `message`    TEXT         NOT NULL,
    `ip_address` VARCHAR(45)  NOT NULL DEFAULT '',
    `is_read`    TINYINT(1)   NOT NULL DEFAULT 0,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_is_read`    (`is_read`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Просмотры страниц (аналитика) ───
CREATE TABLE IF NOT EXISTS `page_views` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `page`       VARCHAR(255)    NOT NULL DEFAULT '',
    `ip_address` VARCHAR(45)     NOT NULL DEFAULT '',
    `referer`    VARCHAR(500)    NOT NULL DEFAULT '',
    `user_agent` VARCHAR(500)    NOT NULL DEFAULT '',
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_page`       (`page`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════════════════════════
-- НАЧАЛЬНЫЕ ДАННЫЕ
-- ═══════════════════════════════════════════════════════════

-- Категории треков
INSERT IGNORE INTO `track_categories` (name, slug, icon, sort_order) VALUES
    ('Свадьбы',       'wedding',     '💒', 1),
    ('Юбилеи',        'anniversary', '🎂', 2),
    ('Дни рождения',  'birthday',    '🎉', 3),
    ('Корпоративы',   'corporate',   '🏢', 4),
    ('Праздники',     'holiday',     '🎄', 5),
    ('Застольные',    'застольные',  '🍷', 6),
    ('Особые поводы', 'special',     '💕', 7),
    ('Детские',       'children',    '🧸', 8);

-- Демо-отзывы
INSERT IGNORE INTO `reviews` (author_name, author_city, rating, text, occasion_tag, is_featured, is_active, sort_order) VALUES
    ('Елена К.',    'Москва',          5, 'Заказали песню на юбилей мамы. Вся семья была в слезах — настолько точно попали в образ! Мама теперь слушает каждый день. Огромное спасибо!', '🎂 Юбилей', 1, 1, 1),
    ('Андрей М.',   'Санкт-Петербург', 5, 'Сделали корпоративный гимн для нашей компании. Всё профессионально, быстро, по делу. Команда пришла в восторг на корпоративе!', '🏢 Корпоратив', 1, 1, 2),
    ('Наталья В.',  'Казань',          5, 'Подарила мужу песню на годовщину — это было незабываемо. Ребята учли все детали нашей истории. Буду заказывать ещё!', '💕 Годовщина', 1, 1, 3),
    ('Дмитрий С.',  'Екатеринбург',   5, 'Заказывал песню для мамы на 8 марта. Результат превзошёл все ожидания — мама плакала от счастья. Профессионалы своего дела!', '🌸 8 Марта', 0, 1, 4),
    ('Ирина П.',    'Новосибирск',     5, 'Детская песенка для дочки на день рождения — это было нечто! Дочь пела её весь вечер. Спасибо за такой подарок!', '🎉 День рождения', 0, 1, 5);

-- Администратор по умолчанию
-- Пароль: admin123 (ОБЯЗАТЕЛЬНО смените после установки!)
INSERT IGNORE INTO `admins` (username, email, password_hash, name, is_active) VALUES
    ('admin', 'admin@hit.owlex.top',
     '$argon2id$v=19$m=65536,t=4,p=1$c29tZXNhbHQ$RdescudvJCsgt3ub+b+dWRWJTmaaJObG', -- admin123
     'Администратор', 1);
```

---

## Файл 11: `includes/config.php` — финальная версия

```php
<?php
/**
 * Конфигурация сайта "Хитовая Песня"
 * ОБЯЗАТЕЛЬНО заполните все настройки перед запуском!
 *
 * Путь: /includes/config.php
 */

declare(strict_types=1);

// ─── Защита от прямого вызова ───
if (!defined('APP_START')) {
    define('APP_START', microtime(true));
}

// ─── Режим работы ───
define('APP_ENV', getenv('APP_ENV') ?: 'production'); // production | development

// ─── PHP настройки ───
error_reporting(APP_ENV === 'development' ? E_ALL : 0);
ini_set('display_errors',  APP_ENV === 'development' ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/errors.log');

// ─── Кодировка ───
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
date_default_timezone_set('Europe/Moscow');

// ─── Пути ───
define('BASE_PATH',   realpath(__DIR__ . '/..'));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('LOG_PATH',    BASE_PATH . '/logs');
define('UPLOAD_PATH', BASE_PATH . '/uploads');

// Создаём директории если нет
foreach ([LOG_PATH, UPLOAD_PATH . '/tracks', UPLOAD_PATH . '/covers'] as $dir) {
    if (!is_dir($dir)) @mkdir($dir, 0750, true);
}

// ─── Сайт ───
define('SITE_NAME',   'Хитовая Песня');
define('SITE_SLOGAN', 'Исполнение ваших желаний');
define('SITE_URL',    rtrim(getenv('SITE_URL') ?: 'https://hit.owlex.top', '/'));

// ─── База данных ───
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'hitsong');
define('DB_USER', getenv('DB_USER') ?: 'hitsong_user');
define('DB_PASS', getenv('DB_PASS') ?: 'CHANGE_ME_DB_PASSWORD');
define('DB_CHARSET', 'utf8mb4');

// ─── Email ───
define('ADMIN_EMAIL',   getenv('ADMIN_EMAIL')   ?: 'admin@hit.owlex.top');
define('SMTP_HOST',     getenv('SMTP_HOST')     ?: 'smtp.yandex.ru');
define('SMTP_PORT',     getenv('SMTP_PORT')     ?: '465');
define('SMTP_USER',     getenv('SMTP_USER')     ?: '');
define('SMTP_PASS',     getenv('SMTP_PASS')     ?: '');
define('SMTP_SECURE',   getenv('SMTP_SECURE')   ?: 'ssl'); // ssl | tls

// ─── Telegram ───
define('TELEGRAM_BOT_TOKEN', getenv('TELEGRAM_BOT_TOKEN') ?: '');
define('TELEGRAM_CHAT_ID',   getenv('TELEGRAM_CHAT_ID')   ?: '');
define('TELEGRAM_USERNAME',  getenv('TELEGRAM_USERNAME')  ?: '@hitpesnya');

// ─── Контакты ───
define('CONTACT_PHONE',   getenv('CONTACT_PHONE')   ?: '+7 (999) 999-99-99');
define('WHATSAPP_NUMBER', getenv('WHATSAPP_NUMBER') ?: '+79999999999');
define('VK_PAGE',         getenv('VK_PAGE')         ?: 'hitpesnya');
define('OK_PAGE',         getenv('OK_PAGE')         ?: 'hitpesnya');

// ─── Безопасность ───
define('CSRF_SECRET', getenv('CSRF_SECRET') ?: 'CHANGE_ME_TO_RANDOM_64_CHAR_STRING');
```

---

## Файл 12: `public/api/page-view.php` (трекинг посещений)

```php
<?php
/**
 * API: Счётчик просмотров страниц (вызывается через Beacon API)
 * Путь: /public/api/page-view.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$page    = mb_substr(trim($_POST['page'] ?? $_SERVER['HTTP_REFERER'] ?? ''), 0, 255, 'UTF-8');
$ip      = get_client_ip();
$referer = mb_substr($_SERVER['HTTP_REFERER'] ?? '', 0, 500, 'UTF-8');
$ua      = mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500, 'UTF-8');

// Rate limiting: не более 60 просмотров в минуту с одного IP
if (!check_rate_limit('pageview_' . $ip, 60, 60)) {
    http_response_code(429);
    exit;
}

// Фильтруем ботов
$bot_patterns = ['bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 'python', 'php'];
$ua_lower = strtolower($ua);
foreach ($bot_patterns as $pattern) {
    if (str_contains($ua_lower, $pattern)) {
        http_response_code(204);
        exit;
    }
}

try {
    $db = Database::getInstance();
    $db->execute(
        "INSERT INTO page_views (page, ip_address, referer, user_agent, created_at)
         VALUES (:page, :ip, :ref, :ua, NOW())",
        [':page' => $page, ':ip' => $ip, ':ref' => $referer, ':ua' => $ua]
    );
} catch (Exception $e) {
    log_error('page-view: ' . $e->getMessage());
}

http_response_code(204);
exit;
```

Добавьте в `main.js` вызов трекера:

```javascript
// ─── Трекинг просмотров страниц ───
(function trackPageView() {
    if ('sendBeacon' in navigator) {
        const data = new FormData();
        data.append('page', window.location.pathname);
        navigator.sendBeacon('/api/page-view.php', data);
    }
})();
```

---

## Финальный CSS для ошибок (добавьте в `main.css`)

```css
/* ═══════════════════════════════════════
   СТРАНИЦЫ ОШИБОК
═══════════════════════════════════════ */

.error-section {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - var(--header-height));
    padding-block: var(--space-3xl);
}

.error-card {
    text-align: center;
    max-width: 520px;
    margin-inline: auto;
}

.error-card__code {
    font-family: var(--font-heading);
    font-size: clamp(80px, 15vw, 140px);
    font-weight: var(--font-weight-black);
    color: var(--color-primary);
    opacity: 0.15;
    line-height: 1;
    margin-bottom: -20px;
}

.error-card__icon {
    font-size: 64px;
    margin-bottom: var(--space-md);
}

.error-card__title {
    font-family: var(--font-heading);
    font-size: var(--font-size-h2);
    font-weight: var(--font-weight-bold);
    color: var(--color-text);
    margin-bottom: var(--space-md);
}

.error-card__title::after { display: none; }

.error-card__desc {
    font-size: var(--font-size-lg);
    color: var(--color-text-muted);
    line-height: var(--line-height-relaxed);
    margin-bottom: var(--space-xl);
}

.error-card__actions {
    display: flex;
    gap: var(--space-sm);
    justify-content: center;
    flex-wrap: wrap;
}

/* ═══════════════════════════════════════
   СТИЛЬ ДЛЯ CURRENT-FILE В АДМИНКЕ
═══════════════════════════════════════ */

.current-file {
    padding: var(--space-sm);
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: var(--radius-md);
    margin-bottom: var(--space-sm);
}

.current-file__label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--color-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 6px;
}

.stats-period-bar {
    display: flex;
    gap: 6px;
    margin-bottom: var(--space-lg);
}
```

---

## ✅ Проект полностью завершён!

### Итоговая таблица всех файлов по этапам:

| Этап | Файлы |
|------|-------|
| **1** | `.htaccess`, `config.php`, `db.php`, `functions.php`, `security.php`, `schema.sql` |
| **2** | `index.php`, 5 CSS, 3 JS, `header.php`, `footer.php`, `head-meta.php` |
| **3** | `portfolio.php`, `portfolio.js`, `api/get-tracks.php`, `api/track-play.php` |
| **4** | `pricing.php` |
| **5** | `order.php`, `thank-you.php`, `form-wizard.js`, `api/submit-order.php`, `mail.php`, `telegram.php` |
| **6** | `contacts.php`, `api/contact.php` |
| **7** | Вся админка: login, dashboard, orders, tracks, stats, CSS, JS |
| **8** | `stats.php`, `track-edit.php`, `.htaccess`, `404.php`, `500.php`, `sitemap.php`, `robots.txt`, `schema.sql` полный, `api/page-view.php` |

### Чек-лист перед запуском:

```
□ Заполнить includes/config.php (БД, email, Telegram, телефон)
□ Импортировать database/schema.sql в MySQL
□ Сменить пароль администратора (admin/login.php → первый вход)
□ Настроить UPLOAD_PATH (права 750 на папку uploads/)
□ Настроить LOG_PATH (права 750 на папку logs/)
□ Прописать Telegram Bot Token и Chat ID
□ Проверить email (SMTP или системный mail())
□ Убедиться что mod_rewrite включён в Apache
□ Включить HTTPS и раскомментировать HSTS в .htaccess
□ Добавить реальные MP3-треки через админку
□ Сменить контакты на реальные в config.php
□ Проверить форму заказа end-to-end
□ Проверить уведомления (email + Telegram)
```
