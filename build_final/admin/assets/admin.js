/**
 * JavaScript для панели администратора
 * Путь: /admin/assets/admin.js
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {

    /* ─── Мобильный сайдбар ─── */
    const burger  = document.getElementById('admin-burger');
    const sidebar = document.getElementById('admin-sidebar');

    if (burger && sidebar) {
        burger.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        document.addEventListener('click', (e) => {
            if (sidebar.classList.contains('open') &&
                !sidebar.contains(e.target) &&
                !burger.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    }

    /* ─── Кликабельные строки таблицы ─── */
    document.querySelectorAll('.admin-table__row[data-href]').forEach(row => {
        row.addEventListener('click', (e) => {
            // Не переходим при клике на ссылку или кнопку внутри строки
            if (e.target.closest('a, button, form')) return;
            window.location.href = row.dataset.href;
        });
    });

    /* ─── Flash-уведомления из URL ─── */
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('added') === '1' && window.Notify) {
        Notify.success('Готово!', 'Трек добавлен');
    }
    if (urlParams.get('saved') === '1' && window.Notify) {
        Notify.success('Сохранено', 'Изменения сохранены');
    }

});