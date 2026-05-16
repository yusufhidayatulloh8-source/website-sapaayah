<?php
$auth = auth_user();
if (!$auth) {
    redirect('auth/login.php');
}
$adminPage = clean_input($_GET['page'] ?? 'dashboard');
$adminAssetVersion = '20260515-5';
$adminCss = asset('css/admin.css');
if (strpos($adminCss, '?') === false) {
    $adminCss .= '?v=' . $adminAssetVersion;
} else {
    $adminCss .= '&v=' . $adminAssetVersion;
}

if (!function_exists('admin_menu_icon_svg')) {
    function admin_menu_icon_svg(string $key): string
    {
        $icons = [
            'dashboard' => '<svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="4" rx="1.5"/><rect x="14" y="10" width="7" height="11" rx="1.5"/><rect x="3" y="13" width="7" height="8" rx="1.5"/></svg>',
            'articles' => '<svg viewBox="0 0 24 24" fill="none"><path d="M7 3h8l4 4v14H7z"/><path d="M15 3v4h4"/><path d="M10 12h6M10 16h6"/></svg>',
            'programs' => '<svg viewBox="0 0 24 24" fill="none"><path d="M12 3a4 4 0 0 1 4 4c0 2.2-1.8 4-4 4s-4-1.8-4-4a4 4 0 0 1 4-4Z"/><path d="M6.5 20a5.5 5.5 0 0 1 11 0"/></svg>',
            'program-registrations' => '<svg viewBox="0 0 24 24" fill="none"><path d="M8 4h8l2 2v14H6V6z"/><path d="M15 4v4h4"/><path d="M9 12h6M9 16h4"/></svg>',
            'events' => '<svg viewBox="0 0 24 24" fill="none"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg>',
            'galleries' => '<svg viewBox="0 0 24 24" fill="none"><rect x="4" y="5" width="16" height="14" rx="2"/><path d="m8 14 3-3 2 2 3-3 2 2"/><circle cx="9" cy="9" r="1.2"/></svg>',
            'testimonials' => '<svg viewBox="0 0 24 24" fill="none"><path d="M6 6h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H9l-5 3V8a2 2 0 0 1 2-2Z"/></svg>',
            'donations' => '<svg viewBox="0 0 24 24" fill="none"><rect x="4" y="8" width="16" height="12" rx="2"/><path d="M12 8V5m0 0-2 2m2-2 2 2"/><path d="M8 14h8"/></svg>',
            'users' => '<svg viewBox="0 0 24 24" fill="none"><path d="M12 3a4 4 0 1 1 0 8 4 4 0 0 1 0-8Z"/><path d="M5 20a7 7 0 0 1 14 0"/></svg>',
            'contacts' => '<svg viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>',
            'comments' => '<svg viewBox="0 0 24 24" fill="none"><path d="M4 6h16v10H8l-4 4z"/></svg>',
            'categories' => '<svg viewBox="0 0 24 24" fill="none"><path d="M3 7h8v8H3zM13 7h8v5h-8zM13 14h8v1"/></svg>',
            'settings' => '<svg viewBox="0 0 24 24" fill="none"><path d="M12 8.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7Z"/><path d="m19.4 15 1.2 2-2 3.5-2.3-.3a7.7 7.7 0 0 1-1.7 1l-.7 2.2H10l-.7-2.2c-.6-.2-1.2-.5-1.8-1l-2.2.3-2-3.5 1.2-2a8.5 8.5 0 0 1 0-2l-1.2-2 2-3.5 2.2.3c.6-.4 1.2-.7 1.8-1L10 .6h4l.7 2.2c.6.2 1.1.5 1.7 1l2.3-.3 2 3.5-1.2 2a8.5 8.5 0 0 1 0 2Z"/></svg>',
            'logs' => '<svg viewBox="0 0 24 24" fill="none"><path d="M12 7v5l3 2"/><circle cx="12" cy="12" r="9"/></svg>',
        ];

        $svg = $icons[$key] ?? $icons['dashboard'];
        return str_replace('<svg ', '<svg class="menu-icon-svg" ', $svg);
    }
}

$menuItems = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'DB'],
    ['key' => 'articles', 'label' => 'Artikel', 'icon' => 'AR'],
    ['key' => 'programs', 'label' => 'Program SAPA', 'icon' => 'PR'],
    ['key' => 'program-registrations', 'label' => 'Pendaftar Program', 'icon' => 'PD'],
    ['key' => 'events', 'label' => 'Event', 'icon' => 'EV'],
    ['key' => 'galleries', 'label' => 'Galeri', 'icon' => 'GL'],
    ['key' => 'testimonials', 'label' => 'Testimoni', 'icon' => 'TS'],
    ['key' => 'donations', 'label' => 'Donasi', 'icon' => 'DN'],
    ['key' => 'users', 'label' => 'Pengguna', 'icon' => 'US'],
    ['key' => 'contacts', 'label' => 'Kontak', 'icon' => 'CT'],
    ['key' => 'comments', 'label' => 'Komentar', 'icon' => 'CM'],
    ['key' => 'categories', 'label' => 'Kategori', 'icon' => 'KG'],
    ['key' => 'settings', 'label' => 'Pengaturan', 'icon' => 'ST'],
    ['key' => 'logs', 'label' => 'Log Aktivitas', 'icon' => 'LG'],
];

