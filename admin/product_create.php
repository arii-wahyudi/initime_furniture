<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require __DIR__ . '/config.php';
require_admin();

$title = 'Tambah Produk Baru - Admin';
include __DIR__ . '/partials/header.php';
?>

<body class="bg-light">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <?php include __DIR__ . '/partials/topbar.php'; ?>

    <main class="admin-main">
        <div class="container-fluid py-4" style="max-width:900px;">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Tambah Produk Baru</h5>
                </div>
                <div class="card-body">
                    <form action="product_store.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                        
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
            let selectedFiles = [];

            // Click button to open file dialog
                addImageBtn.addEventListener('click', () => {
                    fileInput.click();
                });

                function handleFiles(files) {
                    const arr = Array.from(files);
                    const maxImages = 10;
                    if (selectedFiles.length + arr.length > maxImages) {
                        alert('Maksimal upload 10 gambar!');
                        return;
                    }
                    arr.forEach(file => {
                        if (file.type.startsWith('image/') && file.size <= 5 * 1024 * 1024) {
                            selectedFiles.push(file);
                            renderImageCard(file);
                        } else if (!file.type.startsWith('image/')) {
                            alert('File bukan gambar: ' + file.name);
                        } else {
                            alert('File terlalu besar (> 5MB): ' + file.name);
                        }
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

            function handleFiles(files) {
                const arr = Array.from(files);
                
                arr.forEach(file => {
                    if (file.type.startsWith('image/') && file.size <= 5 * 1024 * 1024) {
                        selectedFiles.push(file);
                        renderImageCard(file);
                    } else if (!file.type.startsWith('image/')) {
                        alert('File bukan gambar: ' + file.name);
                    } else {
                        alert('File terlalu besar (> 5MB): ' + file.name);
                    }
                });

                updateFileInput();
            }

            function renderImageCard(file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const cardCol = document.createElement('div');
                    cardCol.className = 'col-6 col-md-3';
                    cardCol.innerHTML = `
                        <div class="card position-relative h-100" style="overflow:hidden;">
                            <div class="ratio ratio-1x1">
                                <img src="${e.target.result}" class="object-fit-cover image-preview" alt="${file.name}">
                            </div>
                            <div class="position-absolute top-0 end-0 p-2" style="z-index:10;">
                                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.col-6').remove()">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="position-absolute bottom-0 start-0 end-0 p-2" style="background:rgba(0,0,0,0.4);z-index:9;">
                                <button type="button" class="btn btn-sm btn-light w-100 btn-removebg" 
                                        onclick="removeBGNewImage(this, '${e.target.result}')">
                                    <i class="fas fa-wand-magic-sparkles"></i> Remove BG
                                </button>
                            </div>
                        </div>
                    `;
                    
                    // Insert before add button
                    imageGrid.insertBefore(cardCol, addImageBtn.parentElement);
                };
                reader.readAsDataURL(file);
            }

            function updateFileInput() {
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => {
                    dataTransfer.items.add(file);
                });
                fileInput.files = dataTransfer.files;
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
    </script>
</body>

</html>
