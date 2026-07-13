<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "Testing public/index.php includes...\n";

// Это как в index.php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
echo "DB: " . DB_NAME . "\n";

try {
    $db = Database::getInstance();
    echo "✅ DB connected\n";
} catch (Exception $e) {
    echo "❌ DB Error: " . $e->getMessage() . "\n";
}
