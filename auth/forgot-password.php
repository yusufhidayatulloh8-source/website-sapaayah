<?php
require_once __DIR__ . '/../includes/init.php';

$resetLink = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean_input($_POST['email'] ?? '');
    $token = create_reset_token($email);

    if ($token) {
        $resetLink = base_url('auth/reset-password.php?token=' . urlencode($token));
        flash('success', 'Token reset berhasil dibuat. Gunakan link di bawah ini.');
    } else {
        flash('error', 'Email tidak ditemukan di sistem.');
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - SAPA Ayah CMS</title>
    <link rel="icon" type="image/png" href="<?= asset('images/logotab.png?v=20260515-1') ?>">
    <link rel="shortcut icon" href="<?= asset('images/logotab.png?v=20260515-1') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<section class="section" style="min-height:100vh;display:grid;place-items:center;">
    <div class="content-wrap" style="width:min(520px,92%);">
        <h1>Lupa Password</h1>
        <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <input type="email" name="email" required placeholder="Email akun Anda">
            <button class="btn" type="submit">Buat Token Reset</button>
        </form>
        <?php if ($resetLink): ?>
            <p><strong>Reset link:</strong><br><a href="<?= e($resetLink) ?>"><?= e($resetLink) ?></a></p>
        <?php endif; ?>
        <p><a href="<?= base_url('auth/login.php') ?>">Kembali ke login</a></p>
    </div>
</section>
</body>
</html>
