# ЭТАП 6: Контакты + ЭТАП 7: Админка

---

## ЭТАП 6 — Файл 1: `public/contacts.php`

```php
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
```

---

## ЭТАП 6 — Файл 2: `public/api/contact.php`

```php
<?php
/**
 * API: Обработка формы быстрой связи
 * Метод: POST
 *
 * Путь: /public/api/contact.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../../includes/mail.php';
require_once __DIR__ . '/../../includes/telegram.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['success' => false, 'message' => 'Method not allowed'], 405);
}
if (!is_ajax_request()) {
    send_json(['success' => false, 'message' => 'Forbidden'], 403);
}

// Rate limiting: 3 сообщения в час с одного IP
$client_ip = get_client_ip();
if (!check_rate_limit('contact_' . $client_ip, 3, 3600)) {
    send_json(['success' => false, 'message' => 'Слишком много запросов. Подождите немного.'], 429);
}

// Honeypot
if (!empty($_POST['website'])) {
    send_json(['success' => true]);
}

// Защита от быстрой отправки
$start = (int)($_POST['form_start_time'] ?? 0);
if ($start > 0 && ((int)(microtime(true) * 1000) - $start) < 5000) {
    log_error("contact: возможный бот, IP={$client_ip}");
}

// CSRF
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    send_json(['success' => false, 'message' => 'Ошибка безопасности. Обновите страницу.'], 403);
}

// Санитизация
$name    = sanitize_string($_POST['name']    ?? '', 100);
$contact = sanitize_string($_POST['contact'] ?? '', 100);
$message = sanitize_text($_POST['message']   ?? '', 2000);

// Валидация
$errors = [];
if (mb_strlen($name, 'UTF-8') < 2)    $errors[] = 'Введите имя';
if (mb_strlen($contact, 'UTF-8') < 5) $errors[] = 'Введите телефон или Telegram';
if (mb_strlen($message, 'UTF-8') < 10)$errors[] = 'Напишите сообщение';

if (!empty($errors)) {
    send_json(['success' => false, 'message' => implode('. ', $errors)], 422);
}

// Сохранение в БД
try {
    $db = Database::getInstance();
    $db->execute(
        "INSERT INTO contact_messages (name, contact, message, ip_address, created_at)
         VALUES (:name, :contact, :message, :ip, NOW())",
        [
            ':name'    => $name,
            ':contact' => $contact,
            ':message' => $message,
            ':ip'      => $client_ip,
        ]
    );
} catch (Exception $e) {
    log_error('contact: DB error: ' . $e->getMessage());
}

// Email
try {
    $subject = "Новое сообщение от {$name} | Хитовая Песня";
    $html = "<h2>Сообщение с сайта</h2>
             <p><b>Имя:</b> " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "</p>
             <p><b>Контакт:</b> " . htmlspecialchars($contact, ENT_QUOTES, 'UTF-8') . "</p>
             <p><b>Сообщение:</b><br>" . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . "</p>";
    send_html_email(ADMIN_EMAIL, $subject, $html);
} catch (Exception $e) {
    log_error('contact: email failed: ' . $e->getMessage());
}

// Telegram
try {
    $tg_text = "📨 *Новое сообщение с сайта*\n\n"
        . "*Имя:* " . escape_tg($name) . "\n"
        . "*Контакт:* " . escape_tg($contact) . "\n"
        . "*Сообщение:*\n" . escape_tg(mb_substr($message, 0, 500, 'UTF-8'));
    send_telegram_message($tg_text, ['parse_mode' => 'MarkdownV2']);
} catch (Exception $e) {
    log_error('contact: telegram failed: ' . $e->getMessage());
}

send_json(['success' => true, 'message' => 'Сообщение отправлено']);
```

---

## Дополнения к `main.css` для contacts.php

```css
/* ═══════════════════════════════════════
   КОНТАКТЫ — ДОПОЛНИТЕЛЬНЫЕ СТИЛИ
═══════════════════════════════════════ */

/* Блок часов работы */
.hours-block {
    display: flex;
    align-items: center;
    gap: var(--space-xl);
    background: var(--color-bg-white);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-xl);
    padding: var(--space-xl);
    box-shadow: var(--shadow-sm);
    flex-wrap: wrap;
}

.hours-block__icon {
    font-size: 56px;
    flex-shrink: 0;
}

.hours-block__content {
    flex: 1;
    min-width: 200px;
}

.hours-block__title {
    font-family: var(--font-heading);
    font-size: var(--font-size-h3);
    font-weight: var(--font-weight-bold);
    color: var(--color-text);
    margin-bottom: var(--space-sm);
}

.hours-block__title::after { display: none; }

.hours-grid {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: var(--space-sm);
}

.hours-row {
    display: flex;
    align-items: center;
    gap: var(--space-md);
    flex-wrap: wrap;
}

.hours-row__days {
    font-weight: var(--font-weight-semibold);
    color: var(--color-text);
    font-size: var(--font-size-base);
}

.hours-row__time {
    font-family: var(--font-heading);
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-bold);
    color: var(--color-primary);
}

.hours-block__note {
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
}

.hours-block__now {
    flex-shrink: 0;
}

.hours-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: var(--radius-full);
    font-weight: var(--font-weight-bold);
    font-size: var(--font-size-sm);
}

.hours-status--open {
    background: var(--color-success-light);
    color: var(--color-success);
}

.hours-status--closed {
    background: var(--color-error-light);
    color: var(--color-error);
}

/* Форма контактов */
.contact-form-wrap {
    display: grid;
    grid-template-columns: 1fr 1.3fr;
    gap: var(--space-2xl);
    align-items: start;
    max-width: 960px;
    margin-inline: auto;
}

.contact-form-info__list {
    display: flex;
    flex-direction: column;
    gap: var(--space-sm);
    margin-top: var(--space-lg);
    font-size: var(--font-size-base);
    color: var(--color-text-muted);
}

.contact-form-info__list li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    line-height: 1.5;
}

.contact-form {
    background: var(--color-bg-white);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-xl);
    padding: var(--space-xl);
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
    gap: var(--space-md);
}

.contact-success {
    display: flex;
    align-items: flex-start;
    gap: var(--space-sm);
    padding: var(--space-md);
    background: var(--color-success-light);
    border: 1px solid var(--color-success);
    border-radius: var(--radius-lg);
    color: var(--color-success);
    font-size: var(--font-size-base);
}

.contact-success p {
    margin-top: 4px;
    color: var(--color-text-muted);
    font-size: var(--font-size-sm);
}

@media (max-width: 768px) {
    .hours-block {
        flex-direction: column;
        text-align: center;
    }
    .hours-row {
        flex-direction: column;
        gap: 4px;
        align-items: center;
    }
    .contact-form-wrap {
        grid-template-columns: 1fr;
        gap: var(--space-xl);
    }
}
```

---

## ЭТАП 7 — Файл 1: `admin/includes/auth.php`

```php
<?php
/**
 * Проверка авторизации в админке
 * Подключается в начале каждой страницы админки
 *
 * Путь: /admin/includes/auth.php
 */

declare(strict_types=1);

if (!defined('IN_ADMIN')) {
    define('IN_ADMIN', true);
}

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';

// ─── Настройки сессии ───
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/admin',
        'domain'   => '',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

/**
 * Проверить, авторизован ли текущий пользователь
 * При провале — редирект на страницу входа
 */
function require_auth(): void
{
    if (!is_authenticated()) {
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '/admin/');
        header('Location: /admin/login.php?redirect=' . $redirect);
        exit;
    }

    // Обновляем время последней активности
    $_SESSION['last_activity'] = time();

    // Сессия устарела (неактивна более 2 часов)
    if (isset($_SESSION['last_activity'])) {
        $inactive = time() - $_SESSION['last_activity'];
        if ($inactive > 7200) {
            session_destroy();
            header('Location: /admin/login.php?reason=timeout');
            exit;
        }
    }

    // Регенерация ID сессии каждые 30 минут
    if (!isset($_SESSION['regenerated_at'])) {
        $_SESSION['regenerated_at'] = time();
    } elseif (time() - $_SESSION['regenerated_at'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['regenerated_at'] = time();
    }
}

/**
 * Проверить авторизацию без редиректа
 *
 * @return bool
 */
function is_authenticated(): bool
{
    return !empty($_SESSION['admin_id'])
        && !empty($_SESSION['admin_token'])
        && isset($_SESSION['last_activity']);
}

/**
 * Авторизовать пользователя
 *
 * @param int    $admin_id
 * @param string $token
 */
function login_admin(int $admin_id, string $token): void
{
    session_regenerate_id(true);
    $_SESSION['admin_id']        = $admin_id;
    $_SESSION['admin_token']     = $token;
    $_SESSION['last_activity']   = time();
    $_SESSION['regenerated_at']  = time();
}

/**
 * Разлогинить пользователя
 */
function logout_admin(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '',
            time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

/**
 * Получить данные текущего администратора
 *
 * @return array|null
 */
function get_current_admin(): ?array
{
    static $admin = null;

    if ($admin !== null) return $admin;

    if (!is_authenticated()) return null;

    try {
        $db    = Database::getInstance();
        $admin = $db->fetchOne(
            "SELECT id, username, email, name FROM admins WHERE id = :id AND is_active = 1",
            [':id' => (int)$_SESSION['admin_id']]
        );
    } catch (Exception $e) {
        log_error('auth: ' . $e->getMessage());
        $admin = null;
    }

    return $admin ?: null;
}
```

---

## ЭТАП 7 — Файл 2: `admin/login.php`

