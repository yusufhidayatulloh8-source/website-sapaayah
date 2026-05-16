<?php
$pageTitle = 'Home - Yayasan SAPA Ayah';
$featuredPrograms = db_all('SELECT * FROM programs WHERE status = "active" ORDER BY is_featured DESC, created_at DESC LIMIT 6');
$latestArticles = db_all('SELECT a.*, c.name AS category_name FROM articles a LEFT JOIN categories c ON c.id = a.category_id WHERE a.status = "published" ORDER BY a.published_at DESC LIMIT 3');
$latestEvents = db_all('SELECT * FROM events ORDER BY event_date DESC LIMIT 4');
$testimonials = db_all('SELECT * FROM testimonials WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 3');
$stats = [
    'Ayah Belajar' => 2000,
    'Event Terlaksana' => db_one('SELECT COUNT(*) total FROM events')['total'] ?? 0,
    'Program Aktif' => db_one('SELECT COUNT(*) total FROM programs WHERE status = "active"')['total'] ?? 0,
    'Kolaborator' => 38,
];

$journeyStages = [
    ['number' => '01', 'stage' => 'SAPA Start', 'title' => 'Calon Ayah', 'description' => 'Persiapan mental, spiritual, dan pengetahuan pranikah.'],
    ['number' => '02', 'stage' => 'SAPA Grow', 'title' => 'Ayah Muda', 'description' => 'Pembelajaran parenting, bonding, dan ritme keluarga baru.'],
    ['number' => '03', 'stage' => 'SAPA Lead', 'title' => 'Ayah Aktif', 'description' => 'Penguatan peran kepemimpinan dan pengasuhan berbasis nilai.'],
    ['number' => '04', 'stage' => 'SAPA Mentor', 'title' => 'Ayah Pembina', 'description' => 'Berbagi pengalaman dan membimbing ayah lain bertumbuh.'],
];

$coreValues = [
    ['title' => 'Sahabat Tumbuh', 'description' => 'Ayah bertumbuh bersama, bukan sendirian.'],
    ['title' => 'Belajar adalah Kekuatan', 'description' => 'Pembelajar sejati adalah pemimpin keluarga sejati.'],
    ['title' => 'Kehadiran Lebih dari Hadiah', 'description' => 'Waktu dan teladan adalah bentuk cinta terbaik.'],
    ['title' => 'Ayah Manusia Biasa', 'description' => 'Setiap ayah berhak pulih, belajar, dan tumbuh.'],
    ['title' => 'Maskulinitas Rahmah', 'description' => 'Tegas dalam prinsip, lembut dalam pendekatan.'],
];

include __DIR__ . '/../../templates/header.php';
?>
<section class="hero hero-home" id="home">
    <div class="container hero-content">
        <div class="hero-copy reveal">
            <span class="eyebrow">Yayasan SAPA Ayah</span>
            <h1>Ayah Hebat Bukan Ayah Sempurna, Tapi Ayah yang Terus Bertumbuh</h1>
            <p>Menjadi sahabat tumbuh bagi para ayah Indonesia. Karena setiap ayah adalah pembelajar sepanjang hayat.</p>
            <div class="hero-actions">
                <a class="btn" href="<?= base_url('?page=program') ?>">Mulai Perjalanan</a>
                <a class="btn btn-outline" href="<?= base_url('?page=kontak') ?>">Gabung Komunitas</a>
            </div>
        </div>
        <div class="hero-panel reveal">
            <div class="hero-mark">
                <img src="<?= asset('images/logo-sapa.svg') ?>" alt="SAPA Ayah">
            </div>
            <p>"Menyapa untuk Tumbuh, Tumbuh untuk Menyapa"</p>
        </div>
    </div>
    <div class="container stats-grid hero-stats">
        <?php foreach ($stats as $label => $value): ?>
            <article class="stat-item reveal">
                <h3><?= e(is_numeric($value) && (int) $value >= 1000 ? number_format((int) $value, 0, ',', '.') . '+' : (string) $value) ?></h3>
                <p><?= e($label) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section section-soft" id="about">
    <div class="container">
        <?php $title = 'Tentang SAPA Ayah'; $subtitle = 'Sahabat Pembelajar Ayah'; include __DIR__ . '/../../templates/components/section-head.php'; ?>
        <div class="feature-grid">
            <article class="feature-copy reveal">
                <p>Yayasan SAPA Ayah adalah ruang belajar, refleksi, dukungan, dan pemulihan peran ayah. Kami percaya keterlibatan ayah yang sehat adalah fondasi keluarga yang kuat.</p>
                <p>Kami hadir bukan sekadar sebagai ruang pelatihan, tetapi sebagai ekosistem belajar yang hangat, praktis, dan bertumbuh bersama.</p>
                <a class="btn btn-sm" href="<?= base_url('?page=tentang') ?>">Kenali Yayasan</a>
            </article>
            <aside class="quote-card reveal">
                <blockquote>"Setiap ayah adalah pembelajar sepanjang hayat."</blockquote>
            </aside>
        </div>
    </div>
</section>

