# ЭТАП 4: Тарифы + ЭТАП 5: Анкета заказа

Создаём все файлы подряд без остановок.

---

## ЭТАП 4 — Файл 1: `public/pricing.php`

```php
<?php
/**
 * Страница тарифов и услуг
 * Три пакета + корпоративный + доп. услуги + FAQ по оплате
 *
 * Путь: /public/pricing.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';

$page_meta = [
    'title'       => 'Тарифы и цены на песни — от 2 500 ₽ | Хитовая Песня',
    'description' => 'Три тарифа на создание персональной песни: Базовый от 2 500 ₽, Стандарт 5 000 ₽, Премиум 10 000 ₽. Оплата после результата. Без предоплаты.',
    'keywords'    => 'цена на песню, стоимость именной песни, тарифы студии, сколько стоит песня на заказ',
    'canonical'   => SITE_URL . '/pricing.php',
];

// ─── Тарифы ───
$tariffs = [
    [
        'id'       => 'basic',
        'name'     => 'Базовый',
        'price'    => 2500,
        'featured' => false,
        'timing'   => '3–5 дней',
        'features' => [
            'Индивидуальный текст песни',
            '1 готовый вариант трека',
            'Профессиональная аранжировка',
            'Формат MP3 (320 kbps)',
            '1 бесплатная правка',
        ],
        'not_included' => [
            'WAV-версия',
            'Несколько вариантов',
            'Видео с текстом',
        ],
    ],
    [
        'id'       => 'standard',
        'name'     => 'Стандарт',
        'price'    => 5000,
        'featured' => true,
        'timing'   => '2–4 дня',
        'features' => [
            'Индивидуальный текст песни',
            '2–3 варианта готового трека',
            'Профессиональный мастеринг',
            'Форматы MP3 + WAV',
            '2 бесплатные правки',
            'Приоритетная поддержка',
        ],
        'not_included' => [
            'Видео с текстом',
        ],
    ],
    [
        'id'       => 'premium',
        'name'     => 'Премиум',
        'price'    => 10000,
        'featured' => false,
        'timing'   => '1–3 дня',
        'features' => [
            'Всё из тарифа «Стандарт»',
            '5+ вариантов трека на выбор',
            'Максимальное качество звука',
            'Видео с текстом (lyric video)',
            'Неограниченные правки',
            'Приоритетное обслуживание',
            'Исходники проекта',
        ],
        'not_included' => [],
    ],
];

// ─── Дополнительные услуги ───
$extra_services = [
    ['name' => 'Срочно (24 часа)',             'price' => '+50% к тарифу',  'icon' => '⚡'],
    ['name' => 'Очень срочно (12 часов)',       'price' => '+100% к тарифу', 'icon' => '🚀'],
    ['name' => 'Дополнительный язык',           'price' => '+2 000 ₽',      'icon' => '🌍'],
    ['name' => 'Дополнительный куплет',         'price' => '+1 000 ₽',      'icon' => '🎵'],
    ['name' => 'Видеоклип (lyric video)',       'price' => '+3 000 ₽',      'icon' => '🎬'],
    ['name' => 'Печать текста в рамке',         'price' => '+1 500 ₽',      'icon' => '🖼️'],
    ['name' => 'Детский голос / хор',           'price' => '+1 500 ₽',      'icon' => '👶'],
    ['name' => 'Запись с живыми инструментами', 'price' => 'по запросу',    'icon' => '🎸'],
];

// ─── FAQ по оплате ───
$payment_faq = [
    [
        'q' => 'Как проходит оплата?',
        'a' => 'Сначала мы создаём песню, вы её слушаете. Только после того, как результат вам понравился — вы оплачиваете. Принимаем оплату на карту (Сбербанк, Тинькофф, ВТБ), через СБП, а также наличными при встрече.',
    ],
    [
        'q' => 'Когда именно нужно платить?',
        'a' => 'После прослушивания готовой песни и согласования всех правок. Если песня вам не понравилась — вы ничего не платите. Никакой предоплаты.',
    ],
    [
        'q' => 'Какие способы оплаты доступны?',
        'a' => 'Перевод на карту по номеру телефона (СБП), банковские карты Сбербанк / Тинькофф / ВТБ. Выставляем счёт на юридическое лицо по запросу.',
    ],
    [
        'q' => 'Есть ли рассрочка для корпоративных заказов?',
        'a' => 'Для корпоративных и крупных заказов обсуждаем условия индивидуально. Возможна частичная предоплата после согласования техзадания.',
    ],
    [
        'q' => 'Даёте ли вы гарантию качества?',
        'a' => 'Да. Мы дорабатываем песню бесплатно в рамках вашего тарифа. Если после всех правок результат вас всё равно не устроил — вы ничего не платите.',
    ],
];

require_once __DIR__ . '/../includes/head-meta.php';
require_once __DIR__ . '/../includes/header.php';
?>

<main>

    <!-- ═══════════════════════════════════════
         ЗАГОЛОВОК
    ═══════════════════════════════════════ -->
    <section class="page-hero section--primary section--sm">
        <div class="container">
            <div class="page-hero__content reveal">
                <nav class="breadcrumb" aria-label="Хлебные крошки">
                    <span class="breadcrumb__item">
                        <a href="/" class="breadcrumb__link">Главная</a>
                        <span class="breadcrumb__sep" aria-hidden="true">›</span>
                    </span>
                    <span class="breadcrumb__item">
                        <span aria-current="page">Тарифы</span>
                    </span>
                </nav>
                <h1 class="section-title" style="color:#fff;">Тарифы и услуги</h1>
                <p class="section-subtitle">
                    Выберите подходящий пакет или обсудим индивидуально.
                    Оплата — только после того, как услышите результат.
                </p>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════
         ТАРИФНЫЕ КАРТОЧКИ
    ═══════════════════════════════════════ -->
    <section class="section section--white" id="tariffs">
        <div class="container">

            <div class="section-header reveal">
                <h2 class="section-title">Выберите тариф</h2>
                <p class="section-subtitle">
                    Все тарифы включают уникальный текст и профессиональную запись.
                    Оплата только после одобрения результата.
                </p>
            </div>

            <div class="pricing-grid">
                <?php foreach ($tariffs as $i => $tariff): ?>
                    <div class="pricing-card<?= $tariff['featured'] ? ' pricing-card--featured' : '' ?> reveal reveal--delay-<?= $i + 1 ?>">

                        <?php if ($tariff['featured']): ?>
                            <div class="pricing-card__badge">
                                <span class="badge badge--popular">⭐ Популярный</span>
                            </div>
                        <?php endif; ?>

                        <!-- Название -->
                        <div class="pricing-card__name">
                            <?= h($tariff['name']) ?>
                        </div>

                        <!-- Цена -->
                        <div class="pricing-card__price">
                            <?= number_format($tariff['price'], 0, '.', ' ') ?> ₽
                        </div>

                        <!-- Срок -->
                        <div class="pricing-card__timing">
                            <span>⏱</span>
                            <span>Срок: <?= h($tariff['timing']) ?></span>
                        </div>

                        <!-- Что входит -->
                        <ul class="pricing-card__features" aria-label="Включено в тариф">
                            <?php foreach ($tariff['features'] as $feature): ?>
                                <li class="pricing-card__feature">
                                    <span class="pricing-card__feature-icon" aria-hidden="true">✅</span>
                                    <span><?= h($feature) ?></span>
                                </li>
                            <?php endforeach; ?>

                            <?php foreach ($tariff['not_included'] as $feature): ?>
                                <li class="pricing-card__feature pricing-card__feature--no">
                                    <span class="pricing-card__feature-icon" aria-hidden="true">➖</span>
                                    <span><?= h($feature) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <!-- Кнопка -->
                        <a
                            href="/order.php?tariff=<?= urlencode($tariff['id']) ?>"
                            class="btn <?= $tariff['featured'] ? 'btn--primary' : 'btn--outline' ?> btn--full btn--lg"
                            aria-label="Выбрать тариф <?= h($tariff['name']) ?>"
                        >
                            Выбрать «<?= h($tariff['name']) ?>»
                        </a>

                    </div><!-- /.pricing-card -->
                <?php endforeach; ?>
            </div><!-- /.pricing-grid -->

            <!-- Сноска -->
            <p class="pricing-note reveal">
                💡 Не знаете, какой тариф выбрать?
                <a href="/order.php?tariff=help" class="text-primary">Оставьте заявку</a> —
                поможем определиться бесплатно.
            </p>

        </div>
    </section>


    <!-- ═══════════════════════════════════════
         КОРПОРАТИВНЫЙ ТАРИФ
    ═══════════════════════════════════════ -->
    <section class="section" id="corporate">
        <div class="container">

            <div class="corporate-card reveal">
                <div class="corporate-card__decor" aria-hidden="true">
                    <span>🏢</span>
                    <span>🎵</span>
                    <span>🏆</span>
                </div>

                <div class="corporate-card__content">
                    <div class="corporate-card__badge">
                        <span class="badge badge--primary">Для бизнеса</span>
                    </div>

                    <h2 class="corporate-card__title">Корпоративный тариф</h2>

                    <p class="corporate-card__price">от 15 000 ₽</p>
                    <p class="corporate-card__desc">Обсуждается индивидуально</p>

                    <ul class="corporate-card__features">
                        <li>🎯 Гимны и джинглы компаний</li>
                        <li>🎉 Песни для корпоративов и тимбилдингов</li>
                        <li>📢 Рекламные и имиджевые треки</li>
                        <li>🏅 Награждение сотрудников в музыкальной форме</li>
                        <li>📋 Договор и закрывающие документы</li>
                        <li>♾️ Неограниченные правки</li>
                    </ul>
                </div>

                <div class="corporate-card__action">
                    <a href="/contacts.php" class="btn btn--accent btn--xl">
                        Обсудить проект
                    </a>
                    <p class="corporate-card__action-note">
                        Ответим в течение часа и обсудим детали
                    </p>
                </div>
            </div>

        </div>
    </section>


    <!-- ═══════════════════════════════════════
         ДОПОЛНИТЕЛЬНЫЕ УСЛУГИ
    ═══════════════════════════════════════ -->
    <section class="section section--light" id="extras">
        <div class="container">

            <div class="section-header reveal">
                <h2 class="section-title">Дополнительные услуги</h2>
                <p class="section-subtitle">
                    Добавьте к любому тарифу — указывайте при заказе
                </p>
            </div>

            <div class="extras-grid reveal">
                <?php foreach ($extra_services as $extra): ?>
                    <div class="extra-card">
                        <span class="extra-card__icon" aria-hidden="true">
                            <?= $extra['icon'] ?>
                        </span>
                        <div class="extra-card__info">
                            <span class="extra-card__name"><?= h($extra['name']) ?></span>
                            <span class="extra-card__price"><?= h($extra['price']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>


    <!-- ═══════════════════════════════════════
         FAQ ПО ОПЛАТЕ
    ═══════════════════════════════════════ -->
    <section class="section section--white" id="payment-faq">
        <div class="container">

            <div class="section-header reveal">
                <h2 class="section-title">Вопросы об оплате</h2>
                <p class="section-subtitle">
                    Работаем честно и прозрачно
                </p>
            </div>

            <div class="faq-list reveal">
                <?php foreach ($payment_faq as $i => $faq): ?>
                    <div class="faq-item">
                        <button
                            class="faq-item__btn"
                            aria-expanded="false"
                            aria-controls="pfaq-body-<?= $i ?>"
                        >
                            <span><?= h($faq['q']) ?></span>
                            <span class="faq-item__icon" aria-hidden="true">+</span>
                        </button>
                        <div class="faq-item__body" id="pfaq-body-<?= $i ?>">
                            <p class="faq-item__text"><?= h($faq['a']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>


    <!-- ═══════════════════════════════════════
         CTA
    ═══════════════════════════════════════ -->
    <section class="cta-section">
        <div class="cta-section__decor" aria-hidden="true">
            <div class="cta-section__circle cta-section__circle--1"></div>
            <div class="cta-section__circle cta-section__circle--2"></div>
        </div>
        <div class="container">
            <div class="cta-section__content reveal">
                <h2 class="cta-section__title">Готовы начать?</h2>
                <p class="cta-section__subtitle">
                    Оставьте заявку — свяжемся в течение часа,
                    уточним детали и начнём работу
                </p>
                <div class="cta-section__actions">
                    <a href="/order.php" class="btn btn--accent btn--xl">
                        🚀 Оставить заявку
                    </a>
                    <a href="/portfolio.php" class="btn btn--outline-white btn--xl">
                        Послушать примеры
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
```

