<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/plain');

$mysqli = new mysqli('localhost', 'tlax_usr', '&-6WP7{JPp]O[6S8', 'tlax');
if ($mysqli->connect_error) die("DB Error: " . $mysqli->connect_error);
$mysqli->set_charset('utf8mb4');

// Простой CREATE TABLE
$queries = [
// track_categories
"CREATE TABLE IF NOT EXISTS `track_categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `icon` VARCHAR(50) NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

// settings
"CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

// reviews
"CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `text` TEXT NOT NULL,
    `rating` TINYINT NOT NULL DEFAULT 5,
    `event_type` VARCHAR(50) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

// admin sessions (FK to admins.id)
"CREATE TABLE IF NOT EXISTS `admin_sessions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `admin_id` INT UNSIGNED NOT NULL,
    `session_id` VARCHAR(128) NOT NULL UNIQUE,
    `ip_address` VARCHAR(45) NOT NULL,
    `user_agent` VARCHAR(255) NOT NULL DEFAULT '',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at` DATETIME NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    INDEX `idx_session_id` (`session_id`),
    INDEX `idx_admin_id` (`admin_id`),
    FOREIGN KEY (`admin_id`) REFERENCES admins(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
];

foreach ($queries as $sql) {
    if ($mysqli->query($sql)) {
        echo "OK: " . substr($sql, 0, 50) . "...\n";
    } else {
        echo "ERR ($mysqli->errno): " . $mysqli->error . "\n";
    }
}

// Insert default settings
$defaults = [
    ['site_name', 'Хитовая Песня'],
    ['site_slogan', 'Исполнение ваших желаний'],
    ['contact_email', 'admin@tlax.ru'],
    ['contact_phone', '+7 999 999 99 99'],
    ['telegram_bot', ''],
    ['currency', 'RUB'],
];
foreach ($defaults as $d) {
    $mysqli->query("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('$d[0]', '$d[1]')");
}

// Insert categories
$mysqli->query("INSERT IGNORE INTO track_categories (id, name, slug, icon) VALUES (1, 'Свадьба', 'wedding', '💒'), (2, 'День рождения', 'birthday', '🎉'), (3, 'Юбилей', 'anniversary', '🎂'), (4, 'Корпоратив', 'corporate', '🏢')");

// Create admin
$pass_hash = password_hash('admin123', PASSWORD_DEFAULT);
$mysqli->query("INSERT IGNORE INTO admins (username, password_hash, email, display_name) VALUES ('admin', '$pass_hash', 'admin@tlax.ru', 'Administrator')");

// Show all tables
echo "\n=== TABLES ===\n";
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    echo "  $row[0]\n";
}

// Show admin
echo "\n=== ADMINS ===\n";
$result = $mysqli->query("SELECT id, username, email, display_name FROM admins");
while ($row = $result->fetch_assoc()) {
    echo "  $row[id] | $row[username] | $row[email] | $row[display_name]\n";
}

$mysqli->close();
