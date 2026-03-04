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
$res = mysqli_query($conn, "SELECT id, nama_kategori, image FROM kategori_produk ORDER BY id");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) $categories[] = $r;
    mysqli_free_result($res);
}

// determine Whatsapp contact number using toko contact if available
$wa_number = '';
if (!empty($conn)) {
    $kt = [];
    $rk = @mysqli_query($conn, "SELECT telepon FROM kontak_toko ORDER BY id DESC LIMIT 1");
    if ($rk) {
        $kt = mysqli_fetch_assoc($rk) ?: [];
        mysqli_free_result($rk);
    }
    $tel_source = $kt['telepon'] ?? ($settings['whatsapp'] ?? '');
    if (!empty($tel_source)) {
        // normalize: keep digits and plus, convert leading 0 to 62
        $tel = preg_replace('/[^0-9+]/', '', $tel_source);
        if ($tel !== '' && preg_match('/^0/', $tel)) {
            $tel = '62' . preg_replace('/^0+/', '', $tel);
        }
        $wa_number = preg_replace('/[^0-9]/', '', $tel);
    }
}
if ($wa_number === '') {
    // fallback default
    $wa_number = '628123456789';
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

// jika tidak ada produk ditemukan dan ada query, coba cari berdasarkan setiap kata
if ($q !== '' && empty($products)) {
    $terms = preg_split('/\s+/', trim($q));
    $parts = [];
    foreach ($terms as $t) {
        $t = mysqli_real_escape_string($conn, $t);
        if ($t === '') continue;
        $parts[] = "(p.nama_produk LIKE '%$t%' OR p.deskripsi LIKE '%$t%')";
    }
    if (!empty($parts)) {
        $sql2 = "SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori_produk k ON p.id_kategori = k.id WHERE p.status = 'aktif'";
        if ($cat) {
            $sql2 .= " AND p.id_kategori = " . $cat;
        }
        $sql2 .= " AND (" . implode(' OR ', $parts) . ")";
        $sql2 .= " ORDER BY p.created_at DESC";
        $res2 = mysqli_query($conn, $sql2);
        if ($res2) {
            while ($r = mysqli_fetch_assoc($res2)) $products[] = $r;
            mysqli_free_result($res2);
        }
    }
}

// Log searches to statistik (server-side) when query present
if ($q !== '') {
    // record search interest per returned product (helps determine which products were searched)
    if (!empty($products)) {
        $ev = 'search';
        $count = 0;
        $safe_q = mb_substr($q, 0, 250); // limit length

        // attempt to prepare statement including search_query; catch exception if column missing
        $use_query_col = false;
        try {
            $stmt = mysqli_prepare($conn, "INSERT INTO produk_statistik (id_produk, tipe_event, search_query) VALUES (?, ?, ?)");
            if ($stmt) {
                $use_query_col = true;
            }
        } catch (mysqli_sql_exception $e) {
            // no search_query column, fall back
            $use_query_col = false;
        }

        if ($use_query_col) {
            foreach ($products as $pr) {
                if (!isset($pr['id'])) continue;
                $pid = (int)$pr['id'];
                mysqli_stmt_bind_param($stmt, 'iss', $pid, $ev, $safe_q);
                mysqli_stmt_execute($stmt);
                $count++;
                if ($count >= 50) break; // avoid too many inserts
            }
            mysqli_stmt_close($stmt);
        } else {
            // fallback older schema: only id_produk and tipe_event
            try {
                $stmt2 = mysqli_prepare($conn, "INSERT INTO produk_statistik (id_produk, tipe_event) VALUES (?, ?)");
            } catch (mysqli_sql_exception $e) {
                $stmt2 = false;
            }
            if ($stmt2) {
                foreach ($products as $pr) {
                    if (!isset($pr['id'])) continue;
                    $pid = (int)$pr['id'];
                    mysqli_stmt_bind_param($stmt2, 'is', $pid, $ev);
                    mysqli_stmt_execute($stmt2);
                    $count++;
                    if ($count >= 50) break;
                }
                mysqli_stmt_close($stmt2);
            }
        }
    } else {
        // fallback: record generic search event
        // prefer to also record the query text when no products found
        $safe_q = mb_substr($q, 0, 250);
        $use_query_col = false;
        try {
            $stmt = mysqli_prepare($conn, "INSERT INTO produk_statistik (tipe_event, search_query) VALUES (?, ?)");
            if ($stmt) {
                $use_query_col = true;
            }
        } catch (mysqli_sql_exception $e) {
            $use_query_col = false;
        }
        if ($use_query_col && $stmt) {
            $ev = 'search';
            mysqli_stmt_bind_param($stmt, 'ss', $ev, $safe_q);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } else {
            try {
                $stmt = mysqli_prepare($conn, "INSERT INTO produk_statistik (tipe_event) VALUES (?)");
            } catch (mysqli_sql_exception $e) {
                $stmt = false;
            }
            if ($stmt) {
                $ev = 'search';
                mysqli_stmt_bind_param($stmt, 's', $ev);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
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
                    // prefer category image, fallback to settings about image or defaults
                    $img = '';
                    if (!empty($cat['image'])) $img = public_image_url($cat['image'], 'categories');
                    if (empty($img)) $img = public_image_url($settings['about_image'] ?? '', 'settings');
                    if (empty($img)) $img = $default_cat_images[$i % count($default_cat_images)];
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
                <div class="col-12 g-1">
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
                <!-- Kategori dropdown dihapus sesuai permintaan -->
            </div>
        </div>

        <div class="row mt-3 px-1 px-lg-0" data-aos="fade-up">
            <?php if (empty($products)) : ?>
                <div class="col-12">
                    <p class="text-center">Belum ada produk.</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $p):
                    // Get primary image from produk_gambar table if available, fallback to old gambar column
                    $primary_img = get_product_primary_image($p['id'], $conn);
                    if ($primary_img) {
                        $img = public_image_url($primary_img['gambar'] ?? '');
                    } else {
                        $img = public_image_url($p['gambar'] ?? '');
                    }
                    // count images for badge
                    $img_count = 0;
                    $tmp_imgs = get_product_images($p['id'], $conn);
                    if (!empty($tmp_imgs) && is_array($tmp_imgs)) $img_count = count($tmp_imgs);

                    $price = isset($p['harga']) ? number_format((float)$p['harga'], 0, ',', '.') : '-';
                    $category = isset($p['nama_kategori']) ? htmlspecialchars($p['nama_kategori']) : '';
                    $slug = htmlspecialchars($p['slug'] ?? '');
                    $pid = (int)$p['id'];
                    // use the pre-computed whatsapp number from kontak_toko/settings
                    $wa_msg = rawurlencode("Saya mau beli produk {$p['nama_produk']} - apakah masih tersedia? Mohon info harga dan estimasi kirim.");
                ?>
                    <div class="col-6 col-lg-2 g-3">
                        <div class="card shadow h-100">
                            <div class="card-body m-0 p-0">
                                <div class="ratio ratio-1x1 position-relative">
                                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>" class="object-fit-cover" />
                                </div>
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