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