<section class="section" id="journey">
    <div class="container">
        <?php $title = 'Perjalanan Keayahaan'; $subtitle = 'Setiap ayah memiliki tahap tumbuh yang unik. SAPA hadir untuk mendampingi dari titik mana pun.'; include __DIR__ . '/../../templates/components/section-head.php'; ?>
        <div class="journey-track">
            <?php foreach ($journeyStages as $stage): ?>
                <article class="journey-card reveal">
                    <span><?= e($stage['number']) ?></span>
                    <h3><?= e($stage['stage']) ?></h3>
                    <strong><?= e($stage['title']) ?></strong>
                    <p><?= e($stage['description']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-soft" id="programs">
    <div class="container">
        <?php $title = 'Program Unggulan'; $subtitle = 'Program pembelajaran ayah yang dirancang untuk mendampingi perjalanan keayahaan di setiap tahap kehidupan.'; include __DIR__ . '/../../templates/components/section-head.php'; ?>
        <div class="cards-grid">
            <?php foreach ($featuredPrograms as $program): include __DIR__ . '/../../templates/components/program-card.php'; endforeach; ?>
        </div>
        <div class="section-action reveal">
            <a class="btn btn-outline" href="<?= base_url('?page=program') ?>">Lihat Semua Program</a>
        </div>
    </div>
</section>

<section class="section" id="values">
    <div class="container">
        <?php $title = 'Nilai-Nilai Inti'; $subtitle = 'Prinsip yang memandu setiap langkah perjalanan ayah bersama SAPA.'; include __DIR__ . '/../../templates/components/section-head.php'; ?>
        <div class="value-grid">
            <?php foreach ($coreValues as $value): ?>
                <article class="value-card reveal">
                    <span aria-hidden="true"></span>
                    <h3><?= e($value['title']) ?></h3>
                    <p><?= e($value['description']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-soft" id="media">
    <div class="container">
        <?php $title = 'SAPA Media'; $subtitle = 'Konten inspiratif dan edukatif untuk menemani perjalanan keayahaan Anda.'; include __DIR__ . '/../../templates/components/section-head.php'; ?>
        <div class="media-grid">
            <article class="media-feature reveal">
                <div class="video-shell">
                    <iframe src="<?= e(youtube_embed_url(setting('hero_video_url'), 'https://www.youtube.com/embed/dQw4w9WgXcQ')) ?>" title="Video Profile" allowfullscreen loading="lazy"></iframe>
                </div>
                <div>
                    <span class="eyebrow">Video & Webinar</span>
                    <h3>Video Profile Yayasan</h3>
                    <p>Kenali semangat SAPA Ayah, perjalanan, dan dampak yang sudah dirasakan para ayah di berbagai daerah.</p>
                </div>
            </article>
            <div class="media-list reveal">
                <span class="eyebrow">SAPA Stories</span>
                <?php foreach ($latestArticles as $article): ?>
                    <article>
                        <small><?= e($article['category_name'] ?: 'Artikel') ?> / <?= format_date($article['published_at']) ?></small>
                        <h4><?= e($article['title']) ?></h4>
                        <a href="<?= base_url('?page=artikel-detail&ref=' . urlencode(encrypt_id((int)$article['id']))) ?>">Baca Selengkapnya</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="section" id="events">
    <div class="container">
        <?php $title = 'Event & Kegiatan'; $subtitle = 'Bergabung dengan kegiatan yang mempertemukan para ayah pembelajar.'; include __DIR__ . '/../../templates/components/section-head.php'; ?>
        <div class="event-list">
            <?php foreach ($latestEvents as $event): include __DIR__ . '/../../templates/components/event-card.php'; endforeach; ?>
        </div>
        <div class="section-action reveal">
            <a class="btn btn-outline" href="<?= base_url('?page=event') ?>">Lihat Semua Event</a>
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container">
        <?php $title = 'Testimoni Peserta'; $subtitle = 'Cerita nyata dari ayah yang bertumbuh bersama SAPA Ayah.'; include __DIR__ . '/../../templates/components/section-head.php'; ?>
        <div class="testi-slider">
            <?php foreach ($testimonials as $item): include __DIR__ . '/../../templates/components/testimonial-card.php'; endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php $title = 'Partner & Collaboration'; $subtitle = 'Bersama lembaga, komunitas, sekolah, dan mitra sosial.'; include __DIR__ . '/../../templates/components/section-head.php'; ?>
        <div class="partner-list reveal">
            <div>Mitra Pendidikan</div>
            <div>Komunitas Ayah</div>
            <div>Lembaga Sosial</div>
            <div>Psikolog Partner</div>
            <div>Media Kolaborator</div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container cta-band reveal">
        <div>
            <h2>Karena Anak Tidak Membutuhkan Ayah Sempurna</h2>
            <p>Mereka membutuhkan ayah yang hadir. Perjalanan dimulai dari keputusan untuk belajar dan bertumbuh.</p>
        </div>
        <div class="cta-actions">
            <a class="btn btn-light" href="<?= base_url('?page=kontak') ?>">Bergabung</a>
            <a class="btn btn-ghost-light" href="<?= base_url('?page=donasi') ?>">Donasi</a>
            <a class="btn btn-ghost-light" href="<?= base_url('?page=kontak') ?>">Kolaborasi</a>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
