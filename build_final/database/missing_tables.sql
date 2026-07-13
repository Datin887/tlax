USE tlax;

-- Таблица categories для треков
CREATE TABLE IF NOT EXISTS `track_categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `icon` VARCHAR(50) NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO track_categories (id, name, slug, icon) VALUES
(1, 'Свадьба', 'wedding', '💒'),
(2, 'День рождения', 'birthday', '🎉'),
(3, 'Юбилей', 'anniversary', '🎂'),
(4, 'Корпоратив', 'corporate', '🏢');
