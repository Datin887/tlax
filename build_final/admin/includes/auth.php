<?php
/**
 * Проверка авторизации в админке
 * Подключается в начале каждой страницы админки
 *
 * Путь: /admin/includes/auth.php
 */

declare(strict_types=1);

if (!defined('IN_ADMIN')) {
    define('IN_ADMIN', true);
}

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';

// ─── Настройки сессии ───
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/admin',
        'domain'   => '',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

/**
 * Проверить, авторизован ли текущий пользователь
 * При провале — редирект на страницу входа
 */
function require_auth(): void
{
    if (!is_authenticated()) {
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '/admin/');
        header('Location: /admin/login.php?redirect=' . $redirect);
        exit;
    }

    // Обновляем время последней активности
    $_SESSION['last_activity'] = time();

    // Сессия устарела (неактивна более 2 часов)
    if (isset($_SESSION['last_activity'])) {
        $inactive = time() - $_SESSION['last_activity'];
        if ($inactive > 7200) {
            session_destroy();
            header('Location: /admin/login.php?reason=timeout');
            exit;
        }
    }

    // Регенерация ID сессии каждые 30 минут
    if (!isset($_SESSION['regenerated_at'])) {
        $_SESSION['regenerated_at'] = time();
    } elseif (time() - $_SESSION['regenerated_at'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['regenerated_at'] = time();
    }
}

/**
 * Проверить авторизацию без редиректа
 *
 * @return bool
 */
function is_authenticated(): bool
{
    return !empty($_SESSION['admin_id'])
        && !empty($_SESSION['admin_token'])
        && isset($_SESSION['last_activity']);
}

/**
 * Авторизовать пользователя
 *
 * @param int    $admin_id
 * @param string $token
 */
function login_admin(int $admin_id, string $token): void
{
    session_regenerate_id(true);
    $_SESSION['admin_id']        = $admin_id;
    $_SESSION['admin_token']     = $token;
    $_SESSION['last_activity']   = time();
    $_SESSION['regenerated_at']  = time();
}

/**
 * Разлогинить пользователя
 */
function logout_admin(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '',
            time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

/**
 * Получить данные текущего администратора
 *
 * @return array|null
 */
function get_current_admin(): ?array
{
    static $admin = null;

    if ($admin !== null) return $admin;

    if (!is_authenticated()) return null;

    try {
        $db    = Database::getInstance();
        $admin = $db->fetchOne(
            "SELECT id, username, email, name FROM admins WHERE id = :id AND is_active = 1",
            [':id' => (int)$_SESSION['admin_id']]
        );
    } catch (Exception $e) {
        log_error('auth: ' . $e->getMessage());
        $admin = null;
    }

    return $admin ?: null;
}