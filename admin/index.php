<?php
require __DIR__ . '/config.php';
require_admin();

$title = 'Admin Dashboard - Intime Furniture';
include __DIR__ . '/partials/header.php';

// Gather statistics
$stats = [];
//$ general counts
$res = mysqli_query($conn, "SELECT COUNT(*) as total_products FROM produk");
$stats['total_products'] = ($res && $row = mysqli_fetch_assoc($res)) ? $row['total_products'] : 0;
if ($res) mysqli_free_result($res);

$res = mysqli_query($conn, "SELECT COUNT(*) as total_categories FROM kategori_produk");
$stats['total_categories'] = ($res && $row = mysqli_fetch_assoc($res)) ? $row['total_categories'] : 0;
if ($res) mysqli_free_result($res);

$res = mysqli_query($conn, "SELECT COUNT(*) as total_settings FROM settings");
$stats['total_settings'] = ($res && $row = mysqli_fetch_assoc($res)) ? $row['total_settings'] : 0;
if ($res) mysqli_free_result($res);

// Admins
$res = mysqli_query($conn, "SELECT COUNT(*) as total_admins FROM admin");
$stats['total_admins'] = ($res && $row = mysqli_fetch_assoc($res)) ? $row['total_admins'] : 0;
if ($res) mysqli_free_result($res);

// Produk statistik totals
$res = mysqli_query($conn, "SELECT COUNT(*) as total_events FROM produk_statistik");
$stats['total_events'] = ($res && $row = mysqli_fetch_assoc($res)) ? $row['total_events'] : 0;
if ($res) mysqli_free_result($res);

// Breakdown by event type
// event type breakdown (keep for reference if needed)
$event_breakdown = ['search' => 0, 'click' => 0, 'wa_click' => 0];
$res = mysqli_query($conn, "SELECT tipe_event, COUNT(*) as cnt FROM produk_statistik GROUP BY tipe_event");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $event_breakdown[$r['tipe_event']] = (int)$r['cnt'];
    }
    mysqli_free_result($res);
}

// Events last 7 days
$events_last7 = [];
$res = mysqli_query($conn, "SELECT DATE(created_at) as d, COUNT(*) as c FROM produk_statistik WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(created_at) ORDER BY DATE(created_at)");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $events_last7[$r['d']] = (int)$r['c'];
    }
    mysqli_free_result($res);
}

// Top products by events
$top_products = [];
$res = mysqli_query($conn, "SELECT p.id, p.nama_produk, COUNT(ps.id) as cnt FROM produk_statistik ps JOIN produk p ON ps.id_produk = p.id GROUP BY ps.id_produk ORDER BY cnt DESC LIMIT 6");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) $top_products[] = $r;
    mysqli_free_result($res);
}

// Recent products
$recent = [];
$res = mysqli_query($conn, "SELECT p.id, p.nama_produk, p.harga, p.gambar, k.nama_kategori, p.created_at FROM produk p LEFT JOIN kategori_produk k ON p.id_kategori = k.id ORDER BY p.created_at DESC LIMIT 6");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) $recent[] = $r;
    mysqli_free_result($res);
}

// Top clicks and searches per product
$top_clicks = [];
$res = mysqli_query($conn, "SELECT p.id, p.nama_produk, COUNT(*) as cnt FROM produk_statistik ps JOIN produk p ON ps.id_produk = p.id WHERE ps.tipe_event = 'click' GROUP BY ps.id_produk ORDER BY cnt DESC LIMIT 8");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) $top_clicks[] = $r;
    mysqli_free_result($res);
}

$top_searches = [];
$res = mysqli_query($conn, "SELECT p.id, p.nama_produk, COUNT(*) as cnt FROM produk_statistik ps JOIN produk p ON ps.id_produk = p.id WHERE ps.tipe_event = 'search' GROUP BY ps.id_produk ORDER BY cnt DESC LIMIT 8");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) $top_searches[] = $r;
    mysqli_free_result($res);
}

