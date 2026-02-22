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

        // Multiple Images Upload Handler
        (function() {
            const imageGrid = document.getElementById('imageGrid');
            const addImageBtn = document.getElementById('addImageBtn');
            const fileInput = document.getElementById('additionalImagesInput');
            // selectedFiles stores objects: { id: string, file: File, url: string }
            let selectedFiles = [];

            // Click button to open file dialog
                addImageBtn.addEventListener('click', () => {
                    fileInput.click();
                });

            function handleFiles(files) {
                console.log('handleFiles called, files:', files);
                const arr = Array.from(files);
                const maxImages = 10;
                if (selectedFiles.length + arr.length > maxImages) {
                    alert('Maksimal upload 10 gambar!');
                    return;
                }
                arr.forEach(file => {
                    if (!file) return;
                    if (!file.type.startsWith('image/')) {
                        alert('File bukan gambar: ' + file.name);
                        return;
                    }
                    if (file.size > 2 * 1024 * 1024) {
                        alert('File terlalu besar (> 2MB): ' + file.name);
                        return;
                    }
                    const id = Date.now().toString(36) + Math.random().toString(36).slice(2,8);
                    const url = URL.createObjectURL(file);
                    selectedFiles.push({id, file, url});
                    renderImageCard({id, file, url});
                });
                updateFileInput();
            }

            // Drag over grid
            imageGrid.addEventListener('dragover', (e) => {
                e.preventDefault();
                imageGrid.style.backgroundColor = '#e7f1ff';
            });

            imageGrid.addEventListener('dragleave', () => {
                imageGrid.style.backgroundColor = '';
            });

            // Drop files
            imageGrid.addEventListener('drop', (e) => {
                e.preventDefault();
                imageGrid.style.backgroundColor = '';
                handleFiles(e.dataTransfer.files);
            });

            // File input change
            fileInput.addEventListener('change', (e) => {
                handleFiles(e.target.files);
            });

            function renderImageCard(obj) {
                console.log('renderImageCard for', obj.id, obj.file.name);
                // obj: { id, file, url }
                const cardCol = document.createElement('div');
                cardCol.className = 'col-6 col-md-3';
                // ensure column has visible height if CSS missing
                cardCol.style.minHeight = '140px';
                cardCol.dataset.id = obj.id;
                cardCol.innerHTML = `
                    <div class="card position-relative h-100" style="overflow:hidden;">
                        <div class="ratio ratio-1x1">
                            <img src="${obj.url}" alt="${obj.file.name}" style="width:100%;height:100%;object-fit:cover;display:block;">
                        </div>
                        <div class="position-absolute top-0 end-0 p-2" style="z-index:10;">
                            <button type="button" class="btn btn-sm btn-danger btn-remove-image">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="position-absolute bottom-0 start-0 end-0 p-2" style="background:rgba(0,0,0,0.4);z-index:9;">
                            <button type="button" class="btn btn-sm btn-light w-100 btn-removebg" data-id="${obj.id}">
                                <i class="fas fa-wand-magic-sparkles"></i> Remove BG
                            </button>
                        </div>
                    </div>
                `;
                // Insert before add button
                imageGrid.insertBefore(cardCol, addImageBtn.parentElement);
                console.log('inserted card for', obj.id, 'into imageGrid children:', imageGrid.children.length);

                // Remove handler
                cardCol.querySelector('.btn-remove-image').addEventListener('click', function() {
                    // revoke object URL
                    URL.revokeObjectURL(obj.url);
                    // remove from DOM
                    cardCol.remove();
                    // remove from selectedFiles
                    selectedFiles = selectedFiles.filter(item => item.id !== obj.id);
                    updateFileInput();
                });

                // Remove BG handler (sends file to preview remove bg)
                cardCol.querySelector('.btn-removebg').addEventListener('click', function() {
                    const btn = this;
                    const item = selectedFiles.find(i => i.id === obj.id);
                    if (!item) return;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; btn.disabled = true;
                    const fd = new FormData();
                    fd.append('image', item.file);
                    fetch('product_preview_removebg.php', { method: 'POST', body: fd, credentials: 'same-origin' })
                        .then(r => r.json())
                        .then(j => {
                            btn.disabled = false;
                            if (j.ok) {
                                // update image src with returned data
                                cardCol.querySelector('img').src = j.data;
                            } else {
                                alert('Error: ' + (j.error || 'unknown'));
                            }
                            btn.innerHTML = '<i class="fas fa-wand-magic-sparkles"></i> Remove BG';
                        }).catch(err => {
                            btn.disabled = false; btn.innerHTML = '<i class="fas fa-wand-magic-sparkles"></i> Remove BG';
                            alert('Network error: ' + err.message);
                        });
                });
            }

            function updateFileInput() {
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(item => {
                    dataTransfer.items.add(item.file);
                });
                fileInput.files = dataTransfer.files;
                console.log('updateFileInput set', fileInput.files.length, fileInput.files);
            }

            // Keep add button always at end
            const observer = new MutationObserver(() => {
                if (addImageBtn && addImageBtn.parentElement && imageGrid.contains(addImageBtn.parentElement)) {
                    imageGrid.appendChild(addImageBtn.parentElement);
                }
            });
            observer.observe(imageGrid, { childList: true });
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
