<?php
/**
 * Главная страница
 * Путь: /public/index.php
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';

// Получаем треки для главной
$featured_tracks = [];
try {
    $db = Database::getInstance();
    $featured_tracks = $db->fetchAll(
        "SELECT * FROM tracks WHERE is_featured = 1 AND is_active = 1 ORDER BY sort_order ASC, created_at DESC LIMIT ?",
        [FEATURED_TRACKS_LIMIT]
    );
} catch (Exception $e) {
    log_error('index: ошибка загрузки треков: ' . $e->getMessage());
}

// Отзывы
$reviews = [];
try {
    $reviews = $db->fetchAll(
        "SELECT * FROM reviews WHERE is_featured = 1 AND is_active = 1 ORDER BY sort_order ASC, created_at DESC LIMIT 6"
    );
} catch (Exception $e) {
    log_error('index: ошибка загрузки отзывов: ' . $e->getMessage());
}

$page_meta = [
    'title' => SEO_DEFAULT_TITLE,
    'description' => SEO_DEFAULT_DESCRIPTION,
    'keywords' => SEO_DEFAULT_KEYWORDS,
    'canonical' => APP_URL . '/',
];

require_once __DIR__ . '/includes/head-meta.php';
require_once __DIR__ . '/includes/header.php';
?>

<main>
    <!-- HERO -->
    <section class="page-hero section--primary">
        <div class="container">
            <div class="page-hero__content reveal">
                <h1 class="hero-title">Персональная песня для вашего праздника</h1>
                <p class="hero-subtitle">
                    Свадьба, юбилей, день рождения или корпоратив? 
                    Мы напишем и исполним песню, которая станет волнующим подарком.
                </p>
                <div class="hero-actions" style="margin-top: var(--space-xl);">
                    <a href="/order.php" class="btn btn--accent" style="font-size: 18px; padding: 16px 32px;">
                        Заказать песню
                    </a>
                    <a href="/portfolio.php" class="btn btn--outline">
                        Посмотреть примеры
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ТАРИФЫ -->
    <section class="section section--white">
        <div class="container">
            <div class="section-header reveal">
                <h2 class="section-title">Три тарифа — выбирайте свой</h2>
                <p class="section-subtitle">Оплата только после результата. Без предоплаты.</p>
            </div>
            
            <div class="grid grid--3 reveal">
                <?php foreach (TARIFFS as $key => $tariff): ?>
                    <div class="card">
                        <div class="card__content">
                            <h3 class="card__title"><?= h($tariff['name']) ?> тариф</h3>
                            <p class="card__price" style="font-size: 32px; font-weight: bold; color: var(--color-primary); margin: var(--space-md) 0;">
                                <?= h($tariff['label']) ?>
                            </p>
                            <ul style="margin-bottom: var(--space-lg);">
                                <li style="padding: 8px 0; border-bottom: 1px solid var(--color-border);">✓ Индивидуальный текст</li>
                                <li style="padding: 8px 0; border-bottom: 1px solid var(--color-border);">✓ Профессиональная аранжировка</li>
                                <li style="padding: 8px 0; border-bottom: 1px solid var(--color-border);">✓ Готовый трек в формате MP3</li>
                                <?php if ($key !== 'basic'): ?>
                                    <li style="padding: 8px 0; border-bottom: 1px solid var(--color-border);">✓ WAV-версия</li>
                                <?php endif; ?>
                                <?php if ($key === 'premium'): ?>
                                    <li style="padding: 8px 0; border-bottom: 1px solid var(--color-border);">✓ Видео с текстом</li>
                                    <li style="padding: 8px 0;">✓ Исходники проекта</li>
                                <?php endif; ?>
                            </ul>
                            <a href="/order.php?tariff=<?= h($key) ?>" class="btn btn--primary" style="width: 100%;">
                                Выбрать тариф
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ПОРТФОЛИО -->
    <?php if (!empty($featured_tracks)): ?>
    <section class="section">
        <div class="container">
            <div class="section-header reveal">
                <h2 class="section-title">Последние работы</h2>
                <a href="/portfolio.php" class="btn btn--outline">Смотреть все →</a>
            </div>
            
            <div class="grid grid--3 reveal">
                <?php foreach ($featured_tracks as $track): ?>
                    <div class="track-card">
                        <div class="track-card__cover">
                            <?php if (!empty($track['cover_image'])): ?>
                                <img src="<?= h($track['cover_image']) ?>" alt="<?= h($track['title']) ?>" class="track-card__image">
                            <?php else: ?>
                                <div style="background: linear-gradient(135deg, var(--color-primary), var(--color-accent)); width: 100%; height: 100%;"></div>
                            <?php endif; ?>
                            <button class="track-card__play" data-track-id="<?= (int)$track['id'] ?>" data-audio-src="<?= h($track['audio_file'] ?? '') ?>">
                                ▶
                            </button>
                        </div>
                        <div class="track-card__content">
                            <div class="track-card__category"><?= h($track['category'] ?? 'Песня') ?></div>
                            <h3 class="track-card__title"><?= h($track['title']) ?></h3>
                            <div class="track-card__meta">
                                <span><?= format_duration((int)($track['duration'] ?? 0)) ?></span>
                                <span><?= format_plays_count((int)($track['plays_count'] ?? 0)) ?> прослушиваний</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ОТЗЫВЫ -->
    <?php if (!empty($reviews)): ?>
    <section class="section section--light">
        <div class="container">
            <div class="section-header reveal">
                <h2 class="section-title">Отзывы клиентов</h2>
            </div>
            
            <div class="grid grid--2 reveal">
                <?php foreach ($reviews as $review): ?>
                    <div class="card">
                        <div class="card__content">
                            <div style="display: flex; gap: var(--space-sm); margin-bottom: var(--space-md);">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <span style="color: <?= $i < $review['rating'] ? '#FFD700' : '#ddd' ?>;">★</span>
                                <?php endfor; ?>
                            </div>
                            <p class="card__text">"<?= h($review['text']) ?>"</p>
                            <p style="margin-top: var(--space-md); font-weight: var(--font-weight-semibold);">
                                — <?= h($review['author_name']) ?>, <?= h($review['author_city'] ?? 'город') ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA -->
    <section class="section section--primary">
        <div class="container">
            <div class="page-hero__content reveal" style="max-width: 600px;">
                <h2 class="hero-title">Готовы заказать песню?</h2>
                <p class="hero-subtitle">
                    Заполните анкету за 5 минут — и мы начнём работу над вашей историей.
                </p>
                <a href="/order.php" class="btn btn--accent" style="font-size: 18px; padding: 16px 32px;">
                    Начать заказ
                </a>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>