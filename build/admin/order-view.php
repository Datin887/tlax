<?php
/**
 * Просмотр и редактирование заявки
 * Путь: /admin/order-view.php
 */

declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/includes/auth.php';
require_auth();

$order_id = (int)($_GET['id'] ?? 0);
if (!$order_id) {
    header('Location: /admin/orders.php');
    exit;
}

$csrf_token = generate_csrf_token();
$message    = '';
$msg_type   = '';

try {
    $db    = Database::getInstance();
    $order = $db->fetchOne("SELECT * FROM orders WHERE id = :id", [':id' => $order_id]);

    if (!$order) {
        header('Location: /admin/orders.php');
        exit;
    }

    // ─── Смена статуса ───
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $message  = 'Ошибка CSRF';
            $msg_type = 'error';
        } else {
            $new_status = preg_replace('/[^a-z_]/', '', $_POST['new_status'] ?? '');
            $note       = sanitize_text($_POST['status_note'] ?? '', 500);

            $allowed_statuses = ['new', 'in_progress', 'review', 'done', 'cancelled'];
            if (in_array($new_status, $allowed_statuses, true)) {
                $old_status = $order['status'];

                $db->execute(
                    "UPDATE orders SET status = :status, updated_at = NOW() WHERE id = :id",
                    [':status' => $new_status, ':id' => $order_id]
                );

                $db->execute(
                    "INSERT INTO order_logs (order_id, action, old_status, new_status, note, created_at)
                     VALUES (:order_id, 'status_change', :old, :new, :note, NOW())",
                    [
                        ':order_id' => $order_id,
                        ':old'      => $old_status,
                        ':new'      => $new_status,
                        ':note'     => $note,
                    ]
                );

                $order['status'] = $new_status;
                $message  = 'Статус обновлён';
                $msg_type = 'success';
            }
        }
    }

    // ─── История изменений ───
    $logs = $db->fetchAll(
        "SELECT * FROM order_logs WHERE order_id = :id ORDER BY created_at DESC LIMIT 20",
        [':id' => $order_id]
    );

} catch (Exception $e) {
    log_error('admin/order-view: ' . $e->getMessage());
    header('Location: /admin/orders.php');
    exit;
}

$page_title = 'Заявка ' . h($order['order_number']);

require_once __DIR__ . '/includes/admin-header.php';
?>

<?php if ($message): ?>
    <div class="alert alert--<?= $msg_type === 'success' ? 'success' : 'error' ?>">
        <?= h($message) ?>
    </div>
<?php endif; ?>

<!-- Хлебные крошки -->
<div class="admin-breadcrumb">
    <a href="/admin/orders.php">Заявки</a> › <?= h($order['order_number']) ?>
</div>

