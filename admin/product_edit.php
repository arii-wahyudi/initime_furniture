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

                        <!-- Multi Images Upload Grid -->
                        <div class="mb-3">
                            <label class="form-label">Galeri Gambar Produk</label>
                            <div class="row g-2" id="imageGrid">
                                <!-- Existing images (if edit) -->
                                <?php
                                $product_images = [];
                                try {
                                    if (function_exists('get_product_images')) {
                                        $product_images = get_product_images($id, $conn);
                                    }
                                } catch (Exception $e) {
                                    // Silently fail
                                }

                                foreach ($product_images as $img_item):
                                ?>
                                    <div class="col-6 col-md-3" data-image-id="<?= (int)$img_item['id'] ?>">
                                        <div class="card position-relative h-100" style="overflow:hidden;">
                                            <div class="ratio ratio-1x1">
                                                <img src="<?= htmlspecialchars(
                                                                (function_exists('public_image_url')) ? public_image_url($img_item['gambar'] ?? '') : ('../uploads/products/' . ($img_item['gambar'] ?? ''))
                                                            ) ?>"
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

        // Multiple Images Upload Handler - Simplified & Robust
        (function() {
            const imageGrid = document.getElementById('imageGrid');
            const addImageBtn = document.getElementById('addImageBtn');
            const fileInput = document.getElementById('additionalImagesInput');
            const form = document.getElementById('productForm');
            
            const MAX_IMAGES = 10;
            const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
            const selectedFiles = new Map(); // id => File

            if (!addImageBtn) return; // Skip if grid doesn't exist

            // Click button to open file dialog
            addImageBtn.addEventListener('click', () => {
                fileInput.click();
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

            // File input change
            fileInput.addEventListener('change', (e) => {
                handleFiles(e.target.files);
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

                    const id = 'new_img_' + Date.now() + '_' + validCount;
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
                            <button type="button" class="btn btn-sm btn-danger" 
                                    onclick="removeNewImageCard('${id}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;

                imageGrid.insertBefore(cardCol, addImageBtn.parentElement);
            }

            window.removeNewImageCard = function(id) {
                selectedFiles.delete(id);
                document.querySelector(`[data-id="${id}"]`)?.remove();
            };

            // Form submit handler
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Build FormData from form
                const formData = new FormData(form);
                
                // Clear existing file input in formdata
                formData.delete('additional_images[]');
                
                // Add new selected files
                selectedFiles.forEach((file) => {
                    formData.append('additional_images[]', file);
                });

                // Submit with proper error handling
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
                    } else {
                        // Success - redirect
                        window.location.href = 'products.php';
                    }
                })
                .catch(err => {
                    alert('❌ Network error: ' + err.message);
                    console.error(err);
                });
            });

            // Keep add button always at end
            const observer = new MutationObserver(() => {
                if (addImageBtn && addImageBtn.parentElement && imageGrid.contains(addImageBtn.parentElement)) {
                    imageGrid.appendChild(addImageBtn.parentElement);
                }
            });
            observer.observe(imageGrid, {
                childList: true
            });
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

        // Remove image (existing)
        function removeImage(imageId, productId) {
            if (!confirm('Hapus gambar ini?')) return;
            window.location.href = 'product_image_action.php?action=delete&id=' + imageId + '&product_id=' + productId;
        }
    </script>
</body>

</html>