---

## Дополнения к CSS для pricing.php

Добавьте в конец `public/assets/css/main.css`:

```css
/* ═══════════════════════════════════════
   ТАРИФЫ — ДОПОЛНЕНИЯ
═══════════════════════════════════════ */

/* Недоступная фича в тарифе */
.pricing-card__feature--no {
    opacity: 0.4;
    text-decoration: line-through;
}

.pricing-note {
    text-align: center;
    margin-top: var(--space-xl);
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
}

/* ═══════════════════════════════════════
   КОРПОРАТИВНАЯ КАРТОЧКА
═══════════════════════════════════════ */

.corporate-card {
    background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%);
    border-radius: var(--radius-2xl);
    padding: var(--space-2xl);
    display: grid;
    grid-template-columns: 1fr auto;
    gap: var(--space-2xl);
    align-items: center;
    position: relative;
    overflow: hidden;
    color: #fff;
    box-shadow: var(--shadow-xl);
}

.corporate-card__decor {
    position: absolute;
    right: var(--space-xl);
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
    gap: var(--space-sm);
    opacity: 0.07;
    font-size: 60px;
    pointer-events: none;
}

.corporate-card__badge {
    margin-bottom: var(--space-sm);
}

.corporate-card__title {
    font-family: var(--font-heading);
    font-size: clamp(24px, 3vw, 36px);
    font-weight: var(--font-weight-bold);
    color: #fff;
    margin-bottom: var(--space-xs);
    line-height: 1.2;
}

.corporate-card__title::after {
    display: none;
}

.corporate-card__price {
    font-family: var(--font-heading);
    font-size: 32px;
    font-weight: var(--font-weight-black);
    color: var(--color-accent);
    margin-bottom: 4px;
}

.corporate-card__desc {
    font-size: var(--font-size-sm);
    color: rgba(255,255,255,0.6);
    margin-bottom: var(--space-lg);
}

.corporate-card__features {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    font-size: var(--font-size-sm);
    color: rgba(255,255,255,0.85);
}

.corporate-card__features li {
    display: flex;
    align-items: center;
    gap: 8px;
}

.corporate-card__action {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-sm);
    flex-shrink: 0;
}

.corporate-card__action-note {
    font-size: var(--font-size-xs);
    color: rgba(255,255,255,0.6);
    text-align: center;
    max-width: 160px;
}

/* Адаптив корпоративной карточки */
@media (max-width: 768px) {
    .corporate-card {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .corporate-card__features {
        grid-template-columns: 1fr;
        text-align: left;
    }

    .corporate-card__decor {
        display: none;
    }
}

/* ═══════════════════════════════════════
   ДОПОЛНИТЕЛЬНЫЕ УСЛУГИ
═══════════════════════════════════════ */

.extras-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--space-sm);
}

.extra-card {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    padding: var(--space-md);
    background: var(--color-bg-white);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    transition: var(--transition-base);
}

.extra-card:hover {
    border-color: var(--color-primary);
    box-shadow: var(--shadow-sm);
    transform: translateX(4px);
}

.extra-card__icon {
    font-size: 28px;
    flex-shrink: 0;
    width: 44px;
    text-align: center;
}

.extra-card__info {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
}

.extra-card__name {
    font-weight: var(--font-weight-semibold);
    color: var(--color-text);
    font-size: var(--font-size-sm);
}

.extra-card__price {
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-bold);
    color: var(--color-primary);
}
```

---

## ЭТАП 5 — Файл 1: `public/order.php`

