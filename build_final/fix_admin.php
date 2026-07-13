<?php
$mysqli = new mysqli('localhost', 'tlax_usr', '&-6WP7{JPp]O[6S8', 'tlax');
$mysqli->set_charset('utf8mb4');
$mysqli->query("UPDATE admins SET email='admin@tlax.ru' WHERE username='admin'");
echo "Admin email updated\n";
$mysqli->close();
