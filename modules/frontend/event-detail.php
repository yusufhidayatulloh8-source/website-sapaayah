<?php
$eventId = decrypt_id($_GET['ref'] ?? '');
$event = $eventId ? db_one('SELECT * FROM events WHERE id = :id LIMIT 1', ['id' => $eventId]) : null;
if (!$event) {
    include __DIR__ . '/../../templates/error-404.php';
    exit;
}
$pageTitle = $event['title'] . ' - Event SAPA Ayah';
$gallery = db_all('SELECT * FROM galleries WHERE category = :category ORDER BY id DESC LIMIT 8', ['category' => $event['slug']]);
include __DIR__ . '/../../templates/header.php';
?>
<section class="section">
    <div class="container two-col">
        <article class="content-wrap reveal">
            <h1><?= e($event['title']) ?></h1>
            <p><strong>Tanggal:</strong> <?= format_date($event['event_date']) ?> <?= e($event['event_time']) ?></p>
            <p><strong>Lokasi:</strong> <?= e($event['location'] ?: '-') ?></p>
            <p><?= nl2br(e($event['description'] ?: 'Deskripsi kegiatan belum tersedia.')) ?></p>
            <?php if (!empty($event['video_url'])): ?>
                <p><a class="btn btn-sm" target="_blank" rel="noopener" href="<?= e($event['video_url']) ?>">Lihat Video Dokumentasi</a></p>
            <?php endif; ?>
        </article>
        <aside class="content-wrap reveal">
            <img src="<?= $event['thumbnail'] ? upload_url($event['thumbnail']) : asset('images/logo-sapa.jpg') ?>" alt="<?= e($event['title']) ?>" style="border-radius:12px;">
            <?php if (!empty($event['gmap_embed'])): ?>
                <div style="margin-top:12px;"><?= $event['gmap_embed'] ?></div>
            <?php endif; ?>
        </aside>
    </div>
</section>
<?php if ($gallery): ?>
<section class="section">
    <div class="container">
        <h2>Gallery Kegiatan</h2>
        <div class="gallery-grid">
            <?php foreach ($gallery as $media): ?>
                <?php if ($media['media_type'] === 'photo' && $media['file_path']): ?>
                    <a href="<?= upload_url($media['file_path']) ?>" data-lightbox>
                        <img src="<?= upload_url($media['file_path']) ?>" alt="<?= e($media['title']) ?>">
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
