CREATE DATABASE IF NOT EXISTS `tlax` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tlax`;

CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `display_name` VARCHAR(100) NOT NULL DEFAULT '',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `last_login_at` DATETIME NULL,
    `last_login_ip` VARCHAR(45) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_number` VARCHAR(20) NOT NULL UNIQUE,
    `occasion` VARCHAR(50) NOT NULL,
    `tariff` VARCHAR(50) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `contact` VARCHAR(255) NOT NULL,
    `whatsapp` VARCHAR(20) NULL,
    `telegram` VARCHAR(50) NULL,
    `email` VARCHAR(100) NULL,
    `dedication` TEXT NULL,
    `duration` INT UNSIGNED NOT NULL,
    `style` VARCHAR(100) NULL,
    `mood` VARCHAR(100) NULL,
    `tempo` VARCHAR(50) NULL,
    `urgency` VARCHAR(50) NOT NULL DEFAULT 'normal',
    `price` INT UNSIGNED NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'new',
    `admin_note` TEXT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tracks` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `artist` VARCHAR(100) NULL,
    `category` VARCHAR(50) NOT NULL,
    `duration` INT UNSIGNED NOT NULL,
    `plays_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `cover_path` VARCHAR(255) NULL,
    `audio_path` VARCHAR(255) NOT NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_is_featured` (`is_featured`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
