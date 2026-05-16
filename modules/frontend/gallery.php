<?php
$pageTitle = 'Gallery - Yayasan SAPA Ayah';
$photos = db_all('SELECT * FROM galleries WHERE media_type = "photo" ORDER BY created_at DESC LIMIT 24');
$videos = db_all('SELECT * FROM galleries WHERE media_type = "video" ORDER BY created_at DESC LIMIT 8');
include __DIR__ . '/../../templates/header.php';
?>
<section class="section">
    <div class="container">
        <?php $title = 'Gallery Foto'; $subtitle = 'Dokumentasi aktivitas, kelas, dan kolaborasi SAPA Ayah.'; include __DIR__ . '/../../templates/components/section-head.php'; ?>
        <div class="gallery-grid">
            <?php foreach ($photos as $photo): ?>
                <?php if ($photo['file_path']): ?>
                    <a href="<?= upload_url($photo['file_path']) ?>" data-lightbox>
                        <img src="<?= upload_url($photo['file_path']) ?>" alt="<?= e($photo['title']) ?>" loading="lazy">
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="section">
    <div class="container">
        <?php $title = 'Gallery Video'; $subtitle = 'Rekaman insight dan kegiatan pengasuhan bersama.'; include __DIR__ . '/../../templates/components/section-head.php'; ?>
        <div class="cards-grid">
            <?php foreach ($videos as $video): ?>
                <article class="card reveal">
                    <div class="card-body">
                        <h3><?= e($video['title']) ?></h3>
                        <p><?= e($video['description'] ?: '') ?></p>
                        <?php if ($video['video_url']): ?>
                            <a class="btn btn-sm" target="_blank" rel="noopener" href="<?= e($video['video_url']) ?>">Tonton Video</a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
