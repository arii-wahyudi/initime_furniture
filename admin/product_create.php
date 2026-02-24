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

                        <!-- main image removed; using additional images grid instead -->

                        <div class="mb-3">
                            <label class="form-label">Gambar (jpg/png)</label>
                            <div class="row g-3" id="imageGrid">
                                <!-- Five fixed slots -->
                            </div>

                            <small class="form-text text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i> Klik kotak untuk pilih gambar, atau lepas file ke kotak
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

        // Five-slot Image Uploader (fixed slots)
        (function() {
            const imageGrid = document.getElementById('imageGrid');
            if (!imageGrid) return;

            const slotCount = 5;

            for (let i = 0; i < slotCount; i++) {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4';

                col.innerHTML = `
                    <div class="card p-2 text-center" style="min-height:200px;position:relative;">
                        <div class="slot-label text-start mb-1"><strong>${i===0? 'Gambar Utama' : 'Gambar ' + (i+1)}</strong></div>
                        <div class="preview" style="height:120px;display:flex;align-items:center;justify-content:center;background:#f8f9ff;border-radius:6px;overflow:hidden;cursor:pointer;">
                            <img src="" alt="" style="max-width:100%;max-height:100%;display:none;">
                            <div class="placeholder text-muted" style="display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-plus" style="font-size:2rem;color:#0d6efd"></i>
                            </div>
                        </div>
                        <input type="file" name="images[]" accept="image/*" class="d-none slot-input">
                        <div class="mt-2 d-flex justify-content-between" style="gap:6px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-remove-slot">Hapus</button>
                            <button type="button" class="btn btn-sm btn-outline-primary btn-remove-bg"><i class="fas fa-wand-magic-sparkles"></i> Remove BG</button>
                        </div>
                    </div>
                `;

                imageGrid.appendChild(col);

                const input = col.querySelector('.slot-input');
                const preview = col.querySelector('.preview');
                const img = col.querySelector('img');
                const placeholder = col.querySelector('.placeholder');
                const btnRemove = col.querySelector('.btn-remove-slot');
                const btnRemoveBg = col.querySelector('.btn-remove-bg');

                preview.addEventListener('click', () => input.click());

                input.addEventListener('change', (e) => {
                    if (e.target.files && e.target.files[0]) {
                        handleSlotFile(e.target.files[0], col);
                    }
                });

                preview.addEventListener('dragover', (e) => { e.preventDefault(); preview.style.opacity = 0.8; });
                preview.addEventListener('dragleave', () => { preview.style.opacity = ''; });
                preview.addEventListener('drop', (e) => { e.preventDefault(); preview.style.opacity = ''; if (e.dataTransfer.files && e.dataTransfer.files[0]) handleSlotFile(e.dataTransfer.files[0], col); });

                btnRemove.addEventListener('click', () => {
                    input.value = '';
                    img.src = '';
                    img.style.display = 'none';
                    placeholder.style.display = 'flex';
                });

                btnRemoveBg.addEventListener('click', () => {
                    if (!img.src) { alert('Belum ada gambar di kotak ini'); return; }
                    btnRemoveBg.disabled = true;
                    btnRemoveBg.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    if (img.src.startsWith('blob:')) {
                        fetch(img.src).then(r => r.blob()).then(b => fileToBase64(b)).then(base64 => {
                            removeBGNewImage(btnRemoveBg, base64);
                        }).catch(err => { alert('Error: ' + err.message); btnRemoveBg.disabled = false; btnRemoveBg.innerHTML = '<i class="fas fa-wand-magic-sparkles"></i> Remove BG'; });
                    } else if (img.src.startsWith('data:')) {
                        const base64 = img.src.split(',')[1];
                        removeBGNewImage(btnRemoveBg, base64);
                    } else {
                        fetch(img.src).then(r => r.blob()).then(b => fileToBase64(b)).then(base64 => {
                            removeBGNewImage(btnRemoveBg, base64);
                        }).catch(err => { alert('Error: ' + err.message); btnRemoveBg.disabled = false; btnRemoveBg.innerHTML = '<i class="fas fa-wand-magic-sparkles"></i> Remove BG'; });
                    }
                });
            }

            function handleSlotFile(file, col) {
                if (!file) return;
                if (!file.type.startsWith('image/')) { alert('File bukan gambar'); return; }
                if (file.size > 5 * 1024 * 1024) { alert('File terlalu besar (>5MB)'); return; }

                const img = col.querySelector('img');
                const placeholder = col.querySelector('.placeholder');
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    img.style.display = 'block';
                    placeholder.style.display = 'none';
                };
                reader.readAsDataURL(file);

                const dt = new DataTransfer();
                dt.items.add(file);
                col.querySelector('.slot-input').files = dt.files;
            }

            function fileToBase64(blob) {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = () => resolve(reader.result.split(',')[1]);
                    reader.onerror = reject;
                    reader.readAsDataURL(blob);
                });
            }
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
