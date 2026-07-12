# ЭТАП 3: Портфолио + API треков

Создаём:
1. `public/portfolio.php`
2. `public/api/get-tracks.php`
3. `public/api/track-play.php`

---

## Файл 1: `public/portfolio.php`

```php
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
```

---

## Файл 2: `public/assets/js/portfolio.js`

```javascript
/**
 * JavaScript для страницы портфолио
 * Загрузка треков через AJAX, фильтрация, "показать ещё"
 *
 * Путь: /public/assets/js/portfolio.js
 */

'use strict';

(function () {

    /* ─── Конфигурация ─── */
    const config = window.PortfolioConfig || {
        apiUrl:   '/api/get-tracks.php',
        category: '',
        perPage:  12,
        orderUrl: '/order.php',
    };

    /* ─── Состояние ─── */
    const state = {
        page:     1,
        category: config.category || '',
        loading:  false,
        hasMore:  false,
        total:    0,
        loaded:   0,
    };

    /* ─── DOM-элементы ─── */
    const gridEl      = document.getElementById('tracks-grid');
    const loadingEl   = document.getElementById('tracks-loading');
    const emptyEl     = document.getElementById('tracks-empty');
    const moreWrapEl  = document.getElementById('tracks-more-wrap');
    const moreBtnEl   = document.getElementById('tracks-more-btn');
    const counterEl   = document.getElementById('tracks-counter');

    /* ═══════════════════════════════════════
       ЗАГРУЗКА ТРЕКОВ
    ═══════════════════════════════════════ */

    /**
     * Загрузить треки с сервера
     * @param {boolean} append — добавить к существующим или заменить
     */
    async function loadTracks(append = false) {
        if (state.loading) return;
        state.loading = true;

        if (!append) {
            showLoading(true);
            gridEl.innerHTML = '';
        } else {
            setMoreBtnLoading(true);
        }

        try {
            const params = new URLSearchParams({
                category: state.category,
                page:     state.page,
                per_page: config.perPage,
            });

            const response = await fetch(`${config.apiUrl}?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const data = await response.json();

            if (!data.success) throw new Error(data.message || 'Ошибка сервера');

            const tracks = data.tracks || [];
            state.total   = data.total || 0;
            state.hasMore = data.has_more || false;
            state.loaded += tracks.length;

            if (!append) {
                showLoading(false);
            }

            if (tracks.length === 0 && !append) {
                showEmpty(true);
                showMoreBtn(false);
            } else {
                showEmpty(false);
                renderTracks(tracks, append);
                updateCounter();
                showMoreBtn(state.hasMore);

                // Инициализируем плееры для новых карточек
                if (window.HitSong && window.HitSong.Player) {
                    window.HitSong.Player.init();
                }

                // Scroll reveal для новых карточек
                initRevealForNew();
            }

        } catch (err) {
            console.error('Ошибка загрузки треков:', err);
            showLoading(false);

            if (!append) {
                gridEl.innerHTML = `
                    <div class="tracks-error">
                        <p>⚠️ Не удалось загрузить треки. Попробуйте обновить страницу.</p>
                        <button onclick="location.reload()" class="btn btn--outline btn--sm">
                            Обновить
                        </button>
                    </div>
                `;
            } else {
                if (window.Notify) {
                    Notify.error('Ошибка', 'Не удалось загрузить треки');
                }
            }
        } finally {
            state.loading = false;
            setMoreBtnLoading(false);
        }
    }

    /* ═══════════════════════════════════════
       РЕНДЕРИНГ КАРТОЧЕК
    ═══════════════════════════════════════ */

    /**
     * Отрисовать массив треков в сетку
     * @param {Array}   tracks
     * @param {boolean} append
     */
    function renderTracks(tracks, append = false) {
        if (!append) gridEl.innerHTML = '';

        tracks.forEach((track, i) => {
            const card = createTrackCard(track, i);
            gridEl.appendChild(card);
        });
    }

    /**
     * Создать DOM-карточку трека
     * @param {Object} track
     * @param {number} index — для задержки анимации
     * @returns {Element}
     */
    function createTrackCard(track, index) {
        const article = document.createElement('article');
        article.className = 'track-card track-card--portfolio reveal';
        article.setAttribute('aria-label', `Трек: ${escHtml(track.title)}`);

        // Определяем обложку
        const coverClass  = getCoverClass(track.category_slug);
        const coverIcon   = track.category_icon || '🎵';
        const hasCover    = !!track.cover_url;
        const hasAudio    = !!track.audio_url;

        // Формируем URL заказа "Хочу похожую"
        const orderParams = new URLSearchParams();
        if (track.category_slug) orderParams.set('category', track.category_slug);
        if (track.style)         orderParams.set('style',    track.style);
        const orderUrl = `${config.orderUrl}?${orderParams.toString()}`;

        article.innerHTML = `
            <!-- Обложка -->
            <div class="track-card__cover ${hasCover ? '' : coverClass}">
                ${hasCover
                    ? `<img
                            data-src="${escHtml(track.cover_url)}"
                            src="/assets/img/placeholder-cover.svg"
                            alt="Обложка: ${escHtml(track.title)}"
                            loading="lazy"
                            width="400"
                            height="225"
                       >`
                    : `<span class="track-card__cover-icon" aria-hidden="true">${coverIcon}</span>`
                }
                ${hasAudio ? `
                    <div class="track-card__play-overlay">
                        <button
                            class="track-card__play-btn"
                            aria-label="Воспроизвести ${escHtml(track.title)}"
                        >&#9654;</button>
                    </div>
                ` : ''}
            </div>

            <!-- Контент -->
            <div class="track-card__body">

                <!-- Категория -->
                ${track.category_name ? `
                    <span class="track-card__category">
                        ${escHtml(track.category_icon || '')}
                        ${escHtml(track.category_name)}
                    </span>
                ` : ''}

                <!-- Название -->
                <h3 class="track-card__title">${escHtml(track.title)}</h3>

                <!-- Описание (для портфолио — больше текста) -->
                ${track.description ? `
                    <p class="track-card__desc">${escHtml(truncate(track.description, 100))}</p>
                ` : ''}

                <!-- Мета-данные -->
                <div class="track-card__meta">
                    ${track.style ? `
                        <span class="track-card__meta-item" title="Стиль">
                            🎼 ${escHtml(track.style)}
                        </span>
                    ` : ''}
                    ${track.mood ? `
                        <span class="track-card__meta-item" title="Настроение">
                            🎭 ${escHtml(track.mood)}
                        </span>
                    ` : ''}
                    ${track.voice_type ? `
                        <span class="track-card__meta-item" title="Голос">
                            🎤 ${escHtml(track.voice_type)}
                        </span>
                    ` : ''}
                </div>

                <!-- Плеер -->
                ${hasAudio ? `
                    <div
                        data-player
                        data-track-id="${parseInt(track.id)}"
                        data-audio-src="${escHtml(track.audio_url)}"
                    >
                        <div class="track-player">
                            <button
                                class="track-player__btn"
                                data-player-play
                                aria-label="Воспроизвести ${escHtml(track.title)}"
                            >&#9654;</button>

                            <div class="track-player__progress-wrap">
                                <input
                                    type="range"
                                    class="track-player__progress"
                                    data-player-progress
                                    min="0" max="100" value="0" step="0.1"
                                    aria-label="Прогресс воспроизведения"
                                >
                                <div class="track-player__time">
                                    <span data-player-current>0:00</span>
                                    <span data-player-duration>${escHtml(track.duration_formatted || '0:00')}</span>
                                </div>
                            </div>

                            <input
                                type="range"
                                class="track-player__volume"
                                data-player-volume
                                min="0" max="100" value="80"
                                aria-label="Громкость"
                            >
                        </div>
                    </div>
                ` : `
                    <div class="track-player track-player--no-audio">
                        <span>🎵 Превью недоступно</span>
                    </div>
                `}

                <!-- Кнопка "Хочу похожую" -->
                <div class="track-card__actions">
                    <a href="${escHtml(orderUrl)}" class="btn btn--outline btn--sm btn--full">
                        Хочу похожую 🎵
                    </a>
                </div>

            </div><!-- /.track-card__body -->
        `;

        return article;
    }

    /* ═══════════════════════════════════════
       UI-ХЕЛПЕРЫ
    ═══════════════════════════════════════ */

    function showLoading(show) {
        if (!loadingEl) return;
        loadingEl.hidden = !show;
        loadingEl.setAttribute('aria-busy', show ? 'true' : 'false');
    }

    function showEmpty(show) {
        if (!emptyEl) return;
        emptyEl.hidden = !show;
    }

    function showMoreBtn(show) {
        if (!moreWrapEl) return;
        moreWrapEl.hidden = !show;
    }

    function setMoreBtnLoading(loading) {
        if (!moreBtnEl) return;
        moreBtnEl.disabled = loading;
        moreBtnEl.innerHTML = loading
            ? '<span class="spinner spinner--sm spinner--white"></span> Загружаем…'
            : 'Показать ещё';
    }

    function updateCounter() {
        if (!counterEl) return;
        if (state.total > 0) {
            counterEl.textContent = `Показано ${state.loaded} из ${state.total}`;
        }
    }

    /**
     * Инициализировать scroll-reveal для новых карточек
     */
    function initRevealForNew() {
        if (!('IntersectionObserver' in window)) {
            document.querySelectorAll('.reveal:not(.revealed)').forEach(el => {
                el.classList.add('revealed');
            });
            return;
        }

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }
        );

        document.querySelectorAll('.reveal:not(.revealed)').forEach(el => {
            observer.observe(el);
        });
    }

    /**
     * Определить CSS-класс обложки по slug категории
     */
    function getCoverClass(slug) {
        const map = {
            wedding:     'track-card__cover--wedding',
            birthday:    'track-card__cover--birthday',
            anniversary: 'track-card__cover--anniversary',
            corporate:   'track-card__cover--corporate',
            holiday:     'track-card__cover--holiday',
            children:    'track-card__cover--children',
        };
        return map[slug] || 'track-card__cover--wedding';
    }

    /**
     * Обрезать строку
     * @param {string} str
     * @param {number} maxLen
     * @returns {string}
     */
    function truncate(str, maxLen) {
        if (!str) return '';
        return str.length > maxLen ? str.slice(0, maxLen) + '…' : str;
    }

    /**
     * Экранирование HTML
     * @param {string} str
     * @returns {string}
     */
    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    /* ═══════════════════════════════════════
       СОБЫТИЯ
    ═══════════════════════════════════════ */

    // Кнопка "Показать ещё"
    if (moreBtnEl) {
        moreBtnEl.addEventListener('click', () => {
            state.page++;
            loadTracks(true);
        });
    }

    /* ═══════════════════════════════════════
       ИНИЦИАЛИЗАЦИЯ
    ═══════════════════════════════════════ */

    document.addEventListener('DOMContentLoaded', () => {
        loadTracks(false);
    });

})();
```

---

## Файл 3: `public/api/get-tracks.php`

```php
<?php
/**
 * API: Получение списка треков
 * Метод: GET
 * Параметры:
 *   category  — slug категории (опционально)
 *   page      — номер страницы (по умолчанию 1)
 *   per_page  — треков на страницу (по умолчанию 12, макс 24)
 *   featured  — только избранные (1/0)
 *   search    — поиск по названию
 *
 * Возвращает JSON
 *
 * Путь: /public/api/get-tracks.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';

// ─── Только AJAX-запросы ───
if (!is_ajax_request()) {
    http_response_code(403);
    send_json(['success' => false, 'message' => 'Forbidden'], 403);
}

// ─── Только GET ───
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send_json(['success' => false, 'message' => 'Method not allowed'], 405);
}

// ─── Rate limiting ───
if (!check_rate_limit('get_tracks_' . get_client_ip(), 120, 60)) {
    send_json(['success' => false, 'message' => 'Too many requests'], 429);
}

// ─── Параметры запроса ───
$category = trim($_GET['category'] ?? '');
$category = preg_replace('/[^a-z0-9_-]/', '', $category); // Санитизация

$page = max(1, (int)($_GET['page'] ?? 1));

$per_page = min(24, max(1, (int)($_GET['per_page'] ?? 12)));

$featured_only = (int)($_GET['featured'] ?? 0) === 1;

$search = trim($_GET['search'] ?? '');
$search = mb_substr($search, 0, 100, 'UTF-8');

try {
    $db = Database::getInstance();

    // ─── Строим WHERE-условия ───
    $conditions = ['t.is_active = 1'];
    $params     = [];

    // Фильтр по категории
    if ($category !== '') {
        $conditions[] = 'c.slug = :category';
        $params[':category'] = $category;
    }

    // Только избранные
    if ($featured_only) {
        $conditions[] = 't.is_featured = 1';
    }

    // Поиск по названию
    if ($search !== '') {
        $conditions[] = 't.title LIKE :search';
        $params[':search'] = '%' . $search . '%';
    }

    $where_sql = 'WHERE ' . implode(' AND ', $conditions);

    // ─── Общее количество ───
    $count_sql = "
        SELECT COUNT(t.id) AS total
        FROM tracks t
        LEFT JOIN track_categories c ON t.category_id = c.id
        {$where_sql}
    ";

    $count_row = $db->fetchOne($count_sql, $params);
    $total     = (int)($count_row['total'] ?? 0);

    // ─── Смещение ───
    $offset = ($page - 1) * $per_page;

    // ─── Основной запрос ───
    $params[':limit']  = $per_page;
    $params[':offset'] = $offset;

    $tracks_sql = "
        SELECT
            t.id,
            t.title,
            t.description,
            t.audio_file,
            t.cover_image,
            t.duration,
            t.style,
            t.mood,
            t.voice_type,
            t.plays_count,
            t.is_featured,
            t.sort_order,
            c.id   AS category_id,
            c.name AS category_name,
            c.slug AS category_slug,
            c.icon AS category_icon
        FROM tracks t
        LEFT JOIN track_categories c ON t.category_id = c.id
        {$where_sql}
        ORDER BY t.sort_order ASC, t.is_featured DESC, t.created_at DESC
        LIMIT :limit OFFSET :offset
    ";

    $tracks_raw = $db->fetchAll($tracks_sql, $params);

    // ─── Форматируем данные для ответа ───
    $tracks = array_map(function (array $track): array {
        $audio_url = '';
        if (!empty($track['audio_file'])) {
            $audio_path = __DIR__ . '/../../uploads/tracks/' . $track['audio_file'];
            if (file_exists($audio_path)) {
                $audio_url = SITE_URL . '/uploads/tracks/' . rawurlencode($track['audio_file']);
            }
        }

        $cover_url = '';
        if (!empty($track['cover_image'])) {
            $cover_path = __DIR__ . '/../../uploads/covers/' . $track['cover_image'];
            if (file_exists($cover_path)) {
                $cover_url = SITE_URL . '/uploads/covers/' . rawurlencode($track['cover_image']);
            }
        }

        return [
            'id'                 => (int)$track['id'],
            'title'              => $track['title'],
            'description'        => $track['description'] ?? '',
            'audio_url'          => $audio_url,
            'cover_url'          => $cover_url,
            'duration'           => (int)($track['duration'] ?? 0),
            'duration_formatted' => format_duration((int)($track['duration'] ?? 0)),
            'style'              => $track['style'] ?? '',
            'mood'               => $track['mood'] ?? '',
            'voice_type'         => $track['voice_type'] ?? '',
            'plays_count'        => (int)($track['plays_count'] ?? 0),
            'is_featured'        => (bool)$track['is_featured'],
            'category_id'        => (int)($track['category_id'] ?? 0),
            'category_name'      => $track['category_name'] ?? '',
            'category_slug'      => $track['category_slug'] ?? '',
            'category_icon'      => $track['category_icon'] ?? '',
        ];
    }, $tracks_raw);

    // ─── Есть ли ещё страницы ───
    $has_more = ($offset + count($tracks)) < $total;

    send_json([
        'success'  => true,
        'tracks'   => $tracks,
        'total'    => $total,
        'page'     => $page,
        'per_page' => $per_page,
        'has_more' => $has_more,
        'loaded'   => $offset + count($tracks),
    ]);

} catch (Exception $e) {
    log_error('api/get-tracks: ' . $e->getMessage());
    send_json(['success' => false, 'message' => 'Внутренняя ошибка сервера'], 500);
}
```

---

## Файл 4: `public/api/track-play.php`

```php
<?php
/**
 * API: Счётчик прослушиваний трека
 * Метод: POST
 * Тело запроса:
 *   track_id — ID трека
 *   action   — 'play' (зарезервировано для расширения)
 *
 * Путь: /public/api/track-play.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';

// ─── Только POST ───
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['success' => false, 'message' => 'Method not allowed'], 405);
}

// ─── Rate limiting — не более 30 запросов в минуту с одного IP ───
$client_ip = get_client_ip();
if (!check_rate_limit('track_play_' . $client_ip, 30, 60)) {
    // Не возвращаем ошибку пользователю — аналитика некритична
    send_json(['success' => true, 'skipped' => true]);
}

// ─── Получаем данные ───
$track_id = (int)($_POST['track_id'] ?? 0);
$action   = trim($_POST['action'] ?? 'play');

if ($track_id <= 0) {
    send_json(['success' => false, 'message' => 'Invalid track_id'], 400);
}

// Разрешённые действия
$allowed_actions = ['play'];
if (!in_array($action, $allowed_actions, true)) {
    send_json(['success' => false, 'message' => 'Invalid action'], 400);
}

try {
    $db = Database::getInstance();

    // ─── Проверяем, что трек существует и активен ───
    $track = $db->fetchOne(
        "SELECT id FROM tracks WHERE id = :id AND is_active = 1",
        [':id' => $track_id]
    );

    if (!$track) {
        send_json(['success' => false, 'message' => 'Track not found'], 404);
    }

    // ─── Защита от накрутки: одно прослушивание с IP за 5 минут ───
    $rate_key   = 'play_' . $track_id . '_' . $client_ip;
    $recent_key = md5($rate_key);

    // Проверяем через track_plays — было ли прослушивание с этого IP за последние 5 мин
    $recent = $db->fetchOne(
        "SELECT id FROM track_plays
         WHERE track_id = :track_id
           AND ip_address = :ip
           AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)",
        [
            ':track_id' => $track_id,
            ':ip'       => $client_ip,
        ]
    );

    if ($recent) {
        // Уже считали — возвращаем успех без повторного счёта
        send_json(['success' => true, 'counted' => false]);
    }

    // ─── Записываем прослушивание ───
    $user_agent = mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500, 'UTF-8');

    $db->execute(
        "INSERT INTO track_plays (track_id, ip_address, user_agent, created_at)
         VALUES (:track_id, :ip, :ua, NOW())",
        [
            ':track_id' => $track_id,
            ':ip'       => $client_ip,
            ':ua'       => $user_agent,
        ]
    );

    // ─── Обновляем счётчик в таблице треков ───
    $db->execute(
        "UPDATE tracks SET plays_count = plays_count + 1 WHERE id = :id",
        [':id' => $track_id]
    );

    send_json(['success' => true, 'counted' => true]);

} catch (Exception $e) {
    log_error('api/track-play: ' . $e->getMessage());
    // Аналитика некритична — не показываем ошибку пользователю
    send_json(['success' => true, 'counted' => false]);
}
```

---

## Дополнения к `includes/functions.php`

Добавьте в конец файла эти функции (если их ещё нет из Этапа 1):

```php
/**
 * Проверка: является ли запрос AJAX
 *
 * @return bool
 */
