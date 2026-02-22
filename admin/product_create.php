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
                    <form id="productForm" action="product_store.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>

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
                            <div class="row g-2" id="imageGrid">
                                <!-- Add More Button -->
                                <div class="col-6 col-md-3">
                                    <div class="card h-100 d-flex align-items-center justify-content-center"
                                        id="addImageBtn"
                                        style="cursor:pointer;border:2px dashed #0d6efd;min-height:200px;background:#f8f9ff;transition:all 0.3s ease;">
                                        <div class="text-center">
                                            <i class="fas fa-plus" style="font-size:2.5rem;color:#0d6efd;"></i>
                                            <p class="mt-2 mb-0"><small class="fw-500">Tambah Gambar</small></p>
                                        </div>
                                        <input type="file" id="additionalImagesInput" name="additional_images[]"
                                            class="d-none" accept="image/*" multiple>
                                    </div>
                                </div>
                            </div>

                            <small class="form-text text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i> Klik + untuk tambah gambar, atau drag ke sini
                            </small>
                            <input type="hidden" name="additional_images[]" value="">
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

        // Multiple Images Upload Handler - Simplified & Robust
        (function() {
            const imageGrid = document.getElementById('imageGrid');
            const addImageBtn = document.getElementById('addImageBtn');
            const fileInput = document.getElementById('additionalImagesInput');
            const form = document.getElementById('productForm');
            const loadingOverlay = document.getElementById('loadingOverlay');

            const MAX_IMAGES = 10;
            const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
            const selectedFiles = new Map(); // id => File

            // Click button to open file dialog
            addImageBtn.addEventListener('click', () => {
                fileInput.click();
            });

            // File input change
            fileInput.addEventListener('change', (e) => {
                handleFiles(e.target.files);
            });

            // Drag and drop
            imageGrid.addEventListener('dragover', (e) => {
                e.preventDefault();
                imageGrid.style.backgroundColor = '#e7f1ff';
            });

            imageGrid.addEventListener('dragleave', () => {
                imageGrid.style.backgroundColor = '';
            });

            imageGrid.addEventListener('drop', (e) => {
                e.preventDefault();
                imageGrid.style.backgroundColor = '';
                handleFiles(e.dataTransfer.files);
            });

            function handleFiles(files) {
                const arr = Array.from(files);

                // Validate total count
                if (selectedFiles.size + arr.length > MAX_IMAGES) {
                    alert(`Maksimal ${MAX_IMAGES} gambar! Saat ini: ${selectedFiles.size}`);
                    return;
                }

                let validCount = 0;
                arr.forEach(file => {
                    if (!file.type.startsWith('image/')) {
                        alert(`❌ ${file.name}: Bukan file gambar`);
                        return;
                    }
                    if (file.size > MAX_FILE_SIZE) {
                        alert(`❌ ${file.name}: Terlalu besar (> 5MB)`);
                        return;
                    }

                    const id = 'img_' + Date.now() + '_' + validCount;
                    selectedFiles.set(id, file);
                    renderImageCard(id, file);
                    validCount++;
                });
            }

            function renderImageCard(id, file) {
                const cardCol = document.createElement('div');
                cardCol.className = 'col-6 col-md-3';
                cardCol.dataset.id = id;

                const sizeKb = Math.round(file.size / 1024);
                const preview = file.type === 'image/jpeg' || file.type === 'image/png' ?
                    '📷' : '🖼️';

                cardCol.innerHTML = `
                    <div class="card h-100 position-relative" style="overflow:hidden;">
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height:150px;">
                            <div class="text-center">
                                <div style="font-size:40px;">${preview}</div>
                                <small class="text-muted d-block text-truncate px-2">${escapeHtml(file.name)}</small>
                                <small class="text-muted">${sizeKb}KB</small>
                            </div>
                        </div>
                        <div class="position-absolute top-0 end-0">
                            <button type="button" class="btn btn-sm btn-danger btn-remove-image" 
                                    onclick="removeImageCard('${id}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;

                imageGrid.insertBefore(cardCol, addImageBtn.parentElement);
            }

            window.removeImageCard = function(id) {
                selectedFiles.delete(id);
                document.querySelector(`[data-id="${id}"]`)?.remove();
            };

            // Form submit handler
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (selectedFiles.size === 0) {
                    alert('Pilih minimal 1 gambar');
                    return;
                }

                // Build FormData dari form
                const formData = new FormData(form);

                // Clear existing file input
                formData.delete('additional_images[]');

                // Add selected files properly
                selectedFiles.forEach((file) => {
                    formData.append('additional_images[]', file);
                });

                // Show loading
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'flex';
                }

                // Submit form
                fetch(form.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Server error: ' + response.status);
                        return response.text();
                    })
                    .then(html => {
                        // Check for error messages
                        if (html.includes('Gagal') || html.includes('Error') || html.includes('error')) {
                            alert('⚠️ Terjadi kesalahan. Cek console untuk detail.');
                            console.error('Server response:', html.substring(0, 500));
                            if (loadingOverlay) loadingOverlay.style.display = 'none';
                        } else {
                            // Success - redirect
                            window.location.href = 'products.php';
                        }
                    })
                    .catch(err => {
                        alert('❌ Network error: ' + err.message);
                        console.error(err);
                        if (loadingOverlay) loadingOverlay.style.display = 'none';
                    });
            });
        })();

        // Remove BG for new image (if needed)
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
    </script>
</body>

</html>