<?php
require_once __DIR__ . '/includes/init.php';

$page = clean_input($_GET['page'] ?? 'home');
$allowed = [
    'home', 'tentang', 'program', 'program-detail', 'event', 'event-detail',
    'artikel', 'artikel-detail', 'gallery', 'testimoni', 'kontak', 'donasi'
];

if (!in_array($page, $allowed, true)) {
    include __DIR__ . '/templates/error-404.php';
    exit;
}

$file = __DIR__ . '/modules/frontend/' . $page . '.php';
if (!file_exists($file)) {
    include __DIR__ . '/templates/error-404.php';
    exit;
}

include $file;
