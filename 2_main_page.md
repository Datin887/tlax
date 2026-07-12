# ЭТАП 2: Главная страница

Отлично! Этап 1 на GitHub — приступаем к Этапу 2.

Создаём в порядке:
1. `assets/css/variables.css`
2. `assets/css/reset.css`
3. `assets/css/main.css`
4. `assets/css/components.css`
5. `assets/css/responsive.css`
6. `assets/js/main.js`
7. `assets/js/player.js`
8. `assets/js/notifications.js`
9. `public/index.php`

---

## Файл 1: `public/assets/css/variables.css`

```css
/**
 * CSS-переменные (дизайн-токены) проекта "Хитовая Песня"
 * Все цвета, шрифты, отступы, тени — только здесь
 * 
 * Путь: /public/assets/css/variables.css
 */

@import url('https://fonts.googleapis.com/css2?family=Unbounded:wght@700;800&family=Manrope:wght@400;500;600;700&family=Cormorant+Garamond:ital,wght@1,500&display=swap');

:root {
    /* ─── Цвета ─── */
    --color-primary:       #8B1E3F;
    --color-primary-dark:  #6B1230;
    --color-primary-light: #A83255;

    --color-accent:        #D4A574;
    --color-accent-light:  #F0E6D2;
    --color-accent-dark:   #B8864E;

    --color-bg:            #F5EFE6;
    --color-bg-white:      #FFFFFF;
    --color-bg-section:    #FAF6F0;

    --color-text:          #2C1810;
    --color-text-muted:    #6B5D54;
    --color-text-light:    #9A8880;

    --color-border:        #E8D5B7;
    --color-border-light:  #F0E6D2;

    --color-success:       #4A7C59;
    --color-success-light: #EBF4EE;
    --color-error:         #B33951;
    --color-error-light:   #FDEEF1;
    --color-warning:       #C17B2E;
    --color-warning-light: #FDF3E7;

    /* ─── Типографика ─── */
    --font-heading:  'Unbounded', sans-serif;
    --font-body:     'Manrope', sans-serif;
    --font-accent:   'Cormorant Garamond', serif;

    --font-size-base:  16px;
    --font-size-sm:    14px;
    --font-size-xs:    12px;
    --font-size-lg:    18px;
    --font-size-xl:    20px;

    --font-size-h1:    clamp(32px, 5vw, 56px);
    --font-size-h2:    clamp(28px, 4vw, 44px);
    --font-size-h3:    clamp(20px, 3vw, 30px);
    --font-size-h4:    clamp(18px, 2.5vw, 24px);

    --font-weight-normal:   400;
    --font-weight-medium:   500;
    --font-weight-semibold: 600;
    --font-weight-bold:     700;
    --font-weight-black:    800;

    --line-height-tight:   1.2;
    --line-height-normal:  1.6;
    --line-height-relaxed: 1.8;

    /* ─── Отступы (система 8px) ─── */
    --space-xs:  8px;
    --space-sm:  16px;
    --space-md:  24px;
    --space-lg:  32px;
    --space-xl:  48px;
    --space-2xl: 64px;
    --space-3xl: 96px;
    --space-4xl: 128px;

    /* ─── Скругления ─── */
    --radius-xs:   6px;
    --radius-sm:   10px;
    --radius-md:   12px;
    --radius-lg:   16px;
    --radius-xl:   20px;
    --radius-2xl:  28px;
    --radius-full: 9999px;

    /* ─── Тени ─── */
    --shadow-xs:  0 2px 8px  rgba(139, 30, 63, 0.06);
    --shadow-sm:  0 4px 20px rgba(139, 30, 63, 0.08);
    --shadow-md:  0 8px 30px rgba(139, 30, 63, 0.12);
    --shadow-lg:  0 15px 40px rgba(139, 30, 63, 0.15);
    --shadow-xl:  0 25px 60px rgba(139, 30, 63, 0.20);

    --shadow-accent-sm: 0 4px 20px rgba(212, 165, 116, 0.25);
    --shadow-accent-md: 0 8px 30px rgba(212, 165, 116, 0.35);

    /* ─── Анимации ─── */
    --transition-fast:   all 0.15s ease;
    --transition-base:   all 0.3s ease;
    --transition-slow:   all 0.5s ease;
    --transition-spring: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);

    /* ─── Z-индексы ─── */
    --z-base:     1;
    --z-dropdown: 100;
    --z-sticky:   200;
    --z-overlay:  300;
    --z-modal:    400;
    --z-toast:    500;

    /* ─── Сетка ─── */
    --container-width: 1200px;
    --container-pad:   var(--space-sm);

    /* ─── Шапка ─── */
    --header-height: 72px;
}

/* ─── Медиа-переменные (используем в компонентах) ─── */
@media (min-width: 768px) {
    :root {
        --container-pad: var(--space-md);
    }
}

@media (min-width: 1024px) {
    :root {
        --container-pad: var(--space-lg);
    }
}
```

---

## Файл 2: `public/assets/css/reset.css`

```css
/**
 * CSS Reset + базовые нормализации
 * Современный reset без агрессивного обнуления
 * 
 * Путь: /public/assets/css/reset.css
 */

/* ─── Box-sizing для всех ─── */
*,
*::before,
*::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

/* ─── Базовый HTML/Body ─── */
html {
    font-size: var(--font-size-base);
    scroll-behavior: smooth;
    -webkit-text-size-adjust: 100%;
    text-size-adjust: 100%;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

body {
    font-family: var(--font-body);
    font-size: var(--font-size-base);
    font-weight: var(--font-weight-normal);
    line-height: var(--line-height-normal);
    color: var(--color-text);
    background-color: var(--color-bg);
    min-height: 100vh;
    overflow-x: hidden;
}

/* ─── Типографика ─── */
h1, h2, h3, h4, h5, h6 {
    font-family: var(--font-heading);
    font-weight: var(--font-weight-bold);
    line-height: var(--line-height-tight);
    color: var(--color-text);
}

p {
    line-height: var(--line-height-normal);
}

/* ─── Ссылки ─── */
a {
    color: inherit;
    text-decoration: none;
    transition: var(--transition-base);
}

a:hover {
    color: var(--color-primary);
}

a:focus-visible {
    outline: 2px solid var(--color-primary);
    outline-offset: 3px;
    border-radius: var(--radius-xs);
}

/* ─── Списки ─── */
ul, ol {
    list-style: none;
}

/* ─── Изображения ─── */
img, picture, video, canvas, svg {
    display: block;
    max-width: 100%;
}

img {
    height: auto;
}

/* ─── Формы ─── */
input, button, textarea, select, optgroup {
    font: inherit;
    color: inherit;
}

button {
    cursor: pointer;
    background: none;
    border: none;
}

textarea {
    resize: vertical;
}

fieldset {
    border: none;
}

/* ─── Таблицы ─── */
table {
    border-collapse: collapse;
    border-spacing: 0;
}

/* ─── HR ─── */
hr {
    border: none;
    border-top: 1px solid var(--color-border);
    margin: var(--space-md) 0;
}

/* ─── Выделение ─── */
::selection {
    background-color: var(--color-primary);
    color: #fff;
}

/* ─── Скроллбар ─── */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: var(--color-bg);
}

::-webkit-scrollbar-thumb {
    background: var(--color-primary);
    border-radius: var(--radius-full);
}

::-webkit-scrollbar-thumb:hover {
    background: var(--color-primary-dark);
}

/* ─── Focus visible глобально ─── */
:focus {
    outline: none;
}

:focus-visible {
    outline: 2px solid var(--color-primary);
    outline-offset: 3px;
}

/* ─── Убираем стрелки у number-input ─── */
input[type='number']::-webkit-inner-spin-button,
input[type='number']::-webkit-outer-spin-button {
    -webkit-appearance: none;
    appearance: none;
}

input[type='number'] {
    -moz-appearance: textfield;
    appearance: textfield;
}

/* ─── Placeholder ─── */
::placeholder {
    color: var(--color-text-light);
    opacity: 1;
}
```

---

## Файл 3: `public/assets/css/main.css`

