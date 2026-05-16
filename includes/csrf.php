<?php

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    if (!$token || empty($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

function require_csrf(): void
{
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf($token)) {
            write_log('warning', 'Invalid CSRF token (POST)', ['ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
            http_response_code(419);
            include __DIR__ . '/../templates/error-419.php';
            exit;
        }
    }

    if ($method === 'GET' && isset($_GET['action'])) {
        $token = $_GET['csrf_token'] ?? '';
        if (!verify_csrf($token)) {
            write_log('warning', 'Invalid CSRF token (GET action)', ['ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
            http_response_code(419);
            include __DIR__ . '/../templates/error-419.php';
            exit;
        }
    }
}
