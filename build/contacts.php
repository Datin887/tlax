<?php
/**
 * Страница контактов
 * Способы связи, часы работы, быстрая форма
 *
 * Путь: /public/contacts.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';

$csrf_token = generate_csrf_token();

$page_meta = [
    'title'       => 'Контакты — Хитовая Песня | Напишите нам',
    'description' => 'Свяжитесь со студией Хитовая Песня: телефон, Telegram, WhatsApp, ВКонтакте, Одноклассники. Работаем ежедневно с 9:00 до 22:00 МСК.',
    'keywords'    => 'контакты студии песен, написать хитовая песня, телефон заказ песни',
    'canonical'   => SITE_URL . '/contacts.php',
];

$contacts = [
    [
        'type'    => 'phone',
        'icon'    => '📱',
        'label'   => 'Телефон',
        'value'   => CONTACT_PHONE,
        'href'    => 'tel:' . preg_replace('/\D/', '', CONTACT_PHONE),
        'action'  => 'Позвонить',
        'class'   => 'contact-card--phone',
    ],
    [
        'type'    => 'sms',
        'icon'    => '💬',
        'label'   => 'SMS',
        'value'   => CONTACT_PHONE,
        'href'    => 'sms:' . preg_replace('/\D/', '', CONTACT_PHONE),
        'action'  => 'Написать',
        'class'   => 'contact-card--sms',
    ],
    [
        'type'    => 'telegram',
        'icon'    => '✈️',
        'label'   => 'Telegram',
        'value'   => TELEGRAM_USERNAME,
        'href'    => 'https://t.me/' . ltrim(TELEGRAM_USERNAME, '@'),
        'action'  => 'Открыть',
        'class'   => 'contact-card--telegram',
        'target'  => '_blank',
    ],
    [
        'type'    => 'whatsapp',
        'icon'    => '💚',
        'label'   => 'WhatsApp',
        'value'   => WHATSAPP_NUMBER,
        'href'    => 'https://wa.me/' . preg_replace('/\D/', '', WHATSAPP_NUMBER),
        'action'  => 'Открыть',
        'class'   => 'contact-card--whatsapp',
        'target'  => '_blank',
    ],
    [
        'type'    => 'vk',
        'icon'    => '🔵',
        'label'   => 'ВКонтакте',
        'value'   => 'vk.com/' . VK_PAGE,
        'href'    => 'https://vk.com/' . VK_PAGE,
        'action'  => 'Перейти',
        'class'   => 'contact-card--vk',
        'target'  => '_blank',
    ],
    [
        'type'    => 'ok',
        'icon'    => '🟠',
        'label'   => 'Одноклассники',
        'value'   => 'ok.ru/' . OK_PAGE,
        'href'    => 'https://ok.ru/' . OK_PAGE,
        'action'  => 'Перейти',
        'class'   => 'contact-card--ok',
        'target'  => '_blank',
    ],
    [
        'type'    => 'email',
        'icon'    => '✉️',
        'label'   => 'Email',
        'value'   => ADMIN_EMAIL,
        'href'    => 'mailto:' . ADMIN_EMAIL,
        'action'  => 'Написать',
        'class'   => 'contact-card--email',
    ],
];

require_once __DIR__ . '/../includes/head-meta.php';
require_once __DIR__ . '/../includes/header.php';
?>

<main>

    <!-- ЗАГОЛОВОК -->
    <section class="page-hero section--primary section--sm">
        <div class="container">
            <div class="page-hero__content reveal">
                <nav class="breadcrumb" aria-label="Хлебные крошки">
                    <span class="breadcrumb__item">
                        <a href="/" class="breadcrumb__link">Главная</a>
                        <span class="breadcrumb__sep" aria-hidden="true">›</span>
                    </span>
                    <span class="breadcrumb__item">
                        <span aria-current="page">Контакты</span>
                    </span>
                </nav>
                <h1 class="section-title" style="color:#fff;">Контакты</h1>
                <p class="section-subtitle">
                    Выберите удобный способ связи.
                    Отвечаем в течение часа с 9:00 до 22:00 МСК.
                </p>
            </div>
        </div>
    </section>


    <!-- СПОСОБЫ СВЯЗИ -->
    <section class="section section--white" id="contacts">
        <div class="container">

            <div class="section-header reveal">
                <h2 class="section-title">Как с нами связаться</h2>
            </div>

            <div class="contacts-grid reveal">
                <?php foreach ($contacts as $contact): ?>
                    <a
                        href="<?= h($contact['href']) ?>"
                        class="contact-card <?= h($contact['class']) ?>"
                        <?= isset($contact['target']) ? 'target="' . h($contact['target']) . '" rel="noopener noreferrer"' : '' ?>
                        aria-label="<?= h($contact['label']) ?>: <?= h($contact['value']) ?>"
                    >
                        <div class="contact-card__icon" aria-hidden="true">
                            <?= $contact['icon'] ?>
                        </div>
                        <div class="contact-card__info">
                            <span class="contact-card__type"><?= h($contact['label']) ?></span>
                            <span class="contact-card__value"><?= h($contact['value']) ?></span>
                        </div>
                        <span class="contact-card__action"><?= h($contact['action']) ?> →</span>
                    </a>
                <?php endforeach; ?>
            </div>

        </div>
    </section>


    <!-- ЧАСЫ РАБОТЫ -->
    <section class="section" id="hours">
        <div class="container">
            <div class="hours-block reveal">

                <div class="hours-block__icon" aria-hidden="true">🕐</div>

                <div class="hours-block__content">
                    <h2 class="hours-block__title">Часы работы</h2>
                    <div class="hours-grid">
                        <div class="hours-row">
                            <span class="hours-row__days">Понедельник — Воскресенье</span>
                            <span class="hours-row__time">9:00 — 22:00 МСК</span>
                        </div>
                    </div>
                    <p class="hours-block__note">
                        Отвечаем в течение часа. Срочные заявки обрабатываем приоритетно.
                    </p>
                </div>

                <div class="hours-block__now" id="hours-now" aria-live="polite">
                    <!-- Заполняется JS -->
                </div>

            </div>
        </div>
    </section>


    <!-- БЫСТРАЯ ФОРМА -->
    <section class="section section--light" id="contact-form">
        <div class="container">

            <div class="contact-form-wrap">

                <div class="contact-form-info reveal">
                    <h2 class="section-title section-header--left">Напишите нам</h2>
                    <p class="section-subtitle section-subtitle--left">
                        Оставьте сообщение — ответим в течение часа
                    </p>

                    <ul class="contact-form-info__list">
                        <li>🎵 Ответим на любые вопросы о создании песни</li>
                        <li>💰 Подберём подходящий тариф</li>
                        <li>⚡ Уточним сроки для срочных заказов</li>
                        <li>🏢 Обсудим корпоративный проект</li>
                    </ul>
                </div>

                <form
                    id="contact-form"
                    class="contact-form reveal"
                    novalidate
                    data-csrf="<?= h($csrf_token) ?>"
                >
                    <div class="form-group">
                        <label class="form-label" for="contact-name">
                            Ваше имя <span class="required" aria-hidden="true">*</span>
                        </label>
                        <input
                            type="text"
                            id="contact-name"
                            name="name"
                            class="form-input"
                            placeholder="Как вас зовут?"
                            maxlength="100"
                            required
                            autocomplete="given-name"
                        >
                        <div class="form-error" id="error-contact-name" hidden></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact-contact">
                            Телефон или Telegram <span class="required" aria-hidden="true">*</span>
                        </label>
                        <input
                            type="text"
                            id="contact-contact"
                            name="contact"
                            class="form-input"
                            placeholder="+7 (999) 999-99-99 или @username"
                            maxlength="100"
                            required
                            autocomplete="tel"
                        >
                        <div class="form-error" id="error-contact-contact" hidden></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact-message">
                            Сообщение <span class="required" aria-hidden="true">*</span>
                        </label>
                        <textarea
                            id="contact-message"
                            name="message"
                            class="form-textarea"
                            rows="5"
                            placeholder="Задайте вопрос или расскажите о вашем проекте…"
                            minlength="10"
                            maxlength="2000"
                            required
                        ></textarea>
                        <div class="form-char-count" id="contact-message-count">0 / 2000</div>
                        <div class="form-error" id="error-contact-message" hidden></div>
                    </div>

                    <!-- Honeypot -->
                    <div style="position:absolute;left:-9999px;opacity:0;pointer-events:none;" aria-hidden="true">
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">
                    <input type="hidden" name="form_start_time" id="contact-start-time" value="">

                    <button type="submit" class="btn btn--primary btn--lg btn--full" id="contact-submit">
                        Отправить сообщение
                    </button>

                    <div id="contact-success" class="contact-success" hidden>
                        <span aria-hidden="true">✅</span>
                        <div>
                            <strong>Сообщение отправлено!</strong>
                            <p>Ответим в течение часа.</p>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </section>

</main>

<script>
document.getElementById('contact-start-time').value = Date.now();

/* ─── Индикатор «сейчас работаем» ─── */
(function() {
    const el = document.getElementById('hours-now');
    if (!el) return;
    const now   = new Date();
    const hours = now.getHours();
    const isOpen = hours >= 9 && hours < 22;
    el.innerHTML = isOpen
        ? '<span class="hours-status hours-status--open">🟢 Сейчас работаем</span>'
        : '<span class="hours-status hours-status--closed">🔴 Сейчас не работаем</span>';
})();

