<article class="card reveal">
    <img src="<?= $program['thumbnail'] ? upload_url($program['thumbnail']) : asset('images/logo-sapa.jpg') ?>" alt="<?= e($program['title']) ?>" loading="lazy">
    <div class="card-body">
        <h3><?= e($program['title']) ?></h3>
        <p><?= e(limit_words($program['short_description'] ?? '', 20)) ?></p>
        <a class="btn btn-sm" href="<?= base_url('?page=program-detail&ref=' . urlencode(encrypt_id((int)$program['id']))) ?>">Lihat Detail</a>
        <a class="btn btn-sm btn-outline" href="<?= base_url('?page=program-detail&ref=' . urlencode(encrypt_id((int)$program['id'])) . '#daftar-program') ?>">Daftar Sekarang</a>
    </div>
</article>
