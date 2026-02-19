<?php
$title = "Produk Kami - Intime Furniture";
require __DIR__ . '/admin/config.php';

// Load settings and categories
$settings = [];
$res = mysqli_query($conn, "SELECT nama_setting, isi FROM settings");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) $settings[$r['nama_setting']] = $r['isi'];
    mysqli_free_result($res);
}

$categories = [];
$res = mysqli_query($conn, "SELECT id, nama_kategori FROM kategori_produk ORDER BY id");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) $categories[] = $r;
    mysqli_free_result($res);
}

// Load products
// handle filters: search query and category
$q = trim((string)($_GET['q'] ?? ''));
$cat = (int)($_GET['cat'] ?? 0);

$products = [];
$sql = "SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori_produk k ON p.id_kategori = k.id WHERE p.status = 'aktif'";
if ($cat) {
    $sql .= " AND p.id_kategori = " . $cat;
}
if ($q !== '') {
    $qe = mysqli_real_escape_string($conn, $q);
    $sql .= " AND (p.nama_produk LIKE '%$qe%' OR p.deskripsi LIKE '%$qe%')";
}
$sql .= " ORDER BY p.created_at DESC";
$res = mysqli_query($conn, $sql);
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) $products[] = $r;
    mysqli_free_result($res);
}

// Log searches to statistik (server-side) when query present
if ($q !== '') {
    $stmt = mysqli_prepare($conn, "INSERT INTO produk_statistik (tipe_event) VALUES (?)");
    if ($stmt) {
        $ev = 'search';
        mysqli_stmt_bind_param($stmt, 's', $ev);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

include 'partials/header.php';
?>

<body>
    <!-- NAVBAR SECTION -->
    <?php include 'partials/navbar.php'; ?>

    <!-- CATEGORY SECTION START -->
    <div class="container-lg mt-4">
        <div class="row g-2" data-aos="fade-up">
            <?php
            $default_cat_images = [
                'assets/img/cat1-ruangtamu.png',
                'assets/img/cat2-ruangmakan.jpg',
                'assets/img/cat3-ruangrapat.png'
            ];

            if (!empty($categories)):
                foreach ($categories as $i => $cat):
                    $img = $settings['about_image'] ?? $default_cat_images[$i % count($default_cat_images)];
            ?>
                    <div class="col-6 col-md-4">
                        <a href="product.php?cat=<?= (int)$cat['id'] ?>" class="text-decoration-none text-dark category-link" data-cat-id="<?= (int)$cat['id'] ?>">
                            <div class="card card-category shadow bg-card-category">
                                <img src="<?= htmlspecialchars($img) ?>" class="card-img object-fit-cover opacity-50" alt="<?= htmlspecialchars($cat['nama_kategori']) ?>" />
                                <div class="card-img-overlay d-flex justify-content-center align-items-center p-4">
                                    <h5 class="card-title m-0 text-center"><?= htmlspecialchars($cat['nama_kategori']) ?></h5>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php
                endforeach;
            else:
                // fallback: show a placeholder
                ?>
                <div class="col-12">
                    <p class="text-center text-muted">Belum ada kategori.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- CATEGORY SECTION END -->


    <!-- PRODUCT SECTION START -->
    <div class="container-lg mb-5" id="product">
        <div class="d-flex align-items-center justify-content-center pt-5">
            <h5 class="text-center fw-bold mb-4 fs-md-5 bg-title py-2 px-3 rounded-pill">
                Produk Kami
            </h5>
        </div>

        <div class="container-lg mt-3">
            <div class="row justify-content-center" data-aos="fade-up">
                <div class="col-lg-8 col-6 g-1">
                    <form method="get" action="product.php">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input
                                type="text"
                                name="q"
                                value="<?= htmlspecialchars($q) ?>"
                                class="form-control border-start-0"
                                placeholder="Cari produk..." />
                        </div>
                    </form>
                </div>
                <div class="col-lg-4 col-6 g-1">
                    <div class="dropdown w-100">
                        <a class="btn btn-light shadow w-100 d-flex justify-content-between" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                            <span><i class="fas fa-sliders-h me-2"></i>Kategori</span>
                            <small><i class="fas fa-chevron-down"></i></small>
                        </a>

                        <ul class="dropdown-menu w-100 dropdown-menu-custom mt-2">
                            <li><a class="dropdown-item" href="product.php">Semua Kategori</a></li>
                            <?php foreach ($categories as $c): ?>
                                <li><a class="dropdown-item category-link" data-cat-id="<?= (int)$c['id'] ?>" href="product.php?cat=<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['nama_kategori']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3 px-1 px-lg-0" data-aos="fade-up">
            <?php if (empty($products)) : ?>
                <div class="col-12">
                    <p class="text-center">Belum ada produk.</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $p):
                    $img = public_image_url($p['gambar'] ?? '');
                    $price = isset($p['harga']) ? number_format((float)$p['harga'], 0, ',', '.') : '-';
                    $category = isset($p['nama_kategori']) ? htmlspecialchars($p['nama_kategori']) : '';
                    $slug = htmlspecialchars($p['slug'] ?? '');
                    $pid = (int)$p['id'];
                    $wa_number = isset($settings['whatsapp']) ? preg_replace('/[^0-9]/', '', $settings['whatsapp']) : '628123456789';
                    $wa_msg = rawurlencode("Saya mau beli produk {$p['nama_produk']} - apakah masih tersedia? Mohon info harga dan estimasi kirim.");
                ?>
                    <div class="col-6 col-lg-2 g-3">
                        <div class="card shadow h-100">
                            <div class="card-body m-0 p-0">
                                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>" class="w-100" />
                            </div>
                            <div class="card-footer py-3">
                                <h4><?= htmlspecialchars($p['nama_produk']) ?></h4>
                                <span class="badge text-bg-secondary"><?= $category ?></span>
                                <p class="my-2">Rp <?= $price ?></p>

                                <div class="d-grid">
                                    <a href="product_detail.php?slug=<?= urlencode($slug) ?>" class="btn btn-outline-secondary text-dark btn-view-detail" data-product-id="<?= $pid ?>">Lihat Detail</a>
                                    <a href="https://wa.me/<?= $wa_number ?>?text=<?= $wa_msg ?>" class="btn btn-success mt-2 wa-link" data-product-id="<?= $pid ?>">Pesan via WhatsApp</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <!-- Modal Start -->
    <!-- Product details moved to product_detail.php -->
    <!-- PRODUCT SECTION END -->



    <!-- FOOTER & WA SECTION -->
    <?php include 'partials/footer.php'; ?>


    <!-- SCRIPT -->
    <?php include 'partials/scripts.php'; ?>

</body>

</html>