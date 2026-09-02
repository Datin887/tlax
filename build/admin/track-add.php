<?php
/**
 * Добавление нового трека
 * Путь: /admin/track-add.php
 */

declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/admin-functions.php';
require_auth();

$page_title = 'Добавить трек';
$csrf_token = generate_csrf_token();
$errors     = [];
$message    = '';

try {
    $db         = Database::getInstance();
    $categories = $db->fetchAll("SELECT * FROM track_categories ORDER BY sort_order ASC");
} catch (Exception $e) {
    $categories = [];
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
        $sort_order  = (int)($_POST['sort_order'] ?? 0);

        if (mb_strlen($title, 'UTF-8') < 2) {
            $errors[] = 'Введите название трека';
        }

        // ─── Загрузка аудио ───
        $audio_filename = '';
        if (!empty($_FILES['audio_file']['name'])) {
            $audio_upload = upload_audio_file($_FILES['audio_file']);
            if ($audio_upload['success']) {
                $audio_filename = $audio_upload['filename'];
            } else {
                $errors[] = $audio_upload['error'];
            }
        } else {
            $errors[] = 'Загрузите аудио файл (MP3)';
        }

        // ─── Загрузка обложки ───
        $cover_filename = '';
        if (!empty($_FILES['cover_image']['name'])) {
            $cover_upload = upload_image_file($_FILES['cover_image']);
            if ($cover_upload['success']) {
                $cover_filename = $cover_upload['filename'];
            } else {
                $errors[] = $cover_upload['error'];
            }
        }

        // ─── Длительность из аудио ───
        $duration = 0;
        if ($audio_filename) {
            $duration = get_audio_duration(UPLOAD_PATH . '/tracks/' . $audio_filename);
        }

        if (empty($errors)) {
            try {
                $db->execute(
                    "INSERT INTO tracks (title, description, category_id, audio_file, cover_image, duration, music_style, mood, voice_type, is_featured, is_active, lyrics, sort_order, created_at)
                     VALUES (:title, :desc, :cat_id, :audio, :cover, :dur, :style, :mood, :voice, :featured, :active, :lyrics, :sort, NOW())",
                    [
                        ':title'    => $title,
                        ':desc'     => $description,
                        ':cat_id'   => $category_id ?: null,
                        ':audio'    => $audio_filename,
                        ':cover'    => $cover_filename,
                        ':dur'      => $duration,
                        ':style'    => $style,
                        ':mood'     => $mood,
                        ':voice'    => $voice_type,
                        ':featured' => $is_featured,
                        ':active'   => $is_active,
                        ':lyrics'   => $lyrics,
                        ':sort'     => $sort_order,
                    ]
                );
                header('Location: /admin/tracks.php?added=1');
                exit;
            } catch (Exception $e) {
                log_error('admin/track-add: ' . $e->getMessage());
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
    <a href="/admin/tracks.php">Треки</a> › Добавить
</div>

<form method="POST" action="/admin/track-add.php" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">

    <div class="track-form-grid">

        <!-- Основные данные -->
        <div class="admin-card">
            <div class="admin-card__header"><h2 class="admin-card__title">Основное</h2></div>
            <div class="admin-card__body">

                <div class="form-group">
                    <label class="form-label" for="title">Название <span class="required">*</span></label>
                    <input type="text" id="title" name="title" class="form-input"
                           value="<?= h($_POST['title'] ?? '') ?>" required maxlength="200" autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Описание</label>
                    <textarea id="description" name="description" class="form-textarea" rows="3" maxlength="1000"><?= h($_POST['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="category_id">Категория</label>
                    <select id="category_id" name="category_id" class="form-input">
                        <option value="">— Выберите —</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>"
                                <?= (int)($_POST['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>>
                                <?= h($cat['icon'] ?? '') ?> <?= h($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:var(--space-sm);">
                    <div class="form-group">
                        <label class="form-label" for="style">Стиль</label>
                        <input type="text" id="style" name="style" class="form-input"
                               value="<?= h($_POST['style'] ?? '') ?>" maxlength="100" placeholder="Поп, Рок…">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="mood">Настроение</label>
                        <input type="text" id="mood" name="mood" class="form-input"
                               value="<?= h($_POST['mood'] ?? '') ?>" maxlength="50" placeholder="Весёлое…">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="voice_type">Голос</label>
                        <input type="text" id="voice_type" name="voice_type" class="form-input"
                               value="<?= h($_POST['voice_type'] ?? '') ?>" maxlength="30" placeholder="Мужской…">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="sort_order">Порядок сортировки</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-input"
                           value="<?= (int)($_POST['sort_order'] ?? 0) ?>" min="0" style="max-width:120px;">
                </div>

                <div style="display:flex;gap:var(--space-lg);">
                    <label class="form-check">
                        <input type="checkbox" name="is_active" class="form-check__input" value="1"
                               <?= !isset($_POST['is_active']) || $_POST['is_active'] ? 'checked' : '' ?>>
                        <span class="form-check__label">Активен (виден на сайте)</span>
                    </label>
                    <label class="form-check">
                        <input type="checkbox" name="is_featured" class="form-check__input" value="1"
                               <?= !empty($_POST['is_featured']) ? 'checked' : '' ?>>
                        <span class="form-check__label">⭐ Показать на главной</span>
                    </label>
                </div>

            </div>
        </div>

        <!-- Файлы -->
        <div class="admin-card">
            <div class="admin-card__header"><h2 class="admin-card__title">Файлы</h2></div>
            <div class="admin-card__body">

                <div class="form-group">
                    <label class="form-label" for="audio_file">
                        Аудио файл <span class="required">*</span>
                    </label>
                    <input type="file" id="audio_file" name="audio_file" class="form-input"
                           accept="audio/mpeg,audio/mp3,.mp3" required>
                    <span class="form-hint">MP3, до 20 МБ. Длительность определится автоматически.</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="cover_image">Обложка</label>
                    <input type="file" id="cover_image" name="cover_image" class="form-input"
                           accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp">
                    <span class="form-hint">JPG/PNG/WebP, до 5 МБ, рекомендуется 800×450</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="lyrics">Текст песни</label>
                    <textarea id="lyrics" name="lyrics" class="form-textarea" rows="8"
                              maxlength="10000" placeholder="Текст для отображения…"><?= h($_POST['lyrics'] ?? '') ?></textarea>
                </div>

            </div>
        </div>

    </div><!-- /.track-form-grid -->

    <div style="display:flex;gap:var(--space-sm);margin-top:var(--space-lg);">
        <button type="submit" class="btn btn--primary btn--lg">💾 Сохранить трек</button>
        <a href="/admin/tracks.php" class="btn btn--outline btn--lg">Отмена</a>
    </div>

</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>