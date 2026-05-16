<?php
admin_guard();
$pageTitle = 'Profile Saya - SAPA Admin';
$pageHeading = 'Profile Saya';

$user = auth_user();
if (!$user) {
    redirect('auth/login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = clean_input($_POST['action'] ?? 'profile');

    if ($action === 'profile') {
        try {
            $photo = handle_upload('profile_photo', 'profiles', ['image/jpeg', 'image/png', 'image/webp'], 2097152);
        } catch (Throwable $e) {
            $photo = null;
            flash('error', $e->getMessage());
        }

        $update = [
            'full_name' => clean_input($_POST['full_name'] ?? $user['full_name']),
            'phone' => clean_input($_POST['phone'] ?? ''),
            'gender' => clean_input($_POST['gender'] ?? '') ?: null,
            'birth_place' => clean_input($_POST['birth_place'] ?? ''),
            'birth_date' => clean_input($_POST['birth_date'] ?? '') ?: null,
            'address' => trim($_POST['address'] ?? ''),
            'bio' => trim($_POST['bio'] ?? ''),
            'updated_at' => now(),
        ];

        if ($photo) {
            $update['profile_photo'] = $photo;
        }

        db_update('users', $update, 'id = :id', ['id' => $user['id']]);
        log_activity('update_profile', 'users', (int) $user['id']);
        flash('success', 'Profile berhasil diperbarui.');
        redirect('admin/index.php?page=profile');
    }

    if ($action === 'password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!password_verify($currentPassword, $user['password'])) {
            flash('error', 'Password saat ini tidak sesuai.');
        } elseif (strlen($newPassword) < 8) {
            flash('error', 'Password baru minimal 8 karakter.');
        } elseif ($newPassword !== $confirmPassword) {
            flash('error', 'Konfirmasi password tidak cocok.');
        } else {
            db_update('users', [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                'updated_at' => now(),
            ], 'id = :id', ['id' => $user['id']]);
            log_activity('change_password', 'users', (int) $user['id']);
            flash('success', 'Password berhasil diubah.');
        }

        redirect('admin/index.php?page=profile');
    }
}

$user = auth_user();
include __DIR__ . '/../../admin/partials/header.php';
?>
<section class="panel">
    <h3>Edit Profile</h3>
    <form method="post" enctype="multipart/form-data" class="inline-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="profile">
        <input type="text" name="full_name" value="<?= e($user['full_name']) ?>" required placeholder="Nama lengkap">
        <input type="text" name="phone" value="<?= e($user['phone'] ?: '') ?>" placeholder="Nomor HP">
        <select name="gender">
            <option value="">Pilih jenis kelamin</option>
            <option value="male" <?= $user['gender'] === 'male' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="female" <?= $user['gender'] === 'female' ? 'selected' : '' ?>>Perempuan</option>
        </select>
        <input type="text" name="birth_place" value="<?= e($user['birth_place'] ?: '') ?>" placeholder="Tempat lahir">
        <input type="date" name="birth_date" value="<?= e($user['birth_date'] ?: '') ?>">
        <input type="file" name="profile_photo">
        <textarea name="address" placeholder="Alamat lengkap"><?= e($user['address'] ?: '') ?></textarea>
        <textarea name="bio" placeholder="Bio singkat"><?= e($user['bio'] ?: '') ?></textarea>
        <button class="btn btn-primary" type="submit">Simpan Profile</button>
    </form>
</section>

<section class="panel">
    <h3>Ganti Password</h3>
    <form method="post" class="inline-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="password">
        <input type="password" name="current_password" placeholder="Password saat ini" required>
        <input type="password" name="new_password" placeholder="Password baru" required>
        <input type="password" name="confirm_password" placeholder="Konfirmasi password" required>
        <button class="btn btn-success" type="submit">Update Password</button>
    </form>
</section>
<?php include __DIR__ . '/../../admin/partials/footer.php'; ?>

