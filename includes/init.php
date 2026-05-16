<?php

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/logger.php';
register_error_handlers();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/upload.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/middleware.php';

restore_session_from_remember_me();
enforce_session_timeout();
require_csrf();
