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
                            <input id="gambarInput" type="file" name="gambar" class="form-control" accept="image/*" required>
                            <div class="form-text">Upload gambar untuk produk baru</div>
                            <div class="mt-3 text-center">
                                <img id="previewImage" src="" alt="Preview" style="max-width:100%; max-height:360px; display:none; border:1px solid #eee; padding:6px; background:#fff;">
                            </div>
                            <input type="hidden" name="removebg" id="removebg_hidden" value="0">
                            <input type="hidden" name="preview_ai_data" id="preview_ai_data" value="">
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
    </script>
</body>

</html>
