<?php
require_once __DIR__ . '/../includes/init.php';

if (is_logged_in()) {
    redirect('admin/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = clean_input($_POST['identity'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember_me']);

    if (login_attempt($identity, $password, $remember)) {
        log_activity('login', 'auth', (int) $_SESSION['auth']['id']);
        redirect('admin/index.php');
    }

    flash('error', 'Login gagal. Periksa email/username dan password Anda.');
    keep_old_input($_POST);
    redirect('auth/login.php');
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SAPA Ayah CMS</title>
    <link rel="icon" type="image/png" href="<?= asset('images/logotab.png?v=20260515-1') ?>">
    <link rel="shortcut icon" href="<?= asset('images/logotab.png?v=20260515-1') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/auth-login.css?v=20260515-1') ?>">
</head>
<body class="auth-body">
<main class="auth-shell">
    <section class="auth-brand-side">
        <div class="auth-brand-overlay"></div>
        <div class="auth-brand-content">
            <div class="auth-mark"><img src="<?= asset('images/logosapa.svg') ?>" alt="Logo SAPA Ayah"></div>
            <h1>SAPA Ayah</h1>
            <p class="auth-subtitle">Sahabat Pembelajar Ayah</p>
            <p class="auth-quote">"Setiap ayah adalah pembelajar sepanjang hayat."</p>
            <div class="auth-brand-note">
                Portal admin untuk mengelola konten, program, event, dan komunitas SAPA Ayah
            </div>
        </div>
    </section>

    <section class="auth-form-side">
        <div class="auth-card">
            <h2>Selamat Datang Kembali</h2>
            <p class="auth-desc">Masuk ke dashboard admin SAPA Ayah</p>
            <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
            <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
            <?php if (!empty($_GET['timeout'])): ?><div class="alert alert-danger">Sesi Anda berakhir, silakan login ulang.</div><?php endif; ?>
            <form method="post" class="auth-form">
                <?= csrf_field() ?>
                <label for="identity">Email</label>
                <input id="identity" type="text" name="identity" required placeholder="admin@sapaayah.org" value="<?= old('identity') ?>">

                <div class="auth-label-row">
                    <label for="password">Password</label>
                    <a href="<?= base_url('auth/forgot-password.php') ?>">Lupa password?</a>
                </div>
                <input id="password" type="password" name="password" required placeholder="********">

                <label class="auth-remember">
                    <input type="checkbox" name="remember_me">
                    <span>Ingat saya</span>
                </label>
                <button class="btn auth-submit" type="submit">Masuk ke Dashboard</button>
            </form>
            <p class="auth-help">Belum punya akses? <a href="<?= base_url('auth/register.php') ?>">Hubungi Admin</a></p>
        </div>
        <p class="auth-copy">(c) 2026 Yayasan SAPA Ayah. All rights reserved.</p>
    </section>
</main>
</body>
</html>
