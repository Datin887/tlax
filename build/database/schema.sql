-- ======================================================
-- СХЕМА БАЗЫ ДАННЫХ "ХИТОВАЯ ПЕСНЯ"
-- MySQL 5.7+ / MariaDB 10+
-- Кодировка: utf8mb4 (полная поддержка Unicode и emoji)
-- 
-- Путь: /database/schema.sql
-- Применение: mysql -u user -p hitpesnya < schema.sql
-- ======================================================

-- Создание базы данных (если не существует)
CREATE DATABASE IF NOT EXISTS `hitpesnya`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `hitpesnya`;

-- Отключаем проверки внешних ключей при создании таблиц
SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;
SET time_zone = '+03:00';

-- ======================================================
-- ТАБЛИЦА: admins
-- Администраторы системы
-- ======================================================

CREATE TABLE IF NOT EXISTS `admins` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(50)  NOT NULL UNIQUE COMMENT 'Логин',
    `password_hash` VARCHAR(255) NOT NULL COMMENT 'Хэш пароля (Argon2id)',
    `email`         VARCHAR(100) NOT NULL UNIQUE,
    `display_name`  VARCHAR(100) NOT NULL DEFAULT '' COMMENT 'Отображаемое имя',
    `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
    `last_login_at` DATETIME     NULL     COMMENT 'Последний вход',
    `last_login_ip` VARCHAR(45)  NULL     COMMENT 'IP последнего входа',
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_username` (`username`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Администраторы системы';

-- ======================================================
-- ТАБЛИЦА: admin_sessions
-- Сессии администраторов
-- ======================================================

CREATE TABLE IF NOT EXISTS `admin_sessions` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `admin_id`    INT UNSIGNED    NOT NULL,
    `session_id`  VARCHAR(128)    NOT NULL UNIQUE COMMENT 'ID сессии PHP',
    `ip_address`  VARCHAR(45)     NOT NULL,
    `user_agent`  VARCHAR(255)    NOT NULL DEFAULT '',
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at`  DATETIME        NOT NULL,
    `is_active`   TINYINT(1)      NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    INDEX `idx_session_id` (`session_id`),
    INDEX `idx_admin_id` (`admin_id`),
    INDEX `idx_expires` (`expires_at`),
    FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Сессии администраторов';

-- ======================================================
-- ТАБЛИЦА: orders
-- Заявки на создание песен
-- ======================================================

CREATE TABLE IF NOT EXISTS `orders` (
    `id`                    INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `order_number`          VARCHAR(20)     NOT NULL UNIQUE COMMENT 'Номер HP-00001',

    -- ШАГ 1: Повод
    `occasion`              VARCHAR(50)     NOT NULL COMMENT 'Повод (wedding, birthday...)',
    `occasion_other`        VARCHAR(255)    NULL     COMMENT 'Другой повод (текст)',
    `event_date`            DATE            NULL     COMMENT 'Дата мероприятия',
    `urgency`               ENUM('normal','fast','urgent','very_urgent')
                                            NOT NULL DEFAULT 'normal',

    -- ШАГ 2: Герой
    `hero_name`             VARCHAR(255)    NOT NULL COMMENT 'Имя героя',
    `hero_age`              TINYINT UNSIGNED NULL    COMMENT 'Возраст',
    `hero_relation`         VARCHAR(100)    NULL     COMMENT 'Кем приходится',
    `hero_profession`       VARCHAR(255)    NULL     COMMENT 'Профессия',
    `hero_hobbies`          VARCHAR(500)    NULL     COMMENT 'Хобби и увлечения',

    -- ШАГ 3: История
    `story`                 TEXT            NOT NULL COMMENT 'Основная история',
    `must_include`          TEXT            NULL     COMMENT 'Что обязательно упомянуть',
    `must_exclude`          TEXT            NULL     COMMENT 'Чего избегать',

    -- ШАГ 4: Стиль
    `mood`                  VARCHAR(50)     NULL     COMMENT 'Настроение песни',
    `music_styles`          JSON            NULL     COMMENT 'Стили музыки (массив)',
    `voice_type`            VARCHAR(50)     NULL     COMMENT 'Тип голоса',
    `duration_type`         ENUM('short','standard','long')
                                            NOT NULL DEFAULT 'standard',

    -- ШАГ 5: Тариф
    `tariff`                ENUM('basic','standard','premium','corporate','unknown')
                                            NOT NULL DEFAULT 'standard',
    `extra_wishes`          TEXT            NULL     COMMENT 'Дополнительные пожелания',

    -- ШАГ 6: Контакты
    `client_name`           VARCHAR(255)    NOT NULL COMMENT 'Имя клиента',
    `client_phone`          VARCHAR(20)     NOT NULL COMMENT 'Телефон',
    `client_telegram`       VARCHAR(100)    NULL     COMMENT 'Telegram',
    `client_whatsapp`       VARCHAR(20)     NULL     COMMENT 'WhatsApp',
    `client_ok`             VARCHAR(255)    NULL     COMMENT 'Одноклассники (ссылка)',
    `client_vk`             VARCHAR(255)    NULL     COMMENT 'ВКонтакте (ссылка)',
    `client_email`          VARCHAR(150)    NULL     COMMENT 'Email',
    `contact_time`          VARCHAR(50)     NULL     COMMENT 'Удобное время связи',
    `preferred_contact`     VARCHAR(50)     NULL     COMMENT 'Предпочтительный способ',

    -- Мета
    `status`                ENUM('new','in_progress','review','completed','cancelled')
                                            NOT NULL DEFAULT 'new',
    `manager_notes`         TEXT            NULL     COMMENT 'Заметки менеджера',
    `admin_id`              INT UNSIGNED    NULL     COMMENT 'Менеджер, взявший заявку',
    `price_calculated`      DECIMAL(10,2)   NULL     COMMENT 'Рассчитанная стоимость',
    `paid_at`               DATETIME        NULL     COMMENT 'Время оплаты',

    -- Техническое
    `ip_address`            VARCHAR(45)     NOT NULL COMMENT 'IP при отправке',
    `user_agent`            VARCHAR(500)    NOT NULL DEFAULT '',
    `referrer`              VARCHAR(500)    NULL     COMMENT 'Источник (UTM)',
    `utm_source`            VARCHAR(100)    NULL,
    `utm_medium`            VARCHAR(100)    NULL,
    `utm_campaign`          VARCHAR(100)    NULL,
    `notification_sent`     TINYINT(1)      NOT NULL DEFAULT 0 COMMENT 'Уведомление отправлено',
    `created_at`            DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`            DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE INDEX `idx_order_number` (`order_number`),
    INDEX `idx_status` (`status`),
    INDEX `idx_occasion` (`occasion`),
    INDEX `idx_tariff` (`tariff`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_client_phone` (`client_phone`),
    INDEX `idx_admin_id` (`admin_id`),
    FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Заявки на создание песен';

-- ======================================================
-- ТАБЛИЦА: order_logs
-- История изменений статусов заявок
-- ======================================================

CREATE TABLE IF NOT EXISTS `order_logs` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id`    INT UNSIGNED    NOT NULL,
    `admin_id`    INT UNSIGNED    NULL     COMMENT 'Кто изменил (NULL = система)',
    `action`      VARCHAR(100)    NOT NULL COMMENT 'Тип действия',
    `old_value`   TEXT            NULL     COMMENT 'Старое значение',
    `new_value`   TEXT            NULL     COMMENT 'Новое значение',
    `comment`     TEXT            NULL     COMMENT 'Комментарий',
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_order_id` (`order_id`),
    INDEX `idx_created_at` (`created_at`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='История изменений заявок';

-- ======================================================
-- ТАБЛИЦА: tracks
-- Треки для портфолио
-- ======================================================

CREATE TABLE IF NOT EXISTS `tracks` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `title`         VARCHAR(255)    NOT NULL COMMENT 'Название трека',
    `description`   TEXT            NULL     COMMENT 'Описание',
    `category`      VARCHAR(50)     NOT NULL COMMENT 'Категория (wedding, birthday...)',
    `subcategory`   VARCHAR(100)    NULL,
    `music_style`   VARCHAR(100)    NULL     COMMENT 'Стиль музыки',
    `mood`          VARCHAR(100)    NULL     COMMENT 'Настроение',
    `voice_type`    VARCHAR(50)     NULL     COMMENT 'Голос (male, female, duet)',
    `duration`      SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Длительность в секундах',
    `language`      VARCHAR(10)     NOT NULL DEFAULT 'ru',
    `audio_file`    VARCHAR(500)    NULL     COMMENT 'Путь к аудиофайлу',
    `cover_image`   VARCHAR(500)    NULL     COMMENT 'Путь к обложке',
    `lyrics`        LONGTEXT        NULL     COMMENT 'Текст песни',
    `plays_count`   INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT 'Число прослушиваний',
    `is_featured`   TINYINT(1)      NOT NULL DEFAULT 0 COMMENT 'На главной',
    `is_active`     TINYINT(1)      NOT NULL DEFAULT 1 COMMENT 'Активен/скрыт',
    `sort_order`    SMALLINT        NOT NULL DEFAULT 0 COMMENT 'Порядок сортировки',
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_category` (`category`),
    INDEX `idx_is_featured` (`is_featured`),
    INDEX `idx_is_active` (`is_active`),
    INDEX `idx_sort_order` (`sort_order`),
    INDEX `idx_plays_count` (`plays_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Треки портфолио';

-- ======================================================
-- ТАБЛИЦА: track_plays
-- Статистика прослушиваний (детальная)
-- ======================================================

CREATE TABLE IF NOT EXISTS `track_plays` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `track_id`    INT UNSIGNED    NOT NULL,
    `ip_address`  VARCHAR(45)     NOT NULL,
    `user_agent`  VARCHAR(255)    NOT NULL DEFAULT '',
    `page`        VARCHAR(50)     NOT NULL DEFAULT 'unknown' COMMENT 'Где слушали (index, portfolio)',
    `played_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_track_id` (`track_id`),
    INDEX `idx_played_at` (`played_at`),
    INDEX `idx_ip` (`ip_address`),
    FOREIGN KEY (`track_id`) REFERENCES `tracks`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Детальная статистика прослушиваний';

-- ======================================================
-- ТАБЛИЦА: reviews
-- Отзывы клиентов
-- ======================================================

CREATE TABLE IF NOT EXISTS `reviews` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `author_name` VARCHAR(100)  NOT NULL COMMENT 'Имя клиента',
    `author_city` VARCHAR(100)  NULL     COMMENT 'Город',
    `rating`      TINYINT       NOT NULL DEFAULT 5 COMMENT '1-5 звёзд',
    `text`        TEXT          NOT NULL COMMENT 'Текст отзыва',
    `occasion_tag` VARCHAR(100) NULL     COMMENT 'Тэг повода (Свадьба, Юбилей...)',
    `is_featured` TINYINT(1)    NOT NULL DEFAULT 0 COMMENT 'На главной',
    `is_active`   TINYINT(1)    NOT NULL DEFAULT 1,
    `sort_order`  SMALLINT      NOT NULL DEFAULT 0,
    `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_is_featured` (`is_featured`),
    INDEX `idx_is_active` (`is_active`),
    INDEX `idx_rating` (`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Отзывы клиентов';

-- ======================================================
-- ТАБЛИЦА: contact_messages
-- Сообщения из формы контактов
-- ======================================================

CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(255)  NOT NULL,
    `contact`     VARCHAR(255)  NOT NULL COMMENT 'Телефон или Telegram',
    `message`     TEXT          NOT NULL,
    `ip_address`  VARCHAR(45)   NOT NULL,
    `is_read`     TINYINT(1)    NOT NULL DEFAULT 0,
    `read_at`     DATETIME      NULL,
    `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_is_read` (`is_read`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Сообщения из формы контактов';

-- ======================================================
-- ТАБЛИЦА: page_views
-- Простая аналитика посещений
-- ======================================================

CREATE TABLE IF NOT EXISTS `page_views` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `page`        VARCHAR(100)    NOT NULL COMMENT 'Страница (index, portfolio...)',
    `ip_address`  VARCHAR(45)     NOT NULL,
    `user_agent`  VARCHAR(255)    NOT NULL DEFAULT '',
    `referer`     VARCHAR(500)    NULL,
    `viewed_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_page` (`page`),
    INDEX `idx_viewed_at` (`viewed_at`),
    INDEX `idx_ip` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Статистика посещений страниц';

-- ======================================================
-- ТАБЛИЦА: settings
-- Настройки сайта (key-value)
-- ======================================================

CREATE TABLE IF NOT EXISTS `settings` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `key`         VARCHAR(100)  NOT NULL UNIQUE,
    `value`       TEXT          NULL,
    `description` VARCHAR(255)  NULL,
    `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `idx_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Настройки сайта';

-- ======================================================
-- Включаем обратно проверки внешних ключей
-- ======================================================

SET FOREIGN_KEY_CHECKS = 1;

-- ======================================================
-- НАЧАЛЬНЫЕ ДАННЫЕ
-- ======================================================

-- --- Администратор по умолчанию ---
-- Пароль: Admin123! (ОБЯЗАТЕЛЬНО сменить после установки!)
-- Хэш сгенерирован через password_hash('Admin123!', PASSWORD_ARGON2ID)
INSERT INTO `admins` (`username`, `password_hash`, `email`, `display_name`) VALUES
(
    'admin',
    '$argon2id$v=19$m=65536,t=4,p=1$PLACEHOLDER_HASH',
    'admin@hit.owlex.top',
    'Администратор'
);
-- ВАЖНО: После установки запустить скрипт setup/generate-password.php
-- или вручную обновить хэш через PHP:
-- UPDATE admins SET password_hash = '<hash>' WHERE username = 'admin';

-- --- Демо-треки для портфолио ---
INSERT INTO `tracks` 
    (`title`, `description`, `category`, `music_style`, `mood`, `voice_type`, `duration`, `is_featured`, `is_active`, `sort_order`) 
VALUES
(
    'Свадебный вальс',
    'Трогательная песня о первом танце молодожёнов. История любви, рассказанная в музыке.',
    'wedding', 'Поп', 'Романтичное', 'female', 198, 1, 1, 1
),
(
    'Маме в день рождения',
    'Тёплая песня-признание для самого близкого человека. Лирика о маминой любви и заботе.',
    'birthday', 'Бардовская', 'Трогательное, лирическое', 'male', 215, 1, 1, 2
),
(
    'Корпоративный гимн',
    'Зажигательная песня для команды! Объединяет коллектив и поднимает дух.',
    'corporate', 'Поп', 'Весёлое, зажигательное', 'duet', 187, 1, 1, 3
),
(
    'Дедушке — 70 лет!',
    'Торжественная юбилейная песня с юмором и теплотой. Вся семья пела хором!',
    'anniversary', 'Ретро (советская эстрада)', 'Торжественное, величественное', 'male', 223, 1, 1, 4
),
(
    'Малышке 3 годика',
    'Весёлая детская песенка с любимыми персонажами и именем ребёнка.',
    'children', 'Поп', 'Весёлое, зажигательное', 'female', 165, 1, 1, 5
),
(
    'Любимой на 8 Марта',
    'Романтичная песня-признание. Нежная, как весенний ветер.',
    'special', 'Джаз/Блюз', 'Романтичное', 'male', 204, 1, 1, 6
),
(
    'Застольная на юбилей',
    'Весёлая песня для банкета. Все гости подхватили припев!',
    'anniversary', 'Шансон', 'Весёлое, зажигательное', 'male', 232, 0, 1, 7
),
(
    'Новогодний корпоратив',
    'Праздничная песня с упоминанием всех отделов компании и весёлыми куплетами.',
    'corporate', 'Поп', 'Весёлое, зажигательное', 'duet', 196, 0, 1, 8
);

-- --- Отзывы клиентов ---
INSERT INTO `reviews` 
    (`author_name`, `author_city`, `rating`, `text`, `occasion_tag`, `is_featured`, `sort_order`) 
VALUES
(
    'Анна и Дмитрий',
    'Москва',
    5,
    'Заказали песню на свадьбу — не могли оторваться когда услышали! Все гости плакали от умиления. Ребята всё учли: и как мы познакомились, и наши любимые места. Это было лучшее украшение торжества!',
    '💒 Свадьба',
    1, 1
),
(
    'Ольга М.',
    'Санкт-Петербург',
    5,
    'Заказывала песню маме на 60-летие. Мама плакала и смеялась одновременно — всё было написано с такой теплотой и точностью! Даже упомянули её любимые пироги и дачу. Спасибо огромное!',
    '🎂 Юбилей',
    1, 2
),
(
    'Команда "АльфаТех"',
    'Казань',
    5,
    'Заказали корпоративный гимн для нашей компании. Получилось настолько круто, что теперь поём его на каждом корпоративе! Ребята вникли в специфику нашей работы и создали что-то по-настоящему уникальное.',
    '🏢 Корпоратив',
    1, 3
),
(
    'Сергей К.',
    'Екатеринбург',
    5,
    'Делал сюрприз жене на день рождения. Написал все подробности, и песня получилась — будто кто-то прочитал мои мысли и положил их на музыку. Жена в полном восторге!',
    '🎉 День рождения',
    0, 4
),
(
    'Елена Васильева',
    'Новосибирск',
    5,
    'Дочке заказала песню на 5-летие. Малышка слушает её каждый день, знает наизусть и поёт вместе! Столько радости в одной песне — это бесценно.',
    '🧸 Детская',
    0, 5
);

-- --- Настройки сайта ---
INSERT INTO `settings` (`key`, `value`, `description`) VALUES
('site_name',           'Хитовая Песня',                'Название сайта'),
('site_slogan',         'Исполнение ваших желаний',     'Слоган'),
('orders_total',        '500',                          'Показываемое число заказов (обновляется вручную)'),
('years_work',          '5',                            'Лет работы'),
('maintenance_mode',    '0',                            '1 = режим обслуживания'),
('notify_email',        '1',                            'Email-уведомления включены'),
('notify_telegram',     '0',                            'Telegram-уведомления включены');

-- ======================================================
-- ПРЕДСТАВЛЕНИЕ: v_orders_summary
-- Сводная таблица заявок для дашборда
-- ======================================================

CREATE OR REPLACE VIEW `v_orders_summary` AS
SELECT
    DATE(`created_at`) AS order_date,
    COUNT(*)           AS total,
    SUM(CASE WHEN `status` = 'new'         THEN 1 ELSE 0 END) AS new_count,
    SUM(CASE WHEN `status` = 'in_progress' THEN 1 ELSE 0 END) AS in_progress_count,
    SUM(CASE WHEN `status` = 'completed'   THEN 1 ELSE 0 END) AS completed_count,
    SUM(CASE WHEN `status` = 'cancelled'   THEN 1 ELSE 0 END) AS cancelled_count
FROM `orders`
GROUP BY DATE(`created_at`)
ORDER BY order_date DESC;

-- ======================================================
-- ПРОЦЕДУРА: generate_order_number
-- Автоматическая генерация номера заявки
-- ======================================================

DELIMITER //

CREATE PROCEDURE IF NOT EXISTS `generate_order_number`(IN order_id INT, OUT order_num VARCHAR(20))
BEGIN
    SET order_num = CONCAT('HP-', LPAD(order_id, 5, '0'));
END//

DELIMITER ;

-- ======================================================
-- ТРИГГЕР: after_order_insert
-- Автоматически устанавливает номер заявки после вставки
-- ======================================================

DELIMITER //

CREATE TRIGGER IF NOT EXISTS `after_order_insert`
AFTER INSERT ON `orders`
FOR EACH ROW
BEGIN
    DECLARE order_num VARCHAR(20);
    CALL generate_order_number(NEW.id, order_num);
    UPDATE `orders` SET `order_number` = order_num WHERE `id` = NEW.id;
END//

DELIMITER ;

-- ======================================================
-- ТРИГГЕР: after_order_status_update
-- Автоматически логирует изменение статуса
-- ======================================================

DELIMITER //

CREATE TRIGGER IF NOT EXISTS `after_order_status_update`
AFTER UPDATE ON `orders`
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO `order_logs` (`order_id`, `action`, `old_value`, `new_value`)
        VALUES (NEW.id, 'status_changed', OLD.status, NEW.status);
    END IF;
END//

DELIMITER ;

-- ======================================================
-- КОНЕЦ СХЕМЫ
-- ======================================================

-- Проверяем что всё создалось
SELECT 'База данных успешно инициализирована!' AS message;
SELECT TABLE_NAME, TABLE_ROWS, TABLE_COMMENT
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'hitpesnya'
ORDER BY TABLE_NAME;