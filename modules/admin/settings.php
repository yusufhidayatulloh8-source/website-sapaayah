<?php
admin_guard('settings.manage');
$pageTitle = 'Pengaturan Website - SAPA Admin';
$pageHeading = 'Pengaturan Website';

$keys = [
    'site_name', 'site_tagline', 'site_email', 'site_phone', 'site_address',
    'hero_video_url', 'bank_account', 'whatsapp_number', 'instagram_url',
    'facebook_url', 'youtube_url'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($keys as $key) {
        $value = trim($_POST[$key] ?? '');
        if ($key === 'hero_video_url') {
            $value = youtube_embed_url($value);
        }
        $row = db_one('SELECT id FROM settings WHERE setting_key = :key LIMIT 1', ['key' => $key]);
        if ($row) {
            db_update('settings', ['setting_value' => $value, 'updated_at' => now()], 'id = :id', ['id' => $row['id']]);
        } else {
            db_insert('settings', ['setting_key' => $key, 'setting_value' => $value, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    try {
        $qris = handle_upload('qris_image_file', 'qris', ['image/jpeg', 'image/png', 'image/webp'], 2097152);
        if ($qris) {
            $row = db_one('SELECT id FROM settings WHERE setting_key = "qris_image" LIMIT 1');
            if ($row) {
                db_update('settings', ['setting_value' => $qris, 'updated_at' => now()], 'id = :id', ['id' => $row['id']]);
            } else {
                db_insert('settings', ['setting_key' => 'qris_image', 'setting_value' => $qris, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
    }

    log_activity('update', 'settings');
    flash('success', 'Pengaturan website berhasil diperbarui.');
    redirect('admin/index.php?page=settings');
}

$data = [];
foreach ($keys as $key) {
    $data[$key] = setting($key);
}
$qris = setting('qris_image');

include __DIR__ . '/../../admin/partials/header.php';
?>
<section class="panel">
    <h3>General Setting</h3>
    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="inline-form">
            <?php foreach ($keys as $key): ?>
                <div>
                    <label><?= e($key) ?></label>
                    <input type="text" name="<?= e($key) ?>" value="<?= e($data[$key] ?? '') ?>"<?= $key === 'hero_video_url' ? ' placeholder="https://www.youtube.com/watch?v=..."' : '' ?>>
                </div>
            <?php endforeach; ?>
            <div>
                <label>QRIS Image</label>
                <input type="file" name="qris_image_file">
                <?php if ($qris): ?><small>Current: <?= e($qris) ?></small><?php endif; ?>
            </div>
            <button class="btn btn-primary" type="submit">Simpan Pengaturan</button>
        </div>
    </form>
</section>
<?php include __DIR__ . '/../../admin/partials/footer.php'; ?>

