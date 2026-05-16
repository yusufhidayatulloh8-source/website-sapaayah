<?php
admin_guard('programs.view');
$pageTitle = 'Pendaftar Program - SAPA Admin';
$pageHeading = 'Pendaftar Program';

ensure_program_registrations_table();

$action = clean_input($_GET['action'] ?? '');
$refId = decrypt_id($_GET['ref'] ?? '');

if ($action === 'delete' && $refId) {
    if (!can_access('programs.delete')) {
        flash('error', 'Anda tidak memiliki izin menghapus pendaftaran.');
    } else {
        db_delete('program_registrations', 'id = :id', ['id' => $refId]);
        log_activity('delete', 'program_registrations', $refId);
        flash('success', 'Data pendaftaran berhasil dihapus.');
    }
    redirect('admin/index.php?page=program-registrations');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!can_access('programs.edit')) {
        flash('error', 'Anda tidak memiliki izin mengubah status pendaftaran.');
        redirect('admin/index.php?page=program-registrations');
    }

    $id = decrypt_id($_POST['ref'] ?? '') ?: 0;
    $status = clean_input($_POST['status'] ?? 'pending');
    $allowedStatuses = ['pending', 'contacted', 'accepted', 'cancelled'];

    if ($id && in_array($status, $allowedStatuses, true)) {
        db_update('program_registrations', ['status' => $status, 'updated_at' => now()], 'id = :id', ['id' => $id]);
        log_activity('update', 'program_registrations', $id);
        flash('success', 'Status pendaftaran berhasil diperbarui.');
    } else {
        flash('error', 'Data pendaftaran tidak valid.');
    }

    redirect('admin/index.php?page=program-registrations');
}

$rows = db_all(
    'SELECT pr.*, p.title AS program_title
     FROM program_registrations pr
     JOIN programs p ON p.id = pr.program_id
     ORDER BY pr.created_at DESC'
);

include __DIR__ . '/../../admin/partials/header.php';
?>
<section class="panel">
    <h3>Daftar Pendaftar Program</h3>
    <p class="panel-sub">Data dari form Daftar Sekarang pada halaman program website.</p>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Program</th>
                    <th>Peserta</th>
                    <th>Kontak</th>
                    <th>Motivasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$rows): ?>
                <tr><td colspan="7">Belum ada pendaftar program.</td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e(format_date($row['created_at'], 'd M Y H:i')) ?></td>
                    <td><?= e($row['program_title']) ?></td>
                    <td>
                        <strong><?= e($row['full_name']) ?></strong><br>
                        <small><?= e($row['city'] ?: '-') ?></small>
                    </td>
                    <td>
                        <?= e($row['phone']) ?><br>
                        <small><?= e($row['email'] ?: '-') ?></small>
                    </td>
                    <td><?= e(limit_words($row['motivation'] ?: '-', 18)) ?></td>
                    <td>
                        <form method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="ref" value="<?= e(encrypt_id((int) $row['id'])) ?>">
                            <select name="status">
                                <option value="pending" <?= $row['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="contacted" <?= $row['status'] === 'contacted' ? 'selected' : '' ?>>Dihubungi</option>
                                <option value="accepted" <?= $row['status'] === 'accepted' ? 'selected' : '' ?>>Diterima</option>
                                <option value="cancelled" <?= $row['status'] === 'cancelled' ? 'selected' : '' ?>>Batal</option>
                            </select>
                            <button class="btn btn-primary" type="submit">Update</button>
                        </form>
                    </td>
                    <td>
                        <?php if (can_access('programs.delete')): ?>
                            <a class="btn btn-danger" data-confirm="Hapus data pendaftaran ini?" href="<?= base_url('admin/index.php?page=program-registrations&action=delete&csrf_token=' . urlencode(csrf_token()) . '&ref=' . urlencode(encrypt_id((int) $row['id']))) ?>">Hapus</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/../../admin/partials/footer.php'; ?>
