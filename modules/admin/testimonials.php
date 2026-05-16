<?php
admin_guard('testimonials.view');
$pageTitle = 'Manajemen Testimoni - SAPA Admin';
$pageHeading = 'Manajemen Testimoni';

$action = clean_input($_GET['action'] ?? '');
$refId = decrypt_id($_GET['ref'] ?? '');

if ($action === 'delete' && $refId) {
    if (!can_access('testimonials.delete')) {
        flash('error', 'Anda tidak memiliki izin menghapus testimoni.');
    } else {
        db_delete('testimonials', 'id = :id', ['id' => $refId]);
        log_activity('delete', 'testimonials', $refId);
        flash('success', 'Testimoni berhasil dihapus.');
    }
    redirect('admin/index.php?page=testimonials');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = clean_input($_POST['form_action'] ?? 'create');

    try {
        $photo = handle_upload('photo', 'gallery', ['image/jpeg', 'image/png', 'image/webp'], 2097152);
    } catch (Throwable $e) {
        $photo = null;
        flash('error', $e->getMessage());
    }

    $payload = [
        'name' => clean_input($_POST['name'] ?? ''),
        'role_or_job' => clean_input($_POST['role_or_job'] ?? ''),
        'testimonial' => trim($_POST['testimonial'] ?? ''),
        'rating' => (int) ($_POST['rating'] ?? 5),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'updated_at' => now(),
    ];

    if ($photo) {
        $payload['photo'] = $photo;
    }

    if ($formAction === 'create') {
        if (!can_access('testimonials.create')) {
            flash('error', 'Anda tidak memiliki izin menambah testimoni.');
            redirect('admin/index.php?page=testimonials');
        }
        $payload['created_at'] = now();
        db_insert('testimonials', $payload);
        log_activity('create', 'testimonials');
        flash('success', 'Testimoni berhasil ditambahkan.');
    } else {
        if (!can_access('testimonials.edit')) {
            flash('error', 'Anda tidak memiliki izin mengedit testimoni.');
            redirect('admin/index.php?page=testimonials');
        }
        $id = decrypt_id($_POST['ref'] ?? '') ?: 0;
        db_update('testimonials', $payload, 'id = :id', ['id' => $id]);
        log_activity('update', 'testimonials', $id);
        flash('success', 'Testimoni berhasil diperbarui.');
    }

    redirect('admin/index.php?page=testimonials');
}

$edit = $refId ? db_one('SELECT * FROM testimonials WHERE id = :id LIMIT 1', ['id' => $refId]) : null;
$rows = db_all('SELECT * FROM testimonials ORDER BY id DESC');

include __DIR__ . '/../../admin/partials/header.php';
?>
<section class="panel">
    <h3><?= $edit ? 'Edit Testimoni' : 'Tambah Testimoni' ?></h3>
    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="form_action" value="<?= $edit ? 'update' : 'create' ?>">
        <?php if ($edit): ?><input type="hidden" name="ref" value="<?= e(encrypt_id((int)$edit['id'])) ?>"><?php endif; ?>
        <div class="inline-form">
            <input type="text" name="name" value="<?= e($edit['name'] ?? '') ?>" placeholder="Nama" required>
            <input type="text" name="role_or_job" value="<?= e($edit['role_or_job'] ?? '') ?>" placeholder="Profesi / role">
            <input type="number" min="1" max="5" name="rating" value="<?= e((string)($edit['rating'] ?? 5)) ?>" placeholder="Rating 1-5">
            <label><input type="checkbox" name="is_featured" <?= (int)($edit['is_featured'] ?? 1) === 1 ? 'checked' : '' ?>> Tampilkan di homepage</label>
            <textarea name="testimonial" placeholder="Isi testimoni" required><?= e($edit['testimonial'] ?? '') ?></textarea>
            <input type="file" name="photo">
            <button class="btn btn-primary" type="submit"><?= $edit ? 'Update Testimoni' : 'Simpan Testimoni' ?></button>
        </div>
    </form>
</section>
<section class="panel">
    <h3>Daftar Testimoni</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama</th><th>Rating</th><th>Featured</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e($row['name']) ?><br><small><?= e($row['role_or_job']) ?></small></td>
                    <td><?= e((string)$row['rating']) ?></td>
                    <td><?= (int)$row['is_featured'] === 1 ? 'Ya' : 'Tidak' ?></td>
                    <td>
                        <a class="btn btn-secondary" href="<?= base_url('admin/index.php?page=testimonials&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Edit</a>
                        <?php if (can_access('testimonials.delete')): ?>
                            <a class="btn btn-danger" data-confirm="Hapus testimoni ini?" href="<?= base_url('admin/index.php?page=testimonials&action=delete&csrf_token=' . urlencode(csrf_token()) . '&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/../../admin/partials/footer.php'; ?>