```css
/**
 * Основные стили: layout, секции, компоненты главной
 * Импортирует variables.css и reset.css
 * 
 * Путь: /public/assets/css/main.css
 */

@import url('variables.css');
@import url('reset.css');

/* ═══════════════════════════════════════
   LAYOUT — КОНТЕЙНЕР, СЕТКИ
═══════════════════════════════════════ */

.container {
    width: 100%;
    max-width: var(--container-width);
    margin-inline: auto;
    padding-inline: var(--container-pad);
}

.section {
    padding-block: var(--space-3xl);
}

.section--sm {
    padding-block: var(--space-xl);
}

.section--lg {
    padding-block: var(--space-4xl);
}

/* Светлый и тёмный фоны секций */
.section--light {
    background-color: var(--color-bg-section);
}

.section--white {
    background-color: var(--color-bg-white);
}

.section--dark {
    background-color: var(--color-primary-dark);
    color: #fff;
}

.section--primary {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
    color: #fff;
}

/* Заголовок секции */
.section-header {
    text-align: center;
    margin-bottom: var(--space-2xl);
}

.section-header--left {
    text-align: left;
}

.section-title {
    font-size: var(--font-size-h2);
    font-family: var(--font-heading);
    font-weight: var(--font-weight-bold);
    color: var(--color-text);
    margin-bottom: var(--space-sm);
    line-height: var(--line-height-tight);
}

.section--dark .section-title,
.section--primary .section-title {
    color: #fff;
}

.section-subtitle {
    font-size: var(--font-size-lg);
    color: var(--color-text-muted);
    max-width: 640px;
    margin-inline: auto;
    line-height: var(--line-height-relaxed);
}

.section--dark .section-subtitle,
.section--primary .section-subtitle {
    color: rgba(255, 255, 255, 0.75);
}

.section-subtitle--left {
    margin-inline: 0;
}

/* Декоративная линия под заголовком */
.section-title::after {
    content: '';
    display: block;
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, var(--color-primary), var(--color-accent));
    border-radius: var(--radius-full);
    margin: var(--space-xs) auto 0;
}

.section-header--left .section-title::after {
    margin-left: 0;
}

.section--dark .section-title::after,
.section--primary .section-title::after {
    background: linear-gradient(90deg, var(--color-accent), #fff);
}

/* ═══════════════════════════════════════
   ШАПКА САЙТА
═══════════════════════════════════════ */

.header {
    position: sticky;
    top: 0;
    z-index: var(--z-sticky);
    height: var(--header-height);
    background: rgba(245, 239, 230, 0.95);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--color-border);
    transition: var(--transition-base);
}

.header.scrolled {
    box-shadow: var(--shadow-md);
}

.header__inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 100%;
    gap: var(--space-md);
}

/* Логотип */
.logo {
    display: flex;
    align-items: center;
    gap: var(--space-xs);
    flex-shrink: 0;
    text-decoration: none;
}

.logo__icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.logo__text {
    display: flex;
    flex-direction: column;
    line-height: 1;
}

.logo__name {
    font-family: var(--font-heading);
    font-size: 15px;
    font-weight: var(--font-weight-bold);
    color: var(--color-primary);
    letter-spacing: -0.3px;
}

.logo__slogan {
    font-size: 10px;
    color: var(--color-text-muted);
    font-weight: var(--font-weight-medium);
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

/* Навигация */
.nav {
    display: flex;
    align-items: center;
    gap: var(--space-xs);
}

.nav__link {
    padding: 8px 14px;
    border-radius: var(--radius-md);
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    color: var(--color-text-muted);
    transition: var(--transition-base);
    white-space: nowrap;
}

.nav__link:hover {
    color: var(--color-primary);
    background-color: var(--color-accent-light);
}

.nav__link.active {
    color: var(--color-primary);
    background-color: var(--color-accent-light);
}

/* Кнопка заказа в шапке */
.header__cta {
    flex-shrink: 0;
}

/* Бургер-кнопка (мобильная) */
.burger {
    display: none;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 40px;
    height: 40px;
    gap: 6px;
    border-radius: var(--radius-sm);
    transition: var(--transition-base);
}

.burger:hover {
    background-color: var(--color-accent-light);
}

.burger__line {
    display: block;
    width: 22px;
    height: 2px;
    background-color: var(--color-text);
    border-radius: var(--radius-full);
    transition: var(--transition-base);
}

.burger.open .burger__line:nth-child(1) {
    transform: translateY(8px) rotate(45deg);
}

.burger.open .burger__line:nth-child(2) {
    opacity: 0;
    transform: scaleX(0);
}

.burger.open .burger__line:nth-child(3) {
    transform: translateY(-8px) rotate(-45deg);
}

/* Мобильное меню */
.mobile-menu {
    display: none;
    position: fixed;
    inset: var(--header-height) 0 0 0;
    background: var(--color-bg);
    z-index: var(--z-overlay);
    padding: var(--space-md);
    flex-direction: column;
    gap: var(--space-xs);
    overflow-y: auto;
    transform: translateX(100%);
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.mobile-menu.open {
    transform: translateX(0);
}

.mobile-menu__link {
    display: block;
    padding: var(--space-sm) var(--space-md);
    border-radius: var(--radius-md);
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: var(--color-text);
    transition: var(--transition-base);
    border: 1px solid transparent;
}

.mobile-menu__link:hover,
.mobile-menu__link.active {
    color: var(--color-primary);
    background-color: var(--color-accent-light);
    border-color: var(--color-border);
}

.mobile-menu__cta {
    margin-top: var(--space-sm);
}

/* ═══════════════════════════════════════
   КНОПКИ
═══════════════════════════════════════ */

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-xs);
    padding: 14px 28px;
    border-radius: var(--radius-md);
    font-family: var(--font-body);
    font-size: var(--font-size-base);
    font-weight: var(--font-weight-semibold);
    line-height: 1;
    text-decoration: none;
    cursor: pointer;
    border: 2px solid transparent;
    transition: var(--transition-base);
    white-space: nowrap;
    position: relative;
    overflow: hidden;
    user-select: none;
}

.btn::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0);
    transition: var(--transition-base);
}

.btn:hover::before {
    background: rgba(255, 255, 255, 0.1);
}

/* Primary */
.btn--primary {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
    color: #fff;
    border-color: transparent;
    box-shadow: var(--shadow-sm);
}

.btn--primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: #fff;
}

.btn--primary:active {
    transform: translateY(0);
    box-shadow: var(--shadow-xs);
}

/* Accent / Golden */
.btn--accent {
    background: linear-gradient(135deg, var(--color-accent) 0%, var(--color-accent-dark) 100%);
    color: var(--color-text);
    border-color: transparent;
    box-shadow: var(--shadow-accent-sm);
}

.btn--accent:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-accent-md);
    color: var(--color-text);
}

.btn--accent:active {
    transform: translateY(0);
}

/* Outline Primary */
.btn--outline {
    background: transparent;
    color: var(--color-primary);
    border-color: var(--color-primary);
}

.btn--outline:hover {
    background: var(--color-primary);
    color: #fff;
    transform: translateY(-2px);
}

/* Outline White (на тёмном фоне) */
.btn--outline-white {
    background: transparent;
    color: #fff;
    border-color: rgba(255, 255, 255, 0.6);
}

.btn--outline-white:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: #fff;
    color: #fff;
}

/* Ghost */
.btn--ghost {
    background: transparent;
    color: var(--color-primary);
    border-color: transparent;
    padding-inline: var(--space-sm);
}

.btn--ghost:hover {
    background: var(--color-accent-light);
    color: var(--color-primary);
}

/* Размеры */
.btn--sm {
    padding: 10px 20px;
    font-size: var(--font-size-sm);
}

.btn--lg {
    padding: 18px 36px;
    font-size: var(--font-size-lg);
    border-radius: 14px;
}

.btn--xl {
    padding: 20px 44px;
    font-size: var(--font-size-xl);
    border-radius: 16px;
}

.btn--full {
    width: 100%;
}

.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

/* ═══════════════════════════════════════
   СЕКЦИЯ HERO
═══════════════════════════════════════ */

.hero {
    position: relative;
    min-height: calc(100vh - var(--header-height));
    display: flex;
    align-items: center;
    background: linear-gradient(145deg, var(--color-primary-dark) 0%, var(--color-primary) 50%, #A83255 100%);
    overflow: hidden;
    padding-block: var(--space-3xl);
}

/* Декоративные ноты */
.hero__decor {
    position: absolute;
    inset: 0;
    pointer-events: none;
    overflow: hidden;
}

.hero__note {
    position: absolute;
    font-size: 80px;
    opacity: 0.04;
    animation: float 8s ease-in-out infinite;
    user-select: none;
}

.hero__note:nth-child(1) { top: 10%; left: 5%;  animation-delay: 0s;    font-size: 100px; }
.hero__note:nth-child(2) { top: 60%; left: 3%;  animation-delay: 1.5s;  font-size: 60px;  }
.hero__note:nth-child(3) { top: 20%; right: 5%; animation-delay: 3s;    font-size: 90px;  }
.hero__note:nth-child(4) { top: 70%; right: 8%; animation-delay: 0.5s;  font-size: 70px;  }
.hero__note:nth-child(5) { top: 45%; left: 15%; animation-delay: 2s;    font-size: 50px;  }
.hero__note:nth-child(6) { top: 30%; right: 20%; animation-delay: 4s;   font-size: 55px;  }
.hero__note:nth-child(7) { bottom: 15%; left: 30%; animation-delay: 1s; font-size: 75px;  }

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(-5deg); }
    50%       { transform: translateY(-20px) rotate(5deg); }
}

/* Декоративные круги */
.hero__circle {
    position: absolute;
    border-radius: 50%;
    background: rgba(212, 165, 116, 0.06);
    pointer-events: none;
}

.hero__circle--1 {
    width: 600px;
    height: 600px;
    top: -200px;
    right: -100px;
}

.hero__circle--2 {
    width: 400px;
    height: 400px;
    bottom: -150px;
    left: -100px;
}

/* Контент hero */
.hero__content {
    position: relative;
    z-index: 1;
    text-align: center;
    max-width: 800px;
    margin-inline: auto;
}

.hero__pretitle {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(212, 165, 116, 0.2);
    border: 1px solid rgba(212, 165, 116, 0.4);
    color: var(--color-accent);
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 8px 16px;
    border-radius: var(--radius-full);
    margin-bottom: var(--space-md);
}

.hero__title {
    font-size: clamp(40px, 7vw, 72px);
    font-family: var(--font-heading);
    font-weight: var(--font-weight-black);
    color: #fff;
    line-height: 1.05;
    margin-bottom: var(--space-md);
    letter-spacing: -1px;
}

.hero__title span {
    background: linear-gradient(135deg, var(--color-accent) 0%, #F8D49A 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero__subtitle {
    font-size: clamp(16px, 2.5vw, 20px);
    color: rgba(255, 255, 255, 0.8);
    line-height: var(--line-height-relaxed);
    max-width: 600px;
    margin: 0 auto var(--space-xl);
}

.hero__actions {
    display: flex;
    gap: var(--space-sm);
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: var(--space-2xl);
}

/* Счётчики в hero */
.hero__stats {
    display: flex;
    gap: var(--space-xl);
    justify-content: center;
    flex-wrap: wrap;
    padding-top: var(--space-xl);
    border-top: 1px solid rgba(255, 255, 255, 0.15);
}

.hero__stat {
    text-align: center;
}

.hero__stat-value {
    display: block;
    font-family: var(--font-heading);
    font-size: clamp(22px, 3.5vw, 32px);
    font-weight: var(--font-weight-bold);
    color: var(--color-accent);
    line-height: 1;
    margin-bottom: 4px;
}

.hero__stat-label {
    font-size: var(--font-size-sm);
    color: rgba(255, 255, 255, 0.65);
    white-space: nowrap;
}

/* ═══════════════════════════════════════
   СЕКЦИЯ "КАК ЭТО РАБОТАЕТ"
═══════════════════════════════════════ */

.steps {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-lg);
    position: relative;
}

/* Соединительная линия между шагами */
.steps::before {
    content: '';
    position: absolute;
    top: 48px;
    left: calc(16.66% + var(--space-md));
    right: calc(16.66% + var(--space-md));
    height: 2px;
    background: linear-gradient(90deg, var(--color-primary), var(--color-accent), var(--color-primary));
    opacity: 0.25;
}

.step-card {
    position: relative;
    text-align: center;
    padding: var(--space-xl) var(--space-md);
    background: var(--color-bg-white);
    border-radius: var(--radius-xl);
    border: 1px solid var(--color-border);
    box-shadow: var(--shadow-sm);
    transition: var(--transition-base);
}

.step-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-md);
    border-color: var(--color-accent);
}

.step-card__number {
    position: absolute;
    top: -16px;
    left: 50%;
    transform: translateX(-50%);
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-bold);
    font-family: var(--font-heading);
}

.step-card__icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--color-accent-light), var(--color-border));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    margin: 0 auto var(--space-md);
    transition: var(--transition-base);
}

.step-card:hover .step-card__icon {
    background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
    transform: scale(1.1) rotate(5deg);
}

.step-card__title {
    font-family: var(--font-heading);
    font-size: var(--font-size-xl);
    font-weight: var(--font-weight-bold);
    color: var(--color-text);
    margin-bottom: var(--space-xs);
}

.step-card__desc {
    font-size: var(--font-size-base);
    color: var(--color-text-muted);
    line-height: var(--line-height-relaxed);
}

/* ═══════════════════════════════════════
   СЕКЦИЯ ТРЕКОВЫХ КАРТОЧЕК
═══════════════════════════════════════ */

.tracks-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-lg);
}

.track-card {
    background: var(--color-bg-white);
    border-radius: var(--radius-xl);
    border: 1px solid var(--color-border);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: var(--transition-base);
}

.track-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
    border-color: var(--color-accent);
}

.track-card__cover {
    position: relative;
    aspect-ratio: 16 / 9;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent-dark) 100%);
    overflow: hidden;
}

.track-card__cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition-slow);
}

.track-card:hover .track-card__cover img {
    transform: scale(1.05);
}

/* Градиент по категории (если нет обложки) */
.track-card__cover--wedding     { background: linear-gradient(135deg, #8B1E3F, #D4A574); }
.track-card__cover--birthday    { background: linear-gradient(135deg, #6B1230, #A83255); }
.track-card__cover--anniversary { background: linear-gradient(135deg, #4A2040, #8B1E3F); }
.track-card__cover--corporate   { background: linear-gradient(135deg, #1E3F8B, #3F6BB5); }
.track-card__cover--holiday     { background: linear-gradient(135deg, #1E5C3F, #4A7C59); }
.track-card__cover--children    { background: linear-gradient(135deg, #7C3F1E, #D4A574); }

.track-card__cover-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 48px;
    opacity: 0.5;
}

.track-card__play-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.2);
    opacity: 0;
    transition: var(--transition-base);
}

.track-card:hover .track-card__play-overlay {
    opacity: 1;
}

.track-card__play-btn {
    width: 56px;
    height: 56px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary);
    font-size: 20px;
    transition: var(--transition-spring);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    border: none;
    cursor: pointer;
}

.track-card__play-btn:hover {
    transform: scale(1.15);
    background: #fff;
}

.track-card__body {
    padding: var(--space-md);
}

.track-card__category {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: var(--color-accent-light);
    color: var(--color-primary);
    font-size: var(--font-size-xs);
    font-weight: var(--font-weight-semibold);
    padding: 4px 10px;
    border-radius: var(--radius-full);
    margin-bottom: var(--space-xs);
    letter-spacing: 0.3px;
}

.track-card__title {
    font-family: var(--font-heading);
    font-size: 17px;
    font-weight: var(--font-weight-bold);
    color: var(--color-text);
    margin-bottom: var(--space-xs);
    line-height: 1.3;
}

.track-card__meta {
    display: flex;
    align-items: center;
    gap: var(--space-xs);
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
    margin-bottom: var(--space-sm);
}

.track-card__meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Аудиоплеер в карточке */
.track-player {
    display: flex;
    align-items: center;
    gap: var(--space-xs);
    padding: 10px 12px;
    background: var(--color-bg);
    border-radius: var(--radius-sm);
    border: 1px solid var(--color-border);
}

.track-player__btn {
    width: 36px;
    height: 36px;
    background: var(--color-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 14px;
    flex-shrink: 0;
    border: none;
    cursor: pointer;
    transition: var(--transition-base);
}

.track-player__btn:hover {
    background: var(--color-primary-dark);
    transform: scale(1.1);
}

.track-player__btn.playing {
    background: var(--color-accent-dark);
}

.track-player__progress-wrap {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.track-player__progress {
    width: 100%;
    height: 4px;
    background: var(--color-border);
    border-radius: var(--radius-full);
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    outline: none;
    position: relative;
}

.track-player__progress::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--color-primary);
    cursor: pointer;
    box-shadow: 0 1px 4px rgba(0,0,0,0.2);
}

.track-player__progress::-moz-range-thumb {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--color-primary);
    cursor: pointer;
    border: none;
}

.track-player__time {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    color: var(--color-text-muted);
    font-variant-numeric: tabular-nums;
}

.track-player__volume {
    width: 60px;
    height: 4px;
    background: var(--color-border);
    border-radius: var(--radius-full);
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    outline: none;
    flex-shrink: 0;
}

.track-player__volume::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--color-accent-dark);
    cursor: pointer;
}

.tracks-grid__more {
    text-align: center;
    margin-top: var(--space-xl);
}

/* ═══════════════════════════════════════
   СЕКЦИЯ ПРЕИМУЩЕСТВ
═══════════════════════════════════════ */

.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-md);
}

.feature-card {
    padding: var(--space-lg) var(--space-md);
    background: var(--color-bg-white);
    border-radius: var(--radius-xl);
    border: 1px solid var(--color-border);
    transition: var(--transition-base);
}

.feature-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
    border-color: var(--color-primary);
}

.feature-card__icon {
    font-size: 36px;
    margin-bottom: var(--space-sm);
    display: block;
}

.feature-card__title {
    font-family: var(--font-heading);
    font-size: 16px;
    font-weight: var(--font-weight-bold);
    color: var(--color-text);
    margin-bottom: var(--space-xs);
}

.feature-card__desc {
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
    line-height: var(--line-height-relaxed);
}

/* ═══════════════════════════════════════
   СЕКЦИЯ ОТЗЫВОВ
═══════════════════════════════════════ */

.reviews-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-lg);
}

.review-card {
    padding: var(--space-lg);
    background: var(--color-bg-white);
    border-radius: var(--radius-xl);
    border: 1px solid var(--color-border);
    box-shadow: var(--shadow-sm);
    transition: var(--transition-base);
    position: relative;
}

.review-card::before {
    content: '\201C';
    position: absolute;
    top: -10px;
    left: var(--space-md);
    font-family: var(--font-accent);
    font-size: 80px;
    color: var(--color-primary);
    opacity: 0.15;
    line-height: 1;
}

.review-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
}

.review-card__header {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    margin-bottom: var(--space-md);
}

.review-card__avatar {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-family: var(--font-heading);
    font-size: 20px;
    font-weight: var(--font-weight-bold);
    flex-shrink: 0;
}

.review-card__author {
    flex: 1;
}

.review-card__name {
    font-weight: var(--font-weight-bold);
    color: var(--color-text);
    font-size: var(--font-size-base);
}

.review-card__city {
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
}

.review-card__stars {
    color: var(--color-accent);
    font-size: 16px;
    letter-spacing: 2px;
    margin-bottom: var(--space-xs);
}

.review-card__text {
    font-size: var(--font-size-base);
    color: var(--color-text-muted);
    line-height: var(--line-height-relaxed);
    margin-bottom: var(--space-sm);
}

.review-card__tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: var(--color-accent-light);
    color: var(--color-primary);
    font-size: var(--font-size-xs);
    font-weight: var(--font-weight-semibold);
    padding: 4px 10px;
    border-radius: var(--radius-full);
}

/* ═══════════════════════════════════════
   СЕКЦИЯ CTA (финальная)
═══════════════════════════════════════ */

.cta-section {
    background: linear-gradient(145deg, var(--color-primary-dark) 0%, var(--color-primary) 100%);
    position: relative;
    overflow: hidden;
    text-align: center;
    padding-block: var(--space-3xl);
}

.cta-section__decor {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.cta-section__circle {
    position: absolute;
    border-radius: 50%;
    border: 1px solid rgba(212, 165, 116, 0.15);
}

.cta-section__circle--1 { width: 300px; height: 300px; top: -100px; right: 5%; }
.cta-section__circle--2 { width: 200px; height: 200px; bottom: -60px; left: 10%; }
.cta-section__circle--3 { width: 150px; height: 150px; top: 30%; left: 5%; }

.cta-section__content {
    position: relative;
    z-index: 1;
    max-width: 600px;
    margin-inline: auto;
}

.cta-section__title {
    font-family: var(--font-heading);
    font-size: clamp(28px, 4vw, 44px);
    font-weight: var(--font-weight-bold);
    color: #fff;
    margin-bottom: var(--space-md);
    line-height: var(--line-height-tight);
}

.cta-section__subtitle {
    font-size: var(--font-size-lg);
    color: rgba(255, 255, 255, 0.75);
    margin-bottom: var(--space-xl);
    line-height: var(--line-height-relaxed);
}

.cta-section__actions {
    display: flex;
    gap: var(--space-sm);
    justify-content: center;
    flex-wrap: wrap;
}

/* ═══════════════════════════════════════
   СЕКЦИЯ FAQ
═══════════════════════════════════════ */

.faq-list {
    max-width: 760px;
    margin-inline: auto;
    display: flex;
    flex-direction: column;
    gap: var(--space-xs);
}

.faq-item {
    background: var(--color-bg-white);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: var(--transition-base);
}

.faq-item.open {
    border-color: var(--color-primary);
    box-shadow: var(--shadow-sm);
}

.faq-item__btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--space-md);
    padding: var(--space-md) var(--space-lg);
    text-align: left;
    background: none;
    border: none;
    cursor: pointer;
    font-size: var(--font-size-base);
    font-weight: var(--font-weight-semibold);
    color: var(--color-text);
    transition: var(--transition-base);
}

.faq-item__btn:hover {
    color: var(--color-primary);
}

.faq-item.open .faq-item__btn {
    color: var(--color-primary);
}

.faq-item__icon {
    width: 28px;
    height: 28px;
    background: var(--color-accent-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 16px;
    color: var(--color-primary);
    transition: var(--transition-base);
}

.faq-item.open .faq-item__icon {
    background: var(--color-primary);
    color: #fff;
    transform: rotate(45deg);
}

.faq-item__body {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease, padding 0.4s ease;
}

.faq-item.open .faq-item__body {
    max-height: 500px;
}

.faq-item__text {
    padding: 0 var(--space-lg) var(--space-md);
    font-size: var(--font-size-base);
    color: var(--color-text-muted);
    line-height: var(--line-height-relaxed);
}

/* ═══════════════════════════════════════
   ПОДВАЛ
═══════════════════════════════════════ */

.footer {
    background: var(--color-text);
    color: rgba(255, 255, 255, 0.7);
    padding-block: var(--space-2xl);
}

.footer__grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr 1fr;
    gap: var(--space-xl);
    padding-bottom: var(--space-xl);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.footer__brand {
    display: flex;
    flex-direction: column;
    gap: var(--space-sm);
}

.footer__logo {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}

.footer__logo-icon {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.footer__logo-name {
    font-family: var(--font-heading);
    font-size: 14px;
    font-weight: var(--font-weight-bold);
    color: #fff;
}

.footer__desc {
    font-size: var(--font-size-sm);
    line-height: var(--line-height-relaxed);
}

.footer__social {
    display: flex;
    gap: 10px;
    margin-top: var(--space-xs);
}

.footer__social-link {
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.08);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    transition: var(--transition-base);
    text-decoration: none;
}

.footer__social-link:hover {
    background: var(--color-primary);
    transform: translateY(-2px);
}

.footer__col-title {
    font-family: var(--font-heading);
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-bold);
    color: #fff;
    margin-bottom: var(--space-md);
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.footer__links {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.footer__link {
    font-size: var(--font-size-sm);
    color: rgba(255, 255, 255, 0.6);
    text-decoration: none;
    transition: var(--transition-base);
}

.footer__link:hover {
    color: var(--color-accent);
    padding-left: 4px;
}

.footer__contacts {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.footer__contact-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: var(--font-size-sm);
    color: rgba(255, 255, 255, 0.6);
}

.footer__contact-icon {
    width: 20px;
    text-align: center;
    flex-shrink: 0;
}

.footer__bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: var(--space-lg);
    flex-wrap: wrap;
    gap: var(--space-sm);
}

.footer__copy {
    font-size: var(--font-size-sm);
    color: rgba(255, 255, 255, 0.4);
}

.footer__copy-links {
    display: flex;
    gap: var(--space-md);
}

.footer__copy-link {
    font-size: var(--font-size-sm);
    color: rgba(255, 255, 255, 0.4);
    text-decoration: none;
    transition: var(--transition-base);
}

.footer__copy-link:hover {
    color: var(--color-accent);
}

/* ═══════════════════════════════════════
   SCROLL REVEAL (анимация появления)
═══════════════════════════════════════ */

.reveal {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}

.reveal.revealed {
    opacity: 1;
    transform: translateY(0);
}

.reveal--left {
    transform: translateX(-30px);
}

.reveal--right {
    transform: translateX(30px);
}

.reveal--left.revealed,
.reveal--right.revealed {
    transform: translateX(0);
}

.reveal--delay-1 { transition-delay: 0.1s; }
.reveal--delay-2 { transition-delay: 0.2s; }
.reveal--delay-3 { transition-delay: 0.3s; }
.reveal--delay-4 { transition-delay: 0.4s; }
.reveal--delay-5 { transition-delay: 0.5s; }
.reveal--delay-6 { transition-delay: 0.6s; }

/* ═══════════════════════════════════════
   БЭЙДЖИ
═══════════════════════════════════════ */

.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: var(--font-size-xs);
    font-weight: var(--font-weight-semibold);
    letter-spacing: 0.3px;
}

.badge--primary {
    background: var(--color-primary);
    color: #fff;
}

.badge--accent {
    background: var(--color-accent-light);
    color: var(--color-accent-dark);
}

.badge--success {
    background: var(--color-success-light);
    color: var(--color-success);
}

.badge--popular {
    background: linear-gradient(135deg, var(--color-accent), var(--color-accent-dark));
    color: #fff;
    font-size: 11px;
    padding: 3px 10px;
}

/* ═══════════════════════════════════════
   ХЛЕБНЫЕ КРОШКИ
═══════════════════════════════════════ */

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
    flex-wrap: wrap;
    padding-block: var(--space-md);
}

.breadcrumb__item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.breadcrumb__link {
    color: var(--color-primary);
    text-decoration: none;
}

.breadcrumb__link:hover {
    text-decoration: underline;
}

.breadcrumb__sep {
    color: var(--color-border);
}

/* ═══════════════════════════════════════
   ВСПОМОГАТЕЛЬНЫЕ КЛАССЫ
═══════════════════════════════════════ */

.text-center    { text-align: center; }
.text-left      { text-align: left; }
.text-right     { text-align: right; }

.text-primary   { color: var(--color-primary); }
.text-accent    { color: var(--color-accent-dark); }
.text-muted     { color: var(--color-text-muted); }
.text-white     { color: #fff; }

.mt-auto        { margin-top: auto; }
.mb-sm          { margin-bottom: var(--space-sm); }
.mb-md          { margin-bottom: var(--space-md); }
.mb-lg          { margin-bottom: var(--space-lg); }
.mb-xl          { margin-bottom: var(--space-xl); }

.flex           { display: flex; }
.flex-center    { display: flex; align-items: center; justify-content: center; }
.flex-between   { display: flex; align-items: center; justify-content: space-between; }
.gap-sm         { gap: var(--space-sm); }
.gap-md         { gap: var(--space-md); }

.visually-hidden {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* ─── Разделитель-заглушка (для dev) ─── */
.placeholder-block {
    background: var(--color-accent-light);
    border: 2px dashed var(--color-border);
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    text-align: center;
    color: var(--color-text-muted);
    font-style: italic;
}
```