function is_ajax_request(): bool
{
    return (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) || (
        !empty($_SERVER['HTTP_ACCEPT']) &&
        str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')
    );
}

/**
 * Отправить JSON-ответ и завершить скрипт
 *
 * @param  array $data
 * @param  int   $status HTTP-код
 * @return never
 */
function send_json(array $data, int $status = 200): never
{
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        header('X-Content-Type-Options: nosniff');
        // Запрет кэширования для API
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

/**
 * Получить IP-адрес клиента
 *
 * @return string
 */
function get_client_ip(): string
{
    $headers = [
        'HTTP_CF_CONNECTING_IP',   // Cloudflare
        'HTTP_X_REAL_IP',          // Nginx proxy
        'HTTP_X_FORWARDED_FOR',    // Общий прокси
        'REMOTE_ADDR',             // Напрямую
    ];

    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = trim(explode(',', $_SERVER[$header])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }

    // Фолбэк — принимаем любой IP включая приватные
    return trim($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

/**
 * Rate limiting через файловый кэш
 * (простая реализация без Redis/Memcached)
 *
 * @param  string $key      — уникальный ключ
 * @param  int    $limit    — максимум запросов
 * @param  int    $window   — окно в секундах
 * @return bool             — true = разрешено, false = лимит превышен
 */
function check_rate_limit(string $key, int $limit, int $window): bool
{
    $cache_dir  = defined('LOG_PATH') ? LOG_PATH . '/rate_limit' : sys_get_temp_dir() . '/hitsong_rl';
    $cache_file = $cache_dir . '/' . md5($key) . '.json';

    // Создаём директорию если нет
    if (!is_dir($cache_dir)) {
        @mkdir($cache_dir, 0750, true);
    }

    $now  = time();
    $data = ['count' => 0, 'window_start' => $now];

    // Читаем существующие данные
    if (file_exists($cache_file)) {
        $raw = @file_get_contents($cache_file);
        if ($raw) {
            $saved = json_decode($raw, true);
            if (is_array($saved)) {
                // Если окно ещё не истекло
                if ($now - $saved['window_start'] < $window) {
                    $data = $saved;
                }
                // Иначе — сбрасываем счётчик
            }
        }
    }

    $data['count']++;

    // Записываем
    @file_put_contents($cache_file, json_encode($data), LOCK_EX);

    return $data['count'] <= $limit;
}

/**
 * Получить параметр из URL ($_GET) с дефолтным значением
 *
 * @param  string $name
 * @param  string $default
 * @return string
 */
function get_url_param(string $name, string $default = ''): string
{
    return isset($_GET[$name]) ? trim((string)$_GET[$name]) : $default;
}
```

---

## Дополнения к `public/assets/css/main.css`

Добавьте в конец `main.css` стили для новых элементов портфолио:

```css
/* ═══════════════════════════════════════
   PAGE-HERO (заголовок внутренних страниц)
═══════════════════════════════════════ */

.page-hero {
    background: linear-gradient(145deg, var(--color-primary-dark) 0%, var(--color-primary) 100%);
    position: relative;
    overflow: hidden;
    padding-block: var(--space-2xl);
}

.page-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(
        circle at 80% 50%,
        rgba(212, 165, 116, 0.12) 0%,
        transparent 60%
    );
    pointer-events: none;
}

.page-hero__content {
    position: relative;
    z-index: 1;
}

.page-hero .section-title {
    margin-bottom: var(--space-sm);
}

.page-hero .breadcrumb {
    padding-top: 0;
    margin-bottom: var(--space-sm);
}

.page-hero .breadcrumb,
.page-hero .breadcrumb a,
.page-hero .breadcrumb__sep {
    color: rgba(255, 255, 255, 0.65);
}

.page-hero .breadcrumb a:hover {
    color: var(--color-accent);
}

/* ═══════════════════════════════════════
   ПОРТФОЛИО — ДОПОЛНИТЕЛЬНЫЕ СТИЛИ
═══════════════════════════════════════ */

.tracks-grid--portfolio {
    min-height: 200px;
    position: relative;
}

/* Описание трека (портфолио версия) */
.track-card__desc {
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
    line-height: var(--line-height-relaxed);
    margin-bottom: var(--space-xs);
}

/* Кнопка "Хочу похожую" */
.track-card__actions {
    margin-top: var(--space-sm);
    padding-top: var(--space-sm);
    border-top: 1px solid var(--color-border-light);
}

/* Счётчик загруженных */
.tracks-counter {
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
    text-align: center;
    margin-top: var(--space-xs);
}

/* Состояние загрузки */
.tracks-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-3xl);
}

