<?php
admin_guard('users.view');
$pageTitle = 'Manajemen User - SAPA Admin';
$pageHeading = 'Manajemen User';

$roles = db_all('SELECT * FROM roles ORDER BY id ASC');
$editId = decrypt_id($_GET['ref'] ?? '');
$action = clean_input($_GET['action'] ?? '');

if ($action === 'delete' && $editId) {
    if (!has_role('admin')) {
        flash('error', 'Hanya admin yang dapat menghapus user.');
        redirect('admin/index.php?page=users');
    }

    if ((int) $editId === (int) $_SESSION['auth']['id']) {
        flash('error', 'Tidak dapat menghapus akun sendiri.');
        redirect('admin/index.php?page=users');
    }

    db_delete('users', 'id = :id', ['id' => $editId]);
    log_activity('delete', 'users', $editId);
    flash('success', 'User berhasil dihapus.');
    redirect('admin/index.php?page=users');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = clean_input($_POST['form_action'] ?? 'create');

    $data = [
        'role_id' => (int) ($_POST['role_id'] ?? 2),
        'full_name' => clean_input($_POST['full_name'] ?? ''),
        'username' => clean_input($_POST['username'] ?? ''),
        'email' => clean_input($_POST['email'] ?? ''),
        'phone' => clean_input($_POST['phone'] ?? ''),
        'gender' => clean_input($_POST['gender'] ?? '') ?: null,
        'birth_place' => clean_input($_POST['birth_place'] ?? ''),
        'birth_date' => clean_input($_POST['birth_date'] ?? '') ?: null,
        'address' => trim($_POST['address'] ?? ''),
        'bio' => trim($_POST['bio'] ?? ''),
        'status' => clean_input($_POST['status'] ?? 'active'),
        'updated_at' => now(),
    ];

    try {
        $photo = handle_upload('profile_photo', 'profiles', ['image/jpeg', 'image/png', 'image/webp'], 2097152);
    } catch (Throwable $e) {
        $photo = null;
        flash('error', $e->getMessage());
    }

    if ($photo) {
        $data['profile_photo'] = $photo;
    }

    if ($formAction === 'create') {
        $password = $_POST['password'] ?? '';
        if (strlen($password) < 8) {
            flash('error', 'Password minimal 8 karakter.');
            redirect('admin/index.php?page=users');
        }

        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        $data['registered_at'] = now();
        $data['created_at'] = now();

        db_insert('users', $data);
        log_activity('create', 'users', null, ['email' => $data['email']]);
        flash('success', 'User baru berhasil ditambahkan.');
    } else {
        $id = decrypt_id($_POST['ref'] ?? '') ?: 0;
        if ($id <= 0) {
            flash('error', 'Data user tidak valid.');
            redirect('admin/index.php?page=users');
        }

        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        db_update('users', $data, 'id = :id', ['id' => $id]);
        log_activity('update', 'users', $id);
        flash('success', 'Data user berhasil diperbarui.');
    }

    redirect('admin/index.php?page=users');
}

$editData = $editId ? db_one('SELECT * FROM users WHERE id = :id LIMIT 1', ['id' => $editId]) : null;
$users = db_all('SELECT u.*, r.name AS role_name FROM users u LEFT JOIN roles r ON r.id = u.role_id ORDER BY u.id DESC');

include __DIR__ . '/../../admin/partials/header.php';
?>
<section class="panel">
    <h3><?= $editData ? 'Edit User' : 'Tambah User Baru' ?></h3>
    <form method="post" enctype="multipart/form-data" class="inline-form">
        <?= csrf_field() ?>
        <input type="hidden" name="form_action" value="<?= $editData ? 'update' : 'create' ?>">
        <?php if ($editData): ?><input type="hidden" name="ref" value="<?= e(encrypt_id((int)$editData['id'])) ?>"><?php endif; ?>
        <input type="text" name="full_name" value="<?= e($editData['full_name'] ?? '') ?>" placeholder="Nama lengkap" required>
        <input type="text" name="username" value="<?= e($editData['username'] ?? '') ?>" placeholder="Username" required>
        <input type="email" name="email" value="<?= e($editData['email'] ?? '') ?>" placeholder="Email" required>
        <input type="password" name="password" placeholder="<?= $editData ? 'Kosongkan jika tidak diubah' : 'Password minimal 8 karakter' ?>" <?= $editData ? '' : 'required' ?>>
        <input type="text" name="phone" value="<?= e($editData['phone'] ?? '') ?>" placeholder="Nomor HP">
        <select name="gender">
            <option value="">Jenis kelamin</option>
            <option value="male" <?= ($editData['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="female" <?= ($editData['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Perempuan</option>
        </select>
        <input type="text" name="birth_place" value="<?= e($editData['birth_place'] ?? '') ?>" placeholder="Tempat lahir">
        <input type="date" name="birth_date" value="<?= e($editData['birth_date'] ?? '') ?>">
        <textarea name="address" placeholder="Alamat lengkap"><?= e($editData['address'] ?? '') ?></textarea>
        <textarea name="bio" placeholder="Bio singkat"><?= e($editData['bio'] ?? '') ?></textarea>
        <select name="status">
            <option value="active" <?= ($editData['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= ($editData['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            <option value="blocked" <?= ($editData['status'] ?? '') === 'blocked' ? 'selected' : '' ?>>Blocked</option>
        </select>
        <select name="role_id">
            <?php foreach ($roles as $role): ?>
                <option value="<?= $role['id'] ?>" <?= (int)($editData['role_id'] ?? 2) === (int)$role['id'] ? 'selected' : '' ?>><?= e($role['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="file" name="profile_photo">
        <button class="btn btn-primary" type="submit"><?= $editData ? 'Update User' : 'Simpan User' ?></button>
    </form>
</section>

<section class="panel">
    <h3>Daftar User</h3>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= e($user['full_name']) ?></td>
                    <td><?= e($user['username']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td><?= e($user['role_name']) ?></td>
                    <td><?= e($user['status']) ?></td>
                    <td>
                        <a class="btn btn-secondary" href="<?= base_url('admin/index.php?page=users&ref=' . urlencode(encrypt_id((int)$user['id']))) ?>">Edit</a>
                        <?php if (has_role('admin')): ?>
                            <a class="btn btn-danger" data-confirm="Hapus user ini?" href="<?= base_url('admin/index.php?page=users&action=delete&csrf_token=' . urlencode(csrf_token()) . '&ref=' . urlencode(encrypt_id((int)$user['id']))) ?>">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/../../admin/partials/footer.php'; ?>

