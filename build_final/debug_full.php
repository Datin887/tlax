<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "Step 1: start\n";

require_once __DIR__ . '/includes/config.php';
echo "APP_ROOT = " . APP_ROOT . "\n";
echo "DB_NAME = " . DB_NAME . "\n";

require_once __DIR__ . '/includes/db.php';
echo "DB loaded\n";

require_once __DIR__ . '/includes/functions.php';
echo "Functions loaded\n";

require_once __DIR__ . '/includes/security.php';
echo "Security loaded\n";

echo "Step 2: DB connect\n";
try {
    $db = Database::getInstance();
    echo "DB connected\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