---

## Файл 4: `public/assets/css/components.css`

```css
/**
 * Компоненты: формы, уведомления, спиннеры, модалки
 * 
 * Путь: /public/assets/css/components.css
 */

/* ═══════════════════════════════════════
   ФОРМЫ
═══════════════════════════════════════ */

.form-group {
    display: flex;
    flex-direction: column;
    gap: var(--space-xs);
}

.form-label {
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    color: var(--color-text);
}

.form-label span.required {
    color: var(--color-primary);
    margin-left: 3px;
}

.form-input,
.form-textarea,
.form-select {
    width: 100%;
    padding: 12px 16px;
    background: var(--color-bg-white);
    border: 1.5px solid var(--color-border);
    border-radius: var(--radius-sm);
    font-family: var(--font-body);
    font-size: var(--font-size-base);
    color: var(--color-text);
    transition: var(--transition-base);
    outline: none;
}

.form-input:hover,
.form-textarea:hover,
.form-select:hover {
    border-color: var(--color-accent);
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(139, 30, 63, 0.12);
    background: var(--color-bg-white);
}

.form-input.error,
.form-textarea.error,
.form-select.error {
    border-color: var(--color-error);
    box-shadow: 0 0 0 3px rgba(179, 57, 81, 0.12);
}

.form-input.success,
.form-textarea.success,
.form-select.success {
    border-color: var(--color-success);
    box-shadow: 0 0 0 3px rgba(74, 124, 89, 0.12);
}

.form-textarea {
    min-height: 120px;
    resize: vertical;
}

.form-hint {
    font-size: var(--font-size-xs);
    color: var(--color-text-muted);
}

.form-error {
    font-size: var(--font-size-xs);
    color: var(--color-error);
    display: flex;
    align-items: center;
    gap: 4px;
}

.form-error::before {
    content: '✕';
    width: 14px;
    height: 14px;
    background: var(--color-error);
    color: #fff;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    flex-shrink: 0;
}

.form-success {
    font-size: var(--font-size-xs);
    color: var(--color-success);
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Radio / Checkbox */
.form-radio-group,
.form-check-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.form-radio-group--row,
.form-check-group--row {
    flex-direction: row;
    flex-wrap: wrap;
}

.form-radio,
.form-check {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    user-select: none;
}

.form-radio__input,
.form-check__input {
    appearance: none;
    -webkit-appearance: none;
    width: 20px;
    height: 20px;
    border: 2px solid var(--color-border);
    background: var(--color-bg-white);
    cursor: pointer;
    transition: var(--transition-base);
    flex-shrink: 0;
}

.form-radio__input {
    border-radius: 50%;
}

.form-check__input {
    border-radius: 5px;
}

.form-radio__input:checked {
    border-color: var(--color-primary);
    background: var(--color-primary);
    box-shadow: inset 0 0 0 4px var(--color-bg-white);
}

.form-check__input:checked {
    border-color: var(--color-primary);
    background: var(--color-primary);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%23fff' d='M13.3 4.3l-6.6 6.6-3.3-3.3 1.4-1.4 1.9 1.9 5.2-5.2z'/%3E%3C/svg%3E");
    background-size: 12px;
    background-position: center;
    background-repeat: no-repeat;
}

.form-radio__input:hover,
.form-check__input:hover {
    border-color: var(--color-primary);
}

.form-radio__label,
.form-check__label {
    font-size: var(--font-size-base);
    color: var(--color-text);
    cursor: pointer;
}

/* Большие radio-карточки (для шага повода) */
.radio-card {
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

.radio-card:hover {
    border-color: var(--color-primary);
    background: var(--color-accent-light);
    transform: translateY(-2px);
}

.radio-card.selected {
    border-color: var(--color-primary);
    background: var(--color-accent-light);
    box-shadow: var(--shadow-sm);
}

.radio-card__input {
    display: none;
}

.radio-card__icon {
    font-size: 32px;
    line-height: 1;
}

.radio-card__label {
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    color: var(--color-text);
}

/* Тарифные radio-карточки */
.tariff-card {
    padding: var(--space-lg);
    background: var(--color-bg-white);
    border: 2px solid var(--color-border);
    border-radius: var(--radius-xl);
    cursor: pointer;
    transition: var(--transition-base);
    position: relative;
}

.tariff-card:hover {
    border-color: var(--color-primary);
    box-shadow: var(--shadow-md);
    transform: translateY(-3px);
}

.tariff-card.selected {
    border-color: var(--color-primary);
    box-shadow: var(--shadow-md);
}

.tariff-card.featured {
    border-color: var(--color-accent-dark);
}

.tariff-card__popular {
    position: absolute;
    top: -12px;
    right: var(--space-md);
}

.tariff-card__price {
    font-family: var(--font-heading);
    font-size: 28px;
    font-weight: var(--font-weight-bold);
    color: var(--color-primary);
    margin-bottom: var(--space-sm);
}

.tariff-card__name {
    font-weight: var(--font-weight-bold);
    font-size: var(--font-size-lg);
    color: var(--color-text);
    margin-bottom: var(--space-sm);
}

.tariff-card__features {
    display: flex;
    flex-direction: column;
    gap: 8px;
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
}

.tariff-card__feature {
    display: flex;
    align-items: flex-start;
    gap: 6px;
}

.tariff-card__feature::before {
    content: '✅';
    font-size: 12px;
    flex-shrink: 0;
    line-height: 1.6;
}

/* Маска телефона */
.form-phone-wrap {
    position: relative;
}

.form-phone-prefix {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-text-muted);
    font-size: var(--font-size-base);
    font-weight: var(--font-weight-medium);
    pointer-events: none;
    z-index: 1;
}

.form-input--phone {
    padding-left: 42px;
}

/* Счётчик символов */
.form-char-count {
    font-size: var(--font-size-xs);
    color: var(--color-text-light);
    text-align: right;
}

.form-char-count.warning {
    color: var(--color-warning);
}

/* ═══════════════════════════════════════
   WIZARD (многошаговая форма)
═══════════════════════════════════════ */

.wizard {
    max-width: 760px;
    margin-inline: auto;
}

.wizard__progress {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    margin-bottom: var(--space-2xl);
    position: relative;
}

.wizard__step-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    position: relative;
    z-index: 1;
}

.wizard__step-dot {
    width: 40px;
    height: 40px;
    background: var(--color-border);
    border: 2px solid var(--color-border);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-bold);
    color: var(--color-text-muted);
    transition: var(--transition-base);
    font-family: var(--font-heading);
}

.wizard__step-indicator.active .wizard__step-dot {
    background: var(--color-primary);
    border-color: var(--color-primary);
    color: #fff;
    box-shadow: 0 0 0 4px rgba(139, 30, 63, 0.2);
}

.wizard__step-indicator.done .wizard__step-dot {
    background: var(--color-success);
    border-color: var(--color-success);
    color: #fff;
}

.wizard__step-indicator.done .wizard__step-dot::before {
    content: '✓';
}

.wizard__step-label {
    font-size: 11px;
    font-weight: var(--font-weight-semibold);
    color: var(--color-text-muted);
    white-space: nowrap;
    text-align: center;
}

.wizard__step-indicator.active .wizard__step-label {
    color: var(--color-primary);
}

.wizard__step-line {
    flex: 1;
    height: 2px;
    background: var(--color-border);
    margin-bottom: 22px;
    transition: var(--transition-slow);
    min-width: 30px;
}

.wizard__step-line.done {
    background: linear-gradient(90deg, var(--color-success), var(--color-primary));
}

.wizard__header {
    text-align: center;
    margin-bottom: var(--space-xl);
}

.wizard__step-num {
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
    margin-bottom: 6px;
    font-weight: var(--font-weight-medium);
}

.wizard__step-title {
    font-family: var(--font-heading);
    font-size: var(--font-size-h3);
    font-weight: var(--font-weight-bold);
    color: var(--color-text);
}

.wizard__panel {
    display: none;
    animation: fadeIn 0.3s ease;
}

.wizard__panel.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}

.wizard__body {
    background: var(--color-bg-white);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-xl);
    padding: var(--space-xl);
    box-shadow: var(--shadow-sm);
    margin-bottom: var(--space-md);
}

.wizard__footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: var(--space-sm);
}

.wizard__footer-info {
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
}

/* ═══════════════════════════════════════
   УВЕДОМЛЕНИЯ / TOAST
═══════════════════════════════════════ */

.toast-container {
    position: fixed;
    bottom: var(--space-lg);
    right: var(--space-lg);
    z-index: var(--z-toast);
    display: flex;
    flex-direction: column;
    gap: var(--space-xs);
    max-width: 380px;
    width: calc(100vw - var(--space-lg) * 2);
}

.toast {
    display: flex;
    align-items: flex-start;
    gap: var(--space-sm);
    padding: var(--space-md);
    background: var(--color-bg-white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    border-left: 4px solid var(--color-primary);
    animation: slideInRight 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    transition: var(--transition-base);
}

.toast.removing {
    animation: slideOutRight 0.3s ease forwards;
}

@keyframes slideInRight {
    from { opacity: 0; transform: translateX(100%); }
    to   { opacity: 1; transform: translateX(0); }
}

@keyframes slideOutRight {
    from { opacity: 1; transform: translateX(0); }
    to   { opacity: 0; transform: translateX(100%); }
}

.toast--success { border-color: var(--color-success); }
.toast--error   { border-color: var(--color-error);   }
.toast--warning { border-color: var(--color-warning);  }
.toast--info    { border-color: var(--color-primary);  }

.toast__icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.toast--success .toast__icon { background: var(--color-success-light); }
.toast--error   .toast__icon { background: var(--color-error-light);   }
.toast--warning .toast__icon { background: var(--color-warning-light); }
.toast--info    .toast__icon { background: var(--color-accent-light);  }

.toast__content {
    flex: 1;
    min-width: 0;
}

.toast__title {
    font-weight: var(--font-weight-semibold);
    font-size: var(--font-size-sm);
    color: var(--color-text);
    margin-bottom: 2px;
}

.toast__message {
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
    line-height: 1.4;
}

.toast__close {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-text-muted);
    font-size: 16px;
    border-radius: var(--radius-xs);
    transition: var(--transition-fast);
    flex-shrink: 0;
    cursor: pointer;
    background: none;
    border: none;
}

.toast__close:hover {
    background: var(--color-bg);
    color: var(--color-text);
}

/* Прогресс-бар тоста */
.toast__progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    border-radius: 0 0 0 var(--radius-lg);
    background: currentColor;
    animation: toastProgress 4s linear forwards;
}

@keyframes toastProgress {
    from { width: 100%; }
    to   { width: 0%; }
}

/* ═══════════════════════════════════════
   СПИННЕР / ЗАГРУЗКА
═══════════════════════════════════════ */

.spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid rgba(139, 30, 63, 0.2);
    border-top-color: var(--color-primary);
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

.spinner--white {
    border-color: rgba(255, 255, 255, 0.3);
    border-top-color: #fff;
}

.spinner--sm {
    width: 14px;
    height: 14px;
}

.spinner--lg {
    width: 32px;
    height: 32px;
    border-width: 3px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loading-overlay {
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: inherit;
    z-index: 10;
    backdrop-filter: blur(2px);
}

/* ═══════════════════════════════════════
   КАРТОЧКИ ТАРИФОВ (pricing.php)
═══════════════════════════════════════ */

.pricing-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-lg);
    align-items: start;
}

.pricing-card {
    background: var(--color-bg-white);
    border: 2px solid var(--color-border);
    border-radius: var(--radius-2xl);
    padding: var(--space-xl);
    position: relative;
    transition: var(--transition-base);
}

.pricing-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-lg);
}

.pricing-card--featured {
    border-color: var(--color-primary);
    box-shadow: var(--shadow-md);
    transform: scale(1.03);
}

.pricing-card--featured:hover {
    transform: scale(1.03) translateY(-6px);
}

.pricing-card__badge {
    position: absolute;
    top: -14px;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
}

.pricing-card__name {
    font-family: var(--font-heading);
    font-size: var(--font-size-xl);
    font-weight: var(--font-weight-bold);
    color: var(--color-text);
    margin-bottom: var(--space-xs);
}

.pricing-card__price {
    font-family: var(--font-heading);
    font-size: 40px;
    font-weight: var(--font-weight-black);
    color: var(--color-primary);
    line-height: 1;
    margin-bottom: var(--space-sm);
}

.pricing-card__price span {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-normal);
    color: var(--color-text-muted);
}

.pricing-card__timing {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
    margin-bottom: var(--space-lg);
    padding-bottom: var(--space-lg);
    border-bottom: 1px solid var(--color-border);
}

.pricing-card__features {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: var(--space-xl);
}

.pricing-card__feature {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
    line-height: 1.5;
}

.pricing-card__feature-icon {
    flex-shrink: 0;
    font-size: 16px;
    margin-top: 1px;
}

/* ═══════════════════════════════════════
   СТАТУС-БЕЙДЖИ
═══════════════════════════════════════ */

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: var(--font-weight-semibold);
}

.status-badge::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    flex-shrink: 0;
}

.status-badge--new      { background: #E3F0FF; color: #1E5BAD; }
.status-badge--progress { background: var(--color-warning-light); color: var(--color-warning); }
.status-badge--review   { background: #EDE9FF; color: #5B21B6; }
.status-badge--done     { background: var(--color-success-light); color: var(--color-success); }
.status-badge--cancelled{ background: var(--color-error-light); color: var(--color-error); }

/* ═══════════════════════════════════════
   ПАГИНАЦИЯ
═══════════════════════════════════════ */

.pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding-top: var(--space-xl);
    flex-wrap: wrap;
}

.pagination__btn {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-sm);
    border: 1px solid var(--color-border);
    background: var(--color-bg-white);
    color: var(--color-text-muted);
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    text-decoration: none;
    transition: var(--transition-base);
    cursor: pointer;
}

.pagination__btn:hover {
    border-color: var(--color-primary);
    color: var(--color-primary);
    background: var(--color-accent-light);
}

.pagination__btn.active {
    background: var(--color-primary);
    border-color: var(--color-primary);
    color: #fff;
}

.pagination__btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.pagination__btn--wide {
    width: auto;
    padding-inline: var(--space-sm);
}

/* ═══════════════════════════════════════
   СЕКЦИЯ КОНТАКТОВ
═══════════════════════════════════════ */

.contacts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: var(--space-md);
}

.contact-card {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    padding: var(--space-md);
    background: var(--color-bg-white);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    text-decoration: none;
    transition: var(--transition-base);
    box-shadow: var(--shadow-xs);
}

.contact-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
    border-color: var(--color-primary);
    color: var(--color-text);
}

.contact-card__icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}

.contact-card--phone     .contact-card__icon { background: #E3F0FF; }
.contact-card--telegram  .contact-card__icon { background: #E3F4FF; }
.contact-card--whatsapp  .contact-card__icon { background: #E3FFE6; }
.contact-card--vk        .contact-card__icon { background: #E3EEFF; }
.contact-card--ok        .contact-card__icon { background: #FFF0E3; }
.contact-card--email     .contact-card__icon { background: var(--color-accent-light); }
.contact-card--sms       .contact-card__icon { background: #F0E3FF; }

.contact-card__info {
    flex: 1;
    min-width: 0;
}

.contact-card__type {
    font-size: var(--font-size-xs);
    font-weight: var(--font-weight-semibold);
    color: var(--color-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.contact-card__value {
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    color: var(--color-text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.contact-card__action {
    font-size: var(--font-size-xs);
    color: var(--color-primary);
    font-weight: var(--font-weight-semibold);
    flex-shrink: 0;
}

/* ═══════════════════════════════════════
   ФИЛЬТРЫ (portfolio)
═══════════════════════════════════════ */

.filter-bar {
    display: flex;
    gap: var(--space-xs);
    flex-wrap: wrap;
    padding-bottom: var(--space-xl);
}

.filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    border-radius: var(--radius-full);
    border: 1.5px solid var(--color-border);
    background: var(--color-bg-white);
    color: var(--color-text-muted);
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    cursor: pointer;
    transition: var(--transition-base);
    text-decoration: none;
    white-space: nowrap;
}

.filter-btn:hover {
    border-color: var(--color-primary);
    color: var(--color-primary);
    background: var(--color-accent-light);
}

.filter-btn.active {
    background: var(--color-primary);
    border-color: var(--color-primary);
    color: #fff;
}
```

