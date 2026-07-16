<?php
/**
 * Страница заказа — многошаговая анкета (6 шагов)
 * Сохраняет прогресс в localStorage, отправляет через AJAX
 *
 * Путь: /public/order.php
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';

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

require_once __DIR__ . '/includes/head-meta.php';
require_once __DIR__ . '/includes/header.php';
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
require_once __DIR__ . '/includes/footer.php';
?>