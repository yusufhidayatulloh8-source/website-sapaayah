<?php
require_once __DIR__ . '/../includes/init.php';
if (is_logged_in()) {
    log_activity('logout', 'auth', (int) $_SESSION['auth']['id']);
}
logout_user();
flash('success', 'Anda telah logout.');
redirect('auth/login.php');
