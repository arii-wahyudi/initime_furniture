<?php
require __DIR__ . '/config.php';
require_admin();

$title = 'Manajemen Produk - Intime Furniture';
include __DIR__ . '/partials/header.php';
?>
<body class="bg-light">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <?php include __DIR__ . '/partials/topbar.php'; ?>

    <main class="admin-main">
        <div class="container-fluid py-4">
            <div class="d-flex justify-content-between align-items-center mb-3" style="margin-left:0;">
                <h4 class="mb-0">Produk</h4>
                <a href="product_edit.php" class="btn btn-primary btn-sm">Tambah Produk</a>
            </div>

            <?php
            // fetch rows into array so we can render both desktop table and mobile cards
            $rows = [];
            $res = mysqli_query($conn, "SELECT p.id, p.nama_produk, p.harga, p.status, k.nama_kategori, p.gambar, p.slug FROM produk p LEFT JOIN kategori_produk k ON p.id_kategori = k.id ORDER BY p.id DESC");
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
                                    <th>Gbr</th>
                                    <th>Nama</th>
                                    <th class="d-none d-md-table-cell">Kategori</th>
                                    <th class="d-none d-md-table-cell">Harga</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($rows)): $i=1; foreach($rows as $row): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <?php $rimg = resolve_image_info($row['gambar'] ?? '', 'products'); $rthumb = htmlspecialchars($rimg['url']); ?>
                                        <td><img src="<?= $rthumb ?>" style="width:56px;height:40px;object-fit:cover;border-radius:4px;border:1px solid #eee"></td>
                                        <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                                        <td class="d-none d-md-table-cell"><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></td>
                                        <td class="d-none d-md-table-cell">Rp <?= number_format($row['harga'],0,',','.') ?></td>
                                        <td><?= htmlspecialchars($row['status']) ?></td>
                                        <td>
                                            <a href="product_edit.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="product_delete.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus produk?')">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="6" class="text-muted small">Tidak ada produk.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile list (compact cards) -->
                    <div class="d-block d-sm-none">
                        <?php if (!empty($rows)): foreach($rows as $row):
                            $ri = resolve_image_info($row['gambar'] ?? '', 'products');
                            $thumb = $ri['url'] ?: 'assets/img/furniture-img.png';
                            $thumb = htmlspecialchars($thumb);
                            $status = htmlspecialchars($row['status'] ?? '');
                        ?>
                        <div class="card mb-2 admin-mobile-product-card">
                            <div class="row g-0 align-items-center">
                                <div class="col-4">
                                    <div class="ratio ratio-1x1 rounded-start overflow-hidden">
                                        <img src="<?= $thumb ?>" alt="<?= htmlspecialchars($row['nama_produk']) ?>" class="object-fit-cover">
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="card-body py-2 px-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="fw-semibold product-title small mb-1"><?= htmlspecialchars($row['nama_produk']) ?></div>
                                            <?php if (strtolower($status) === 'aktif'): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= $status ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-success fw-bold">Rp <?= number_format($row['harga'],0,',','.') ?></div>
                                        <div class="mt-2 d-flex gap-2">
                                            <a href="product_edit.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary flex-fill">Edit</a>
                                            <a href="product_delete.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-danger flex-fill" onclick="return confirm('Hapus produk?')">Hapus</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; else: ?>
                            <div class="text-muted small">Tidak ada produk.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>
