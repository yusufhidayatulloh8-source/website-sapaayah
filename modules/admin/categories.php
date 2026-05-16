<?php
admin_guard('articles.view');
$pageTitle = 'Kategori Artikel - SAPA Admin';
$pageHeading = 'Kategori Artikel';

$action = clean_input($_GET['action'] ?? '');
$refId = decrypt_id($_GET['ref'] ?? '');

if ($action === 'delete' && $refId) {
    if (!has_role('admin')) {
        flash('error', 'Hanya admin yang dapat menghapus kategori.');
    } else {
        db_delete('categories', 'id = :id', ['id' => $refId]);
        log_activity('delete', 'categories', $refId);
        flash('success', 'Kategori berhasil dihapus.');
    }
    redirect('admin/index.php?page=categories');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = clean_input($_POST['form_action'] ?? 'create');
    $name = clean_input($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {
        flash('error', 'Nama kategori wajib diisi.');
        redirect('admin/index.php?page=categories');
    }

    $payload = [
        'name' => $name,
        'slug' => slugify($name),
        'description' => $description,
        'updated_at' => now(),
    ];

    if ($formAction === 'create') {
        $payload['created_at'] = now();
        db_insert('categories', $payload);
        log_activity('create', 'categories');
        flash('success', 'Kategori berhasil ditambahkan.');
    } else {
        $id = decrypt_id($_POST['ref'] ?? '');
        db_update('categories', $payload, 'id = :id', ['id' => $id]);
        log_activity('update', 'categories', $id);
        flash('success', 'Kategori berhasil diperbarui.');
    }
    redirect('admin/index.php?page=categories');
}

$edit = $refId ? db_one('SELECT * FROM categories WHERE id = :id LIMIT 1', ['id' => $refId]) : null;
$rows = db_all('SELECT * FROM categories ORDER BY id DESC');

include __DIR__ . '/../../admin/partials/header.php';
?>
<section class="panel">
    <h3><?= $edit ? 'Edit Kategori' : 'Tambah Kategori' ?></h3>
    <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="form_action" value="<?= $edit ? 'update' : 'create' ?>">
        <?php if ($edit): ?><input type="hidden" name="ref" value="<?= e(encrypt_id((int)$edit['id'])) ?>"><?php endif; ?>
        <div class="inline-form">
            <input type="text" name="name" value="<?= e($edit['name'] ?? '') ?>" placeholder="Nama kategori" required>
            <textarea name="description" placeholder="Deskripsi"><?= e($edit['description'] ?? '') ?></textarea>
            <button class="btn btn-primary" type="submit"><?= $edit ? 'Update' : 'Simpan' ?></button>
        </div>
    </form>
</section>
<section class="panel">
    <h3>Daftar Kategori</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama</th><th>Slug</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e($row['name']) ?></td>
                    <td><?= e($row['slug']) ?></td>
                    <td>
                        <a class="btn btn-secondary" href="<?= base_url('admin/index.php?page=categories&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Edit</a>
                        <?php if (has_role('admin')): ?>
                            <a class="btn btn-danger" data-confirm="Hapus kategori ini?" href="<?= base_url('admin/index.php?page=categories&action=delete&csrf_token=' . urlencode(csrf_token()) . '&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/../../admin/partials/footer.php'; ?>

