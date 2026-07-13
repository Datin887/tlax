<?php
/**
 * Список заявок с фильтрами и поиском
 * Путь: /admin/orders.php
 */

declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/includes/auth.php';
require_auth();

$page_title = 'Заявки';

// ─── Параметры фильтрации ───
$filter_status   = preg_replace('/[^a-z_]/', '', $_GET['status'] ?? '');
$filter_tariff   = preg_replace('/[^a-z_]/', '', $_GET['tariff'] ?? '');
$filter_occasion = preg_replace('/[^a-z_]/', '', $_GET['occasion'] ?? '');
$search          = sanitize_string($_GET['search'] ?? '', 100);
$page            = max(1, (int)($_GET['page'] ?? 1));
$per_page        = 20;

try {
    $db = Database::getInstance();

    // ─── WHERE условия ───
    $conditions = ['1=1'];
    $params     = [];

    if ($filter_status) {
        $conditions[] = 'status = :status';
        $params[':status'] = $filter_status;
    }
    if ($filter_tariff) {
        $conditions[] = 'tariff = :tariff';
        $params[':tariff'] = $filter_tariff;
    }
    if ($filter_occasion) {
        $conditions[] = 'occasion = :occasion';
        $params[':occasion'] = $filter_occasion;
    }
    if ($search) {
        $conditions[] = '(client_name LIKE :search OR client_phone LIKE :search OR order_number LIKE :search OR hero_name LIKE :search)';
        $params[':search'] = '%' . $search . '%';
    }

    $where = 'WHERE ' . implode(' AND ', $conditions);

    // Общее количество
    $total_row = $db->fetchOne("SELECT COUNT(*) AS total FROM orders {$where}", $params);
    $total     = (int)($total_row['total'] ?? 0);
    $pages     = (int)ceil($total / $per_page);

    // Заявки
    $offset = ($page - 1) * $per_page;
    $params[':limit']  = $per_page;
    $params[':offset'] = $offset;

    $orders = $db->fetchAll(
        "SELECT id, order_number, client_name, client_phone, occasion, tariff, urgency, status, created_at
         FROM orders
         {$where}
         ORDER BY created_at DESC
         LIMIT :limit OFFSET :offset",
        $params
    );

    // Счётчики по статусам (для вкладок)
    $status_counts = $db->fetchAll(
        "SELECT status, COUNT(*) AS cnt FROM orders GROUP BY status"
    );
    $status_map = [];
    foreach ($status_counts as $row) {
        $status_map[$row['status']] = (int)$row['cnt'];
    }

} catch (Exception $e) {
    log_error('admin/orders: ' . $e->getMessage());
    $orders = [];
    $total  = 0;
    $pages  = 0;
    $status_map = [];
}

$statuses = [
    ''            => ['label' => 'Все',       'count' => $total],
    'new'         => ['label' => 'Новые',     'count' => $status_map['new'] ?? 0],
    'in_progress' => ['label' => 'В работе',  'count' => $status_map['in_progress'] ?? 0],
    'review'      => ['label' => 'На проверке','count' => $status_map['review'] ?? 0],
    'done'        => ['label' => 'Выполнены', 'count' => $status_map['done'] ?? 0],
    'cancelled'   => ['label' => 'Отменены',  'count' => $status_map['cancelled'] ?? 0],
];

require_once __DIR__ . '/includes/admin-header.php';
?>

<!-- Вкладки статусов -->
<div class="admin-tabs">
    <?php foreach ($statuses as $status_val => $status_info): ?>
        <?php
            $tab_url = '/admin/orders.php?' . http_build_query(array_filter([
                'status'   => $status_val,
                'tariff'   => $filter_tariff,
                'occasion' => $filter_occasion,
                'search'   => $search,
            ]));
        ?>
        <a
            href="<?= h($tab_url) ?>"
            class="admin-tab<?= $filter_status === $status_val ? ' active' : '' ?>"
        >
            <?= h($status_info['label']) ?>
            <span class="admin-tab__count"><?= $status_info['count'] ?></span>
        </a>
    <?php endforeach; ?>
</div>

