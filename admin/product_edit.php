<?php
require __DIR__ . '/config.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;
if ($id) {
    $q = mysqli_prepare($conn, "SELECT * FROM produk WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($q, 'i', $id);
    mysqli_stmt_execute($q);
    $res = mysqli_stmt_get_result($q);
    $product = mysqli_fetch_assoc($res);
}

$title = $id ? 'Edit Produk - Admin' : 'Tambah Produk - Admin';
include __DIR__ . '/partials/header.php';
?>

<body class="bg-light">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <?php include __DIR__ . '/partials/topbar.php'; ?>

    <main class="admin-main">
        <div class="container-fluid py-4" style="max-width:900px;">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><?= $id ? 'Edit Produk' : 'Tambah Produk' ?></h5>
                </div>
                <div class="card-body">
                    <form action="<?= $id ? 'product_update.php' : 'product_store.php' ?>" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>

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
                            <div class="form-text">Pilih gambar baru untuk mengganti, atau gunakan tombol Hapus BG untuk memproses gambar saat ini.</div>
                            <div class="mt-3 text-center">
                                <?php if (!empty($product['gambar'])): ?>
                                    <img id="previewImage" src="<?= htmlspecialchars(
                                                                    (strpos($product['gambar'], 'http') === 0) ? $product['gambar'] : ('../uploads/products/' . $product['gambar'])
                                                                ) ?>" alt="Preview" style="max-width:100%; max-height:360px; display:inline-block; border:1px solid #eee; padding:6px; background:#fff;">
                                <?php else: ?>
                                    <img id="previewImage" src="" alt="Preview" style="max-width:100%; max-height:360px; display:none; border:1px solid #eee; padding:6px; background:#fff;">
                                <?php endif; ?>
                                <div class="mt-2">
                                    <button id="btnHapusBg" type="button" class="btn btn-sm btn-outline-primary">Hapus BG</button>
                                    <span id="aiStatus" class="small text-muted ms-2"></span>
                                </div>
                            </div>
                            <input type="hidden" name="removebg" id="removebg_hidden" value="0">
                            <input type="hidden" name="preview_ai_data" id="preview_ai_data" value="">
                            <input type="hidden" name="existing_gambar" value="<?= htmlspecialchars($product['gambar'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($product['deskripsi'] ?? '') ?></textarea>
                        </div>
                        <button class="btn btn-primary">Simpan</button>
                        <a href="products.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/scripts.php'; ?>
    <script>
        (function() {
            const input = document.getElementById('gambarInput');
            const preview = document.getElementById('previewImage');
            const btnHapus = document.getElementById('btnHapusBg');
            const removeHidden = document.getElementById('removebg_hidden');
            const previewField = document.getElementById('preview_ai_data');
            const aiStatus = document.getElementById('aiStatus');
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

            function showFilePreview(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
            }

            input.addEventListener('change', function() {
                const f = input.files && input.files[0];
                if (previewField) previewField.value = '';
                if (removeHidden) removeHidden.value = '0';
                aiStatus.textContent = '';
                if (f) {
                    showFilePreview(f);
                }
            });

            btnHapus && btnHapus.addEventListener('click', function() {
                // if a file input has selection, preview that file processed; otherwise try to process existing image by fetching file URL
                const f = input.files && input.files[0];
                aiStatus.textContent = 'Memproses...';
                btnHapus.disabled = true;

                const fd = new FormData();
                if (f) fd.append('image', f);
                else {
                    const existing = document.querySelector('input[name="existing_gambar"]').value || '';
                    if (!existing) {
                        btnHapus.disabled = false;
                        aiStatus.textContent = 'Tidak ada gambar untuk diproses';
                        return;
                    }
                    fetch('../uploads/products/' + existing).then(r => r.blob()).then(b => {
                        fd.append('image', b, existing);
                        sendPreview(fd);
                    }).catch(err => {
                        btnHapus.disabled = false;
                        aiStatus.textContent = 'Gagal mengambil gambar';
                    });
                    return;
                }
                sendPreview(fd);

                function sendPreview(fd) {
                    fetch('product_preview_removebg.php', {
                            method: 'POST',
                            body: fd,
                            credentials: 'same-origin'
                        })
                        .then(r => r.json())
                        .then(j => {
                            btnHapus.disabled = false;
                            if (j.ok) {
                                preview.src = j.data;
                                preview.style.display = 'inline-block';
                                aiStatus.textContent = 'Preview siap';
                                if (previewField) previewField.value = j.data;
                                if (removeHidden) removeHidden.value = '1';
                            } else {
                                aiStatus.textContent = 'Error: ' + (j.error || 'unknown');
                            }
                        }).catch(err => {
                            btnHapus.disabled = false;
                            aiStatus.textContent = 'Network error';
                        });
                }
            });
        })();
    </script>
</body>

</html>