// Category popularity (by events on products in that category)
$cat_stats = [];
$res = mysqli_query($conn, "SELECT k.id, k.nama_kategori,
    COALESCE(pcnt.cnt,0) + COALESCE(ccnt.cnt,0) AS cnt
    FROM kategori_produk k
    LEFT JOIN (
        SELECT p.id_kategori AS kid, COUNT(ps.id) AS cnt FROM produk_statistik ps JOIN produk p ON ps.id_produk = p.id GROUP BY p.id_kategori
    ) pcnt ON pcnt.kid = k.id
    LEFT JOIN (
        SELECT id_kategori AS kid, COUNT(*) AS cnt FROM produk_statistik WHERE id_kategori IS NOT NULL GROUP BY id_kategori
    ) ccnt ON ccnt.kid = k.id
    ORDER BY cnt DESC LIMIT 8");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) $cat_stats[] = $r;
    mysqli_free_result($res);
}
?>

<body class="bg-light">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <?php include __DIR__ . '/partials/topbar.php'; ?>

    <main class="admin-main">
        <div class="container-fluid py-4" style="max-width:1200px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Dashboard</h3>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <div class="card p-3 shadow-sm h-100">
                        <div class="small text-muted">Produk Terbanyak Di-Klik</div>
                        <?php if (!empty($top_clicks)): ?>
                            <div class="h5 mb-0"><?= htmlspecialchars($top_clicks[0]['nama_produk']) ?></div>
                            <small class="text-muted"><?= (int)$top_clicks[0]['cnt'] ?> klik</small>
                        <?php else: ?>
                            <div class="h5 mb-0">-</div>
                            <small class="text-muted">Belum ada data</small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card p-3 shadow-sm h-100">
                        <div class="small text-muted">Produk Terbanyak Dicari</div>
                        <?php if (!empty($top_searches)): ?>
                            <div class="h5 mb-0"><?= htmlspecialchars($top_searches[0]['nama_produk']) ?></div>
                            <small class="text-muted"><?= (int)$top_searches[0]['cnt'] ?> pencarian</small>
                        <?php else: ?>
                            <div class="h5 mb-0">-</div>
                            <small class="text-muted">Belum ada data</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-8 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Events (7 hari terakhir)</h5>
                            <canvas id="eventsLine" height="120"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Kategori Terpopuler</h5>
                            <canvas id="categoriesDonut" height="200"></canvas>
                            <?php if (!empty($cat_stats)): ?>
                                <ul class="list-unstyled mt-3 small">
                                    <?php foreach ($cat_stats as $c): ?>
                                        <li><?= htmlspecialchars($c['nama_kategori']) ?> — <?= (int)$c['cnt'] ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="small text-muted mt-2">Belum ada data kategori.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Produk Terbaru</h5>
                    <?php if (empty($recent)): ?>
                        <p class="text-muted small">Belum ada produk.</p>
                    <?php else: ?>
                        <div class="row g-3 mt-3">
                            <?php foreach ($recent as $p): ?>
                                <div class="col-6 col-md-3">
                                    <div class="card h-100">
                                        <?php if (!empty($p['gambar'])): ?>
                                            <div class="ratio ratio-1x1">
                                                <img src="<?= htmlspecialchars(public_image_url($p['gambar'], 'products')) ?>" class="card-img-top object-fit-cover" alt="<?= htmlspecialchars($p['nama_produk']) ?>">
                                            </div>
                                        <?php endif; ?>
                                        <div class="card-body p-2">
                                            <div class="small text-muted"><?= htmlspecialchars($p['nama_kategori'] ?? '-') ?></div>
                                            <div class="fw-bold"><?= htmlspecialchars($p['nama_produk']) ?></div>
                                            <div class="text-primary">Rp <?= number_format($p['harga'], 0, ',', '.') ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title">Top Produk — Klik</h6>
                            <canvas id="topClicks" height="140"></canvas>
                            <?php if (!empty($top_clicks)): ?>
                                <ol class="small mt-2">
                                    <?php foreach ($top_clicks as $c): ?>
                                        <li><?= htmlspecialchars($c['nama_produk']) ?> — <?= (int)$c['cnt'] ?> klik</li>
                                    <?php endforeach; ?>
                                </ol>
                            <?php else: ?>
                                <p class="small text-muted mt-2">Belum ada data klik.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title">Top Produk — Pencarian</h6>
                            <canvas id="topSearches" height="140"></canvas>
                            <?php if (!empty($top_searches)): ?>
                                <ol class="small mt-2">
                                    <?php foreach ($top_searches as $s): ?>
                                        <li><?= htmlspecialchars($s['nama_produk']) ?> — <?= (int)$s['cnt'] ?> pencarian</li>
                                    <?php endforeach; ?>
                                </ol>
                            <?php else: ?>
                                <p class="small text-muted mt-2">Belum ada data pencarian.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Top Produk (berdasarkan events)</h5>
                    <?php if (empty($top_products)): ?>
                        <p class="text-muted small">Belum ada data statistik produk.</p>
                    <?php else: ?>
                        <ol class="small">
                            <?php foreach ($top_products as $tp): ?>
                                <li><?= htmlspecialchars($tp['nama_produk']) ?> — <?= (int)$tp['cnt'] ?> events</li>
                            <?php endforeach; ?>
                        </ol>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Prepare data for charts
                const eventsLast7 = <?= json_encode(array_values($events_last7)) ?>;
                const eventsLast7Labels = <?= json_encode(array_keys($events_last7)) ?>;
                const eventBreakdown = <?= json_encode(array_values($event_breakdown)) ?>;
                const eventBreakLabels = <?= json_encode(array_keys($event_breakdown)) ?>;
                // categories data
                const catLabels = <?= json_encode(array_map(function ($c) {
                                        return $c['nama_kategori'];
                                    }, $cat_stats)) ?>;
                const catValues = <?= json_encode(array_map(function ($c) {
                                        return (int)$c['cnt'];
                                    }, $cat_stats)) ?>;
                // Top product stats
                const topClicks = <?= json_encode(array_column($top_clicks, 'cnt')) ?>;
                const topClicksLabels = <?= json_encode(array_column($top_clicks, 'nama_produk')) ?>;
                const topSearches = <?= json_encode(array_column($top_searches, 'cnt')) ?>;
                const topSearchesLabels = <?= json_encode(array_column($top_searches, 'nama_produk')) ?>;

                // Line chart for last 7 days
                const ctxLine = document.getElementById('eventsLine');
                if (ctxLine) {
                    new Chart(ctxLine, {
                        type: 'line',
                        data: {
                            labels: eventsLast7Labels,
                            datasets: [{
                                label: 'Events',
                                data: eventsLast7,
                                borderColor: '#6f42c1',
                                backgroundColor: 'rgba(111,66,193,0.08)',
                                tension: 0.3,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }

                // Donut chart for categories
                const ctxCat = document.getElementById('categoriesDonut');
                if (ctxCat) {
                    new Chart(ctxCat, {
                        type: 'doughnut',
                        data: {
                            labels: catLabels,
                            datasets: [{
                                data: catValues,
                                backgroundColor: ['#4dabf7', '#51cf66', '#ff922b', '#6f42c1', '#ffd43b', '#ffa8a8', '#74c0fc', '#b197fc']
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }
                // Bar chart for top clicks
                const ctxClicks = document.getElementById('topClicks');
                if (ctxClicks && topClicks.length) {
                    new Chart(ctxClicks, {
                        type: 'bar',
                        data: {
                            labels: topClicksLabels,
                            datasets: [{
                                label: 'Klik',
                                data: topClicks,
                                backgroundColor: '#4dabf7'
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }

                // Bar chart for top searches
                const ctxSearches = document.getElementById('topSearches');
                if (ctxSearches && topSearches.length) {
                    new Chart(ctxSearches, {
                        type: 'bar',
                        data: {
                            labels: topSearchesLabels,
                            datasets: [{
                                label: 'Pencarian',
                                data: topSearches,
                                backgroundColor: '#51cf66'
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }
            });
        </script>

        <?php include __DIR__ . '/partials/scripts.php'; ?>
</body>

</html>