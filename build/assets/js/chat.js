/**
 * Чат «Музыкальный продюсер» — логика (order.php)
 * - Голосовой ввод: нативный Web Speech API (ru-RU)
 * - Отправка: POST /api/chat.php (JSON { message }), фолбэк-заглушка в JS
 * Vanilla JS, без зависимостей.
 */
(function () {
    'use strict';

    /* ─── Конфиг (подставляется из order.php) ─── */
    var cfg = window.OrderChatConfig || {};
    var API_URL = cfg.apiUrl || '/api/chat.php';
    var FALLBACK_REPLY = cfg.fallbackReply || 'Отлично! Расскажите подробнее о главном герое праздника.';
    var MIN_TYPING_MS = 700;   /* минимум «печатания» для натуральности */
    var FALLBACK_DELAY_MS = 1000;
    var LEAD_REDIRECT_MS = 2600; /* пауза на прочтение финального ответа → страница «Спасибо» */

    /* ─── DOM ─── */
    var messagesEl, inputEl, sendBtn, micBtn, micHint, typingEl, quickWrap;

    /* ─── Состояние ─── */
    var speechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    var recognizer = null;
    var isRecording = false;
    var isWaitingReply = false;

    /* ─── Утилиты ─── */
    function el(tag, className, text) {
        var node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    }

    function scrollDown() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function showHint(text) {
        micHint.textContent = text || '';
    }

    /* ─── Бабблы ─── */
    function addBotMessage(text) {
        messagesEl.appendChild(el('div', 'chat__msg-bot', text));
        scrollDown();
    }

    function addUserMessage(text) {
        var wrap = el('div', 'chat__msg-user');
        var p = el('p', '', text);
        wrap.appendChild(p);
        messagesEl.appendChild(wrap);
        scrollDown();
    }

    function hideQuickReplies() {
        if (quickWrap) {
            quickWrap.style.display = 'none';
        }
    }

    /* ─── Индикатор «Продюсер печатает…» ─── */
    function showTyping() {
        typingEl = el('div', 'chat__typing');
        var label = el('span', '', 'Продюсер печатает');
        var dots = el('span', 'chat__typing-dots');
        dots.appendChild(el('span'));
        dots.appendChild(el('span'));
        dots.appendChild(el('span'));
        typingEl.appendChild(label);
        typingEl.appendChild(dots);
        messagesEl.appendChild(typingEl);
        scrollDown();
    }

    function hideTyping() {
        if (typingEl && typingEl.parentNode) {
            typingEl.parentNode.removeChild(typingEl);
        }
        typingEl = null;
    }

    /* ─── Голосовой ввод (Web Speech API) ─── */
    function voiceSupported() {
        return !!speechRecognition;
    }

    function micReset() {
        isRecording = false;
        micBtn.classList.remove('is-recording');
        micBtn.setAttribute('aria-pressed', 'false');
        showHint(voiceSupported() ? '' : 'Голосовой ввод недоступен в этом браузере — используйте Chrome, Edge или Safari.');
        if (recognizer) {
            try { recognizer.stop(); } catch (e) { /* noop */ }
            recognizer = null;
        }
    }

    function micToggle() {
        if (!voiceSupported()) {
            return;
        }
        if (isRecording) {
            if (recognizer) { recognizer.stop(); }
            buzz(60); /* отклик: стоп */
            return;
        }
        buzz([25, 40, 25]); /* отклик: старт записи */
        startListening();
    }

    function startListening() {
        var rec;
        try {
            rec = new speechRecognition();
        } catch (e) {
            showHint('Не удалось запустить распознавание речи.');
            return;
        }

        recognizer = rec;
        rec.lang = 'ru-RU';
        rec.interimResults = true;
        rec.continuous = false;

        isRecording = true;
        micBtn.classList.add('is-recording');
        micBtn.setAttribute('aria-pressed', 'true');
        showHint('🎤 Говорите… Нажмите ещё раз, чтобы остановить');

        var finalText = '';

        rec.onresult = function (event) {
            var interim = '';
            var final = '';
            var i, result, text;
            for (i = 0; i < event.results.length; i++) {
                result = event.results[i];
                text = result[0] && result[0].transcript ? result[0].transcript : '';
                if (result.final) {
                    final += text;
                } else {
                    interim += text;
                }
            }
            if (final) {
                finalText += (finalText && !endsWithSpace(finalText) ? ' ' : '') + final;
                setInputValue(finalText + interim);
            } else if (interim) {
                setInputValue(finalText + (finalText && !endsWithSpace(finalText) ? ' ' : '') + interim);
            }
        };

        rec.onend = function () {
            micReset();
            inputEl.focus();
        };

        rec.onerror = function (event) {
            showHint('Ошибка распознавания: ' + (event && event.error ? event.error : 'unknown'));
            micReset();
        };

        try {
            rec.start();
        } catch (e) {
            micReset();
            showHint('Не удалось запустить микрофон. Проверьте разрешение в браузере.');
        }
    }

    function endsWithSpace(str) {
        return /\s$/.test(str);
    }

    function setInputValue(value) {
        inputEl.value = value;
        autoGrowInput();
    }

    /* ─── Поле ввода ─── */
    function autoGrowInput() {
        inputEl.style.height = 'auto';
        inputEl.style.height = Math.min(120, Math.max(44, inputEl.scrollHeight)) + 'px';
    }

    /* ─── Отправка сообщения ─── */
    function sendMessage(rawText) {
        var text = (rawText || '').trim();
        if (text === '' || isWaitingReply) {
            return;
        }

        /* Если идёт запись — останавливаем */
        if (isRecording && recognizer) {
            recognizer.stop();
        }

        addUserMessage(text);
        inputEl.value = '';
        autoGrowInput();
        hideQuickReplies();
        setWaiting(true);

        /* ─── Запрос к бэкенду + фолбэк ─── */
        var started = Date.now();

        fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: text })
        })
            .then(function (res) {
                if (!res.ok) { throw new Error('HTTP ' + res.status); }
                return res.json();
            })
            .then(function (data) {
                if (!data || data.success !== true || !data.reply) {
                    throw new Error('bad payload');
                }
                var delay = Math.max(0, MIN_TYPING_MS - (Date.now() - started));
                replyAfter(delay, data.reply);

                /* Лид принят — показываем финальный ответ и уводим на «Спасибо» */
                if (data.redirect) {
                    setTimeout(function () {
                        window.location.href = data.redirect;
                    }, delay + LEAD_REDIRECT_MS);
                }
            })
            .catch(function () {
                /* Бэкенд пока недоступен — JS-заглушка через 1 секунду */
                setTimeout(function () {
                    setWaiting(false);
                    addBotMessage(FALLBACK_REPLY);
                }, FALLBACK_DELAY_MS);
            });
    }

    function replyAfter(delayMs, text) {
        setTimeout(function () {
            setWaiting(false);
            addBotMessage(text);
        }, delayMs);
    }

    function setWaiting(value) {
        isWaitingReply = value;
        sendBtn.disabled = value;
        sendBtn.setAttribute('aria-disabled', value ? 'true' : 'false');
        if (value) {
            showTyping();
        } else {
            hideTyping();
        }
    }

    /* ─── Пресет из URL (?occasion=wedding и т.п.) — автоотправка ─── */
    function applyPreset() {
        var occasion = (cfg.presetOccasion || '').toString();
        var labels = {
            wedding: 'Свадьба',
            birthday: 'День рождения',
            anniversary: 'Юбилей',
            corporate: 'Корпоратив',
            love: 'Годовщина',
            march8: '8 Марта',
            feb23: '23 Февраля',
            newyear: 'Новый год',
            proposal: 'Предложение',
            birth: 'Рождение ребёнка',
            retirement: 'Выход на пенсию',
            other: 'Другой повод'
        };
        if (labels[occasion]) {
            setTimeout(function () {
                sendMessage(labels[occasion]);
            }, 800);
        }
    }

    /* ─── Мобильная клавиатура: инпут не должен уезжать за экран ─── */
    function keepInputVisible() {
        if (!inputEl) return;
        var vh = window.visualViewport ? window.visualViewport.height : window.innerHeight;
        var rect = inputEl.getBoundingClientRect();
        if (rect.bottom > vh - 4 || rect.top < 0) {
            inputEl.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
    }

    /* ─── Индикация: вибрация (где поддерживается) при записи ─── */
    function buzz(pattern) {
        if (navigator.vibrate) {
            try { navigator.vibrate(pattern || 40); } catch (err) { /* нет поддержки */ }
        }
    }

    /* ─── Инициализация ─── */
    function init() {
        messagesEl = document.getElementById('chat-messages');
        inputEl = document.getElementById('chat-input');
        sendBtn = document.getElementById('chat-send');
        micBtn = document.getElementById('chat-mic');
        micHint = document.getElementById('chat-mic-hint');
        quickWrap = document.getElementById('chat-quick-replies');

        if (!messagesEl || !inputEl || !sendBtn || !micBtn) {
            return;
        }

        /* Фокус на поле — поднимаем его над экранной клавиатурой */
        inputEl.addEventListener('focus', function () {
            setTimeout(keepInputVisible, 60);
        });
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', keepInputVisible);
            window.visualViewport.addEventListener('scroll', keepInputVisible);
        }

        /* Отправка по кнопке */
        sendBtn.addEventListener('click', function () {
            sendMessage(inputEl.value);
        });

        /* Enter — отправить, Shift+Enter — новая строка */
        inputEl.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage(inputEl.value);
            }
        });

        /* Авто-рост поля */
        inputEl.addEventListener('input', autoGrowInput);

        /* Быстрые кнопки: отправляем чистый текст из data-send (без эмодзи) */
        var quicks = quickWrap ? Array.prototype.slice.call(quickWrap.querySelectorAll('.chat__quick')) : [];
        quicks.forEach(function (btn) {
            btn.addEventListener('click', function () {
                sendMessage(btn.dataset.send || btn.textContent);
            });
        });

        /* Микрофон */
        if (!voiceSupported()) {
            micBtn.disabled = true;
            showHint('Голосовой ввод недоступен в этом браузере — используйте Chrome, Edge или Safari.');
        } else {
            micBtn.addEventListener('click', micToggle);
        }

        /* Пресет из URL */
        applyPreset();

        scrollDown();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();