```php
<?php
/**
 * Страница заказа — многошаговая анкета (6 шагов)
 * Сохраняет прогресс в localStorage, отправляет через AJAX
 *
 * Путь: /public/order.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';

// ─── Предзаполнение из URL-параметров ───
$preset_tariff   = preg_replace('/[^a-z_]/', '', $_GET['tariff']   ?? '');
$preset_category = preg_replace('/[^a-z0-9_-]/', '', $_GET['category'] ?? '');
$preset_occasion = preg_replace('/[^a-z0-9_-]/', '', $_GET['occasion'] ?? '');
$preset_style    = preg_replace('/[^a-zA-Zа-яА-Я0-9 _-]/u', '', $_GET['style'] ?? '');

// ─── CSRF-токен ───
$csrf_token = generate_csrf_token();

// ─── SEO ───
$page_meta = [
    'title'       => 'Заказать песню — анкета | Хитовая Песня',
    'description' => 'Заполните анкету и получите уникальную песню для вашего праздника. Всего 6 шагов. Оплата только после результата.',
    'canonical'   => SITE_URL . '/order.php',
    'og_type'     => 'website',
];

// ─── Данные для анкеты ───
$occasions = [
    ['value' => 'wedding',     'icon' => '💒', 'label' => 'Свадьба'],
    ['value' => 'anniversary', 'icon' => '🎂', 'label' => 'Юбилей'],
    ['value' => 'birthday',    'icon' => '🎉', 'label' => 'День рождения'],
    ['value' => 'love',        'icon' => '💕', 'label' => 'Годовщина'],
    ['value' => 'corporate',   'icon' => '🏢', 'label' => 'Корпоратив'],
    ['value' => 'march8',      'icon' => '🌸', 'label' => '8 Марта'],
    ['value' => 'feb23',       'icon' => '⚔️', 'label' => '23 Февраля'],
    ['value' => 'newyear',     'icon' => '🎄', 'label' => 'Новый год'],
    ['value' => 'proposal',    'icon' => '💍', 'label' => 'Предложение'],
    ['value' => 'birth',       'icon' => '👶', 'label' => 'Рождение ребёнка'],
    ['value' => 'retirement',  'icon' => '🏆', 'label' => 'Выход на пенсию'],
    ['value' => 'other',       'icon' => '✨', 'label' => 'Другое'],
];

$music_styles = [
    'Поп', 'Рок', 'Шансон', 'Бардовская',
    'Ретро (советская эстрада)', 'Народная',
    'Рэп / Хип-хоп', 'Джаз / Блюз',
    'Электронная', 'Кантри', 'На ваш вкус',
];

$moods = [
    ['value' => 'fun',        'label' => 'Весёлое, зажигательное',        'icon' => '🎉'],
    ['value' => 'touching',   'label' => 'Трогательное, лирическое',      'icon' => '🥹'],
    ['value' => 'solemn',     'label' => 'Торжественное, величественное', 'icon' => '👑'],
    ['value' => 'funny',      'label' => 'Шуточное, с юмором',           'icon' => '😄'],
    ['value' => 'romantic',   'label' => 'Романтичное',                   'icon' => '❤️'],
    ['value' => 'nostalgic',  'label' => 'Ностальгическое',               'icon' => '🌅'],
    ['value' => 'any',        'label' => 'На ваш вкус',                   'icon' => '🎵'],
];

$voice_types = [
    ['value' => 'male',     'label' => 'Мужской',       'icon' => '👨'],
    ['value' => 'female',   'label' => 'Женский',       'icon' => '👩'],
    ['value' => 'duet',     'label' => 'Дуэт',          'icon' => '👫'],
    ['value' => 'children', 'label' => 'Детский',       'icon' => '🧒'],
    ['value' => 'any',      'label' => 'На ваш вкус',   'icon' => '🎤'],
];

$durations = [
    ['value' => 'short',    'label' => 'Короткая',   'desc' => '1.5–2 мин'],
    ['value' => 'standard', 'label' => 'Стандартная','desc' => '2.5–3.5 мин'],
    ['value' => 'long',     'label' => 'Длинная',    'desc' => '4+ мин'],
];

$urgencies = [
    ['value' => 'normal', 'label' => 'Не срочно',                  'sub' => '5+ дней',          'extra' => ''],
    ['value' => 'fast',   'label' => 'Быстро',                     'sub' => '3–4 дня',          'extra' => ''],
    ['value' => 'urgent', 'label' => 'Срочно',                     'sub' => '1–2 дня',          'extra' => '+50%'],
    ['value' => 'asap',   'label' => 'Очень срочно',               'sub' => 'сегодня–завтра',  'extra' => '+100%'],
];

$tariffs_order = [
    ['value' => 'basic',    'label' => 'Базовый',   'price' => '2 500 ₽', 'featured' => false],
    ['value' => 'standard', 'label' => 'Стандарт',  'price' => '5 000 ₽', 'featured' => true],
    ['value' => 'premium',  'label' => 'Премиум',   'price' => '10 000 ₽','featured' => false],
    ['value' => 'help',     'label' => 'Помогите выбрать', 'price' => '', 'featured' => false],
];

$contact_times = [
    ['value' => 'any',     'label' => 'В любое время'],
    ['value' => 'morning', 'label' => 'Утро (9–12)'],
    ['value' => 'day',     'label' => 'День (12–18)'],
    ['value' => 'evening', 'label' => 'Вечер (18–22)'],
];

$contact_methods = [
    ['value' => 'phone',    'label' => 'Телефон',        'icon' => '📱'],
    ['value' => 'sms',      'label' => 'SMS',            'icon' => '💬'],
    ['value' => 'telegram', 'label' => 'Telegram',       'icon' => '✈️'],
    ['value' => 'whatsapp', 'label' => 'WhatsApp',       'icon' => '💚'],
    ['value' => 'ok',       'label' => 'Одноклассники',  'icon' => '🟠'],
    ['value' => 'vk',       'label' => 'ВКонтакте',      'icon' => '🔵'],
];

// Передаём пресеты в JS
$js_presets = json_encode([
    'tariff'   => $preset_tariff,
    'category' => $preset_category,
    'occasion' => $preset_occasion,
    'style'    => $preset_style,
]);

require_once __DIR__ . '/../includes/head-meta.php';
require_once __DIR__ . '/../includes/header.php';
?>

<main class="order-page">

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
                    6 простых шагов — и мы начнём создавать вашу уникальную песню
                </p>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════
         ФОРМА-ВИЗАРД
    ═══════════════════════════════════════ -->
    <section class="section section--light" id="order-form-section">
        <div class="container">

            <div class="wizard" id="order-wizard" role="main" aria-label="Анкета заказа">

                <!-- ─── Прогресс-бар ─── -->
                <div class="wizard__progress" role="progressbar" aria-valuemin="1" aria-valuemax="6" aria-valuenow="1" aria-label="Прогресс заполнения анкеты">

                    <?php for ($s = 1; $s <= 6; $s++): ?>
                        <?php
                            $step_labels = [
                                1 => 'Повод',
                                2 => 'Герой',
                                3 => 'История',
                                4 => 'Стиль',
                                5 => 'Тариф',
                                6 => 'Контакты',
                            ];
                        ?>
                        <div
                            class="wizard__step-indicator<?= $s === 1 ? ' active' : '' ?>"
                            data-step="<?= $s ?>"
                            aria-label="Шаг <?= $s ?>: <?= $step_labels[$s] ?>"
                        >
                            <div class="wizard__step-dot"><?= $s ?></div>
                            <div class="wizard__step-label"><?= $step_labels[$s] ?></div>
                        </div>

                        <?php if ($s < 6): ?>
                            <div class="wizard__step-line<?= $s === 1 ? '' : '' ?>" data-after-step="<?= $s ?>"></div>
                        <?php endif; ?>
                    <?php endfor; ?>

                </div><!-- /.wizard__progress -->


                <!-- ─── Форма ─── -->
                <form
                    id="order-form"
                    novalidate
                    data-csrf="<?= h($csrf_token) ?>"
                    data-submit-url="/api/submit-order.php"
                >

                    <!-- ══════════════════════════
                         ШАГ 1 — ПОВОД
                    ══════════════════════════ -->
                    <div class="wizard__panel active" data-panel="1">

                        <div class="wizard__header">
                            <p class="wizard__step-num">Шаг 1 из 6</p>
                            <h2 class="wizard__step-title">По какому поводу?</h2>
                        </div>

                        <div class="wizard__body">

                            <!-- Повод -->
                            <fieldset class="form-group">
                                <legend class="form-label">
                                    Выберите повод <span class="required" aria-hidden="true">*</span>
                                </legend>
                                <div class="radio-cards-grid" role="group" aria-label="Повод для песни">
                                    <?php foreach ($occasions as $occ): ?>
                                        <label class="radio-card" for="occasion-<?= h($occ['value']) ?>">
                                            <input
                                                type="radio"
                                                class="radio-card__input"
                                                id="occasion-<?= h($occ['value']) ?>"
                                                name="occasion"
                                                value="<?= h($occ['value']) ?>"
                                                required
                                            >
                                            <span class="radio-card__icon" aria-hidden="true"><?= $occ['icon'] ?></span>
                                            <span class="radio-card__label"><?= h($occ['label']) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <div class="form-error" id="error-occasion" hidden aria-live="polite"></div>

                                <!-- Поле "Другое" -->
                                <div id="occasion-other-wrap" class="form-group" hidden style="margin-top: var(--space-sm);">
                                    <label class="form-label" for="occasion_other">Опишите повод</label>
                                    <input
                                        type="text"
                                        id="occasion_other"
                                        name="occasion_other"
                                        class="form-input"
                                        placeholder="Например: выпускной, день учителя…"
                                        maxlength="100"
                                    >
                                </div>
                            </fieldset>

                            <!-- Дата мероприятия -->
                            <div class="form-group" style="margin-top: var(--space-md);">
                                <label class="form-label" for="event_date">
                                    Дата мероприятия
                                    <span class="form-hint">(если известна)</span>
                                </label>
                                <input
                                    type="date"
                                    id="event_date"
                                    name="event_date"
                                    class="form-input form-input--date"
                                    min="<?= date('Y-m-d') ?>"
                                    max="<?= date('Y-m-d', strtotime('+2 years')) ?>"
                                >
                                <span class="form-hint">Укажите, чтобы мы успели к нужному дню</span>
                            </div>

                            <!-- Срочность -->
                            <div class="form-group" style="margin-top: var(--space-md);">
                                <span class="form-label">Срочность</span>
                                <div class="urgency-grid" role="group" aria-label="Срочность заказа">
                                    <?php foreach ($urgencies as $u): ?>
                                        <label class="urgency-card" for="urgency-<?= h($u['value']) ?>">
                                            <input
                                                type="radio"
                                                id="urgency-<?= h($u['value']) ?>"
                                                name="urgency"
                                                value="<?= h($u['value']) ?>"
                                                class="urgency-card__input"
                                                <?= $u['value'] === 'normal' ? 'checked' : '' ?>
                                            >
                                            <div class="urgency-card__content">
                                                <span class="urgency-card__label"><?= h($u['label']) ?></span>
                                                <span class="urgency-card__sub"><?= h($u['sub']) ?></span>
                                                <?php if ($u['extra']): ?>
                                                    <span class="urgency-card__extra"><?= h($u['extra']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                        </div><!-- /.wizard__body -->

                        <div class="wizard__footer">
                            <div></div><!-- пустышка для выравнивания -->
                            <button type="button" class="btn btn--primary btn--lg wizard-next" data-step="1">
                                Далее →
                            </button>
                        </div>

                    </div><!-- /.wizard__panel[1] -->


                    <!-- ══════════════════════════
                         ШАГ 2 — ГЕРОЙ
                    ══════════════════════════ -->
                    <div class="wizard__panel" data-panel="2">

                        <div class="wizard__header">
                            <p class="wizard__step-num">Шаг 2 из 6</p>
                            <h2 class="wizard__step-title">Для кого песня?</h2>
                        </div>

                        <div class="wizard__body">

                            <!-- Имя -->
                            <div class="form-group">
                                <label class="form-label" for="hero_name">
                                    Имя героя (или имена)
                                    <span class="required" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="hero_name"
                                    name="hero_name"
                                    class="form-input"
                                    placeholder="Например: Мария, Александр и Елена"
                                    maxlength="100"
                                    required
                                >
                                <div class="form-error" id="error-hero_name" hidden aria-live="polite"></div>
                            </div>

                            <!-- Возраст -->
                            <div class="form-group">
                                <label class="form-label" for="hero_age">Возраст</label>
                                <input
                                    type="number"
                                    id="hero_age"
                                    name="hero_age"
                                    class="form-input"
                                    placeholder="Например: 50"
                                    min="1"
                                    max="120"
                                    style="max-width: 160px;"
                                >
                                <span class="form-hint">Для юбилея — обязательно</span>
                            </div>

                            <!-- Кем приходится -->
                            <div class="form-group">
                                <label class="form-label" for="hero_relation">Кем приходится вам?</label>
                                <input
                                    type="text"
                                    id="hero_relation"
                                    name="hero_relation"
                                    class="form-input"
                                    placeholder="Например: жена, мама, лучший друг, коллега"
                                    maxlength="100"
                                    list="relations-list"
                                >
                                <datalist id="relations-list">
                                    <option value="Жена">
                                    <option value="Муж">
                                    <option value="Мама">
                                    <option value="Папа">
                                    <option value="Бабушка">
                                    <option value="Дедушка">
                                    <option value="Дочь">
                                    <option value="Сын">
                                    <option value="Сестра">
                                    <option value="Брат">
                                    <option value="Подруга">
                                    <option value="Друг">
                                    <option value="Коллега">
                                    <option value="Начальник">
                                    <option value="Учитель">
                                </datalist>
                            </div>

                            <!-- Профессия -->
                            <div class="form-group">
                                <label class="form-label" for="hero_profession">
                                    Профессия / деятельность
                                </label>
                                <input
                                    type="text"
                                    id="hero_profession"
                                    name="hero_profession"
                                    class="form-input"
                                    placeholder="Например: врач, учитель, предприниматель"
                                    maxlength="100"
                                >
                            </div>

                            <!-- Хобби -->
                            <div class="form-group">
                                <label class="form-label" for="hero_hobbies">
                                    Хобби и увлечения
                                </label>
                                <input
                                    type="text"
                                    id="hero_hobbies"
                                    name="hero_hobbies"
                                    class="form-input"
                                    placeholder="Например: рыбалка, кулинария, путешествия, дача"
                                    maxlength="200"
                                >
                                <span class="form-hint">Перечислите через запятую — добавим в песню</span>
                            </div>

                        </div><!-- /.wizard__body -->

                        <div class="wizard__footer">
                            <button type="button" class="btn btn--outline wizard-prev" data-step="2">
                                ← Назад
                            </button>
                            <button type="button" class="btn btn--primary btn--lg wizard-next" data-step="2">
                                Далее →
                            </button>
                        </div>

                    </div><!-- /.wizard__panel[2] -->


                    <!-- ══════════════════════════
                         ШАГ 3 — ИСТОРИЯ
                    ══════════════════════════ -->
                    <div class="wizard__panel" data-panel="3">

                        <div class="wizard__header">
                            <p class="wizard__step-num">Шаг 3 из 6</p>
                            <h2 class="wizard__step-title">Расскажите историю</h2>
                        </div>

                        <div class="wizard__body">

                            <!-- Основной текст -->
                            <div class="form-group">
                                <label class="form-label" for="story">
                                    Расскажите о герое
                                    <span class="required" aria-hidden="true">*</span>
                                </label>
                                <textarea
                                    id="story"
                                    name="story"
                                    class="form-textarea"
                                    rows="6"
                                    placeholder="Расскажите о герое, ваших отношениях, важных моментах, ярких воспоминаниях. Чем подробнее — тем лучше получится песня!"
                                    minlength="50"
                                    maxlength="3000"
                                    required
                                ></textarea>
                                <div class="form-char-count" id="story-count">0 / 3000</div>
                                <div class="form-error" id="error-story" hidden aria-live="polite"></div>
                                <span class="form-hint">Минимум 50 символов</span>
                            </div>

                            <!-- Что упомянуть -->
                            <div class="form-group">
                                <label class="form-label" for="must_include">
                                    Что обязательно упомянуть?
                                </label>
                                <textarea
                                    id="must_include"
                                    name="must_include"
                                    class="form-textarea"
                                    rows="3"
                                    placeholder="Шутки, прозвища, памятные места, важные даты, смешные истории, особенные слова…"
                                    maxlength="1000"
                                ></textarea>
                                <div class="form-char-count" id="must-include-count">0 / 1000</div>
                            </div>

                            <!-- Чего избегать -->
                            <div class="form-group">
                                <label class="form-label" for="avoid">
                                    Чего избегать?
                                </label>
                                <textarea
                                    id="avoid"
                                    name="avoid"
                                    class="form-textarea"
                                    rows="2"
                                    placeholder="Нежелательные темы, болезненные воспоминания, неудачные шутки…"
                                    maxlength="500"
                                ></textarea>
                            </div>

                        </div><!-- /.wizard__body -->

                        <div class="wizard__footer">
                            <button type="button" class="btn btn--outline wizard-prev" data-step="3">
                                ← Назад
                            </button>
                            <button type="button" class="btn btn--primary btn--lg wizard-next" data-step="3">
                                Далее →
                            </button>
                        </div>

                    </div><!-- /.wizard__panel[3] -->


                    <!-- ══════════════════════════
                         ШАГ 4 — СТИЛЬ
                    ══════════════════════════ -->
                    <div class="wizard__panel" data-panel="4">

                        <div class="wizard__header">
                            <p class="wizard__step-num">Шаг 4 из 6</p>
                            <h2 class="wizard__step-title">Стиль и настроение</h2>
                        </div>

                        <div class="wizard__body">

                            <!-- Настроение -->
                            <fieldset class="form-group">
                                <legend class="form-label">Настроение песни</legend>
                                <div class="mood-grid" role="group" aria-label="Настроение">
                                    <?php foreach ($moods as $mood): ?>
                                        <label class="mood-card" for="mood-<?= h($mood['value']) ?>">
                                            <input
                                                type="radio"
                                                id="mood-<?= h($mood['value']) ?>"
                                                name="mood"
                                                value="<?= h($mood['value']) ?>"
                                                class="mood-card__input"
                                            >
                                            <span class="mood-card__icon" aria-hidden="true"><?= $mood['icon'] ?></span>
                                            <span class="mood-card__label"><?= h($mood['label']) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </fieldset>

                            <!-- Стиль музыки -->
                            <fieldset class="form-group" style="margin-top: var(--space-md);">
                                <legend class="form-label">
                                    Стиль музыки
                                    <span class="form-hint">(можно несколько)</span>
                                </legend>
                                <div class="style-grid" role="group" aria-label="Музыкальный стиль">
                                    <?php foreach ($music_styles as $style): ?>
                                        <label class="style-check" for="style-<?= h(transliterate($style)) ?>">
                                            <input
                                                type="checkbox"
                                                id="style-<?= h(transliterate($style)) ?>"
                                                name="music_styles[]"
                                                value="<?= h($style) ?>"
                                                class="style-check__input"
                                            >
                                            <span class="style-check__label"><?= h($style) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </fieldset>

                            <!-- Голос -->
                            <fieldset class="form-group" style="margin-top: var(--space-md);">
                                <legend class="form-label">Голос исполнителя</legend>
                                <div class="voice-grid" role="group" aria-label="Тип голоса">
                                    <?php foreach ($voice_types as $vt): ?>
                                        <label class="voice-card" for="voice-<?= h($vt['value']) ?>">
                                            <input
                                                type="radio"
                                                id="voice-<?= h($vt['value']) ?>"
                                                name="voice_type"
                                                value="<?= h($vt['value']) ?>"
                                                class="voice-card__input"
                                            >
                                            <span class="voice-card__icon" aria-hidden="true"><?= $vt['icon'] ?></span>
                                            <span class="voice-card__label"><?= h($vt['label']) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </fieldset>

                            <!-- Длительность -->
                            <fieldset class="form-group" style="margin-top: var(--space-md);">
                                <legend class="form-label">Длительность</legend>
                                <div class="duration-grid" role="group" aria-label="Длительность">
                                    <?php foreach ($durations as $dur): ?>
                                        <label class="duration-card" for="duration-<?= h($dur['value']) ?>">
                                            <input
                                                type="radio"
                                                id="duration-<?= h($dur['value']) ?>"
                                                name="duration"
                                                value="<?= h($dur['value']) ?>"
                                                class="duration-card__input"
                                                <?= $dur['value'] === 'standard' ? 'checked' : '' ?>
                                            >
                                            <span class="duration-card__label"><?= h($dur['label']) ?></span>
                                            <span class="duration-card__desc"><?= h($dur['desc']) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </fieldset>

                        </div><!-- /.wizard__body -->

                        <div class="wizard__footer">
                            <button type="button" class="btn btn--outline wizard-prev" data-step="4">
                                ← Назад
                            </button>
                            <button type="button" class="btn btn--primary btn--lg wizard-next" data-step="4">
                                Далее →
                            </button>
                        </div>

                    </div><!-- /.wizard__panel[4] -->


                    <!-- ══════════════════════════
                         ШАГ 5 — ТАРИФ
                    ══════════════════════════ -->
                    <div class="wizard__panel" data-panel="5">

                        <div class="wizard__header">
                            <p class="wizard__step-num">Шаг 5 из 6</p>
                            <h2 class="wizard__step-title">Выберите тариф</h2>
                        </div>

                        <div class="wizard__body">

                            <div class="tariff-cards-grid" role="group" aria-label="Выбор тарифа">
                                <?php foreach ($tariffs_order as $t): ?>
                                    <label
                                        class="tariff-card<?= $t['featured'] ? ' featured' : '' ?>"
                                        for="tariff-<?= h($t['value']) ?>"
                                    >
                                        <input
                                            type="radio"
                                            id="tariff-<?= h($t['value']) ?>"
                                            name="tariff"
                                            value="<?= h($t['value']) ?>"
                                            class="visually-hidden"
                                        >

                                        <?php if ($t['featured']): ?>
                                            <div class="tariff-card__popular">
                                                <span class="badge badge--popular">⭐ Популярный</span>
                                            </div>
                                        <?php endif; ?>

                                        <div class="tariff-card__name"><?= h($t['label']) ?></div>

                                        <?php if ($t['price']): ?>
                                            <div class="tariff-card__price"><?= h($t['price']) ?></div>
                                        <?php else: ?>
                                            <div class="tariff-card__price" style="font-size:16px; color: var(--color-text-muted);">
                                                Расскажем и поможем
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($t['value'] === 'basic'): ?>
                                            <ul class="tariff-card__features">
                                                <li class="tariff-card__feature">1 вариант трека</li>
                                                <li class="tariff-card__feature">MP3 320 kbps</li>
                                                <li class="tariff-card__feature">1 правка</li>
                                                <li class="tariff-card__feature">Срок: 3–5 дней</li>
                                            </ul>
                                        <?php elseif ($t['value'] === 'standard'): ?>
                                            <ul class="tariff-card__features">
                                                <li class="tariff-card__feature">2–3 варианта трека</li>
                                                <li class="tariff-card__feature">MP3 + WAV</li>
                                                <li class="tariff-card__feature">2 правки</li>
                                                <li class="tariff-card__feature">Срок: 2–4 дня</li>
                                            </ul>
                                        <?php elseif ($t['value'] === 'premium'): ?>
                                            <ul class="tariff-card__features">
                                                <li class="tariff-card__feature">5+ вариантов трека</li>
                                                <li class="tariff-card__feature">MP3 + WAV + Lyric Video</li>
                                                <li class="tariff-card__feature">Неограниченные правки</li>
                                                <li class="tariff-card__feature">Срок: 1–3 дня</li>
                                            </ul>
                                        <?php endif; ?>

                                    </label>
                                <?php endforeach; ?>
                            </div>

                            <!-- Доп. пожелания -->
                            <div class="form-group" style="margin-top: var(--space-lg);">
                                <label class="form-label" for="extra_wishes">
                                    Дополнительные пожелания
                                </label>
                                <textarea
                                    id="extra_wishes"
                                    name="extra_wishes"
                                    class="form-textarea"
                                    rows="3"
                                    placeholder="Срочный заказ, дополнительный язык, видеоклип, живые инструменты или любые другие пожелания…"
                                    maxlength="1000"
                                ></textarea>
                            </div>

                        </div><!-- /.wizard__body -->

                        <div class="wizard__footer">
                            <button type="button" class="btn btn--outline wizard-prev" data-step="5">
                                ← Назад
                            </button>
                            <button type="button" class="btn btn--primary btn--lg wizard-next" data-step="5">
                                Далее →
                            </button>
                        </div>

                    </div><!-- /.wizard__panel[5] -->


                    <!-- ══════════════════════════
                         ШАГ 6 — КОНТАКТЫ
                    ══════════════════════════ -->
                    <div class="wizard__panel" data-panel="6">

                        <div class="wizard__header">
                            <p class="wizard__step-num">Шаг 6 из 6</p>
                            <h2 class="wizard__step-title">Ваши контакты</h2>
                        </div>

                        <div class="wizard__body">

                            <!-- Имя -->
                            <div class="form-group">
                                <label class="form-label" for="client_name">
                                    Как вас зовут <span class="required" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="client_name"
                                    name="client_name"
                                    class="form-input"
                                    placeholder="Ваше имя"
                                    maxlength="100"
                                    required
                                    autocomplete="given-name"
                                >
                                <div class="form-error" id="error-client_name" hidden aria-live="polite"></div>
                            </div>

                            <!-- Телефон -->
                            <div class="form-group">
                                <label class="form-label" for="client_phone">
                                    Телефон <span class="required" aria-hidden="true">*</span>
                                </label>
                                <div class="form-phone-wrap">
                                    <span class="form-phone-prefix" aria-hidden="true">+7</span>
                                    <input
                                        type="tel"
                                        id="client_phone"
                                        name="client_phone"
                                        class="form-input form-input--phone"
                                        placeholder="(999) 999-99-99"
                                        required
                                        autocomplete="tel"
                                        inputmode="numeric"
                                    >
                                </div>
                                <div class="form-error" id="error-client_phone" hidden aria-live="polite"></div>
                            </div>

                            <!-- Мессенджеры — сетка -->
                            <div class="contact-inputs-grid">

                                <div class="form-group">
                                    <label class="form-label" for="client_telegram">
                                        ✈️ Telegram
                                    </label>
                                    <input
                                        type="text"
                                        id="client_telegram"
                                        name="client_telegram"
                                        class="form-input"
                                        placeholder="@username"
                                        maxlength="100"
                                        autocomplete="off"
                                    >
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="client_whatsapp">
                                        💚 WhatsApp
                                    </label>
                                    <input
                                        type="tel"
                                        id="client_whatsapp"
                                        name="client_whatsapp"
                                        class="form-input"
                                        placeholder="+7 (999) 999-99-99"
                                        maxlength="20"
                                        autocomplete="off"
                                    >
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="client_vk">
                                        🔵 ВКонтакте
                                    </label>
                                    <input
                                        type="url"
                                        id="client_vk"
                                        name="client_vk"
                                        class="form-input"
                                        placeholder="vk.com/id…"
                                        maxlength="150"
                                        autocomplete="off"
                                    >
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="client_ok">
                                        🟠 Одноклассники
                                    </label>
                                    <input
                                        type="url"
                                        id="client_ok"
                                        name="client_ok"
                                        class="form-input"
                                        placeholder="ok.ru/profile/…"
                                        maxlength="150"
                                        autocomplete="off"
                                    >
                                </div>

                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label class="form-label" for="client_email">
                                    ✉️ Email
                                </label>
                                <input
                                    type="email"
                                    id="client_email"
                                    name="client_email"
                                    class="form-input"
                                    placeholder="your@email.ru"
                                    maxlength="150"
                                    autocomplete="email"
                                >
                            </div>

                            <!-- Время для связи -->
                            <div class="form-group">
                                <span class="form-label">Когда удобно связаться?</span>
                                <div class="form-radio-group form-radio-group--row">
                                    <?php foreach ($contact_times as $ct): ?>
                                        <label class="form-radio">
                                            <input
                                                type="radio"
                                                name="contact_time"
                                                value="<?= h($ct['value']) ?>"
                                                class="form-radio__input"
                                                <?= $ct['value'] === 'any' ? 'checked' : '' ?>
                                            >
                                            <span class="form-radio__label"><?= h($ct['label']) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Как связаться первым -->
                            <div class="form-group">
                                <span class="form-label">Как удобнее связаться первым?</span>
                                <div class="form-radio-group form-radio-group--row">
                                    <?php foreach ($contact_methods as $cm): ?>
                                        <label class="form-radio">
                                            <input
                                                type="radio"
                                                name="contact_method"
                                                value="<?= h($cm['value']) ?>"
                                                class="form-radio__input"
                                                <?= $cm['value'] === 'phone' ? 'checked' : '' ?>
                                            >
                                            <span class="form-radio__label">
                                                <?= $cm['icon'] ?> <?= h($cm['label']) ?>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Согласие -->
                            <div class="form-group">
                                <label class="form-check">
                                    <input
                                        type="checkbox"
                                        name="agree_policy"
                                        id="agree_policy"
                                        class="form-check__input"
                                        value="1"
                                        required
                                    >
                                    <span class="form-check__label">
                                        Я соглашаюсь с
                                        <a href="/privacy.php" target="_blank" class="text-primary">
                                            политикой конфиденциальности
                                        </a>
                                        и даю согласие на обработку персональных данных
                                        <span class="required" aria-hidden="true">*</span>
                                    </span>
                                </label>
                                <div class="form-error" id="error-agree_policy" hidden aria-live="polite"></div>
                            </div>

                            <!-- Honeypot (скрытое поле — для ботов) -->
                            <div class="form-group" aria-hidden="true" style="position:absolute; left:-9999px; opacity:0; pointer-events:none;" tabindex="-1">
                                <label for="website">Сайт (не заполняйте)</label>
                                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                            </div>

                            <!-- CSRF -->
                            <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">
                            <!-- Время начала заполнения -->
                            <input type="hidden" name="form_start_time" id="form_start_time" value="">

                        </div><!-- /.wizard__body -->

                        <div class="wizard__footer">
                            <button type="button" class="btn btn--outline wizard-prev" data-step="6">
                                ← Назад
                            </button>
                            <button type="submit" class="btn btn--accent btn--xl" id="submit-btn">
                                🚀 Отправить заявку
                            </button>
                        </div>

                    </div><!-- /.wizard__panel[6] -->

                </form><!-- /#order-form -->

                <!-- Гарантии под формой -->
                <div class="wizard__guarantees">
                    <span>🔒 Данные защищены</span>
                    <span>✅ Без предоплаты</span>
                    <span>⚡ Ответ за 1 час</span>
                    <span>🎵 Оплата после результата</span>
                </div>

            </div><!-- /.wizard -->

        </div><!-- /.container -->
    </section>

</main>

<script>
    window.OrderPresets = <?= $js_presets ?>;
    // Время старта заполнения формы (защита от ботов)
    document.getElementById('form_start_time').value = Date.now();
</script>

<?php
$extra_js = ['/assets/js/form-wizard.js'];
require_once __DIR__ . '/../includes/footer.php';
?>
```

