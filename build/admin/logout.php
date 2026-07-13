<?php
/**
 * Выход из админки
 * Путь: /admin/logout.php
 */

declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// CSRF для выхода
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        logout_admin();
    }
} else {
    // GET тоже разрешаем (прямая ссылка)
    logout_admin();
}

header('Location: /admin/login.php');
exit;