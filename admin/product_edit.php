<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require __DIR__ . '/config.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    die('ID produk tidak valid. <a href="products.php">Kembali ke daftar produk</a>');
}

// Fetch product
$q = mysqli_prepare($conn, "SELECT * FROM produk WHERE id = ? LIMIT 1");
if (!$q) {
    die('Database error: ' . htmlspecialchars(mysqli_error($conn)));
}

mysqli_stmt_bind_param($q, 'i', $id);
if (!mysqli_stmt_execute($q)) {
    die('Database error: ' . htmlspecialchars(mysqli_error($conn)));
}

$res = mysqli_stmt_get_result($q);
$product = mysqli_fetch_assoc($res);
mysqli_stmt_close($q);

if (!$product) {
    die('Produk tidak ditemukan. <a href="products.php">Kembali ke daftar produk</a>');
}

$title = 'Edit Produk - Admin';
include __DIR__ . '/partials/header.php';
?>

<body class="bg-light">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <?php include __DIR__ . '/partials/topbar.php'; ?>

    <main class="admin-main">
        <div class="container-fluid py-4" style="max-width:900px;">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Edit Produk: <?= htmlspecialchars($product['nama_produk']) ?></h5>
                </div>
                <div class="card-body">
                    <form id="productForm" action="product_update.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>

                        <input type="hidden" name="id" value="<?= (int)$id ?>">

                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" name="nama_produk" class="form-control" placeholder="Masukkan nama produk" value="<?= htmlspecialchars($product['nama_produk'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="id_kategori" class="form-select">
                                <option value="">-- Pilih Kategori Produk --</option>
                                <?php
                                $res = mysqli_query($conn, "SELECT id, nama_kategori FROM kategori_produk ORDER BY nama_kategori");
                                while ($row = mysqli_fetch_assoc($res)):
                                    $sel = ($product && $product['id_kategori'] == $row['id']) ? 'selected' : '';
                                ?>
                                    <option value="<?= (int)$row['id'] ?>" <?= $sel ?>><?= htmlspecialchars($row['nama_kategori']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input id="harga_display" type="text" class="form-control" placeholder="0" value="<?= isset($product['harga']) ? number_format($product['harga'], 0, ',', '.') : '' ?>">
                            </div>
                            <input type="hidden" name="harga" id="harga" value="<?= isset($product['harga']) ? (float)$product['harga'] : 0 ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gambar (jpg/png)</label>
                            <input id="gambarInput" type="file" name="gambar" class="form-control" accept="image/*">
                            <div class="form-text">Pilih gambar baru untuk mengganti gambar utama produk</div>
                            <div class="mt-3 text-center">
                                <?php if (!empty($product['gambar'])): ?>
                                    <img id="previewImage" src="<?= htmlspecialchars(
                                                                    (strpos($product['gambar'], 'http') === 0) ? $product['gambar'] : ('../uploads/products/' . $product['gambar'])
                                                                ) ?>" alt="Preview" style="max-width:100%; max-height:360px; display:inline-block; border:1px solid #eee; padding:6px; background:#fff;">
                                <?php else: ?>
                                    <img id="previewImage" src="" alt="Preview" style="max-width:100%; max-height:360px; display:none; border:1px solid #eee; padding:6px; background:#fff;">
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="removebg" id="removebg_hidden" value="0">
                            <input type="hidden" name="preview_ai_data" id="preview_ai_data" value="">
                            <input type="hidden" name="existing_gambar" value="<?= htmlspecialchars($product['gambar'] ?? '') ?>">
                        </div>

                        <!-- Galeri Gambar (5 slot, mendukung existing + upload baru) -->
                        <div class="mb-3">
                            <label class="form-label">Galeri Gambar Produk</label>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <button type="button" id="btnMultiUploadEdit" class="btn btn-sm btn-info">Upload Banyak</button>
                                <input type="file" id="multiUploadInputEdit" class="d-none" accept="image/*" multiple>
                                <small class="text-muted">Pilih beberapa gambar sekaligus — akan mengisi slot berurutan</small>
                            </div>

                            <div class="row g-2" id="imageSlotsEdit">
                                <?php
                                $product_images = [];
                                try {
                                    if (function_exists('get_product_images')) {
                                        $product_images = array_values(get_product_images($id, $conn));
                                    }
                                } catch (Exception $e) {
                                    $product_images = [];
                                }

                                for ($i = 0; $i < 5; $i++):
                                    $slot = isset($product_images[$i]) ? $product_images[$i] : null;
                                    $previewSrc = '';
                                    $existingId = '';
                                    if ($slot) {
                                        $rawName = $slot['gambar'] ?? '';
                                        $existingId = (int)$slot['id'];
                                        // Try to resolve public URL first; fallback to admin relative uploads path
                                        if (function_exists('public_image_url')) {
                                            $tmp = public_image_url($rawName);
                                            if (!empty($tmp) && (strpos($tmp, 'http') === 0 || strpos($tmp, '/') === 0)) {
                                                $previewSrc = $tmp;
                                            } else {
                                                $previewSrc = '../uploads/products/' . $rawName;
                                            }
                                        } else {
                                            $previewSrc = '../uploads/products/' . $rawName;
                                        }
                                    }
                                ?>
                                    <div class="col-4 col-sm-4 col-md-3 col-lg-2">
                                        <div class="card p-2 h-100 image-slot-card">
                                            <div class="d-flex flex-column h-100">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <?php if ($i === 0): ?>
                                                            <span class="badge bg-primary">Gambar Utama</span>
                                                        <?php else: ?>
                                                            <span class="text-muted">Slot <?= $i + 1 ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-clear-file" data-index="<?= $i ?>" title="Hapus file">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="image-preview-wrapper mb-2" data-index-wrapper="<?= $i ?>">
                                                    <img src="<?= htmlspecialchars($previewSrc) ?>" alt="preview" class="img-preview" data-index="<?= $i ?>" <?= $previewSrc ? 'style="display:block"' : '' ?> />
                                                    <div class="placeholder text-center text-muted" data-index="<?= $i ?>" <?= $previewSrc ? 'style="display:none"' : '' ?> >
                                                        <i class="fas fa-image" style="font-size:1.6rem;"></i>
                                                        <div class="mt-1 small">Klik untuk pilih gambar</div>
                                                    </div>
                                                </div>

                                                <div class="file-meta small text-muted mb-2" data-index-meta="<?= $i ?>" style="<?= $previewSrc ? 'display:block' : 'display:none' ?>;background:#f6fbf8;padding:6px;border-radius:6px;border:1px solid #eef6ef;">
                                                    <div class="file-name" data-index-name="<?= $i ?>"><?= $slot ? htmlspecialchars(basename($slot['gambar'])) : '' ?></div>
                                                    <div class="file-size text-muted" data-index-size="<?= $i ?>" style="font-size:0.75rem;"></div>
                                                </div>

                                                <div class="d-flex gap-2 mt-2 align-items-center slot-actions">
                                                    <input type="file" name="images[]" accept="image/*" class="d-none image-input-edit" data-index="<?= $i ?>">
                                                    <input type="hidden" name="existing_image_ids[]" value="<?= $existingId ?>" data-index-hidden="<?= $i ?>">
                                                    <button type="button" class="btn btn-sm btn-primary btn-select-file-edit" data-index="<?= $i ?>">Pilih Gambar</button>
                                                    <small class="text-muted ms-auto align-self-center">jpg/png, max 2MB</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($product['deskripsi'] ?? '') ?></textarea>
                        </div>

                        <button class="btn btn-primary">Simpan Perubahan</button>
                        <a href="products.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php
    // Safely include scripts
    if (file_exists(__DIR__ . '/partials/scripts.php')) {
        try {
            include __DIR__ . '/partials/scripts.php';
        } catch (Exception $e) {
            echo "<!-- Error loading scripts: " . htmlspecialchars($e->getMessage()) . " -->";
        }
    }
    ?>
    <script>
        (function() {
            const input = document.getElementById('gambarInput');
            const preview = document.getElementById('previewImage');
            const hargaDisplay = document.getElementById('harga_display');
            const hargaHidden = document.getElementById('harga');

            function formatRupiah(value) {
                if (!value) return '0';
                const digits = value.toString().replace(/\D/g, '');
                if (!digits) return '0';
                return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function updateHargaFields() {
                const raw = hargaDisplay.value || '';
                const digits = raw.replace(/\D/g, '');
                hargaHidden.value = digits ? parseFloat(digits) : 0;
                hargaDisplay.value = formatRupiah(digits);
            }

            hargaDisplay && hargaDisplay.addEventListener('input', function(e) {
                const pos = this.selectionStart;
                updateHargaFields();
                this.selectionStart = this.selectionEnd = pos;
            });

            input.addEventListener('change', function() {
                const f = input.files && input.files[0];
                if (f) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'inline-block';
                    };
                    reader.readAsDataURL(f);
                }
            });
        })();

        // Per-slot editable gallery handlers + multi-upload (edit)
        (function() {
            const inputs = document.querySelectorAll('.image-input-edit');
            const selects = document.querySelectorAll('.btn-select-file-edit');
            const clears = document.querySelectorAll('.btn-clear-file');
            const multiBtn = document.getElementById('btnMultiUploadEdit');
            const multiInput = document.getElementById('multiUploadInputEdit');

            function getPreview(i) { return document.querySelector('.img-preview[data-index="' + i + '"]'); }
            function getPlaceholder(i) { return document.querySelector('.placeholder[data-index="' + i + '"]'); }
            function getMeta(i) { return document.querySelector('[data-index-meta="' + i + '"]'); }
            function getHidden(i) { return document.querySelector('input[data-index-hidden="' + i + '"]'); }

            selects.forEach(btn => {
                const idx = btn.dataset.index;
                const input = document.querySelector('.image-input-edit[data-index="' + idx + '"]');
                btn.addEventListener('click', () => input.click());
            });

            document.querySelectorAll('.image-preview-wrapper').forEach(wrapper => {
                const idx = wrapper.getAttribute('data-index-wrapper');
                const input = document.querySelector('.image-input-edit[data-index="' + idx + '"]');
                wrapper.addEventListener('click', () => input.click());
            });

            inputs.forEach(input => {
                const idx = input.dataset.index;
                const preview = getPreview(idx);
                const placeholder = getPlaceholder(idx);
                input.addEventListener('change', (e) => {
                    const f = e.target.files[0];
                    if (!f) return;
                    if (!f.type.startsWith('image/')) { alert('File bukan gambar'); input.value = ''; return; }
                    if (f.size > 5 * 1024 * 1024) { alert('File terlalu besar (>5MB)'); input.value = ''; return; }
                    const url = URL.createObjectURL(f);
                    if (preview) { preview.src = url; preview.style.display = 'block'; }
                    if (placeholder) placeholder.style.display = 'none';
                    const meta = getMeta(idx);
                    if (meta) { meta.style.display = 'block'; meta.querySelector('[data-index-name="' + idx + '"]').textContent = f.name; meta.querySelector('[data-index-size="' + idx + '"]').textContent = Math.round(f.size/1024) + ' KB'; }
                    // clear existing id marker
                    const hidden = getHidden(idx);
                    if (hidden) hidden.value = '';
                });
            });

            clears.forEach(btn => {
                const idx = btn.dataset.index;
                const input = document.querySelector('.image-input-edit[data-index="' + idx + '"]');
                const preview = getPreview(idx);
                const placeholder = getPlaceholder(idx);
                btn.addEventListener('click', () => {
                    // clear file input
                    if (input) input.value = '';
                    if (preview) { preview.src = ''; preview.style.display = 'none'; }
                    if (placeholder) placeholder.style.display = 'block';
                    const meta = getMeta(idx); if (meta) meta.style.display = 'none';
                    const hidden = getHidden(idx); if (hidden) hidden.value = '';
                });
            });

            if (multiBtn && multiInput) {
                multiBtn.addEventListener('click', () => multiInput.click());
                multiInput.addEventListener('change', (e) => {
                    const files = Array.from(e.target.files || []);
                    files.slice(0,5).forEach((file, i) => {
                        const slotInput = document.querySelector('.image-input-edit[data-index="' + i + '"]');
                        if (!slotInput) return;
                        const dt = new DataTransfer(); dt.items.add(file); slotInput.files = dt.files;
                        slotInput.dispatchEvent(new Event('change'));
                    });
                    multiInput.value = '';
                });
            }
        })();

        // Remove image (existing)
        function removeImage(imageId, productId) {
            if (!confirm('Hapus gambar ini?')) return;
            window.location.href = 'product_image_action.php?action=delete&id=' + imageId + '&product_id=' + productId;
        }
    </script>
</body>

</html>