# PROJECT_BRIEF.md — "Хитовая Песня" (tlax)

## 📋 Цель
Создание сайта студии персонализированных песен с портфолио, многошаговой анкетой заказа и админкой.

## 🛠 Стек технологий
- **Backend:** PHP 8.1+ (нативный), MySQL, PDO
- **Frontend:** Vanilla JS, CSS (без фреймворков)
- **Безопасность:** CSRF, honeypot, rate limiting, валидация, сессии

## 📁 Структура проекта
```
/
├── public/           # Веб-доступная папка (index.php, portfolio.php, и т.д.)
├── includes/         # config.php, db.php, functions.php, security.php
├── admin/            # Админка
├── database/         # schema.sql
├── uploads/          # Треки, обложки
└── logs/            # Логи
```

## 🎯 8 Этапов реализации

### Этап 1: Фундамент ✅
- `.htaccess` (безопасность, gzip, кэш, редиректы)
- `includes/config.php` (константы, настройки)
- `includes/db.php` (Singleton, PDO, prepared statements)
- `includes/functions.php` (хелперы)
- `includes/security.php` (CSRF, honeypot, rate limiting)
- `database/schema.sql` (таблицы БД)

### Этап 2: Главная страница
- CSS-переменные и стили (variables.css, main.css, components.css, responsive.css)
- JS (main.js, player.js, notifications.js)
- `public/index.php`

### Этап 3: Портфолио
- `public/portfolio.php` (фильтры по категориям)
- `public/api/get-tracks.php` (AJAX загрузка)
- `public/api/track-play.php` (счётчик прослушиваний)

### Этап 4: Тарифы
- `public/pricing.php`
- Три тарифа: Базовый (2500₽), Стандарт (5000₽), Премиум (10000₽)
- Корпоративный от 15000₽

### Этап 5: Анкета заказа (многошаговая)
- Шаг 1: Повод (wedding, birthday, corporate...)
- Шаг 2: Герой песни (имя, пол, возраст)
- Шаг 3: Тональность (радостная, романтичная...)
- Шаг 4: Текст (стихии, длина, детали)
- Шаг 5: Музыка (стиль, темп, примеры)
- Шаг 6: Загрузка фото/аудио
- Шаг 7: Сроки и оплата
- Шаг 8: Подтверждение

### Этап 6: Контакты
- `public/contacts.php`
- Форма обратной связи

### Этап 7: Админка
- Авторизация
- Управление заявками
- Загрузка треков
- Статистика

### Этап 8: Финализация
- `admin/stats.php`
- Файлы ошибок (400.php, 404.php, 500.php)
- Тесты
- Деплой

## 🔐 Доступы к хостингу
- **Хост:** 5.35.100.174 (fastpanel)
- **SFTP:** tlax_usr / ro8}bw=xscS|x.ho
- **Бэкап:** fastuser01 / &-6WP7{JPp]O[6S8

## 📊 Бюджет/Статус
- **Репозиторий:** https://github.com/Datin887/tlax
- **Файлов в ТЗ:** ~18K строк
- **Статус:** Запуск

---
*Дата запуска:** 2026-07-12*
*Ответственный:** Анна-Корень (оркестратор)**