```php
<?php
/**
 * Страница входа в админку
 *
 * Путь: /admin/login.php
 */

declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/includes/auth.php';

// Уже авторизован — редирект
if (is_authenticated()) {
    header('Location: /admin/');
    exit;
}

$error   = '';
$success = '';
$ip      = get_client_ip();

// ─── Обработка формы входа ───
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Ошибка безопасности. Обновите страницу.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Rate limiting: 5 попыток за 15 минут с одного IP
        $rate_key = 'admin_login_' . $ip;
        if (!check_rate_limit($rate_key, 5, 900)) {
            $error = 'Слишком много неудачных попыток. Подождите 15 минут.';
            log_error("admin/login: блокировка IP {$ip}");
        } else {
            try {
                $db = Database::getInstance();

                $admin = $db->fetchOne(
                    "SELECT id, username, password_hash, is_active, name
                     FROM admins
                     WHERE username = :username OR email = :username
                     LIMIT 1",
                    [':username' => $username]
                );

                if ($admin && $admin['is_active'] && password_verify($password, $admin['password_hash'])) {

                    // Успешный вход
                    $token = bin2hex(random_bytes(32));
                    login_admin((int)$admin['id'], $token);

                    // Логируем успешный вход
                    $db->execute(
                        "INSERT INTO admin_sessions (admin_id, token, ip_address, user_agent, created_at, last_activity)
                         VALUES (:admin_id, :token, :ip, :ua, NOW(), NOW())",
                        [
                            ':admin_id' => $admin['id'],
                            ':token'    => hash('sha256', $token),
                            ':ip'       => $ip,
                            ':ua'       => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
                        ]
                    );

                    // Нужна смена пароля (если пароль слабый)?
                    if (password_needs_rehash($admin['password_hash'], PASSWORD_ARGON2ID)) {
                        $new_hash = password_hash($password, PASSWORD_ARGON2ID);
                        $db->execute(
                            "UPDATE admins SET password_hash = :hash WHERE id = :id",
                            [':hash' => $new_hash, ':id' => $admin['id']]
                        );
                    }

                    $redirect = '/admin/';
                    $redirect_param = $_GET['redirect'] ?? '';
                    if ($redirect_param && str_starts_with($redirect_param, '/admin/')) {
                        $redirect = $redirect_param;
                    }

                    header('Location: ' . $redirect);
                    exit;

                } else {
                    // Неверные данные
                    $error = 'Неверный логин или пароль';
                    log_error("admin/login: неудача для '{$username}', IP={$ip}");
                }

            } catch (Exception $e) {
                log_error('admin/login: ' . $e->getMessage());
                $error = 'Произошла ошибка. Попробуйте позже.';
            }
        }
    }
}

$csrf_token = generate_csrf_token();
$reason = $_GET['reason'] ?? '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админку — Хитовая Песня</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/assets/css/variables.css">
    <link rel="stylesheet" href="/assets/css/reset.css">
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/admin/assets/admin.css">
</head>
<body class="admin-login-page">

<div class="login-wrap">

    <div class="login-card">

        <!-- Логотип -->
        <div class="login-card__logo">
            <div class="logo__icon" aria-hidden="true">🎵</div>
            <div>
                <div class="logo__name" style="font-size:18px;">Хитовая Песня</div>
                <div class="logo__slogan">Панель управления</div>
            </div>
        </div>

        <h1 class="login-card__title">Вход</h1>

        <?php if ($reason === 'timeout'): ?>
            <div class="alert alert--warning">⚠️ Сессия истекла. Войдите снова.</div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert--error" role="alert">❌ <?= h($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/admin/login.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">

            <div class="form-group">
                <label class="form-label" for="username">Логин или Email</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-input"
                    value="<?= h($_POST['username'] ?? '') ?>"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="admin"
                >
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Пароль</label>
                <div class="password-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    >
                    <button
                        type="button"
                        class="password-toggle"
                        aria-label="Показать/скрыть пароль"
                        onclick="togglePassword()"
                    >👁</button>
                </div>
            </div>

            <button type="submit" class="btn btn--primary btn--full btn--lg" style="margin-top: var(--space-md);">
                Войти
            </button>
        </form>

        <div class="login-card__back">
            <a href="/" class="btn btn--ghost btn--sm">← На сайт</a>
        </div>

    </div>

</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>
```

---

## ЭТАП 7 — Файл 3: `admin/logout.php`

```php
<?php
/**
 * Выход из админки
 * Путь: /admin/logout.php
 */

declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// CSRF для выхода
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        logout_admin();
    }
} else {
    // GET тоже разрешаем (прямая ссылка)
    logout_admin();
}

header('Location: /admin/login.php');
exit;
```

---

## ЭТАП 7 — Файл 4: `admin/includes/admin-header.php`

```php
<?php
/**
 * Шапка и sidebar админки
 * Путь: /admin/includes/admin-header.php
 */

$current_admin = get_current_admin();
$admin_name    = $current_admin['name'] ?? $current_admin['username'] ?? 'Администратор';
$current_file  = basename($_SERVER['PHP_SELF'], '.php');
$admin_csrf    = generate_csrf_token();

$nav_items = [
    ['file' => 'index',      'icon' => '📊', 'label' => 'Дашборд',  'href' => '/admin/'],
    ['file' => 'orders',     'icon' => '📋', 'label' => 'Заявки',   'href' => '/admin/orders.php'],
    ['file' => 'tracks',     'icon' => '🎵', 'label' => 'Треки',    'href' => '/admin/tracks.php'],
    ['file' => 'stats',      'icon' => '📈', 'label' => 'Статистика','href' => '/admin/stats.php'],
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($page_title ?? 'Админка') ?> — Хитовая Песня</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/assets/css/variables.css">
    <link rel="stylesheet" href="/assets/css/reset.css">
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/admin/assets/admin.css">
    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="<?= h($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="admin-body">

<!-- ─── Sidebar ─── -->
<aside class="admin-sidebar" id="admin-sidebar" role="navigation" aria-label="Навигация админки">

    <div class="admin-sidebar__logo">
        <div class="logo__icon" aria-hidden="true">🎵</div>
        <div>
            <div class="admin-sidebar__logo-name">Хитовая Песня</div>
            <div class="admin-sidebar__logo-sub">Панель управления</div>
        </div>
    </div>

    <nav class="admin-nav">
        <?php foreach ($nav_items as $item): ?>
            <a
                href="<?= h($item['href']) ?>"
                class="admin-nav__item<?= $current_file === $item['file'] || ($item['file'] === 'index' && $current_file === 'index') ? ' active' : '' ?>"
            >
                <span class="admin-nav__icon" aria-hidden="true"><?= $item['icon'] ?></span>
                <span class="admin-nav__label"><?= h($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="admin-sidebar__bottom">
        <a href="/" class="admin-nav__item" target="_blank" rel="noopener">
            <span class="admin-nav__icon" aria-hidden="true">🌐</span>
            <span class="admin-nav__label">На сайт</span>
        </a>
        <form method="POST" action="/admin/logout.php">
            <input type="hidden" name="csrf_token" value="<?= h($admin_csrf) ?>">
            <button type="submit" class="admin-nav__item admin-nav__item--btn">
                <span class="admin-nav__icon" aria-hidden="true">🚪</span>
                <span class="admin-nav__label">Выйти</span>
            </button>
        </form>
    </div>

</aside>

<!-- ─── Основной контент ─── -->
<div class="admin-main">

    <!-- Topbar -->
    <header class="admin-topbar">
        <button class="admin-burger" id="admin-burger" aria-label="Меню">
            <span></span><span></span><span></span>
        </button>

        <h1 class="admin-topbar__title"><?= h($page_title ?? 'Админка') ?></h1>

        <div class="admin-topbar__right">
            <span class="admin-topbar__user">
                👤 <?= h($admin_name) ?>
            </span>
            <a href="/" target="_blank" class="btn btn--outline btn--sm">🌐 Сайт</a>
        </div>
    </header>

    <!-- Контент -->
    <div class="admin-content">
```

---

## ЭТАП 7 — Файл 5: `admin/includes/admin-footer.php`

```php
<?php
/**
 * Закрывающие теги и JS для админки
 * Путь: /admin/includes/admin-footer.php
 */
?>
    </div><!-- /.admin-content -->
</div><!-- /.admin-main -->

<script src="/assets/js/notifications.js" defer></script>
<script src="/admin/assets/admin.js" defer></script>
<?php if (isset($extra_js)): ?>
    <?php foreach ($extra_js as $js): ?>
        <script src="<?= h($js) ?>" defer></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
```

---

## ЭТАП 7 — Файл 6: `admin/index.php` (Дашборд)

