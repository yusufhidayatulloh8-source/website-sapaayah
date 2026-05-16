<?php
admin_guard('articles.view');
$pageTitle = 'Manajemen Artikel - SAPA Admin';
$pageHeading = 'Manajemen Artikel';

$action = clean_input($_GET['action'] ?? '');
$refId = decrypt_id($_GET['ref'] ?? '');

if ($action === 'delete' && $refId) {
    if (!can_access('articles.delete')) {
        flash('error', 'Anda tidak memiliki izin menghapus artikel.');
    } else {
        db_delete('articles', 'id = :id', ['id' => $refId]);
        log_activity('delete', 'articles', $refId);
        flash('success', 'Artikel berhasil dihapus.');
    }
    redirect('admin/index.php?page=articles');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = clean_input($_POST['form_action'] ?? 'create');

    try {
        $thumb = handle_upload('thumbnail', 'articles', ['image/jpeg', 'image/png', 'image/webp'], 2097152);
    } catch (Throwable $e) {
        $thumb = null;
        flash('error', $e->getMessage());
    }

    $title = clean_input($_POST['title'] ?? '');
    $payload = [
        'category_id' => (int) ($_POST['category_id'] ?? 0) ?: null,
        'author_id' => (int) $_SESSION['auth']['id'],
        'title' => $title,
        'slug' => slugify($title),
        'excerpt' => trim($_POST['excerpt'] ?? ''),
        'content' => trim($_POST['content'] ?? ''),
        'tags' => clean_input($_POST['tags'] ?? ''),
        'status' => clean_input($_POST['status'] ?? 'draft'),
        'published_at' => clean_input($_POST['status'] ?? 'draft') === 'published' ? now() : null,
        'updated_at' => now(),
    ];

    if ($thumb) {
        $payload['thumbnail'] = $thumb;
    }

    if ($formAction === 'create') {
        if (!can_access('articles.create')) {
            flash('error', 'Anda tidak memiliki izin membuat artikel.');
            redirect('admin/index.php?page=articles');
        }
        $payload['created_at'] = now();
        db_insert('articles', $payload);
        log_activity('create', 'articles');
        flash('success', 'Artikel berhasil ditambahkan.');
    } else {
        if (!can_access('articles.edit')) {
            flash('error', 'Anda tidak memiliki izin mengedit artikel.');
            redirect('admin/index.php?page=articles');
        }
        $id = decrypt_id($_POST['ref'] ?? '') ?: 0;
        db_update('articles', $payload, 'id = :id', ['id' => $id]);
        log_activity('update', 'articles', $id);
        flash('success', 'Artikel berhasil diperbarui.');
    }

    redirect('admin/index.php?page=articles');
}

$edit = $refId ? db_one('SELECT * FROM articles WHERE id = :id LIMIT 1', ['id' => $refId]) : null;
$categories = db_all('SELECT * FROM categories ORDER BY name ASC');
$rows = db_all('SELECT a.*, c.name category_name FROM articles a LEFT JOIN categories c ON c.id = a.category_id ORDER BY a.id DESC');

include __DIR__ . '/../../admin/partials/header.php';
?>
<section class="panel">
    <h3><?= $edit ? 'Edit Artikel' : 'Tambah Artikel' ?></h3>
    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="form_action" value="<?= $edit ? 'update' : 'create' ?>">
        <?php if ($edit): ?><input type="hidden" name="ref" value="<?= e(encrypt_id((int)$edit['id'])) ?>"><?php endif; ?>
        <div class="inline-form">
            <input type="text" name="title" value="<?= e($edit['title'] ?? '') ?>" placeholder="Judul artikel" required>
            <select name="category_id">
                <option value="">Pilih kategori</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (int)($edit['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="tags" value="<?= e($edit['tags'] ?? '') ?>" placeholder="Tags, pisahkan koma">
            <select name="status">
                <option value="draft" <?= ($edit['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= ($edit['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
            <textarea name="excerpt" placeholder="Ringkasan artikel"><?= e($edit['excerpt'] ?? '') ?></textarea>
            <textarea name="content" placeholder="Konten artikel (HTML diperbolehkan)"><?= e($edit['content'] ?? '') ?></textarea>
            <input type="file" name="thumbnail">
            <button class="btn btn-primary" type="submit"><?= $edit ? 'Update Artikel' : 'Simpan Artikel' ?></button>
        </div>
    </form>
</section>
<section class="panel">
    <h3>Daftar Artikel</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Judul</th><th>Kategori</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e($row['title']) ?></td>
                    <td><?= e($row['category_name'] ?: '-') ?></td>
                    <td><?= e($row['status']) ?></td>
                    <td>
                        <a class="btn btn-secondary" href="<?= base_url('admin/index.php?page=articles&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Edit</a>
                        <?php if (can_access('articles.delete')): ?>
                            <a class="btn btn-danger" data-confirm="Hapus artikel ini?" href="<?= base_url('admin/index.php?page=articles&action=delete&csrf_token=' . urlencode(csrf_token()) . '&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/../../admin/partials/footer.php'; ?>

