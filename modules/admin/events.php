<?php
admin_guard('events.view');
$pageTitle = 'Manajemen Event - SAPA Admin';
$pageHeading = 'Manajemen Event';

$action = clean_input($_GET['action'] ?? '');
$refId = decrypt_id($_GET['ref'] ?? '');

if ($action === 'delete' && $refId) {
    if (!can_access('events.delete')) {
        flash('error', 'Anda tidak memiliki izin menghapus event.');
    } else {
        db_delete('events', 'id = :id', ['id' => $refId]);
        log_activity('delete', 'events', $refId);
        flash('success', 'Event berhasil dihapus.');
    }
    redirect('admin/index.php?page=events');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = clean_input($_POST['form_action'] ?? 'create');

    try {
        $thumb = handle_upload('thumbnail', 'events', ['image/jpeg', 'image/png', 'image/webp'], 2097152);
    } catch (Throwable $e) {
        $thumb = null;
        flash('error', $e->getMessage());
    }

    $title = clean_input($_POST['title'] ?? '');
    $payload = [
        'title' => $title,
        'slug' => slugify($title),
        'description' => trim($_POST['description'] ?? ''),
        'event_date' => clean_input($_POST['event_date'] ?? date('Y-m-d')),
        'event_time' => clean_input($_POST['event_time'] ?? ''),
        'location' => clean_input($_POST['location'] ?? ''),
        'gmap_embed' => trim($_POST['gmap_embed'] ?? ''),
        'video_url' => clean_input($_POST['video_url'] ?? ''),
        'status' => clean_input($_POST['status'] ?? 'upcoming'),
        'updated_at' => now(),
    ];
    if ($thumb) {
        $payload['thumbnail'] = $thumb;
    }

    if ($formAction === 'create') {
        if (!can_access('events.create')) {
            flash('error', 'Anda tidak memiliki izin membuat event.');
            redirect('admin/index.php?page=events');
        }
        $payload['created_at'] = now();
        db_insert('events', $payload);
        log_activity('create', 'events');
        flash('success', 'Event berhasil ditambahkan.');
    } else {
        if (!can_access('events.edit')) {
            flash('error', 'Anda tidak memiliki izin mengedit event.');
            redirect('admin/index.php?page=events');
        }
        $id = decrypt_id($_POST['ref'] ?? '') ?: 0;
        db_update('events', $payload, 'id = :id', ['id' => $id]);
        log_activity('update', 'events', $id);
        flash('success', 'Event berhasil diperbarui.');
    }

    redirect('admin/index.php?page=events');
}

$edit = $refId ? db_one('SELECT * FROM events WHERE id = :id LIMIT 1', ['id' => $refId]) : null;
$rows = db_all('SELECT * FROM events ORDER BY event_date DESC, id DESC');

include __DIR__ . '/../../admin/partials/header.php';
?>
<section class="panel">
    <h3><?= $edit ? 'Edit Event' : 'Tambah Event' ?></h3>
    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="form_action" value="<?= $edit ? 'update' : 'create' ?>">
        <?php if ($edit): ?><input type="hidden" name="ref" value="<?= e(encrypt_id((int)$edit['id'])) ?>"><?php endif; ?>
        <div class="inline-form">
            <input type="text" name="title" value="<?= e($edit['title'] ?? '') ?>" placeholder="Judul event" required>
            <input type="date" name="event_date" value="<?= e($edit['event_date'] ?? date('Y-m-d')) ?>" required>
            <input type="text" name="event_time" value="<?= e($edit['event_time'] ?? '') ?>" placeholder="Jam kegiatan">
            <input type="text" name="location" value="<?= e($edit['location'] ?? '') ?>" placeholder="Lokasi">
            <input type="text" name="video_url" value="<?= e($edit['video_url'] ?? '') ?>" placeholder="URL video dokumentasi">
            <select name="status">
                <option value="upcoming" <?= ($edit['status'] ?? '') === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                <option value="completed" <?= ($edit['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="cancelled" <?= ($edit['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
            <textarea name="description" placeholder="Deskripsi event"><?= e($edit['description'] ?? '') ?></textarea>
            <textarea name="gmap_embed" placeholder="Google Maps iframe (opsional)"><?= e($edit['gmap_embed'] ?? '') ?></textarea>
            <input type="file" name="thumbnail">
            <button class="btn btn-primary" type="submit"><?= $edit ? 'Update Event' : 'Simpan Event' ?></button>
        </div>
    </form>
</section>
<section class="panel">
    <h3>Daftar Event</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Judul</th><th>Tanggal</th><th>Status</th><th>Lokasi</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e($row['title']) ?></td>
                    <td><?= format_date($row['event_date']) ?> <?= e($row['event_time']) ?></td>
                    <td><?= e($row['status']) ?></td>
                    <td><?= e($row['location']) ?></td>
                    <td>
                        <a class="btn btn-secondary" href="<?= base_url('admin/index.php?page=events&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Edit</a>
                        <?php if (can_access('events.delete')): ?>
                            <a class="btn btn-danger" data-confirm="Hapus event ini?" href="<?= base_url('admin/index.php?page=events&action=delete&csrf_token=' . urlencode(csrf_token()) . '&ref=' . urlencode(encrypt_id((int)$row['id']))) ?>">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/../../admin/partials/footer.php'; ?>