$menuBadges = [
    'articles' => (int) (db_one('SELECT COUNT(*) total FROM articles WHERE status = "published"')['total'] ?? 0),
    'events' => (int) (db_one('SELECT COUNT(*) total FROM events WHERE status = "upcoming"')['total'] ?? 0),
];

$pageDescriptions = [
    'dashboard' => 'Selamat datang kembali. Berikut ringkasan aktivitas hari ini.',
    'articles' => 'Kelola konten artikel dan publikasi yayasan.',
    'programs' => 'Atur program unggulan SAPA Ayah.',
    'program-registrations' => 'Pantau peserta yang mendaftar program.',
    'events' => 'Kelola jadwal event dan dokumentasi kegiatan.',
    'galleries' => 'Atur media foto dan video dokumentasi.',
    'testimonials' => 'Kelola testimoni peserta dan mitra.',
    'donations' => 'Verifikasi serta monitor data donasi.',
    'users' => 'Kelola akun pengguna dan hak akses.',
    'contacts' => 'Lihat pesan yang masuk dari formulir kontak.',
    'comments' => 'Moderasi komentar artikel.',
    'categories' => 'Atur kategori artikel.',
    'settings' => 'Konfigurasi identitas dan informasi website.',
    'logs' => 'Audit trail aktivitas sistem.',
    'profile' => 'Perbarui informasi akun Anda.',
];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'Admin Panel') ?></title>
    <link rel="icon" type="image/png" href="<?= asset('images/logo-sapa-mark.png?v=20260515-2') ?>">
    <link rel="shortcut icon" href="<?= asset('images/logo-sapa-mark.png?v=20260515-2') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e($adminCss) ?>">
</head>
<body>
<div class="admin-shell">
    <aside class="sidebar" data-sidebar>
        <div class="brand-admin">
            <div class="brand-badge" aria-hidden="true"><img src="<?= asset('images/logo-sapa-mark.png?v=20260515-2') ?>" alt=""></div>
            <div class="brand-text">
                <strong>SAPA Ayah</strong>
                <small>Admin Panel</small>
            </div>
            <button class="sidebar-collapse" type="button" data-sidebar-collapse aria-label="Collapse Sidebar">&lt;</button>
        </div>
        <nav class="nav-admin">
            <?php foreach ($menuItems as $item): ?>
                <a class="<?= $adminPage === $item['key'] ? 'active' : '' ?>" href="<?= base_url('admin/index.php?page=' . $item['key']) ?>">
                    <span class="nav-icon" aria-hidden="true"><?= admin_menu_icon_svg($item['key']) ?></span>
                    <span class="nav-label"><?= e($item['label']) ?></span>
                    <?php if (!empty($menuBadges[$item['key']])): ?>
                        <span class="nav-badge"><?= e((string) $menuBadges[$item['key']]) ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="sidebar-user">
            <div class="avatar-mini"><?= e(strtoupper(substr($auth['full_name'] ?? 'A', 0, 1))) ?></div>
            <div class="sidebar-user-text">
                <strong><?= e($auth['full_name']) ?></strong>
                <small><?= e($auth['email']) ?></small>
            </div>
        </div>
    </aside>
    <div class="sidebar-backdrop" data-sidebar-backdrop></div>
    <div class="main-panel">
        <div class="topbar">
            <div class="topbar-title">
                <button class="sidebar-mobile-btn" type="button" data-sidebar-toggle aria-label="Buka menu">MENU</button>
                <h1><?= e($pageHeading ?? 'Dashboard Admin') ?></h1>
                <small><?= e($pageDescriptions[$adminPage] ?? 'Kelola konten dan data Yayasan SAPA Ayah.') ?></small>
            </div>
            <div class="profile-menu" data-profile-menu>
                <button class="user-chip" type="button" data-profile-btn>
                    <img src="<?= $auth['profile_photo'] ? upload_url($auth['profile_photo']) : asset('images/logo-sapa.jpg') ?>" alt="Profile">
                    <span><?= e($auth['full_name']) ?></span>
                <small><?= e($auth['role_name'] ?? 'user') ?></small>
            </button>
            <div class="profile-dropdown">
                    <a href="<?= base_url('admin/index.php?page=profile') ?>">Edit Profile</a>
                    <a href="<?= base_url('admin/index.php?page=settings') ?>">Pengaturan Akun</a>
                    <a href="<?= base_url('auth/logout.php') ?>">Logout</a>
                </div>
            </div>
        </div>

        <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
