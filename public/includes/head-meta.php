<?php
/**
 * Общие мета-теги и стили для всех страниц
 * Путь: /public/includes/head-meta.php
 */

$meta_title = $page_meta['title'] ?? SEO_DEFAULT_TITLE;
$meta_desc = $page_meta['description'] ?? SEO_DEFAULT_DESCRIPTION;
$meta_kw = $page_meta['keywords'] ?? SEO_DEFAULT_KEYWORDS;
$meta_canonical = $page_meta['canonical'] ?? APP_URL;
?>
<!DOCTYPE html>
<html lang="<?= h(APP_LANG) ?>">
<head>
    <meta charset="<?= h(APP_CHARSET) ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($meta_title) ?></title>
    <meta name="description" content="<?= h($meta_desc) ?>">
    <meta name="keywords" content="<?= h($meta_kw) ?>">
    <link rel="canonical" href="<?= h($meta_canonical) ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= h($meta_title) ?>">
    <meta property="og:description" content="<?= h($meta_desc) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= h($meta_canonical) ?>">
    <meta property="og:image" content="<?= h(SEO_DEFAULT_OG_IMAGE) ?>">
    
    <!-- Favicon -->
    <link rel="icon" href="/assets/img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/responsive.css">
</head>
<body>