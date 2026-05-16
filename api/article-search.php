<?php
require_once __DIR__ . '/../includes/init.php';
header('Content-Type: application/json');

$query = clean_input($_GET['q'] ?? '');
if ($query === '') {
    echo json_encode(['status' => true, 'data' => []]);
    exit;
}

$data = db_all(
    'SELECT id, title, slug FROM articles WHERE status = "published" AND (title LIKE :q_title OR content LIKE :q_content) ORDER BY published_at DESC LIMIT 10',
    [
        'q_title' => '%' . $query . '%',
        'q_content' => '%' . $query . '%',
    ]
);

$result = array_map(function ($row) {
    return [
        'title' => $row['title'],
        'url' => base_url('?page=artikel-detail&ref=' . urlencode(encrypt_id((int)$row['id']))),
    ];
}, $data);

echo json_encode(['status' => true, 'data' => $result]);
