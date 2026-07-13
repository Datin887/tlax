<?php
/**
 * Страница портфолио — все примеры работ с фильтрацией по категориям
 * Динамическая загрузка через AJAX + пагинация "Показать ещё"
 *
 * Путь: /public/portfolio.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';

// ─── Получаем список категорий из БД ───
$categories = [];
try {
    $db = Database::getInstance();
    $categories = $db->fetchAll(
        "SELECT c.*, COUNT(t.id) AS track_count
         FROM track_categories c
         LEFT JOIN tracks t ON t.category_id = c.id AND t.is_active = 1
         GROUP BY c.id
         HAVING track_count > 0
         ORDER BY c.sort_order ASC, c.name ASC"
    );
} catch (Exception $e) {
    log_error('portfolio: ошибка загрузки категорий: ' . $e->getMessage());
}

// ─── Активная категория из URL ───
$active_category = trim(get_url_param('category', ''));
$active_category = preg_replace('/[^a-z0-9_-]/', '', $active_category); // Санитизация

// ─── SEO ───
$category_label = 'Все работы';
foreach ($categories as $cat) {
    if ($cat['slug'] === $active_category) {
        $category_label = $cat['name'];
        break;
    }
}

$page_meta = [
    'title'       => 'Портфолио — ' . $category_label . ' | Хитовая Песня',
    'description' => 'Более 500 уникальных песен для свадеб, юбилеев, дней рождения и корпоративов. Послушайте примеры работ студии Хитовая Песня.',
    'keywords'    => 'примеры песен на заказ, портфолио студии, образцы именных песен',
    'canonical'   => SITE_URL . '/portfolio.php' . ($active_category ? '?category=' . $active_category : ''),
];

require_once __DIR__ . '/../includes/head-meta.php';
require_once __DIR__ . '/../includes/header.php';
?>

<main>

    <!-- ═══════════════════════════════════════
         ЗАГОЛОВОК СТРАНИЦЫ
    ═══════════════════════════════════════ -->
    <section class="page-hero section--primary section--sm">
        <div class="container">
            <div class="page-hero__content reveal">

                <!-- Хлебные крошки -->
                <nav class="breadcrumb" aria-label="Хлебные крошки">
                    <span class="breadcrumb__item">
                        <a href="/" class="breadcrumb__link">Главная</a>
                        <span class="breadcrumb__sep" aria-hidden="true">›</span>
                    </span>
                    <span class="breadcrumb__item">
                        <span aria-current="page">Портфолио</span>
                    </span>
                </nav>

                <h1 class="section-title" style="color:#fff;">Наши работы</h1>
                <p class="section-subtitle">
                    Более 500 уникальных песен для самых разных поводов.
                    Каждая — особенная история.
                </p>

            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════
         ФИЛЬТРЫ ПО КАТЕГОРИЯМ
    ═══════════════════════════════════════ -->
    <section class="section--white section--sm" id="filters">
        <div class="container">

            <div class="filter-bar reveal" role="navigation" aria-label="Фильтр по категориям">

                <!-- Кнопка "Все" -->
                <a
                    href="/portfolio.php"
                    class="filter-btn<?= $active_category === '' ? ' active' : '' ?>"
                    aria-current="<?= $active_category === '' ? 'true' : 'false' ?>"
                >
                    🎵 Все работы
                </a>

                <!-- Категории из БД -->
                <?php foreach ($categories as $cat): ?>
                    <a
                        href="/portfolio.php?category=<?= urlencode($cat['slug']) ?>"
                        class="filter-btn<?= $active_category === $cat['slug'] ? ' active' : '' ?>"
                        aria-current="<?= $active_category === $cat['slug'] ? 'true' : 'false' ?>"
                    >
                        <?= htmlspecialchars($cat['icon'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
                        <span class="filter-btn__count"><?= (int)$cat['track_count'] ?></span>
                    </a>
                <?php endforeach; ?>

            </div><!-- /.filter-bar -->

        </div>
    </section>


    <!-- ═══════════════════════════════════════
         СЕТКА ТРЕКОВ
    ═══════════════════════════════════════ -->
    <section class="section" id="tracks-section">
        <div class="container">

            <!-- Индикатор загрузки -->
            <div id="tracks-loading" class="tracks-loading" aria-live="polite" aria-busy="true">
                <div class="tracks-loading__inner">
                    <div class="spinner spinner--lg"></div>
                    <p>Загружаем работы…</p>
                </div>
            </div>

            <!-- Сетка (заполняется через JS) -->
            <div
                id="tracks-grid"
                class="tracks-grid tracks-grid--portfolio"
                aria-label="Список треков"
                data-category="<?= htmlspecialchars($active_category, ENT_QUOTES, 'UTF-8') ?>"
                data-per-page="12"
                data-page="1"
            ></div>

            <!-- Пустое состояние -->
            <div id="tracks-empty" class="tracks-empty" hidden>
                <div class="tracks-empty__icon" aria-hidden="true">🎵</div>
                <h3 class="tracks-empty__title">Треков пока нет</h3>
                <p class="tracks-empty__desc">В этой категории ещё нет примеров работ.</p>
                <a href="/portfolio.php" class="btn btn--outline btn--lg">
                    Посмотреть все работы
                </a>
            </div>

            <!-- Кнопка "Показать ещё" -->
            <div id="tracks-more-wrap" class="tracks-grid__more" hidden>
                <button id="tracks-more-btn" class="btn btn--outline btn--lg">
                    Показать ещё
                </button>
                <p id="tracks-counter" class="tracks-counter"></p>
            </div>

        </div>
    </section>


    <!-- ═══════════════════════════════════════
         CTA БЛОК
    ═══════════════════════════════════════ -->
    <section class="cta-section">
        <div class="cta-section__decor" aria-hidden="true">
            <div class="cta-section__circle cta-section__circle--1"></div>
            <div class="cta-section__circle cta-section__circle--2"></div>
        </div>
        <div class="container">
            <div class="cta-section__content reveal">
                <h2 class="cta-section__title">Хотите такую же?</h2>
                <p class="cta-section__subtitle">
                    Расскажите нам вашу историю — создадим уникальную песню именно для вас
                </p>
                <div class="cta-section__actions">
                    <a
                        href="/order.php<?= $active_category ? '?category=' . urlencode($active_category) : '' ?>"
                        class="btn btn--accent btn--xl"
                    >
                        🎵 Заказать похожую
                    </a>
                    <a href="/pricing.php" class="btn btn--outline-white btn--xl">
                        Посмотреть цены
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
// Передаём в JS переменные для инициализации
$js_config = json_encode([
    'apiUrl'     => '/api/get-tracks.php',
    'category'   => $active_category,
    'perPage'    => 12,
    'orderUrl'   => '/order.php',
]);
?>

<script>
    window.PortfolioConfig = <?= $js_config ?>;
</script>

<?php
$extra_js = ['/assets/js/portfolio.js'];
require_once __DIR__ . '/../includes/footer.php';
?>