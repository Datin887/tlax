/**
 * Система уведомлений (Toast)
 * Показывает красивые всплывающие сообщения
 * 
 * Путь: /public/assets/js/notifications.js
 * Использование:
 *   Notify.success('Заголовок', 'Текст сообщения');
 *   Notify.error('Ошибка', 'Описание ошибки');
 *   Notify.warning('Внимание', 'Текст');
 *   Notify.info('Инфо', 'Текст');
 */

'use strict';

(function() {

    /* ─── Иконки для типов уведомлений ─── */
    const ICONS = {
        success: '✅',
        error:   '❌',
        warning: '⚠️',
        info:    'ℹ️',
    };

    /* ─── Время показа по умолчанию (мс) ─── */
    const DEFAULT_DURATION = 5000;

    /* ─── Контейнер для тостов ─── */
    let container = null;

    /**
     * Получить или создать контейнер
     * @returns {Element}
     */
    function getContainer() {
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            container.setAttribute('role', 'region');
            container.setAttribute('aria-label', 'Уведомления');
            container.setAttribute('aria-live', 'polite');
            document.body.appendChild(container);
        }
        return container;
    }

    /**
     * Показать уведомление
     * @param {string} type    — success | error | warning | info
     * @param {string} title   — заголовок
     * @param {string} message — текст (опционально)
     * @param {number} duration — время показа в мс (0 = не скрывать)
     * @returns {Element} — DOM-элемент тоста
     */
    function show(type, title, message = '', duration = DEFAULT_DURATION) {
        const cnt  = getContainer();
        const icon = ICONS[type] || ICONS.info;

        const toast = document.createElement('div');
        toast.className = `toast toast--${type}`;
        toast.setAttribute('role', 'alert');
        toast.style.position = 'relative';

        toast.innerHTML = `
            <div class="toast__icon">${icon}</div>
            <div class="toast__content">
                ${title   ? `<div class="toast__title">${escapeHtml(title)}</div>`   : ''}
                ${message ? `<div class="toast__message">${escapeHtml(message)}</div>` : ''}
            </div>
            <button class="toast__close" aria-label="Закрыть уведомление">✕</button>
        `;

        // Кнопка закрытия
        const closeBtn = toast.querySelector('.toast__close');
        closeBtn.addEventListener('click', () => remove(toast));

        // Добавляем в контейнер
        cnt.appendChild(toast);

        // Автоудаление
        let removeTimer = null;
        if (duration > 0) {
            removeTimer = setTimeout(() => remove(toast), duration);
        }

        // Пауза при наведении
        toast.addEventListener('mouseenter', () => {
            if (removeTimer) clearTimeout(removeTimer);
        });

        toast.addEventListener('mouseleave', () => {
            if (duration > 0) {
                removeTimer = setTimeout(() => remove(toast), duration / 2);
            }
        });

        // Ограничение: не более 5 одновременных тостов
        const toasts = cnt.querySelectorAll('.toast');
        if (toasts.length > 5) {
            remove(toasts[0]);
        }

        return toast;
    }

    /**
     * Удалить тост с анимацией
     * @param {Element} toast
     */
    function remove(toast) {
        if (!toast || !toast.isConnected) return;
        toast.classList.add('removing');
        toast.addEventListener('animationend', () => {
            if (toast.isConnected) toast.remove();
        }, { once: true });

        // Фолбэк если анимация не сработала
        setTimeout(() => {
            if (toast.isConnected) toast.remove();
        }, 400);
    }

    /**
     * Экранирование HTML (защита от XSS)
     * @param {string} str
     * @returns {string}
     */
    function escapeHtml(str) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        };
        return String(str).replace(/[&<>"']/g, m => map[m]);
    }

    /**
     * Показать сообщение об ошибке с полями формы
     * @param {Object} errors — объект {поле: [сообщения]}
     */
    function showFormErrors(errors) {
        const messages = Object.values(errors).flat();
        const text = messages.slice(0, 3).join('; ');
        show('error', 'Исправьте ошибки', text);
    }

    /**
     * Уведомление из PHP (из data-атрибута body)
     * Используется для flash-сообщений после редиректа
     */
    function initFlashMessages() {
        const body = document.body;
        const flash = body.dataset.flash;

        if (flash) {
            try {
                const data = JSON.parse(flash);
                if (data.type && data.title) {
                    // Небольшая задержка, чтобы страница успела отрисоваться
                    setTimeout(() => {
                        show(data.type, data.title, data.message || '');
                    }, 300);
                }
            } catch {
                // Невалидный JSON — игнорируем
            }
        }
    }

    /* ─── Глобальное API ─── */
    window.Notify = {
        success: (title, msg, dur)  => show('success', title, msg, dur),
        error:   (title, msg, dur)  => show('error',   title, msg, dur),
        warning: (title, msg, dur)  => show('warning', title, msg, dur),
        info:    (title, msg, dur)  => show('info',    title, msg, dur),
        show,
        remove,
        showFormErrors,
    };

    /* ─── Инициализация ─── */
    document.addEventListener('DOMContentLoaded', initFlashMessages);

})();