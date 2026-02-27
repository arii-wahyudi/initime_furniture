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
                            <div class="mb-2">
                                <small class="text-muted">Pilih gambar per slot — klik kotak untuk memilih gambar.</small>
                            </div>

                            <div class="row g-2" id="imageSlotsRow">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <div class="col-4 col-sm-4 col-md-3 col-lg-2">
                                        <div class="card p-2 h-100 image-slot-card" style="min-height:0;border:0;background:transparent;">
                                            <div class="d-flex flex-column h-100">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <?php if ($i === 0): ?>
                                                            <span class="badge bg-primary px-1 me-1">Slot Utama</span>
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
                                                    <img src="" alt="preview" class="img-preview" data-index="<?= $i ?>" />
                                                    <div class="placeholder text-center text-muted" data-index="<?= $i ?>">
                                                        <i class="fas fa-image" style="font-size:1.6rem;"></i>
                                                        <div class="mt-1 small">Klik untuk pilih gambar</div>
                                                    </div>
                                                </div>

                                                <div class="file-meta small text-muted mb-2" data-index-meta="<?= $i ?>" style="display:none;background:#f6fbf8;padding:6px;border-radius:6px;border:1px solid #eef6ef;">
                                                    <div class="file-name" data-index-name="<?= $i ?>"></div>
                                                    <div class="file-size text-muted" data-index-size="<?= $i ?>" style="font-size:0.75rem;"></div>
                                                </div>

                                                <div class="d-flex mt-2 align-items-center slot-actions">
                                                    <input type="file" name="images[]" accept="image/*" class="d-none image-input" data-index="<?= $i ?>">
                                                    <div class="mt-2">
                                                        <button type="button" class="btn btn-sm btn-primary btn-select-file" data-index="<?= $i ?>">Pilih Gambar</button>
                                                        <!-- Remove BG button removed to simplify upload flow -->
                                                        <small class="text-muted ms-auto align-self-center">jpg/png, max 2MB</small>
                                                    </div>
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
    <style>
        /* Image slots - square thumbnails and responsive grid */
        #imageSlotsRow {
            --gap: 1rem;
        }

        .image-slot-card {
            padding: 14px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            border: 1px solid #e8ecf1;
        }

        /* make a strict 5-column layout on wide screens, responsive down to 3/2/1 */
        @media (min-width:1200px) {
            #imageSlotsRow {
                display: flex;
                gap: 1rem;
                flex-wrap: wrap;
            }

            #imageSlotsRow>div {
                flex: 0 0 calc(20% - 1rem);
                max-width: calc(20% - 1rem);
            }
        }

        @media (min-width:992px) and (max-width:1199.98px) {
            #imageSlotsRow {
                display: flex;
                gap: 0.75rem;
                flex-wrap: wrap;
            }

            #imageSlotsRow>div {
                flex: 0 0 calc(25% - 0.75rem);
                max-width: calc(25% - 0.75rem);
            }
        }

        @media (min-width:768px) and (max-width:991.98px) {
            #imageSlotsRow>div {
                flex: 0 0 32%;
                max-width: 32%;
            }
        }

        .image-preview-wrapper {
            position: relative;
            width: 100%;
            padding-top: 100%;
            background: #f5f7fc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .image-preview-wrapper .img-preview {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .image-preview-wrapper .placeholder {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: #7a8597;
        }

        .image-preview-wrapper .placeholder i {
            font-size: 2rem;
            color: #a0afc2;
            margin-bottom: 8px;
        }

        .image-preview-wrapper .placeholder .small {
            font-size: 0.8rem;
            text-align: center;
        }

        .file-meta {
            background: #f0f5fb;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #e0e8f2;
            font-size: 0.8rem;
            margin-bottom: 12px;
        }

        .image-slot-card .btn-select-file {
            font-size: 0.85rem;
            padding: 8px 12px;
            width: 100%;
        }

        .image-slot-card .btn-clear-file {
            opacity: 0.85;
            padding: 6px 10px;
        }

        .image-preview-wrapper {
            cursor: pointer;
            transition: all 0.2s;
        }

        .image-preview-wrapper:hover {
            border-color: #94a3b8;
            background: #f0f4fa;
        }

        @media (max-width:575.98px) {
            .col-4 {
                flex: 0 0 50%;
                max-width: 50%;
            }

            #imageSlotsRow>div {
                margin-bottom: 1rem;
            }
        }

        @media (min-width:576px) and (max-width:767.98px) {
            .col-sm-4 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        /* Mobile behavior: stack action buttons and enlarge tap targets */
        @media (max-width:575.98px) {
            .slot-actions {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }

            .slot-actions .btn-select-file,
            .btn-clear-file {
                width: 100%;
                font-size: 0.9rem;
                padding: 10px 8px;
            }

            .slot-actions small {
                display: block;
                margin-top: 8px;
                text-align: center;
                font-size: 0.75rem;
            }

            .image-slot-card {
                padding: 12px;
            }

            .file-meta {
                margin-bottom: 10px;
            }
        }
    </style>
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

        // Per-slot image handlers for 5 slots (simplified) + multi-upload
        (function() {
            const inputs = document.querySelectorAll('.image-input');
            const selects = document.querySelectorAll('.btn-select-file');
            const clears = document.querySelectorAll('.btn-clear-file');

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

            // allow clicking the preview wrapper to open file dialog
            document.querySelectorAll('.image-preview-wrapper').forEach(wrapper => {
                const idx = wrapper.getAttribute('data-index-wrapper');
                const input = document.querySelector('.image-input[data-index="' + idx + '"]');
                wrapper.addEventListener('click', () => input.click());
            });

            inputs.forEach(input => {
                const idx = input.dataset.index;
                const preview = getPreviewByIndex(idx);
                const placeholder = getPlaceholderByIndex(idx);

                input.addEventListener('change', (e) => {
                    const f = e.target.files[0];
                    if (!f) return;
                    if (!f.type.startsWith('image/')) {
                        alert('File bukan gambar');
                        input.value = '';
                        return;
                    }
                    if (f.size > 2 * 1024 * 1024) {
                        alert('File terlalu besar (>2MB)');
                        input.value = '';
                        return;
                    }
                    const url = URL.createObjectURL(f);
                    preview.src = url;
                    preview.style.display = 'block';
                    if (placeholder) placeholder.style.display = 'none';
                    // show meta (name + size)
                    const meta = document.querySelector('[data-index-meta="' + idx + '"]');
                    const nameEl = document.querySelector('[data-index-name="' + idx + '"]');
                    const sizeEl = document.querySelector('[data-index-size="' + idx + '"]');
                    if (meta && nameEl && sizeEl) {
                        nameEl.textContent = f.name;
                        sizeEl.textContent = Math.round(f.size / 1024) + ' KB';
                        meta.style.display = 'block';
                    }
                });
            });

            clears.forEach(btn => {
                const idx = btn.dataset.index;
                const input = document.querySelector('.image-input[data-index="' + idx + '"]');
                const preview = getPreviewByIndex(idx);
                const placeholder = getPlaceholderByIndex(idx);
                btn.addEventListener('click', () => {
                    input.value = '';
                    if (preview) {
                        preview.src = '';
                        preview.style.display = 'none';
                    }
                    if (placeholder) placeholder.style.display = 'block';
                    const meta = document.querySelector('[data-index-meta="' + idx + '"]');
                    if (meta) meta.style.display = 'none';
                });
            });

            // Multi-upload removed: select images per slot by clicking a box
        })();

        function showLoadingOverlay() {
            const el = document.getElementById('loadingOverlay');
            if (el) el.style.display = 'flex';
        }

        function hideLoadingOverlay() {
            const el = document.getElementById('loadingOverlay');
            if (el) el.style.display = 'none';
        }
    </script>
</body>

</html>