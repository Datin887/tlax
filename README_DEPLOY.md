# Деплой проекта "Хитовая Песня"

## Что готово
- **Архив:** `tlax-deploy-final.zip` (154KB) — готов к заливке
- **Структура:**
  ```
  / ← .htaccess, index.php, *.php
  /includes/ ← config.php, db.php, functions.php, security.php, mail.php, telegram.php, head-meta.php, header.php, footer.php
  /admin/ ← панель администратора
  /assets/css/ ← CSS стили
  /assets/js/ ← JS скрипты
  /api/ ← API эндпоинты
  /database/ ← schema.sql
  /logs/ ← логи (создать, chmod 750)
  ```

## Инструкция деплоя (fastpanel)

1. **Залить архив** через файловый менеджер fastpanel в `/var/www/tlax_ru_usr/data/www/tlax.ru/`

2. **Распаковать:**
   - `unzip tlax-deploy-final.zip -d /`

3. **Создать БД:**
   ```sql
   CREATE DATABASE tlax_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   -- Импортировать database/schema.sql
   ```

4. **Настроить config.php:**
   - DB_NAME, DB_USER, DB_PASS
   - APP_DOMAIN = 'tlax.ru'
   - TELEGRAM_BOT_TOKEN, ADMIN_EMAIL, VK_PAGE, OK_PAGE, WHATSAPP_NUMBER

5. **Права на папки:**
   ```bash
   chmod 750 logs/
   chmod 750 assets/uploads/tracks/
   chmod 750 assets/uploads/covers/
   ```

## Проверить после деплоя
- https://tlax.ru/ → главная
- https://tlax.ru/pricing.php → тарифы
- https://tlax.ru/order.php → форма заказа (8 шагов)
- https://tlax.ru/admin/ → вход в админку

## Нужны иконки (отсутствуют)
- /assets/img/logo.svg
- /assets/img/og-default.jpg
- /assets/img/icons/apple-touch-icon.png
- /favicon.ico

*Можно добавить позже*
