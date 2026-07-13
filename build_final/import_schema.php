<?php
$mysqli = new mysqli('localhost', 'tlax_usr', '&-6WP7{JPp]O[6S8', 'tlax');
if ($mysqli->connect_error) die("DB Error: " . $mysqli->connect_error);
$mysqli->set_charset('utf8mb4');

$sql = file_get_contents(__DIR__ . '/database/schema.sql');

// Простой парсер: убираем комментарии, SET, USE, CF DATABASE
$lines = explode("\n", $sql);
$statement = '';
$ok = 0;
$skip = false;

foreach ($lines as $line) {
    $trim = trim($line);
    
    // Skip comments and empty lines
    if (empty($trim) || substr($trim, 0, 2) === '--') continue;
    if (strpos($trim, 'DELIMITER') !== false) continue;
    
    // Skip SET/USE/CREATE DATABASE
    if (stripos($trim, 'SET ') === 0) continue;
    if (stripos($trim, 'USE ') === 0) continue;
    if (stripos($trim, 'CREATE DATABASE') === 0) continue;
    
    $statement .= $line . "\n";
    
    // If line ends with ;, execute
    if (substr(trim($statement), -1) === ';') {
        $stmt = trim(rtrim($statement, ";\n\r\t "));
        if (strlen($stmt) > 5) {
            if ($mysqli->query($stmt)) {
                $ok++;
            } else {
                if ($mysqli->errno != 1062 && $mysqli->errno != 1050) {
                    echo "Err ($mysqli->errno): " . substr($stmt, 0, 120) . "\n";
                }
            }
        }
        $statement = '';
    }
}

echo "Imported $ok statements\n";

// Show tables
$result = $mysqli->query("SHOW TABLES");
echo "Tables: " . $result->num_rows . "\n";
while ($row = $result->fetch_row()) {
    echo "  - $row[0]\n";
}

$mysqli->close();
