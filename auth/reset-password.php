<?php
require_once __DIR__ . '/../includes/init.php';

$token = clean_input($_GET['token'] ?? '');
if ($token === '') {
    flash('error', 'Token reset tidak valid.');
    redirect('auth/forgot-password.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirmation'] ?? '';

    if (strlen($password) < 8) {
        flash('error', 'Password minimal 8 karakter.');
    } elseif ($password !== $confirm) {
        flash('error', 'Konfirmasi password tidak cocok.');
    } elseif (reset_password($token, $password)) {
        flash('success', 'Password berhasil direset. Silakan login.');
        redirect('auth/login.php');
    } else {
        flash('error', 'Token reset tidak valid atau sudah kedaluwarsa.');
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - SAPA Ayah CMS</title>
    <link rel="icon" type="image/svg+xml" href="<?= asset('images/logosapa.svg') ?>">
    <link rel="shortcut icon" href="<?= asset('images/logosapa.svg') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<section class="section" style="min-height:100vh;display:grid;place-items:center;">
    <div class="content-wrap" style="width:min(520px,92%);">
        <h1>Reset Password</h1>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <input type="password" name="password" required placeholder="Password baru">
            <input type="password" name="password_confirmation" required placeholder="Konfirmasi password baru">
            <button class="btn" type="submit">Update Password</button>
        </form>
    </div>
</section>
</body>
</html>
