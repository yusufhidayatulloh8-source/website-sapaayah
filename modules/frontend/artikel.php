<?php
$pageTitle = 'Artikel - Yayasan SAPA Ayah';
$search = clean_input($_GET['q'] ?? '');
$params = [];
$sql = 'SELECT a.*, c.name AS category_name FROM articles a LEFT JOIN categories c ON c.id = a.category_id WHERE a.status = "published"';
if ($search !== '') {
    $sql .= ' AND (a.title LIKE :search_title OR a.content LIKE :search_content OR c.name LIKE :search_category)';
    $params['search_title'] = '%' . $search . '%';
    $params['search_content'] = '%' . $search . '%';
    $params['search_category'] = '%' . $search . '%';
}
$sql .= ' ORDER BY a.published_at DESC';
$articles = db_all($sql, $params);
$categories = db_all('SELECT * FROM categories ORDER BY name ASC');
include __DIR__ . '/../../templates/header.php';
?>
<section class="section">
    <div class="container">
        <?php $title = 'Artikel & Blog'; $subtitle = 'Wawasan pengasuhan ayah, keluarga, dan kesehatan mental.'; include __DIR__ . '/../../templates/components/section-head.php'; ?>
        <form class="content-wrap" method="get" action="<?= base_url() ?>" style="margin-bottom:16px;">
            <input type="hidden" name="page" value="artikel">
            <div class="form-grid">
                <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari artikel...">
                <button class="btn" type="submit">Cari</button>
            </div>
            <p style="margin-top:12px;">Kategori:
                <?php foreach ($categories as $category): ?>
                    <span class="badge" style="margin:4px;"><?= e($category['name']) ?></span>
                <?php endforeach; ?>
            </p>
        </form>
        <div class="cards-grid">
            <?php foreach ($articles as $article): include __DIR__ . '/../../templates/components/article-card.php'; endforeach; ?>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
