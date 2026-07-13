<?php
header('Content-Type: text/plain; charset=utf-8');

$mysqli = new mysqli('localhost', 'tlax_usr', '&-6WP7{JPp]O[6S8', 'tlax');

if ($mysqli->connect_error) {
    die("Connect failed: " . $mysqli->connect_error . "\n");
}

$sql = file_get_contents(__DIR__ . '/database/schema_simple.sql');
if ($mysqli->multi_query($sql)) {
    echo "✅ Schema imported!\n";
    $result = $mysqli->query("SHOW TABLES");
    echo "Tables: " . $result->num_rows . "\n";
} else {
    echo "❌ Error: " . $mysqli->error . "\n";
}
$mysqli->close();
