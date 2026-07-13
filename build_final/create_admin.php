<?php
$mysqli = new mysqli('localhost', 'tlax_usr', '&-6WP7{JPp]O[6S8', 'tlax');
if ($mysqli->connect_error) die($mysqli->connect_error);

$pass_hash = password_hash('admin123', PASSWORD_ARGON2ID);
$stmt = $mysqli->prepare("INSERT IGNORE INTO admins (username, password_hash, email, display_name) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $user, $hash, $email, $name);
$user = 'admin';
$hash = $pass_hash;
$email = 'admin@tlax.ru';
$name = 'Administrator';
$stmt->execute();
echo "Admin created: admin / admin123\n";
$mysqli->close();
