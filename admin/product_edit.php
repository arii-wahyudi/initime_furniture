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

                        <!-- Multiple Images Section -->
                        <?php if ($id): ?>
                        <hr>
                        <h6 class="mb-3">Kelola Gambar Produk</h6>
                        
                        <!-- Existing Images -->
                        <?php
                        $product_images = get_product_images($id, $conn);
                        if (!empty($product_images)):
                        ?>
                        <div class="mb-4">
                            <label class="form-label">Gambar yang Sudah Ada</label>
                            <div class="row g-3">
                                <?php foreach ($product_images as $img_item): ?>
                                <div class="col-6 col-md-4">
                                    <div class="card">
                                        <div class="ratio ratio-1x1">
                                            <img src="<?= htmlspecialchars(public_image_url($img_item['gambar'] ?? '')) ?>" 
                                                 class="object-fit-cover" 
                                                 alt="Product image">
                                        </div>
                        <div class="card-body p-2">
                                            <?php if ($img_item['urutan'] == 0): ?>
                                            <span class="badge bg-primary mb-2">Gambar Utama</span>
                                            <?php endif; ?>
                                            <div class="btn-group d-flex gap-1" role="group" style="font-size:0.85rem;">
                                                <?php if ($img_item['urutan'] != 0): ?>
                                                <a href="product_image_action.php?action=set_primary&id=<?= (int)$img_item['id'] ?>&product_id=<?= (int)$id ?>" 
                                                   class="btn btn-sm btn-outline-info flex-fill">Set Utama</a>
                                                <?php endif; ?>
                                                <a href="product_image_action.php?action=delete&id=<?= (int)$img_item['id'] ?>&product_id=<?= (int)$id ?>" 
                                                   class="btn btn-sm btn-outline-danger flex-fill"
                                                   onclick="return confirm('Hapus gambar ini?')">Hapus</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Upload Images - Grid Style Shopee Seller -->
                        <div class="mb-4">
                            <label class="form-label">Gambar</label>
                            
                            <div class="row g-2" id="imageGrid">
                                <!-- Existing images (if edit) -->
                                <?php if ($id): ?>
                                    <?php
                                    $product_images = get_product_images($id, $conn);
                                    foreach ($product_images as $img_item):
                                    ?>
                                    <div class="col-6 col-md-3" data-image-id="<?= (int)$img_item['id'] ?>">
                                        <div class="card position-relative h-100" style="overflow:hidden;">
                                            <div class="ratio ratio-1x1">
                                                <img src="<?= htmlspecialchars(public_image_url($img_item['gambar'] ?? '')) ?>" 
                                                     class="object-fit-cover image-preview" 
                                                     alt="Product image">
                                            </div>
                                            <div class="position-absolute top-0 end-0 p-2" style="z-index:10;">
                                                <button type="button" class="btn btn-sm btn-danger btn-remove" 
                                                        onclick="removeImage(<?= (int)$img_item['id'] ?>, <?= (int)$id ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <div class="position-absolute bottom-0 start-0 end-0 p-2" style="background:rgba(0,0,0,0.4);z-index:9;">
                                                <button type="button" class="btn btn-sm btn-light w-100 btn-removebg"
                                                        data-img="<?= htmlspecialchars($img_item['gambar']) ?>"
                                                        onclick="removeBGImage(this)">
                                                    <i class="fas fa-wand-magic-sparkles"></i> Remove BG
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

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

        // Multiple Images Upload Handler - Grid Style (Shopee Seller)
        (function() {
            const imageGrid = document.getElementById('imageGrid');
            const addImageBtn = document.getElementById('addImageBtn');
            const fileInput = document.getElementById('additionalImagesInput');
            let selectedFiles = [];

            // Click button to open file dialog
            addImageBtn.addEventListener('click', () => {
                fileInput.click();
            });

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

        // Remove BG for existing image
        function removeBGImage(btn) {
            const imgPath = btn.getAttribute('data-img');
            if (!imgPath || !confirm('Remove background dari gambar ini?')) return;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;

            const formData = new FormData();
            formData.append('image_path', imgPath);

            fetch('product_remove_bg_existing.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(r => r.json())
            .then(data => {
                if (data.ok) {
                    btn.closest('.card').querySelector('img').src = data.url;
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
                alert('Network error');
                btn.disabled = false;
            });
        }

        // Remove BG for new image (preview)
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

        // Remove image (existing)
        function removeImage(imageId, productId) {
            if (!confirm('Hapus gambar ini?')) return;
            window.location.href = 'product_image_action.php?action=delete&id=' + imageId + '&product_id=' + productId;
        }
    </script>
    </script>
</body>

</html>