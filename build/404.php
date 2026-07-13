<?php
/**
 * Страница 404 — не найдено
 * Путь: /public/404.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

http_response_code(404);

$page_meta = [
    'title'       => '404 — Страница не найдена | Хитовая Песня',
    'description' => 'Запрашиваемая страница не найдена.',
    'canonical'   => SITE_URL . '/404',
];

require_once __DIR__ . '/../includes/head-meta.php';
require_once __DIR__ . '/../includes/header.php';
?>

<main>
    <section class="error-section">
        <div class="container">
            <div class="error-card">
                <div class="error-card__code" aria-hidden="true">404</div>
                <div class="error-card__icon" aria-hidden="true">🎵</div>
                <h1 class="error-card__title">Страница не найдена</h1>
                <p class="error-card__desc">
                    Эта страница пропала, как несыгранная нота.
                    Возможно, адрес изменился или страница была удалена.
                </p>
                <div class="error-card__actions">
                    <a href="/" class="btn btn--primary btn--lg">На главную</a>
                    <a href="/portfolio.php" class="btn btn--outline btn--lg">Примеры работ</a>
                    <a href="/order.php" class="btn btn--outline btn--lg">Заказать песню</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>