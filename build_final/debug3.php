<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "Testing includes...\n";

// Проверяем config.php
$config_path = __DIR__ . '/includes/config.php';
if (!file_exists($config_path)) {
    die("Config not found: $config_path\n");
}
echo "Config exists\n";

// Подключаем
try {
    require_once $config_path;
    echo "Config loaded\n";
    echo "APP_ROOT = $APP_ROOT\n";
    echo "APP_ENV = $APP_ENV\n";
} catch (Throwable $e) {
    echo "Config Error: " . $e->getMessage() . "\n";
}
