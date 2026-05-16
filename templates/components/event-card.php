<article class="card reveal">
    <img src="<?= $event['thumbnail'] ? upload_url($event['thumbnail']) : asset('images/logo-sapa.jpg') ?>" alt="<?= e($event['title']) ?>" loading="lazy">
    <div class="card-body">
        <small><?= format_date($event['event_date']) ?> • <?= e($event['location'] ?: 'Lokasi menyusul') ?></small>
        <h3><?= e($event['title']) ?></h3>
        <p><?= e(limit_words($event['description'] ?? '', 20)) ?></p>
        <a class="btn btn-sm" href="<?= base_url('?page=event-detail&ref=' . urlencode(encrypt_id((int)$event['id']))) ?>">Detail Event</a>
    </div>
</article>