---

## ЭТАП 5 — Файл 2: `public/assets/js/form-wizard.js`

```javascript
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
```

---

## ЭТАП 5 — Файл 3: `public/api/submit-order.php`

```php
<?php
/**
 * API: Приём и сохранение заявки на песню
 * Метод: POST
 * Возвращает JSON
 *
 * Путь: /public/api/submit-order.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../../includes/mail.php';
require_once __DIR__ . '/../../includes/telegram.php';

// ─── Только AJAX POST ───
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['success' => false, 'message' => 'Method not allowed'], 405);
}

if (!is_ajax_request()) {
    send_json(['success' => false, 'message' => 'Forbidden'], 403);
}

// ─── Rate limiting: 5 заявок с одного IP в час ───
$client_ip = get_client_ip();
if (!check_rate_limit('submit_order_' . $client_ip, 5, 3600)) {
    send_json([
        'success' => false,
        'message' => 'Слишком много заявок. Пожалуйста, подождите немного.',
        'errors'  => [],
    ], 429);
}

// ─── Honeypot: поле "website" должно быть пустым ───
if (!empty($_POST['website'])) {
    // Бот — молча возвращаем "успех"
    send_json(['success' => true, 'order_number' => 'HP-00000', 'redirect' => '/thank-you.php?order=HP-00000']);
}

// ─── Защита от слишком быстрой отправки (< 10 секунд) ───
$form_start_time = (int)($_POST['form_start_time'] ?? 0);
if ($form_start_time > 0) {
    $elapsed_ms = (int)(microtime(true) * 1000) - $form_start_time;
    if ($elapsed_ms < 10000) { // < 10 секунд
        log_error("submit-order: подозрение на бота, IP={$client_ip}, время={$elapsed_ms}ms");
        // Не отказываем явно, но логируем
    }
}

// ─── CSRF-валидация ───
$csrf_token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($csrf_token)) {
    send_json(['success' => false, 'message' => 'Недействительный токен безопасности. Обновите страницу.'], 403);
}

// ─── Сбор и санитизация данных ───
$data = [
    // Шаг 1
    'occasion'         => sanitize_string($_POST['occasion']       ?? '', 50),
    'occasion_other'   => sanitize_string($_POST['occasion_other'] ?? '', 100),
    'event_date'       => sanitize_date($_POST['event_date']       ?? ''),
    'urgency'          => sanitize_string($_POST['urgency']        ?? 'normal', 20),

    // Шаг 2
    'hero_name'        => sanitize_string($_POST['hero_name']      ?? '', 100),
    'hero_age'         => (int)($_POST['hero_age'] ?? 0),
    'hero_relation'    => sanitize_string($_POST['hero_relation']  ?? '', 100),
    'hero_profession'  => sanitize_string($_POST['hero_profession']?? '', 100),
    'hero_hobbies'     => sanitize_string($_POST['hero_hobbies']   ?? '', 200),

    // Шаг 3
    'story'            => sanitize_text($_POST['story']            ?? '', 3000),
    'must_include'     => sanitize_text($_POST['must_include']     ?? '', 1000),
    'avoid'            => sanitize_text($_POST['avoid']            ?? '', 500),

    // Шаг 4
    'mood'             => sanitize_string($_POST['mood']           ?? '', 30),
    'music_styles'     => sanitize_array($_POST['music_styles']    ?? [], 11, 50),
    'voice_type'       => sanitize_string($_POST['voice_type']     ?? '', 20),
    'duration'         => sanitize_string($_POST['duration']       ?? 'standard', 20),

    // Шаг 5
    'tariff'           => sanitize_string($_POST['tariff']         ?? '', 20),
    'extra_wishes'     => sanitize_text($_POST['extra_wishes']     ?? '', 1000),

    // Шаг 6
    'client_name'      => sanitize_string($_POST['client_name']    ?? '', 100),
    'client_phone'     => sanitize_phone($_POST['client_phone']    ?? ''),
    'client_telegram'  => sanitize_string($_POST['client_telegram']?? '', 100),
    'client_whatsapp'  => sanitize_phone($_POST['client_whatsapp'] ?? ''),
    'client_vk'        => sanitize_url($_POST['client_vk']         ?? ''),
    'client_ok'        => sanitize_url($_POST['client_ok']         ?? ''),
    'client_email'     => sanitize_email($_POST['client_email']    ?? ''),
    'contact_time'     => sanitize_string($_POST['contact_time']   ?? 'any', 20),
    'contact_method'   => sanitize_string($_POST['contact_method'] ?? 'phone', 20),

    // Системные
    'ip_address'       => $client_ip,
    'user_agent'       => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
];

// ─── Валидация ───
$errors = [];

// Повод
$allowed_occasions = [
    'wedding','anniversary','birthday','love','corporate',
    'march8','feb23','newyear','proposal','birth','retirement','other'
];
if (empty($data['occasion']) || !in_array($data['occasion'], $allowed_occasions, true)) {
    $errors['occasion'] = 'Выберите повод для песни';
}

// Если "другое" — нужен текст
if ($data['occasion'] === 'other' && empty($data['occasion_other'])) {
    $errors['occasion_other'] = 'Опишите повод';
}

// Дата мероприятия (если указана — должна быть в будущем)
if (!empty($data['event_date'])) {
    $event_ts = strtotime($data['event_date']);
    if ($event_ts === false || $event_ts < strtotime('today')) {
        $errors['event_date'] = 'Укажите дату в будущем';
    }
}

// Имя героя
if (mb_strlen($data['hero_name'], 'UTF-8') < 2) {
    $errors['hero_name'] = 'Введите имя героя';
}

// История
if (mb_strlen($data['story'], 'UTF-8') < 50) {
    $errors['story'] = 'Расскажите историю подробнее (минимум 50 символов)';
}

// Имя клиента
if (mb_strlen($data['client_name'], 'UTF-8') < 2) {
    $errors['client_name'] = 'Введите ваше имя';
}

// Телефон
$phone_digits = preg_replace('/\D/', '', $data['client_phone']);
if (strlen($phone_digits) < 11) {
    $errors['client_phone'] = 'Введите корректный номер телефона';
}

// Email (если указан)
if (!empty($data['client_email']) && !filter_var($data['client_email'], FILTER_VALIDATE_EMAIL)) {
    $errors['client_email'] = 'Введите корректный email';
}

// Согласие с политикой
if (empty($_POST['agree_policy'])) {
    $errors['agree_policy'] = 'Необходимо согласие с политикой конфиденциальности';
}

if (!empty($errors)) {
    send_json(['success' => false, 'message' => 'Исправьте ошибки в форме', 'errors' => $errors], 422);
}

// ─── Сохранение в БД ───
try {
    $db = Database::getInstance();

    // Генерируем номер заявки
    $order_number = generate_order_number();

    // Сериализуем массивы
    $music_styles_str = implode(', ', $data['music_styles']);

    $order_id = $db->insert(
        "INSERT INTO orders (
            order_number, occasion, occasion_other, event_date, urgency,
            hero_name, hero_age, hero_relation, hero_profession, hero_hobbies,
            story, must_include, avoid,
            mood, music_styles, voice_type, duration,
            tariff, extra_wishes,
            client_name, client_phone, client_telegram, client_whatsapp,
            client_vk, client_ok, client_email,
            contact_time, contact_method,
            status, ip_address, user_agent, created_at
        ) VALUES (
            :order_number, :occasion, :occasion_other, :event_date, :urgency,
            :hero_name, :hero_age, :hero_relation, :hero_profession, :hero_hobbies,
            :story, :must_include, :avoid,
            :mood, :music_styles, :voice_type, :duration,
            :tariff, :extra_wishes,
            :client_name, :client_phone, :client_telegram, :client_whatsapp,
            :client_vk, :client_ok, :client_email,
            :contact_time, :contact_method,
            'new', :ip_address, :user_agent, NOW()
        )",
        [
            ':order_number'   => $order_number,
            ':occasion'       => $data['occasion'],
            ':occasion_other' => $data['occasion_other'],
            ':event_date'     => $data['event_date'] ?: null,
            ':urgency'        => $data['urgency'],
            ':hero_name'      => $data['hero_name'],
            ':hero_age'       => $data['hero_age'] ?: null,
            ':hero_relation'  => $data['hero_relation'],
            ':hero_profession'=> $data['hero_profession'],
            ':hero_hobbies'   => $data['hero_hobbies'],
            ':story'          => $data['story'],
            ':must_include'   => $data['must_include'],
            ':avoid'          => $data['avoid'],
            ':mood'           => $data['mood'],
            ':music_styles'   => $music_styles_str,
            ':voice_type'     => $data['voice_type'],
            ':duration'       => $data['duration'],
            ':tariff'         => $data['tariff'],
            ':extra_wishes'   => $data['extra_wishes'],
            ':client_name'    => $data['client_name'],
            ':client_phone'   => $data['client_phone'],
            ':client_telegram'=> $data['client_telegram'],
            ':client_whatsapp'=> $data['client_whatsapp'],
            ':client_vk'      => $data['client_vk'],
            ':client_ok'      => $data['client_ok'],
            ':client_email'   => $data['client_email'],
            ':contact_time'   => $data['contact_time'],
            ':contact_method' => $data['contact_method'],
            ':ip_address'     => $data['ip_address'],
            ':user_agent'     => $data['user_agent'],
        ]
    );

    // ─── Логируем создание ───
    $db->execute(
        "INSERT INTO order_logs (order_id, action, new_status, note, created_at)
         VALUES (:order_id, 'created', 'new', 'Заявка создана через сайт', NOW())",
        [':order_id' => $order_id]
    );

    // ─── Уведомления (не блокируем ответ при ошибке) ───
    $order_data = array_merge($data, [
        'id'           => $order_id,
        'order_number' => $order_number,
    ]);

    try {
        send_order_email($order_data);
    } catch (Exception $e) {
        log_error('submit-order: email failed: ' . $e->getMessage());
    }

    try {
        send_order_telegram($order_data);
    } catch (Exception $e) {
        log_error('submit-order: telegram failed: ' . $e->getMessage());
    }

    // ─── Успех ───
    send_json([
        'success'      => true,
        'order_number' => $order_number,
        'redirect'     => '/thank-you.php?order=' . urlencode($order_number),
        'message'      => 'Заявка принята!',
    ]);

} catch (Exception $e) {
    log_error('submit-order: DB error: ' . $e->getMessage());
    send_json([
        'success' => false,
        'message' => 'Произошла ошибка при сохранении заявки. Пожалуйста, попробуйте снова или свяжитесь с нами напрямую.',
    ], 500);
}
```

