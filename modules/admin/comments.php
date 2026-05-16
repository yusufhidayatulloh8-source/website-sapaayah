<?php
admin_guard('articles.view');
$pageTitle = 'Moderasi Komentar - SAPA Admin';
$pageHeading = 'Moderasi Komentar';

$action = clean_input($_GET['action'] ?? '');
$refId = decrypt_id($_GET['ref'] ?? '');

if ($action && $refId) {
    if ($action === 'approve') {
        db_update('comments', ['status' => 'approved', 'updated_at' => now()], 'id = :id', ['id' => $refId]);
        log_activity('approve', 'comments', $refId);
        flash('success', 'Komentar disetujui.');
    } elseif ($action === 'reject') {
        db_update('comments', ['status' => 'rejected', 'updated_at' => now()], 'id = :id', ['id' => $refId]);
        log_activity('reject', 'comments', $refId);
        flash('success', 'Komentar ditolak.');
    } elseif ($action === 'delete' && has_role('admin')) {
        db_delete('comments', 'id = :id', ['id' => $refId]);
        log_activity('delete', 'comments', $refId);
        flash('success', 'Komentar dihapus.');
    }
    redirect('admin/index.php?page=comments');
}

$rows = db_all('SELECT c.*, a.title AS article_title FROM comments c LEFT JOIN articles a ON a.id = c.article_id ORDER BY c.id DESC');

include __DIR__ . '/../../admin/partials/header.php';
?>
<section class="panel">
    <h3>Daftar Komentar Artikel</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Artikel</th><th>Pengirim</th><th>Komentar</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e($row['article_title'] ?: '-') ?></td>
                    <td><?= e($row['name'] ?: '-') ?><br><small><?= e($row['email'] ?: '-') ?></small></td>
                    <td><?= e(limit_words($row['comment'], 18)) ?></td>
                    <td><?= e($row['status']) ?></td>
                    <td>
                        <a class="btn btn-success" href="<?= base_url('admin/index.php?page=comments&action=approve&csrf_token=' . urlencode(csrf_token()) . '&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Approve</a>
                        <a class="btn btn-secondary" href="<?= base_url('admin/index.php?page=comments&action=reject&csrf_token=' . urlencode(csrf_token()) . '&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Reject</a>
                        <?php if (has_role('admin')): ?>
                            <a class="btn btn-danger" data-confirm="Hapus komentar ini?" href="<?= base_url('admin/index.php?page=comments&action=delete&csrf_token=' . urlencode(csrf_token()) . '&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/../../admin/partials/footer.php'; ?>

