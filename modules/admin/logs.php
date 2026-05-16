<?php
admin_guard('dashboard.view');
$pageTitle = 'Activity Log - SAPA Admin';
$pageHeading = 'Activity Log';

$rows = db_all('SELECT al.*, u.full_name FROM activity_logs al LEFT JOIN users u ON u.id = al.user_id ORDER BY al.id DESC LIMIT 300');

include __DIR__ . '/../../admin/partials/header.php';
?>
<section class="panel">
    <h3>Audit Trail Aktivitas</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Waktu</th><th>User</th><th>Aksi</th><th>Entitas</th><th>Detail</th><th>IP</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= format_date($row['created_at'], 'd M Y H:i:s') ?></td>
                    <td><?= e($row['full_name'] ?: '-') ?></td>
                    <td><?= e($row['action']) ?></td>
                    <td><?= e($row['entity']) ?> #<?= e((string)$row['entity_id']) ?></td>
                    <td><small><?= e($row['meta'] ?: '-') ?></small></td>
                    <td><?= e($row['ip_address'] ?: '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/../../admin/partials/footer.php'; ?>

