<?php
$pageTitle = 'Event & Kegiatan - Yayasan SAPA Ayah';
$latest = db_all('SELECT * FROM events WHERE status = "completed" ORDER BY event_date DESC LIMIT 6');
$upcoming = db_all('SELECT * FROM events WHERE status = "upcoming" ORDER BY event_date ASC LIMIT 6');
include __DIR__ . '/../../templates/header.php';
?>
<section class="section">
    <div class="container">
        <?php $title = 'Event Mendatang'; $subtitle = 'Agenda pembelajaran dan aktivitas kolaboratif SAPA Ayah.'; include __DIR__ . '/../../templates/components/section-head.php'; ?>
        <div class="cards-grid">
            <?php foreach ($upcoming as $event): include __DIR__ . '/../../templates/components/event-card.php'; endforeach; ?>
        </div>
    </div>
</section>
<section class="section">
    <div class="container">
        <?php $title = 'Dokumentasi Event Terbaru'; $subtitle = 'Kegiatan yang sudah terlaksana bersama para ayah dan keluarga.'; include __DIR__ . '/../../templates/components/section-head.php'; ?>
        <div class="cards-grid">
            <?php foreach ($latest as $event): include __DIR__ . '/../../templates/components/event-card.php'; endforeach; ?>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
