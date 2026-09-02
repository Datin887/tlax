<?php
/**
 * Страница благодарности после отправки заявки
 *
 * Путь: /public/thank-you.php
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';

// ─── Номер заказа из URL ───
$order_number = preg_replace('/[^A-Z0-9-]/', '', strtoupper($_GET['order'] ?? ''));
if (empty($order_number)) {
    header('Location: /');
    exit;
}

$page_meta = [
    'title'       => 'Заявка принята! | Хитовая Песня',
    'description' => 'Ваша заявка на создание персональной песни принята. Мы свяжемся с вами в течение часа.',
    'canonical'   => SITE_URL . '/thank-you.php',
];

require_once __DIR__ . '/includes/head-meta.php';
require_once __DIR__ . '/includes/header.php';
?>

<main>

    <section class="section thank-you-section">
        <div class="container">
            <div class="thank-you-card reveal">

                <!-- Анимированная галочка -->
                <div class="thank-you-card__icon" aria-hidden="true">
                    <svg class="checkmark" viewBox="0 0 52 52" aria-hidden="true">
                        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                        <path   class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>

                <h1 class="thank-you-card__title">Спасибо!</h1>

                <div class="thank-you-card__order">
                    Ваша заявка
                    <strong class="thank-you-card__number"><?= h($order_number) ?></strong>
                    успешно принята
                </div>

                <p class="thank-you-card__message">
                    Мы свяжемся с вами в течение <strong>1 часа</strong>
                    и обсудим все детали создания вашей песни.
                    <br>Работаем с 9:00 до 22:00 МСК, без выходных.
                </p>

                <!-- Что дальше -->
                <div class="thank-you-card__steps">
                    <h2 class="thank-you-card__steps-title">Что дальше?</h2>
                    <ol class="thank-you-steps">
                        <li class="thank-you-steps__item">
                            <span class="thank-you-steps__icon" aria-hidden="true">📞</span>
                            <div>
                                <strong>Мы звоним / пишем</strong>
                                <span>Уточняем детали и отвечаем на вопросы</span>
                            </div>
                        </li>
                        <li class="thank-you-steps__item">
                            <span class="thank-you-steps__icon" aria-hidden="true">🎵</span>
                            <div>
                                <strong>Создаём песню</strong>
                                <span>Пишем текст, музыку, делаем запись</span>
                            </div>
                        </li>
                        <li class="thank-you-steps__item">
                            <span class="thank-you-steps__icon" aria-hidden="true">🎁</span>
                            <div>
                                <strong>Вы слушаете</strong>
                                <span>Нравится — оплачиваете и получаете файл</span>
                            </div>
                        </li>
                    </ol>
                </div>

                <!-- Контакты для связи -->
                <div class="thank-you-card__contacts">
                    <p class="thank-you-card__contacts-title">
                        Не хотите ждать? Напишите сами:
                    </p>
                    <div class="thank-you-contacts-grid">
                        <a
                            href="tel:<?= h(preg_replace('/\D/', '', CONTACT_PHONE)) ?>"
                            class="thank-you-contact"
                        >
                            <span aria-hidden="true">📱</span>
                            <span><?= h(CONTACT_PHONE) ?></span>
                        </a>
                        <a
                            href="https://t.me/<?= h(ltrim(TELEGRAM_USERNAME, '@')) ?>"
                            class="thank-you-contact"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <span aria-hidden="true">✈️</span>
                            <span><?= h(TELEGRAM_USERNAME) ?></span>
                        </a>
                        <a
                            href="https://wa.me/<?= h(preg_replace('/\D/', '', WHATSAPP_NUMBER)) ?>"
                            class="thank-you-contact"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <span aria-hidden="true">💚</span>
                            <span>WhatsApp</span>
                        </a>
                    </div>
                </div>

                <!-- Кнопки -->
                <div class="thank-you-card__actions">
                    <a href="/portfolio.php" class="btn btn--primary btn--lg">
                        Послушать наши работы
                    </a>
                    <a href="/" class="btn btn--outline btn--lg">
                        На главную
                    </a>
                </div>

            </div><!-- /.thank-you-card -->
        </div>
    </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>