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
                    <form action="<?= $id ? 'category_update.php' : 'category_store.php' ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <?php if ($id): ?><input type="hidden" name="id" value="<?= (int)$id ?>"><?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label">Nama Kategori</label>
                            <input type="text" name="nama_kategori" class="form-control" required value="<?= htmlspecialchars($category['nama_kategori'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gambar Kategori (opsional)</label>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <?php $imgInfo = resolve_image_info($category['image'] ?? '', 'categories'); $imgUrl = htmlspecialchars($imgInfo['url']); ?>
                                <?php if (!empty($imgUrl)): ?><img id="preview_image" src="<?= $imgUrl ?>" class="img-preview" style="display:block"><?php else: ?><img id="preview_image" src="" class="img-preview" style="display:none"><?php endif; ?>
                                <div>
                                    <input type="file" id="image_file" name="image_file" class="form-control" accept="image/*" onchange="previewFile(this,'preview_image'); showFilename(this,'image_filename')">
                                    <div id="image_filename" class="input-filename"><?= htmlspecialchars(basename($category['image'] ?? '')) ?></div>
                                </div>
                            </div>
                            <input type="text" name="image" class="form-control" placeholder="Path atau filename (opsional)" value="<?= htmlspecialchars($category['image'] ?? '') ?>">
                            <div class="form-help">Upload gambar kategori untuk tampil pada listing. Maks 1MB disarankan.</div>
                            <?php if (!empty($category['image'])): ?>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="remove_image" name="remove_image">
                                    <label class="form-check-label" for="remove_image">Hapus gambar saat menyimpan</label>
                                </div>
                            <?php endif; ?>
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
