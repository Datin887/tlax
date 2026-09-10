<?php
/**
 * Страница заказа — интерактивный AI-чат «Музыкальный продюсер»
 * - Голосовой ввод (Web Speech API, ru-RU) и текстовый ввод
 * - Диалог собирает бриф и закрывает на заявку
 *
 * Путь: /order.php
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';

// ─── Предзаполнение из URL-параметров (например, /order.php?occasion=wedding) ───
$preset_occasion = preg_replace('/[^a-z0-9_-]/', '', $_GET['occasion'] ?? '');

// ─── SEO ───
$page_meta = [
    'title'       => 'Заказать песню — чат с продюсером | Хитовая Песня',
    'description' => 'Расскажите о вашем празднике в чате или надиктуйте голосом — и мы создадим уникальную песню. Без длинных анкет, ответ за минуту.',
    'canonical'   => SITE_URL . '/order.php',
    'og_type'     => 'website',
];

// ─── Доп. стили (подключаются в head-meta, поэтому объявляем ДО неё) ───
$extra_css = ['/assets/css/chat.css'];

require_once __DIR__ . '/includes/head-meta.php';
require_once __DIR__ . '/includes/header.php';
?>

<main class="order-chat-page">

    <!-- ═══════════════════════════════════════
         ЗАГОЛОВОК
    ═══════════════════════════════════════ -->
    <section class="page-hero section--primary section--sm">
        <div class="container">
            <div class="page-hero__content">
                <nav class="breadcrumb" aria-label="Хлебные крошки">
                    <span class="breadcrumb__item">
                        <a href="/" class="breadcrumb__link">Главная</a>
                        <span class="breadcrumb__sep" aria-hidden="true">›</span>
                    </span>
                    <span class="breadcrumb__item">
                        <span aria-current="page">Заказать песню</span>
                    </span>
                </nav>
                <h1 class="section-title" style="color:#fff;">Заказать песню</h1>
                <p class="section-subtitle">
                    Расскажите о празднике в чате — это займёт 2 минуты. Можно надиктовать голосом 🎤
                </p>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════
         ЧАТ С ПРОДЮСЕРОМ
    ═══════════════════════════════════════ -->
    <section class="section section--light">
        <div class="container">

            <div class="chat-root">
                <div class="chat" id="producer-chat" role="log" aria-live="polite" aria-label="Чат с музыкальным продюсером">

                    <!-- ─── Шапка чата ─── -->
                    <div class="chat__header">
                        <div class="chat__avatar" aria-hidden="true">🎵</div>
                        <div class="chat__info">
                            <div class="chat__name">Музыкальный продюсер</div>
                            <div class="chat__status">
                                <span class="chat__status-dot" aria-hidden="true"></span>
                                Отвечаю мгновенно
                            </div>
                        </div>
                    </div>

                    <!-- ─── Сообщения ─── -->
                    <div class="chat__messages" id="chat-messages">

                        <!-- Приветствие бота -->
                        <div class="chat__msg-bot">
                            Привет! Я помогу создать идеальную песню для вашего праздника. Какой у вас повод? (Свадьба, день рождения, юбилей, корпоратив). Напишите или надиктуйте голосом 🎤
                        </div>

                        <!-- Быстрые кнопки-подсказки -->
                        <div class="chat__quick-replies" id="chat-quick-replies">
                            <button type="button" class="chat__quick" data-send="Свадьба">🎉 Свадьба</button>
                            <button type="button" class="chat__quick" data-send="День рождения">🎂 День рождения</button>
                            <button type="button" class="chat__quick" data-send="Юбилей">🍷 Юбилей</button>
                            <button type="button" class="chat__quick" data-send="Корпоратив">🏢 Корпоратив</button>
                        </div>

                    </div>

                    <!-- ─── Область ввода ─── -->
                    <div class="chat__input-area">
                        <textarea
                            class="chat__input"
                            id="chat-input"
                            rows="1"
                            maxlength="2000"
                            placeholder="Опишите вашу идею или нажмите микрофон..."
                            aria-label="Сообщение для продюсера"
                        ></textarea>

                        <button type="button" class="chat__mic" id="chat-mic" aria-label="Голосовой ввод" aria-pressed="false" title="Голосовой ввод">
                            🎤
                        </button>

                        <button type="button" class="chat__send" id="chat-send" aria-label="Отправить сообщение" title="Отправить">
                            <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true">
                                <path d="M2 4.5 L3.4 3.2 L5.2 4 L6.9 2.8 L8.7 3.7 L10.4 2.6 L12.2 3.5 L13.9 2.5 L15.7 3.4 L17.4 2.4 L19.2 3.3 L20.9 2.6 L22.4 3.6 L23.2 4.8 L22.8 5.7 L21.5 6.9 L20.5 8 L19.7 9 L20 9.9 L21.6 10.8 L23.3 11.5 L23 13.2 L21.6 13.6 L20.4 14.6 L19.4 15.6 L18.6 16.6 L17.8 17.6 L18 19 L19 20 L20 21 L19.8 22.4 L18.6 22.9 L17.2 23 L15.8 23 L14.4 23.1 L13 23.1 L11.6 23.2 L10.2 23.2 L8.8 23.3 L7.4 23.3 L6 23.3 L4.6 23.3 L3.2 23.3 L2 23.4 Z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Подсказка (микрофон / запись) -->
                    <div class="chat__mic-hint" id="chat-mic-hint"></div>

                </div><!-- /.chat -->
            </div><!-- /.chat-root -->

        </div><!-- /.container -->
    </section>

</main>

<script>
    window.OrderChatConfig = {
        apiUrl: '/api/chat.php',
        presetOccasion: <?= $preset_occasion === '' ? "''" : "'" . $preset_occasion . "'" ?>,
        fallbackReply: 'Отлично! Расскажите подробнее о главном герое праздника.'
    };
</script>

<?php
$extra_js  = ['/assets/js/chat.js'];
require_once __DIR__ . '/includes/footer.php';
?>