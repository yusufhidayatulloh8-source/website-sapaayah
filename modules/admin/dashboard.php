<?php
admin_guard('dashboard.view');
$pageTitle = 'Dashboard Admin - SAPA Ayah';
$pageHeading = 'Dashboard';

$statCards = [
    ['label' => 'Total Pengguna', 'value' => (int) (db_one('SELECT COUNT(*) total FROM users')['total'] ?? 0), 'icon' => 'US'],
    ['label' => 'Total Artikel', 'value' => (int) (db_one('SELECT COUNT(*) total FROM articles')['total'] ?? 0), 'icon' => 'AR'],
    ['label' => 'Event Aktif', 'value' => (int) (db_one('SELECT COUNT(*) total FROM events WHERE status = "upcoming"')['total'] ?? 0), 'icon' => 'EV'],
    ['label' => 'Program SAPA', 'value' => (int) (db_one('SELECT COUNT(*) total FROM programs')['total'] ?? 0), 'icon' => 'PR'],
];

$monthlyRows = db_all('
    SELECT DATE_FORMAT(created_at, "%Y-%m") AS ym, COUNT(*) AS total
    FROM contacts
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
    GROUP BY DATE_FORMAT(created_at, "%Y-%m")
    ORDER BY ym ASC
');

$monthMap = [];
foreach ($monthlyRows as $row) {
    $monthMap[$row['ym']] = (int) $row['total'];
}

$monthLabels = [];
$monthValues = [];
for ($i = 5; $i >= 0; $i--) {
    $stamp = strtotime('-' . $i . ' month');
    $ym = date('Y-m', $stamp);
    $monthLabels[] = date('M', $stamp);
    $monthValues[] = $monthMap[$ym] ?? 0;
}

$distributionRows = [
    ['label' => 'Artikel', 'value' => (int) (db_one('SELECT COUNT(*) total FROM articles')['total'] ?? 0)],
    ['label' => 'Program', 'value' => (int) (db_one('SELECT COUNT(*) total FROM programs')['total'] ?? 0)],
    ['label' => 'Testimoni', 'value' => (int) (db_one('SELECT COUNT(*) total FROM testimonials')['total'] ?? 0)],
    ['label' => 'Event', 'value' => (int) (db_one('SELECT COUNT(*) total FROM events')['total'] ?? 0)],
];

$recentArticles = db_all('
    SELECT a.title, a.status, u.full_name AS author_name
    FROM articles a
    LEFT JOIN users u ON u.id = a.author_id
    ORDER BY a.id DESC
    LIMIT 3
');

$upcomingEvents = db_all('
    SELECT title, event_date, location, status
    FROM events
    WHERE status = "upcoming"
    ORDER BY event_date ASC, id DESC
    LIMIT 3
');

include __DIR__ . '/../../admin/partials/header.php';
?>
<div class="stat-grid">
    <?php foreach ($statCards as $card): ?>
        <article class="stat-card">
            <div>
                <p><?= e($card['label']) ?></p>
                <h3><?= e((string) $card['value']) ?></h3>
            </div>
            <div class="stat-icon"><?= e($card['icon']) ?></div>
        </article>
    <?php endforeach; ?>
</div>

<div class="panel-grid">
    <section class="panel">
        <h3>Pengunjung Website</h3>
        <p class="panel-sub">Total kunjungan 6 bulan terakhir</p>
        <canvas id="chart" width="900" height="250" style="width:100%;max-width:100%;border-radius:10px;"></canvas>
    </section>
    <section class="panel">
        <h3>Distribusi Konten</h3>
        <p class="panel-sub">Perbandingan jumlah konten per kategori</p>
        <canvas id="contentChart" width="900" height="250" style="width:100%;max-width:100%;border-radius:10px;"></canvas>
    </section>
</div>

<div class="panel-grid">
    <section class="panel">
        <h3>Artikel Terbaru</h3>
        <p class="panel-sub">Konten terbaru yang baru ditambahkan</p>
        <div class="dashboard-list">
            <?php foreach ($recentArticles as $article): ?>
                <article class="dashboard-item">
                    <div>
                        <h4><?= e($article['title']) ?></h4>
                        <p>Oleh <?= e($article['author_name'] ?: 'Admin') ?></p>
                    </div>
                    <span class="dashboard-tag <?= ($article['status'] ?? '') === 'published' ? 'tag-green' : 'tag-yellow' ?>">
                        <?= e(ucfirst($article['status'] ?? 'draft')) ?>
                    </span>
                </article>
            <?php endforeach; ?>
            <?php if (!$recentArticles): ?>
                <p class="panel-sub">Belum ada artikel.</p>
            <?php endif; ?>
        </div>
    </section>
    <section class="panel">
        <h3>Event Mendatang</h3>
        <p class="panel-sub">Ringkasan event terdekat</p>
        <div class="dashboard-list">
            <?php foreach ($upcomingEvents as $event): ?>
                <article class="dashboard-item">
                    <div>
                        <h4><?= e($event['title']) ?></h4>
                        <p><?= format_date($event['event_date'], 'd M Y') ?> - <?= e($event['location'] ?: '-') ?></p>
                    </div>
                    <span class="dashboard-tag <?= ($event['status'] ?? '') === 'upcoming' ? 'tag-green' : 'tag-yellow' ?>">
                        <?= e(ucfirst($event['status'] ?? '-')) ?>
                    </span>
                </article>
            <?php endforeach; ?>
            <?php if (!$upcomingEvents): ?>
                <p class="panel-sub">Belum ada event.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<section class="panel">
    <h3>Aksi Cepat</h3>
    <p class="panel-sub">Akses cepat untuk aktivitas rutin admin</p>
    <div class="quick-actions">
        <a class="btn quick-1" href="<?= base_url('admin/index.php?page=articles') ?>">Artikel Baru</a>
        <a class="btn quick-2" href="<?= base_url('admin/index.php?page=events') ?>">Buat Event</a>
        <a class="btn quick-3" href="<?= base_url('admin/index.php?page=programs') ?>">Program Baru</a>
        <a class="btn quick-4" href="<?= base_url('admin/index.php?page=testimonials') ?>">Kelola Testimoni</a>
    </div>
</section>

<section class="panel">
    <h3>Aktivitas Terbaru</h3>
    <div class="table-wrap">
        <table>
            <thead>
            <tr><th>Waktu</th><th>User</th><th>Aksi</th><th>Entitas</th><th>IP</th></tr>
            </thead>
            <tbody>
            <?php
            $recentLogs = db_all('SELECT al.*, u.full_name FROM activity_logs al LEFT JOIN users u ON u.id = al.user_id ORDER BY al.id DESC LIMIT 8');
            foreach ($recentLogs as $log):
            ?>
                <tr>
                    <td><?= format_date($log['created_at'], 'd M Y H:i') ?></td>
                    <td><?= e($log['full_name'] ?: '-') ?></td>
                    <td><?= e($log['action']) ?></td>
                    <td><?= e($log['entity']) ?> #<?= e((string) $log['entity_id']) ?></td>
                    <td><?= e($log['ip_address'] ?: '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
(() => {
    const visitorLabels = <?= json_encode($monthLabels, JSON_UNESCAPED_UNICODE) ?>;
    const visitorValues = <?= json_encode($monthValues, JSON_UNESCAPED_UNICODE) ?>;
    const contentRows = <?= json_encode($distributionRows, JSON_UNESCAPED_UNICODE) ?>;

    const drawGrid = (ctx, width, height, padX, padY, steps, color) => {
        ctx.strokeStyle = color;
        ctx.lineWidth = 1;
        for (let i = 0; i <= steps; i++) {
            const y = padY + ((height - padY * 2 - 20) / steps) * i;
            ctx.beginPath();
            ctx.moveTo(padX, y);
            ctx.lineTo(width - padX, y);
            ctx.stroke();
        }
    };

    const drawBar = (ctx, x, y, width, height) => {
        if (typeof ctx.roundRect === "function") {
            ctx.beginPath();
            ctx.roundRect(x, y, width, height, [8, 8, 0, 0]);
            ctx.fill();
            return;
        }
        ctx.fillRect(x, y, width, height);
    };

    const visitorCanvas = document.getElementById("chart");
    if (visitorCanvas) {
        const ctx = visitorCanvas.getContext("2d");
        const max = Math.max(...visitorValues, 10);
        const padX = 40;
        const padY = 22;
        const chartW = visitorCanvas.width - padX * 2;
        const chartH = visitorCanvas.height - padY * 2 - 20;

        ctx.clearRect(0, 0, visitorCanvas.width, visitorCanvas.height);
        ctx.fillStyle = "#6b6b6b";
        ctx.font = "12px Inter";
        drawGrid(ctx, visitorCanvas.width, visitorCanvas.height, padX, padY, 4, "#e8dcc4");

        ctx.strokeStyle = "#6b7456";
        ctx.lineWidth = 2.5;
        ctx.beginPath();
        visitorValues.forEach((val, idx) => {
            const x = padX + (chartW / Math.max(visitorValues.length - 1, 1)) * idx;
            const y = padY + chartH - (val / max) * chartH;
            if (idx === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        });
        ctx.stroke();

        visitorValues.forEach((val, idx) => {
            const x = padX + (chartW / Math.max(visitorValues.length - 1, 1)) * idx;
            const y = padY + chartH - (val / max) * chartH;
            ctx.fillStyle = "#6b7456";
            ctx.beginPath();
            ctx.arc(x, y, 4, 0, Math.PI * 2);
            ctx.fill();
        });

        ctx.fillStyle = "#6b6b6b";
        visitorLabels.forEach((label, idx) => {
            const x = padX + (chartW / Math.max(visitorLabels.length - 1, 1)) * idx;
            ctx.fillText(label, x - 10, visitorCanvas.height - 6);
        });
    }

    const contentCanvas = document.getElementById("contentChart");
    if (contentCanvas) {
        const ctx = contentCanvas.getContext("2d");
        const padX = 36;
        const padY = 22;
        const chartW = contentCanvas.width - padX * 2;
        const chartH = contentCanvas.height - padY * 2 - 20;
        const max = Math.max(...contentRows.map((row) => Number(row.value || 0)), 10);

        ctx.clearRect(0, 0, contentCanvas.width, contentCanvas.height);
        ctx.fillStyle = "#6b6b6b";
        ctx.font = "12px Inter";
        drawGrid(ctx, contentCanvas.width, contentCanvas.height, padX, padY, 4, "#e8dcc4");

        const count = Math.max(contentRows.length, 1);
        const slotW = chartW / count;
        const barW = Math.min(90, slotW * 0.72);

        contentRows.forEach((row, idx) => {
            const val = Number(row.value || 0);
            const barH = (val / max) * chartH;
            const x = padX + slotW * idx + (slotW - barW) / 2;
            const y = padY + chartH - barH;

            ctx.fillStyle = "#8b9474";
            drawBar(ctx, x, y, barW, barH);

            ctx.fillStyle = "#5f6550";
            ctx.fillText(row.label, x + (barW / 2) - (row.label.length * 3), contentCanvas.height - 6);
        });
    }
})();
</script>
<?php include __DIR__ . '/../../admin/partials/footer.php'; ?>