```php
<?php
/**
 * Дашборд — главная страница админки
 * Статистика, последние заявки, графики
 *
 * Путь: /admin/index.php
 */

declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/includes/auth.php';
require_auth();

$page_title = 'Дашборд';

try {
    $db = Database::getInstance();

    // ─── Статистика заявок ───
    $stats_orders = $db->fetchOne(
        "SELECT
            COUNT(*) AS total,
            SUM(status = 'new') AS new_count,
            SUM(status = 'in_progress') AS progress_count,
            SUM(status = 'done') AS done_count,
            SUM(DATE(created_at) = CURDATE()) AS today_count,
            SUM(created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) AS week_count,
            SUM(created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS month_count
         FROM orders"
    );

    // ─── Статистика треков ───
    $stats_tracks = $db->fetchOne(
        "SELECT COUNT(*) AS total, SUM(is_active = 1) AS active_count FROM tracks"
    );

    // ─── Посетители сегодня (из page_views) ───
    $stats_views = $db->fetchOne(
        "SELECT COUNT(*) AS today FROM page_views WHERE DATE(created_at) = CURDATE()"
    );

    // ─── Последние 5 заявок ───
    $recent_orders = $db->fetchAll(
        "SELECT id, order_number, client_name, occasion, tariff, status, created_at
         FROM orders
         ORDER BY created_at DESC
         LIMIT 5"
    );

    // ─── График заявок за 30 дней (данные для JS) ───
    $chart_data = $db->fetchAll(
        "SELECT DATE(created_at) AS date, COUNT(*) AS count
         FROM orders
         WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
         GROUP BY DATE(created_at)
         ORDER BY date ASC"
    );

    // ─── Топ категорий ───
    $top_occasions = $db->fetchAll(
        "SELECT occasion, COUNT(*) AS cnt
         FROM orders
         GROUP BY occasion
         ORDER BY cnt DESC
         LIMIT 5"
    );

} catch (Exception $e) {
    log_error('admin/index: ' . $e->getMessage());
    $stats_orders  = ['total'=>0,'new_count'=>0,'progress_count'=>0,'done_count'=>0,'today_count'=>0,'week_count'=>0,'month_count'=>0];
    $stats_tracks  = ['total'=>0,'active_count'=>0];
    $stats_views   = ['today'=>0];
    $recent_orders = [];
    $chart_data    = [];
    $top_occasions = [];
}

// Подготовка данных графика
$chart_labels = [];
$chart_values = [];
$chart_map    = [];
foreach ($chart_data as $row) {
    $chart_map[$row['date']] = (int)$row['count'];
}
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $chart_labels[] = date('d.m', strtotime($date));
    $chart_values[] = $chart_map[$date] ?? 0;
}

$extra_css = ['https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css'];

require_once __DIR__ . '/includes/admin-header.php';
?>

<!-- Виджеты статистики -->
<div class="dash-stats">

    <div class="dash-stat dash-stat--primary">
        <div class="dash-stat__icon" aria-hidden="true">📋</div>
        <div class="dash-stat__content">
            <div class="dash-stat__value"><?= (int)$stats_orders['total'] ?></div>
            <div class="dash-stat__label">Всего заявок</div>
            <div class="dash-stat__sub">Сегодня: <b><?= (int)$stats_orders['today_count'] ?></b></div>
        </div>
    </div>

    <div class="dash-stat dash-stat--warning">
        <div class="dash-stat__icon" aria-hidden="true">🆕</div>
        <div class="dash-stat__content">
            <div class="dash-stat__value"><?= (int)$stats_orders['new_count'] ?></div>
            <div class="dash-stat__label">Новых заявок</div>
            <div class="dash-stat__sub">Требуют ответа</div>
        </div>
    </div>

    <div class="dash-stat dash-stat--info">
        <div class="dash-stat__icon" aria-hidden="true">⚙️</div>
        <div class="dash-stat__content">
            <div class="dash-stat__value"><?= (int)$stats_orders['progress_count'] ?></div>
            <div class="dash-stat__label">В работе</div>
            <div class="dash-stat__sub">За неделю: <b><?= (int)$stats_orders['week_count'] ?></b></div>
        </div>
    </div>

    <div class="dash-stat dash-stat--success">
        <div class="dash-stat__icon" aria-hidden="true">✅</div>
        <div class="dash-stat__content">
            <div class="dash-stat__value"><?= (int)$stats_orders['done_count'] ?></div>
            <div class="dash-stat__label">Выполнено</div>
            <div class="dash-stat__sub">За месяц: <b><?= (int)$stats_orders['month_count'] ?></b></div>
        </div>
    </div>

    <div class="dash-stat">
        <div class="dash-stat__icon" aria-hidden="true">🎵</div>
        <div class="dash-stat__content">
            <div class="dash-stat__value"><?= (int)$stats_tracks['active_count'] ?></div>
            <div class="dash-stat__label">Треков в портфолио</div>
            <div class="dash-stat__sub">Всего: <b><?= (int)$stats_tracks['total'] ?></b></div>
        </div>
    </div>

    <div class="dash-stat">
        <div class="dash-stat__icon" aria-hidden="true">👁️</div>
        <div class="dash-stat__content">
            <div class="dash-stat__value"><?= (int)$stats_views['today'] ?></div>
            <div class="dash-stat__label">Посетителей сегодня</div>
            <div class="dash-stat__sub">&nbsp;</div>
        </div>
    </div>

</div><!-- /.dash-stats -->


<!-- График + Топ -->
<div class="dash-grid">

    <!-- График заявок -->
    <div class="admin-card admin-card--chart">
        <div class="admin-card__header">
            <h2 class="admin-card__title">Заявки за 30 дней</h2>
        </div>
        <div class="admin-card__body">
            <canvas id="ordersChart" height="100" aria-label="График заявок за 30 дней"></canvas>
        </div>
    </div>

    <!-- Топ поводов -->
    <div class="admin-card">
        <div class="admin-card__header">
            <h2 class="admin-card__title">Топ поводов</h2>
        </div>
        <div class="admin-card__body">
            <?php if (empty($top_occasions)): ?>
                <p class="admin-empty">Заявок пока нет</p>
            <?php else: ?>
                <ul class="top-list">
                    <?php foreach ($top_occasions as $occ): ?>
                        <li class="top-list__item">
                            <span class="top-list__label">
                                <?= h(get_occasion_label($occ['occasion'])) ?>
                            </span>
                            <span class="top-list__count"><?= (int)$occ['cnt'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

</div><!-- /.dash-grid -->


<!-- Последние заявки -->
<div class="admin-card">
    <div class="admin-card__header">
        <h2 class="admin-card__title">Последние заявки</h2>
        <a href="/admin/orders.php" class="btn btn--outline btn--sm">Все заявки</a>
    </div>
    <div class="admin-card__body admin-card__body--no-pad">

        <?php if (empty($recent_orders)): ?>
            <p class="admin-empty" style="padding: var(--space-lg);">Заявок пока нет</p>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Номер</th>
                            <th>Клиент</th>
                            <th>Повод</th>
                            <th>Тариф</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                            <tr class="admin-table__row" data-href="/admin/order-view.php?id=<?= (int)$order['id'] ?>">
                                <td>
                                    <span class="order-number"><?= h($order['order_number']) ?></span>
                                </td>
                                <td><?= h($order['client_name']) ?></td>
                                <td><?= h(get_occasion_label($order['occasion'])) ?></td>
                                <td><?= h(get_tariff_label($order['tariff'])) ?></td>
                                <td><?= render_status_badge($order['status']) ?></td>
                                <td>
                                    <span class="admin-date">
                                        <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/admin/order-view.php?id=<?= (int)$order['id'] ?>" class="btn btn--sm btn--outline">
                                        Открыть
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const ctx = document.getElementById('ordersChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_labels, JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{
                label: 'Заявки',
                data: <?= json_encode($chart_values) ?>,
                borderColor: '#8B1E3F',
                backgroundColor: 'rgba(139,30,63,0.08)',
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#8B1E3F',
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: 'rgba(0,0,0,0.05)' },
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        maxTicksLimit: 10,
                        maxRotation: 0,
                    },
                },
            },
        },
    });
})();
</script>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
```

---

## ЭТАП 7 — Файл 7: `admin/orders.php`

