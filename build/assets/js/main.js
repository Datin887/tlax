/**
 * Основной JavaScript файл
 * Шапка, мобильное меню, FAQ, scroll reveal, базовые утилиты
 * 
 * Путь: /public/assets/js/main.js
 */

'use strict';

/* ═══════════════════════════════════════
   УТИЛИТЫ
═══════════════════════════════════════ */

/**
 * Короткий querySelector
 * @param {string} selector
 * @param {Element|Document} context
 * @returns {Element|null}
 */
const $ = (selector, context = document) => context.querySelector(selector);

/**
 * Короткий querySelectorAll → Array
 * @param {string} selector
 * @param {Element|Document} context
 * @returns {Element[]}
 */
const $$ = (selector, context = document) => Array.from(context.querySelectorAll(selector));

/**
 * Создать элемент с атрибутами и содержимым
 * @param {string} tag
 * @param {Object} attrs
 * @param {string|Element[]} children
 * @returns {Element}
 */
function createElement(tag, attrs = {}, children = '') {
    const el = document.createElement(tag);
    Object.entries(attrs).forEach(([key, val]) => {
        if (key === 'className') el.className = val;
        else if (key === 'innerHTML') el.innerHTML = val;
        else el.setAttribute(key, val);
    });
    if (typeof children === 'string') {
        el.textContent = children;
    } else if (Array.isArray(children)) {
        children.forEach(child => el.appendChild(child));
    }
    return el;
}

/**
 * Debounce — задержка выполнения функции
 * @param {Function} fn
 * @param {number} delay
 * @returns {Function}
 */
function debounce(fn, delay) {
    let timer;
    return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

/**
 * Throttle — ограничение частоты вызовов
 * @param {Function} fn
 * @param {number} limit
 * @returns {Function}
 */
function throttle(fn, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            fn.apply(this, args);
            inThrottle = true;
            setTimeout(() => (inThrottle = false), limit);
        }
    };
}

/**
 * Форматирование времени (секунды → MM:SS)
 * @param {number} seconds
 * @returns {string}
 */
function formatTime(seconds) {
    if (isNaN(seconds) || seconds < 0) return '0:00';
    const m = Math.floor(seconds / 60);
    const s = Math.floor(seconds % 60);
    return `${m}:${s.toString().padStart(2, '0')}`;
}

/**
 * Получение параметра из URL
 * @param {string} name
 * @returns {string|null}
 */
function getUrlParam(name) {
    const params = new URLSearchParams(window.location.search);
    return params.get(name);
}

/* ═══════════════════════════════════════
   ШАПКА: SCROLL + STICKY
═══════════════════════════════════════ */

function initHeader() {
    const header = $('.header');
    if (!header) return;

    const handleScroll = throttle(() => {
        if (window.scrollY > 20) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }, 100);

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll(); // Инициализация при загрузке
}

/* ═══════════════════════════════════════
   МОБИЛЬНОЕ МЕНЮ
═══════════════════════════════════════ */

function initMobileMenu() {
    const burger      = $('.burger');
    const mobileMenu  = $('.mobile-menu');
    const body        = document.body;

    if (!burger || !mobileMenu) return;

    function openMenu() {
        burger.classList.add('open');
        mobileMenu.classList.add('open');
        body.style.overflow = 'hidden'; // Запрет скролла
        burger.setAttribute('aria-expanded', 'true');
        burger.setAttribute('aria-label', 'Закрыть меню');
    }

    function closeMenu() {
        burger.classList.remove('open');
        mobileMenu.classList.remove('open');
        body.style.overflow = '';
        burger.setAttribute('aria-expanded', 'false');
        burger.setAttribute('aria-label', 'Открыть меню');
    }

    burger.addEventListener('click', () => {
        if (burger.classList.contains('open')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    // Закрытие при клике на ссылку
    $$('.mobile-menu__link', mobileMenu).forEach(link => {
        link.addEventListener('click', closeMenu);
    });

    // Закрытие при клике вне меню (на overlay)
    document.addEventListener('click', (e) => {
        if (
            mobileMenu.classList.contains('open') &&
            !mobileMenu.contains(e.target) &&
            !burger.contains(e.target)
        ) {
            closeMenu();
        }
    });

    // Закрытие по Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && mobileMenu.classList.contains('open')) {
            closeMenu();
            burger.focus();
        }
    });

    // Закрытие при ресайзе на большой экран
    window.addEventListener('resize', debounce(() => {
        if (window.innerWidth > 640 && mobileMenu.classList.contains('open')) {
            closeMenu();
        }
    }, 200));
}

/* ═══════════════════════════════════════
   FAQ — АККОРДЕОН
═══════════════════════════════════════ */

function initFAQ() {
    const faqItems = $$('.faq-item');
    if (!faqItems.length) return;

    faqItems.forEach(item => {
        const btn  = $('.faq-item__btn', item);
        const body = $('.faq-item__body', item);

        if (!btn || !body) return;

        btn.setAttribute('aria-expanded', 'false');

        btn.addEventListener('click', () => {
            const isOpen = item.classList.contains('open');

            // Закрываем все другие
            faqItems.forEach(other => {
                if (other !== item) {
                    other.classList.remove('open');
                    const otherBtn = $('.faq-item__btn', other);
                    if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
                }
            });

            // Переключаем текущий
            item.classList.toggle('open', !isOpen);
            btn.setAttribute('aria-expanded', (!isOpen).toString());
        });
    });
}

/* ═══════════════════════════════════════
   SCROLL REVEAL (Intersection Observer)
═══════════════════════════════════════ */