---

## Файл 5: `public/assets/css/responsive.css`

```css
/**
 * Адаптивные стили (mobile-first подход)
 * Брейкпоинты: 480, 640, 768, 1024, 1200
 * 
 * Путь: /public/assets/css/responsive.css
 */

/* ═══════════════════════════════════════
   480px — Маленькие телефоны
═══════════════════════════════════════ */

@media (max-width: 480px) {
    .hero__stats {
        gap: var(--space-lg);
    }

    .hero__actions {
        flex-direction: column;
        align-items: stretch;
    }

    .hero__actions .btn {
        text-align: center;
    }

    .btn--xl {
        padding: 16px 28px;
        font-size: var(--font-size-lg);
    }

    .wizard__progress {
        gap: 0;
    }

    .wizard__step-label {
        display: none;
    }

    .wizard__body {
        padding: var(--space-md);
    }
}

/* ═══════════════════════════════════════
   640px — Мобильные
═══════════════════════════════════════ */

@media (max-width: 640px) {
    /* Шапка */
    .nav { display: none; }
    .header__cta { display: none; }
    .burger { display: flex; }
    .mobile-menu { display: flex; }

    /* Hero */
    .hero {
        min-height: 85vh;
        padding-block: var(--space-2xl);
    }

    .hero__pretitle {
        font-size: 11px;
    }

    /* Шаги */
    .steps {
        grid-template-columns: 1fr;
        gap: var(--space-md);
    }

    .steps::before {
        display: none;
    }

    /* Треки */
    .tracks-grid {
        grid-template-columns: 1fr;
    }

    /* Преимущества */
    .features-grid {
        grid-template-columns: 1fr;
    }

    /* Отзывы */
    .reviews-grid {
        grid-template-columns: 1fr;
    }

    /* Footer */
    .footer__grid {
        grid-template-columns: 1fr;
        gap: var(--space-xl);
    }

    .footer__bottom {
        flex-direction: column;
        text-align: center;
    }

    /* Pricing */
    .pricing-grid {
        grid-template-columns: 1fr;
    }

    .pricing-card--featured {
        transform: none;
        order: -1;
    }

    .pricing-card--featured:hover {
        transform: translateY(-6px);
    }

    /* Wizard */
    .wizard__step-label {
        font-size: 10px;
    }

    .wizard__step-dot {
        width: 32px;
        height: 32px;
        font-size: 12px;
    }

    .wizard__step-line {
        min-width: 16px;
    }

    /* Контакты */
    .contacts-grid {
        grid-template-columns: 1fr;
    }

    /* Фильтры */
    .filter-bar {
        gap: 6px;
    }

    .filter-btn {
        padding: 7px 14px;
        font-size: var(--font-size-xs);
    }

    /* Toast */
    .toast-container {
        bottom: var(--space-sm);
        right: var(--space-sm);
        left: var(--space-sm);
        width: auto;
    }

    /* Section */
    .section {
        padding-block: var(--space-2xl);
    }

    .section-header {
        margin-bottom: var(--space-xl);
    }

    /* FAQ */
    .faq-item__btn {
        padding: var(--space-sm) var(--space-md);
        font-size: var(--font-size-sm);
    }

    .faq-item__text {
        padding: 0 var(--space-md) var(--space-sm);
    }
}

/* ═══════════════════════════════════════
   768px — Планшеты портрет
═══════════════════════════════════════ */

@media (min-width: 641px) and (max-width: 768px) {
    /* Шапка */
    .nav { display: none; }
    .header__cta { display: none; }
    .burger { display: flex; }
    .mobile-menu { display: flex; }

    /* Сетки */
    .steps {
        grid-template-columns: 1fr;
    }

    .steps::before {
        display: none;
    }

    .tracks-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .features-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .reviews-grid {
        grid-template-columns: 1fr;
    }

    .pricing-grid {
        grid-template-columns: 1fr;
    }

    .pricing-card--featured {
        transform: none;
        border-width: 3px;
    }

    .footer__grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* ═══════════════════════════════════════
   1024px — Планшеты ландшафт / маленькие ноуты
═══════════════════════════════════════ */

@media (min-width: 769px) and (max-width: 1024px) {
    /* Показываем навигацию, скрываем бургер */
    .burger { display: none; }
    .mobile-menu { display: none !important; }

    .nav__link {
        padding: 7px 10px;
        font-size: 13px;
    }

    /* Сетки */
    .tracks-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .features-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .reviews-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .pricing-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .pricing-card--featured {
        transform: none;
        order: -1;
        grid-column: span 2;
        max-width: 400px;
        margin-inline: auto;
        width: 100%;
    }

    .footer__grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* ═══════════════════════════════════════
   1200px+ — Десктоп
═══════════════════════════════════════ */

@media (min-width: 1025px) {
    .burger { display: none; }
    .mobile-menu { display: none !important; }

    /* Все сетки — 3 колонки */
    .steps { grid-template-columns: repeat(3, 1fr); }
    .tracks-grid { grid-template-columns: repeat(3, 1fr); }
    .features-grid { grid-template-columns: repeat(3, 1fr); }
    .reviews-grid { grid-template-columns: repeat(3, 1fr); }
    .pricing-grid { grid-template-columns: repeat(3, 1fr); }
    .footer__grid { grid-template-columns: 1.5fr 1fr 1fr 1fr; }
}

/* ═══════════════════════════════════════
   PRINT
═══════════════════════════════════════ */

@media print {
    .header,
    .footer,
    .burger,
    .mobile-menu,
    .hero__decor,
    .cta-section__decor {
        display: none !important;
    }

    .hero {
        background: var(--color-primary) !important;
        min-height: auto;
        padding-block: var(--space-xl);
    }

    * {
        box-shadow: none !important;
    }

    a[href]::after {
        content: ' (' attr(href) ')';
        font-size: 11px;
        color: var(--color-text-muted);
    }
}

/* ═══════════════════════════════════════
   PREFERS-REDUCED-MOTION
═══════════════════════════════════════ */

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }

    .reveal {
        opacity: 1;
        transform: none;
    }
}

/* ═══════════════════════════════════════
   DARK MODE (опционально, минимальный)
═══════════════════════════════════════ */

@media (prefers-color-scheme: dark) {
    /* Намеренно не реализуем — сайт имеет тёплый фирменный стиль,
       который не предполагает тёмную тему */
}
```

