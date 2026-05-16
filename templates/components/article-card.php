<article class="card reveal">
    <img src="<?= $article['thumbnail'] ? upload_url($article['thumbnail']) : asset('images/logo-sapa.jpg') ?>" alt="<?= e($article['title']) ?>" loading="lazy">
    <div class="card-body">
        <small><?= e($article['category_name'] ?? 'Artikel') ?> • <?= format_date($article['published_at'] ?? $article['created_at']) ?></small>
        <h3><?= e($article['title']) ?></h3>
        <p><?= e(limit_words($article['excerpt'] ?: strip_tags($article['content']), 25)) ?></p>
        <a class="btn btn-sm" href="<?= base_url('?page=artikel-detail&ref=' . urlencode(encrypt_id((int)$article['id']))) ?>">Baca Artikel</a>
    </div>
</article>
