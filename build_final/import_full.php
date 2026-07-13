<?php
$mysqli = new mysqli('localhost', 'tlax_usr', '&-6WP7{JPp]O[6S8', 'tlax');
if ($mysqli->connect_error) die("DB Error: " . $mysqli->connect_error);
$mysqli->set_charset('utf8mb4');

$sql = file_get_contents(__DIR__ . '/database/schema.sql');

// Разделяем на отдельные запросы (MySQL не понимает ; внутри строк)
$statements = array_filter(array_map('trim', explode(';', $sql)));

foreach ($statements as $stmt) {
    if (empty($stmt) || substr($stmt, 0, 2) === '--') continue;
    if (stripos($stmt, 'SET ') !== false) continue;  // Skip SET statements
    if (stripos($stmt, 'CREATE DATABASE') !== false) continue;  // Skip DB creation
    if (stripos($stmt, 'USE ') !== false) continue;  // Skip USE statements
    if ($mysqli->query($stmt)) {
        // ok
    } else {
        // Ignore duplicate key errors
        if ($mysqli->errno != 1062) {
            echo "Error: " . $mysqli->error . " (stmt: " . substr($stmt, 0, 80) . ")\n";
        }
    }
}

echo "Schema imported (or already exists)!\n";

// Show tables
$result = $mysqli->query("SHOW TABLES");
echo "Tables: " . $result->num_rows . "\n";
while ($row = $result->fetch_row()) {
    echo "  - $row[0]\n";
}

$mysqli->close();
