<?php
$title = "Tentang Kami - Intime Furniture";
// Use centralized config which provides $conn
require __DIR__ . '/admin/config.php';

// Load settings into associative array: $settings['site_name'] etc.
$settings = [];
$sql = "SELECT nama_setting, isi FROM settings";
if ($res = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($res)) {
        $settings[$row['nama_setting']] = $row['isi'];
    }
    mysqli_free_result($res);
}

// Load contact info (single row expected)
$contact = null;
$sql = "SELECT alamat, telepon, email, maps_embed FROM kontak_toko ORDER BY id LIMIT 1";
if ($res = mysqli_query($conn, $sql)) {
    $contact = mysqli_fetch_assoc($res);
    mysqli_free_result($res);
}

include 'partials/header.php';
?>

<body>
    <?php include 'partials/navbar.php'; ?>

    <main class="container-lg py-4">
        <!-- Hero -->
        <section class="bg-light rounded-4 shadow-sm p-4 p-md-5 mb-5" data-aos="fade-up">
            <div class="row g-5 align-items-center">
                <div class="col-lg-7">
                    <h1 class="display-6 fw-bold mb-2"><?= htmlspecialchars($settings['site_name'] ?? 'INTIME FURNITURE') ?></h1>
                    <p class="text-muted lead"><?= htmlspecialchars($settings['about_desc'] ?? 'Penyedia furniture berkualitas untuk rumah & kantor fungsional, nyaman, dan estetik.') ?></p>

                    <div class="d-lg-none">
                        <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow-sm">
                            <?php
                            $about_img = isset($settings['about_image']) ? $settings['about_image'] : 'assets/img/furniture-img.png';
                            // Ensure it has assets/img/ prefix
                            if (strpos($about_img, 'assets/img/') === false) {
                                $about_img = 'assets/img/' . basename($about_img);
                            }
                            ?>
                            <img src="<?= htmlspecialchars($about_img) ?>" alt="<?= htmlspecialchars($settings['about_hero_title'] ?? 'INTIME FURNITURE') ?>" class="object-fit-cover w-100 h-100" />
                        </div>
                    </div>

                    <div class="row gy-3 mt-4">
                        <div class="col-6 col-sm-4">
                            <div class="d-flex flex-column">
                                <small class="text-uppercase text-muted">Tahun Berdiri</small>
                                <strong class="fs-5">2019</strong>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="d-flex flex-column">
                                <small class="text-uppercase text-muted">Kontak WA</small>
                                <a class="fw-semibold" href="<?= 'https://wa.me/' . preg_replace('/\D/', '', $contact['telepon'] ?? '6281317011839') ?>"><?= htmlspecialchars($contact['telepon'] ?? '0813‑1701‑1839') ?></a>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="d-flex flex-column">
                                <small class="text-uppercase text-muted">Email</small>
                                <a class="fw-semibold" href="<?= 'mailto:' . htmlspecialchars($contact['email'] ?? 'furnitureintime@gmail.com') ?>"><?= htmlspecialchars($contact['email'] ?? 'furnitureintime@gmail.com') ?></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 d-none d-lg-block">
                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow-sm">
                        <?php
                        $about_img = isset($settings['about_image']) ? $settings['about_image'] : 'assets/img/furniture-img.png';
                        // Ensure it has assets/img/ prefix
                        if (strpos($about_img, 'assets/img/') === false) {
                            $about_img = 'assets/img/' . basename($about_img);
                        }
                        ?>
                        <img src="<?= htmlspecialchars($about_img) ?>" alt="<?= htmlspecialchars($settings['about_hero_title'] ?? 'INTIME FURNITURE') ?>" class="object-fit-cover w-100 h-100" />
                    </div>
                </div>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-lg-8">
                <article class="mb-4" data-aos="fade-up">
                    <h3 class="fw-bold">Tentang Perusahaan</h3>
                    <p class="text-muted">Intime furniture adalah perusahaan yang bergerak di bidang <strong>penyediaan dan penjualan furniture berkualitas untuk kebutuhan rumah dan kantor</strong>. Kami hadir untuk memberikan solusi interior yang <strong>fungsional, nyaman, dan estetik</strong>, dengan desain modern serta material pilihan.</p>

                    <p class="text-muted">Sejak berdiri, kami berkomitmen untuk menghadirkan produk furniture yang tidak hanya indah secara visual, tetapi juga <strong>kuat, ergonomis, dan tahan lama</strong>, sehingga mampu menunjang kenyamanan aktivitas sehari-hari, baik di rumah maupun di lingkungan kerja.</p>
                </article>

                <section class="mb-4" data-aos="fade-up">
                    <h4 class="fw-bold">Produk &amp; Layanan</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-body">
                                    <h6 class="fw-semibold"><i class="fas fa-home me-2 text-primary"></i> Furniture Rumah</h6>
                                    <p class="mb-0 text-muted small">Sofa, meja makan, kursi, lemari, tempat tidur, kitchen set.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-body">
                                    <h6 class="fw-semibold"><i class="fas fa-building me-2 text-primary"></i> Furniture Kantor</h6>
                                    <p class="mb-0 text-muted small">Meja kerja, kursi kantor, workstation, lemari arsip, meja meeting.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-body">
                                    <h6 class="fw-semibold"><i class="fas fa-pencil-ruler me-2 text-primary"></i> Custom Furniture</h6>
                                    <p class="mb-0 text-muted small">Desain dan produksi sesuai kebutuhan pelanggan.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-body">
                                    <h6 class="fw-semibold"><i class="fas fa-truck me-2 text-primary"></i> Pengiriman &amp; Instalasi</h6>
                                    <p class="mb-0 text-muted small">Layanan pengiriman dan instalasi profesional ke seluruh area layanan kami.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mb-4" data-aos="fade-up">
                    <h4 class="fw-bold">Keunggulan Kami</h4>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Desain modern &amp; minimalis</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Material berkualitas tinggi</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Harga kompetitif</li>

                                <div class="d-md-none">
                                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Bisa custom sesuai kebutuhan</li>
                                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Pelayanan profesional & tepat waktu</li>
                                </div>
                            </ul>
                        </div>
                        <div class="col-sm-6 d-none d-md-block">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Bisa custom sesuai kebutuhan</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Pelayanan profesional & tepat waktu</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section class="mb-4" data-aos="fade-up">
                    <h4 class="fw-bold">Visi &amp; Misi</h4>
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm border-0 p-3">
                                <h5 class="fw-semibold">Visi</h5>
                                <p class="mb-0 text-muted">Menjadi perusahaan furniture terpercaya di Indonesia yang menghadirkan produk berkualitas tinggi dengan desain inovatif.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm border-0 p-3">
                                <h5 class="fw-semibold">Misi</h5>
                                <ul class="mb-0 text-muted">
                                    <li>Menyediakan furniture yang nyaman, fungsional, dan bernilai estetika</li>
                                    <li>Mengutamakan kepuasan pelanggan melalui kualitas produk dan layanan</li>
                                    <li>Terus berinovasi mengikuti perkembangan desain interior</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="col-lg-4">
                <div class="card shadow-sm mb-3" data-aos="fade-up">
                    <div class="card-body">
                        <h6 class="fw-bold">Kontak &amp; Lokasi</h6>
                        <p class="mb-1"><i class="fas fa-map-marker-alt me-2 text-primary"></i><?= htmlspecialchars($contact['alamat'] ?? 'Blk. Singkuk No.22B, Pagar Kuning, Kec. Limo, Kota Depok, Jawa Barat 16515') ?></p>
                        <p class="mb-1"><i class="fas fa-phone me-2 text-primary"></i><a href="<?= 'https://wa.me/' . preg_replace('/\D/', '', $contact['telepon'] ?? '6281317011839') ?>"><?= htmlspecialchars($contact['telepon'] ?? '0813‑1701‑1839') ?></a></p>
                        <p class="mb-3"><i class="fas fa-envelope me-2 text-primary"></i><a href="<?= 'mailto:' . htmlspecialchars($contact['email'] ?? 'furnitureintime@gmail.com') ?>"><?= htmlspecialchars($contact['email'] ?? 'furnitureintime@gmail.com') ?></a></p>
                        <a href="https://maps.app.goo.gl/s8Pg3RnhPKGB5MyV8" target="_blank" class="btn btn-outline-secondary w-100">Buka di Google Maps</a>
                    </div>
                </div>

                <div class="card shadow-sm" data-aos="fade-up">
                    <div class="card-body p-0">
                        <iframe src="<?= htmlspecialchars($contact['maps_embed'] ?? 'https://www.google.com/maps?q=Blk.%20Singkuk%20No.22B%20Pagar%20Kuning%20Limo%20Depok&output=embed') ?>" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    <?php include 'partials/footer.php'; ?>
    <?php include 'partials/scripts.php'; ?>
</body>

</html>