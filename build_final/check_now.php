<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '/var/www/tlax_ru_usr/data/www/tlax.ru/public/includes/config.php';
$db = Database::getInstance();

// Проверяем таблицы
$tables = $db->fetchAll("SHOW TABLES");
echo "Tables: " . count($tables) . "\n";
foreach ($tables as $t) echo "  - $t[0]\n";
