<?php
/**
 * API: Приём заявки
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!is_ajax_request()) {
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

// CSRF проверка
$csrf_token = $_POST['csrf_token'] ?? '';
if (!csrf_verify($csrf_token)) {
    echo json_encode(['success' => false, 'message' => 'Недействительный токен безопасности']);
    exit;
}

// Rate limiting
$client_ip = get_client_ip();
if (!check_rate_limit('submit_order_' . $client_ip, 5, 3600)) {
    echo json_encode(['success' => false, 'message' => 'Too many requests'], 429);
    exit;
}

// Обработка заявки
try {
    $db = Database::getInstance();
    
    // Данные формы
    $data = [
        'occasion' => $_POST['occasion'] ?? '',
        'hero_name' => $_POST['hero_name'] ?? '',
        'story' => $_POST['story'] ?? '',
        'mood' => $_POST['mood'] ?? '',
        'tariff' => $_POST['tariff'] ?? 'basic',
        'client_name' => $_POST['client_name'] ?? '',
        'client_phone' => $_POST['client_phone'] ?? '',
        'client_email' => $_POST['client_email'] ?? '',
    ];
    
    // Создаём заказ
    $stmt = $db->getConnection()->prepare("
        INSERT INTO orders (occasion, hero_name, story, mood, tariff, client_name, client_phone, client_email, ip_address, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $data['occasion'], $data['hero_name'], $data['story'], 
        $data['mood'], $data['tariff'], $data['client_name'], 
        $data['client_phone'], $data['client_email'], $client_ip
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Заявка принята! Мы свяжемся в ближайшее время.']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
}
