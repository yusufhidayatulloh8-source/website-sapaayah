<?php
$programId = decrypt_id($_GET['ref'] ?? '');
$program = $programId ? db_one('SELECT * FROM programs WHERE id = :id LIMIT 1', ['id' => $programId]) : null;
if (!$program) {
    include __DIR__ . '/../../templates/error-404.php';
    exit;
}
$pageTitle = $program['title'] . ' - Program SAPA Ayah';
ensure_program_registrations_table();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = clean_input($_POST['full_name'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $city = clean_input($_POST['city'] ?? '');
    $motivation = trim($_POST['motivation'] ?? '');

    if ($fullName && $phone) {
        db_insert('program_registrations', [
            'program_id' => (int) $program['id'],
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'city' => $city,
            'motivation' => xss_clean($motivation),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        flash('success', 'Pendaftaran berhasil dikirim. Tim SAPA Ayah akan menghubungi Anda.');
        redirect('?page=program-detail&ref=' . urlencode(encrypt_id((int) $program['id'])) . '#daftar-program');
    }

    flash('error', 'Nama lengkap dan nomor HP wajib diisi.');
}

$gallery = [];
if (!empty($program['gallery_json'])) {
    $gallery = json_decode($program['gallery_json'], true) ?: [];
}

include __DIR__ . '/../../templates/header.php';
?>
<section class="section">
    <div class="container two-col">
        <article class="content-wrap reveal">
            <h1><?= e($program['title']) ?></h1>
            <p><?= nl2br(e($program['description'] ?: $program['short_description'])) ?></p>
            <h3>Jadwal Kegiatan</h3>
            <p><?= nl2br(e($program['schedule_info'] ?: 'Jadwal akan diumumkan melalui kanal resmi SAPA Ayah.')) ?></p>
            <a class="btn" href="#daftar-program">Daftar Sekarang</a>
        </article>
        <aside class="content-wrap reveal">
            <img src="<?= $program['thumbnail'] ? upload_url($program['thumbnail']) : asset('images/logo-sapa.jpg') ?>" alt="<?= e($program['title']) ?>" style="border-radius:12px;">
        </aside>
    </div>
</section>

<section class="section" id="daftar-program">
    <div class="container">
        <div class="content-wrap reveal">
            <h2>Form Daftar Program</h2>
            <p>Isi data berikut untuk mengikuti program <?= e($program['title']) ?>.</p>
            <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
            <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
            <form method="post">
                <?= csrf_field() ?>
                <div class="form-grid">
                    <input type="text" name="full_name" placeholder="Nama lengkap" required>
                    <input type="text" name="phone" placeholder="Nomor HP / WhatsApp" required>
                    <input type="email" name="email" placeholder="Email">
                    <input type="text" name="city" placeholder="Domisili">
                </div>
                <textarea name="motivation" placeholder="Ceritakan singkat alasan mengikuti program ini"></textarea>
                <button class="btn" type="submit">Kirim Pendaftaran</button>
            </form>
        </div>
    </div>
</section>

<?php if ($gallery): ?>
<section class="section">
    <div class="container">
        <h2>Galeri Program</h2>
        <div class="gallery-grid">
            <?php foreach ($gallery as $item): ?>
                <a href="<?= upload_url($item) ?>" data-lightbox>
                    <img src="<?= upload_url($item) ?>" alt="Gallery <?= e($program['title']) ?>" loading="lazy">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