```php
<?php
/**
 * Список заявок с фильтрами и поиском
 * Путь: /admin/orders.php
 */

declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/includes/auth.php';
require_auth();

$page_title = 'Заявки';

// ─── Параметры фильтрации ───
$filter_status   = preg_replace('/[^a-z_]/', '', $_GET['status'] ?? '');
$filter_tariff   = preg_replace('/[^a-z_]/', '', $_GET['tariff'] ?? '');
$filter_occasion = preg_replace('/[^a-z_]/', '', $_GET['occasion'] ?? '');
$search          = sanitize_string($_GET['search'] ?? '', 100);
$page            = max(1, (int)($_GET['page'] ?? 1));
$per_page        = 20;

try {
    $db = Database::getInstance();

    // ─── WHERE условия ───
    $conditions = ['1=1'];
    $params     = [];

    if ($filter_status) {
        $conditions[] = 'status = :status';
        $params[':status'] = $filter_status;
    }
    if ($filter_tariff) {
        $conditions[] = 'tariff = :tariff';
        $params[':tariff'] = $filter_tariff;
    }
    if ($filter_occasion) {
        $conditions[] = 'occasion = :occasion';
        $params[':occasion'] = $filter_occasion;
    }
    if ($search) {
        $conditions[] = '(client_name LIKE :search OR client_phone LIKE :search OR order_number LIKE :search OR hero_name LIKE :search)';
        $params[':search'] = '%' . $search . '%';
    }

    $where = 'WHERE ' . implode(' AND ', $conditions);

    // Общее количество
    $total_row = $db->fetchOne("SELECT COUNT(*) AS total FROM orders {$where}", $params);
    $total     = (int)($total_row['total'] ?? 0);
    $pages     = (int)ceil($total / $per_page);

    // Заявки
    $offset = ($page - 1) * $per_page;
    $params[':limit']  = $per_page;
    $params[':offset'] = $offset;

    $orders = $db->fetchAll(
        "SELECT id, order_number, client_name, client_phone, occasion, tariff, urgency, status, created_at
         FROM orders
         {$where}
         ORDER BY created_at DESC
         LIMIT :limit OFFSET :offset",
        $params
    );

    // Счётчики по статусам (для вкладок)
    $status_counts = $db->fetchAll(
        "SELECT status, COUNT(*) AS cnt FROM orders GROUP BY status"
    );
    $status_map = [];
    foreach ($status_counts as $row) {
        $status_map[$row['status']] = (int)$row['cnt'];
    }

} catch (Exception $e) {
    log_error('admin/orders: ' . $e->getMessage());
    $orders = [];
    $total  = 0;
    $pages  = 0;
    $status_map = [];
}

$statuses = [
    ''            => ['label' => 'Все',       'count' => $total],
    'new'         => ['label' => 'Новые',     'count' => $status_map['new'] ?? 0],
    'in_progress' => ['label' => 'В работе',  'count' => $status_map['in_progress'] ?? 0],
    'review'      => ['label' => 'На проверке','count' => $status_map['review'] ?? 0],
    'done'        => ['label' => 'Выполнены', 'count' => $status_map['done'] ?? 0],
    'cancelled'   => ['label' => 'Отменены',  'count' => $status_map['cancelled'] ?? 0],
];

require_once __DIR__ . '/includes/admin-header.php';
?>

<!-- Вкладки статусов -->
<div class="admin-tabs">
    <?php foreach ($statuses as $status_val => $status_info): ?>
        <?php
            $tab_url = '/admin/orders.php?' . http_build_query(array_filter([
                'status'   => $status_val,
                'tariff'   => $filter_tariff,
                'occasion' => $filter_occasion,
                'search'   => $search,
            ]));
        ?>
        <a
            href="<?= h($tab_url) ?>"
            class="admin-tab<?= $filter_status === $status_val ? ' active' : '' ?>"
        >
            <?= h($status_info['label']) ?>
            <span class="admin-tab__count"><?= $status_info['count'] ?></span>
        </a>
    <?php endforeach; ?>
</div>

<!-- Фильтры и поиск -->
<div class="admin-card admin-card--filters">
    <form method="GET" action="/admin/orders.php" class="admin-filters">

        <input type="hidden" name="status" value="<?= h($filter_status) ?>">

        <div class="admin-filter__search">
            <input
                type="search"
                name="search"
                class="form-input"
                placeholder="Поиск по имени, телефону, номеру…"
                value="<?= h($search) ?>"
            >
        </div>

        <select name="tariff" class="form-input form-select--sm">
            <option value="">Все тарифы</option>
            <option value="basic"    <?= $filter_tariff === 'basic'    ? 'selected' : '' ?>>Базовый</option>
            <option value="standard" <?= $filter_tariff === 'standard' ? 'selected' : '' ?>>Стандарт</option>
            <option value="premium"  <?= $filter_tariff === 'premium'  ? 'selected' : '' ?>>Премиум</option>
            <option value="help"     <?= $filter_tariff === 'help'     ? 'selected' : '' ?>>Помогите выбрать</option>
        </select>

        <select name="occasion" class="form-input form-select--sm">
            <option value="">Все поводы</option>
            <?php foreach (['wedding','anniversary','birthday','love','corporate','march8','feb23','newyear','proposal','birth','retirement','other'] as $occ): ?>
                <option value="<?= $occ ?>" <?= $filter_occasion === $occ ? 'selected' : '' ?>>
                    <?= h(get_occasion_label($occ)) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn--primary btn--sm">Найти</button>

        <?php if ($search || $filter_tariff || $filter_occasion): ?>
            <a href="/admin/orders.php?status=<?= h($filter_status) ?>" class="btn btn--ghost btn--sm">
                Сбросить
            </a>
        <?php endif; ?>

    </form>
</div>

<!-- Таблица заявок -->
<div class="admin-card">
    <div class="admin-card__header">
        <h2 class="admin-card__title">
            Заявки
            <span class="admin-card__count"><?= $total ?></span>
        </h2>
    </div>
    <div class="admin-card__body admin-card__body--no-pad">

        <?php if (empty($orders)): ?>
            <div class="admin-empty" style="padding: var(--space-2xl);">
                <p>📭 Заявок не найдено</p>
                <?php if ($search || $filter_tariff || $filter_occasion || $filter_status): ?>
                    <a href="/admin/orders.php" class="btn btn--outline btn--sm" style="margin-top: var(--space-sm);">
                        Показать все
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Номер</th>
                            <th>Дата</th>
                            <th>Клиент</th>
                            <th>Телефон</th>
                            <th>Повод</th>
                            <th>Тариф</th>
                            <th>Срочность</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr class="admin-table__row">
                                <td>
                                    <a href="/admin/order-view.php?id=<?= (int)$order['id'] ?>" class="order-number">
                                        <?= h($order['order_number']) ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="admin-date">
                                        <?= date('d.m.Y', strtotime($order['created_at'])) ?><br>
                                        <small><?= date('H:i', strtotime($order['created_at'])) ?></small>
                                    </span>
                                </td>
                                <td><b><?= h($order['client_name']) ?></b></td>
                                <td>
                                    <a href="tel:<?= h(preg_replace('/\D/', '', $order['client_phone'])) ?>" class="admin-phone">
                                        <?= h($order['client_phone']) ?>
                                    </a>
                                </td>
                                <td><?= h(get_occasion_label($order['occasion'])) ?></td>
                                <td><?= h(get_tariff_label($order['tariff'])) ?></td>
                                <td><?= h(get_urgency_label($order['urgency'])) ?></td>
                                <td><?= render_status_badge($order['status']) ?></td>
                                <td>
                                    <a href="/admin/order-view.php?id=<?= (int)$order['id'] ?>" class="btn btn--sm btn--outline">
                                        Открыть
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Пагинация -->
            <?php if ($pages > 1): ?>
                <div class="pagination" style="padding: var(--space-lg);">
                    <?php for ($p = 1; $p <= $pages; $p++): ?>
                        <?php
                            $page_url = '/admin/orders.php?' . http_build_query(array_filter([
                                'status'   => $filter_status,
                                'tariff'   => $filter_tariff,
                                'occasion' => $filter_occasion,
                                'search'   => $search,
                                'page'     => $p,
                            ]));
                        ?>
                        <a
                            href="<?= h($page_url) ?>"
                            class="pagination__btn<?= $p === $page ? ' active' : '' ?>"
                        ><?= $p ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
```

---

## ЭТАП 7 — Файл 8: `admin/order-view.php`

