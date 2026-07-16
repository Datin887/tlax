<?php
/**
 * API: Получение списка треков
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';

header('Content-Type: application/json; charset=utf-8');

// Only AJAX
if (!is_ajax_request()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

// Only GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Rate limiting
if (!check_rate_limit('get_tracks_' . get_client_ip(), 120, 60)) {
    echo json_encode(['success' => false, 'message' => 'Too many requests']);
    exit;
}

// Parameters
$category = preg_replace('/[^a-z0-9_-]/', '', trim($_GET['category'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = min(24, max(1, (int)($_GET['per_page'] ?? 12)));
$featured_only = (int)($_GET['featured'] ?? 0) === 1;
$search = mb_substr(trim($_GET['search'] ?? ''), 0, 100, 'UTF-8');

try {
    $db = Database::getInstance();
    
    $conditions = ['t.is_active = 1'];
    $params = [];
    
    if ($category !== '') {
        $conditions[] = 'c.slug = :category';
        $params[':category'] = $category;
    }
    if ($featured_only) {
        $conditions[] = 't.is_featured = 1';
    }
    if ($search !== '') {
        $conditions[] = 't.title LIKE :search';
        $params[':search'] = '%' . $search . '%';
    }
    
    $where_sql = 'WHERE ' . implode(' AND ', $conditions);
    
    $count_row = $db->fetchOne("SELECT COUNT(t.id) AS total FROM tracks t LEFT JOIN track_categories c ON t.category_id = c.id {$where_sql}", $params);
    $total = (int)($count_row['total'] ?? 0);
    
    $offset = ($page - 1) * $per_page;
    $params[':limit'] = $per_page;
    $params[':offset'] = $offset;
    
    $tracks_raw = $db->fetchAll("
        SELECT t.id, t.title, t.description, t.audio_file, t.cover_image, t.duration,
               t.music_style, t.mood, t.voice_type, t.plays_count, t.is_featured, t.sort_order,
               c.id AS category_id, c.name AS category_name, c.slug AS category_slug, c.icon AS category_icon
        FROM tracks t
        LEFT JOIN track_categories c ON t.category_id = c.id
        {$where_sql}
        ORDER BY t.sort_order ASC, t.is_featured DESC, t.created_at DESC
        LIMIT :limit OFFSET :offset
    ", $params);
    
    $tracks = [];
    foreach ($tracks_raw as $track) {
        $audio_url = '';
        if (!empty($track['audio_file'])) {
            $audio_path = __DIR__ . '/../assets/uploads/tracks/' . $track['audio_file'];
            if (file_exists($audio_path)) {
                $audio_url = SITE_URL . '/assets/uploads/tracks/' . rawurlencode($track['audio_file']);
            }
        }
        
        $cover_url = '';
        if (!empty($track['cover_image'])) {
            $cover_path = __DIR__ . '/../assets/uploads/covers/' . $track['cover_image'];
            if (file_exists($cover_path)) {
                $cover_url = SITE_URL . '/assets/uploads/covers/' . rawurlencode($track['cover_image']);
            }
        }
        
        $tracks[] = [
            'id' => (int)$track['id'],
            'title' => $track['title'],
            'description' => $track['description'] ?? '',
            'audio_url' => $audio_url,
            'cover_url' => $cover_url,
            'duration' => (int)($track['duration'] ?? 0),
            'duration_formatted' => format_duration((int)($track['duration'] ?? 0)),
            'style' => $track['music_style'] ?? '',
            'mood' => $track['mood'] ?? '',
            'voice_type' => $track['voice_type'] ?? '',
            'plays_count' => (int)($track['plays_count'] ?? 0),
            'is_featured' => (bool)$track['is_featured'],
            'category_id' => (int)($track['category_id'] ?? 0),
            'category_name' => $track['category_name'] ?? '',
            'category_slug' => $track['category_slug'] ?? '',
            'category_icon' => $track['category_icon'] ?? '',
        ];
    }
    
    echo json_encode([
        'success' => true,
        'tracks' => $tracks,
        'total' => $total,
        'page' => $page,
        'per_page' => $per_page,
        'has_more' => ($offset + count($tracks)) < $total,
        'loaded' => $offset + count($tracks),
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    log_error('api/get-tracks: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Internal error']);
}
