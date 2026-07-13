<?php
$mysqli = new mysqli('localhost', 'tlax_usr', '&-6WP7{JPp]O[6S8', 'tlax');
if ($mysqli->connect_error) die($mysqli->connect_error);
$sql = file_get_contents(__DIR__ . '/missing_tables.sql');
if ($mysqli->multi_query($sql)) {
    echo "Missing tables created\n";
} else {
    echo "Error: " . $mysqli->error . "\n";
}
$mysqli->close();