```php
<?php
/**
 * Просмотр и редактирование заявки
 * Путь: /admin/order-view.php
 */

declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/includes/auth.php';
require_auth();

$order_id = (int)($_GET['id'] ?? 0);
if (!$order_id) {
    header('Location: /admin/orders.php');
    exit;
}

$csrf_token = generate_csrf_token();
$message    = '';
$msg_type   = '';

try {
    $db    = Database::getInstance();
    $order = $db->fetchOne("SELECT * FROM orders WHERE id = :id", [':id' => $order_id]);

    if (!$order) {
        header('Location: /admin/orders.php');
        exit;
    }

    // ─── Смена статуса ───
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $message  = 'Ошибка CSRF';
            $msg_type = 'error';
        } else {
            $new_status = preg_replace('/[^a-z_]/', '', $_POST['new_status'] ?? '');
            $note       = sanitize_text($_POST['status_note'] ?? '', 500);

            $allowed_statuses = ['new', 'in_progress', 'review', 'done', 'cancelled'];
            if (in_array($new_status, $allowed_statuses, true)) {
                $old_status = $order['status'];

                $db->execute(
                    "UPDATE orders SET status = :status, updated_at = NOW() WHERE id = :id",
                    [':status' => $new_status, ':id' => $order_id]
                );

                $db->execute(
                    "INSERT INTO order_logs (order_id, action, old_status, new_status, note, created_at)
                     VALUES (:order_id, 'status_change', :old, :new, :note, NOW())",
                    [
                        ':order_id' => $order_id,
                        ':old'      => $old_status,
                        ':new'      => $new_status,
                        ':note'     => $note,
                    ]
                );

                $order['status'] = $new_status;
                $message  = 'Статус обновлён';
                $msg_type = 'success';
            }
        }
    }

    // ─── История изменений ───
    $logs = $db->fetchAll(
        "SELECT * FROM order_logs WHERE order_id = :id ORDER BY created_at DESC LIMIT 20",
        [':id' => $order_id]
    );

} catch (Exception $e) {
    log_error('admin/order-view: ' . $e->getMessage());
    header('Location: /admin/orders.php');
    exit;
}

$page_title = 'Заявка ' . h($order['order_number']);

require_once __DIR__ . '/includes/admin-header.php';
?>

<?php if ($message): ?>
    <div class="alert alert--<?= $msg_type === 'success' ? 'success' : 'error' ?>">
        <?= h($message) ?>
    </div>
<?php endif; ?>

<!-- Хлебные крошки -->
<div class="admin-breadcrumb">
    <a href="/admin/orders.php">Заявки</a> › <?= h($order['order_number']) ?>
</div>

<div class="order-view-grid">

    <!-- ─── Левая колонка: данные ─── -->
    <div class="order-view-main">

        <!-- Статус и быстрые действия -->
        <div class="admin-card">
            <div class="admin-card__header">
                <div>
                    <h2 class="admin-card__title"><?= h($order['order_number']) ?></h2>
                    <div style="margin-top: 6px;"><?= render_status_badge($order['status']) ?></div>
                </div>
                <div class="admin-card__actions">
                    <?php if ($order['client_phone']): ?>
                        <a href="tel:<?= h(preg_replace('/\D/', '', $order['client_phone'])) ?>" class="btn btn--sm btn--primary">
                            📱 Позвонить
                        </a>
                    <?php endif; ?>
                    <?php if ($order['client_telegram']): ?>
                        <a href="https://t.me/<?= h(ltrim($order['client_telegram'], '@')) ?>" class="btn btn--sm btn--outline" target="_blank" rel="noopener">
                            ✈️ Telegram
                        </a>
                    <?php endif; ?>
                    <?php if ($order['client_whatsapp']): ?>
                        <a href="https://wa.me/<?= h(preg_replace('/\D/', '', $order['client_whatsapp'])) ?>" class="btn btn--sm btn--outline" target="_blank" rel="noopener">
                            💚 WhatsApp
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Данные клиента -->
        <div class="admin-card">
            <div class="admin-card__header">
                <h2 class="admin-card__title">Клиент</h2>
            </div>
            <div class="admin-card__body">
                <dl class="order-dl">
                    <div class="order-dl__row">
                        <dt>Имя</dt>
                        <dd><?= h($order['client_name']) ?></dd>
                    </div>
                    <div class="order-dl__row">
                        <dt>Телефон</dt>
                        <dd>
                            <a href="tel:<?= h(preg_replace('/\D/', '', $order['client_phone'])) ?>">
                                <?= h($order['client_phone']) ?>
                            </a>
                        </dd>
                    </div>
                    <?php if ($order['client_telegram']): ?>
                        <div class="order-dl__row">
                            <dt>Telegram</dt>
                            <dd><a href="https://t.me/<?= h(ltrim($order['client_telegram'], '@')) ?>" target="_blank"><?= h($order['client_telegram']) ?></a></dd>
                        </div>
                    <?php endif; ?>
                    <?php if ($order['client_whatsapp']): ?>
                        <div class="order-dl__row">
                            <dt>WhatsApp</dt>
                            <dd><?= h($order['client_whatsapp']) ?></dd>
                        </div>
                    <?php endif; ?>
                    <?php if ($order['client_vk']): ?>
                        <div class="order-dl__row">
                            <dt>ВКонтакте</dt>
                            <dd><a href="<?= h($order['client_vk']) ?>" target="_blank"><?= h($order['client_vk']) ?></a></dd>
                        </div>
                    <?php endif; ?>
                    <?php if ($order['client_ok']): ?>
                        <div class="order-dl__row">
                            <dt>Одноклассники</dt>
                            <dd><a href="<?= h($order['client_ok']) ?>" target="_blank"><?= h($order['client_ok']) ?></a></dd>
                        </div>
                    <?php endif; ?>
                    <?php if ($order['client_email']): ?>
                        <div class="order-dl__row">
                            <dt>Email</dt>
                            <dd><a href="mailto:<?= h($order['client_email']) ?>"><?= h($order['client_email']) ?></a></dd>
                        </div>
                    <?php endif; ?>
                    <div class="order-dl__row">
                        <dt>Связаться</dt>
                        <dd><?= h(get_contact_time_label($order['contact_time'])) ?> — <?= h($order['contact_method']) ?></dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Детали заказа -->
        <div class="admin-card">
            <div class="admin-card__header">
                <h2 class="admin-card__title">Детали заказа</h2>
            </div>
            <div class="admin-card__body">
                <dl class="order-dl">
                    <div class="order-dl__row">
                        <dt>Повод</dt>
                        <dd>
                            <?= h(get_occasion_label($order['occasion'])) ?>
                            <?= $order['occasion'] === 'other' && $order['occasion_other'] ? ': ' . h($order['occasion_other']) : '' ?>
                        </dd>
                    </div>
                    <?php if ($order['event_date']): ?>
                        <div class="order-dl__row">
                            <dt>Дата</dt>
                            <dd><?= date('d.m.Y', strtotime($order['event_date'])) ?></dd>
                        </div>
                    <?php endif; ?>
                    <div class="order-dl__row">
                        <dt>Срочность</dt>
                        <dd><?= h(get_urgency_label($order['urgency'])) ?></dd>
                    </div>
                    <div class="order-dl__row">
                        <dt>Тариф</dt>
                        <dd><?= h(get_tariff_label($order['tariff'])) ?></dd>
                    </div>
                    <div class="order-dl__row">
                        <dt>Герой</dt>
                        <dd>
                            <?= h($order['hero_name']) ?>
                            <?= $order['hero_age'] ? ', ' . (int)$order['hero_age'] . ' лет' : '' ?>
                            <?= $order['hero_relation'] ? ' (' . h($order['hero_relation']) . ')' : '' ?>
                        </dd>
                    </div>
                    <?php if ($order['hero_profession']): ?>
                        <div class="order-dl__row">
                            <dt>Профессия</dt>
                            <dd><?= h($order['hero_profession']) ?></dd>
                        </div>
                    <?php endif; ?>
                    <?php if ($order['hero_hobbies']): ?>
                        <div class="order-dl__row">
                            <dt>Хобби</dt>
                            <dd><?= h($order['hero_hobbies']) ?></dd>
                        </div>
                    <?php endif; ?>
                    <div class="order-dl__row">
                        <dt>Настроение</dt>
                        <dd><?= h($order['mood'] ?: 'Не указано') ?></dd>
                    </div>
                    <?php if ($order['music_styles']): ?>
                        <div class="order-dl__row">
                            <dt>Стиль</dt>
                            <dd><?= h($order['music_styles']) ?></dd>
                        </div>
                    <?php endif; ?>
                    <div class="order-dl__row">
                        <dt>Голос</dt>
                        <dd><?= h($order['voice_type'] ?: 'Не указан') ?></dd>
                    </div>
                    <div class="order-dl__row">
                        <dt>Длительность</dt>
                        <dd><?= h(get_duration_label($order['duration'])) ?></dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Текст истории -->
        <div class="admin-card">
            <div class="admin-card__header">
                <h2 class="admin-card__title">История</h2>
            </div>
            <div class="admin-card__body">
                <div class="order-story">
                    <?= nl2br(h($order['story'])) ?>
                </div>
                <?php if ($order['must_include']): ?>
                    <div class="order-story-section">
                        <strong>Обязательно упомянуть:</strong>
                        <p><?= nl2br(h($order['must_include'])) ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($order['avoid']): ?>
                    <div class="order-story-section">
                        <strong>Избегать:</strong>
                        <p><?= nl2br(h($order['avoid'])) ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($order['extra_wishes']): ?>
                    <div class="order-story-section">
                        <strong>Дополнительные пожелания:</strong>
                        <p><?= nl2br(h($order['extra_wishes'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /.order-view-main -->


    <!-- ─── Правая колонка: управление ─── -->
    <div class="order-view-sidebar">

        <!-- Смена статуса -->
        <div class="admin-card">
            <div class="admin-card__header">
                <h2 class="admin-card__title">Изменить статус</h2>
            </div>
            <div class="admin-card__body">
                <form method="POST" action="/admin/order-view.php?id=<?= $order_id ?>">
                    <input type="hidden" name="change_status" value="1">
                    <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">

                    <div class="form-group">
                        <label class="form-label" for="new_status">Новый статус</label>
                        <select name="new_status" id="new_status" class="form-input">
                            <option value="new"         <?= $order['status'] === 'new'         ? 'selected' : '' ?>>🆕 Новая</option>
                            <option value="in_progress" <?= $order['status'] === 'in_progress' ? 'selected' : '' ?>>⚙️ В работе</option>
                            <option value="review"      <?= $order['status'] === 'review'      ? 'selected' : '' ?>>👁️ На проверке</option>
                            <option value="done"        <?= $order['status'] === 'done'        ? 'selected' : '' ?>>✅ Выполнена</option>
                            <option value="cancelled"   <?= $order['status'] === 'cancelled'   ? 'selected' : '' ?>>❌ Отменена</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="status_note">Примечание</label>
                        <textarea id="status_note" name="status_note" class="form-textarea" rows="3" placeholder="Комментарий к смене статуса…" maxlength="500"></textarea>
                    </div>

                    <button type="submit" class="btn btn--primary btn--full">Сохранить</button>
                </form>
            </div>
        </div>

        <!-- Мета-данные -->
        <div class="admin-card">
            <div class="admin-card__header">
                <h2 class="admin-card__title">Информация</h2>
            </div>
            <div class="admin-card__body">
                <dl class="order-dl">
                    <div class="order-dl__row">
                        <dt>Создана</dt>
                        <dd><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></dd>
                    </div>
                    <?php if ($order['updated_at']): ?>
                        <div class="order-dl__row">
                            <dt>Обновлена</dt>
                            <dd><?= date('d.m.Y H:i', strtotime($order['updated_at'])) ?></dd>
                        </div>
                    <?php endif; ?>
                    <div class="order-dl__row">
                        <dt>IP</dt>
                        <dd style="font-family:monospace;"><?= h($order['ip_address']) ?></dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- История изменений -->
        <?php if (!empty($logs)): ?>
            <div class="admin-card">
                <div class="admin-card__header">
                    <h2 class="admin-card__title">История</h2>
                </div>
                <div class="admin-card__body">
                    <ul class="order-log">
                        <?php foreach ($logs as $log): ?>
                            <li class="order-log__item">
                                <div class="order-log__time">
                                    <?= date('d.m.Y H:i', strtotime($log['created_at'])) ?>
                                </div>
                                <div class="order-log__text">
                                    <?php if ($log['action'] === 'status_change'): ?>
                                        Статус: <b><?= h($log['old_status']) ?></b> → <b><?= h($log['new_status']) ?></b>
                                    <?php else: ?>
                                        <?= h($log['action']) ?>
                                    <?php endif; ?>
                                    <?php if ($log['note']): ?>
                                        <div class="order-log__note"><?= h($log['note']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

    </div><!-- /.order-view-sidebar -->

</div><!-- /.order-view-grid -->

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
```

---

## ЭТАП 7 — Файл 9: `admin/tracks.php`