---

## ЭТАП 5 — Файл 4: `public/thank-you.php`

```php
<?php
/**
 * Страница благодарности после отправки заявки
 *
 * Путь: /public/thank-you.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';

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

require_once __DIR__ . '/../includes/head-meta.php';
require_once __DIR__ . '/../includes/header.php';
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
```

---

## ЭТАП 5 — Файл 5: `includes/mail.php`

```php
<?php
/**
 * Отправка email-уведомлений
 * Использует встроенный mail() или SMTP через сокеты
 *
 * Путь: /includes/mail.php
 */

declare(strict_types=1);

/**
 * Отправить email об новой заявке администратору
 *
 * @param array $order — данные заявки
 * @return bool
 */
function send_order_email(array $order): bool
{
    $to      = ADMIN_EMAIL;
    $subject = sprintf(
        'Новая заявка %s — %s (%s)',
        $order['order_number'],
        get_occasion_label($order['occasion']),
        get_tariff_label($order['tariff'])
    );

    $html = build_order_email_html($order);

    return send_html_email($to, $subject, $html);
}

/**
 * Отправить HTML email
 *
 * @param string $to
 * @param string $subject
 * @param string $html_body
 * @return bool
 */
function send_html_email(string $to, string $subject, string $html_body): bool
{
    $from_name  = SITE_NAME;
    $from_email = 'noreply@' . parse_url(SITE_URL, PHP_URL_HOST);

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: =?UTF-8?B?" . base64_encode($from_name) . "?= <{$from_email}>\r\n";
    $headers .= "Reply-To: {$from_email}\r\n";
    $headers .= "X-Mailer: PHP/" . PHP_VERSION . "\r\n";

    $encoded_subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

    $result = @mail($to, $encoded_subject, $html_body, $headers);

    $log_msg = sprintf(
        '[%s] Email to %s: %s — %s',
        date('Y-m-d H:i:s'),
        $to,
        $subject,
        $result ? 'OK' : 'FAILED'
    );

    log_to_file(LOG_PATH . '/mail.log', $log_msg);

    return $result;
}

/**
 * Сгенерировать HTML-тело письма с заявкой
 *
 * @param array $order
 * @return string
 */
function build_order_email_html(array $order): string
{
    $admin_link = SITE_URL . '/admin/order-view.php?id=' . (int)($order['id'] ?? 0);

    $rows = build_email_rows([
        ['Номер заявки',    $order['order_number']],
        ['Повод',           get_occasion_label($order['occasion']) . (
            $order['occasion'] === 'other' && !empty($order['occasion_other'])
                ? ': ' . $order['occasion_other'] : ''
        )],
        ['Дата мероприятия', $order['event_date'] ?: 'Не указана'],
        ['Срочность',        get_urgency_label($order['urgency'])],
        ['─────────────', '─────────────────────────────'],
        ['Имя героя',        $order['hero_name']],
        ['Возраст',          $order['hero_age'] ? $order['hero_age'] . ' лет' : 'Не указан'],
        ['Кем приходится',   $order['hero_relation'] ?: 'Не указано'],
        ['Профессия',        $order['hero_profession'] ?: 'Не указана'],
        ['Хобби',            $order['hero_hobbies'] ?: 'Не указаны'],
        ['─────────────', '─────────────────────────────'],
        ['История',          nl2br(htmlspecialchars($order['story'], ENT_QUOTES, 'UTF-8'))],
        ['Упомянуть',        $order['must_include'] ?: 'Не указано'],
        ['Избегать',         $order['avoid'] ?: 'Не указано'],
        ['─────────────', '─────────────────────────────'],
        ['Настроение',       $order['mood'] ?: 'Не указано'],
        ['Стиль музыки',     $order['music_styles'] ?: 'Не указан'],
        ['Голос',            $order['voice_type'] ?: 'Не указан'],
        ['Длительность',     get_duration_label($order['duration'])],
        ['─────────────', '─────────────────────────────'],
        ['Тариф',            get_tariff_label($order['tariff'])],
        ['Доп. пожелания',   $order['extra_wishes'] ?: 'Нет'],
        ['─────────────', '─────────────────────────────'],
        ['Имя клиента',      $order['client_name']],
        ['Телефон',          $order['client_phone']],
        ['Telegram',         $order['client_telegram'] ?: 'Не указан'],
        ['WhatsApp',         $order['client_whatsapp'] ?: 'Не указан'],
        ['ВКонтакте',        $order['client_vk'] ?: 'Не указан'],
        ['Одноклассники',    $order['client_ok'] ?: 'Не указан'],
        ['Email',            $order['client_email'] ?: 'Не указан'],
        ['Время для связи',  get_contact_time_label($order['contact_time'])],
        ['Способ связи',     $order['contact_method']],
    ]);

    return <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Новая заявка {$order['order_number']}</title>
</head>
<body style="margin:0;padding:0;background:#F5EFE6;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#F5EFE6;padding:24px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">

  <!-- Шапка -->
  <tr>
    <td style="background:linear-gradient(135deg,#6B1230,#8B1E3F);padding:32px;text-align:center;">
      <h1 style="color:#fff;font-size:24px;margin:0 0 8px;">🎵 Хитовая Песня</h1>
      <p style="color:rgba(255,255,255,0.8);margin:0;font-size:14px;">Новая заявка на создание песни</p>
    </td>
  </tr>

  <!-- Номер заявки -->
  <tr>
    <td style="background:#F0E6D2;padding:16px 32px;text-align:center;">
      <p style="margin:0;font-size:20px;font-weight:bold;color:#8B1E3F;">
        Заявка #{$order['order_number']}
      </p>
    </td>
  </tr>

  <!-- Данные -->
  <tr>
    <td style="padding:24px 32px;">
      <table width="100%" cellpadding="8" cellspacing="0">
        {$rows}
      </table>
    </td>
  </tr>

  <!-- Кнопка -->
  <tr>
    <td style="padding:0 32px 32px;text-align:center;">
      <a href="{$admin_link}"
         style="display:inline-block;background:#8B1E3F;color:#fff;text-decoration:none;
                padding:14px 32px;border-radius:12px;font-size:16px;font-weight:bold;">
        Открыть в админке
      </a>
    </td>
  </tr>

  <!-- Подвал -->
  <tr>
    <td style="background:#2C1810;padding:16px 32px;text-align:center;">
      <p style="color:rgba(255,255,255,0.5);font-size:12px;margin:0;">
        © Хитовая Песня · {$_SERVER['HTTP_HOST']}
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>
</body>
</html>
HTML;
}

/**
 * Построить строки таблицы для письма
 *
 * @param array $rows — массив [label, value]
 * @return string HTML
 */
function build_email_rows(array $rows): string
{
    $html = '';
    foreach ($rows as [$label, $value]) {
        if (str_starts_with((string)$label, '─')) {
            $html .= '<tr><td colspan="2" style="padding:4px 0;"><hr style="border:none;border-top:1px solid #E8D5B7;"></td></tr>';
            continue;
        }
        $html .= sprintf(
            '<tr>
                <td style="width:160px;color:#6B5D54;font-size:13px;vertical-align:top;padding:6px 12px 6px 0;white-space:nowrap;">%s</td>
                <td style="color:#2C1810;font-size:14px;vertical-align:top;padding:6px 0;">%s</td>
             </tr>',
            htmlspecialchars($label, ENT_QUOTES, 'UTF-8'),
            $value // value уже может содержать HTML (nl2br)
        );
    }
    return $html;
}

/* ─── Хелперы лейблов ─── */

function get_occasion_label(string $val): string
{
    return [
        'wedding'     => '💒 Свадьба',
        'anniversary' => '🎂 Юбилей',
        'birthday'    => '🎉 День рождения',
        'love'        => '💕 Годовщина',
        'corporate'   => '🏢 Корпоратив',
        'march8'      => '🌸 8 Марта',
        'feb23'       => '⚔️ 23 Февраля',
        'newyear'     => '🎄 Новый год',
        'proposal'    => '💍 Предложение',
        'birth'       => '👶 Рождение ребёнка',
        'retirement'  => '🏆 Выход на пенсию',
        'other'       => '✨ Другое',
    ][$val] ?? $val;
}

function get_tariff_label(string $val): string
{
    return [
        'basic'    => 'Базовый (2 500 ₽)',
        'standard' => 'Стандарт (5 000 ₽)',
        'premium'  => 'Премиум (10 000 ₽)',
        'help'     => 'Помогите выбрать',
    ][$val] ?? $val;
}

function get_urgency_label(string $val): string
{
    return [
        'normal' => 'Не срочно (5+ дней)',
        'fast'   => 'Быстро (3–4 дня)',
        'urgent' => 'Срочно (1–2 дня, +50%)',
        'asap'   => 'Очень срочно (сегодня–завтра, +100%)',
    ][$val] ?? $val;
}

function get_duration_label(string $val): string
{
    return [
        'short'    => 'Короткая (1.5–2 мин)',
        'standard' => 'Стандартная (2.5–3.5 мин)',
        'long'     => 'Длинная (4+ мин)',
    ][$val] ?? $val;
}

function get_contact_time_label(string $val): string
{
    return [
        'any'     => 'В любое время',
        'morning' => 'Утро (9–12)',
        'day'     => 'День (12–18)',
        'evening' => 'Вечер (18–22)',
    ][$val] ?? $val;
}
```

