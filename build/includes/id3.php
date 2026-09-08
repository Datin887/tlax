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

// ─── Таблицы MPEG-audio (для определения длительности) ───

/**
 * Точная длительность MP3 в секундах.
 *
 * 1. Пропускает ID3v2-тег (и footer, если есть).
 * 2. Находит первый MPEG-фрейм (sync 0xFFE), читает версию/слой/битрейт/частоту.
 * 3. Если найден Xing/Info (VBR) — берёт точное число фреймов.
 * 4. Иначе — CBR-оценка: (размер аудио × 8) / битрейт.
 *
 * @param string $filepath — путь к MP3
 * @return int секунды (0 если не удалось)
 */
function id3_mp3_duration(string $filepath): int
{
    // Битрейты (kbps): [Layer I], [Layer II], [Layer III] — для MPEG1 и MPEG2/2.5
    $BR_V1 = [
        [0, 32, 64, 96, 128, 160, 192, 224, 256, 288, 320, 352, 384, 416, 448],     // Layer I
        [0, 32, 48, 56, 64, 80, 96, 112, 128, 160, 192, 224, 256, 320, 384],        // Layer II
        [0, 32, 40, 48, 56, 64, 80, 96, 112, 128, 160, 192, 224, 256, 320],         // Layer III
    ];
    $BR_V2 = [
        [0, 32, 48, 56, 64, 80, 96, 112, 128, 144, 160, 176, 192, 224, 256],        // Layer I
        [0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160],             // Layer II/III
        [0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160],             // Layer II/III
    ];
    $SR_V1  = [44100, 48000, 32000];
    $SR_V2  = [22050, 24000, 16000];
    $SR_V25 = [11025, 12000, 8000];

    if (!file_exists($filepath)) return 0;

    $size = filesize($filepath);
    if ($size < 128) return 0;

    $fp = @fopen($filepath, 'rb');
    if ($fp === false) return 0;

    $offset = 0;

    // ─── Пропустить ID3v2 ───
    $head = @fread($fp, 10);
    if (strlen($head) === 10 && substr($head, 0, 3) === 'ID3') {
        $id3sz = 0;
        for ($i = 6; $i < 10; $i++) $id3sz = ($id3sz << 7) | (ord($head[$i]) & 0x7F);
        $offset = 10 + $id3sz;
        // Footer после ID3v2.4 (флаг 0x10)
        if ((ord($head[5]) & 0x10) !== 0) $offset += 10;
    }

    if ($offset >= $size) { fclose($fp); return 0; }

    // ─── Читаем окно для поиска первого фрейма ───
    $scan = min($size - $offset, 65536);
    fseek($fp, $offset);
    $buf = @fread($fp, $scan);
    fclose($fp);

    $len = strlen($buf);
    $pos = 0;
    while ($pos + 4 <= $len) {
        if (ord($buf[$pos]) === 0xFF && (ord($buf[$pos + 1]) & 0xE0) === 0xE0) break;
        $pos++;
    }
    if ($pos + 4 > $len) return 0;

    $b1 = ord($buf[$pos + 1]);
    $b2 = ord($buf[$pos + 2]);
    $b3 = ord($buf[$pos + 3]);

    $version = ($b1 >> 3) & 0x03;   // 0=2.5, 1=reserved, 2=MPEG2, 3=MPEG1
    $layer   = ($b1 >> 1) & 0x03;   // 1=III, 2=II, 3=I
    $br_idx  = ($b2 >> 4) & 0x0F;
    $sr_idx  = ($b2 >> 2) & 0x03;
    $padding = ($b2 >> 1) & 0x01;

    if ($version === 1 || $layer === 0 || $br_idx === 0 || $br_idx === 15 || $sr_idx === 3) return 0;

    // ─── Битрейт и частота ───
    $bitrate  = 0;
    $samprate = 0;

    if ($version === 3) {
        $samprate = $SR_V1[$sr_idx];
        $bitrate  = $BR_V1[3 - $layer][$br_idx]; // layer 3=I→idx0, 2=II→idx1, 1=III→idx2
    } elseif ($version === 2) {
        $samprate = $SR_V2[$sr_idx];
        $bitrate  = $BR_V2[3 - $layer][$br_idx];
    } else {
        $samprate = $SR_V25[$sr_idx];
        $bitrate  = $BR_V2[3 - $layer][$br_idx];
    }

    if ($bitrate <= 0 || $samprate <= 0) return 0;

    // Сэмплов на фрейм: Layer I=384, Layer II=1152, Layer III: MPEG1=1152, MPEG2/2.5=576
    $spf = 384;
    if ($layer === 2) $spf = 1152;
    if ($layer === 1) $spf = ($version === 3) ? 1152 : 576;

    // ─── Xing/Info (VBR) — ищем в начале аудио ───
    $xing_off = -1;
    $search_start = $pos + 4;
    // MPEG2/2.5 и MPEG1-mono имеют +1 байт side info
    $side = 1;
    if ($version === 3 && (($b3 >> 6) & 0x03) !== 3) $side = 0; // MPEG1 stereo: без side info
    $needle_start = $search_start + $side;
    $needle_end   = min($needle_start + 256, $len - 4);
    for ($i = $needle_start; $i <= $needle_end; $i++) {
        if (ord($buf[$i]) === 0x58 && ord($buf[$i + 1]) === 0x69 && ord($buf[$i + 2]) === 0x6E && ord($buf[$i + 3]) === 0x67) { $xing_off = $i; break; } // 'Xing'
        if (ord($buf[$i]) === 0x49 && ord($buf[$i + 1]) === 0x6E && ord($buf[$i + 2]) === 0x66 && ord($buf[$i + 3]) === 0x6F) { $xing_off = $i; break; } // 'Info'
    }

    if ($xing_off >= 0 && $xing_off + 20 <= $len) {
        $flags = (ord($buf[$xing_off + 4]) << 24) | (ord($buf[$xing_off + 5]) << 16) | (ord($buf[$xing_off + 6]) << 8) | ord($buf[$xing_off + 7]);
        if (($flags & 0x01) !== 0 && $xing_off + 12 <= $len) {
            $frames = (ord($buf[$xing_off + 8]) << 24) | (ord($buf[$xing_off + 9]) << 16) | (ord($buf[$xing_off + 10]) << 8) | ord($buf[$xing_off + 11]);
            if ($frames > 0) {
                return max(1, (int)round($frames * $spf / $samprate));
            }
        }
    }

    // ─── CBR: (размер аудио × 8) / битрейт ───
    $audio_bytes = $size - $offset;
    return max(1, (int)round($audio_bytes * 8 / $bitrate));
}