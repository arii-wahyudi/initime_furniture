<?php
require __DIR__ . '/config.php';
require_admin();

$title = 'Kategori Produk - Admin';
include __DIR__ . '/partials/header.php';
?>
<body class="bg-light">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <?php include __DIR__ . '/partials/topbar.php'; ?>

    <main class="admin-main">
        <div class="container-fluid py-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Kategori Produk</h4>
                <a href="category_edit.php" class="btn btn-primary btn-sm">Tambah Kategori</a>
            </div>

            <?php
            // detect created_at column
            $has_created = false;
            $cols = mysqli_query($conn, "SHOW COLUMNS FROM kategori_produk");
            if ($cols) { while ($c = mysqli_fetch_assoc($cols)) { if ($c['Field'] === 'created_at') { $has_created = true; break; } } mysqli_free_result($cols); }

            // fetch rows
            $rows = [];
            if ($has_created) $res = mysqli_query($conn, "SELECT id, nama_kategori, created_at FROM kategori_produk ORDER BY nama_kategori");
            else $res = mysqli_query($conn, "SELECT id, nama_kategori FROM kategori_produk ORDER BY nama_kategori");
            if ($res) { while ($r = mysqli_fetch_assoc($res)) $rows[] = $r; mysqli_free_result($res); }
            ?>

            <div class="card">
                <div class="card-body p-2">
                    <!-- Desktop table -->
                    <div class="table-responsive d-none d-sm-block">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Kategori</th>
                                    <th class="d-none d-md-table-cell">Dibuat</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($rows)): $i=1; foreach($rows as $row): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                                        <td class="d-none d-md-table-cell"><?= htmlspecialchars($row['created_at'] ?? '-') ?></td>
                                        <td>
                                            <a href="category_edit.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="category_delete.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kategori?')">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="4" class="text-muted small">Tidak ada kategori.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile compact list -->
                    <div class="d-block d-sm-none">
                        <?php if (!empty($rows)): foreach($rows as $row): ?>
                            <div class="card mb-2 shadow-sm">
                                <div class="card-body p-2 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small fw-bold"><?= htmlspecialchars($row['nama_kategori']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($row['created_at'] ?? '-') ?></div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="category_edit.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="category_delete.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kategori?')">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                            <div class="text-muted small">Tidak ada kategori.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>
