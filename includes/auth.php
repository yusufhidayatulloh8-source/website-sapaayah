<?php

function user_by_id(int $id): ?array
{
    return db_one('SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id = u.role_id WHERE u.id = :id LIMIT 1', ['id' => $id]);
}

function user_by_email_or_username(string $identity): ?array
{
    return db_one(
        'SELECT * FROM users WHERE email = :email_identity OR username = :username_identity LIMIT 1',
        [
            'email_identity' => $identity,
            'username_identity' => $identity,
        ]
    );
}

function set_auth_session(array $user): void
{
    session_regenerate_id(true);

    $_SESSION['auth'] = [
        'id' => (int) $user['id'],
        'name' => $user['full_name'],
        'role_id' => (int) $user['role_id'],
        'role_name' => $user['role_name'] ?? '',
        'last_activity' => time(),
    ];
}

function login_attempt(string $identity, string $password, bool $remember = false): bool
{
    $user = user_by_email_or_username($identity);

    if (!$user || $user['status'] !== 'active') {
        return false;
    }

    if (!password_verify($password, $user['password'])) {
        return false;
    }

    $role = db_one('SELECT name FROM roles WHERE id = :id LIMIT 1', ['id' => $user['role_id']]);
    $user['role_name'] = $role['name'] ?? 'user';
    set_auth_session($user);

    db_update('users', ['last_login_at' => now()], 'id = :id', ['id' => $user['id']]);

    if ($remember) {
        create_remember_me_token((int) $user['id']);
    }

    return true;
}

function logout_user(): void
{
    if (!empty($_COOKIE['remember_token'])) {
        db_delete('remember_tokens', 'token = :token', ['token' => hash('sha256', $_COOKIE['remember_token'])]);
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
    }

    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

function create_remember_me_token(int $userId): void
{
    $rawToken = bin2hex(random_bytes(32));
    $hashed = hash('sha256', $rawToken);
    $expiresAt = date('Y-m-d H:i:s', time() + ((int) app_config('remember_me_days', 30) * 86400));

    db_insert('remember_tokens', [
        'user_id' => $userId,
        'token' => $hashed,
        'expires_at' => $expiresAt,
        'created_at' => now(),
    ]);

    setcookie('remember_token', $rawToken, [
        'expires' => strtotime($expiresAt),
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function restore_session_from_remember_me(): void
{
    if (is_logged_in() || empty($_COOKIE['remember_token'])) {
        return;
    }

    $hashed = hash('sha256', $_COOKIE['remember_token']);
    $token = db_one(
        'SELECT rt.*, u.full_name, u.role_id FROM remember_tokens rt JOIN users u ON u.id = rt.user_id WHERE rt.token = :token AND rt.expires_at > NOW() AND u.status = "active" LIMIT 1',
        ['token' => $hashed]
    );

    if (!$token) {
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        return;
    }

    $role = db_one('SELECT name FROM roles WHERE id = :id LIMIT 1', ['id' => $token['role_id']]);
    set_auth_session([
        'id' => $token['user_id'],
        'full_name' => $token['full_name'],
        'role_id' => $token['role_id'],
        'role_name' => $role['name'] ?? 'user',
    ]);
}

function is_logged_in(): bool
{
    return !empty($_SESSION['auth']['id']);
}

function auth_user(): ?array
{
    if (!is_logged_in()) {
        return null;
    }

    return user_by_id((int) $_SESSION['auth']['id']);
}

function enforce_session_timeout(): void
{
    if (!is_logged_in()) {
        return;
    }

    $lastActivity = $_SESSION['auth']['last_activity'] ?? 0;
    $timeout = (int) app_config('session_timeout', 3600);

    if ((time() - $lastActivity) > $timeout) {
        logout_user();
        redirect('auth/login.php?timeout=1');
    }

    $_SESSION['auth']['last_activity'] = time();
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('error', 'Silakan login terlebih dahulu.');
        redirect('auth/login.php');
    }
}

function has_role(string $roleName): bool
{
    return strtolower($_SESSION['auth']['role_name'] ?? '') === strtolower($roleName);
}

function require_role(string $roleName): void
{
    require_login();

    if (!has_role($roleName)) {
        http_response_code(403);
        include __DIR__ . '/../templates/error-403.php';
        exit;
    }
}

function can_access(string $permissionKey): bool
{
    if (!is_logged_in()) {
        return false;
    }

    if (has_role('admin')) {
        return true;
    }

    $roleId = (int) ($_SESSION['auth']['role_id'] ?? 0);
    $permission = db_one(
        'SELECT p.id FROM role_permissions rp JOIN permissions p ON p.id = rp.permission_id WHERE rp.role_id = :role_id AND p.permission_key = :permission_key LIMIT 1',
        [
            'role_id' => $roleId,
            'permission_key' => $permissionKey,
        ]
    );

    return (bool) $permission;
}

function require_permission(string $permissionKey): void
{
    require_login();

    if (!can_access($permissionKey)) {
        http_response_code(403);
        include __DIR__ . '/../templates/error-403.php';
        exit;
    }
}

function register_user(array $payload): int
{
    $passwordHash = password_hash($payload['password'], PASSWORD_DEFAULT);

    return db_insert('users', [
        'role_id' => $payload['role_id'] ?? 2,
        'full_name' => $payload['full_name'],
        'username' => $payload['username'],
        'email' => $payload['email'],
        'password' => $passwordHash,
        'phone' => $payload['phone'] ?? '',
        'gender' => $payload['gender'] ?? null,
        'birth_place' => $payload['birth_place'] ?? '',
        'birth_date' => $payload['birth_date'] ?? null,
        'address' => $payload['address'] ?? '',
        'profile_photo' => $payload['profile_photo'] ?? null,
        'bio' => $payload['bio'] ?? '',
        'status' => $payload['status'] ?? 'active',
        'registered_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

function create_reset_token(string $email): ?string
{
    $user = db_one('SELECT id FROM users WHERE email = :email LIMIT 1', ['email' => $email]);
    if (!$user) {
        return null;
    }

    $rawToken = random_token(24);
    db_insert('password_resets', [
        'user_id' => $user['id'],
        'token' => hash('sha256', $rawToken),
        'expires_at' => date('Y-m-d H:i:s', time() + 3600),
        'created_at' => now(),
    ]);

    return $rawToken;
}

function reset_password(string $token, string $newPassword): bool
{
    $hashed = hash('sha256', $token);
    $row = db_one('SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW() LIMIT 1', ['token' => $hashed]);

    if (!$row) {
        return false;
    }

    db_update('users', [
        'password' => password_hash($newPassword, PASSWORD_DEFAULT),
        'updated_at' => now(),
    ], 'id = :id', ['id' => $row['user_id']]);

    db_delete('password_resets', 'id = :id', ['id' => $row['id']]);

    return true;
}
