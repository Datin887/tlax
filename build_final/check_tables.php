<?php
$mysqli = new mysqli('localhost', 'tlax_usr', '&-6WP7{JPp]O[6S8', 'tlax');
$mysqli->set_charset('utf8mb4');
echo "Tables:\n";
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    echo "  $row[0]\n";
}
echo "\nAdmins:\n";
$result = $mysqli->query("SELECT id, username, email FROM admins");
while ($row = $result->fetch_assoc()) {
    echo "  $row[username] ($row[email])\n";
}
$mysqli->close();
