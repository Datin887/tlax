<?php
/**
 * Страница тарифов и услуг
 * Три пакета + корпоративный + доп. услуги + FAQ по оплате
 *
 * Путь: /public/pricing.php
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';

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
            '5 вариантов треков',
            'Максимальное качество звука',
            'Видео с текстом (lyric video)',
            '5 правок',
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

require_once __DIR__ . '/includes/head-meta.php';
require_once __DIR__ . '/includes/header.php';
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
                    <div class="pricing-card<?= $tariff['featured'] ? ' pricing-card--featured' : '' ?> reveal reveal--delay-<?= $i + 1 ?>" data-tariff-id="<?= h($tariff['id']) ?>">

                        <?php if ($tariff['featured']): ?>
                            <div class="pricing-card__badge pricing-card__badge--popular">
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

<script>
(function() {
    const cards = document.querySelectorAll('.pricing-card');
    const badge = document.querySelector('.pricing-card__badge--popular');
    if (!badge) return;

    cards.forEach(card => {
        card.addEventListener('mouseenter', () => badge.style.opacity = '0');
        card.addEventListener('mouseleave', () => badge.style.opacity = '1');
    });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>