<?php
if (!isset($pageTitle)) {
    $pageTitle = setting('site_name', 'Yayasan SAPA Ayah');
}
$currentPage = $_GET['page'] ?? 'home';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= asset('images/logo-sapa-mark.png?v=20260515-2') ?>">
    <link rel="shortcut icon" href="<?= asset('images/logo-sapa-mark.png?v=20260515-2') ?>">
    <meta name="description" content="Yayasan SAPA Ayah - Sahabat Pembelajar Ayah. Menyapa untuk Tumbuh, Tumbuh untuk Menyapa.">
    <meta name="keywords" content="SAPA Ayah, Yayasan, Pengasuhan, Ayah, Keluarga">
    <meta name="author" content="Yayasan SAPA Ayah">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css?v=20260516-1') ?>">
</head>
<body>
<header class="site-header" id="top">
    <div class="container nav-wrap">
        <a href="<?= base_url() ?>" class="brand" aria-label="Yayasan SAPA Ayah">
            <img src="<?= asset('images/logo-sapa-mark.png?v=20260515-2') ?>" alt="Logo SAPA Ayah" loading="lazy">
            <div>
                <strong>Yayasan SAPA Ayah</strong>
                <small>Sahabat Pembelajar Ayah</small>
            </div>
        </a>
        <button class="menu-toggle" type="button" data-menu-toggle aria-label="Buka Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <nav class="main-nav" data-menu>
            <a class="<?= active_page('home') ?>" href="<?= base_url('?page=home') ?>">Beranda</a>
            <a class="<?= active_page('tentang') ?>" href="<?= base_url('?page=tentang') ?>">Tentang</a>
            <a class="<?= active_page('program') ?>" href="<?= base_url('?page=program') ?>">Program</a>
            <a class="<?= active_page('gallery') ?>" href="<?= base_url('?page=gallery') ?>">Media</a>
            <a class="<?= active_page('event') ?>" href="<?= base_url('?page=event') ?>">Event</a>
            <a class="<?= active_page('artikel') ?>" href="<?= base_url('?page=artikel') ?>">Artikel</a>
            <a class="<?= active_page('kontak') ?>" href="<?= base_url('?page=kontak') ?>">Kontak</a>
            <a class="nav-login" href="<?= base_url('auth/login.php') ?>">Login Admin</a>
            <a class="btn btn-sm" href="<?= base_url('?page=donasi') ?>">Donasi</a>
        </nav>
    </div>
</header>
<main>
