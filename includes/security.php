<?php

function urlsafe_b64encode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function urlsafe_b64decode(string $data): string
{
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $data .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($data, '-_', '+/')) ?: '';
}

function encrypt_param(string $plain): string
{
    $cipher = app_config('encryption_cipher');
    $key = hash('sha256', app_config('encryption_key'), true);
    $iv = substr(hash('sha256', app_config('encryption_iv'), true), 0, openssl_cipher_iv_length($cipher));

    $encrypted = openssl_encrypt($plain, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    return urlsafe_b64encode($encrypted ?: '');
}

function decrypt_param(string $encrypted): ?string
{
    $cipher = app_config('encryption_cipher');
    $key = hash('sha256', app_config('encryption_key'), true);
    $iv = substr(hash('sha256', app_config('encryption_iv'), true), 0, openssl_cipher_iv_length($cipher));

    $decoded = urlsafe_b64decode($encrypted);
    if ($decoded === '') {
        return null;
    }

    $decrypted = openssl_decrypt($decoded, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    return $decrypted === false ? null : $decrypted;
}

function encrypt_id(int $id): string
{
    return encrypt_param((string) $id);
}

function decrypt_id(?string $value): ?int
{
    if (!$value) {
        return null;
    }

    $decrypted = decrypt_param($value);
    if ($decrypted === null || !ctype_digit($decrypted)) {
        return null;
    }

    return (int) $decrypted;
}

function request_string(string $key, string $default = ''): string
{
    return clean_input($_POST[$key] ?? $_GET[$key] ?? $default);
}

function request_int(string $key, int $default = 0): int
{
    $value = $_POST[$key] ?? $_GET[$key] ?? $default;
    return filter_var($value, FILTER_VALIDATE_INT) !== false ? (int) $value : $default;
}

function xss_clean(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function random_token(int $length = 32): string
{
    return bin2hex(random_bytes($length));
}
