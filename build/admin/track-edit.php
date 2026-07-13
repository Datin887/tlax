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