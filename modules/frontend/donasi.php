<?php
$pageTitle = 'Donasi - Yayasan SAPA Ayah';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $proof = handle_upload('proof_file', 'qris', ['image/jpeg', 'image/png', 'application/pdf'], 2097152);
        db_insert('donations', [
            'donor_name' => clean_input($_POST['donor_name'] ?? ''),
            'donor_email' => clean_input($_POST['donor_email'] ?? ''),
            'donor_phone' => clean_input($_POST['donor_phone'] ?? ''),
            'amount' => (float) ($_POST['amount'] ?? 0),
            'transfer_date' => clean_input($_POST['transfer_date'] ?? '') ?: null,
            'bank_name' => clean_input($_POST['bank_name'] ?? ''),
            'message' => xss_clean($_POST['message'] ?? ''),
            'proof_file' => $proof,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        flash('success', 'Konfirmasi donasi berhasil dikirim. Terima kasih atas dukungan Anda.');
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
    }
    redirect('?page=donasi');
}

include __DIR__ . '/../../templates/header.php';
?>
<section class="section">
    <div class="container two-col">
        <article class="content-wrap reveal">
            <h1>Donasi untuk Program SAPA Ayah</h1>
            <p>Dukungan Anda membantu keberlangsungan edukasi pengasuhan ayah, mentoring, dan ruang pemulihan keluarga.</p>
            <h3>Rekening Yayasan</h3>
            <p><?= e(setting('bank_account', 'BCA 1234567890 a.n Yayasan SAPA Ayah')) ?></p>
            <h3>QRIS</h3>
            <?php if (setting('qris_image')): ?>
                <img src="<?= upload_url(setting('qris_image')) ?>" alt="QRIS">
            <?php else: ?>
                <p>QRIS akan ditampilkan setelah diatur melalui panel admin.</p>
            <?php endif; ?>
        </article>
        <aside class="content-wrap reveal">
            <h3>Form Konfirmasi Donasi</h3>
            <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
            <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="text" name="donor_name" required placeholder="Nama Donatur">
                <input type="email" name="donor_email" placeholder="Email">
                <input type="text" name="donor_phone" placeholder="Nomor HP">
                <input type="number" name="amount" required placeholder="Nominal Donasi">
                <input type="date" name="transfer_date">
                <input type="text" name="bank_name" placeholder="Bank Pengirim">
                <textarea name="message" placeholder="Pesan / keterangan"></textarea>
                <label>Upload bukti transfer (jpg/png/pdf, max 2MB)</label>
                <input type="file" name="proof_file" required>
                <button class="btn" type="submit">Kirim Konfirmasi</button>
            </form>
        </aside>
    </div>
</section>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