---

## Файл 6: `public/assets/js/main.js`

```javascript
/**
 * Основной JavaScript файл
 * Шапка, мобильное меню, FAQ, scroll reveal, базовые утилиты
 * 
 * Путь: /public/assets/js/main.js
 */

'use strict';

/* ═══════════════════════════════════════
   УТИЛИТЫ
═══════════════════════════════════════ */

/**
 * Короткий querySelector
 * @param {string} selector
 * @param {Element|Document} context
 * @returns {Element|null}
 */
const $ = (selector, context = document) => context.querySelector(selector);

/**
 * Короткий querySelectorAll → Array
 * @param {string} selector
 * @param {Element|Document} context
 * @returns {Element[]}
 */
const $$ = (selector, context = document) => Array.from(context.querySelectorAll(selector));

/**
 * Создать элемент с атрибутами и содержимым
 * @param {string} tag
 * @param {Object} attrs
 * @param {string|Element[]} children
 * @returns {Element}
 */
function createElement(tag, attrs = {}, children = '') {
    const el = document.createElement(tag);
    Object.entries(attrs).forEach(([key, val]) => {
        if (key === 'className') el.className = val;
        else if (key === 'innerHTML') el.innerHTML = val;
        else el.setAttribute(key, val);
    });
    if (typeof children === 'string') {
        el.textContent = children;
    } else if (Array.isArray(children)) {
        children.forEach(child => el.appendChild(child));
    }
    return el;
}

/**
 * Debounce — задержка выполнения функции
 * @param {Function} fn
 * @param {number} delay
 * @returns {Function}
 */
function debounce(fn, delay) {
    let timer;
    return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

/**
 * Throttle — ограничение частоты вызовов
 * @param {Function} fn
 * @param {number} limit
 * @returns {Function}
 */
function throttle(fn, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            fn.apply(this, args);
            inThrottle = true;
            setTimeout(() => (inThrottle = false), limit);
        }
    };
}

/**
 * Форматирование времени (секунды → MM:SS)
 * @param {number} seconds
 * @returns {string}
 */
function formatTime(seconds) {
    if (isNaN(seconds) || seconds < 0) return '0:00';
    const m = Math.floor(seconds / 60);
    const s = Math.floor(seconds % 60);
    return `${m}:${s.toString().padStart(2, '0')}`;
}

/**
 * Получение параметра из URL
 * @param {string} name
 * @returns {string|null}
 */
function getUrlParam(name) {
    const params = new URLSearchParams(window.location.search);
    return params.get(name);
}

/* ═══════════════════════════════════════
   ШАПКА: SCROLL + STICKY
═══════════════════════════════════════ */

function initHeader() {
    const header = $('.header');
    if (!header) return;

    const handleScroll = throttle(() => {
        if (window.scrollY > 20) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }, 100);

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll(); // Инициализация при загрузке
}

/* ═══════════════════════════════════════
   МОБИЛЬНОЕ МЕНЮ
═══════════════════════════════════════ */

function initMobileMenu() {
    const burger      = $('.burger');
    const mobileMenu  = $('.mobile-menu');
    const body        = document.body;

    if (!burger || !mobileMenu) return;

    function openMenu() {
        burger.classList.add('open');
        mobileMenu.classList.add('open');
        body.style.overflow = 'hidden'; // Запрет скролла
        burger.setAttribute('aria-expanded', 'true');
        burger.setAttribute('aria-label', 'Закрыть меню');
    }

    function closeMenu() {
        burger.classList.remove('open');
        mobileMenu.classList.remove('open');
        body.style.overflow = '';
        burger.setAttribute('aria-expanded', 'false');
        burger.setAttribute('aria-label', 'Открыть меню');
    }

    burger.addEventListener('click', () => {
        if (burger.classList.contains('open')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    // Закрытие при клике на ссылку
    $$('.mobile-menu__link', mobileMenu).forEach(link => {
        link.addEventListener('click', closeMenu);
    });

    // Закрытие при клике вне меню (на overlay)
    document.addEventListener('click', (e) => {
        if (
            mobileMenu.classList.contains('open') &&
            !mobileMenu.contains(e.target) &&
            !burger.contains(e.target)
        ) {
            closeMenu();
        }
    });

    // Закрытие по Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && mobileMenu.classList.contains('open')) {
            closeMenu();
            burger.focus();
        }
    });

    // Закрытие при ресайзе на большой экран
    window.addEventListener('resize', debounce(() => {
        if (window.innerWidth > 640 && mobileMenu.classList.contains('open')) {
            closeMenu();
        }
    }, 200));
}

/* ═══════════════════════════════════════
   FAQ — АККОРДЕОН
═══════════════════════════════════════ */

function initFAQ() {
    const faqItems = $$('.faq-item');
    if (!faqItems.length) return;

    faqItems.forEach(item => {
        const btn  = $('.faq-item__btn', item);
        const body = $('.faq-item__body', item);

        if (!btn || !body) return;

        btn.setAttribute('aria-expanded', 'false');

        btn.addEventListener('click', () => {
            const isOpen = item.classList.contains('open');

            // Закрываем все другие
            faqItems.forEach(other => {
                if (other !== item) {
                    other.classList.remove('open');
                    const otherBtn = $('.faq-item__btn', other);
                    if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
                }
            });

            // Переключаем текущий
            item.classList.toggle('open', !isOpen);
            btn.setAttribute('aria-expanded', (!isOpen).toString());
        });
    });
}

/* ═══════════════════════════════════════
   SCROLL REVEAL (Intersection Observer)
═══════════════════════════════════════ */

function initScrollReveal() {
    const elements = $$('.reveal');
    if (!elements.length) return;

    // Если браузер не поддерживает IntersectionObserver
    if (!('IntersectionObserver' in window)) {
        elements.forEach(el => el.classList.add('revealed'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target); // Один раз
                }
            });
        },
        {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        }
    );

    elements.forEach(el => observer.observe(el));
}

/* ═══════════════════════════════════════
   АКТИВНАЯ НАВИГАЦИЯ
═══════════════════════════════════════ */

function initActiveNav() {
    const currentPath = window.location.pathname;

    $$('.nav__link, .mobile-menu__link').forEach(link => {
        const href = link.getAttribute('href') || '';

        // Точное совпадение или начало пути
        const isActive =
            (currentPath === '/' && (href === '/' || href === '/index.php')) ||
            (currentPath !== '/' && href !== '/' && currentPath.includes(href.replace('.php', '')));

        if (isActive) {
            link.classList.add('active');
            link.setAttribute('aria-current', 'page');
        }
    });
}

/* ═══════════════════════════════════════
   ПЛАВНЫЙ СКРОЛЛ К ЯКОРЯМ
═══════════════════════════════════════ */

function initSmoothScroll() {
    $$('a[href^="#"]').forEach(link => {
        link.addEventListener('click', (e) => {
            const targetId = link.getAttribute('href');
            if (targetId === '#') return;

            const target = $(targetId);
            if (!target) return;

            e.preventDefault();

            const headerHeight = parseInt(
                getComputedStyle(document.documentElement)
                    .getPropertyValue('--header-height')
            ) || 72;

            const targetTop = target.getBoundingClientRect().top + window.scrollY - headerHeight - 16;

            window.scrollTo({ top: targetTop, behavior: 'smooth' });
        });
    });
}

/* ═══════════════════════════════════════
   HERO — АНИМАЦИЯ СЧЁТЧИКОВ
═══════════════════════════════════════ */

function initCounterAnimation() {
    const counters = $$('.hero__stat-value[data-count]');
    if (!counters.length) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;

                const el        = entry.target;
                const target    = parseInt(el.dataset.count);
                const suffix    = el.dataset.suffix || '';
                const duration  = 1500;
                const start     = performance.now();

                function update(now) {
                    const elapsed  = now - start;
                    const progress = Math.min(elapsed / duration, 1);
                    // Easing
                    const eased    = 1 - Math.pow(1 - progress, 3);
                    const current  = Math.floor(eased * target);

                    el.textContent = current.toLocaleString('ru') + suffix;

                    if (progress < 1) {
                        requestAnimationFrame(update);
                    } else {
                        el.textContent = target.toLocaleString('ru') + suffix;
                    }
                }

                requestAnimationFrame(update);
                observer.unobserve(el);
            });
        },
        { threshold: 0.5 }
    );

    counters.forEach(counter => observer.observe(counter));
}

/* ═══════════════════════════════════════
   ТЕЛЕФОННАЯ МАСКА
═══════════════════════════════════════ */

function initPhoneMask() {
    $$('input[type="tel"]').forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');

            // Убираем лишний 7/8 в начале
            if (value.startsWith('7') || value.startsWith('8')) {
                value = value.substring(1);
            }

            value = value.substring(0, 10);

            let formatted = '';
            if (value.length > 0) formatted += '(' + value.substring(0, 3);
            if (value.length >= 4) formatted += ') ' + value.substring(3, 6);
            if (value.length >= 7) formatted += '-' + value.substring(6, 8);
            if (value.length >= 9) formatted += '-' + value.substring(8, 10);

            this.value = formatted;
        });

        input.addEventListener('focus', function() {
            if (!this.value) {
                this.placeholder = '(999) 999-99-99';
            }
        });

        input.addEventListener('keydown', function(e) {
            // Разрешаем Delete, Backspace, стрелки, Tab
            if (['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)) return;
            // Разрешаем цифры
            if (!/^\d$/.test(e.key)) e.preventDefault();
        });
    });
}

/* ═══════════════════════════════════════
   LAZY LOADING ИЗОБРАЖЕНИЙ
═══════════════════════════════════════ */

function initLazyImages() {
    if (!('IntersectionObserver' in window)) return;

    const images = $$('img[data-src]');
    if (!images.length) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const img = entry.target;
                img.src = img.dataset.src;
                if (img.dataset.srcset) img.srcset = img.dataset.srcset;
                img.removeAttribute('data-src');
                img.classList.add('loaded');
                observer.unobserve(img);
            });
        },
        { rootMargin: '200px' }
    );

    images.forEach(img => observer.observe(img));
}

/* ═══════════════════════════════════════
   КОПИРОВАНИЕ В БУФЕР ОБМЕНА
═══════════════════════════════════════ */

function initCopyButtons() {
    $$('[data-copy]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const text = btn.dataset.copy;
            try {
                await navigator.clipboard.writeText(text);
                const original = btn.textContent;
                btn.textContent = 'Скопировано!';
                setTimeout(() => { btn.textContent = original; }, 2000);
            } catch {
                // Фолбэк для старых браузеров
                const ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
            }
        });
    });
}

/* ═══════════════════════════════════════
   ЭКСПОРТ УТИЛИТ (глобально для других скриптов)
═══════════════════════════════════════ */

window.HitSong = window.HitSong || {};
Object.assign(window.HitSong, {
    $,
    $$,
    createElement,
    debounce,
    throttle,
    formatTime,
    getUrlParam,
});

/* ═══════════════════════════════════════
   ИНИЦИАЛИЗАЦИЯ
═══════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {
    initHeader();
    initMobileMenu();
    initFAQ();
    initScrollReveal();
    initActiveNav();
    initSmoothScroll();
    initCounterAnimation();
    initPhoneMask();
    initLazyImages();
    initCopyButtons();
});
```

