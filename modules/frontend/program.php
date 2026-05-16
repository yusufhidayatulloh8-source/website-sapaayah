<?php
$pageTitle = 'Program - Yayasan SAPA Ayah';
$programs = db_all('SELECT * FROM programs WHERE status = "active" ORDER BY is_featured DESC, title ASC');
include __DIR__ . '/../../templates/header.php';
?>
<section class="section">
    <div class="container">
        <?php $title = 'Program SAPA Ayah'; $subtitle = 'Dua belas program pembelajaran ayah untuk mendampingi setiap tahap perjalanan keluarga.'; include __DIR__ . '/../../templates/components/section-head.php'; ?>
        <div class="cards-grid">
            <?php foreach ($programs as $program): include __DIR__ . '/../../templates/components/program-card.php'; endforeach; ?>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
