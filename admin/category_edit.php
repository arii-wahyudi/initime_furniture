<?php
require __DIR__ . '/config.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$category = null;
if ($id) {
    $q = mysqli_prepare($conn, "SELECT * FROM kategori_produk WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($q, 'i', $id);
    mysqli_stmt_execute($q);
    $res = mysqli_stmt_get_result($q);
    $category = mysqli_fetch_assoc($res);
}

$title = $id ? 'Edit Kategori' : 'Tambah Kategori';
include __DIR__ . '/partials/header.php';
?>
<body class="bg-light">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <?php include __DIR__ . '/partials/topbar.php'; ?>

    <main class="admin-main">
        <div class="container-fluid py-4" style="max-width:700px;">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= $title ?></h5>
                    <form action="<?= $id ? 'category_update.php' : 'category_store.php' ?>" method="post">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <?php if ($id): ?><input type="hidden" name="id" value="<?= (int)$id ?>"><?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label">Nama Kategori</label>
                            <input type="text" name="nama_kategori" class="form-control" required value="<?= htmlspecialchars($category['nama_kategori'] ?? '') ?>">
                        </div>
                        <button class="btn btn-primary">Simpan</button>
                        <a href="categories.php" class="btn btn-link">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>
