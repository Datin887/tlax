<?php
/**
 * Функции для админки
 * Путь: /admin/includes/admin-functions.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../includes/id3.php';

/**
 * Отрисовать бэйдж статуса заявки
 *
 * @param string $status
 * @return string HTML
 */
function render_status_badge(string $status): string
{
    $map = [
        'new'         => ['label' => 'Новая',       'class' => 'status-badge--new'],
        'in_progress' => ['label' => 'В работе',    'class' => 'status-badge--progress'],
        'review'      => ['label' => 'Проверка',    'class' => 'status-badge--review'],
        'done'        => ['label' => 'Выполнена',   'class' => 'status-badge--done'],
        'cancelled'   => ['label' => 'Отменена',    'class' => 'status-badge--cancelled'],
    ];

    $info = $map[$status] ?? ['label' => $status, 'class' => ''];

    return sprintf(
        '<span class="status-badge %s">%s</span>',
        htmlspecialchars($info['class'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($info['label'], ENT_QUOTES, 'UTF-8')
    );
}

/**
 * Загрузить аудио файл
 *
 * @param array $file — $_FILES['field']
 * @return array ['success' => bool, 'filename' => string, 'error' => string]
 */
function upload_audio_file(array $file): array
{
    // Проверка ошибок загрузки
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Ошибка загрузки файла (код ' . $file['error'] . ')'];
    }

    // Размер (max 20 МБ)
    $max_size = 20 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'Файл слишком большой (максимум 20 МБ)'];
    }

    // MIME-тип
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mime     = $finfo->file($file['tmp_name']);
    $allowed  = ['audio/mpeg', 'audio/mp3', 'audio/x-mpeg', 'audio/x-mp3'];

    if (!in_array($mime, $allowed, true)) {
        return ['success' => false, 'error' => 'Допускаются только MP3 файлы'];
    }

    // Генерируем уникальное имя
    $ext      = 'mp3';
    $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $dest     = UPLOAD_PATH . '/tracks/' . $filename;

    if (!is_dir(UPLOAD_PATH . '/tracks')) {
        @mkdir(UPLOAD_PATH . '/tracks', 0750, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['success' => false, 'error' => 'Не удалось сохранить файл'];
    }

    return ['success' => true, 'filename' => $filename, 'error' => ''];
}

/**
 * Загрузить изображение (обложку)
 *
 * @param array $file — $_FILES['field']
 * @return array
 */
function upload_image_file(array $file): array
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Ошибка загрузки изображения'];
    }

    $max_size = 5 * 1024 * 1024; // 5 МБ
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'Изображение слишком большое (максимум 5 МБ)'];
    }

    $finfo   = new finfo(FILEINFO_MIME_TYPE);
    $mime    = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    if (!array_key_exists($mime, $allowed)) {
        return ['success' => false, 'error' => 'Допускаются только JPG, PNG, WebP'];
    }

    $ext      = $allowed[$mime];
    $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $dest     = UPLOAD_PATH . '/covers/' . $filename;

    if (!is_dir(UPLOAD_PATH . '/covers')) {
        @mkdir(UPLOAD_PATH . '/covers', 0750, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['success' => false, 'error' => 'Не удалось сохранить изображение'];
    }

    return ['success' => true, 'filename' => $filename, 'error' => ''];
}

/**
 * Получить длительность MP3 в секундах
 *
 * @param string $filepath
 * @return int
 */
function get_audio_duration(string $filepath): int
{
    if (!file_exists($filepath)) return 0;

    // Точный парсер MP3 (MPEG-фреймы + Xing для VBR) — работает без внешних библиотек
    $dur = id3_mp3_duration($filepath);
    if ($dur > 0) return $dur;

    // Пробуем через getID3 если есть (резервный вариант)
    if (class_exists('getID3')) {
        $id3      = new getID3();
        $info     = $id3->analyze($filepath);
        return (int)($info['playtime_seconds'] ?? 0);
    }

    // Фолбэк: читаем заголовок MP3
    try {
        $fp = fopen($filepath, 'rb');
        if (!$fp) return 0;

        $size = filesize($filepath);
        fseek($fp, 0);
        $header = fread($fp, 4);
        fclose($fp);

        // Простая оценка по размеру файла (128 kbps среднее)
        return (int)($size / (128 * 1024 / 8));
    } catch (Throwable) {
        return 0;
    }
}