```php
<?php
/**
 * Управление треками портфолио
 * Путь: /admin/tracks.php
 */

declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/includes/auth.php';
require_auth();

$page_title = 'Треки портфолио';
$csrf_token = generate_csrf_token();

// ─── Обработка действий ───
$message  = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = 'Ошибка CSRF';
        $msg_type = 'error';
    } else {
        $action   = $_POST['action'] ?? '';
        $track_id = (int)($_POST['track_id'] ?? 0);

        try {
            $db = Database::getInstance();

            if ($action === 'toggle_active' && $track_id) {
                $db->execute(
                    "UPDATE tracks SET is_active = !is_active WHERE id = :id",
                    [':id' => $track_id]
                );
                $message = 'Видимость обновлена'; $msg_type = 'success';
            }

            if ($action === 'toggle_featured' && $track_id) {
                $db->execute(
                    "UPDATE tracks SET is_featured = !is_featured WHERE id = :id",
                    [':id' => $track_id]
                );
                $message = 'Избранное обновлено'; $msg_type = 'success';
            }

            if ($action === 'delete' && $track_id) {
                $track = $db->fetchOne("SELECT * FROM tracks WHERE id = :id", [':id' => $track_id]);
                if ($track) {
                    // Удаляем файлы
                    if ($track['audio_file']) @unlink(UPLOAD_PATH . '/tracks/' . $track['audio_file']);
                    if ($track['cover_image']) @unlink(UPLOAD_PATH . '/covers/' . $track['cover_image']);
                    $db->execute("DELETE FROM tracks WHERE id = :id", [':id' => $track_id]);
                    $message = 'Трек удалён'; $msg_type = 'success';
                }
            }

        } catch (Exception $e) {
            log_error('admin/tracks: ' . $e->getMessage());
            $message = 'Ошибка: ' . $e->getMessage();
            $msg_type = 'error';
        }
    }
}

// ─── Список треков ───
try {
    $db     = Database::getInstance();
    $tracks = $db->fetchAll(
        "SELECT t.*, c.name AS category_name
         FROM tracks t
         LEFT JOIN track_categories c ON t.category_id = c.id
         ORDER BY t.sort_order ASC, t.created_at DESC"
    );
    $categories = $db->fetchAll("SELECT * FROM track_categories ORDER BY sort_order ASC");
} catch (Exception $e) {
    log_error('admin/tracks: ' . $e->getMessage());
    $tracks = []; $categories = [];
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<?php if ($message): ?>
    <div class="alert alert--<?= $msg_type === 'success' ? 'success' : 'error' ?>">
        <?= h($message) ?>
    </div>
<?php endif; ?>

<div class="admin-card__header" style="margin-bottom: var(--space-md);">
    <div></div>
    <a href="/admin/track-add.php" class="btn btn--primary">+ Добавить трек</a>
</div>

<div class="admin-card">
    <div class="admin-card__header">
        <h2 class="admin-card__title">
            Треки <span class="admin-card__count"><?= count($tracks) ?></span>
        </h2>
    </div>
    <div class="admin-card__body admin-card__body--no-pad">

        <?php if (empty($tracks)): ?>
            <div class="admin-empty" style="padding: var(--space-2xl); text-align:center;">
                <p>🎵 Треков пока нет</p>
                <a href="/admin/track-add.php" class="btn btn--primary" style="margin-top: var(--space-md);">
                    Добавить первый трек
                </a>
            </div>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th width="60">Обложка</th>
                            <th>Название</th>
                            <th>Категория</th>
                            <th>Стиль</th>
                            <th>👂 Прослушиваний</th>
                            <th>Главная</th>
                            <th>Активен</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tracks as $track): ?>
                            <tr class="admin-table__row">
                                <td>
                                    <?php if ($track['cover_image']): ?>
                                        <img
                                            src="/uploads/covers/<?= h($track['cover_image']) ?>"
                                            alt="<?= h($track['title']) ?>"
                                            style="width:48px;height:48px;object-fit:cover;border-radius:8px;"
                                            loading="lazy"
                                        >
                                    <?php else: ?>
                                        <div style="width:48px;height:48px;background:var(--color-accent-light);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px;">🎵</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= h($track['title']) ?></strong>
                                    <?php if ($track['duration']): ?>
                                        <br><small class="text-muted"><?= h(format_duration((int)$track['duration'])) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= h($track['category_name'] ?? '—') ?></td>
                                <td><?= h($track['style'] ?? '—') ?></td>
                                <td style="text-align:center;"><?= number_format((int)$track['plays_count']) ?></td>
                                <td style="text-align:center;">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">
                                        <input type="hidden" name="action" value="toggle_featured">
                                        <input type="hidden" name="track_id" value="<?= (int)$track['id'] ?>">
                                        <button type="submit" class="toggle-btn" title="Вкл/выкл на главной">
                                            <?= $track['is_featured'] ? '⭐' : '☆' ?>
                                        </button>
                                    </form>
                                </td>
                                <td style="text-align:center;">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">
                                        <input type="hidden" name="action" value="toggle_active">
                                        <input type="hidden" name="track_id" value="<?= (int)$track['id'] ?>">
                                        <button type="submit" class="toggle-btn" title="Вкл/выкл видимость">
                                            <?= $track['is_active'] ? '✅' : '❌' ?>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div style="display:flex;gap:6px;">
                                        <a href="/admin/track-edit.php?id=<?= (int)$track['id'] ?>" class="btn btn--sm btn--outline">✏️</a>
                                        <form method="POST" onsubmit="return confirm('Удалить трек «<?= h(addslashes($track['title'])) ?>»?')">
                                            <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="track_id" value="<?= (int)$track['id'] ?>">
                                            <button type="submit" class="btn btn--sm btn--danger">🗑️</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
```

---

## ЭТАП 7 — Файл 10: `admin/track-add.php`

```php
<?php
/**
 * Добавление нового трека
 * Путь: /admin/track-add.php
 */

declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/includes/auth.php';
require_auth();

$page_title = 'Добавить трек';
$csrf_token = generate_csrf_token();
$errors     = [];
$message    = '';

try {
    $db         = Database::getInstance();
    $categories = $db->fetchAll("SELECT * FROM track_categories ORDER BY sort_order ASC");
} catch (Exception $e) {
    $categories = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Ошибка CSRF';
    } else {

        $title       = sanitize_string($_POST['title']       ?? '', 200);
        $description = sanitize_text($_POST['description']   ?? '', 1000);
        $category_id = (int)($_POST['category_id'] ?? 0);
        $style       = sanitize_string($_POST['style']       ?? '', 100);
        $mood        = sanitize_string($_POST['mood']        ?? '', 50);
        $voice_type  = sanitize_string($_POST['voice_type']  ?? '', 30);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_active   = isset($_POST['is_active'])   ? 1 : 0;
        $lyrics      = sanitize_text($_POST['lyrics']        ?? '', 10000);
        $sort_order  = (int)($_POST['sort_order'] ?? 0);

        if (mb_strlen($title, 'UTF-8') < 2) {
            $errors[] = 'Введите название трека';
        }

        // ─── Загрузка аудио ───
        $audio_filename = '';
        if (!empty($_FILES['audio_file']['name'])) {
            $audio_upload = upload_audio_file($_FILES['audio_file']);
            if ($audio_upload['success']) {
                $audio_filename = $audio_upload['filename'];
            } else {
                $errors[] = $audio_upload['error'];
            }
        } else {
            $errors[] = 'Загрузите аудио файл (MP3)';
        }

        // ─── Загрузка обложки ───
        $cover_filename = '';
        if (!empty($_FILES['cover_image']['name'])) {
            $cover_upload = upload_image_file($_FILES['cover_image']);
            if ($cover_upload['success']) {
                $cover_filename = $cover_upload['filename'];
            } else {
                $errors[] = $cover_upload['error'];
            }
        }

        // ─── Длительность из аудио ───
        $duration = 0;
        if ($audio_filename) {
            $duration = get_audio_duration(UPLOAD_PATH . '/tracks/' . $audio_filename);
        }

        if (empty($errors)) {
            try {
                $db->execute(
                    "INSERT INTO tracks (title, description, category_id, audio_file, cover_image, duration, style, mood, voice_type, is_featured, is_active, lyrics, sort_order, created_at)
                     VALUES (:title, :desc, :cat_id, :audio, :cover, :dur, :style, :mood, :voice, :featured, :active, :lyrics, :sort, NOW())",
                    [
                        ':title'    => $title,
                        ':desc'     => $description,
                        ':cat_id'   => $category_id ?: null,
                        ':audio'    => $audio_filename,
                        ':cover'    => $cover_filename,
                        ':dur'      => $duration,
                        ':style'    => $style,
                        ':mood'     => $mood,
                        ':voice'    => $voice_type,
                        ':featured' => $is_featured,
                        ':active'   => $is_active,
                        ':lyrics'   => $lyrics,
                        ':sort'     => $sort_order,
                    ]
                );
                header('Location: /admin/tracks.php?added=1');
                exit;
            } catch (Exception $e) {
                log_error('admin/track-add: ' . $e->getMessage());
                $errors[] = 'Ошибка при сохранении';
            }
        }
    }
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<?php if (!empty($errors)): ?>
    <div class="alert alert--error">
        <?php foreach ($errors as $err): ?>
            <div>❌ <?= h($err) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="admin-breadcrumb">
    <a href="/admin/tracks.php">Треки</a> › Добавить
</div>

<form method="POST" action="/admin/track-add.php" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">

    <div class="track-form-grid">

        <!-- Основные данные -->
        <div class="admin-card">
            <div class="admin-card__header"><h2 class="admin-card__title">Основное</h2></div>
            <div class="admin-card__body">

                <div class="form-group">
                    <label class="form-label" for="title">Название <span class="required">*</span></label>
                    <input type="text" id="title" name="title" class="form-input"
                           value="<?= h($_POST['title'] ?? '') ?>" required maxlength="200" autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Описание</label>
                    <textarea id="description" name="description" class="form-textarea" rows="3" maxlength="1000"><?= h($_POST['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="category_id">Категория</label>
                    <select id="category_id" name="category_id" class="form-input">
                        <option value="">— Выберите —</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>"
                                <?= (int)($_POST['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>>
                                <?= h($cat['icon'] ?? '') ?> <?= h($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:var(--space-sm);">
                    <div class="form-group">
                        <label class="form-label" for="style">Стиль</label>
                        <input type="text" id="style" name="style" class="form-input"
                               value="<?= h($_POST['style'] ?? '') ?>" maxlength="100" placeholder="Поп, Рок…">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="mood">Настроение</label>
                        <input type="text" id="mood" name="mood" class="form-input"
                               value="<?= h($_POST['mood'] ?? '') ?>" maxlength="50" placeholder="Весёлое…">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="voice_type">Голос</label>
                        <input type="text" id="voice_type" name="voice_type" class="form-input"
                               value="<?= h($_POST['voice_type'] ?? '') ?>" maxlength="30" placeholder="Мужской…">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="sort_order">Порядок сортировки</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-input"
                           value="<?= (int)($_POST['sort_order'] ?? 0) ?>" min="0" style="max-width:120px;">
                </div>

                <div style="display:flex;gap:var(--space-lg);">
                    <label class="form-check">
                        <input type="checkbox" name="is_active" class="form-check__input" value="1"
                               <?= !isset($_POST['is_active']) || $_POST['is_active'] ? 'checked' : '' ?>>
                        <span class="form-check__label">Активен (виден на сайте)</span>
                    </label>
                    <label class="form-check">
                        <input type="checkbox" name="is_featured" class="form-check__input" value="1"
                               <?= !empty($_POST['is_featured']) ? 'checked' : '' ?>>
                        <span class="form-check__label">⭐ Показать на главной</span>
                    </label>
                </div>

            </div>
        </div>

        <!-- Файлы -->
        <div class="admin-card">
            <div class="admin-card__header"><h2 class="admin-card__title">Файлы</h2></div>
            <div class="admin-card__body">

                <div class="form-group">
                    <label class="form-label" for="audio_file">
                        Аудио файл <span class="required">*</span>
                    </label>
                    <input type="file" id="audio_file" name="audio_file" class="form-input"
                           accept="audio/mpeg,audio/mp3,.mp3" required>
                    <span class="form-hint">MP3, до 20 МБ. Длительность определится автоматически.</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="cover_image">Обложка</label>
                    <input type="file" id="cover_image" name="cover_image" class="form-input"
                           accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp">
                    <span class="form-hint">JPG/PNG/WebP, до 5 МБ, рекомендуется 800×450</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="lyrics">Текст песни</label>
                    <textarea id="lyrics" name="lyrics" class="form-textarea" rows="8"
                              maxlength="10000" placeholder="Текст для отображения…"><?= h($_POST['lyrics'] ?? '') ?></textarea>
                </div>

            </div>
        </div>

    </div><!-- /.track-form-grid -->

    <div style="display:flex;gap:var(--space-sm);margin-top:var(--space-lg);">
        <button type="submit" class="btn btn--primary btn--lg">💾 Сохранить трек</button>
        <a href="/admin/tracks.php" class="btn btn--outline btn--lg">Отмена</a>
    </div>

</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
```

