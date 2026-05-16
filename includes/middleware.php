<?php

function admin_guard(string $permission = ''): void
{
    require_login();
    enforce_session_timeout();

    if ($permission !== '') {
        require_permission($permission);
    }
}

function log_activity(string $action, string $entity, ?int $entityId = null, array $meta = []): void
{
    if (!is_logged_in()) {
        return;
    }

    db_insert('activity_logs', [
        'user_id' => $_SESSION['auth']['id'],
        'action' => $action,
        'entity' => $entity,
        'entity_id' => $entityId,
        'meta' => json_encode($meta, JSON_UNESCAPED_UNICODE),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        'created_at' => now(),
    ]);
}