---

## Файл 7: `public/assets/js/player.js`

```javascript
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
```

---

## Файл 8: `public/assets/js/notifications.js`

```javascript
/**
 * Система уведомлений (Toast)
 * Показывает красивые всплывающие сообщения
 * 
 * Путь: /public/assets/js/notifications.js
 * Использование:
 *   Notify.success('Заголовок', 'Текст сообщения');
 *   Notify.error('Ошибка', 'Описание ошибки');
 *   Notify.warning('Внимание', 'Текст');
 *   Notify.info('Инфо', 'Текст');
 */

'use strict';

(function() {

    /* ─── Иконки для типов уведомлений ─── */
    const ICONS = {
        success: '✅',
        error:   '❌',
        warning: '⚠️',
        info:    'ℹ️',
    };

    /* ─── Время показа по умолчанию (мс) ─── */
    const DEFAULT_DURATION = 5000;

    /* ─── Контейнер для тостов ─── */
    let container = null;

    /**
     * Получить или создать контейнер
     * @returns {Element}
     */
    function getContainer() {
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            container.setAttribute('role', 'region');
            container.setAttribute('aria-label', 'Уведомления');
            container.setAttribute('aria-live', 'polite');
            document.body.appendChild(container);
        }
        return container;
    }

    /**
     * Показать уведомление
     * @param {string} type    — success | error | warning | info
     * @param {string} title   — заголовок
     * @param {string} message — текст (опционально)
     * @param {number} duration — время показа в мс (0 = не скрывать)
     * @returns {Element} — DOM-элемент тоста
     */
    function show(type, title, message = '', duration = DEFAULT_DURATION) {
        const cnt  = getContainer();
        const icon = ICONS[type] || ICONS.info;

        const toast = document.createElement('div');
        toast.className = `toast toast--${type}`;
        toast.setAttribute('role', 'alert');
        toast.style.position = 'relative';

        toast.innerHTML = `
            <div class="toast__icon">${icon}</div>
            <div class="toast__content">
                ${title   ? `<div class="toast__title">${escapeHtml(title)}</div>`   : ''}
                ${message ? `<div class="toast__message">${escapeHtml(message)}</div>` : ''}
            </div>
            <button class="toast__close" aria-label="Закрыть уведомление">✕</button>
        `;

        // Кнопка закрытия
        const closeBtn = toast.querySelector('.toast__close');
        closeBtn.addEventListener('click', () => remove(toast));

        // Добавляем в контейнер
        cnt.appendChild(toast);

        // Автоудаление
        let removeTimer = null;
        if (duration > 0) {
            removeTimer = setTimeout(() => remove(toast), duration);
        }

        // Пауза при наведении
        toast.addEventListener('mouseenter', () => {
            if (removeTimer) clearTimeout(removeTimer);
        });

        toast.addEventListener('mouseleave', () => {
            if (duration > 0) {
                removeTimer = setTimeout(() => remove(toast), duration / 2);
            }
        });

        // Ограничение: не более 5 одновременных тостов
        const toasts = cnt.querySelectorAll('.toast');
        if (toasts.length > 5) {
            remove(toasts[0]);
        }

        return toast;
    }

    /**
     * Удалить тост с анимацией
     * @param {Element} toast
     */
    function remove(toast) {
        if (!toast || !toast.isConnected) return;
        toast.classList.add('removing');
        toast.addEventListener('animationend', () => {
            if (toast.isConnected) toast.remove();
        }, { once: true });

        // Фолбэк если анимация не сработала
        setTimeout(() => {
            if (toast.isConnected) toast.remove();
        }, 400);
    }

    /**
     * Экранирование HTML (защита от XSS)
     * @param {string} str
     * @returns {string}
     */
    function escapeHtml(str) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        };
        return String(str).replace(/[&<>"']/g, m => map[m]);
    }

    /**
     * Показать сообщение об ошибке с полями формы
     * @param {Object} errors — объект {поле: [сообщения]}
     */
    function showFormErrors(errors) {
        const messages = Object.values(errors).flat();
        const text = messages.slice(0, 3).join('; ');
        show('error', 'Исправьте ошибки', text);
    }

    /**
     * Уведомление из PHP (из data-атрибута body)
     * Используется для flash-сообщений после редиректа
     */
    function initFlashMessages() {
        const body = document.body;
        const flash = body.dataset.flash;

        if (flash) {
            try {
                const data = JSON.parse(flash);
                if (data.type && data.title) {
                    // Небольшая задержка, чтобы страница успела отрисоваться
                    setTimeout(() => {
                        show(data.type, data.title, data.message || '');
                    }, 300);
                }
            } catch {
                // Невалидный JSON — игнорируем
            }
        }
    }

    /* ─── Глобальное API ─── */
    window.Notify = {
        success: (title, msg, dur)  => show('success', title, msg, dur),
        error:   (title, msg, dur)  => show('error',   title, msg, dur),
        warning: (title, msg, dur)  => show('warning', title, msg, dur),
        info:    (title, msg, dur)  => show('info',    title, msg, dur),
        show,
        remove,
        showFormErrors,
    };

    /* ─── Инициализация ─── */
    document.addEventListener('DOMContentLoaded', initFlashMessages);

})();
```