---

## ЭТАП 7 — Файл 11: `admin/includes/admin-functions.php`

```php
<?php
/**
 * Функции для админки
 * Путь: /admin/includes/admin-functions.php
 */

declare(strict_types=1);

/**
 * Отрисовать бэйдж статуса заявки
 *
 * @param string $status
 * @return string HTML
 */
function render_status_badge(string $status): string
{
    $map = [
        'new'         => ['label' => 'Новая',       'class' => 'status-badge--new'],
        'in_progress' => ['label' => 'В работе',    'class' => 'status-badge--progress'],
        'review'      => ['label' => 'Проверка',    'class' => 'status-badge--review'],
        'done'        => ['label' => 'Выполнена',   'class' => 'status-badge--done'],
        'cancelled'   => ['label' => 'Отменена',    'class' => 'status-badge--cancelled'],
    ];

    $info = $map[$status] ?? ['label' => $status, 'class' => ''];

    return sprintf(
        '<span class="status-badge %s">%s</span>',
        htmlspecialchars($info['class'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($info['label'], ENT_QUOTES, 'UTF-8')
    );
}

/**
 * Загрузить аудио файл
 *
 * @param array $file — $_FILES['field']
 * @return array ['success' => bool, 'filename' => string, 'error' => string]
 */
function upload_audio_file(array $file): array
{
    // Проверка ошибок загрузки
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Ошибка загрузки файла (код ' . $file['error'] . ')'];
    }

    // Размер (max 20 МБ)
    $max_size = 20 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'Файл слишком большой (максимум 20 МБ)'];
    }

    // MIME-тип
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mime     = $finfo->file($file['tmp_name']);
    $allowed  = ['audio/mpeg', 'audio/mp3', 'audio/x-mpeg', 'audio/x-mp3'];

    if (!in_array($mime, $allowed, true)) {
        return ['success' => false, 'error' => 'Допускаются только MP3 файлы'];
    }

    // Генерируем уникальное имя
    $ext      = 'mp3';
    $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $dest     = UPLOAD_PATH . '/tracks/' . $filename;

    if (!is_dir(UPLOAD_PATH . '/tracks')) {
        @mkdir(UPLOAD_PATH . '/tracks', 0750, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['success' => false, 'error' => 'Не удалось сохранить файл'];
    }

    return ['success' => true, 'filename' => $filename, 'error' => ''];
}

/**
 * Загрузить изображение (обложку)
 *
 * @param array $file — $_FILES['field']
 * @return array
 */
function upload_image_file(array $file): array
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Ошибка загрузки изображения'];
    }

    $max_size = 5 * 1024 * 1024; // 5 МБ
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'Изображение слишком большое (максимум 5 МБ)'];
    }

    $finfo   = new finfo(FILEINFO_MIME_TYPE);
    $mime    = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    if (!array_key_exists($mime, $allowed)) {
        return ['success' => false, 'error' => 'Допускаются только JPG, PNG, WebP'];
    }

    $ext      = $allowed[$mime];
    $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $dest     = UPLOAD_PATH . '/covers/' . $filename;

    if (!is_dir(UPLOAD_PATH . '/covers')) {
        @mkdir(UPLOAD_PATH . '/covers', 0750, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['success' => false, 'error' => 'Не удалось сохранить изображение'];
    }

    return ['success' => true, 'filename' => $filename, 'error' => ''];
}

/**
 * Получить длительность MP3 в секундах
 *
 * @param string $filepath
 * @return int
 */
function get_audio_duration(string $filepath): int
{
    if (!file_exists($filepath)) return 0;

    // Пробуем через getID3 если есть
    if (class_exists('getID3')) {
        $id3      = new getID3();
        $info     = $id3->analyze($filepath);
        return (int)($info['playtime_seconds'] ?? 0);
    }

    // Фолбэк: читаем заголовок MP3
    try {
        $fp = fopen($filepath, 'rb');
        if (!$fp) return 0;

        $size = filesize($filepath);
        fseek($fp, 0);
        $header = fread($fp, 4);
        fclose($fp);

        // Простая оценка по размеру файла (128 kbps среднее)
        return (int)($size / (128 * 1024 / 8));
    } catch (Throwable) {
        return 0;
    }
}
```

---

## ЭТАП 7 — Файл 12: `admin/assets/admin.css`

