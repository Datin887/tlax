<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Проверим что index.php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

echo "APP_ROOT = " . APP_ROOT . "\n";
echo "DB_HOST = " . DB_HOST . "\n";
echo "DB_NAME = " . DB_NAME . "\n";

try {
    $db = Database::getInstance();
    echo "DB connected!\n";
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
