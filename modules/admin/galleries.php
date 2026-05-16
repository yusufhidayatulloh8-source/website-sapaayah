<?php
admin_guard('galleries.view');
$pageTitle = 'Manajemen Gallery - SAPA Admin';
$pageHeading = 'Manajemen Gallery';

$action = clean_input($_GET['action'] ?? '');
$refId = decrypt_id($_GET['ref'] ?? '');

if ($action === 'delete' && $refId) {
    if (!can_access('galleries.delete')) {
        flash('error', 'Anda tidak memiliki izin menghapus gallery.');
    } else {
        db_delete('galleries', 'id = :id', ['id' => $refId]);
        log_activity('delete', 'galleries', $refId);
        flash('success', 'Data gallery berhasil dihapus.');
    }
    redirect('admin/index.php?page=galleries');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = clean_input($_POST['form_action'] ?? 'create');
    $mediaType = clean_input($_POST['media_type'] ?? 'photo');

    $payload = [
        'title' => clean_input($_POST['title'] ?? ''),
        'media_type' => $mediaType,
        'video_url' => clean_input($_POST['video_url'] ?? ''),
        'category' => clean_input($_POST['category'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'updated_at' => now(),
    ];

    if ($mediaType === 'photo') {
        try {
            $file = handle_upload('file_path', 'gallery', ['image/jpeg', 'image/png', 'image/webp'], 3145728);
            if ($file) {
                $payload['file_path'] = $file;
            }
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
    }

    if ($formAction === 'create') {
        if (!can_access('galleries.create')) {
            flash('error', 'Anda tidak memiliki izin menambah gallery.');
            redirect('admin/index.php?page=galleries');
        }
        $payload['created_at'] = now();
        db_insert('galleries', $payload);
        log_activity('create', 'galleries');
        flash('success', 'Gallery berhasil ditambahkan.');
    } else {
        if (!can_access('galleries.edit')) {
            flash('error', 'Anda tidak memiliki izin mengedit gallery.');
            redirect('admin/index.php?page=galleries');
        }
        $id = decrypt_id($_POST['ref'] ?? '') ?: 0;
        db_update('galleries', $payload, 'id = :id', ['id' => $id]);
        log_activity('update', 'galleries', $id);
        flash('success', 'Gallery berhasil diperbarui.');
    }

    redirect('admin/index.php?page=galleries');
}

$edit = $refId ? db_one('SELECT * FROM galleries WHERE id = :id LIMIT 1', ['id' => $refId]) : null;
$rows = db_all('SELECT * FROM galleries ORDER BY id DESC');

include __DIR__ . '/../../admin/partials/header.php';
?>
<section class="panel">
    <h3><?= $edit ? 'Edit Gallery' : 'Tambah Gallery' ?></h3>
    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="form_action" value="<?= $edit ? 'update' : 'create' ?>">
        <?php if ($edit): ?><input type="hidden" name="ref" value="<?= e(encrypt_id((int)$edit['id'])) ?>"><?php endif; ?>
        <div class="inline-form">
            <input type="text" name="title" value="<?= e($edit['title'] ?? '') ?>" placeholder="Judul media" required>
            <select name="media_type">
                <option value="photo" <?= ($edit['media_type'] ?? '') === 'photo' ? 'selected' : '' ?>>Photo</option>
                <option value="video" <?= ($edit['media_type'] ?? '') === 'video' ? 'selected' : '' ?>>Video</option>
            </select>
            <input type="text" name="category" value="<?= e($edit['category'] ?? '') ?>" placeholder="Kategori / slug event">
            <input type="text" name="video_url" value="<?= e($edit['video_url'] ?? '') ?>" placeholder="URL Video (jika media video)">
            <input type="file" name="file_path">
            <textarea name="description" placeholder="Deskripsi"><?= e($edit['description'] ?? '') ?></textarea>
            <button class="btn btn-primary" type="submit"><?= $edit ? 'Update Gallery' : 'Simpan Gallery' ?></button>
        </div>
    </form>
</section>
<section class="panel">
    <h3>Daftar Gallery</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Judul</th><th>Tipe</th><th>Kategori</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e($row['title']) ?></td>
                    <td><?= e($row['media_type']) ?></td>
                    <td><?= e($row['category']) ?></td>
                    <td>
                        <a class="btn btn-secondary" href="<?= base_url('admin/index.php?page=galleries&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Edit</a>
                        <?php if (can_access('galleries.delete')): ?>
                            <a class="btn btn-danger" data-confirm="Hapus media ini?" href="<?= base_url('admin/index.php?page=galleries&action=delete&csrf_token=' . urlencode(csrf_token()) . '&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/../../admin/partials/footer.php'; ?>