```css
/**
 * Стили панели администратора
 * Путь: /admin/assets/admin.css
 */

/* ═══════════════════════════════════════
   LAYOUT АДМИНКИ
═══════════════════════════════════════ */

.admin-body {
    background: #F0F2F5;
    display: flex;
    min-height: 100vh;
    font-family: var(--font-body);
}

/* ─── Sidebar ─── */
.admin-sidebar {
    width: 240px;
    min-height: 100vh;
    background: var(--color-text);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    z-index: 200;
    transition: transform 0.3s ease;
    overflow-y: auto;
}

.admin-sidebar__logo {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: var(--space-lg) var(--space-md);
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

.admin-sidebar__logo-name {
    font-family: var(--font-heading);
    font-size: 14px;
    font-weight: 700;
    color: #fff;
}

.admin-sidebar__logo-sub {
    font-size: 11px;
    color: rgba(255,255,255,0.5);
    margin-top: 2px;
}

.admin-nav {
    flex: 1;
    padding: var(--space-sm) 0;
}

.admin-nav__item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px var(--space-md);
    color: rgba(255,255,255,0.65);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
    background: none;
    border-top: none;
    border-right: none;
    border-bottom: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
}

.admin-nav__item:hover {
    color: #fff;
    background: rgba(255,255,255,0.06);
    border-left-color: rgba(255,255,255,0.3);
}

.admin-nav__item.active {
    color: #fff;
    background: rgba(139,30,63,0.4);
    border-left-color: var(--color-primary);
}

.admin-nav__icon { font-size: 18px; flex-shrink: 0; width: 24px; text-align: center; }
.admin-nav__label { flex: 1; }

.admin-sidebar__bottom {
    border-top: 1px solid rgba(255,255,255,0.08);
    padding: var(--space-sm) 0;
}

.admin-nav__item--btn {
    color: rgba(255,255,255,0.5);
}

/* ─── Основной контент ─── */
.admin-main {
    margin-left: 240px;
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.admin-topbar {
    height: 60px;
    background: #fff;
    border-bottom: 1px solid #E5E7EB;
    display: flex;
    align-items: center;
    gap: var(--space-md);
    padding-inline: var(--space-lg);
    position: sticky;
    top: 0;
    z-index: 100;
}

.admin-topbar__title {
    font-family: var(--font-heading);
    font-size: 18px;
    font-weight: 700;
    color: var(--color-text);
    flex: 1;
}

.admin-topbar__title::after { display: none; }

.admin-topbar__right {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
}

.admin-topbar__user {
    font-size: 14px;
    color: var(--color-text-muted);
}

.admin-burger {
    display: none;
    flex-direction: column;
    gap: 5px;
    width: 36px;
    height: 36px;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: none;
    border: 1px solid var(--color-border);
    cursor: pointer;
}

.admin-burger span {
    display: block;
    width: 18px;
    height: 2px;
    background: var(--color-text);
    border-radius: 2px;
}

.admin-content {
    flex: 1;
    padding: var(--space-lg);
    max-width: 1400px;
}

/* ═══════════════════════════════════════
   КАРТОЧКИ
═══════════════════════════════════════ */

.admin-card {
    background: #fff;
    border-radius: var(--radius-lg);
    border: 1px solid #E5E7EB;
    margin-bottom: var(--space-md);
    overflow: hidden;
}

.admin-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-md) var(--space-lg);
    border-bottom: 1px solid #F3F4F6;
    gap: var(--space-sm);
    flex-wrap: wrap;
}

.admin-card__title {
    font-family: var(--font-heading);
    font-size: 16px;
    font-weight: 700;
    color: var(--color-text);
}

.admin-card__title::after { display: none; }

.admin-card__count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 22px;
    padding: 0 7px;
    background: var(--color-accent-light);
    color: var(--color-primary);
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 700;
    margin-left: 8px;
}

.admin-card__actions {
    display: flex;
    gap: var(--space-xs);
    flex-wrap: wrap;
}

.admin-card__body {
    padding: var(--space-lg);
}

.admin-card__body--no-pad {
    padding: 0;
}

.admin-card--filters { margin-bottom: var(--space-sm); }

/* ═══════════════════════════════════════
   ДАШБОРД
═══════════════════════════════════════ */

.dash-stats {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: var(--space-md);
    margin-bottom: var(--space-lg);
}

.dash-stat {
    background: #fff;
    border-radius: var(--radius-lg);
    border: 1px solid #E5E7EB;
    padding: var(--space-lg);
    display: flex;
    align-items: center;
    gap: var(--space-md);
    transition: box-shadow 0.2s;
}

.dash-stat:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.dash-stat--primary { border-top: 3px solid var(--color-primary); }
.dash-stat--warning { border-top: 3px solid var(--color-warning); }
.dash-stat--info    { border-top: 3px solid #3B82F6; }
.dash-stat--success { border-top: 3px solid var(--color-success); }

.dash-stat__icon { font-size: 28px; flex-shrink: 0; }

.dash-stat__value {
    font-family: var(--font-heading);
    font-size: 28px;
    font-weight: 800;
    color: var(--color-text);
    line-height: 1;
}

.dash-stat__label {
    font-size: 13px;
    color: var(--color-text-muted);
    font-weight: 500;
    margin-top: 3px;
}

.dash-stat__sub {
    font-size: 12px;
    color: var(--color-text-light);
    margin-top: 2px;
}

.dash-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: var(--space-md);
    margin-bottom: var(--space-md);
}

.admin-card--chart canvas {
    max-height: 220px;
}

.top-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.top-list__item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--space-sm);
    padding: 10px 0;
    border-bottom: 1px solid #F3F4F6;
    font-size: 13px;
}

.top-list__item:last-child { border-bottom: none; }

.top-list__label { color: var(--color-text); font-weight: 500; }

.top-list__count {
    font-family: var(--font-heading);
    font-weight: 700;
    color: var(--color-primary);
    background: var(--color-accent-light);
    padding: 2px 10px;
    border-radius: var(--radius-full);
    font-size: 13px;
}

/* ═══════════════════════════════════════
   ТАБЛИЦЫ
═══════════════════════════════════════ */

.admin-table-wrap {
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    font-size: 13px;
    min-width: 600px;
}

.admin-table th {
    padding: 10px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 12px;
    color: var(--color-text-muted);
    background: #F9FAFB;
    border-bottom: 1px solid #E5E7EB;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.admin-table td {
    padding: 12px 16px;
    border-bottom: 1px solid #F3F4F6;
    color: var(--color-text);
    vertical-align: middle;
}

.admin-table__row:last-child td { border-bottom: none; }
.admin-table__row:hover td { background: #FAFAFA; }

.admin-table__row[data-href] { cursor: pointer; }

.order-number {
    font-family: var(--font-heading);
    font-size: 13px;
    font-weight: 700;
    color: var(--color-primary);
    text-decoration: none;
}

.admin-date { font-size: 12px; color: var(--color-text-muted); line-height: 1.4; }
.admin-phone { color: var(--color-primary); text-decoration: none; font-weight: 600; }

.text-muted { color: var(--color-text-muted); }

/* ═══════════════════════════════════════
   ФИЛЬТРЫ
═══════════════════════════════════════ */

.admin-filters {
    display: flex;
    gap: var(--space-xs);
    flex-wrap: wrap;
    align-items: center;
    padding: var(--space-sm) var(--space-md);
}

.admin-filter__search {
    flex: 1;
    min-width: 200px;
}

.form-select--sm {
    height: 38px;
    padding: 0 12px;
    font-size: 13px;
    min-width: 140px;
}

/* ═══════════════════════════════════════
   ВКЛАДКИ
═══════════════════════════════════════ */

.admin-tabs {
    display: flex;
    gap: 4px;
    margin-bottom: var(--space-sm);
    flex-wrap: wrap;
}

.admin-tab {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 600;
    color: var(--color-text-muted);
    text-decoration: none;
    transition: all 0.2s;
}

.admin-tab:hover { border-color: var(--color-primary); color: var(--color-primary); }

.admin-tab.active {
    background: var(--color-primary);
    border-color: var(--color-primary);
    color: #fff;
}

.admin-tab__count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 18px;
    padding: 0 5px;
    border-radius: var(--radius-full);
    font-size: 11px;
    font-weight: 700;
    background: rgba(0,0,0,0.1);
}

.admin-tab.active .admin-tab__count {
    background: rgba(255,255,255,0.25);
    color: #fff;
}

/* ═══════════════════════════════════════
   ПРОСМОТР ЗАЯВКИ
═══════════════════════════════════════ */

.order-view-grid {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: var(--space-md);
    align-items: start;
}

.order-dl {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.order-dl__row {
    display: grid;
    grid-template-columns: 140px 1fr;
    gap: var(--space-sm);
    font-size: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid #F3F4F6;
}

.order-dl__row:last-child { border-bottom: none; }

.order-dl__row dt {
    font-weight: 600;
    color: var(--color-text-muted);
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    padding-top: 2px;
}

.order-dl__row dd { color: var(--color-text); line-height: 1.5; }

.order-story {
    background: #F9FAFB;
    border-radius: var(--radius-md);
    padding: var(--space-md);
    font-size: 14px;
    line-height: 1.7;
    color: var(--color-text);
    margin-bottom: var(--space-sm);
    border: 1px solid #E5E7EB;
}

.order-story-section {
    margin-top: var(--space-sm);
    padding-top: var(--space-sm);
    border-top: 1px solid #F3F4F6;
    font-size: 13px;
    color: var(--color-text-muted);
}

.order-story-section strong {
    color: var(--color-text);
    display: block;
    margin-bottom: 4px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.order-log { display: flex; flex-direction: column; gap: 10px; }

.order-log__item {
    display: flex;
    gap: var(--space-sm);
    font-size: 13px;
}

.order-log__time {
    font-size: 11px;
    color: var(--color-text-muted);
    white-space: nowrap;
    flex-shrink: 0;
    padding-top: 2px;
}

.order-log__text { color: var(--color-text); line-height: 1.4; }
.order-log__note { color: var(--color-text-muted); font-size: 12px; margin-top: 2px; }

/* ═══════════════════════════════════════
   ТРЕКИ
═══════════════════════════════════════ */

.track-form-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: var(--space-md);
    align-items: start;
}

.toggle-btn {
    font-size: 20px;
    background: none;
    border: none;
    cursor: pointer;
    transition: transform 0.15s;
    padding: 4px;
    border-radius: 6px;
}

.toggle-btn:hover { transform: scale(1.2); background: #F3F4F6; }

.btn--danger {
    background: var(--color-error);
    color: #fff;
    border-color: var(--color-error);
}

.btn--danger:hover {
    background: #901E2E;
    border-color: #901E2E;
    color: #fff;
}

/* ═══════════════════════════════════════
   УТИЛИТЫ АДМИНКИ
═══════════════════════════════════════ */

.admin-breadcrumb {
    font-size: 13px;
    color: var(--color-text-muted);
    margin-bottom: var(--space-md);
}

.admin-breadcrumb a {
    color: var(--color-primary);
    text-decoration: none;
}

.admin-breadcrumb a:hover { text-decoration: underline; }

.admin-empty {
    text-align: center;
    color: var(--color-text-muted);
    font-size: 14px;
    padding: var(--space-xl);
}

.alert {
    padding: var(--space-sm) var(--space-md);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 500;
    margin-bottom: var(--space-md);
    border: 1px solid;
}

.alert--success { background: var(--color-success-light); color: var(--color-success); border-color: var(--color-success); }
.alert--error   { background: var(--color-error-light);   color: var(--color-error);   border-color: var(--color-error);   }
.alert--warning { background: var(--color-warning-light); color: var(--color-warning); border-color: var(--color-warning); }

/* Страница входа */
.admin-login-page {
    background: var(--color-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
}

.login-wrap {
    width: 100%;
    max-width: 420px;
    padding: var(--space-md);
}

.login-card {
    background: #fff;
    border-radius: var(--radius-xl);
    padding: var(--space-xl);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--color-border);
}

.login-card__logo {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    margin-bottom: var(--space-xl);
    justify-content: center;
}

.login-card__title {
    font-family: var(--font-heading);
    font-size: 24px;
    font-weight: 700;
    color: var(--color-text);
    text-align: center;
    margin-bottom: var(--space-lg);
}

.login-card__title::after { display: none; }

.login-card__back {
    margin-top: var(--space-md);
    text-align: center;
}

.password-wrap {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
    color: var(--color-text-muted);
    padding: 4px;
    border-radius: 4px;
}

.password-toggle:hover { color: var(--color-primary); }

/* ═══════════════════════════════════════
   АДАПТИВ АДМИНКИ
═══════════════════════════════════════ */

@media (max-width: 1024px) {
    .admin-sidebar {
        transform: translateX(-100%);
    }

    .admin-sidebar.open {
        transform: translateX(0);
    }

    .admin-main {
        margin-left: 0;
    }

    .admin-burger {
        display: flex;
    }

    .dash-grid {
        grid-template-columns: 1fr;
    }

    .order-view-grid {
        grid-template-columns: 1fr;
    }

    .track-form-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .admin-content { padding: var(--space-sm); }
    .dash-stats { grid-template-columns: repeat(2, 1fr); }
    .admin-card__header { flex-direction: column; align-items: flex-start; }
}
```

---

## ЭТАП 7 — Файл 13: `admin/assets/admin.js`

```javascript
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
```

---

## Дополнение к `includes/functions.php` — функции для админки

```php
/**
 * Добавьте в конец includes/functions.php
 */

// Подключаем функции-хелперы для лейблов из mail.php (если не подключены)
if (!function_exists('get_occasion_label')) {
    require_once __DIR__ . '/mail.php';
}

// Подключаем функции админки
if (defined('IN_ADMIN') && !function_exists('render_status_badge')) {
    require_once __DIR__ . '/../admin/includes/admin-functions.php';
}
```

---

## Добавление таблицы `contact_messages` в `database/schema.sql`

```sql
-- Сообщения из формы контактов
CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100)    NOT NULL DEFAULT '',
    `contact`    VARCHAR(100)    NOT NULL DEFAULT '',
    `message`    TEXT            NOT NULL,
    `ip_address` VARCHAR(45)     NOT NULL DEFAULT '',
    `is_read`    TINYINT(1)      NOT NULL DEFAULT 0,
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_is_read`    (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Константа пути загрузок (добавьте в config.php)
-- define('UPLOAD_PATH', __DIR__ . '/../uploads');
```

---

## ✅ Этапы 6 и 7 завершены

### Итог всех файлов:

| Файл | Назначение |
|------|------------|
| `public/contacts.php` | Контакты, часы работы, форма связи |
| `public/api/contact.php` | API приёма сообщений |
| `admin/includes/auth.php` | Авторизация, сессии |
| `admin/login.php` | Форма входа + защита |
| `admin/logout.php` | Выход |
| `admin/includes/admin-header.php` | Шапка + сайдбар |
| `admin/includes/admin-footer.php` | Подвал + скрипты |
| `admin/includes/admin-functions.php` | Хелперы: upload, badges |
| `admin/index.php` | Дашборд с графиком |
| `admin/orders.php` | Список заявок + фильтры |
| `admin/order-view.php` | Просмотр + смена статуса |
| `admin/tracks.php` | Управление треками |
| `admin/track-add.php` | Добавление трека |
| `admin/assets/admin.css` | Все стили панели |
| `admin/assets/admin.js` | JS для панели |