<!-- Фильтры и поиск -->
<div class="admin-card admin-card--filters">
    <form method="GET" action="/admin/orders.php" class="admin-filters">

        <input type="hidden" name="status" value="<?= h($filter_status) ?>">

        <div class="admin-filter__search">
            <input
                type="search"
                name="search"
                class="form-input"
                placeholder="Поиск по имени, телефону, номеру…"
                value="<?= h($search) ?>"
            >
        </div>

        <select name="tariff" class="form-input form-select--sm">
            <option value="">Все тарифы</option>
            <option value="basic"    <?= $filter_tariff === 'basic'    ? 'selected' : '' ?>>Базовый</option>
            <option value="standard" <?= $filter_tariff === 'standard' ? 'selected' : '' ?>>Стандарт</option>
            <option value="premium"  <?= $filter_tariff === 'premium'  ? 'selected' : '' ?>>Премиум</option>
            <option value="help"     <?= $filter_tariff === 'help'     ? 'selected' : '' ?>>Помогите выбрать</option>
        </select>

        <select name="occasion" class="form-input form-select--sm">
            <option value="">Все поводы</option>
            <?php foreach (['wedding','anniversary','birthday','love','corporate','march8','feb23','newyear','proposal','birth','retirement','other'] as $occ): ?>
                <option value="<?= $occ ?>" <?= $filter_occasion === $occ ? 'selected' : '' ?>>
                    <?= h(get_occasion_label($occ)) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn--primary btn--sm">Найти</button>

        <?php if ($search || $filter_tariff || $filter_occasion): ?>
            <a href="/admin/orders.php?status=<?= h($filter_status) ?>" class="btn btn--ghost btn--sm">
                Сбросить
            </a>
        <?php endif; ?>

    </form>
</div>

<!-- Таблица заявок -->
<div class="admin-card">
    <div class="admin-card__header">
        <h2 class="admin-card__title">
            Заявки
            <span class="admin-card__count"><?= $total ?></span>
        </h2>
    </div>
    <div class="admin-card__body admin-card__body--no-pad">

        <?php if (empty($orders)): ?>
            <div class="admin-empty" style="padding: var(--space-2xl);">
                <p>📭 Заявок не найдено</p>
                <?php if ($search || $filter_tariff || $filter_occasion || $filter_status): ?>
                    <a href="/admin/orders.php" class="btn btn--outline btn--sm" style="margin-top: var(--space-sm);">
                        Показать все
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Номер</th>
                            <th>Дата</th>
                            <th>Клиент</th>
                            <th>Телефон</th>
                            <th>Повод</th>
                            <th>Тариф</th>
                            <th>Срочность</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr class="admin-table__row">
                                <td>
                                    <a href="/admin/order-view.php?id=<?= (int)$order['id'] ?>" class="order-number">
                                        <?= h($order['order_number']) ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="admin-date">
                                        <?= date('d.m.Y', strtotime($order['created_at'])) ?><br>
                                        <small><?= date('H:i', strtotime($order['created_at'])) ?></small>
                                    </span>
                                </td>
                                <td><b><?= h($order['client_name']) ?></b></td>
                                <td>
                                    <a href="tel:<?= h(preg_replace('/\D/', '', $order['client_phone'])) ?>" class="admin-phone">
                                        <?= h($order['client_phone']) ?>
                                    </a>
                                </td>
                                <td><?= h(get_occasion_label($order['occasion'])) ?></td>
                                <td><?= h(get_tariff_label($order['tariff'])) ?></td>
                                <td><?= h(get_urgency_label($order['urgency'])) ?></td>
                                <td><?= render_status_badge($order['status']) ?></td>
                                <td>
                                    <a href="/admin/order-view.php?id=<?= (int)$order['id'] ?>" class="btn btn--sm btn--outline">
                                        Открыть
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Пагинация -->
            <?php if ($pages > 1): ?>
                <div class="pagination" style="padding: var(--space-lg);">
                    <?php for ($p = 1; $p <= $pages; $p++): ?>
                        <?php
                            $page_url = '/admin/orders.php?' . http_build_query(array_filter([
                                'status'   => $filter_status,
                                'tariff'   => $filter_tariff,
                                'occasion' => $filter_occasion,
                                'search'   => $search,
                                'page'     => $p,
                            ]));
                        ?>
                        <a
                            href="<?= h($page_url) ?>"
                            class="pagination__btn<?= $p === $page ? ' active' : '' ?>"
                        ><?= $p ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>