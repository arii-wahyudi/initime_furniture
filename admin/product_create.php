<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require __DIR__ . '/config.php';
require_admin();

$title = 'Tambah Produk Baru - Admin';
include __DIR__ . '/partials/header.php';
?>

<body class="bg-light">
    <div id="loadingOverlay" style="display:none;position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(255,255,255,0.7);align-items:center;justify-content:center;">
        <div style="text-align:center;">
            <div class="spinner-border text-primary" style="width:3rem;height:3rem;"></div>
            <div class="mt-3 fw-bold">Sedang memproses...</div>
        </div>
    </div>
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <?php include __DIR__ . '/partials/topbar.php'; ?>

    <main class="admin-main">
        <div class="container-fluid py-4" style="max-width:900px;">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Tambah Produk Baru</h5>
                </div>
                <div class="card-body">
                    <form action="product_store.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate onsubmit="showLoadingOverlay()">
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" name="nama_produk" class="form-control" placeholder="Masukkan nama produk" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="id_kategori" class="form-select">
                                <option value="">-- Pilih Kategori Produk --</option>
                                <?php
                                $res = mysqli_query($conn, "SELECT id, nama_kategori FROM kategori_produk ORDER BY nama_kategori");
                                while ($row = mysqli_fetch_assoc($res)):
                                ?>
                                    <option value="<?= (int)$row['id'] ?>"><?= htmlspecialchars($row['nama_kategori']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Harga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input id="harga_display" type="text" class="form-control" placeholder="0" required>
                            </div>
                            <input type="hidden" name="harga" id="harga" value="0">
                        </div>

                        <!-- Gambar slots: 5 kotak, slot 0 = Utama. Tidak wajib diisi. -->
                        <div class="mb-3">
                            <label class="form-label">Gambar (jpg/png)</label>
                            <div class="row g-2" id="imageSlotsRow">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <div class="col-12 col-md-6">
                                        <div class="card p-2 h-100" style="min-height:200px;">
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

                                                <div class="d-flex align-items-center justify-content-center mb-2" style="flex:1;">
                                                    <img src="" alt="preview" class="img-preview" data-index="<?= $i ?>" style="max-width:100%;max-height:140px;object-fit:contain;display:none;border-radius:6px;background:#f8f9ff;padding:8px;" />
                                                    <div class="placeholder text-center text-muted" data-index="<?= $i ?>" style="width:100%;">
                                                        <i class="fas fa-image" style="font-size:2rem;"></i>
                                                        <div class="mt-1">Klik untuk pilih gambar</div>
                                                    </div>
                                                </div>

                                                <div class="d-flex gap-2 mt-2">
                                                    <input type="file" name="images[]" accept="image/*" class="d-none image-input" data-index="<?= $i ?>">
                                                    <button type="button" class="btn btn-sm btn-primary btn-select-file" data-index="<?= $i ?>">Pilih Gambar</button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-remove-bg" data-index="<?= $i ?>">
                                                        <i class="fas fa-wand-magic-sparkles"></i> Remove BG
                                                    </button>
                                                    <small class="text-muted ms-auto align-self-center">jpg/png, max 2MB</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <small class="form-text text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i> Isi bebas, tidak wajib semua. Klik tiap kotak untuk memilih gambar.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="4" required></textarea>
                        </div>

                        <button class="btn btn-primary">Simpan</button>
                        <a href="products.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/scripts.php'; ?>
    <script>
        (function() {
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
        })();

        // Utility: escape HTML for safe insertion into text nodes
        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        // Per-slot image handlers for 5 slots
        (function() {
            const inputs = document.querySelectorAll('.image-input');
            const selects = document.querySelectorAll('.btn-select-file');
            const clears = document.querySelectorAll('.btn-clear-file');
            const removeBtns = document.querySelectorAll('.btn-remove-bg');

            function getPreviewByIndex(i) {
                return document.querySelector('.img-preview[data-index="' + i + '"]');
            }
            function getPlaceholderByIndex(i) {
                return document.querySelector('.placeholder[data-index="' + i + '"]');
            }

            selects.forEach(btn => {
                const idx = btn.dataset.index;
                const input = document.querySelector('.image-input[data-index="' + idx + '"]');
                btn.addEventListener('click', () => input.click());
            });

            inputs.forEach(input => {
                const idx = input.dataset.index;
                const preview = getPreviewByIndex(idx);
                const placeholder = getPlaceholderByIndex(idx);

                input.addEventListener('change', (e) => {
                    const f = e.target.files[0];
                    if (!f) return;
                    if (!f.type.startsWith('image/')) { alert('File bukan gambar'); input.value = ''; return; }
                    if (f.size > 2 * 1024 * 1024) { alert('File terlalu besar (>2MB)'); input.value = ''; return; }
                    const url = URL.createObjectURL(f);
                    preview.src = url;
                    preview.style.display = 'block';
                    if (placeholder) placeholder.style.display = 'none';
                });
            });

            clears.forEach(btn => {
                const idx = btn.dataset.index;
                const input = document.querySelector('.image-input[data-index="' + idx + '"]');
                const preview = getPreviewByIndex(idx);
                const placeholder = getPlaceholderByIndex(idx);
                btn.addEventListener('click', () => {
                    input.value = '';
                    if (preview) { preview.src = ''; preview.style.display = 'none'; }
                    if (placeholder) placeholder.style.display = 'block';
                });
            });

            removeBtns.forEach(btn => {
                const idx = btn.dataset.index;
                const input = document.querySelector('.image-input[data-index="' + idx + '"]');
                const preview = getPreviewByIndex(idx);
                btn.addEventListener('click', () => {
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(ev) {
                            const base64 = ev.target.result;
                            removeBGNewImage(btn, base64);
                        };
                        reader.readAsDataURL(input.files[0]);
                    } else if (preview && preview.src) {
                        // fetch blob then convert
                        fetch(preview.src).then(r => r.blob()).then(blob => {
                            const r2 = new FileReader();
                            r2.onload = e => removeBGNewImage(btn, e.target.result);
                            r2.readAsDataURL(blob);
                        }).catch(() => alert('Tidak dapat membaca gambar untuk diproses'));
                    } else {
                        alert('Pilih gambar dulu sebelum Remove BG.');
                    }
                });
            });
        })();

        // Remove BG for new image
        function removeBGNewImage(btn, base64Img) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;

            const formData = new FormData();
            formData.append('image', base64Img);

            fetch('product_preview_removebg.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(r => r.json())
            .then(data => {
                if (data.ok) {
                    btn.closest('.card').querySelector('img').src = data.data;
                    btn.innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(() => {
                        btn.innerHTML = '<i class="fas fa-wand-magic-sparkles"></i> Remove BG';
                    }, 1500);
                } else {
                    alert('Error: ' + (data.error || 'Unknown'));
                }
                btn.disabled = false;
            })
            .catch(err => {
                alert('Error: ' + err.message);
                btn.disabled = false;
            });
        }

        function showLoadingOverlay() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }
        function hideLoadingOverlay() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }
    </script>
</body>

</html>