---

## ЭТАП 5 — Файл 6: `includes/telegram.php`

```php
<?php
/**
 * Отправка уведомлений через Telegram Bot API
 *
 * Путь: /includes/telegram.php
 */

declare(strict_types=1);

/**
 * Отправить уведомление о заявке в Telegram
 *
 * @param array $order
 * @return bool
 */
function send_order_telegram(array $order): bool
{
    if (empty(TELEGRAM_BOT_TOKEN) || empty(TELEGRAM_CHAT_ID)) {
        return false;
    }

    $admin_link = SITE_URL . '/admin/order-view.php?id=' . (int)($order['id'] ?? 0);

    $occasion  = get_occasion_label($order['occasion']);
    if ($order['occasion'] === 'other' && !empty($order['occasion_other'])) {
        $occasion .= ': ' . $order['occasion_other'];
    }

    $tariff   = get_tariff_label($order['tariff']);
    $urgency  = get_urgency_label($order['urgency']);

    // Формируем сообщение в формате MarkdownV2
    $lines = [
        '🎵 *Новая заявка ' . escape_tg($order['order_number']) . '*',
        '',
        '📋 *Повод:* ' . escape_tg($occasion),
        '⚡ *Срочность:* ' . escape_tg($urgency),
        '💼 *Тариф:* ' . escape_tg($tariff),
        '',
        '👤 *Герой:* ' . escape_tg($order['hero_name'])
            . ($order['hero_age'] ? ', ' . (int)$order['hero_age'] . ' лет' : ''),
        '',
        '📞 *Клиент:* ' . escape_tg($order['client_name']),
        '📱 *Телефон:* ' . escape_tg($order['client_phone']),
    ];

    if (!empty($order['client_telegram'])) {
        $lines[] = '✈️ *Telegram:* ' . escape_tg($order['client_telegram']);
    }
    if (!empty($order['client_whatsapp'])) {
        $lines[] = '💚 *WhatsApp:* ' . escape_tg($order['client_whatsapp']);
    }
    if (!empty($order['event_date'])) {
        $lines[] = '📅 *Дата:* ' . escape_tg(date('d.m.Y', strtotime($order['event_date'])));
    }

    $lines[] = '';
    $lines[] = '🔗 [Открыть в админке](' . $admin_link . ')';

    $text = implode("\n", $lines);

    return send_telegram_message($text, [
        'parse_mode'              => 'MarkdownV2',
        'disable_web_page_preview'=> true,
    ]);
}

/**
 * Отправить сообщение через Telegram Bot API
 *
 * @param string $text
 * @param array  $extra — доп. параметры
 * @return bool
 */
function send_telegram_message(string $text, array $extra = []): bool
{
    $url = 'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN . '/sendMessage';

    $payload = array_merge([
        'chat_id' => TELEGRAM_CHAT_ID,
        'text'    => $text,
    ], $extra);

    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

    // Используем cURL если доступен
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $json,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        $err      = curl_errno($ch);
        curl_close($ch);

        if ($err) {
            log_error('telegram: cURL error ' . $err);
            return false;
        }
    } else {
        // Фолбэк: file_get_contents
        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => $json,
                'timeout' => 10,
            ],
        ]);
        $response = @file_get_contents($url, false, $context);
    }

    if (!$response) {
        log_error('telegram: нет ответа от API');
        return false;
    }

    $result = json_decode($response, true);
    $ok     = (bool)($result['ok'] ?? false);

    if (!$ok) {
        log_error('telegram: ошибка API: ' . ($result['description'] ?? 'unknown'));
    }

    $log = sprintf('[%s] Telegram: %s', date('Y-m-d H:i:s'), $ok ? 'OK' : 'FAILED');
    log_to_file(LOG_PATH . '/mail.log', $log);

    return $ok;
}

/**
 * Экранирование спецсимволов для MarkdownV2
 *
 * @param string $text
 * @return string
 */
function escape_tg(string $text): string
{
    $chars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
    foreach ($chars as $char) {
        $text = str_replace($char, '\\' . $char, $text);
    }
    return $text;
}
```

