<?php
return [
    'app_name' => 'Yayasan SAPA Ayah',
    'app_url' => 'http://localhost/sapa',
    'timezone' => 'Asia/Jakarta',
    'env' => 'development',
    'session_timeout' => 3600,
    'remember_me_days' => 30,
    'encryption_key' => 'SAPA_AYAH_AES_256_KEY_CHANGE_ME_2026',
    'encryption_cipher' => 'AES-256-CBC',
    'encryption_iv' => 'SAPAAYAH20260513',
    'upload_max_size' => 2097152,
    'allowed_mime' => [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/svg+xml',
        'video/mp4',
        'application/pdf'
    ],
    'admin_email' => 'admin@sapaayah.or.id'
];