---

## Файл 9: `public/index.php`

```php
<?php
/**
 * Главная страница сайта "Хитовая Песня"
 * Hero, как работает, примеры треков, преимущества, отзывы, CTA, FAQ
 * 
 * Путь: /public/index.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';

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
        'a' => 'Только после того, как вы услышите готовую песню и она вам понравится. Мы работаем по принципу "слушаете — потом решаете". Никаких предоплат.',
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

require_once __DIR__ . '/../includes/head-meta.php';
require_once __DIR__ . '/../includes/header.php';
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
                                    <?php if (!empty($track['voice_type'])): ?>
                                        <span class="track-card__meta-item">
                                            🎤 <?= h($track['voice_type']) ?>
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
```

---

## Обновление `includes/head-meta.php`

```php
<?php
/**
 * SEO-мета теги, подключение шрифтов и стилей
 * Включается в начале каждой страницы
 * 
 * Путь: /includes/head-meta.php
 * Требует: $page_meta (массив с title, description, etc.)
 */

// ─── Значения по умолчанию ───
$meta_title       = $page_meta['title']       ?? SITE_NAME . ' — ' . SITE_SLOGAN;
$meta_description = $page_meta['description'] ?? 'Персональные песни на заказ для праздников. Быстро, качественно, с оплатой после результата.';
$meta_keywords    = $page_meta['keywords']    ?? 'песня на заказ, именная песня, песня на праздник';
$meta_og_type     = $page_meta['og_type']     ?? 'website';
$meta_canonical   = $page_meta['canonical']   ?? SITE_URL . '/' . ltrim($_SERVER['PHP_SELF'], '/');
$meta_og_image    = $page_meta['og_image']    ?? SITE_URL . '/assets/img/og-default.jpg';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- ─── SEO ─── -->
    <title><?= htmlspecialchars($meta_title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta_description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <meta name="keywords"    content="<?= htmlspecialchars($meta_keywords,    ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <meta name="robots"      content="index, follow">
    <link rel="canonical"    href="<?= htmlspecialchars($meta_canonical, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">

    <!-- ─── Open Graph ─── -->
    <meta property="og:title"       content="<?= htmlspecialchars($meta_title,       ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($meta_description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <meta property="og:type"        content="<?= htmlspecialchars($meta_og_type,     ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <meta property="og:url"         content="<?= htmlspecialchars($meta_canonical,   ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <meta property="og:image"       content="<?= htmlspecialchars($meta_og_image,    ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale"      content="ru_RU">
    <meta property="og:site_name"   content="<?= htmlspecialchars(SITE_NAME, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">

    <!-- ─── Twitter Card ─── -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= htmlspecialchars($meta_title,       ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($meta_description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <meta name="twitter:image"       content="<?= htmlspecialchars($meta_og_image,    ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">

    <!-- ─── Schema.org Organization ─── -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?= SITE_NAME ?>",
        "url": "<?= SITE_URL ?>",
        "logo": "<?= SITE_URL ?>/assets/img/logo.svg",
        "description": "Студия персональных песен для праздников",
        "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "customer support",
            "availableLanguage": "Russian",
            "hoursAvailable": "Mo-Su 09:00-22:00"
        },
        "sameAs": [
            "https://vk.com/<?= VK_PAGE ?>",
            "https://t.me/<?= ltrim(TELEGRAM_USERNAME, '@') ?>"
        ]
    }
    </script>

    <!-- ─── Favicon ─── -->
    <link rel="icon"             type="image/x-icon" href="/favicon.ico">
    <link rel="icon"             type="image/svg+xml" href="/assets/img/logo.svg">
    <link rel="apple-touch-icon" sizes="180x180"      href="/assets/img/icons/apple-touch-icon.png">
    <link rel="manifest"         href="/manifest.json">
    <meta name="theme-color" content="#8B1E3F">

    <!-- ─── Preconnect (Google Fonts) ─── -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- ─── CSS ─── -->
    <link rel="stylesheet" href="/assets/css/variables.css">
    <link rel="stylesheet" href="/assets/css/reset.css">
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/responsive.css">

    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($css, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
        <?php endforeach; ?>
    <?php endif; ?>

</head>
<body<?php if (isset($body_class)) echo ' class="' . htmlspecialchars($body_class, ENT_QUOTES, 'UTF-8') . '"'; ?>>
```

