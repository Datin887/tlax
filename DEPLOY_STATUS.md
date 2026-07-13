# Статус деплоя "Хитовая Песня"

## ✅ Выполнено
- [x] Извлечены все файлы из markdown (6 etapov)
- [x] Создан build с 46 файлами
- [x] Залиты все файлы на сервер tlax.ru через SFTP
- [x] Config обновлён под домен tlax.ru
- [x] Schema.sql обновлён под БД tlax_db

## 🔄 Осталось (через fastpanel)
1. **Создать БД:** `tlax_db` (utf8mb4)
2. **Импортировать:** `database/schema.sql`
3. **Создать пользователя:** `tlax_db_user` с паролем
4. **Обновить config.php:**
   - DB_PASS = пароль БД
   - ADMIN_EMAIL = ваш email
   - TELEGRAM_BOT_TOKEN (если нужно)
   - SMTP настройки (если нужно email-отправка)
5. **Права на папки:**
   ```bash
   chmod 750 logs/
   chmod 750 assets/uploads/tracks/
   chmod 750 assets/uploads/covers/
   ```

## 🔗 Проверка
- https://tlax.ru/ — главная (сейчас 500 до создания БД)
- https://tlax.ru/admin/ — админка
- https://tlax.ru/api/get-tracks.php — API треков
