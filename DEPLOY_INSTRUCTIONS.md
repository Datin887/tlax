# Инструкция по деплою проекта "Хитовая Песня"

## Готовые файлы
- **Архив:** `tlax-deploy.zip` (19KB) - готов к заливке
- **Файлы:** папка `src/` с полной структурой

## Структура для деплоя

```
/public/           ← веб-доступ (index.php, pricing.php, order.php, contacts.php, 404.php, .htaccess)
/includes/         ← config.php, db.php, functions.php
/admin/            ← админка (index.php)
/assets/css/       ← CSS файлы
/assets/js/        ← JS файлы  
/upload/           ← треки, обложки (через админку)
/logs/             ← логи
/database/         ← schema.sql для импорта
```

## Настройки под fastpanel

### 1. База данных
```sql
-- В phpMyAdmin или через консоль
CREATE DATABASE tlax_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Импортировать database/schema.sql
```

### 2. Настройки в includes/config.php
```php
define('DB_NAME', 'tlax_db');
define('DB_USER', 'tlax_usr'); // или ваш пользователь
define('DB_PASS', 'ваш_пароль_от_БД');
define('APP_DOMAIN', 'tlax.ru');
```

### 3. Права на папки
```bash
chmod 750 logs/ uploads/tracks/
mkdir -p logs/ uploads/tracks/ uploads/covers
```

## Команды деплоя

```bash
# Через SFTP
sftp -P 2222 fastuser@5.35.100.174
put tlax-deploy.zip /
unzip tlax-deploy.zip -d /

# Либо через SCP
scp -P 2222 -r src/* fastuser@5.35.100.174:/www/
```

## Проверка после деплоя
- https://tlax.ru/ - главная
- https://tlax.ru/pricing.php - тарифы
- https://tlax.ru/order.php - форма заказа
- https://tlax.ru/admin/ - админка

---
**Статус:** Готово к деплою, ждём рабочие SFTP-данные 🤝