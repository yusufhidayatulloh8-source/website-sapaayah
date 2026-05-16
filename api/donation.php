<?php
require_once __DIR__ . '/../includes/init.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $proof = handle_upload('proof_file', 'qris', ['image/jpeg', 'image/png', 'application/pdf'], 2097152);
    $id = db_insert('donations', [
        'donor_name' => clean_input($_POST['donor_name'] ?? ''),
        'donor_email' => clean_input($_POST['donor_email'] ?? ''),
        'donor_phone' => clean_input($_POST['donor_phone'] ?? ''),
        'amount' => (float) ($_POST['amount'] ?? 0),
        'transfer_date' => clean_input($_POST['transfer_date'] ?? '') ?: null,
        'bank_name' => clean_input($_POST['bank_name'] ?? ''),
        'message' => xss_clean($_POST['message'] ?? ''),
        'proof_file' => $proof,
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    log_activity('create', 'donations', $id);
    echo json_encode(['status' => true, 'message' => 'Konfirmasi donasi berhasil dikirim']);
} catch (Throwable $e) {
    http_response_code(422);
    echo json_encode(['status' => false, 'message' => $e->getMessage()]);
}