---

## Дополнения к `includes/functions.php`

```php
/**
 * Генерация уникального номера заказа
 * Формат: HP-XXXXX (HP + 5 цифр)
 *
 * @return string
 */
function generate_order_number(): string
{
    static $counter = null;

    if ($counter === null) {
        // Берём текущий счётчик из файла (простой способ без БД)
        $counter_file = LOG_PATH . '/order_counter.txt';
        if (file_exists($counter_file)) {
            $counter = (int)file_get_contents($counter_file);
        } else {
            $counter = 100; // Стартуем с HP-00100
        }
    }

    $counter++;

    // Сохраняем счётчик
    $counter_file = LOG_PATH . '/order_counter.txt';
    @file_put_contents($counter_file, $counter, LOCK_EX);

    return 'HP-' . str_pad((string)$counter, 5, '0', STR_PAD_LEFT);
}

/**
 * Санитизация строки (однострочный ввод)
 *
 * @param string $value
 * @param int    $max_len
 * @return string
 */
function sanitize_string(string $value, int $max_len = 255): string
{
    $value = trim($value);
    $value = strip_tags($value);
    $value = htmlspecialchars_decode($value, ENT_QUOTES);
    $value = mb_substr($value, 0, $max_len, 'UTF-8');
    return $value;
}

/**
 * Санитизация многострочного текста
 *
 * @param string $value
 * @param int    $max_len
 * @return string
 */
function sanitize_text(string $value, int $max_len = 5000): string
{
    $value = trim($value);
    $value = strip_tags($value);
    $value = mb_substr($value, 0, $max_len, 'UTF-8');
    return $value;
}

/**
 * Санитизация телефона
 *
 * @param string $value
 * @return string
 */
function sanitize_phone(string $value): string
{
    // Оставляем только цифры и + в начале
    $value = trim($value);
    $value = preg_replace('/[^\d+]/', '', $value);
    return mb_substr($value, 0, 20);
}

/**
 * Санитизация даты (YYYY-MM-DD)
 *
 * @param string $value
 * @return string
 */
function sanitize_date(string $value): string
{
    $value = trim($value);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        $ts = strtotime($value);
        if ($ts !== false) {
            return date('Y-m-d', $ts);
        }
    }
    return '';
}

/**
 * Санитизация URL
 *
 * @param string $value
 * @return string
 */
function sanitize_url(string $value): string
{
    $value = trim($value);
    $value = filter_var($value, FILTER_SANITIZE_URL);
    if (filter_var($value, FILTER_VALIDATE_URL)) {
        return mb_substr($value, 0, 255);
    }
    // Если не полный URL — проверяем как путь (vk.com/...)
    if (preg_match('/^[a-zA-Z0-9._\-\/]+$/', $value)) {
        return mb_substr($value, 0, 255);
    }
    return '';
}

/**
 * Санитизация email
 *
 * @param string $value
 * @return string
 */
function sanitize_email(string $value): string
{
    $value = trim($value);
    $value = filter_var($value, FILTER_SANITIZE_EMAIL);
    return mb_substr($value, 0, 150);
}

/**
 * Санитизация массива строк
 *
 * @param mixed $value
 * @param int   $max_items
 * @param int   $max_item_len
 * @return array
 */
function sanitize_array($value, int $max_items = 20, int $max_item_len = 100): array
{
    if (!is_array($value)) return [];
    return array_slice(
        array_map(fn($v) => sanitize_string((string)$v, $max_item_len), $value),
        0,
        $max_items
    );
}

/**
 * Транслитерация строки (для slug/id)
 *
 * @param string $str
 * @return string
 */
function transliterate(string $str): string
{
    $map = [
        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo',
        'ж'=>'zh','з'=>'z','и'=>'i','й'=>'j','к'=>'k','л'=>'l','м'=>'m',
        'н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u',
        'ф'=>'f','х'=>'h','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch',
        'ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
        ' '=>'-','('=>'-',')'=>'-','/'=>'-',
    ];
    $str = mb_strtolower($str, 'UTF-8');
    $str = strtr($str, $map);
    $str = preg_replace('/[^a-z0-9-]/', '', $str);
    $str = preg_replace('/-+/', '-', $str);
    return trim($str, '-');
}

/**
 * Запись строки в файл лога
 *
 * @param string $file
 * @param string $message
 */
function log_to_file(string $file, string $message): void
{
    $dir = dirname($file);
    if (!is_dir($dir)) {
        @mkdir($dir, 0750, true);
    }
    @file_put_contents($file, $message . PHP_EOL, FILE_APPEND | LOCK_EX);
}
```

---

## Дополнения к CSS (стили для order.php и thank-you.php)

Добавьте в конец `main.css`:

