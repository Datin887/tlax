/**
 * Многошаговая форма заказа (Wizard)
 * Управление шагами, валидация, сохранение в localStorage, AJAX-отправка
 *
 * Путь: /public/assets/js/form-wizard.js
 */

'use strict';

(function () {

    /* ═══════════════════════════════════════
       КОНФИГУРАЦИЯ
    ═══════════════════════════════════════ */

    const TOTAL_STEPS    = 6;
    const STORAGE_KEY    = 'hitsong_order_draft';
    const SUBMIT_URL     = '/api/submit-order.php';

    /* ═══════════════════════════════════════
       СОСТОЯНИЕ
    ═══════════════════════════════════════ */

    let currentStep = 1;
    let isSubmitting = false;

    /* ═══════════════════════════════════════
       DOM
    ═══════════════════════════════════════ */

    const wizardEl    = document.getElementById('order-wizard');
    const formEl      = document.getElementById('order-form');
    const submitBtn   = document.getElementById('submit-btn');

    if (!wizardEl || !formEl) return; // Не на странице заказа

    /* ═══════════════════════════════════════
       НАВИГАЦИЯ ПО ШАГАМ
    ═══════════════════════════════════════ */

    /**
     * Перейти на указанный шаг
     * @param {number} step
     */
    function goToStep(step) {
        if (step < 1 || step > TOTAL_STEPS) return;

        const currentPanel = wizardEl.querySelector(`.wizard__panel[data-panel="${currentStep}"]`);
        const nextPanel    = wizardEl.querySelector(`.wizard__panel[data-panel="${step}"]`);

        if (!currentPanel || !nextPanel) return;

        // Скрываем текущий, показываем следующий
        currentPanel.classList.remove('active');
        nextPanel.classList.add('active');

        currentStep = step;

        updateProgress();
        scrollToWizard();
        saveDraft();
    }

    /**
     * Обновить визуальный прогресс
     */
    function updateProgress() {
        const indicators = wizardEl.querySelectorAll('.wizard__step-indicator');
        const lines      = wizardEl.querySelectorAll('.wizard__step-line');

        indicators.forEach((indicator, i) => {
            const step = i + 1;
            indicator.classList.remove('active', 'done');
            if (step === currentStep) indicator.classList.add('active');
            if (step < currentStep)   indicator.classList.add('done');
        });

        lines.forEach((line, i) => {
            line.classList.toggle('done', i + 1 < currentStep);
        });

        // Обновляем aria
        const progressEl = wizardEl.querySelector('.wizard__progress');
        if (progressEl) {
            progressEl.setAttribute('aria-valuenow', currentStep);
        }
    }

    /**
     * Плавно проскроллить к форме
     */
    function scrollToWizard() {
        const headerH = parseInt(
            getComputedStyle(document.documentElement).getPropertyValue('--header-height')
        ) || 72;

        const top = wizardEl.getBoundingClientRect().top + window.scrollY - headerH - 20;
        window.scrollTo({ top, behavior: 'smooth' });
    }

    /* ═══════════════════════════════════════
       ВАЛИДАЦИЯ
    ═══════════════════════════════════════ */

    /**
     * Правила валидации для каждого шага
     */
    const stepValidationRules = {
        1: [
            {
                field:   'occasion',
                type:    'radio',
                message: 'Выберите повод для песни',
            },
        ],
        2: [
            {
                field:   'hero_name',
                type:    'text',
                min:     2,
                message: 'Введите имя героя (минимум 2 символа)',
            },
        ],
        3: [
            {
                field:   'story',
                type:    'textarea',
                min:     50,
                message: 'Расскажите историю подробнее (минимум 50 символов)',
            },
        ],
        4: [],  // Стиль — необязателен
        5: [],  // Тариф — необязателен
        6: [
            {
                field:   'client_name',
                type:    'text',
                min:     2,
                message: 'Введите ваше имя',
            },
            {
                field:   'client_phone',
                type:    'phone',
                message: 'Введите корректный номер телефона',
            },
            {
                field:   'agree_policy',
                type:    'checkbox',
                message: 'Необходимо согласие с политикой конфиденциальности',
            },
        ],
    };

    /**
     * Валидировать конкретный шаг
     * @param {number} step
     * @returns {boolean}
     */
    function validateStep(step) {
        const rules  = stepValidationRules[step] || [];
        let   isValid = true;

        // Сначала сбрасываем все ошибки шага
        clearStepErrors(step);

        rules.forEach(rule => {
            const error = validateField(rule);
            if (error) {
                showFieldError(rule.field, error);
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Валидировать одно поле по правилу
     * @param {Object} rule
     * @returns {string|null} — сообщение об ошибке или null
     */
    function validateField(rule) {
        const { field, type, min, message } = rule;

        if (type === 'radio') {
            const checked = formEl.querySelector(`input[name="${field}"]:checked`);
            return checked ? null : message;
        }

        if (type === 'checkbox') {
            const cb = formEl.querySelector(`input[name="${field}"]`);
            return (cb && cb.checked) ? null : message;
        }

        if (type === 'phone') {
            const input = formEl.querySelector(`#client_phone`);
            if (!input) return message;
            const digits = input.value.replace(/\D/g, '');
            return digits.length === 10 ? null : message;
        }

        if (type === 'text' || type === 'textarea') {
            const input = formEl.querySelector(`#${field}`);
            if (!input) return message;
            const val = input.value.trim();
            if (!val) return message;
            if (min && val.length < min) return message;
            return null;
        }

        return null;
    }

    /**
     * Показать ошибку поля
     * @param {string} fieldName
     * @param {string} message
     */
    function showFieldError(fieldName, message) {
        const errorEl = document.getElementById(`error-${fieldName}`);
        const inputEl = formEl.querySelector(`#${fieldName}`);

        if (errorEl) {
            errorEl.textContent = message;
            errorEl.hidden = false;
        }

        if (inputEl) {
            inputEl.classList.add('error');
            inputEl.setAttribute('aria-invalid', 'true');
        }
    }

    /**
     * Убрать ошибку поля
     * @param {string} fieldId
     */
    function clearFieldError(fieldId) {
        const errorEl = document.getElementById(`error-${fieldId}`);
        const inputEl = formEl.querySelector(`#${fieldId}`);

        if (errorEl) errorEl.hidden = true;
        if (inputEl) {
            inputEl.classList.remove('error');
            inputEl.removeAttribute('aria-invalid');
        }
    }

    /**
     * Сбросить все ошибки шага
     * @param {number} step
     */
    function clearStepErrors(step) {
        const rules = stepValidationRules[step] || [];
        rules.forEach(rule => clearFieldError(rule.field));
    }

    /* ═══════════════════════════════════════
       СОХРАНЕНИЕ / ВОССТАНОВЛЕНИЕ ЧЕРНОВИКА
    ═══════════════════════════════════════ */

    /**
     * Собрать данные формы в объект
     * @returns {Object}
     */
    function collectFormData() {
        const data = {};
        const elements = formEl.elements;

        Array.from(elements).forEach(el => {
            if (!el.name || el.name === 'csrf_token' || el.name === 'website') return;

            if (el.type === 'checkbox') {
                if (el.name.endsWith('[]')) {
                    if (!data[el.name]) data[el.name] = [];
                    if (el.checked) data[el.name].push(el.value);
                } else {
                    data[el.name] = el.checked;
                }
            } else if (el.type === 'radio') {
                if (el.checked) data[el.name] = el.value;
            } else {
                data[el.name] = el.value;
            }
        });

        return data;
    }

    /**
     * Сохранить черновик в localStorage
     */
    function saveDraft() {
        try {
            const draft = {
                step: currentStep,
                data: collectFormData(),
                ts:   Date.now(),
            };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(draft));
        } catch {
            // localStorage недоступен — продолжаем без сохранения
        }
    }

    /**
     * Восстановить черновик из localStorage
     */
    function restoreDraft() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return;

            const draft = JSON.parse(raw);

            // Черновик устарел (старше 24 часов)
            if (!draft.ts || Date.now() - draft.ts > 86400000) {
                localStorage.removeItem(STORAGE_KEY);
                return;
            }

            if (!draft.data) return;

            // Восстанавливаем значения полей
            Object.entries(draft.data).forEach(([name, value]) => {
                if (name === 'csrf_token' || name === 'website') return;

                const elements = formEl.querySelectorAll(`[name="${name}"]`);

                elements.forEach(el => {
                    if (el.type === 'radio') {
                        el.checked = el.value === value;
                    } else if (el.type === 'checkbox' && name.endsWith('[]')) {
                        el.checked = Array.isArray(value) && value.includes(el.value);
                    } else if (el.type === 'checkbox') {
                        el.checked = value === true || value === 'true';
                    } else {
                        el.value = value || '';
                    }
                });
            });

            // Синхронизируем визуальные состояния
            syncRadioCardStates();
            updateCharCounters();

            // Показываем кнопку "продолжить" если есть прогресс
            if (draft.step > 1) {
                showDraftNotice(draft.step);
            }

        } catch {
            localStorage.removeItem(STORAGE_KEY);
        }
    }

    /**
     * Показать уведомление о незавершённом черновике
     * @param {number} savedStep
     */
    function showDraftNotice(savedStep) {
        const notice = document.createElement('div');
        notice.className = 'draft-notice reveal';
        notice.innerHTML = `
            <span>📝 У вас есть незавершённая заявка (шаг ${savedStep} из 6)</span>
            <button class="btn btn--sm btn--primary" id="draft-continue">Продолжить</button>
            <button class="btn btn--sm btn--ghost" id="draft-reset">Начать заново</button>
        `;

        const wizardBody = wizardEl.querySelector('.wizard__progress');
        if (wizardBody) {
            wizardEl.insertBefore(notice, wizardBody);
        }

        document.getElementById('draft-continue')?.addEventListener('click', () => {
            notice.remove();
            goToStep(savedStep);
        });

        document.getElementById('draft-reset')?.addEventListener('click', () => {
            localStorage.removeItem(STORAGE_KEY);
            notice.remove();
            formEl.reset();
            goToStep(1);
        });
    }

    /**
     * Очистить черновик
     */
    function clearDraft() {
        try {
            localStorage.removeItem(STORAGE_KEY);
        } catch {
            // Игнорируем
        }
    }

    /* ═══════════════════════════════════════
       СИНХРОНИЗАЦИЯ ВИЗУАЛЬНЫХ СОСТОЯНИЙ
    ═══════════════════════════════════════ */

    /**
     * Обновить классы radio-карточек по состоянию radio-инпутов
     */
    function syncRadioCardStates() {
        // Большие radio-карточки (повод)
        formEl.querySelectorAll('.radio-card').forEach(card => {
            const input = card.querySelector('input[type="radio"]');
            if (input) {
                card.classList.toggle('selected', input.checked);
            }
        });

        // Тарифные карточки
        formEl.querySelectorAll('.tariff-card').forEach(card => {
            const input = card.querySelector('input[type="radio"]');
            if (input) {
                card.classList.toggle('selected', input.checked);
            }
        });

        // Mood-карточки
        formEl.querySelectorAll('.mood-card').forEach(card => {
            const input = card.querySelector('input[type="radio"]');
            if (input) {
                card.classList.toggle('selected', input.checked);
            }
        });

        // Voice-карточки
        formEl.querySelectorAll('.voice-card').forEach(card => {
            const input = card.querySelector('input[type="radio"]');
            if (input) {
                card.classList.toggle('selected', input.checked);
            }
        });

        // Duration-карточки
        formEl.querySelectorAll('.duration-card').forEach(card => {
            const input = card.querySelector('input[type="radio"]');
            if (input) {
                card.classList.toggle('selected', input.checked);
            }
        });

        // Urgency-карточки
        formEl.querySelectorAll('.urgency-card').forEach(card => {
            const input = card.querySelector('input[type="radio"]');
            if (input) {
                card.classList.toggle('selected', input.checked);
            }
        });

        // Style checkboxes
        formEl.querySelectorAll('.style-check').forEach(label => {
            const input = label.querySelector('input[type="checkbox"]');
            if (input) {
                label.classList.toggle('selected', input.checked);
            }
        });
    }

    /**
     * Обновить счётчики символов в textarea
     */
    function updateCharCounters() {
        [
            { textarea: 'story',        counter: 'story-count',        max: 3000 },
            { textarea: 'must_include', counter: 'must-include-count', max: 1000 },
        ].forEach(({ textarea, counter, max }) => {
            const ta      = document.getElementById(textarea);
            const countEl = document.getElementById(counter);
            if (ta && countEl) {
                const len = ta.value.length;
                countEl.textContent = `${len} / ${max}`;
                countEl.classList.toggle('warning', len > max * 0.9);
            }
        });
    }

    /* ═══════════════════════════════════════
       ОТПРАВКА ФОРМЫ
    ═══════════════════════════════════════ */

    /**
     * Отправить заявку на сервер
     */
    async function submitForm() {
        if (isSubmitting) return;
        if (!validateStep(6)) return;

        isSubmitting = true;
        setSubmitLoading(true);

        try {
            const formData = new FormData(formEl);

            // Добавляем phone с префиксом +7
            const phone = document.getElementById('client_phone')?.value;
            if (phone) {
                formData.set('client_phone', '+7' + phone.replace(/\D/g, ''));
            }

            const response = await fetch(SUBMIT_URL, {
                method: 'POST',
                body:   formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const result = await response.json();

            if (result.success) {
                clearDraft();
                // Редирект на страницу благодарности
                window.location.href = result.redirect || `/thank-you.php?order=${result.order_number}`;
            } else {
                handleSubmitErrors(result);
            }

        } catch (err) {
            console.error('Ошибка отправки:', err);
            if (window.Notify) {
                Notify.error(
                    'Ошибка отправки',
                    'Не удалось отправить заявку. Проверьте соединение и попробуйте снова.'
                );
            }
        } finally {
            isSubmitting = false;
            setSubmitLoading(false);
        }
    }

    /**
     * Обработать ошибки от сервера
     * @param {Object} result
     */
    function handleSubmitErrors(result) {
        if (result.errors && typeof result.errors === 'object') {
            // Показываем ошибки по полям
            Object.entries(result.errors).forEach(([field, messages]) => {
                const msg = Array.isArray(messages) ? messages[0] : messages;
                showFieldError(field, msg);
            });

            // Если есть ошибки — возможно нужно вернуться на нужный шаг
            if (window.Notify) {
                Notify.error('Исправьте ошибки', 'Проверьте заполненные поля');
            }
        } else {
            if (window.Notify) {
                Notify.error('Ошибка', result.message || 'Что-то пошло не так. Попробуйте позже.');
            }
        }
    }

    /**
     * Установить состояние загрузки кнопки отправки
     * @param {boolean} loading
     */
    function setSubmitLoading(loading) {
        if (!submitBtn) return;
        submitBtn.disabled = loading;
        submitBtn.innerHTML = loading
            ? '<span class="spinner spinner--sm spinner--white"></span> Отправляем…'
            : '🚀 Отправить заявку';
    }

    /* ═══════════════════════════════════════
       ПРЕДЗАПОЛНЕНИЕ ИЗ URL
    ═══════════════════════════════════════ */

    /**
     * Применить пресеты из URL-параметров
     */
    function applyPresets() {
        const presets = window.OrderPresets || {};

        // Тариф
        if (presets.tariff) {
            const tariffInput = formEl.querySelector(`input[name="tariff"][value="${presets.tariff}"]`);
            if (tariffInput) tariffInput.checked = true;
        }

        // Повод
        if (presets.occasion) {
            const occasionInput = formEl.querySelector(`input[name="occasion"][value="${presets.occasion}"]`);
            if (occasionInput) occasionInput.checked = true;
        }

        // Стиль музыки
        if (presets.style) {
            formEl.querySelectorAll('input[name="music_styles[]"]').forEach(cb => {
                if (cb.value.toLowerCase().includes(presets.style.toLowerCase())) {
                    cb.checked = true;
                }
            });
        }

        syncRadioCardStates();
    }

    /* ═══════════════════════════════════════
       ОБРАБОТЧИКИ СОБЫТИЙ
    ═══════════════════════════════════════ */

    /**
     * Инициализация всех обработчиков событий
     */
    function initEvents() {

        // ─── Кнопки "Далее" ───
        wizardEl.querySelectorAll('.wizard-next').forEach(btn => {
            btn.addEventListener('click', () => {
                const step = parseInt(btn.dataset.step);
                if (validateStep(step)) {
                    goToStep(step + 1);
                } else {
                    // Встряхнуть кнопку при ошибке
                    btn.classList.add('shake');
                    setTimeout(() => btn.classList.remove('shake'), 600);
                    if (window.Notify) {
                        Notify.warning('Заполните поля', 'Пожалуйста, заполните обязательные поля');
                    }
                }
            });
        });

        // ─── Кнопки "Назад" ───
        wizardEl.querySelectorAll('.wizard-prev').forEach(btn => {
            btn.addEventListener('click', () => {
                const step = parseInt(btn.dataset.step);
                goToStep(step - 1);
            });
        });

        // ─── Отправка формы ───
        formEl.addEventListener('submit', (e) => {
            e.preventDefault();
            submitForm();
        });

        // ─── Radio-карточки (повод) ───
        formEl.querySelectorAll('.radio-card').forEach(card => {
            const input = card.querySelector('input[type="radio"]');
            if (!input) return;

            card.addEventListener('click', () => {
                // Снимаем выделение со всех
                formEl.querySelectorAll('.radio-card').forEach(c => c.classList.remove('selected'));
                // Выделяем текущую
                card.classList.add('selected');

                // Показываем поле "Другое"
                const otherWrap = document.getElementById('occasion-other-wrap');
                if (otherWrap) {
                    otherWrap.hidden = input.value !== 'other';
                }

                clearFieldError('occasion');
                saveDraft();
            });
        });

        // ─── Тарифные карточки ───
        formEl.querySelectorAll('.tariff-card').forEach(card => {
            card.addEventListener('click', () => {
                formEl.querySelectorAll('.tariff-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                saveDraft();
            });
        });

        // ─── Mood карточки ───
        formEl.querySelectorAll('.mood-card').forEach(card => {
            const input = card.querySelector('input');
            if (!input) return;
            card.addEventListener('click', () => {
                formEl.querySelectorAll('.mood-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                saveDraft();
            });
        });

        // ─── Voice карточки ───
        formEl.querySelectorAll('.voice-card').forEach(card => {
            const input = card.querySelector('input');
            if (!input) return;
            card.addEventListener('click', () => {
                formEl.querySelectorAll('.voice-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                saveDraft();
            });
        });

        // ─── Duration карточки ───
        formEl.querySelectorAll('.duration-card').forEach(card => {
            card.addEventListener('click', () => {
                formEl.querySelectorAll('.duration-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                saveDraft();
            });
        });

        // ─── Urgency карточки ───
        formEl.querySelectorAll('.urgency-card').forEach(card => {
            card.addEventListener('click', () => {
                formEl.querySelectorAll('.urgency-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                saveDraft();
            });
        });

        // ─── Style checkboxes ───
        formEl.querySelectorAll('.style-check').forEach(label => {
            const input = label.querySelector('input');
            if (!input) return;
            label.addEventListener('click', () => {
                // Отложенный toggle после смены checked
                setTimeout(() => {
                    label.classList.toggle('selected', input.checked);
                    saveDraft();
                }, 0);
            });
        });

        // ─── Счётчики символов ───
        ['story', 'must_include'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', () => {
                    updateCharCounters();
                    clearFieldError(id);
                    saveDraft();
                });
            }
        });

        // ─── Автосохранение при изменении инпутов ───
        formEl.querySelectorAll('input[type="text"], input[type="tel"], input[type="email"], input[type="url"], input[type="date"], input[type="number"]').forEach(input => {
            input.addEventListener('input', () => {
                clearFieldError(input.id || input.name);
                saveDraft();
            });
        });

        // ─── Клавиатурная навигация по шагам ───
        document.addEventListener('keydown', (e) => {
            // Alt+→ = следующий шаг, Alt+← = предыдущий
            if (e.altKey && e.key === 'ArrowRight' && currentStep < TOTAL_STEPS) {
                const nextBtn = wizardEl.querySelector(`.wizard__panel[data-panel="${currentStep}"] .wizard-next`);
                nextBtn?.click();
            }
            if (e.altKey && e.key === 'ArrowLeft' && currentStep > 1) {
                goToStep(currentStep - 1);
            }
        });
    }

    /* ═══════════════════════════════════════
       ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
    ═══════════════════════════════════════ */

    /**
     * Transliterate (для генерации id из русских строк)
     * Используется в PHP-функции — в JS дублируем для согласованности
     */
    function transliterate(str) {
        const map = {
            'а':'a','б':'b','в':'v','г':'g','д':'d','е':'e','ё':'yo',
            'ж':'zh','з':'z','и':'i','й':'j','к':'k','л':'l','м':'m',
            'н':'n','о':'o','п':'p','р':'r','с':'s','т':'t','у':'u',
            'ф':'f','х':'h','ц':'ts','ч':'ch','ш':'sh','щ':'sch',
            'ъ':'','ы':'y','ь':'','э':'e','ю':'yu','я':'ya',
            ' ':'-','(':'-',')':'-','/':'-'
        };
        return str.toLowerCase()
            .split('')
            .map(c => map[c] !== undefined ? map[c] : c)
            .join('')
            .replace(/[^a-z0-9-]/g, '')
            .replace(/-+/g, '-');
    }

    /* ═══════════════════════════════════════
       ИНИЦИАЛИЗАЦИЯ
    ═══════════════════════════════════════ */

    function init() {
        restoreDraft();
        applyPresets();
        initEvents();
        updateProgress();
        updateCharCounters();

        // Устанавливаем время начала заполнения
        const startTimeInput = document.getElementById('form_start_time');
        if (startTimeInput && !startTimeInput.value) {
            startTimeInput.value = Date.now();
        }
    }

    document.addEventListener('DOMContentLoaded', init);

})();