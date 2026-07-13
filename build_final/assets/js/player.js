/**
 * Аудиоплеер для карточек треков
 * Поддерживает множество плееров на странице,
 * но только один может играть одновременно
 * 
 * Путь: /public/assets/js/player.js
 */

'use strict';

(function() {

    /* ─── Глобальное состояние плеера ─── */
    const PlayerState = {
        current: null,        // Текущий активный плеер
        audio:   new Audio(), // Один Audio-объект на все треки
    };

    /**
     * Инициализация всех плееров на странице
     */
    function initPlayers() {
        const playerEls = document.querySelectorAll('[data-player]');
        playerEls.forEach(el => initSinglePlayer(el));
    }

    /**
     * Инициализация одного плеера
     * @param {Element} playerEl — корневой элемент [data-player]
     */
    function initSinglePlayer(playerEl) {
        const trackId  = playerEl.dataset.trackId  || null;
        const audioSrc = playerEl.dataset.audioSrc || null;

        if (!audioSrc) return;

        const playBtn      = playerEl.querySelector('[data-player-play]');
        const progressEl   = playerEl.querySelector('[data-player-progress]');
        const currentEl    = playerEl.querySelector('[data-player-current]');
        const durationEl   = playerEl.querySelector('[data-player-duration]');
        const volumeEl     = playerEl.querySelector('[data-player-volume]');

        if (!playBtn) return;

        const state = {
            isPlaying: false,
            trackId,
            audioSrc,
        };

        /* ─── Кнопка play/pause ─── */
        playBtn.addEventListener('click', () => {
            if (state.isPlaying) {
                pausePlayer(playerEl, state);
            } else {
                playPlayer(playerEl, state, {
                    playBtn, progressEl, currentEl, durationEl, volumeEl
                });
            }
        });

        /* ─── Прогресс-бар — перемотка ─── */
        if (progressEl) {
            progressEl.addEventListener('input', () => {
                if (PlayerState.current !== playerEl) return;
                PlayerState.audio.currentTime =
                    (progressEl.value / 100) * PlayerState.audio.duration;
            });
        }

        /* ─── Громкость ─── */
        if (volumeEl) {
            volumeEl.value = PlayerState.audio.volume * 100;
            volumeEl.addEventListener('input', () => {
                PlayerState.audio.volume = volumeEl.value / 100;
            });
        }

        /* ─── Play кнопка на обложке (если есть) ─── */
        const coverPlayBtn = playerEl.closest('.track-card')
            ?.querySelector('.track-card__play-btn');

        if (coverPlayBtn) {
            coverPlayBtn.addEventListener('click', () => {
                if (state.isPlaying) {
                    pausePlayer(playerEl, state);
                } else {
                    playPlayer(playerEl, state, {
                        playBtn, progressEl, currentEl, durationEl, volumeEl
                    });
                }
            });
        }
    }

    /**
     * Воспроизвести трек
     */
    function playPlayer(playerEl, state, controls) {
        const { playBtn, progressEl, currentEl, durationEl } = controls;
        const audio = PlayerState.audio;

        // Если другой трек играет — останавливаем его
        if (PlayerState.current && PlayerState.current !== playerEl) {
            stopCurrentPlayer();
        }

        // Если новый трек или трек не загружен
        if (PlayerState.current !== playerEl || audio.src !== state.audioSrc) {
            audio.src  = state.audioSrc;
            audio.load();
        }

        audio.play().then(() => {
            state.isPlaying     = true;
            PlayerState.current = playerEl;

            updatePlayButton(playBtn, true);
            updateCoverButton(playerEl, true);

            /* ─── Обновление прогресса ─── */
            audio.ontimeupdate = () => {
                if (PlayerState.current !== playerEl) return;

                const progress = (audio.currentTime / audio.duration) * 100 || 0;

                if (progressEl) {
                    progressEl.value = progress;
                    // Градиент заполнения прогресс-бара
                    progressEl.style.backgroundImage =
                        `linear-gradient(to right, var(--color-primary) ${progress}%, var(--color-border) ${progress}%)`;
                }

                if (currentEl) {
                    currentEl.textContent = HitSong.formatTime(audio.currentTime);
                }
            };

            /* ─── Загрузка метаданных ─── */
            audio.onloadedmetadata = () => {
                if (durationEl) {
                    durationEl.textContent = HitSong.formatTime(audio.duration);
                }
            };

            /* ─── Конец трека ─── */
            audio.onended = () => {
                state.isPlaying = false;
                updatePlayButton(playBtn, false);
                updateCoverButton(playerEl, false);
                if (progressEl) {
                    progressEl.value = 0;
                    progressEl.style.backgroundImage = '';
                }
                if (currentEl) currentEl.textContent = '0:00';
                PlayerState.current = null;
            };

            /* ─── Счётчик прослушиваний ─── */
            if (state.trackId) {
                trackPlayStart(state.trackId);
            }

        }).catch(err => {
            console.warn('Ошибка воспроизведения:', err);
        });
    }

    /**
     * Поставить на паузу
     */
    function pausePlayer(playerEl, state) {
        PlayerState.audio.pause();
        state.isPlaying = false;

        const playBtn = playerEl.querySelector('[data-player-play]');
        updatePlayButton(playBtn, false);
        updateCoverButton(playerEl, false);
    }

    /**
     * Остановить текущий плеер
     */
    function stopCurrentPlayer() {
        if (!PlayerState.current) return;

        const prevEl      = PlayerState.current;
        const prevPlayBtn = prevEl.querySelector('[data-player-play]');

        PlayerState.audio.pause();
        PlayerState.audio.currentTime = 0;
        PlayerState.current = null;

        updatePlayButton(prevPlayBtn, false);
        updateCoverButton(prevEl, false);

        const prevProgress = prevEl.querySelector('[data-player-progress]');
        if (prevProgress) {
            prevProgress.value = 0;
            prevProgress.style.backgroundImage = '';
        }

        const prevCurrent = prevEl.querySelector('[data-player-current]');
        if (prevCurrent) prevCurrent.textContent = '0:00';
    }

    /**
     * Обновить иконку на кнопке play/pause
     */
    function updatePlayButton(btn, isPlaying) {
        if (!btn) return;
        btn.innerHTML = isPlaying
            ? '&#9646;&#9646;' // Пауза
            : '&#9654;';       // Воспроизведение
        btn.setAttribute('aria-label', isPlaying ? 'Пауза' : 'Воспроизвести');
        btn.classList.toggle('playing', isPlaying);
    }

    /**
     * Обновить иконку на кнопке в обложке
     */
    function updateCoverButton(playerEl, isPlaying) {
        const card = playerEl.closest('.track-card');
        if (!card) return;

        const coverBtn = card.querySelector('.track-card__play-btn');
        if (coverBtn) {
            coverBtn.innerHTML = isPlaying ? '&#9646;&#9646;' : '&#9654;';
        }
    }

    /**
     * Отправить статистику прослушивания
     * @param {number|string} trackId
     */
    function trackPlayStart(trackId) {
        const url = '/api/track-play.php';

        // Используем Beacon API для надёжной отправки
        if ('sendBeacon' in navigator) {
            const data = new FormData();
            data.append('track_id', trackId);
            data.append('action', 'play');
            navigator.sendBeacon(url, data);
        } else {
            fetch(url, {
                method: 'POST',
                body: new URLSearchParams({ track_id: trackId, action: 'play' }),
            }).catch(() => {}); // Игнорируем ошибку аналитики
        }
    }

    /**
     * Публичное API плеера
     */
    window.HitSong = window.HitSong || {};
    window.HitSong.Player = {
        init: initPlayers,
        stop: stopCurrentPlayer,
    };

    /* ─── Инициализация после DOM ─── */
    document.addEventListener('DOMContentLoaded', initPlayers);

    /* ─── Пауза при уходе со страницы ─── */
    document.addEventListener('visibilitychange', () => {
        if (document.hidden && PlayerState.current) {
            PlayerState.audio.pause();
        }
    });

})();