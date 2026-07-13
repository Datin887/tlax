<?php
/**
 * Форма заказа (многошаговая)
 * Путь: /public/order.php
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';

// Проверка авторизации для админки
$is_admin = isset($_SESSION['admin_id']);

// Обработка формы
if (is_post() && verify_post_request('order')) {
    // Получаем данные
    $data = [
        'occasion' => post('occasion', '', 50),
        'occasion_other' => post('occasion_other', '', 255),
        'event_date' => post('event_date', ''),
        'urgency' => post('urgency', 'normal'),
        'hero_name' => post('hero_name', '', 255),
        'hero_age' => post('hero_age', 0, 3),
        'hero_relation' => post('hero_relation', '', 100),
        'hero_profession' => post('hero_profession', '', 255),
        'hero_hobbies' => post('hero_hobbies', '', 500),
        'story' => post('story', '', 2000),
        'must_include' => post('must_include', '', 2000),
        'must_exclude' => post('must_exclude', '', 2000),
        'mood' => post('mood', '', 50),
        'music_styles' => json_encode(post('music_styles', [])),
        'voice_type' => post('voice_type', '', 50),
        'duration_type' => post('duration_type', 'standard'),
        'tariff' => post('tariff', 'standard'),
        'extra_wishes' => post('extra_wishes', '', 2000),
        'client_name' => post('client_name', '', 255),
        'client_phone' => post('client_phone', '', 20),
        'client_telegram' => post('client_telegram', '', 100),
        'client_whatsapp' => post('client_whatsapp', '', 20),
        'client_ok' => post('client_ok', '', 255),
        'client_vk' => post('client_vk', '', 255),
        'client_email' => post('client_email', '', 150),
        'contact_time' => post('contact_time', '', 50),
        'preferred_contact' => post('preferred_contact', '', 50),
        'ip_address' => get_client_ip(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'referrer' => $_SERVER['HTTP_REFERER'] ?? '',
    ];

    // Валидация
    $errors = [];
    if (empty($data['occasion'])) $errors[] = 'Укажите повод (Свадьба, День рождения и т.д.)';
    if (empty($data['hero_name'])) $errors[] = 'Укажите имя героя';
    if (empty($data['client_name'])) $errors[] = 'Укажите ваше имя';
    if (empty($data['client_phone'])) $errors[] = 'Укажите телефон';
    if (empty($data['client_email'])) $errors[] = 'Укажите email';

    // Проверка формы
    if (empty($errors)) {
        // Проверяем CSRF
        if (!verify_csrf_token(post('csrf_token', '', 32), 'order')) {
            $errors[] = 'Ошибка безопасности. Обновите страницу.';
        }
    }

    if (!empty($errors)) {
        // Возвращаем ошибки в сессии
        $_SESSION['order_errors'] = $errors;
        $_SESSION['order_data'] = $data;
        redirect('/order.php?step=1&error=' . urlencode(implode('; ', $errors)));
    } else {
        // Сохраняем в БД
        $db = Database::getInstance();
        $db->transaction(function() use ($db, $data) {
            // Сохраняем заявку
            $order_id = $db->insert(
                "INSERT INTO orders (
                    order_number, occasion, occasion_other, event_date, urgency,
                    hero_name, hero_age, hero_relation, hero_profession, hero_hobbies,
                    story, must_include, must_exclude, mood, music_styles, voice_type, duration_type,
                    tariff, extra_wishes, client_name, client_phone, client_telegram, client_whatsapp, client_ok, client_vk, client_email, contact_time, preferred_contact,
                    ip_address, user_agent, utm_source, utm_medium, utm_campaign, notification_sent
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    generate_order_number($db->get_int('orders', 'id', 0) + 1), // Это будет заменено, временно используем placeholder
                    $data['occasion'],
                    $data['occasion_other'],
                    $data['event_date'],
                    $data['urgency'],
                    $data['hero_name'],
                    $data['hero_age'] > 0 ? $data['hero_age'] : null,
                    $data['hero_relation'],
                    $data['hero_profession'],
                    $data['hero_hobbies'],
                    $data['story'],
                    $data['must_include'],
                    $data['must_exclude'],
                    $data['mood'],
                    $data['music_styles'],
                    $data['voice_type'],
                    $data['duration_type'],
                    $data['tariff'],
                    $data['extra_wishes'],
                    $data['client_name'],
                    $data['client_phone'],
                    $data['client_telegram'],
                    $data['client_whatsapp'],
                    $data['client_ok'],
                    $data['client_vk'],
                    $data['client_email'],
                    $data['contact_time'],
                    $data['preferred_contact'],
                    $data['ip_address'],
                    $data['user_agent'],
                    $_GET['utm_source'] ?? '',
                    $_GET['utm_medium'] ?? '',
                    $_GET['utm_campaign'] ?? '',
                    0
                ]
            );

            // Сохраняем логи
            $db->execute("INSERT INTO order_logs (order_id, admin_id, action, old_value, new_value, comment) VALUES (?, ?, 'create', 'none', 'Заявка создана', 'Заявка создана через форму')", [$order_id, $is_admin ? $data['admin_id'] : null]);

            // Перенаправляем на следующий шаг
            $_SESSION['order_step'] = 2;
            $_SESSION['order_data'] = $data;
            redirect('/order.php?step=2');
        });
    }
}

// Получаем данные формы
$order_data = $_SESSION['order_data'] ?? [];
$order_errors = $_SESSION['order_errors'] ?? [];

// Получаем текущий шаг
$current_step = $_GET['step'] ?? ($_SESSION['order_step'] ?? 1);
$current_step = (int)$current_step;

// Получаем список шагов
$steps = [
    1 => 'Выбор повода',
    2 => 'Герой песни',
    3 => 'История и стиль',
    4 => 'Текст песни',
    5 => 'Музыка',
    6 => 'Контакты',
    7 => 'Сроки и оплата',
    8 => 'Подтверждение'
];

// Обработка ошибок
if (!empty($order_errors)) {
    $error_messages = [];
    foreach ($order_errors as $error) {
        $error_messages[] = '<div class="form-error">' . h($error) . '</div>';
    }
    $error_html = implode('', $error_messages);
}

// Обработка успешного отправления
if (isset($_SESSION['order_step']) && $_SESSION['order_step'] == 8) {
    // Сохраняем финальную заявку
    $db = Database::getInstance();
    $db->execute("UPDATE orders SET status = 'completed', paid_at = NOW() WHERE id = ?", [(int)$_SESSION['order_id']]);
    
    // Отправляем уведомление
    if (function_exists('send_telegram_notification')) {
        send_telegram_notification("Новая заявка HP-{$order_id}: {$data['hero_name']} - {$data['occasion']}");
    }
    
    // Перенаправляем на страницу подтверждения
    redirect('/order_confirmation.php');
}
?>

<?php require_once __DIR__ . '/includes/head-meta.php'; require_once __DIR__ . '/includes/header.php'; ?>

<main>
    <section class="section section--white" id="order-form">
        <div class="container">
            <div class="section-header reveal">
                <h2 class="section-title">Закажите персональную песню</h2>
                <p class="section-subtitle">
                    Заполните анкету за 5 минут — и мы начнём работу над вашей историей.
                </p>
            </div>
            
            <div class="order-form-wrap reveal">
                <!-- Ошибки -->
                <?php if (!empty($error_html)): ?>
                    <div class="order-form-errors reveal">
                        <div class="card card--error">
                            <div class="card__title">Ошибки формы</div>
                            <div class="card__content">
                                <?= $error_html ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Форма -->
                <form method="post" action="" class="order-form">
                    <input type="hidden" name="csrf_token" value="<?= csrf_input('order') ?>">
                    
                    <!-- Шаг 1: Повод -->
                    <?php if ($current_step == 1): ?>
                        <div class="step" id="step-1">
                            <div class="stepper">
                                <div class="stepper__step stepper__step--active">
                                    <div class="stepper__circle">1</div>
                                    <div class="stepper__title">Выбор повода</div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Повод</label>
                                <select name="occasion" class="form-input" required>
                                    <option value="">Выберите повод</option>
                                    <option value="wedding" <?= $data['occasion'] === 'wedding' ? 'selected' : '' ?>>Свадьба</option>
                                    <option value="birthday" <?= $data['occasion'] === 'birthday' ? 'selected' : '' ?>>День рождения</option>
                                    <option value="anniversary" <?= $data['occasion'] === 'anniversary' ? 'selected' : '' ?>>Юбилей</option>
                                    <option value="corporate" <?= $data['occasion'] === 'corporate' ? 'selected' : '' ?>>Корпоратив</option>
                                    <option value="holiday" <?= $data['occasion'] === 'holiday' ? 'selected' : '' ?>>Праздник</option>
                                    <option value="застольная" <?= $data['occasion'] === 'застольная' ? 'selected' : '' ?>>Застольная</option>
                                    <option value="special" <?= $data['occasion'] === 'special' ? 'selected' : '' ?>>Особый повод</option>
                                    <option value="children" <?= $data['occasion'] === 'children' ? 'selected' : '' ?>>Детская</option>
                                    <option value="other" <?= $data['occasion'] === 'other' ? 'selected' : '' ?>>Другой</option>
                                </select>
                                <?php if (isset($data['occasion_other'])): ?>
                                    <input type="text" name="occasion_other" class="form-input" value="<?= h($data['occasion_other']) ?>" placeholder="Укажите детали">
                                <?php endif; ?>
                                <?php if (!empty($errors) && in_array('Укажите повод (Свадьба, День рождения и т.д.)', $order_errors)): ?>
                                    <div class="form-error"><?= h($order_errors[array_search('Укажите повод (Свадьба, День рождения и т.д.)', $order_errors)] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Дата мероприятия (если известна)</label>
                                <input type="date" name="event_date" class="form-input" value="<?= h($data['event_date'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Срочность</label>
                                <select name="urgency" class="form-input">
                                    <option value="normal" <?= $data['urgency'] === 'normal' ? 'selected' : '' ?>>Обычная (3-5 дней)</option>
                                    <option value="fast" <?= $data['urgency'] === 'fast' ? 'selected' : '' ?>>Быстро (2-4 дня)</option>
                                    <option value="urgent" <?= $data['urgency'] === 'urgent' ? 'selected' : '' ?>>Срочно (1-2 дня)</option>
                                    <option value="very_urgent" <?= $data['urgency'] === 'very_urgent' ? 'selected' : '' ?>>Очень срочно (сегодня-завтра)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Имя героя</label>
                            <input type="text" name="hero_name" class="form-input" value="<?= h($data['hero_name'] ?? '') ?>" required>
                            <?php if (isset($errors) && in_array('Укажите имя героя', $order_errors)): ?>
                                <div class="form-error"><?= h($order_errors[array_search('Укажите имя героя', $order_errors)]) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Возраст героя (если известен)</label>
                            <input type="number" name="hero_age" class="form-input" min="0" max="150" value="<?= h($data['hero_age'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Отношение к герою</label>
                            <input type="text" name="hero_relation" class="form-input" value="<?= h($data['hero_relation'] ?? '') ?>" placeholder="Брат, сестра, друг и т.д.">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Профессия героя</label>
                            <input type="text" name="hero_profession" class="form-input" value="<?= h($data['hero_profession'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Хобби и увлечения</label>
                            <input type="text" name="hero_hobbies" class="form-input" value="<?= h($data['hero_hobbies'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="order-step-nav" style="margin-top: var(--space-xl);">
                        <div class="stepper">
                            <div class="stepper__step stepper__step--active">
                                <div class="stepper__circle">1</div>
                                <div class="stepper__title">Выбор повода</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">2</div>
                                <div class="stepper__title">Герой песни</div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn--primary">Далее</button>
                    </div>
                <?php endif; ?>
                
                <!-- Шаг 2: Герой -->
                <?php if ($current_step >= 2 && $current_step <= 2): ?>
                    <div class="step" id="step-2">
                        <div class="stepper">
                            <div class="stepper__step stepper__step--active">
                                <div class="stepper__circle">1</div>
                                <div class="stepper__title">Выбор повода</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">2</div>
                                <div class="stepper__title">Герой песни</div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Имя героя</label>
                            <input type="text" name="hero_name" class="form-input" value="<?= h($data['hero_name'] ?? '') ?>" required>
                            <?php if (isset($errors) && in_array('Укажите имя героя', $order_errors)): ?>
                                <div class="form-error"><?= h($order_errors[array_search('Укажите имя героя', $order_errors)]) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Возраст</label>
                            <input type="number" name="hero_age" class="form-input" min="0" max="150" value="<?= h($data['hero_age'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Отношение</label>
                            <input type="text" name="hero_relation" class="form-input" value="<?= h($data['hero_relation'] ?? '') ?>" placeholder="Брат, мама, друг">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Профессия</label>
                            <input type="text" name="hero_profession" class="form-input" value="<?= h($data['hero_profession'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Хобби</label>
                            <input type="text" name="hero_hobbies" class="form-input" value="<?= h($data['hero_hobbies'] ?? '') ?>">
                        </div>
                        
                        <div class="order-step-nav" style="margin-top: var(--space-xl);">
                            <div class="stepper">
                                <div class="stepper__step stepper__step--active">
                                    <div class="stepper__circle">1</div>
                                    <div class="stepper__title">Выбор повода</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">2</div>
                                    <div class="stepper__title">Герой песни</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">3</div>
                                    <div class="stepper__title">История и стиль</div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn--primary">Далее</button>
                            <a href="/order.php?step=1" class="btn btn--outline">Назад</button>
                        </div>
                    </div>
                    
                <?php endif; ?>
                
                <!-- Шаг 3: История и стиль -->
                <?php if ($current_step >= 3 && $current_step <= 3): ?>
                    <div class="step" id="step-3">
                        <div class="stepper">
                            <div class="stepper__step stepper__step--active">
                                <div class="stepper__circle">1</div>
                                <div class="stepper__title">Выбор повода</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">2</div>
                                <div class="stepper__title">Герой песни</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">3</div>
                                <div class="stepper__title">История и стиль</div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Основная история</label>
                            <textarea name="story" class="form-textarea" rows="5" required><?= h($data['story'] ?? '') ?></textarea>
                            <?php if (isset($errors) && in_array('Укажите основную историю', $order_errors)): ?>
                                <div class="form-error"><?= h($order_errors[array_search('Укажите основную историю', $order_errors)]) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Что обязательно упомянуть</label>
                            <textarea name="must_include" class="form-textarea" rows="3" placeholder="Например: 'папа и дочь, 25 лет, первый танец'"><?= h($data['must_include'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Чего избегать</label>
                            <textarea name="must_exclude" class="form-textarea" rows="3" placeholder="Например: 'не упоминать бывшего партнёра'"><?= h($data['must_exclude'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Настроение</label>
                            <select name="mood" class="form-input">
                                <option value="радостная" <?= $data['mood'] === 'радостная' ? 'selected' : '' ?>>Радостная</option>
                                <option value="романтичная" <?= $data['mood'] === 'романтичная' ? 'selected' : '' ?>>Романтичная</option>
                                <option value="ностальгическая" <?= $data['mood'] === 'ностальгическая' ? 'selected' : '' ?>>Ностальгическая</option>
                                <option value="мотивирующая" <?= $data['mood'] === 'мотивирующая' ? 'selected' : '' ?>>Мотивирующая</option>
                                <option value="спокойная" <?= $data['mood'] === 'спокойная' ? 'selected' : '' ?>>Спокойная</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Стили музыки</label>
                            <div class="form-group__options">
                                <?php $styles = ['классическая', 'джаз', 'рок', 'поп', 'хип-хоп', 'электронная', 'фолк', 'кантри'] ?>
                                <?php foreach ($styles as $style): ?>
                                    <label class="form-checkbox" style="display: flex; align-items: center; gap: 8px; margin: 4px 0;">
                                        <input type="checkbox" name="music_styles[]" value="<?= h($style) ?>" <?= in_array($style, $data['music_styles'] ?? []) ? 'checked' : '' ?>>
                                        <span><?= h($style) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Тип голоса</label>
                            <select name="voice_type" class="form-input">
                                <option value="male" <?= $data['voice_type'] === 'male' ? 'selected' : '' ?>>Мужской</option>
                                <option value="female" <?= $data['voice_type'] === 'female' ? 'selected' : '' ?>>Женский</option>
                                <option value="duet" <?= $data['voice_type'] === 'duet' ? 'selected' : '' ?>>Дуэт</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Длительность</label>
                            <select name="duration_type" class="form-input">
                                <option value="short" <?= $data['duration_type'] === 'short' ? 'selected' : '' ?>>Короткая (до 2 мин)</option>
                                <option value="standard" <?= $data['duration_type'] === 'standard' ? 'selected' : '' ?>>Стандартная (2-4 мин)</option>
                                <option value="long" <?= $data['duration_type'] === 'long' ? 'selected' : '' ?>>Длинная (4+ мин)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Тариф</label>
                            <select name="tariff" class="form-input">
                                <option value="basic" <?= $data['tariff'] === 'basic' ? 'selected' : '' ?>>Базовый (2500 ₽)</option>
                                <option value="standard" <?= $data['tariff'] === 'standard' ? 'selected' : '' ?>>Стандарт (5000 ₽)</option>
                                <option value="premium" <?= $data['tariff'] === 'premium' ? 'selected' : '' ?>>Премиум (10000 ₽)</option>
                                <option value="corporate" <?= $data['tariff'] === 'corporate' ? 'selected' : '' ?>>Корпоративный (от 15000 ₽)</option>
                            </select>
                        </div>
                        
                        <div class="order-step-nav" style="margin-top: var(--space-xl);">
                            <div class="stepper">
                                <div class="stepper__step stepper__step--active">
                                    <div class="stepper__circle">1</div>
                                    <div class="stepper__title">Выбор повода</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">2</div>
                                    <div class="stepper__title">Герой песни</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">3</div>
                                    <div class="stepper__title">История и стиль</div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn--primary">Далее</button>
                            <a href="/order.php?step=2" class="btn btn--outline">Назад</a>
                        </div>
                    </div>
                    
                <?php endif; ?>
                
                <!-- Шаг 4: Текст песни -->
                <?php if ($current_step >= 4 && $current_step <= 4): ?>
                    <div class="step" id="step-4">
                        <div class="stepper">
                            <div class="stepper__step stepper__step--active">
                                <div class="stepper__circle">1</div>
                                <div class="stepper__title">Выбор повода</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">2</div>
                                <div class="stepper__title">Герой песни</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">3</div>
                                <div class="stepper__title">История и стиль</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">4</div>
                                <div class="stepper__title">Текст песни</div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Текст песни</label>
                            <textarea name="text" class="form-textarea" rows="10" required><?= h($data['text'] ?? '') ?></textarea>
                            <?php if (isset($errors) && in_array('Укажите текст песни', $order_errors)): ?>
                                <div class="form-error"><?= h($order_errors[array_search('Укажите текст песни', $order_errors)]) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="order-step-nav" style="margin-top: var(--space-xl);">
                            <div class="stepper">
                                <div class="stepper__step stepper__step--active">
                                    <div class="stepper__circle">1</div>
                                    <div class="stepper__title">Выбор повода</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">2</div>
                                    <div class="stepper__title">Герой песни</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">3</div>
                                    <div class="stepper__title">История и стиль</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">4</div>
                                    <div class="stepper__title">Текст песни</div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn--primary">Далее</button>
                            <a href="/order.php?step=3" class="btn btn--outline">Назад</a>
                        </div>
                    </div>
                    
                <?php endif; ?>
                
                <!-- Шаг 5: Музыка -->
                <?php if ($current_step >= 5 && $current_step <= 5): ?>
                    <div class="step" id="step-5">
                        <div class="stepper">
                            <div class="stepper__step stepper__step--active">
                                <div class="stepper__circle">1</div>
                                <div class="stepper__title">Выбор повода</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">2</div>
                                <div class="stepper__title">Герой песни</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">3</div>
                                <div class="stepper__title">История и стиль</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">4</div>
                                <div class="stepper__title">Текст песни</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">5</div>
                                <div class="stepper__title">Музыка</div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Стиль музыки</label>
                            <div class="form-group__options">
                                <label class="form-checkbox" style="display: flex; align-items: center; gap: 8px; margin: 4px 0;">
                                    <input type="checkbox" name="music_styles[]" value="классическая" <?= in_array('классическая', $data['music_styles'] ?? []) ? 'checked' : '' ?>>
                                    <span>Классическая</span>
                                </label>
                                <label class="form-checkbox" style="display: flex; align-items: center; gap: 8px; margin: 4px 0;">
                                    <input type="checkbox" name="music_styles[]" value="джаз" <?= in_array('джаз', $data['music_styles'] ?? []) ? 'checked' : '' ?>>
                                    <span>Джаз</span>
                                </label>
                                <label class="form-checkbox" style="display: flex; align-items: center; gap: 8px; margin: 4px 0;">
                                    <input type="checkbox" name="music_styles[]" value="рок" <?= in_array('рок', $data['music_styles'] ?? []) ? 'checked' : '' ?>>
                                    <span>Рок</span>
                                </label>
                                <label class="form-checkbox" style="display: flex; align-items: center; gap: 8px; margin: 4px 0;">
                                    <input type="checkbox" name="music_styles[]" value="поп" <?= in_array('поп', $data['music_styles'] ?? []) ? 'checked' : '' ?>>
                                    <span>Поп</span>
                                </label>
                                <label class="form-checkbox" style="display: flex; align-items: center; gap: 8px; margin: 4px 0;">
                                    <input type="checkbox" name="music_styles[]" value="хип-хоп" <?= in_array('хип-хоп', $data['music_styles'] ?? []) ? 'checked' : '' ?>>
                                    <span>Хип-хоп</span>
                                </label>
                                <label class="form-checkbox" style="display: flex; align-items: center; gap: 8px; margin: 4px 0;">
                                    <input type="checkbox" name="music_styles[]" value="электронная" <?= in_array('электронная', $data['music_styles'] ?? []) ? 'checked' : '' ?>>
                                    <span>Электронная</span>
                                </label>
                                <label class="form-checkbox" style="display: flex; align-items: center; gap: 8px; margin: 4px 0;">
                                    <input type="checkbox" name="music_styles[]" value="фолк" <?= in_array('фолк', $data['music_styles'] ?? []) ? 'checked' : '' ?>>
                                    <span>Фолк</span>
                                </label>
                                <label class="form-checkbox" style="display: flex; align-items: center; gap: 8px; margin: 4px 0;">
                                    <input type="checkbox" name="music_styles[]" value="кантри" <?= in_array('кантри', $data['music_styles'] ?? []) ? 'checked' : '' ?>>
                                    <span>Кантри</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Темп (BPM)</label>
                            <input type="number" name="tempo" class="form-input" min="40" max="200" value="<?= h($data['tempo'] ?? 120) ?>">
                        </div>
                        
                        <div class="order-step-nav" style="margin-top: var(--space-xl);">
                            <div class="stepper">
                                <div class="stepper__step stepper__step--active">
                                    <div class="stepper__circle">1</div>
                                    <div class="stepper__title">Выбор повода</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">2</div>
                                    <div class="stepper__title">Герой песни</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">3</div>
                                    <div class="stepper__title">История и стиль</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">4</div>
                                    <div class="stepper__title">Текст песни</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">5</div>
                                    <div class="stepper__title">Музыка</div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn--primary">Далее</button>
                            <a href="/order.php?step=4" class="btn btn--outline">Назад</a>
                        </div>
                    </div>
                    
                <?php endif; ?>
                
                <!-- Шаг 6: Контакты -->
                <?php if ($current_step >= 6 && $current_step <= 6): ?>
                    <div class="step" id="step-6">
                        <div class="stepper">
                            <div class="stepper__step stepper__step--active">
                                <div class="stepper__circle">1</div>
                                <div class="stepper__title">Выбор повода</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">2</div>
                                <div class="stepper__title">Герой песни</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">3</div>
                                <div class="stepper__title">История и стиль</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">4</div>
                                <div class="stepper__title">Текст песни</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">5</div>
                                <div class="stepper__title">Музыка</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">6</div>
                                <div class="stepper__title">Контакты</div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Имя клиента</label>
                            <input type="text" name="client_name" class="form-input" value="<?= h($data['client_name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Телефон</label>
                            <input type="text" name="client_phone" class="form-input" value="<?= h($data['client_phone'] ?? '') ?>" required>
                            <?php if (isset($errors) && in_array('Укажите телефон', $order_errors)): ?>
                                <div class="form-error"><?= h($order_errors[array_search('Укажите телефон', $order_errors)]) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Telegram</label>
                            <input type="text" name="client_telegram" class="form-input" value="<?= h($data['client_telegram'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">WhatsApp</label>
                            <input type="text" name="client_whatsapp" class="form-input" value="<?= h($data['client_whatsapp'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="client_email" class="form-input" value="<?= h($data['client_email'] ?? '') ?>" required>
                            <?php if (isset($errors) && in_array('Укажите email', $order_errors)): ?>
                                <div class="form-error"><?= h($order_errors[array_search('Укажите email', $order_errors)]) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Удобное время связи</label>
                            <input type="text" name="contact_time" class="form-input" value="<?= h($data['contact_time'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Предпочтительный способ связи</label>
                            <select name="preferred_contact" class="form-input">
                                <option value="sms" <?= $data['preferred_contact'] === 'sms' ? 'selected' : '' ?>>SMS</option>
                                <option value="telegram" <?= $data['preferred_contact'] === 'telegram' ? 'selected' : '' ?>>Telegram</option>
                                <option value="whatsapp" <?= $data['preferred_contact'] === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
                                <option value="phone" <?= $data['preferred_contact'] === 'phone' ? 'selected' : '' ?>>Телефон</option>
                            </select>
                        </div>
                        
                        <div class="order-step-nav" style="margin-top: var(--space-xl);">
                            <div class="stepper">
                                <div class="stepper__step stepper__step--active">
                                    <div class="stepper__circle">1</div>
                                    <div class="stepper__title">Выбор повода</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">2</div>
                                    <div class="stepper__title">Герой песни</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">3</div>
                                    <div class="stepper__title">История и стиль</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">4</div>
                                    <div class="stepper__title">Текст песни</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">5</div>
                                    <div class="stepper__title">Музыка</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">6</div>
                                    <div class="stepper__title">Контакты</div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn--primary">Далее</button>
                            <a href="/order.php?step=5" class="btn btn--outline">Назад</a>
                        </div>
                    </div>
                    
                <?php endif; ?>
                
                <!-- Шаг 7: Сроки и оплата -->
                <?php if ($current_step >= 7 && $current_step <= 7): ?>
                    <div class="step" id="step-7">
                        <div class="stepper">
                            <div class="stepper__step stepper__step--active">
                                <div class="stepper__circle">1</div>
                                <div class="stepper__title">Выбор повода</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">2</div>
                                <div class="stepper__title">Герой песни</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">3</div>
                                <div class="stepper__title">История и стиль</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">4</div>
                                <div class="stepper__title">Текст песни</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">5</div>
                                <div class="stepper__title">Музыка</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">6</div>
                                <div class="stepper__title">Контакты</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">7</div>
                                <div class="stepper__title">Сроки и оплата</div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Срок готовности</label>
                            <select name="delivery_time" class="form-input">
                                <option value="3-5_days" <?= $data['delivery_time'] === '3-5_days' ? 'selected' : '' ?>>3-5 дней</option>
                                <option value="2-4_days" <?= $data['delivery_time'] === '2-4_days' ? 'selected' : '' ?>>2-4 дня</option>
                                <option value="urgent" <?= $data['delivery_time'] === 'urgent' ? 'selected' : '' ?>>1-2 дня</option>
                                <option value="very_urgent" <?= $data['delivery_time'] === 'very_urgent' ? 'selected' : '' ?>>Сегодня-завтра</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Стоимость</label>
                            <div style="display: flex; align-items: center; gap: var(--space-sm);">
                                <span class="price-label"><?= h($data['price_calculated'] ?? format_price($tariff['price'])) ?></span>
                                <span class="price-label" style="color: var(--color-accent); font-weight: bold;"><?= h($data['tariff_label'] ?? $tariff['label']) ?></span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Способ оплаты</label>
                            <select name="payment_method" class="form-input">
                                <option value="card" <?= $data['payment_method'] === 'card' ? 'selected' : '' ?>>Карта</option>
                                <option value="transfer" <?= $data['payment_method'] === 'transfer' ? 'selected' : '' ?>>Наличные / перевод</option>
                                <option value="paypal" <?= $data['payment_method'] === 'paypal' ? 'selected' : '' ?>>PayPal</option>
                                <option value="other" <?= $data['payment_method'] === 'other' ? 'selected' : '' ?>>Другое</option>
                            </select>
                        </div>
                        
                        <div class="order-step-nav" style="margin-top: var(--space-xl);">
                            <div class="stepper">
                                <div class="stepper__step stepper__step--active">
                                    <div class="stepper__circle">1</div>
                                    <div class="stepper__title">Выбор повода</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">2</div>
                                    <div class="stepper__title">Герой песни</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">3</div>
                                    <div class="stepper__title">История и стиль</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">4</div>
                                    <div class="stepper__title">Текст песни</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">5</div>
                                    <div class="stepper__title">Музыка</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">6</div>
                                    <div class="stepper__title">Контакты</div>
                                </div>
                                <div class="stepper__step">
                                    <div class="stepper__circle">7</div>
                                    <div class="stepper__title">Сроки и оплата</div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn--primary">Подтвердить</button>
                            <a href="/order.php?step=6" class="btn btn--outline">Назад</a>
                        </div>
                    </div>
                    
                <?php endif; ?>
                
                <!-- Шаг 8: Подтверждение -->
                <?php if ($current_step >= 8 && $current_step <= 8): ?>
                    <div class="step" id="step-8">
                        <div class="stepper">
                            <div class="stepper__step stepper__step--active">
                                <div class="stepper__circle">1</div>
                                <div class="stepper__title">Выбор повода</div>
                            </div>
                            <div class="stepper__step">
                                <div class="stepper__circle">2</div>
                                <div class="stepper__title">Герой песни</div>
                            </div>
                            <div class="stepper__step">
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>