function initScrollReveal() {
    const elements = $$('.reveal');
    if (!elements.length) return;

    // Если браузер не поддерживает IntersectionObserver
    if (!('IntersectionObserver' in window)) {
        elements.forEach(el => el.classList.add('revealed'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target); // Один раз
                }
            });
        },
        {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        }
    );

    elements.forEach(el => observer.observe(el));
}

/* ═══════════════════════════════════════
   АКТИВНАЯ НАВИГАЦИЯ
═══════════════════════════════════════ */

function initActiveNav() {
    const currentPath = window.location.pathname;

    $$('.nav__link, .mobile-menu__link').forEach(link => {
        const href = link.getAttribute('href') || '';

        // Точное совпадение или начало пути
        const isActive =
            (currentPath === '/' && (href === '/' || href === '/index.php')) ||
            (currentPath !== '/' && href !== '/' && currentPath.includes(href.replace('.php', '')));

        if (isActive) {
            link.classList.add('active');
            link.setAttribute('aria-current', 'page');
        }
    });
}

/* ═══════════════════════════════════════
   ПЛАВНЫЙ СКРОЛЛ К ЯКОРЯМ
═══════════════════════════════════════ */

function initSmoothScroll() {
    $$('a[href^="#"]').forEach(link => {
        link.addEventListener('click', (e) => {
            const targetId = link.getAttribute('href');
            if (targetId === '#') return;

            const target = $(targetId);
            if (!target) return;

            e.preventDefault();

            const headerHeight = parseInt(
                getComputedStyle(document.documentElement)
                    .getPropertyValue('--header-height')
            ) || 72;

            const targetTop = target.getBoundingClientRect().top + window.scrollY - headerHeight - 16;

            window.scrollTo({ top: targetTop, behavior: 'smooth' });
        });
    });
}

/* ═══════════════════════════════════════
   HERO — АНИМАЦИЯ СЧЁТЧИКОВ
═══════════════════════════════════════ */

function initCounterAnimation() {
    const counters = $$('.hero__stat-value[data-count]');
    if (!counters.length) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;

                const el        = entry.target;
                const target    = parseInt(el.dataset.count);
                const suffix    = el.dataset.suffix || '';
                const duration  = 1500;
                const start     = performance.now();

                function update(now) {
                    const elapsed  = now - start;
                    const progress = Math.min(elapsed / duration, 1);
                    // Easing
                    const eased    = 1 - Math.pow(1 - progress, 3);
                    const current  = Math.floor(eased * target);

                    el.textContent = current.toLocaleString('ru') + suffix;

                    if (progress < 1) {
                        requestAnimationFrame(update);
                    } else {
                        el.textContent = target.toLocaleString('ru') + suffix;
                    }
                }

                requestAnimationFrame(update);
                observer.unobserve(el);
            });
        },
        { threshold: 0.5 }
    );

    counters.forEach(counter => observer.observe(counter));
}

/* ═══════════════════════════════════════
   ТЕЛЕФОННАЯ МАСКА
═══════════════════════════════════════ */

function initPhoneMask() {
    $$('input[type="tel"]').forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');

            // Убираем лишний 7/8 в начале
            if (value.startsWith('7') || value.startsWith('8')) {
                value = value.substring(1);
            }

            value = value.substring(0, 10);

            let formatted = '';
            if (value.length > 0) formatted += '(' + value.substring(0, 3);
            if (value.length >= 4) formatted += ') ' + value.substring(3, 6);
            if (value.length >= 7) formatted += '-' + value.substring(6, 8);
            if (value.length >= 9) formatted += '-' + value.substring(8, 10);

            this.value = formatted;
        });

        input.addEventListener('focus', function() {
            if (!this.value) {
                this.placeholder = '(999) 999-99-99';
            }
        });

        input.addEventListener('keydown', function(e) {
            // Разрешаем Delete, Backspace, стрелки, Tab
            if (['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)) return;
            // Разрешаем цифры
            if (!/^\d$/.test(e.key)) e.preventDefault();
        });
    });
}

/* ═══════════════════════════════════════
   LAZY LOADING ИЗОБРАЖЕНИЙ
═══════════════════════════════════════ */

function initLazyImages() {
    if (!('IntersectionObserver' in window)) return;

    const images = $$('img[data-src]');
    if (!images.length) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const img = entry.target;
                img.src = img.dataset.src;
                if (img.dataset.srcset) img.srcset = img.dataset.srcset;
                img.removeAttribute('data-src');
                img.classList.add('loaded');
                observer.unobserve(img);
            });
        },
        { rootMargin: '200px' }
    );

    images.forEach(img => observer.observe(img));
}

/* ═══════════════════════════════════════
   КОПИРОВАНИЕ В БУФЕР ОБМЕНА
═══════════════════════════════════════ */

function initCopyButtons() {
    $$('[data-copy]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const text = btn.dataset.copy;
            try {
                await navigator.clipboard.writeText(text);
                const original = btn.textContent;
                btn.textContent = 'Скопировано!';
                setTimeout(() => { btn.textContent = original; }, 2000);
            } catch {
                // Фолбэк для старых браузеров
                const ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
            }
        });
    });
}

/* ═══════════════════════════════════════
   ЭКСПОРТ УТИЛИТ (глобально для других скриптов)
═══════════════════════════════════════ */

window.HitSong = window.HitSong || {};
Object.assign(window.HitSong, {
    $,
    $$,
    createElement,
    debounce,
    throttle,
    formatTime,
    getUrlParam,
});

/* ═══════════════════════════════════════
   ИНИЦИАЛИЗАЦИЯ
═══════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {
    initHeader();
    initMobileMenu();
    initFAQ();
    initScrollReveal();
    initActiveNav();
    initSmoothScroll();
    initCounterAnimation();
    initPhoneMask();
    initLazyImages();
    initCopyButtons();
});