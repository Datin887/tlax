/**
 * Аудио-плеер для треков
 * Путь: /public/assets/js/player.js
 */

class AudioPlayer {
    constructor(container) {
        this.container = container;
        this.audio = container.querySelector('audio');
        this.playBtn = container.querySelector('[data-action="play"]');
        this.progress = container.querySelector('[data-progress]');
        this.progressFill = container.querySelector('[data-progress-fill]');
        
        this.init();
    }
    
    init() {
        if (!this.audio) return;
        
        // Клик по play/pause
        if (this.playBtn) {
            this.playBtn.addEventListener('click', () => this.togglePlay());
        }
        
        // Прогресс
        this.audio.addEventListener('timeupdate', () => this.updateProgress());
        
        // Клик по прогресс-бару
        if (this.progress) {
            this.progress.addEventListener('click', (e) => this.seek(e));
        }
        
        // Завершение трека
        this.audio.addEventListener('ended', () => this.onEnded());
    }
    
    togglePlay() {
        if (this.audio.paused) {
            this.audio.play();
            this.playBtn.textContent = '⏸';
        } else {
            this.audio.pause();
            this.playBtn.textContent = '▶';
        }
    }
    
    updateProgress() {
        if (!this.progressFill) return;
        
        const percent = (this.audio.currentTime / this.audio.duration) * 100;
        this.progressFill.style.width = `${percent}%`;
    }
    
    seek(e) {
        const rect = this.progress.getBoundingClientRect();
        const percent = (e.clientX - rect.left) / rect.width;
        this.audio.currentTime = percent * this.audio.duration;
    }
    
    onEnded() {
        this.playBtn.textContent = '▶';
        this.progressFill.style.width = '0%';
    }
}

// Инициализация всех плееров
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.audio-player').forEach(player => {
        new AudioPlayer(player);
    });
    
    // Трек-карточки
    document.querySelectorAll('.track-card__play').forEach(btn => {
        btn.addEventListener('click', function() {
            const trackId = this.dataset.trackId;
            const audioSrc = this.dataset.audioSrc;
            
            if (trackId && audioSrc) {
                playTrack(trackId, audioSrc);
            }
        });
    });
});

// Статистика прослушивания
function playTrack(trackId, audioSrc) {
    // Отправляем статистику
    fetch('/api/track-play.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ track_id: trackId })
    }).catch(() => {});
    
    // Создаём audio элемент
    const audio = new Audio(audioSrc);
    audio.play().catch(e => console.warn('Audio play failed:', e));
}

// Экспортируем
window.AudioPlayer = AudioPlayer;