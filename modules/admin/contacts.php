<?php
admin_guard('dashboard.view');
$pageTitle = 'Pesan Kontak - SAPA Admin';
$pageHeading = 'Pesan Kontak';

$action = clean_input($_GET['action'] ?? '');
$refId = decrypt_id($_GET['ref'] ?? '');

if ($action === 'read' && $refId) {
    db_update('contacts', ['is_read' => 1], 'id = :id', ['id' => $refId]);
    log_activity('read', 'contacts', $refId);
    redirect('admin/index.php?page=contacts');
}

if ($action === 'delete' && $refId && has_role('admin')) {
    db_delete('contacts', 'id = :id', ['id' => $refId]);
    log_activity('delete', 'contacts', $refId);
    flash('success', 'Pesan kontak dihapus.');
    redirect('admin/index.php?page=contacts');
}

$rows = db_all('SELECT * FROM contacts ORDER BY id DESC');

include __DIR__ . '/../../admin/partials/header.php';
?>
<section class="panel">
    <h3>Inbox Kontak</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama</th><th>Subject</th><th>Pesan</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e($row['name']) ?><br><small><?= e($row['email']) ?> <?= e($row['phone']) ?></small></td>
                    <td><?= e($row['subject']) ?></td>
                    <td><?= e(limit_words($row['message'], 16)) ?></td>
                    <td><?= (int)$row['is_read'] === 1 ? 'Read' : 'Unread' ?></td>
                    <td>
                        <?php if ((int)$row['is_read'] === 0): ?>
                            <a class="btn btn-success" href="<?= base_url('admin/index.php?page=contacts&action=read&csrf_token=' . urlencode(csrf_token()) . '&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Mark Read</a>
                        <?php endif; ?>
                        <?php if (has_role('admin')): ?>
                            <a class="btn btn-danger" data-confirm="Hapus pesan ini?" href="<?= base_url('admin/index.php?page=contacts&action=delete&csrf_token=' . urlencode(csrf_token()) . '&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/../../admin/partials/footer.php'; ?>

