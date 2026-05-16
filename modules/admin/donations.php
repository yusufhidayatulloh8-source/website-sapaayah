<?php
admin_guard('donations.view');
$pageTitle = 'Manajemen Donasi - SAPA Admin';
$pageHeading = 'Konfirmasi Donasi';

$action = clean_input($_GET['action'] ?? '');
$refId = decrypt_id($_GET['ref'] ?? '');

if ($action === 'verify' && $refId) {
    if (!can_access('donations.edit')) {
        flash('error', 'Anda tidak memiliki izin verifikasi donasi.');
    } else {
        db_update('donations', ['status' => 'verified', 'updated_at' => now()], 'id = :id', ['id' => $refId]);
        log_activity('verify', 'donations', $refId);
        flash('success', 'Donasi berhasil diverifikasi.');
    }
    redirect('admin/index.php?page=donations');
}

if ($action === 'reject' && $refId) {
    if (!can_access('donations.edit')) {
        flash('error', 'Anda tidak memiliki izin menolak donasi.');
    } else {
        db_update('donations', ['status' => 'rejected', 'updated_at' => now()], 'id = :id', ['id' => $refId]);
        log_activity('reject', 'donations', $refId);
        flash('success', 'Status donasi diubah menjadi rejected.');
    }
    redirect('admin/index.php?page=donations');
}

$rows = db_all('SELECT * FROM donations ORDER BY id DESC');

include __DIR__ . '/../../admin/partials/header.php';
?>
<section class="panel">
    <h3>Daftar Donasi</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Donatur</th><th>Nominal</th><th>Transfer</th><th>Status</th><th>Bukti</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e($row['donor_name']) ?><br><small><?= e($row['donor_email']) ?> <?= e($row['donor_phone']) ?></small></td>
                    <td>Rp <?= number_format((float)$row['amount'], 0, ',', '.') ?></td>
                    <td><?= format_date($row['transfer_date']) ?><br><small><?= e($row['bank_name']) ?></small></td>
                    <td><?= e($row['status']) ?></td>
                    <td>
                        <?php if ($row['proof_file']): ?>
                            <a class="btn btn-secondary" target="_blank" rel="noopener" href="<?= upload_url($row['proof_file']) ?>">Lihat Bukti</a>
                        <?php else: ?>-
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (can_access('donations.edit')): ?>
                            <a class="btn btn-success" href="<?= base_url('admin/index.php?page=donations&action=verify&csrf_token=' . urlencode(csrf_token()) . '&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Verify</a>
                            <a class="btn btn-danger" data-confirm="Tolak donasi ini?" href="<?= base_url('admin/index.php?page=donations&action=reject&csrf_token=' . urlencode(csrf_token()) . '&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Reject</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/../../admin/partials/footer.php'; ?>

