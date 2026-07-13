# Ручной деплой на tlax.ru

## Способы доступа
1. **Web-панель fastpanel** → Файловый менеджер → Загрузить zip
2. **SSH-ключ** (если нужен - сообщи)

## Что делать:
1. Зайти в `/var/www/tlax_ru_usr/data/www/tlax.ru`
2. Очистить папку (или сделать backup)
3. Залить содержимое `tlax-deploy.zip`
4. Создать БД `tlax_db` и импортировать `database/schema.sql`

## После деплоя проверить:
- https://tlax.ru/ → должна открываться главная
- https://tlax.ru/pricing.php → тарифы
- https://tlax.ru/order.php → форма заказа
- https://tlax.ru/admin/ → вход в админку

**P.S.** Если дашборд fastpanel доступен - дай доступ, залью сам.