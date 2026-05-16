<?php

function handle_upload(string $fieldName, string $folder, array $allowedMime = [], int $maxSize = 0): ?string
{
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload gagal dengan kode: ' . $file['error']);
    }

    $maxSize = $maxSize > 0 ? $maxSize : (int) app_config('upload_max_size', 2097152);
    if ($file['size'] > $maxSize) {
        throw new RuntimeException('Ukuran file melebihi batas maksimum.');
    }

    $allowedMime = $allowedMime ?: app_config('allowed_mime', []);
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedMime, true)) {
        throw new RuntimeException('Tipe file tidak diizinkan.');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $safeName = date('YmdHis') . '_' . bin2hex(random_bytes(8)) . '.' . $ext;

    $targetDir = __DIR__ . '/../uploads/' . trim($folder, '/');
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $targetPath = $targetDir . '/' . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('Gagal menyimpan file upload.');
    }

    return trim($folder, '/') . '/' . $safeName;
}
