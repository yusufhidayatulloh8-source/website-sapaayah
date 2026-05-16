<?php
require_once __DIR__ . '/../includes/init.php';
require_login();

$page = clean_input($_GET['page'] ?? 'dashboard');
$allowed = [
    'dashboard', 'users', 'categories', 'articles', 'events', 'programs', 'program-registrations',
    'galleries', 'testimonials', 'donations', 'settings', 'contacts', 'comments', 'logs', 'profile'
];

if (!in_array($page, $allowed, true)) {
    include __DIR__ . '/../templates/error-404.php';
    exit;
}

$file = __DIR__ . '/../modules/admin/' . $page . '.php';
if (!file_exists($file)) {
    include __DIR__ . '/../templates/error-404.php';
    exit;
}

include $file;
