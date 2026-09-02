<?php
/**
 * Главная страница сайта "Хитовая Песня"
 * Hero, как работает, примеры треков, преимущества, отзывы, CTA, FAQ
 * 
 * Путь: /public/index.php
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';

// ─── Получение треков для главной страницы ───
$featured_tracks = [];
try {
    $db = Database::getInstance();
    $featured_tracks = $db->fetchAll(
        "SELECT t.*, c.name AS category_name, c.slug AS category_slug, c.icon AS category_icon
         FROM tracks t
         LEFT JOIN track_categories c ON t.category_id = c.id
         WHERE t.is_active = 1 AND t.is_featured = 1
         ORDER BY t.sort_order ASC, t.created_at DESC
         LIMIT 6"
    );
} catch (Exception $e) {
    log_error('Ошибка загрузки треков на главной: ' . $e->getMessage());
}

// ─── Получение отзывов ───
$reviews = [];
try {
    $db = Database::getInstance();
    $reviews = $db->fetchAll(
        "SELECT * FROM reviews WHERE is_active = 1 AND is_featured = 1
         ORDER BY sort_order ASC, created_at DESC
         LIMIT 3"
    );
} catch (Exception $e) {
    log_error('Ошибка загрузки отзывов: ' . $e->getMessage());
}

// ─── SEO-мета для главной ───
$page_meta = [
    'title'       => 'Хитовая Песня — Уникальные песни на заказ для праздников',
    'description' => 'Создаём персональные песни для свадеб, юбилеев, дней рождения и корпоративов. Оплата после результата. От 2 500 ₽. Срок от 1 дня.',
    'keywords'    => 'песня на заказ, именная песня, песня на день рождения, песня на свадьбу, создать песню',
    'og_type'     => 'website',
    'canonical'   => SITE_URL . '/',
];

// ─── Данные страницы ───
$faq_items = [
    [
        'q' => 'Сколько времени занимает создание песни?',
        'a' => 'В зависимости от выбранного тарифа: Базовый — 3-5 дней, Стандарт — 2-4 дня, Премиум — 1-3 дня. Есть срочные тарифы — готовы выполнить за 12-24 часа.',
    ],
    [
        'q' => 'Когда нужно платить?',
        'a' => 'Отправляем первые 60 секунд песни на согласование. Если нравится — оплачиваете и получаете полную версию. Никаких предоплат.',
    ],
    [
        'q' => 'Что если результат не понравится?',
        'a' => 'Мы бесплатно дорабатываем песню по вашим пожеланиям. В базовом тарифе — 1 правка, в стандарте — 2 правки, в премиуме — неограниченное количество. Вы платите только за то, что нравится.',
    ],
    [
        'q' => 'В каком качестве я получу песню?',
        'a' => 'Базовый тариф — MP3 320 kbps. Стандарт — MP3 + WAV (студийное качество). Премиум — максимальное качество + lyric video. Также можно заказать профессиональный мастеринг.',
    ],
    [
        'q' => 'Можно ли использовать песню на мероприятии публично?',
        'a' => 'Да! Вы получаете все права на использование вашей персональной песни. Вы можете воспроизводить её на мероприятии, делиться с друзьями, публиковать в социальных сетях.',
    ],
    [
        'q' => 'Какие стили и жанры вы делаете?',
        'a' => 'Поп, рок, шансон, бардовская, ретро (советская эстрада), народная, рэп/хип-хоп, джаз/блюз, электронная, кантри. Мужской, женский или детский вокал, дуэты.',
    ],
    [
        'q' => 'Как с вами связаться?',
        'a' => 'Телефон, WhatsApp, Telegram, ВКонтакте, Одноклассники, email — все контакты на странице "Контакты". Отвечаем с 9:00 до 22:00 МСК, как правило в течение часа.',
    ],
];

$features = [
    ['icon' => '🎯', 'title' => 'Индивидуальный подход', 'desc' => 'Каждая песня создаётся с нуля под ваш повод, вашего героя и вашу историю'],
    ['icon' => '⚡', 'title' => 'Быстрые сроки',          'desc' => 'От 1 дня до готового профессионального трека. Есть срочные заказы'],
    ['icon' => '💰', 'title' => 'Оплата после результата','desc' => 'Слушаете готовую песню — потом решаете. Никаких предоплат и рисков'],
    ['icon' => '🎨', 'title' => 'Любые жанры',            'desc' => 'Поп, рок, шансон, рэп, ретро, народная — создадим в любом стиле'],
    ['icon' => '🔄', 'title' => 'Бесплатные правки',     'desc' => 'Дорабатываем до тех пор, пока песня вам не понравится'],
    ['icon' => '🎧', 'title' => 'Студийное качество',     'desc' => 'Профессиональная запись, аранжировка и мастеринг в каждом треке'],
];

// Статичные отзывы на случай если БД пуста
if (empty($reviews)) {
    $reviews = [
        [
            'author_name'  => 'Елена К.',
            'author_city'  => 'Москва',
            'rating'       => 5,
            'text'         => 'Заказали песню на юбилей мамы. Вся семья была в слезах — настолько точно попали в образ! Мама теперь слушает каждый день. Огромное спасибо!',
            'occasion_tag' => '🎂 Юбилей',
        ],
        [
            'author_name'  => 'Андрей М.',
            'author_city'  => 'Санкт-Петербург',
            'rating'       => 5,
            'text'         => 'Сделали корпоративный гимн для нашей компании. Всё профессионально, быстро, по делу. Команда пришла в восторг на корпоративе!',
            'occasion_tag' => '🏢 Корпоратив',
        ],
        [
            'author_name'  => 'Наталья В.',
            'author_city'  => 'Казань',
            'rating'       => 5,
            'text'         => 'Подарила мужу песню на годовщину — это было незабываемо. Ребята учли все детали нашей истории. Буду заказывать ещё!',
            'occasion_tag' => '💕 Годовщина',
        ],
    ];
}

// ─── Счётчик для анимации ───
$stats = [
    ['value' => 500, 'suffix' => '+', 'label' => 'созданных песен'],
    ['value' => 8,   'suffix' => '',  'label' => 'музыкальных жанров'],
    ['value' => 1,   'suffix' => '',  'label' => 'день — минимальный срок'],
];

require_once __DIR__ . '/includes/head-meta.php';
require_once __DIR__ . '/includes/header.php';
?>

<main>

    <!-- ═══════════════════════════════════════
         HERO
    ═══════════════════════════════════════ -->
    <section class="hero" aria-label="Главный баннер">

        <!-- Декоративные ноты -->
        <div class="hero__decor" aria-hidden="true">
            <span class="hero__note">♩</span>
            <span class="hero__note">♪</span>
            <span class="hero__note">♫</span>
            <span class="hero__note">♬</span>
            <span class="hero__note">🎵</span>
            <span class="hero__note">🎶</span>
            <span class="hero__note">♩</span>
        </div>

        <!-- Декоративные круги -->
        <div class="hero__circle hero__circle--1" aria-hidden="true"></div>
        <div class="hero__circle hero__circle--2" aria-hidden="true"></div>

        <div class="container">
            <div class="hero__content">

                <!-- Прелайн -->
                <div class="hero__pretitle">
                    <span>🎵</span>
                    <span>Студия персональных песен</span>
                </div>

                <!-- H1 -->
                <h1 class="hero__title">
                    <span>Хитовая</span><br>Песня
                </h1>

                <!-- Подзаголовок -->
                <p class="hero__subtitle">
                    Уникальные песни для ваших праздников&nbsp;—
                    свадеб, юбилеев, дней рождения и корпоративов.
                    Оплата только после&nbsp;результата.
                </p>

                <!-- Кнопки CTA -->
                <div class="hero__actions">
                    <a href="/order.php" class="btn btn--accent btn--xl">
                        🎵 Заказать песню
                    </a>
                    <a href="#examples" class="btn btn--outline-white btn--xl">
                        Послушать примеры
                    </a>
                </div>

                <!-- Статистика -->
                <div class="hero__stats">
                    <?php foreach ($stats as $i => $stat): ?>
                        <div class="hero__stat">
                            <span
                                class="hero__stat-value"
                                data-count="<?= (int)$stat['value'] ?>"
                                data-suffix="<?= h($stat['suffix']) ?>"
                            >
                                <?= (int)$stat['value'] . h($stat['suffix']) ?>
                            </span>
                            <span class="hero__stat-label">
                                <?= h($stat['label']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div><!-- /.hero__content -->
        </div><!-- /.container -->

    </section><!-- /.hero -->


    <!-- ═══════════════════════════════════════
         КАК ЭТО РАБОТАЕТ
    ═══════════════════════════════════════ -->
    <section class="section section--white" id="how-it-works">
        <div class="container">

            <div class="section-header reveal">
                <h2 class="section-title">Всего 3 шага до вашей песни</h2>
                <p class="section-subtitle">
                    Расскажите нам вашу историю, а мы превратим её в музыку
                </p>
            </div>

            <div class="steps">

                <div class="step-card reveal reveal--delay-1">
                    <div class="step-card__number" aria-hidden="true">1</div>
                    <div class="step-card__icon" aria-hidden="true">📝</div>
                    <h3 class="step-card__title">Расскажите нам</h3>
                    <p class="step-card__desc">
                        Заполните простую анкету&nbsp;— опишите повод,
                        расскажите о герое и поделитесь пожеланиями
                    </p>
                </div>

                <div class="step-card reveal reveal--delay-2">
                    <div class="step-card__number" aria-hidden="true">2</div>
                    <div class="step-card__icon" aria-hidden="true">🎵</div>
                    <h3 class="step-card__title">Мы создаём</h3>
                    <p class="step-card__desc">
                        Пишем уникальный текст, создаём музыку
                        и делаем профессиональную студийную запись
                    </p>
                </div>

                <div class="step-card reveal reveal--delay-3">
                    <div class="step-card__number" aria-hidden="true">3</div>
                    <div class="step-card__icon" aria-hidden="true">🎁</div>
                    <h3 class="step-card__title">Получаете песню</h3>
                    <p class="step-card__desc">
                        Слушаете результат. Нравится — оплачиваете,
                        скачиваете и дарите незабываемые эмоции
                    </p>
                </div>

            </div><!-- /.steps -->

            <div class="text-center" style="margin-top: var(--space-xl);">
                <a href="/order.php" class="btn btn--primary btn--lg reveal">
                    Заполнить анкету
                </a>
            </div>

        </div><!-- /.container -->
    </section>


    <!-- ═══════════════════════════════════════
         ПРИМЕРЫ РАБОТ
    ═══════════════════════════════════════ -->
    <section class="section" id="examples">
        <div class="container">

            <div class="section-header reveal">
                <h2 class="section-title">Послушайте наши работы</h2>
                <p class="section-subtitle">
                    Каждая песня уникальна, как история, которую она рассказывает
                </p>
            </div>

            <?php if (!empty($featured_tracks)): ?>
                <div class="tracks-grid">
                    <?php foreach ($featured_tracks as $i => $track): ?>
                        <?php
                            $delay_class = $i < 3 ? ' reveal--delay-' . ($i + 1) : '';
                            $cover_class = 'track-card__cover--' . ($track['category_slug'] ?? 'wedding');
                            $audio_url   = !empty($track['audio_file'])
                                ? '/uploads/tracks/' . h($track['audio_file'])
                                : '';
                            $cover_url   = !empty($track['cover_image'])
                                ? '/uploads/covers/' . h($track['cover_image'])
                                : '';
                        ?>
                        <article
                            class="track-card reveal<?= $delay_class ?>"
                            aria-label="Трек: <?= h($track['title']) ?>"
                        >
                            <!-- Обложка -->
                            <div class="track-card__cover <?= $audio_url ? '' : $cover_class ?>">
                                <?php if ($cover_url): ?>
                                    <img
                                        src="<?= h($cover_url) ?>"
                                        alt="Обложка: <?= h($track['title']) ?>"
                                        loading="lazy"
                                        width="400"
                                        height="225"
                                    >
                                <?php else: ?>
                                    <span class="track-card__cover-icon" aria-hidden="true">
                                        <?= h($track['category_icon'] ?? '🎵') ?>
                                    </span>
                                <?php endif; ?>

                                <!-- Оверлей play -->
                                <?php if ($audio_url): ?>
                                    <div class="track-card__play-overlay">
                                        <button
                                            class="track-card__play-btn"
                                            aria-label="Воспроизвести <?= h($track['title']) ?>"
                                        >&#9654;</button>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Контент -->
                            <div class="track-card__body">

                                <!-- Категория -->
                                <?php if (!empty($track['category_name'])): ?>
                                    <span class="track-card__category">
                                        <?= h($track['category_icon'] ?? '') ?>
                                        <?= h($track['category_name']) ?>
                                    </span>
                                <?php endif; ?>

                                <!-- Название -->
                                <h3 class="track-card__title">
                                    <?= h($track['title']) ?>
                                </h3>

                                <!-- Мета -->
                                <div class="track-card__meta">
                                    <?php if (!empty($track['mood'])): ?>
                                        <span class="track-card__meta-item">
                                            🎭 <?= h($track['mood']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Плеер -->
                                <?php if ($audio_url): ?>
                                    <div
                                        data-player
                                        data-track-id="<?= (int)$track['id'] ?>"
                                        data-audio-src="<?= h($audio_url) ?>"
                                    >
                                        <div class="track-player">

                                            <!-- Кнопка play/pause -->
                                            <button
                                                class="track-player__btn"
                                                data-player-play
                                                aria-label="Воспроизвести <?= h($track['title']) ?>"
                                            >&#9654;</button>

                                            <!-- Прогресс -->
                                            <div class="track-player__progress-wrap">
                                                <input
                                                    type="range"
                                                    class="track-player__progress"
                                                    data-player-progress
                                                    min="0"
                                                    max="100"
                                                    value="0"
                                                    step="0.1"
                                                    aria-label="Прогресс воспроизведения"
                                                >
                                                <div class="track-player__time">
                                                    <span data-player-current>0:00</span>
                                                    <span data-player-duration>
                                                        <?= h(format_duration($track['duration'] ?? 0)) ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Громкость -->
                                            <input
                                                type="range"
                                                class="track-player__volume"
                                                data-player-volume
                                                min="0"
                                                max="100"
                                                value="80"
                                                aria-label="Громкость"
                                            >

                                        </div>
                                    </div><!-- /data-player -->
                                <?php else: ?>
                                    <div class="track-player" style="justify-content: center; color: var(--color-text-muted); font-size: 13px;">
                                        🎵 Превью недоступно
                                    </div>
                                <?php endif; ?>

                            </div><!-- /.track-card__body -->
                        </article>
                    <?php endforeach; ?>
                </div><!-- /.tracks-grid -->

            <?php else: ?>
                <!-- Заглушка если треков нет -->
                <div class="placeholder-block reveal">
                    <p>🎵 Треки скоро появятся. <a href="/order.php">Заказать первым!</a></p>
                </div>
            <?php endif; ?>

            <!-- Кнопка "Все примеры" -->
            <div class="tracks-grid__more reveal">
                <a href="/portfolio.php" class="btn btn--outline btn--lg">
                    Все примеры работ →
                </a>
            </div>

        </div><!-- /.container -->
    </section>


    <!-- ═══════════════════════════════════════
         ПРЕИМУЩЕСТВА
    ═══════════════════════════════════════ -->
    <section class="section section--light" id="why-us">
        <div class="container">

            <div class="section-header reveal">
                <h2 class="section-title">Почему выбирают нас</h2>
                <p class="section-subtitle">
                    Более 500 довольных клиентов говорят лучше любой рекламы
                </p>
            </div>

            <div class="features-grid">
                <?php foreach ($features as $i => $feature): ?>
                    <div class="feature-card reveal reveal--delay-<?= ($i % 3) + 1 ?>">
                        <span class="feature-card__icon" aria-hidden="true">
                            <?= $feature['icon'] ?>
                        </span>
                        <h3 class="feature-card__title"><?= h($feature['title']) ?></h3>
                        <p class="feature-card__desc"><?= h($feature['desc']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

        </div><!-- /.container -->
    </section>


    <!-- ═══════════════════════════════════════
         ОТЗЫВЫ
    ═══════════════════════════════════════ -->
    <section class="section section--white" id="reviews">
        <div class="container">

            <div class="section-header reveal">
                <h2 class="section-title">Что говорят наши клиенты</h2>
                <p class="section-subtitle">
                    Реальные истории реальных людей
                </p>
            </div>

            <div class="reviews-grid">
                <?php foreach ($reviews as $i => $review): ?>
                    <div class="review-card reveal reveal--delay-<?= $i + 1 ?>">

                        <div class="review-card__header">
                            <!-- Аватар (первая буква имени) -->
                            <div
                                class="review-card__avatar"
                                aria-hidden="true"
                            >
                                <?= mb_strtoupper(mb_substr($review['author_name'], 0, 1, 'UTF-8'), 'UTF-8') ?>
                            </div>

                            <div class="review-card__author">
                                <div class="review-card__name">
                                    <?= h($review['author_name']) ?>
                                </div>
                                <?php if (!empty($review['author_city'])): ?>
                                    <div class="review-card__city">
                                        📍 <?= h($review['author_city']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Звёзды -->
                        <div
                            class="review-card__stars"
                            aria-label="Оценка: <?= (int)($review['rating'] ?? 5) ?> из 5"
                        >
                            <?= str_repeat('★', (int)($review['rating'] ?? 5)) ?>
                            <?= str_repeat('☆', max(0, 5 - (int)($review['rating'] ?? 5))) ?>
                        </div>

                        <!-- Текст -->
                        <p class="review-card__text">
                            <?= h($review['text']) ?>
                        </p>

                        <!-- Тэг повода -->
                        <?php if (!empty($review['occasion_tag'])): ?>
                            <span class="review-card__tag">
                                <?= h($review['occasion_tag']) ?>
                            </span>
                        <?php endif; ?>

                    </div><!-- /.review-card -->
                <?php endforeach; ?>
            </div><!-- /.reviews-grid -->

        </div><!-- /.container -->
    </section>


    <!-- ═══════════════════════════════════════
         CTA СЕКЦИЯ
    ═══════════════════════════════════════ -->
    <section class="cta-section" id="order-cta" aria-labelledby="cta-title">

        <!-- Декор -->
        <div class="cta-section__decor" aria-hidden="true">
            <div class="cta-section__circle cta-section__circle--1"></div>
            <div class="cta-section__circle cta-section__circle--2"></div>
            <div class="cta-section__circle cta-section__circle--3"></div>
        </div>

        <div class="container">
            <div class="cta-section__content reveal">

                <h2 class="cta-section__title" id="cta-title">
                    Готовы создать свою песню?
                </h2>
                <p class="cta-section__subtitle">
                    Оставьте заявку — расскажем, как всё будет,
                    и уточним все детали
                </p>

                <div class="cta-section__actions">
                    <a href="/order.php" class="btn btn--accent btn--xl">
                        🚀 Оставить заявку
                    </a>
                    <a href="/contacts.php" class="btn btn--outline-white btn--xl">
                        Задать вопрос
                    </a>
                </div>

                <!-- Гарантии -->
                <div style="
                    display: flex;
                    gap: var(--space-lg);
                    justify-content: center;
                    flex-wrap: wrap;
                    margin-top: var(--space-xl);
                    padding-top: var(--space-xl);
                    border-top: 1px solid rgba(255,255,255,0.15);
                ">
                    <span style="color: rgba(255,255,255,0.7); font-size: 14px;">✅ Без предоплаты</span>
                    <span style="color: rgba(255,255,255,0.7); font-size: 14px;">✅ Бесплатные правки</span>
                    <span style="color: rgba(255,255,255,0.7); font-size: 14px;">✅ Ответ за 1 час</span>
                    <span style="color: rgba(255,255,255,0.7); font-size: 14px;">✅ Пн–Вс 9:00–22:00</span>
                </div>

            </div>
        </div>

    </section>


    <!-- ═══════════════════════════════════════
         FAQ
    ═══════════════════════════════════════ -->
    <section class="section section--light" id="faq">
        <div class="container">

            <div class="section-header reveal">
                <h2 class="section-title">Частые вопросы</h2>
                <p class="section-subtitle">
                    Если не нашли ответ — напишите нам, мы всегда на связи
                </p>
            </div>

            <div class="faq-list" role="list">
                <?php foreach ($faq_items as $i => $faq): ?>
                    <div class="faq-item reveal reveal--delay-<?= min($i + 1, 5) ?>" role="listitem">
                        <button
                            class="faq-item__btn"
                            id="faq-btn-<?= $i ?>"
                            aria-expanded="false"
                            aria-controls="faq-body-<?= $i ?>"
                        >
                            <span><?= h($faq['q']) ?></span>
                            <span class="faq-item__icon" aria-hidden="true">+</span>
                        </button>
                        <div
                            class="faq-item__body"
                            id="faq-body-<?= $i ?>"
                            aria-labelledby="faq-btn-<?= $i ?>"
                            role="region"
                        >
                            <p class="faq-item__text">
                                <?= h($faq['a']) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div><!-- /.faq-list -->

            <div class="text-center reveal" style="margin-top: var(--space-xl);">
                <p style="color: var(--color-text-muted); margin-bottom: var(--space-md);">
                    Остались вопросы?
                </p>
                <a href="/contacts.php" class="btn btn--outline btn--lg">
                    Написать нам
                </a>
            </div>

        </div><!-- /.container -->
    </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>