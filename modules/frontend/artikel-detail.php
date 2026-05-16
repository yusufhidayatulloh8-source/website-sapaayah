<?php
$articleId = decrypt_id($_GET['ref'] ?? '');
$article = $articleId ? db_one('SELECT a.*, c.name AS category_name, u.full_name AS author_name FROM articles a LEFT JOIN categories c ON c.id = a.category_id LEFT JOIN users u ON u.id = a.author_id WHERE a.id = :id AND a.status = "published" LIMIT 1', ['id' => $articleId]) : null;
if (!$article) {
    include __DIR__ . '/../../templates/error-404.php';
    exit;
}

$comments = db_all('SELECT * FROM comments WHERE article_id = :article_id AND status = "approved" ORDER BY created_at DESC', ['article_id' => $article['id']]);
$related = db_all('SELECT id, title, excerpt FROM articles WHERE status = "published" AND id <> :id ORDER BY published_at DESC LIMIT 3', ['id' => $article['id']]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $commentText = trim($_POST['comment'] ?? '');

    if ($name && $commentText) {
        db_insert('comments', [
            'article_id' => $article['id'],
            'user_id' => is_logged_in() ? $_SESSION['auth']['id'] : null,
            'name' => $name,
            'email' => $email,
            'comment' => xss_clean($commentText),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        flash('success', 'Komentar Anda berhasil dikirim dan menunggu moderasi.');
        redirect('?page=artikel-detail&ref=' . urlencode(encrypt_id((int) $article['id'])));
    }
}

$pageTitle = $article['title'] . ' - Artikel SAPA Ayah';
include __DIR__ . '/../../templates/header.php';
?>
<section class="section">
    <div class="container two-col">
        <article class="content-wrap reveal">
            <small><?= e($article['category_name'] ?: 'Artikel') ?> • <?= format_date($article['published_at']) ?> • <?= e($article['author_name'] ?: 'Admin') ?></small>
            <h1><?= e($article['title']) ?></h1>
            <?php if ($article['thumbnail']): ?>
                <img src="<?= upload_url($article['thumbnail']) ?>" alt="<?= e($article['title']) ?>" style="border-radius:12px;margin-bottom:14px;">
            <?php endif; ?>
            <div><?= $article['content'] ?></div>
        </article>
        <aside class="content-wrap reveal">
            <h3>Related Article</h3>
            <?php foreach ($related as $item): ?>
                <article style="padding:10px 0;border-bottom:1px dashed #d9e6f3;">
                    <h4 style="margin:0 0 6px;"><?= e($item['title']) ?></h4>
                    <p><?= e(limit_words($item['excerpt'], 12)) ?></p>
                    <a class="btn btn-sm" href="<?= base_url('?page=artikel-detail&ref=' . urlencode(encrypt_id((int)$item['id']))) ?>">Baca</a>
                </article>
            <?php endforeach; ?>
        </aside>
    </div>
</section>

<section class="section">
    <div class="container two-col">
        <div class="content-wrap reveal">
            <h3>Komentar</h3>
            <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
            <?php if (!$comments): ?><p>Belum ada komentar.</p><?php endif; ?>
            <?php foreach ($comments as $comment): ?>
                <article style="border-bottom:1px solid #e5eef8;padding:10px 0;">
                    <strong><?= e($comment['name'] ?: 'Anonim') ?></strong>
                    <small>• <?= format_date($comment['created_at'], 'd M Y H:i') ?></small>
                    <p><?= e($comment['comment']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="content-wrap reveal">
            <h3>Tulis Komentar</h3>
            <form method="post">
                <?= csrf_field() ?>
                <div class="form-grid">
                    <input type="text" name="name" required placeholder="Nama">
                    <input type="email" name="email" placeholder="Email">
                </div>
                <textarea name="comment" required placeholder="Komentar Anda"></textarea>
                <button class="btn" type="submit">Kirim Komentar</button>
            </form>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
