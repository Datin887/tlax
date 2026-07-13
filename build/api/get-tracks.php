<?php
/**
 * API: Получение списка треков
 * Метод: GET
 * Параметры:
 *   category  — slug категории (опционально)
 *   page      — номер страницы (по умолчанию 1)
 *   per_page  — треков на страницу (по умолчанию 12, макс 24)
 *   featured  — только избранные (1/0)
 *   search    — поиск по названию
 *
 * Возвращает JSON
 *
 * Путь: /public/api/get-tracks.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';

// ─── Только AJAX-запросы ───
if (!is_ajax_request()) {
    http_response_code(403);
    send_json(['success' => false, 'message' => 'Forbidden'], 403);
}

// ─── Только GET ───
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send_json(['success' => false, 'message' => 'Method not allowed'], 405);
}

// ─── Rate limiting ───
if (!check_rate_limit('get_tracks_' . get_client_ip(), 120, 60)) {
    send_json(['success' => false, 'message' => 'Too many requests'], 429);
}

// ─── Параметры запроса ───
$category = trim($_GET['category'] ?? '');
$category = preg_replace('/[^a-z0-9_-]/', '', $category); // Санитизация

$page = max(1, (int)($_GET['page'] ?? 1));

$per_page = min(24, max(1, (int)($_GET['per_page'] ?? 12)));

$featured_only = (int)($_GET['featured'] ?? 0) === 1;

$search = trim($_GET['search'] ?? '');
$search = mb_substr($search, 0, 100, 'UTF-8');

try {
    $db = Database::getInstance();

    // ─── Строим WHERE-условия ───
    $conditions = ['t.is_active = 1'];
    $params     = [];

    // Фильтр по категории
    if ($category !== '') {
        $conditions[] = 'c.slug = :category';
        $params[':category'] = $category;
    }

    // Только избранные
    if ($featured_only) {
        $conditions[] = 't.is_featured = 1';
    }

    // Поиск по названию
    if ($search !== '') {
        $conditions[] = 't.title LIKE :search';
        $params[':search'] = '%' . $search . '%';
    }

    $where_sql = 'WHERE ' . implode(' AND ', $conditions);

    // ─── Общее количество ───
    $count_sql = "
        SELECT COUNT(t.id) AS total
        FROM tracks t
        LEFT JOIN track_categories c ON t.category_id = c.id
        {$where_sql}
    ";

    $count_row = $db->fetchOne($count_sql, $params);
    $total     = (int)($count_row['total'] ?? 0);

    // ─── Смещение ───
    $offset = ($page - 1) * $per_page;

    // ─── Основной запрос ───
    $params[':limit']  = $per_page;
    $params[':offset'] = $offset;

    $tracks_sql = "
        SELECT
            t.id,
            t.title,
            t.description,
            t.audio_file,
            t.cover_image,
            t.duration,
            t.style,
            t.mood,
            t.voice_type,
            t.plays_count,
            t.is_featured,
            t.sort_order,
            c.id   AS category_id,
            c.name AS category_name,
            c.slug AS category_slug,
            c.icon AS category_icon
        FROM tracks t
        LEFT JOIN track_categories c ON t.category_id = c.id
        {$where_sql}
        ORDER BY t.sort_order ASC, t.is_featured DESC, t.created_at DESC
        LIMIT :limit OFFSET :offset
    ";

    $tracks_raw = $db->fetchAll($tracks_sql, $params);

    // ─── Форматируем данные для ответа ───
    $tracks = array_map(function (array $track): array {
        $audio_url = '';
        if (!empty($track['audio_file'])) {
            $audio_path = __DIR__ . '/../../uploads/tracks/' . $track['audio_file'];
            if (file_exists($audio_path)) {
                $audio_url = SITE_URL . '/uploads/tracks/' . rawurlencode($track['audio_file']);
            }
        }

        $cover_url = '';
        if (!empty($track['cover_image'])) {
            $cover_path = __DIR__ . '/../../uploads/covers/' . $track['cover_image'];
            if (file_exists($cover_path)) {
                $cover_url = SITE_URL . '/uploads/covers/' . rawurlencode($track['cover_image']);
            }
        }

        return [
            'id'                 => (int)$track['id'],
            'title'              => $track['title'],
            'description'        => $track['description'] ?? '',
            'audio_url'          => $audio_url,
            'cover_url'          => $cover_url,
            'duration'           => (int)($track['duration'] ?? 0),
            'duration_formatted' => format_duration((int)($track['duration'] ?? 0)),
            'style'              => $track['style'] ?? '',
            'mood'               => $track['mood'] ?? '',
            'voice_type'         => $track['voice_type'] ?? '',
            'plays_count'        => (int)($track['plays_count'] ?? 0),
            'is_featured'        => (bool)$track['is_featured'],
            'category_id'        => (int)($track['category_id'] ?? 0),
            'category_name'      => $track['category_name'] ?? '',
            'category_slug'      => $track['category_slug'] ?? '',
            'category_icon'      => $track['category_icon'] ?? '',
        ];
    }, $tracks_raw);

    // ─── Есть ли ещё страницы ───
    $has_more = ($offset + count($tracks)) < $total;

    send_json([
        'success'  => true,
        'tracks'   => $tracks,
        'total'    => $total,
        'page'     => $page,
        'per_page' => $per_page,
        'has_more' => $has_more,
        'loaded'   => $offset + count($tracks),
    ]);

} catch (Exception $e) {
    log_error('api/get-tracks: ' . $e->getMessage());
    send_json(['success' => false, 'message' => 'Внутренняя ошибка сервера'], 500);
}