```css
/* ═══════════════════════════════════════
   СТРАНИЦА ЗАКАЗА
═══════════════════════════════════════ */

.order-page {
    background: var(--color-bg);
}

/* Сетка radio-карточек поводов */
.radio-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: var(--space-xs);
    margin-top: var(--space-xs);
}

/* Сетка срочности */
.urgency-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-xs);
    margin-top: var(--space-xs);
}

.urgency-card {
    display: block;
    padding: var(--space-sm);
    background: var(--color-bg-white);
    border: 2px solid var(--color-border);
    border-radius: var(--radius-lg);
    cursor: pointer;
    transition: var(--transition-base);
    user-select: none;
}

.urgency-card__input {
    display: none;
}

.urgency-card:hover {
    border-color: var(--color-primary);
    background: var(--color-accent-light);
}

.urgency-card.selected {
    border-color: var(--color-primary);
    background: var(--color-accent-light);
}

.urgency-card__content {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.urgency-card__label {
    font-weight: var(--font-weight-semibold);
    font-size: var(--font-size-sm);
    color: var(--color-text);
}

.urgency-card__sub {
    font-size: var(--font-size-xs);
    color: var(--color-text-muted);
}

.urgency-card__extra {
    font-size: var(--font-size-xs);
    font-weight: var(--font-weight-bold);
    color: var(--color-primary);
}

/* Сетка настроений */
.mood-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: var(--space-xs);
    margin-top: var(--space-xs);
}

.mood-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: var(--space-sm);
    background: var(--color-bg-white);
    border: 2px solid var(--color-border);
    border-radius: var(--radius-lg);
    cursor: pointer;
    transition: var(--transition-base);
    user-select: none;
}

.mood-card__input { display: none; }

.mood-card:hover { border-color: var(--color-primary); background: var(--color-accent-light); }
.mood-card.selected { border-color: var(--color-primary); background: var(--color-accent-light); }

.mood-card__icon { font-size: 22px; flex-shrink: 0; }
.mood-card__label { font-size: var(--font-size-sm); font-weight: var(--font-weight-semibold); color: var(--color-text); }

/* Стили музыки (checkboxes) */
.style-grid {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-xs);
    margin-top: var(--space-xs);
}

.style-check {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    background: var(--color-bg-white);
    border: 2px solid var(--color-border);
    border-radius: var(--radius-full);
    cursor: pointer;
    transition: var(--transition-base);
    user-select: none;
}

.style-check__input { display: none; }

.style-check:hover { border-color: var(--color-primary); background: var(--color-accent-light); }
.style-check.selected { border-color: var(--color-primary); background: var(--color-primary); }
.style-check.selected .style-check__label { color: #fff; }

.style-check__label {
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    color: var(--color-text);
    transition: var(--transition-base);
}

/* Голос */
.voice-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: var(--space-xs);
    margin-top: var(--space-xs);
}

.voice-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: var(--space-sm);
    background: var(--color-bg-white);
    border: 2px solid var(--color-border);
    border-radius: var(--radius-lg);
    cursor: pointer;
    transition: var(--transition-base);
    text-align: center;
    user-select: none;
}

.voice-card__input { display: none; }

.voice-card:hover { border-color: var(--color-primary); background: var(--color-accent-light); }
.voice-card.selected { border-color: var(--color-primary); background: var(--color-accent-light); }

.voice-card__icon { font-size: 28px; }
.voice-card__label { font-size: var(--font-size-xs); font-weight: var(--font-weight-semibold); color: var(--color-text); }

/* Длительность */
.duration-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-xs);
    margin-top: var(--space-xs);
}

.duration-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding: var(--space-md) var(--space-sm);
    background: var(--color-bg-white);
    border: 2px solid var(--color-border);
    border-radius: var(--radius-lg);
    cursor: pointer;
    transition: var(--transition-base);
    text-align: center;
    user-select: none;
}

.duration-card__input { display: none; }

.duration-card:hover { border-color: var(--color-primary); background: var(--color-accent-light); }
.duration-card.selected { border-color: var(--color-primary); background: var(--color-accent-light); }

.duration-card__label { font-weight: var(--font-weight-bold); color: var(--color-text); font-size: var(--font-size-sm); }
.duration-card__desc  { font-size: var(--font-size-xs); color: var(--color-text-muted); }

/* Тарифные карточки в визарде */
.tariff-cards-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-sm);
}

/* Мессенджеры в 2 колонки */
.contact-inputs-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-sm);
    margin-bottom: var(--space-sm);
}

/* Гарантии под формой */
.wizard__guarantees {
    display: flex;
    gap: var(--space-md);
    justify-content: center;
    flex-wrap: wrap;
    padding: var(--space-lg) 0;
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
    border-top: 1px solid var(--color-border);
    margin-top: var(--space-md);
}

/* Черновик */
.draft-notice {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    flex-wrap: wrap;
    justify-content: center;
    padding: var(--space-sm) var(--space-md);
    background: var(--color-accent-light);
    border: 1px solid var(--color-accent);
    border-radius: var(--radius-lg);
    margin-bottom: var(--space-lg);
    font-size: var(--font-size-sm);
    color: var(--color-text);
}

/* Анимация встряхивания кнопки при ошибке */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20%       { transform: translateX(-6px); }
    40%       { transform: translateX(6px); }
    60%       { transform: translateX(-4px); }
    80%       { transform: translateX(4px); }
}

.shake {
    animation: shake 0.5s ease;
}

/* ═══════════════════════════════════════
   СТРАНИЦА БЛАГОДАРНОСТИ
═══════════════════════════════════════ */

.thank-you-section {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - var(--header-height));
    background: var(--color-bg);
}

.thank-you-card {
    max-width: 640px;
    width: 100%;
    margin-inline: auto;
    background: var(--color-bg-white);
    border-radius: var(--radius-2xl);
    padding: var(--space-2xl);
    text-align: center;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--color-border);
}

/* SVG-чекмарк */
.thank-you-card__icon {
    margin: 0 auto var(--space-lg);
    width: 80px;
    height: 80px;
}

.checkmark {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    stroke-width: 2;
    stroke: var(--color-success);
    stroke-miterlimit: 10;
}

.checkmark__circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 2;
    stroke: var(--color-success);
    fill: var(--color-success-light);
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}

.checkmark__check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    stroke-width: 3;
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.5s forwards;
}

@keyframes stroke {
    100% { stroke-dashoffset: 0; }
}

.thank-you-card__title {
    font-family: var(--font-heading);
    font-size: clamp(32px, 5vw, 48px);
    font-weight: var(--font-weight-black);
    color: var(--color-primary);
    margin-bottom: var(--space-sm);
}

.thank-you-card__title::after { display: none; }

.thank-you-card__order {
    font-size: var(--font-size-lg);
    color: var(--color-text-muted);
    margin-bottom: var(--space-md);
}

.thank-you-card__number {
    color: var(--color-primary);
    font-family: var(--font-heading);
}

.thank-you-card__message {
    font-size: var(--font-size-lg);
    color: var(--color-text);
    line-height: var(--line-height-relaxed);
    margin-bottom: var(--space-xl);
}

/* Шаги "что дальше" */
.thank-you-card__steps {
    background: var(--color-bg);
    border-radius: var(--radius-lg);
    padding: var(--space-lg);
    margin-bottom: var(--space-xl);
    text-align: left;
}

.thank-you-card__steps-title {
    font-family: var(--font-heading);
    font-size: var(--font-size-base);
    font-weight: var(--font-weight-bold);
    color: var(--color-text);
    text-align: center;
    margin-bottom: var(--space-md);
}

.thank-you-card__steps-title::after { display: none; }

.thank-you-steps {
    display: flex;
    flex-direction: column;
    gap: var(--space-sm);
    counter-reset: steps;
}

.thank-you-steps__item {
    display: flex;
    align-items: flex-start;
    gap: var(--space-sm);
}

.thank-you-steps__icon {
    font-size: 24px;
    flex-shrink: 0;
    width: 36px;
    text-align: center;
}

.thank-you-steps__item div {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.thank-you-steps__item strong {
    font-weight: var(--font-weight-semibold);
    color: var(--color-text);
    font-size: var(--font-size-sm);
}

.thank-you-steps__item span {
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
}

/* Контакты */
.thank-you-card__contacts {
    margin-bottom: var(--space-xl);
}

.thank-you-card__contacts-title {
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
    margin-bottom: var(--space-sm);
}

.thank-you-contacts-grid {
    display: flex;
    gap: var(--space-xs);
    justify-content: center;
    flex-wrap: wrap;
}

.thank-you-contact {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 16px;
    background: var(--color-bg);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-full);
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    color: var(--color-text);
    text-decoration: none;
    transition: var(--transition-base);
}

.thank-you-contact:hover {
    border-color: var(--color-primary);
    color: var(--color-primary);
    background: var(--color-accent-light);
}

.thank-you-card__actions {
    display: flex;
    gap: var(--space-sm);
    justify-content: center;
    flex-wrap: wrap;
}

/* ─── Адаптив ─── */
@media (max-width: 640px) {
    .radio-cards-grid    { grid-template-columns: repeat(3, 1fr); }
    .urgency-grid        { grid-template-columns: 1fr; }
    .voice-grid          { grid-template-columns: repeat(3, 1fr); }
    .duration-grid       { grid-template-columns: 1fr; }
    .tariff-cards-grid   { grid-template-columns: 1fr; }
    .contact-inputs-grid { grid-template-columns: 1fr; }
    .mood-grid           { grid-template-columns: 1fr; }

    .thank-you-card {
        padding: var(--space-lg);
    }

    .thank-you-card__actions {
        flex-direction: column;
    }

    .wizard__guarantees {
        gap: var(--space-sm);
    }
}

@media (max-width: 480px) {
    .radio-cards-grid { grid-template-columns: repeat(2, 1fr); }
}
```

---

## ✅ Этапы 4 и 5 завершены

### Итог всех файлов:

| Файл | Назначение |
|------|------------|
| `public/pricing.php` | Тарифы, корпоратив, доп. услуги, FAQ |
| `public/order.php` | 6-шаговая анкета заказа |
| `public/thank-you.php` | Страница благодарности с анимацией |
| `public/assets/js/form-wizard.js` | Управление шагами, валидация, localStorage, AJAX |
| `public/api/submit-order.php` | Приём заявки, валидация, БД, уведомления |
| `includes/mail.php` | HTML email администратору |
| `includes/telegram.php` | Telegram Bot API уведомление |
| Дополнения `functions.php` | sanitize_*, generate_order_number, transliterate |
| Дополнения `main.css` | Все стили форм, wizard, thank-you |

### Ключевые возможности:
- 🔒 **CSRF** + **honeypot** + **time check** — тройная защита от ботов
- 💾 **localStorage** — черновик сохраняется, можно продолжить позже
- 🎯 **Предзаполнение** из URL (`?tariff=standard&occasion=birthday`)
- ✅ **Валидация** на каждом шаге перед переходом
- 📧 **Email** + **Telegram** уведомления при новой заявке
- 📱 **Полная адаптивность** всех шагов
