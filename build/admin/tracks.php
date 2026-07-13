<?php
/**
 * Управление треками портфолио
 * Путь: /admin/tracks.php
 */

declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/includes/auth.php';
require_auth();

$page_title = 'Треки портфолио';
$csrf_token = generate_csrf_token();

// ─── Обработка действий ───
$message  = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = 'Ошибка CSRF';
        $msg_type = 'error';
    } else {
        $action   = $_POST['action'] ?? '';
        $track_id = (int)($_POST['track_id'] ?? 0);

        try {
            $db = Database::getInstance();

            if ($action === 'toggle_active' && $track_id) {
                $db->execute(
                    "UPDATE tracks SET is_active = !is_active WHERE id = :id",
                    [':id' => $track_id]
                );
                $message = 'Видимость обновлена'; $msg_type = 'success';
            }

            if ($action === 'toggle_featured' && $track_id) {
                $db->execute(
                    "UPDATE tracks SET is_featured = !is_featured WHERE id = :id",
                    [':id' => $track_id]
                );
                $message = 'Избранное обновлено'; $msg_type = 'success';
            }

            if ($action === 'delete' && $track_id) {
                $track = $db->fetchOne("SELECT * FROM tracks WHERE id = :id", [':id' => $track_id]);
                if ($track) {
                    // Удаляем файлы
                    if ($track['audio_file']) @unlink(UPLOAD_PATH . '/tracks/' . $track['audio_file']);
                    if ($track['cover_image']) @unlink(UPLOAD_PATH . '/covers/' . $track['cover_image']);
                    $db->execute("DELETE FROM tracks WHERE id = :id", [':id' => $track_id]);
                    $message = 'Трек удалён'; $msg_type = 'success';
                }
            }

        } catch (Exception $e) {
            log_error('admin/tracks: ' . $e->getMessage());
            $message = 'Ошибка: ' . $e->getMessage();
            $msg_type = 'error';
        }
    }
}

// ─── Список треков ───
try {
    $db     = Database::getInstance();
    $tracks = $db->fetchAll(
        "SELECT t.*, c.name AS category_name
         FROM tracks t
         LEFT JOIN track_categories c ON t.category_id = c.id
         ORDER BY t.sort_order ASC, t.created_at DESC"
    );
    $categories = $db->fetchAll("SELECT * FROM track_categories ORDER BY sort_order ASC");
} catch (Exception $e) {
    log_error('admin/tracks: ' . $e->getMessage());
    $tracks = []; $categories = [];
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<?php if ($message): ?>
    <div class="alert alert--<?= $msg_type === 'success' ? 'success' : 'error' ?>">
        <?= h($message) ?>
    </div>
<?php endif; ?>

<div class="admin-card__header" style="margin-bottom: var(--space-md);">
    <div></div>
    <a href="/admin/track-add.php" class="btn btn--primary">+ Добавить трек</a>
</div>

<div class="admin-card">
    <div class="admin-card__header">
        <h2 class="admin-card__title">
            Треки <span class="admin-card__count"><?= count($tracks) ?></span>
        </h2>
    </div>
    <div class="admin-card__body admin-card__body--no-pad">

        <?php if (empty($tracks)): ?>
            <div class="admin-empty" style="padding: var(--space-2xl); text-align:center;">
                <p>🎵 Треков пока нет</p>
                <a href="/admin/track-add.php" class="btn btn--primary" style="margin-top: var(--space-md);">
                    Добавить первый трек
                </a>
            </div>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th width="60">Обложка</th>
                            <th>Название</th>
                            <th>Категория</th>
                            <th>Стиль</th>
                            <th>👂 Прослушиваний</th>
                            <th>Главная</th>
                            <th>Активен</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tracks as $track): ?>
                            <tr class="admin-table__row">
                                <td>
                                    <?php if ($track['cover_image']): ?>
                                        <img
                                            src="/uploads/covers/<?= h($track['cover_image']) ?>"
                                            alt="<?= h($track['title']) ?>"
                                            style="width:48px;height:48px;object-fit:cover;border-radius:8px;"
                                            loading="lazy"
                                        >
                                    <?php else: ?>
                                        <div style="width:48px;height:48px;background:var(--color-accent-light);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px;">🎵</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= h($track['title']) ?></strong>
                                    <?php if ($track['duration']): ?>
                                        <br><small class="text-muted"><?= h(format_duration((int)$track['duration'])) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= h($track['category_name'] ?? '—') ?></td>
                                <td><?= h($track['style'] ?? '—') ?></td>
                                <td style="text-align:center;"><?= number_format((int)$track['plays_count']) ?></td>
                                <td style="text-align:center;">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">
                                        <input type="hidden" name="action" value="toggle_featured">
                                        <input type="hidden" name="track_id" value="<?= (int)$track['id'] ?>">
                                        <button type="submit" class="toggle-btn" title="Вкл/выкл на главной">
                                            <?= $track['is_featured'] ? '⭐' : '☆' ?>
                                        </button>
                                    </form>
                                </td>
                                <td style="text-align:center;">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">
                                        <input type="hidden" name="action" value="toggle_active">
                                        <input type="hidden" name="track_id" value="<?= (int)$track['id'] ?>">
                                        <button type="submit" class="toggle-btn" title="Вкл/выкл видимость">
                                            <?= $track['is_active'] ? '✅' : '❌' ?>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div style="display:flex;gap:6px;">
                                        <a href="/admin/track-edit.php?id=<?= (int)$track['id'] ?>" class="btn btn--sm btn--outline">✏️</a>
                                        <form method="POST" onsubmit="return confirm('Удалить трек «<?= h(addslashes($track['title'])) ?>»?')">
                                            <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="track_id" value="<?= (int)$track['id'] ?>">
                                            <button type="submit" class="btn btn--sm btn--danger">🗑️</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>