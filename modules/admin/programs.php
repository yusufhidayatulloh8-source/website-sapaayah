<?php
admin_guard('programs.view');
$pageTitle = 'Manajemen Program - SAPA Admin';
$pageHeading = 'Manajemen Program';

$action = clean_input($_GET['action'] ?? '');
$refId = decrypt_id($_GET['ref'] ?? '');

if ($action === 'delete' && $refId) {
    if (!can_access('programs.delete')) {
        flash('error', 'Anda tidak memiliki izin menghapus program.');
    } else {
        db_delete('programs', 'id = :id', ['id' => $refId]);
        log_activity('delete', 'programs', $refId);
        flash('success', 'Program berhasil dihapus.');
    }
    redirect('admin/index.php?page=programs');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = clean_input($_POST['form_action'] ?? 'create');

    try {
        $thumb = handle_upload('thumbnail', 'programs', ['image/jpeg', 'image/png', 'image/webp'], 2097152);
    } catch (Throwable $e) {
        $thumb = null;
        flash('error', $e->getMessage());
    }

    $title = clean_input($_POST['title'] ?? '');
    $galleryItems = array_filter(array_map('trim', explode(PHP_EOL, $_POST['gallery_paths'] ?? '')));

    $payload = [
        'title' => $title,
        'slug' => slugify($title),
        'short_description' => trim($_POST['short_description'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'schedule_info' => trim($_POST['schedule_info'] ?? ''),
        'gallery_json' => json_encode(array_values($galleryItems), JSON_UNESCAPED_UNICODE),
        'registration_link' => clean_input($_POST['registration_link'] ?? ''),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'status' => clean_input($_POST['status'] ?? 'active'),
        'updated_at' => now(),
    ];

    if ($thumb) {
        $payload['thumbnail'] = $thumb;
    }

    if ($formAction === 'create') {
        if (!can_access('programs.create')) {
            flash('error', 'Anda tidak memiliki izin menambah program.');
            redirect('admin/index.php?page=programs');
        }
        $payload['created_at'] = now();
        db_insert('programs', $payload);
        log_activity('create', 'programs');
        flash('success', 'Program berhasil ditambahkan.');
    } else {
        if (!can_access('programs.edit')) {
            flash('error', 'Anda tidak memiliki izin mengedit program.');
            redirect('admin/index.php?page=programs');
        }
        $id = decrypt_id($_POST['ref'] ?? '') ?: 0;
        db_update('programs', $payload, 'id = :id', ['id' => $id]);
        log_activity('update', 'programs', $id);
        flash('success', 'Program berhasil diperbarui.');
    }

    redirect('admin/index.php?page=programs');
}

$edit = $refId ? db_one('SELECT * FROM programs WHERE id = :id LIMIT 1', ['id' => $refId]) : null;
$rows = db_all('SELECT * FROM programs ORDER BY is_featured DESC, title ASC');

$galleryText = '';
if (!empty($edit['gallery_json'])) {
    $items = json_decode($edit['gallery_json'], true) ?: [];
    $galleryText = implode(PHP_EOL, $items);
}

include __DIR__ . '/../../admin/partials/header.php';
?>
<section class="panel">
    <h3><?= $edit ? 'Edit Program' : 'Tambah Program' ?></h3>
    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="form_action" value="<?= $edit ? 'update' : 'create' ?>">
        <?php if ($edit): ?><input type="hidden" name="ref" value="<?= e(encrypt_id((int)$edit['id'])) ?>"><?php endif; ?>
        <div class="inline-form">
            <input type="text" name="title" value="<?= e($edit['title'] ?? '') ?>" placeholder="Nama program" required>
            <input type="text" name="registration_link" value="<?= e($edit['registration_link'] ?? '') ?>" placeholder="Link pendaftaran eksternal (opsional)">
            <textarea name="short_description" placeholder="Deskripsi singkat"><?= e($edit['short_description'] ?? '') ?></textarea>
            <textarea name="description" placeholder="Deskripsi lengkap"><?= e($edit['description'] ?? '') ?></textarea>
            <textarea name="schedule_info" placeholder="Jadwal kegiatan"><?= e($edit['schedule_info'] ?? '') ?></textarea>
            <textarea name="gallery_paths" placeholder="Daftar path gallery (satu per baris)"><?= e($galleryText) ?></textarea>
            <select name="status">
                <option value="active" <?= ($edit['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($edit['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
            <label><input type="checkbox" name="is_featured" <?= (int)($edit['is_featured'] ?? 0) === 1 ? 'checked' : '' ?>> Jadikan unggulan</label>
            <input type="file" name="thumbnail">
            <button class="btn btn-primary" type="submit"><?= $edit ? 'Update Program' : 'Simpan Program' ?></button>
        </div>
    </form>
</section>
<section class="panel">
    <h3>Daftar Program</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Program</th><th>Status</th><th>Unggulan</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e($row['title']) ?></td>
                    <td><?= e($row['status']) ?></td>
                    <td><?= (int)$row['is_featured'] === 1 ? 'Ya' : 'Tidak' ?></td>
                    <td>
                        <a class="btn btn-secondary" href="<?= base_url('admin/index.php?page=programs&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Edit</a>
                        <?php if (can_access('programs.delete')): ?>
                            <a class="btn btn-danger" data-confirm="Hapus program ini?" href="<?= base_url('admin/index.php?page=programs&action=delete&csrf_token=' . urlencode(csrf_token()) . '&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/../../admin/partials/footer.php'; ?>

