<?php
require_once __DIR__ . '/../includes/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = clean_input($_POST['full_name'] ?? '');
    $username = clean_input($_POST['username'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirmation'] ?? '';

    if ($password !== $confirm) {
        flash('error', 'Konfirmasi password tidak cocok.');
        keep_old_input($_POST);
        redirect('auth/register.php');
    }

    if (strlen($password) < 8) {
        flash('error', 'Password minimal 8 karakter.');
        keep_old_input($_POST);
        redirect('auth/register.php');
    }

    try {
        $profilePhoto = handle_upload('profile_photo', 'profiles', ['image/jpeg', 'image/png', 'image/webp'], 2097152);
    } catch (Throwable $e) {
        $profilePhoto = null;
        flash('error', $e->getMessage());
        keep_old_input($_POST);
        redirect('auth/register.php');
    }

    try {
        register_user([
            'role_id' => 2,
            'full_name' => $fullName,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'phone' => clean_input($_POST['phone'] ?? ''),
            'gender' => clean_input($_POST['gender'] ?? '') ?: null,
            'birth_place' => clean_input($_POST['birth_place'] ?? ''),
            'birth_date' => clean_input($_POST['birth_date'] ?? '') ?: null,
            'address' => trim($_POST['address'] ?? ''),
            'bio' => trim($_POST['bio'] ?? ''),
            'status' => 'active',
            'profile_photo' => $profilePhoto,
        ]);

        flash('success', 'Registrasi berhasil. Silakan login.');
        clear_old_input();
        redirect('auth/login.php');
    } catch (Throwable $e) {
        write_log('error', 'Register failed', ['message' => $e->getMessage()]);
        flash('error', 'Registrasi gagal, username/email mungkin sudah digunakan.');
        keep_old_input($_POST);
        redirect('auth/register.php');
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - SAPA Ayah CMS</title>
    <link rel="icon" type="image/png" href="<?= asset('images/logotab.png?v=20260515-1') ?>">
    <link rel="shortcut icon" href="<?= asset('images/logotab.png?v=20260515-1') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<section class="section" style="min-height:100vh;display:grid;place-items:center;">
    <div class="content-wrap" style="width:min(760px,92%);">
        <h1>Registrasi User</h1>
        <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="form-grid">
                <input type="text" name="full_name" value="<?= old('full_name') ?>" placeholder="Nama lengkap" required>
                <input type="text" name="username" value="<?= old('username') ?>" placeholder="Username" required>
                <input type="email" name="email" value="<?= old('email') ?>" placeholder="Email" required>
                <input type="text" name="phone" value="<?= old('phone') ?>" placeholder="Nomor HP">
                <select name="gender">
                    <option value="">Jenis kelamin</option>
                    <option value="male">Laki-laki</option>
                    <option value="female">Perempuan</option>
                </select>
                <input type="text" name="birth_place" value="<?= old('birth_place') ?>" placeholder="Tempat lahir">
                <input type="date" name="birth_date" value="<?= old('birth_date') ?>">
                <input type="file" name="profile_photo">
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required>
            </div>
            <textarea name="address" placeholder="Alamat lengkap"><?= old('address') ?></textarea>
            <textarea name="bio" placeholder="Bio singkat"><?= old('bio') ?></textarea>
            <button class="btn" type="submit">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="<?= base_url('auth/login.php') ?>">Login</a></p>
    </div>
</section>
</body>
</html>