---

## Обновление `includes/header.php`

```php
<?php
/**
 * Шапка сайта — навигация, логотип
 * Подключается на всех публичных страницах
 * 
 * Путь: /includes/header.php
 */

// Определяем текущую страницу для подсветки активного пункта
$current_page = basename($_SERVER['PHP_SELF'], '.php');

$nav_items = [
    ['href' => '/',            'slug' => 'index',     'label' => 'Главная'],
    ['href' => '/portfolio.php', 'slug' => 'portfolio', 'label' => 'Портфолио'],
    ['href' => '/pricing.php',   'slug' => 'pricing',   'label' => 'Цены'],
    ['href' => '/contacts.php',  'slug' => 'contacts',  'label' => 'Контакты'],
];
?>

<header class="header" role="banner">
    <div class="container">
        <div class="header__inner">

            <!-- Логотип -->
            <a href="/" class="logo" aria-label="Хитовая Песня — на главную">
                <div class="logo__icon" aria-hidden="true">🎵</div>
                <div class="logo__text">
                    <span class="logo__name">Хитовая Песня</span>
                    <span class="logo__slogan">Исполнение ваших желаний</span>
                </div>
            </a>

            <!-- Навигация (десктоп) -->
            <nav class="nav" aria-label="Основная навигация">
                <?php foreach ($nav_items as $item): ?>
                    <a
                        href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                        class="nav__link<?= $current_page === $item['slug'] ? ' active' : '' ?>"
                        <?= $current_page === $item['slug'] ? 'aria-current="page"' : '' ?>
                    >
                        <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <!-- CTA-кнопка -->
            <div class="header__cta">
                <a href="/order.php" class="btn btn--primary btn--sm">
                    Заказать песню
                </a>
            </div>

            <!-- Бургер (мобильный) -->
            <button
                class="burger"
                aria-label="Открыть меню"
                aria-expanded="false"
                aria-controls="mobile-menu"
            >
                <span class="burger__line"></span>
                <span class="burger__line"></span>
                <span class="burger__line"></span>
            </button>

        </div><!-- /.header__inner -->
    </div><!-- /.container -->
</header>

<!-- Мобильное меню -->
<nav class="mobile-menu" id="mobile-menu" aria-label="Мобильная навигация">
    <?php foreach ($nav_items as $item): ?>
        <a
            href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
            class="mobile-menu__link<?= $current_page === $item['slug'] ? ' active' : '' ?>"
            <?= $current_page === $item['slug'] ? 'aria-current="page"' : '' ?>
        >
            <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
        </a>
    <?php endforeach; ?>

    <div class="mobile-menu__cta">
        <a href="/order.php" class="btn btn--primary btn--full btn--lg">
            🎵 Заказать песню
        </a>
    </div>
</nav>
```

---

## Обновление `includes/footer.php`

```php
<?php
/**
 * Подвал сайта
 * Контакты, ссылки, копирайт
 * 
 * Путь: /includes/footer.php
 */
$current_year = date('Y');
?>

<footer class="footer" role="contentinfo">
    <div class="container">

        <div class="footer__grid">

            <!-- Бренд -->
            <div class="footer__brand">
                <a href="/" class="footer__logo" aria-label="Хитовая Песня">
                    <div class="footer__logo-icon" aria-hidden="true">🎵</div>
                    <span class="footer__logo-name">Хитовая Песня</span>
                </a>

                <p class="footer__desc">
                    Создаём уникальные персональные песни для&nbsp;ваших
                    праздников. Оплата только после результата.
                </p>

                <!-- Соцсети -->
                <div class="footer__social">
                    <a
                        href="https://vk.com/<?= htmlspecialchars(VK_PAGE, ENT_QUOTES, 'UTF-8') ?>"
                        class="footer__social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="ВКонтакте"
                    >🔵</a>
                    <a
                        href="https://t.me/<?= htmlspecialchars(ltrim(TELEGRAM_USERNAME, '@'), ENT_QUOTES, 'UTF-8') ?>"
                        class="footer__social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Telegram"
                    >✈️</a>
                    <a
                        href="https://ok.ru/<?= htmlspecialchars(OK_PAGE, ENT_QUOTES, 'UTF-8') ?>"
                        class="footer__social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Одноклассники"
                    >🟠</a>
                    <a
                        href="https://wa.me/<?= htmlspecialchars(preg_replace('/\D/', '', WHATSAPP_NUMBER), ENT_QUOTES, 'UTF-8') ?>"
                        class="footer__social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="WhatsApp"
                    >💚</a>
                </div>
            </div><!-- /.footer__brand -->

            <!-- Навигация -->
            <div>
                <h3 class="footer__col-title">Навигация</h3>
                <ul class="footer__links">
                    <li><a href="/"              class="footer__link">Главная</a></li>
                    <li><a href="/portfolio.php" class="footer__link">Портфолио</a></li>
                    <li><a href="/pricing.php"   class="footer__link">Тарифы и цены</a></li>
                    <li><a href="/order.php"     class="footer__link">Заказать песню</a></li>
                    <li><a href="/contacts.php"  class="footer__link">Контакты</a></li>
                </ul>
            </div>

            <!-- Поводы -->
            <div>
                <h3 class="footer__col-title">Поводы</h3>
                <ul class="footer__links">
                    <li><a href="/order.php?occasion=wedding"     class="footer__link">💒 Свадьба</a></li>
                    <li><a href="/order.php?occasion=birthday"    class="footer__link">🎂 День рождения</a></li>
                    <li><a href="/order.php?occasion=anniversary" class="footer__link">💕 Юбилей</a></li>
                    <li><a href="/order.php?occasion=corporate"   class="footer__link">🏢 Корпоратив</a></li>
                    <li><a href="/order.php?occasion=newyear"     class="footer__link">🎄 Новый год</a></li>
                    <li><a href="/order.php?occasion=other"       class="footer__link">✨ Другой повод</a></li>
                </ul>
            </div>

            <!-- Контакты -->
            <div>
                <h3 class="footer__col-title">Контакты</h3>
                <div class="footer__contacts">
                    <div class="footer__contact-item">
                        <span class="footer__contact-icon">📱</span>
                        <a
                            href="tel:<?= htmlspecialchars(preg_replace('/\D/', '', CONTACT_PHONE), ENT_QUOTES, 'UTF-8') ?>"
                            class="footer__link"
                        ><?= htmlspecialchars(CONTACT_PHONE, ENT_QUOTES, 'UTF-8') ?></a>
                    </div>
                    <div class="footer__contact-item">
                        <span class="footer__contact-icon">✉️</span>
                        <a
                            href="mailto:<?= htmlspecialchars(ADMIN_EMAIL, ENT_QUOTES, 'UTF-8') ?>"
                            class="footer__link"
                        ><?= htmlspecialchars(ADMIN_EMAIL, ENT_QUOTES, 'UTF-8') ?></a>
                    </div>
                    <div class="footer__contact-item">
                        <span class="footer__contact-icon">✈️</span>
                        <a
                            href="https://t.me/<?= htmlspecialchars(ltrim(TELEGRAM_USERNAME, '@'), ENT_QUOTES, 'UTF-8') ?>"
                            class="footer__link"
                            target="_blank"
                            rel="noopener noreferrer"
                        ><?= htmlspecialchars(TELEGRAM_USERNAME, ENT_QUOTES, 'UTF-8') ?></a>
                    </div>
                    <div class="footer__contact-item">
                        <span class="footer__contact-icon">🕐</span>
                        <span>Пн–Вс: 9:00 – 22:00 МСК</span>
                    </div>
                </div>
            </div>

        </div><!-- /.footer__grid -->

        <!-- Нижняя строка -->
        <div class="footer__bottom">
            <p class="footer__copy">
                © <?= $current_year ?> Хитовая Песня. Все права защищены.
            </p>
            <div class="footer__copy-links">
                <a href="/privacy.php"  class="footer__copy-link">Политика конфиденциальности</a>
                <a href="/sitemap.xml"  class="footer__copy-link">Карта сайта</a>
            </div>
        </div>

    </div><!-- /.container -->
</footer>

<!-- ─── JavaScript ─── -->
<script src="/assets/js/notifications.js" defer></script>
<script src="/assets/js/player.js" defer></script>
<script src="/assets/js/main.js" defer></script>

<?php if (isset($extra_js)): ?>
    <?php foreach ($extra_js as $js): ?>
        <script src="<?= htmlspecialchars($js, ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
```

---

## Также нужна функция `format_duration` в `includes/functions.php`

Добавьте в конец файла `includes/functions.php` (из Этапа 1):

```php
/**
 * Форматирование длительности трека (секунды → M:SS)
 * 
 * @param int $seconds
 * @return string
 */
function format_duration(int $seconds): string
{
    if ($seconds <= 0) return '0:00';
    $m = intdiv($seconds, 60);
    $s = $seconds % 60;
    return $m . ':' . str_pad((string)$s, 2, '0', STR_PAD_LEFT);
}
```

---

## ✅ Этап 2 завершён

### Что создано:

| Файл | Описание |
|------|----------|
| `assets/css/variables.css` | CSS-переменные, Google Fonts |
| `assets/css/reset.css` | Современный reset |
| `assets/css/main.css` | Основные стили всего сайта |
| `assets/css/components.css` | Формы, тосты, плеер, карточки |
| `assets/css/responsive.css` | Адаптив mobile-first |
| `assets/js/main.js` | Шапка, меню, FAQ, scroll-reveal |
| `assets/js/player.js` | Аудиоплеер для треков |
| `assets/js/notifications.js` | Toast-уведомления |
| `public/index.php` | Полная главная страница |
| `includes/head-meta.php` | SEO, подключение CSS |
| `includes/header.php` | Шапка с навигацией |
| `includes/footer.php` | Подвал с JS |

### Что добавить в `config.php` (из Этапа 1):
Убедитесь что есть константы:
- `VK_PAGE` — slug страницы ВК
- `TELEGRAM_USERNAME` — @username бота
- `OK_PAGE` — страница в ОК
- `WHATSAPP_NUMBER` — номер для WA
- `CONTACT_PHONE` — телефон для отображения