.tracks-loading__inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-md);
    color: var(--color-text-muted);
    font-size: var(--font-size-sm);
}

/* Пустое состояние */
.tracks-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: var(--space-3xl);
    text-align: center;
    gap: var(--space-md);
}

.tracks-empty__icon {
    font-size: 64px;
    opacity: 0.4;
}

.tracks-empty__title {
    font-family: var(--font-heading);
    font-size: var(--font-size-h3);
    color: var(--color-text);
}

.tracks-empty__desc {
    color: var(--color-text-muted);
    max-width: 400px;
}

/* Ошибка загрузки */
.tracks-error {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-md);
    padding: var(--space-2xl);
    text-align: center;
    color: var(--color-text-muted);
    grid-column: 1 / -1;
}

/* Счётчик у кнопки фильтра */
.filter-btn__count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 18px;
    padding: 0 5px;
    background: rgba(139, 30, 63, 0.12);
    color: var(--color-primary);
    border-radius: var(--radius-full);
    font-size: 11px;
    font-weight: var(--font-weight-bold);
    line-height: 1;
    transition: var(--transition-base);
}

.filter-btn.active .filter-btn__count {
    background: rgba(255, 255, 255, 0.25);
    color: #fff;
}

/* Плеер без аудио */
.track-player--no-audio {
    justify-content: center;
    color: var(--color-text-muted);
    font-size: var(--font-size-sm);
    font-style: italic;
    min-height: 56px;
}
```

---

## Также добавьте в `database/schema.sql` таблицу `track_plays`

Если её ещё нет в схеме из Этапа 1, добавьте:

```sql
-- Статистика прослушиваний треков
CREATE TABLE IF NOT EXISTS `track_plays` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `track_id`   INT UNSIGNED    NOT NULL,
    `ip_address` VARCHAR(45)     NOT NULL DEFAULT '',
    `user_agent` VARCHAR(500)    NOT NULL DEFAULT '',
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    KEY `idx_track_id`   (`track_id`),
    KEY `idx_ip_track`   (`ip_address`, `track_id`),
    KEY `idx_created_at` (`created_at`),

    CONSTRAINT `fk_play_track`
        FOREIGN KEY (`track_id`)
        REFERENCES `tracks` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
```

---

## ✅ Этап 3 завершён

### Итог файлов:

| Файл | Назначение |
|------|------------|
| `public/portfolio.php` | Полная страница портфолио с фильтрами |
| `public/assets/js/portfolio.js` | AJAX-загрузка, "показать ещё", рендер карточек |
| `public/api/get-tracks.php` | REST API — список треков с фильтрацией и пагинацией |
| `public/api/track-play.php` | REST API — счётчик прослушиваний с защитой от накрутки |
| Дополнения в `functions.php` | `is_ajax_request`, `send_json`, `get_client_ip`, `check_rate_limit`, `get_url_param` |
| Дополнения в `main.css` | Стили page-hero, портфолио, пустых состояний |
| Дополнение в `schema.sql` | Таблица `track_plays` |

### Что умеет портфолио:
- 📂 **Фильтрация** по категориям — переключение через URL
- ⏬ **Пагинация** «Показать ещё» — AJAX без перезагрузки страницы
- 🎵 **Плеер** — один активный, переключение между треками
- 📊 **Счётчик** прослушиваний — защита от накрутки (5 мин с одного IP)
- 💨 **Lazy loading** обложек
- ✨ **Scroll reveal** для карточек
- 📱 **Адаптив** — 3 колонки → 2 → 1