<div class="order-view-grid">

    <!-- ─── Левая колонка: данные ─── -->
    <div class="order-view-main">

        <!-- Статус и быстрые действия -->
        <div class="admin-card">
            <div class="admin-card__header">
                <div>
                    <h2 class="admin-card__title"><?= h($order['order_number']) ?></h2>
                    <div style="margin-top: 6px;"><?= render_status_badge($order['status']) ?></div>
                </div>
                <div class="admin-card__actions">
                    <?php if ($order['client_phone']): ?>
                        <a href="tel:<?= h(preg_replace('/\D/', '', $order['client_phone'])) ?>" class="btn btn--sm btn--primary">
                            📱 Позвонить
                        </a>
                    <?php endif; ?>
                    <?php if ($order['client_telegram']): ?>
                        <a href="https://t.me/<?= h(ltrim($order['client_telegram'], '@')) ?>" class="btn btn--sm btn--outline" target="_blank" rel="noopener">
                            ✈️ Telegram
                        </a>
                    <?php endif; ?>
                    <?php if ($order['client_whatsapp']): ?>
                        <a href="https://wa.me/<?= h(preg_replace('/\D/', '', $order['client_whatsapp'])) ?>" class="btn btn--sm btn--outline" target="_blank" rel="noopener">
                            💚 WhatsApp
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Данные клиента -->
        <div class="admin-card">
            <div class="admin-card__header">
                <h2 class="admin-card__title">Клиент</h2>
            </div>
            <div class="admin-card__body">
                <dl class="order-dl">
                    <div class="order-dl__row">
                        <dt>Имя</dt>
                        <dd><?= h($order['client_name']) ?></dd>
                    </div>
                    <div class="order-dl__row">
                        <dt>Телефон</dt>
                        <dd>
                            <a href="tel:<?= h(preg_replace('/\D/', '', $order['client_phone'])) ?>">
                                <?= h($order['client_phone']) ?>
                            </a>
                        </dd>
                    </div>
                    <?php if ($order['client_telegram']): ?>
                        <div class="order-dl__row">
                            <dt>Telegram</dt>
                            <dd><a href="https://t.me/<?= h(ltrim($order['client_telegram'], '@')) ?>" target="_blank"><?= h($order['client_telegram']) ?></a></dd>
                        </div>
                    <?php endif; ?>
                    <?php if ($order['client_whatsapp']): ?>
                        <div class="order-dl__row">
                            <dt>WhatsApp</dt>
                            <dd><?= h($order['client_whatsapp']) ?></dd>
                        </div>
                    <?php endif; ?>
                    <?php if ($order['client_vk']): ?>
                        <div class="order-dl__row">
                            <dt>ВКонтакте</dt>
                            <dd><a href="<?= h($order['client_vk']) ?>" target="_blank"><?= h($order['client_vk']) ?></a></dd>
                        </div>
                    <?php endif; ?>
                    <?php if ($order['client_ok']): ?>
                        <div class="order-dl__row">
                            <dt>Одноклассники</dt>
                            <dd><a href="<?= h($order['client_ok']) ?>" target="_blank"><?= h($order['client_ok']) ?></a></dd>
                        </div>
                    <?php endif; ?>
                    <?php if ($order['client_email']): ?>
                        <div class="order-dl__row">
                            <dt>Email</dt>
                            <dd><a href="mailto:<?= h($order['client_email']) ?>"><?= h($order['client_email']) ?></a></dd>
                        </div>
                    <?php endif; ?>
                    <div class="order-dl__row">
                        <dt>Связаться</dt>
                        <dd><?= h(get_contact_time_label($order['contact_time'])) ?> — <?= h($order['contact_method']) ?></dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Детали заказа -->
        <div class="admin-card">
            <div class="admin-card__header">
                <h2 class="admin-card__title">Детали заказа</h2>
            </div>
            <div class="admin-card__body">
                <dl class="order-dl">
                    <div class="order-dl__row">
                        <dt>Повод</dt>
                        <dd>
                            <?= h(get_occasion_label($order['occasion'])) ?>
                            <?= $order['occasion'] === 'other' && $order['occasion_other'] ? ': ' . h($order['occasion_other']) : '' ?>
                        </dd>
                    </div>
                    <?php if ($order['event_date']): ?>
                        <div class="order-dl__row">
                            <dt>Дата</dt>
                            <dd><?= date('d.m.Y', strtotime($order['event_date'])) ?></dd>
                        </div>
                    <?php endif; ?>
                    <div class="order-dl__row">
                        <dt>Срочность</dt>
                        <dd><?= h(get_urgency_label($order['urgency'])) ?></dd>
                    </div>
                    <div class="order-dl__row">
                        <dt>Тариф</dt>
                        <dd><?= h(get_tariff_label($order['tariff'])) ?></dd>
                    </div>
                    <div class="order-dl__row">
                        <dt>Герой</dt>
                        <dd>
                            <?= h($order['hero_name']) ?>
                            <?= $order['hero_age'] ? ', ' . (int)$order['hero_age'] . ' лет' : '' ?>
                            <?= $order['hero_relation'] ? ' (' . h($order['hero_relation']) . ')' : '' ?>
                        </dd>
                    </div>
                    <?php if ($order['hero_profession']): ?>
                        <div class="order-dl__row">
                            <dt>Профессия</dt>
                            <dd><?= h($order['hero_profession']) ?></dd>
                        </div>
                    <?php endif; ?>
                    <?php if ($order['hero_hobbies']): ?>
                        <div class="order-dl__row">
                            <dt>Хобби</dt>
                            <dd><?= h($order['hero_hobbies']) ?></dd>
                        </div>
                    <?php endif; ?>
                    <div class="order-dl__row">
                        <dt>Настроение</dt>
                        <dd><?= h($order['mood'] ?: 'Не указано') ?></dd>
                    </div>
                    <?php if ($order['music_styles']): ?>
                        <div class="order-dl__row">
                            <dt>Стиль</dt>
                            <dd><?= h($order['music_styles']) ?></dd>
                        </div>
                    <?php endif; ?>
                    <div class="order-dl__row">
                        <dt>Голос</dt>
                        <dd><?= h($order['voice_type'] ?: 'Не указан') ?></dd>
                    </div>
                    <div class="order-dl__row">
                        <dt>Длительность</dt>
                        <dd><?= h(get_duration_label($order['duration'])) ?></dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Текст истории -->
        <div class="admin-card">
            <div class="admin-card__header">
                <h2 class="admin-card__title">История</h2>
            </div>
            <div class="admin-card__body">
                <div class="order-story">
                    <?= nl2br(h($order['story'])) ?>
                </div>
                <?php if ($order['must_include']): ?>
                    <div class="order-story-section">
                        <strong>Обязательно упомянуть:</strong>
                        <p><?= nl2br(h($order['must_include'])) ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($order['avoid']): ?>
                    <div class="order-story-section">
                        <strong>Избегать:</strong>
                        <p><?= nl2br(h($order['avoid'])) ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($order['extra_wishes']): ?>
                    <div class="order-story-section">
                        <strong>Дополнительные пожелания:</strong>
                        <p><?= nl2br(h($order['extra_wishes'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /.order-view-main -->


    <!-- ─── Правая колонка: управление ─── -->
    <div class="order-view-sidebar">

        <!-- Смена статуса -->
        <div class="admin-card">
            <div class="admin-card__header">
                <h2 class="admin-card__title">Изменить статус</h2>
            </div>
            <div class="admin-card__body">
                <form method="POST" action="/admin/order-view.php?id=<?= $order_id ?>">
                    <input type="hidden" name="change_status" value="1">
                    <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">

                    <div class="form-group">
                        <label class="form-label" for="new_status">Новый статус</label>
                        <select name="new_status" id="new_status" class="form-input">
                            <option value="new"         <?= $order['status'] === 'new'         ? 'selected' : '' ?>>🆕 Новая</option>
                            <option value="in_progress" <?= $order['status'] === 'in_progress' ? 'selected' : '' ?>>⚙️ В работе</option>
                            <option value="review"      <?= $order['status'] === 'review'      ? 'selected' : '' ?>>👁️ На проверке</option>
                            <option value="done"        <?= $order['status'] === 'done'        ? 'selected' : '' ?>>✅ Выполнена</option>
                            <option value="cancelled"   <?= $order['status'] === 'cancelled'   ? 'selected' : '' ?>>❌ Отменена</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="status_note">Примечание</label>
                        <textarea id="status_note" name="status_note" class="form-textarea" rows="3" placeholder="Комментарий к смене статуса…" maxlength="500"></textarea>
                    </div>

                    <button type="submit" class="btn btn--primary btn--full">Сохранить</button>
                </form>
            </div>
        </div>

        <!-- Мета-данные -->
        <div class="admin-card">
            <div class="admin-card__header">
                <h2 class="admin-card__title">Информация</h2>
            </div>
            <div class="admin-card__body">
                <dl class="order-dl">
                    <div class="order-dl__row">
                        <dt>Создана</dt>
                        <dd><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></dd>
                    </div>
                    <?php if ($order['updated_at']): ?>
                        <div class="order-dl__row">
                            <dt>Обновлена</dt>
                            <dd><?= date('d.m.Y H:i', strtotime($order['updated_at'])) ?></dd>
                        </div>
                    <?php endif; ?>
                    <div class="order-dl__row">
                        <dt>IP</dt>
                        <dd style="font-family:monospace;"><?= h($order['ip_address']) ?></dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- История изменений -->
        <?php if (!empty($logs)): ?>
            <div class="admin-card">
                <div class="admin-card__header">
                    <h2 class="admin-card__title">История</h2>
                </div>
                <div class="admin-card__body">
                    <ul class="order-log">
                        <?php foreach ($logs as $log): ?>
                            <li class="order-log__item">
                                <div class="order-log__time">
                                    <?= date('d.m.Y H:i', strtotime($log['created_at'])) ?>
                                </div>
                                <div class="order-log__text">
                                    <?php if ($log['action'] === 'status_change'): ?>
                                        Статус: <b><?= h($log['old_status']) ?></b> → <b><?= h($log['new_status']) ?></b>
                                    <?php else: ?>
                                        <?= h($log['action']) ?>
                                    <?php endif; ?>
                                    <?php if ($log['note']): ?>
                                        <div class="order-log__note"><?= h($log['note']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

    </div><!-- /.order-view-sidebar -->

</div><!-- /.order-view-grid -->

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>