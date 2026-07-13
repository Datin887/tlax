<?php
/**
 * Функции для админки
 * Путь: /admin/includes/admin-functions.php
 */

declare(strict_types=1);

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

    // Пробуем через getID3 если есть
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