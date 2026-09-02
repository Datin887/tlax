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
    <link rel="icon"             type="image/svg+xml" href="/favicon.svg">
    <link rel="apple-touch-icon" sizes="180x180"      href="/assets/img/icons/apple-touch-icon.png">
    <link rel="manifest"         href="/manifest.json">
    <meta name="theme-color" content="#8B1E3F">

    <!-- ─── Preconnect (Google Fonts) ─── -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- ─── CSS ─── -->
    <link rel="stylesheet" href="/assets/css/variables.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/assets/css/reset.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/assets/css/main.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/assets/css/components.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/assets/css/responsive.css?v=<?= time() ?>">

    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($css, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
        <?php endforeach; ?>
    <?php endif; ?>

</head>
<body<?php if (isset($body_class)) echo ' class="' . htmlspecialchars($body_class, ENT_QUOTES, 'UTF-8') . '"'; ?>>