/**
 * Записать ID3-теги в загруженный MP3 (название, исполнитель, альбом, жанр, год,
 * описание в COMM и обложку 300x300 в APIC).
 *
 * Вызывается автоматически из track-add.php и track-edit.php после сохранения трека.
 *
 * @param string $audio_filename — имя файла в UPLOAD_PATH/tracks/
 * @param string $title          — название трека (TIT2)
 * @param string $description    — описание (COMM)
 * @param string $cover_filename — имя обложки в UPLOAD_PATH/covers/ (может быть пустым → берётся ID3_DEFAULT_COVER)
 * @param string $genre          — жанр (перекроет ID3_GENRE если задан)
 * @return bool
 */
function tag_uploaded_track(string $audio_filename, string $title, string $description, string $cover_filename = '', string $genre = ''): bool
{
    if (empty($audio_filename)) return false;

    $audio_path = UPLOAD_PATH . '/tracks/' . $audio_filename;
    if (!file_exists($audio_path)) return false;

    // Обложка для APIC: используем загруженную, иначе общую для всех файлов
    $cover_src = '';
    if (!empty($cover_filename)) {
        $candidate = UPLOAD_PATH . '/covers/' . $cover_filename;
        if (file_exists($candidate)) $cover_src = $candidate;
    }
    if (empty($cover_src)) {
        $default = defined('ID3_DEFAULT_COVER') ? ID3_DEFAULT_COVER : '';
        if (!empty($default) && file_exists($default)) $cover_src = $default;
    }

    // Ресайз до 300x300 (если не 300x300) во временный файл рядом
    $apic_path = '';
    if (!empty($cover_src)) {
        $apic_path = UPLOAD_PATH . '/covers/_apic_tmp_' . bin2hex(random_bytes(4)) . '.jpg';
        if (!id3_make_cover_300($cover_src, $apic_path)) {
            // GD нет/не смог — вставляем как есть если это JPEG
            $ext = strtolower(pathinfo($cover_src, PATHINFO_EXTENSION));
            if ($ext === 'jpg' || $ext === 'jpeg') {
                $apic_path = $cover_src;
            } else {
                $apic_path = '';
            }
        }
    }

    // COMM (описание в плеере): всегда рекламный блок — видно при шеринге.
    // Название трека (TIT2) — берётся из $title, оно сохраняется всегда.
    $comment = defined('ID3_COMMENT') ? ID3_COMMENT : '';

    $tags = [
        'title'      => $title,
        'artist'     => defined('ID3_ARTIST') ? ID3_ARTIST : 'Хитовая Песня',
        'album'      => defined('ID3_ALBUM')  ? ID3_ALBUM  : '',
        'genre'      => !empty($genre) ? $genre : (defined('ID3_GENRE') ? ID3_GENRE : ''),
        'year'       => defined('ID3_YEAR')   ? ID3_YEAR   : '',
        'comment'    => $comment,
        'cover_path' => $apic_path,
    ];

    try {
        $ok = apply_track_tags($audio_path, $tags);
        log_error('tag_uploaded_track: ' . $audio_filename . ' → ' . ($ok ? 'OK' : 'FAILED'));

        // Чистим временную обложку (кроме случая, когда используется оригинал)
        if (!empty($apic_path) && $apic_path !== $cover_src && file_exists($apic_path)) {
            @unlink($apic_path);
        }
        return $ok;
    } catch (Throwable $e) {
        log_error('tag_uploaded_track: исключение: ' . $e->getMessage());
        if (!empty($apic_path) && $apic_path !== $cover_src && file_exists($apic_path)) {
            @unlink($apic_path);
        }
        return false;
    }
}