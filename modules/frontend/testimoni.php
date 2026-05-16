<?php
$pageTitle = 'Testimoni - Yayasan SAPA Ayah';
$testimonials = db_all('SELECT * FROM testimonials ORDER BY is_featured DESC, created_at DESC');
include __DIR__ . '/../../templates/header.php';
?>
<section class="section">
    <div class="container">
        <?php $title = 'Suara Peserta SAPA Ayah'; $subtitle = 'Refleksi pengalaman ayah yang bertumbuh melalui komunitas ini.'; include __DIR__ . '/../../templates/components/section-head.php'; ?>
        <div class="testi-slider">
            <?php foreach ($testimonials as $item): include __DIR__ . '/../../templates/components/testimonial-card.php'; endforeach; ?>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
