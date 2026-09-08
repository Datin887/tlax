<?php
/**
 * ID3v2.3 тегирование MP3 — чистая реализация без внешних зависимостей
 *
 * Пишет внутрь MP3:
 *   TIT2 — название трека
 *   TPE1 — исполнитель (одинаковый для всех)
 *   TALB — альбом (одинаковый)
 *   TCON — жанр (одинаковый)
 *   TYER — год (одинаковый)
 *   COMM — описание трека (русский, UTF-16)
 *   APIC — обложка JPEG 300x300 (одна для всех файлов)
 *
 * Кодировка текста: UTF-16 с BOM (ID3v2.3 encoding 0x01) — понимают
 * Windows, macOS, VLC, Яндекс Музыка, Apple Music и все современные плееры.
 *
 * Путь: /includes/id3.php
 */

declare(strict_types=1);

// ─── Утилиты ───

/**
 * 4 байта big-endian
 */
function id3_be32(int $n): string
{
    return chr(($n >> 24) & 0xFF) . chr(($n >> 16) & 0xFF) . chr(($n >> 8) & 0xFF) . chr($n & 0xFF);
}

/**
 * Syncsafe-размер ID3v2 (4 байта по 7 бит)
 */
function id3_syncsafe(int $value): string
{
    $out = '';
    for ($i = 3; $i >= 0; $i--) {
        $out .= chr(($value >> ($i * 7)) & 0x7F);
    }
    return $out;
}

/**
 * Текст → UTF-16LE с BOM
 */
function id3_utf16(string $text): string
{
    if ($text === '') return '';
    return "\xFF\xFE" . mb_convert_encoding($text, 'UTF-16LE', 'UTF-8');
}

/**
 * Текстовый фрейм (TIT2, TPE1, TALB, TCON, TYER...)
 */
function id3_text_frame(string $frame_id, string $text): string
{
    if ($text === '') return '';
    $data = "\x01" . id3_utf16($text); // encoding 0x01 = UTF-16 c BOM
    return $frame_id . id3_be32(strlen($data)) . "\x00\x00" . $data;
}

/**
 * Фрейм комментария COMM (для описания трека)
 */
function id3_comm_frame(string $text): string
{
    if ($text === '') return '';
    $data = "\x01" . 'rus' . "\x00" . id3_utf16($text); // encoding, язык, короткое описание (пустое), текст
    return 'COMM' . id3_be32(strlen($data)) . "\x00\x00" . $data;
}

/**
 * Фрейм обложки APIC (JPEG)
 */
function id3_apic_frame(string $image_path): string
{
    if (empty($image_path) || !file_exists($image_path)) return '';

    $jpeg = file_get_contents($image_path);
    if ($jpeg === false || strlen($jpeg) < 10) return '';

    // encoding=0x00 (Latin-1 для MIME/описания), MIME, тип 3 (Cover front), пустое описание, данные
    $data = "\x00" . 'image/jpeg' . "\x00" . chr(3) . "\x00" . $jpeg;
    return 'APIC' . id3_be32(strlen($data)) . "\x00\x00" . $data;
}

/**
 * Убрать существующий ID3v2 из начала файла
 */
function id3_strip_v2(string $data): string
{
    if (substr($data, 0, 3) === 'ID3' && strlen($data) >= 10) {
        $size = 0;
        for ($i = 6; $i < 10; $i++) {
            $size = ($size << 7) | (ord($data[$i]) & 0x7F);
        }
        return substr($data, 10 + $size);
    }
    return $data;
}

/**
 * Ресайз изображения до 300x300 (вписать с пропорциями, фон чёрный)
 * Требует GD (на сервере есть). Если GD нет — возвращает false.
 */
function id3_make_cover_300(string $src_path, string $dst_path): bool
{
    if (!function_exists('imagecreatefromstring') || !function_exists('imagecopyresampled')) {
        return false;
    }

    $bin = file_get_contents($src_path);
    if ($bin === false) return false;

    $img = imagecreatefromstring($bin);
    if ($img === false) return false;

    $w = imagesx($img);
    $h = imagesy($img);

    // Уже 300x300 — просто копируем
    if ($w === 300 && $h === 300) {
        imagedestroy($img);
        return file_put_contents($dst_path, $bin) !== false;
    }

    $scale = min(300.0 / $w, 300.0 / $h);
    $nw    = max(1, (int)round($w * $scale));
    $nh    = max(1, (int)round($h * $scale));

    $thumb  = imagecreatetruecolor($nw, $nh);
    $canvas = imagecreatetruecolor(300, 300);
    $black  = imagecolorallocate($canvas, 0, 0, 0);

    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
    imagefill($canvas, 0, 0, $black);
    imagecopy($canvas, $thumb, (int)((300 - $nw) / 2), (int)((300 - $nh) / 2), 0, 0, $nw, $nh);

    $ok = imagejpeg($canvas, $dst_path, 88);

    imagedestroy($img);
    imagedestroy($thumb);
    imagedestroy($canvas);
    return $ok;
}

/**
 * Применить ID3-теги к MP3-файлу.
 *
 * @param string $mp3_path  — путь к MP3 (будет перезаписан)
 * @param array  $tags      — title, artist, album, genre, year, comment, cover_path (путь к JPEG 300x300)
 * @return bool
 */
function apply_track_tags(string $mp3_path, array $tags): bool
{
    if (!file_exists($mp3_path)) return false;

    $data = file_get_contents($mp3_path);
    if ($data === false) return false;

    // Если файл вообще не MP3 (нет фрейма MPEG) — всё равно пропускаем проверку,
    // теги просто будут в начале (валидно для ID3v2)

    $audio  = id3_strip_v2($data);
    $frames = '';

    $frames .= id3_text_frame('TIT2', (string)($tags['title']   ?? ''));
    $frames .= id3_text_frame('TPE1', (string)($tags['artist']  ?? ''));
    $frames .= id3_text_frame('TALB', (string)($tags['album']   ?? ''));
    $frames .= id3_text_frame('TCON', (string)($tags['genre']   ?? ''));
    $frames .= id3_text_frame('TYER', (string)($tags['year']    ?? ''));
    $frames .= id3_comm_frame((string)($tags['comment'] ?? ''));
    $frames .= id3_apic_frame((string)($tags['cover_path'] ?? ''));

    // Ничего писать нечего — не трогаем файл
    if ($frames === '') return true;

    $header = 'ID3' . "\x03\x00\x00" . id3_syncsafe(strlen($frames));
    $result = file_put_contents($mp3_path, $header . $frames . $audio);

    if ($result !== false) {
        // Обновить mtime (сброс кэшей/HEAD-запросов) — touch без внешних утилит
        $fp = @fopen($mp3_path, 'ab');
        if ($fp !== false) {
            fclose($fp);
        }
    }

    return $result !== false;
}