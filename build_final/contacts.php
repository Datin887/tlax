<?php
/**
 * Страница контактов
 * Способы связи, часы работы, быстрая форма
 *
 * Путь: /public/contacts.php
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';

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
        'icon'    => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>',
        'label'   => 'Телефон',
        'value'   => CONTACT_PHONE,
        'href'    => 'tel:' . preg_replace('/\D/', '', CONTACT_PHONE),
        'action'  => 'Позвонить',
        'class'   => 'contact-card--phone',
    ],
    [
        'type'    => 'sms',
        'icon'    => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM9 11H7V9h2v2zm4 0h-2V9h2v2zm4 0h-2V9h2v2z"/></svg>',
        'label'   => 'SMS',
        'value'   => CONTACT_PHONE,
        'href'    => 'sms:' . preg_replace('/\D/', '', CONTACT_PHONE),
        'action'  => 'Написать',
        'class'   => 'contact-card--sms',
    ],
    [
        'type'    => 'telegram',
        'icon'    => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>',
        'label'   => 'Telegram',
        'value'   => TELEGRAM_USERNAME,
        'href'    => 'https://t.me/' . ltrim(TELEGRAM_USERNAME, '@'),
        'action'  => 'Открыть',
        'class'   => 'contact-card--telegram',
        'target'  => '_blank',
    ],
    [
        'type'    => 'whatsapp',
        'icon'    => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>',
        'label'   => 'WhatsApp',
        'value'   => WHATSAPP_NUMBER,
        'href'    => 'https://wa.me/' . preg_replace('/\D/', '', WHATSAPP_NUMBER),
        'action'  => 'Открыть',
        'class'   => 'contact-card--whatsapp',
        'target'  => '_blank',
    ],
    [
        'type'    => 'vk',
        'icon'    => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M15.684 0H8.316C1.592 0 0 1.592 0 8.316v7.368C0 22.408 1.592 24 8.316 24h7.368C22.408 24 24 22.408 24 15.684V8.316C24 1.592 22.391 0 15.684 0zm3.692 17.123h-1.744c-.66 0-.862-.525-2.049-1.714-1.033-1.01-1.49-1.135-1.744-1.135-.356 0-.458.102-.458.593v1.575c0 .424-.135.678-1.253.678-1.846 0-3.896-1.118-5.335-3.202C4.624 10.857 4.03 8.57 4.03 8.096c0-.254.102-.491.593-.491h1.744c.44 0 .61.203.78.678.863 2.49 2.303 4.675 2.896 4.675.22 0 .322-.102.322-.66V9.721c-.068-1.186-.695-1.287-.695-1.71 0-.203.17-.407.44-.407h2.744c.373 0 .508.203.508.643v3.473c0 .372.17.508.271.508.22 0 .407-.136.813-.542 1.254-1.406 2.152-3.574 2.152-3.574.119-.254.322-.491.763-.491h1.744c.525 0 .644.27.525.643-.22 1.017-2.354 4.031-2.354 4.031-.186.305-.254.44 0 .78.186.254.796.779 1.203 1.253.745.847 1.32 1.558 1.473 2.049.17.49-.085.744-.576.744z"/></svg>',
        'label'   => 'ВКонтакте',
        'value'   => 'vk.com/' . VK_PAGE,
        'href'    => 'https://vk.com/' . VK_PAGE,
        'action'  => 'Перейти',
        'class'   => 'contact-card--vk',
        'target'  => '_blank',
    ],
    [
        'type'    => 'ok',
        'icon'    => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 3.6c2.28 0 4.2 1.92 4.2 4.2S14.28 12 12 12s-4.2-1.92-4.2-4.2S9.72 3.6 12 3.6zm0 14.4c-3.06 0-5.76-1.56-7.38-3.9.06-2.52 4.92-3.9 7.38-3.9s7.32 1.38 7.38 3.9c-1.62 2.34-4.32 3.9-7.38 3.9z"/></svg>',
        'label'   => 'Одноклассники',
        'value'   => 'ok.ru/' . OK_PAGE,
        'href'    => 'https://ok.ru/' . OK_PAGE,
        'action'  => 'Перейти',
        'class'   => 'contact-card--ok',
        'target'  => '_blank',
    ],
    [
        'type'    => 'email',
        'icon'    => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>',
        'label'   => 'Email',
        'value'   => ADMIN_EMAIL,
        'href'    => 'mailto:' . ADMIN_EMAIL,
        'action'  => 'Написать',
        'class'   => 'contact-card--email',
    ],
];

require_once __DIR__ . '/includes/head-meta.php';
require_once __DIR__ . '/includes/header.php';
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

                <div class="hours-block__icon" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg></div>

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

<?php require_once __DIR__ . '/includes/footer.php'; ?>