/**
 * Уведомления и toast-сообщения
 * Путь: /public/assets/js/notifications.js
 */

class Notifications {
    constructor() {
        this.container = null;
        this.init();
    }
    
    init() {
        // Создаём контейнер если нет
        let container = document.querySelector('.notifications-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'notifications-container';
            document.body.appendChild(container);
        }
        this.container = container;
    }
    
    show(message, type = 'success', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification notification--${type}`;
        notification.innerHTML = `
            <button class="notification__close" aria-label="Закрыть">&times;</button>
            <div class="notification__content">${message}</div>
        `;
        
        this.container.appendChild(notification);
        
        // Показываем с анимацией
        setTimeout(() => {
            notification.classList.add('notification--active');
        }, 10);
        
        // Кнопка закрыть
        const closeBtn = notification.querySelector('.notification__close');
        closeBtn.addEventListener('click', () => this.hide(notification));
        
        // Автоскрытие
        if (duration > 0) {
            setTimeout(() => this.hide(notification), duration);
        }
    }
    
    hide(notification) {
        notification.classList.remove('notification--active');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }
    
    success(message, duration) {
        this.show(message, 'success', duration);
    }
    
    error(message, duration) {
        this.show(message, 'error', duration);
    }
}

// Глобальный экземпляр
window.notifications = new Notifications();

// Функции для форм
function showFormError(message) {
    if (window.notifications) {
        window.notifications.error(message);
    }
}

function showFormSuccess(message) {
    if (window.notifications) {
        window.notifications.success(message);
    }
}