/* ─── Форма контактов ─── */
(function() {
    const form       = document.getElementById('contact-form');
    const submitBtn  = document.getElementById('contact-submit');
    const successEl  = document.getElementById('contact-success');
    const msgCounter = document.getElementById('contact-message-count');
    const msgArea    = document.getElementById('contact-message');

    if (!form) return;

    // Счётчик символов
    msgArea?.addEventListener('input', () => {
        const len = msgArea.value.length;
        if (msgCounter) msgCounter.textContent = len + ' / 2000';
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Сброс ошибок
        form.querySelectorAll('.form-error').forEach(el => el.hidden = true);
        form.querySelectorAll('.form-input, .form-textarea').forEach(el => {
            el.classList.remove('error');
        });

        // Клиентская валидация
        let valid = true;
        const name    = form.querySelector('[name="name"]');
        const contact = form.querySelector('[name="contact"]');
        const message = form.querySelector('[name="message"]');

        if (!name.value.trim() || name.value.trim().length < 2) {
            showErr('error-contact-name', 'Введите ваше имя'); name.classList.add('error'); valid = false;
        }
        if (!contact.value.trim() || contact.value.trim().length < 5) {
            showErr('error-contact-contact', 'Введите телефон или Telegram'); contact.classList.add('error'); valid = false;
        }
        if (!message.value.trim() || message.value.trim().length < 10) {
            showErr('error-contact-message', 'Напишите сообщение (минимум 10 символов)'); message.classList.add('error'); valid = false;
        }

        if (!valid) return;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner spinner--sm spinner--white"></span> Отправляем…';

        try {
            const fd = new FormData(form);
            const res = await fetch('/api/contact.php', {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json();

            if (data.success) {
                form.reset();
                submitBtn.hidden = true;
                if (successEl) successEl.hidden = false;
                if (window.Notify) Notify.success('Отправлено!', 'Ответим в течение часа');
            } else {
                if (window.Notify) Notify.error('Ошибка', data.message || 'Попробуйте ещё раз');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Отправить сообщение';
            }
        } catch {
            if (window.Notify) Notify.error('Ошибка сети', 'Проверьте подключение');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Отправить сообщение';
        }
    });

    function showErr(id, msg) {
        const el = document.getElementById(id);
        if (el) { el.textContent = msg; el.hidden = false; }
    }
})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>