<?php
$pageTitle = 'Kontak - Yayasan SAPA Ayah';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $subject = clean_input($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && $message) {
        db_insert('contacts', [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => xss_clean($message),
            'is_read' => 0,
            'created_at' => now(),
        ]);
        flash('success', 'Pesan Anda berhasil dikirim. Tim kami akan segera merespon.');
        redirect('?page=kontak');
    }

    flash('error', 'Nama dan pesan wajib diisi.');
}

include __DIR__ . '/../../templates/header.php';
?>
<section class="section">
    <div class="container two-col">
        <article class="content-wrap reveal">
            <h1>Hubungi Kami</h1>
            <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
            <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
            <form method="post">
                <?= csrf_field() ?>
                <div class="form-grid">
                    <input type="text" name="name" placeholder="Nama lengkap" required>
                    <input type="email" name="email" placeholder="Email">
                    <input type="text" name="phone" placeholder="Nomor HP">
                    <input type="text" name="subject" placeholder="Subjek">
                </div>
                <textarea name="message" required placeholder="Pesan"></textarea>
                <button class="btn" type="submit">Kirim Pesan</button>
            </form>
        </article>
        <aside class="content-wrap reveal">
            <h3>Alamat Yayasan</h3>
            <p><?= e(setting('site_address', 'Jakarta, Indonesia')) ?></p>
            <p>Email: <?= e(setting('site_email')) ?></p>
            <p>Phone: <?= e(setting('site_phone')) ?></p>
            <div style="position:relative;padding-top:70%;border-radius:10px;overflow:hidden;">
                <iframe loading="lazy" style="position:absolute;inset:0;border:0;width:100%;height:100%;" src="https://maps.google.com/maps?q=Jakarta&t=&z=11&ie=UTF8&iwloc=&output=embed"></iframe>
            </div>
        </aside>
    </div>
</section>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
