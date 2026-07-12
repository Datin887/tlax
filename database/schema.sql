-- ======================================================
-- СХЕМА БАЗЫ ДАННЫХ "ХИТОВАЯ ПЕСНЯ"
-- MySQL 5.7+ / MariaDB 10+
-- Кодировка: utf8mb4
-- ======================================================

CREATE DATABASE IF NOT EXISTS `tlax_db`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `tlax_db`;

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;
SET time_zone = '+03:00';

-- ======================================================
-- ТАБЛИЦА: admins
-- ======================================================

CREATE TABLE IF NOT EXISTS `admins` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(50)  NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `email`         VARCHAR(100) NOT NULL UNIQUE,
    `display_name`  VARCHAR(100) NOT NULL DEFAULT '',
    `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
    `last_login_at` DATETIME     NULL,
    `last_login_ip` VARCHAR(45)  NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_username` (`username`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- ТАБЛИЦА: orders
-- ======================================================

CREATE TABLE IF NOT EXISTS `orders` (
    `id`              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `order_number`    VARCHAR(20)     NOT NULL UNIQUE,

    `occasion`        VARCHAR(50)     NOT NULL,
    `occasion_other`  VARCHAR(255)    NULL,
    `event_date`      DATE            NULL,
    `urgency`         ENUM('normal','fast','urgent','very_urgent') NOT NULL DEFAULT 'normal',

    `hero_name`       VARCHAR(255)    NOT NULL,
    `hero_age`        TINYINT UNSIGNED NULL,
    `hero_relation`   VARCHAR(100)    NULL,
    `hero_profession` VARCHAR(255)    NULL,
    `hero_hobbies`    VARCHAR(500)    NULL,

    `story`           TEXT            NOT NULL,
    `must_include`    TEXT            NULL,
    `must_exclude`    TEXT            NULL,

    `mood`            VARCHAR(50)     NULL,
    `music_styles`    JSON            NULL,
    `voice_type`      VARCHAR(50)     NULL,
    `duration_type`   ENUM('short','standard','long') NOT NULL DEFAULT 'standard',

    `tariff`          ENUM('basic','standard','premium','corporate','unknown') NOT NULL DEFAULT 'standard',
    `extra_wishes`    TEXT            NULL,

    `client_name`     VARCHAR(255)    NOT NULL,
    `client_phone`    VARCHAR(20)     NOT NULL,
    `client_telegram` VARCHAR(100)    NULL,
    `client_whatsapp` VARCHAR(20)     NULL,
    `client_ok`       VARCHAR(255)    NULL,
    `client_vk`       VARCHAR(255)    NULL,
    `client_email`    VARCHAR(150)    NULL,
    `contact_time`    VARCHAR(50)     NULL,
    `preferred_contact` VARCHAR(50)  NULL,

    `status`          ENUM('new','in_progress','review','completed','cancelled') NOT NULL DEFAULT 'new',
    `manager_notes`   TEXT            NULL,
    `admin_id`        INT UNSIGNED    NULL,
    `price_calculated` DECIMAL(10,2)  NULL,
    `paid_at`         DATETIME        NULL,

    `ip_address`      VARCHAR(45)     NOT NULL,
    `user_agent`      VARCHAR(500)    NOT NULL DEFAULT '',
    `utm_source`      VARCHAR(100)    NULL,
    `utm_medium`      VARCHAR(100)    NULL,
    `utm_campaign`    VARCHAR(100)    NULL,
    `notification_sent` TINYINT(1)     NOT NULL DEFAULT 0,
    `created_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE INDEX `idx_order_number` (`order_number`),
    INDEX `idx_status` (`status`),
    INDEX `idx_occasion` (`occasion`),
    INDEX `idx_tariff` (`tariff`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_client_phone` (`client_phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- ТАБЛИЦА: tracks
-- ======================================================

CREATE TABLE IF NOT EXISTS `tracks` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `title`         VARCHAR(255)    NOT NULL,
    `description`   TEXT            NULL,
    `category`      VARCHAR(50)     NOT NULL,
    `subcategory`   VARCHAR(100)    NULL,
    `music_style`   VARCHAR(100)    NULL,
    `mood`          VARCHAR(100)    NULL,
    `voice_type`    VARCHAR(50)     NULL,
    `duration`      SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `language`      VARCHAR(10)     NOT NULL DEFAULT 'ru',
    `audio_file`    VARCHAR(500)    NULL,
    `cover_image`   VARCHAR(500)    NULL,
    `lyrics`        LONGTEXT        NULL,
    `plays_count`   INT UNSIGNED    NOT NULL DEFAULT 0,
    `is_featured`   TINYINT(1)      NOT NULL DEFAULT 0,
    `is_active`     TINYINT(1)      NOT NULL DEFAULT 1,
    `sort_order`    SMALLINT        NOT NULL DEFAULT 0,
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_category` (`category`),
    INDEX `idx_is_featured` (`is_featured`),
    INDEX `idx_is_active` (`is_active`),
    INDEX `idx_sort_order` (`sort_order`),
    INDEX `idx_plays_count` (`plays_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- ТАБЛИЦА: reviews
-- ======================================================

CREATE TABLE IF NOT EXISTS `reviews` (
    `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `author_name`   VARCHAR(100)  NOT NULL,
    `author_city`   VARCHAR(100)  NULL,
    `rating`        TINYINT       NOT NULL DEFAULT 5,
    `text`          TEXT          NOT NULL,
    `occasion_tag`  VARCHAR(100)  NULL,
    `is_featured`   TINYINT(1)    NOT NULL DEFAULT 0,
    `is_active`     TINYINT(1)    NOT NULL DEFAULT 1,
    `sort_order`    SMALLINT      NOT NULL DEFAULT 0,
    `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_is_featured` (`is_featured`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- ТАБЛИЦА: contact_messages
-- ======================================================

CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(255)  NOT NULL,
    `email`         VARCHAR(150)  NOT NULL,
    `phone`         VARCHAR(20)   NULL,
    `subject`       VARCHAR(255)  NOT NULL,
    `message`       TEXT          NOT NULL,
    `ip_address`    VARCHAR(45)   NOT NULL,
    `is_processed`  TINYINT(1)    NOT NULL DEFAULT 0,
    `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_is_processed` (`is_processed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- ТАБЛИЦА: order_logs
-- ======================================================

CREATE TABLE IF NOT EXISTS `order_logs` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id`    INT UNSIGNED    NOT NULL,
    `admin_id`    INT UNSIGNED    NULL,
    `action`      VARCHAR(100)    NOT NULL,
    `old_value`   TEXT            NULL,
    `new_value`   TEXT            NULL,
    `comment`     TEXT            NULL,
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_order_id` (`order_id`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;