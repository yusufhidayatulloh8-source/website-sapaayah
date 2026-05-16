<?php
require_once __DIR__ . '/../includes/init.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed']);
    exit;
}

$name = clean_input($_POST['name'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $message === '') {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => 'Nama dan pesan wajib diisi']);
    exit;
}

$id = db_insert('contacts', [
    'name' => $name,
    'email' => clean_input($_POST['email'] ?? ''),
    'phone' => clean_input($_POST['phone'] ?? ''),
    'subject' => clean_input($_POST['subject'] ?? ''),
    'message' => xss_clean($message),
    'is_read' => 0,
    'created_at' => now(),
]);

log_activity('create', 'contacts', $id);
echo json_encode(['status' => true, 'message' => 'Pesan berhasil dikirim']);
