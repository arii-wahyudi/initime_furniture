<?php
$title = "Dashboard - Intime Furniture";
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

// Load categories
$categories = [];
$sql = "SELECT id, nama_kategori FROM kategori_produk ORDER BY id";
if ($res = mysqli_query($conn, $sql)) {
  while ($row = mysqli_fetch_assoc($res)) {
    $categories[] = $row;
  }
  mysqli_free_result($res);
}

// Build category map for easy lookup
$cat_map = [];
foreach ($categories as $c) {
  $cat_map[$c['id']] = $c['nama_kategori'];
}

// Load products (top products) - only active
$products = [];
$sql = "SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori_produk k ON p.id_kategori = k.id WHERE p.status = 'aktif' ORDER BY p.created_at DESC LIMIT 8";
if ($res = mysqli_query($conn, $sql)) {
  while ($row = mysqli_fetch_assoc($res)) {
    $products[] = $row;
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
$about_img = isset($settings['about_image']) ? $settings['about_image'] : 'furniture-img.png';
$about_img = 'assets/img/' . basename($about_img);

include 'partials/header.php';
?>

<body>
  <!-- NAVBAR SECTION -->
  <?php include 'partials/navbar.php'; ?>

  <div
    id="carouselExampleCaptions"
    class="container-lg carousel slide py-md-3 mb-3"
    data-bs-ride="carousel">
    <div class="carousel-indicators">
      <button
        type="button"
        data-bs-target="#carouselExampleCaptions"
        data-bs-slide-to="0"
        class="active"
        aria-current="true"
        aria-label="Slide 1"></button>
      <button
        type="button"
        data-bs-target="#carouselExampleCaptions"
        data-bs-slide-to="1"
        aria-label="Slide 2"></button>
    </div>
    <div class="carousel-inner rounded-3 shadow">
      <?php
      $cr1_img = isset($settings['carousel_1_image']) ? $settings['carousel_1_image'] : 'cr1.png';
      $cr1_img = 'assets/img/' . basename($cr1_img);

      $cr1_title = isset($settings['carousel_1_title']) ? $settings['carousel_1_title'] : 'Solusi Interior Fungsional & Estetik';
      $cr1_desc = isset($settings['carousel_1_desc']) ? $settings['carousel_1_desc'] : 'Intime Furniture menghadirkan koleksi terbaik untuk kebutuhan rumah dan kantor.';

      $cr2_img = isset($settings['carousel_2_image']) ? $settings['carousel_2_image'] : 'cr2.jpg';
      $cr2_img = 'assets/img/' . basename($cr2_img);

      $cr2_title = isset($settings['carousel_2_title']) ? $settings['carousel_2_title'] : 'Wujudkan Desain Impian Anda';
      $cr2_desc = isset($settings['carousel_2_desc']) ? $settings['carousel_2_desc'] : 'Nikmati layanan Custom Furniture dengan material pilihan berkualitas tinggi.';
      ?>

      <div class="carousel-item ratio ratio-21x9 active">
        <img src="<?= htmlspecialchars($cr1_img) ?>" class="d-block w-100 img-fluid opacity-25 object-fit-cover" alt="<?= htmlspecialchars($cr1_title) ?>" />
        <div class="carousel-caption w-md-50 w-75 d-flex align-items-center justify-content-start h-100 top-0 start-0 reveal px-4">
          <div class="d-block text-start px-md-5">
            <p class="mb-1 fw-semibold capt-title mb-md-3 mb-2"><?= htmlspecialchars($cr1_title) ?></p>
            <p class="capt-desc"><?= htmlspecialchars($cr1_desc) ?></p>
          </div>
        </div>
      </div>

      <div class="carousel-item ratio ratio-21x9">
        <img src="<?= htmlspecialchars($cr2_img) ?>" class="d-block w-100 img-fluid opacity-25 object-fit-cover" alt="<?= htmlspecialchars($cr2_title) ?>" />
        <div class="carousel-caption w-md-50 w-75 d-flex align-items-center justify-content-start h-100 top-0 start-0 px-4">
          <div class="d-block text-start px-md-5">
            <p class="mb-1 fw-semibold capt-title mb-md-3 mb-2"><?= htmlspecialchars($cr2_title) ?></p>
            <p class="capt-desc"><?= htmlspecialchars($cr2_desc) ?></p>
          </div>
        </div>
      </div>

    </div>

    <button
      class="carousel-control-prev"
      type="button"
      data-bs-target="#carouselExampleCaptions"
      data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button
      class="carousel-control-next"
      type="button"
      data-bs-target="#carouselExampleCaptions"
      data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
  <!-- CAROUSEL SECTION END -->

  <!-- ABOUT US SECTION START -->
  <!-- <div class="container-lg mb-5">
    <div class="d-flex align-items-center justify-content-center pt-5">
      <h5 class="text-center mb-4 fs-md-5 bg-title py-2 px-3 rounded-pill">
        Tentang Kami
      </h5>
    </div>

    <div class="row mt-3" data-aos="fade-up">
      <div class="col-lg-6 d-flex justify-content-center align-items-center">
        <div class="ratio ratio-16x9">
          <img
            src="assets/img/furniture-img.png"
            alt=""
            class="object-fit-cover rounded-3 shadow" />
        </div>
      </div>
      <div
        class="col-lg-6 mt-3 mt-lg-0 d-flex justify-content-center align-items-center">
        <div class="d-inline">
          <h2 class="fw-bold d-block mb-3">INTIME FURNITURE</h2>
          <p>
            Lorem ipsum dolor sit, amet consectetur adipisicing elit. Nemo
            repudiandae quo nam aspernatur magni. Facilis, quisquam
            consequatur! Saepe animi aperiam laboriosam dolores tenetur a quia
            porro maiores voluptate iusto error tempora sint expedita magnam
            maxime, reprehenderit rem alias vitae amet, omnis hic asperiores
            illum. Porro, in facilis. Maxime, voluptatum distinctio.
          </p>
          <div class="row">
            <div class="col-2">
              <div
                class="p-3 bg-title rounded-pill d-flex justify-content-center align-items-center fs-1">
                <i class="fas fa-medal"></i>
              </div>
            </div>
            <div class="col-10">
              <h5 class="fw-bold">4+ Tahun Pengalaman</h5>
              <p>
                Lorem, ipsum dolor sit amet consectetur adipisicing elit.
                Voluptate, facilis.
              </p>
            </div>
          </div>
          <div class="row">
            <div class="col-2">
              <div
                class="p-3 bg-title rounded-pill d-flex justify-content-center align-items-center fs-1">
                <i class="fas fa-users"></i>
              </div>
            </div>
            <div class="col-10">
              <h5 class="fw-bold">Tim Profesional</h5>
              <p>
                Lorem, ipsum dolor sit amet consectetur adipisicing elit.
                Voluptate, facilis.
              </p>
            </div>
          </div>
          <div class="row">
            <div class="col-2">
              <div
                class="p-3 bg-title rounded-pill d-flex justify-content-center align-items-center fs-1">
                <i class="fas fa-clock"></i>
              </div>
            </div>
            <div class="col-10">
              <h5 class="fw-bold">Pengerjaaan Cepat</h5>
              <p>
                Lorem, ipsum dolor sit amet consectetur adipisicing elit.
                Voluptate, facilis.
              </p>
            </div>
          </div>
          <a href="#" class="btn btn-outline-secondary text-dark">Lihat Selengkapnya <i class="fas fa-arrow-right ms-3"></i></a>
        </div>
      </div>
    </div>
  </div> -->
  <div class="container-lg mb-5">
    <div class="d-flex align-items-center justify-content-center pt-3">
      <h5 class="text-center mb-4 fs-md-5 bg-title py-2 px-3 rounded-pill">
        Tentang Kami
      </h5>
    </div>

    <div class="row mt-3" data-aos="fade-up">
      <div class="col-lg-6 d-flex justify-content-center align-items-center">
        <div class="ratio ratio-16x9">
          <img
            src="<?= htmlspecialchars($about_img) ?>"
            alt="<?= htmlspecialchars($settings['about_title'] ?? 'Tentang Kami') ?>"
            class="object-fit-cover rounded-3 shadow" />
        </div>
      </div>
      <div
        class="col-lg-6 mt-3 mt-lg-0 d-flex justify-content-center align-items-center">
        <div class="d-inline">
          <h2 class="fw-bold d-block mb-3"><?= htmlspecialchars($settings['about_title'] ?? 'INTIME FURNITURE') ?></h2>
          <p><?= nl2br(htmlspecialchars($settings['about_desc'] ?? 'Kami adalah penyedia solusi interior yang berfokus pada furniture berkualitas untuk kebutuhan rumah dan kantor.')) ?></p>

          <div class="row mb-3">
            <div class="col-2">
              <div class="p-3 bg-title rounded-pill d-flex justify-content-center align-items-center fs-2">
                <i class="fas fa-couch"></i>
              </div>
            </div>
            <div class="col-10">
              <h5 class="fw-bold mb-1"><?= htmlspecialchars($settings['about_exp_title'] ?? '4+ Tahun Pengalaman') ?></h5>
              <p class="small text-muted"><?= htmlspecialchars($settings['about_exp_desc'] ?? 'Berpengalaman dalam produksi dan instalasi furniture') ?></p>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-2">
              <div class="p-3 bg-title rounded-pill d-flex justify-content-center align-items-center fs-2">
                <i class="fas fa-users"></i>
              </div>
            </div>
            <div class="col-10">
              <h5 class="fw-bold mb-1"><?= htmlspecialchars($settings['about_team_title'] ?? 'Tim Profesional') ?></h5>
              <p class="small text-muted"><?= htmlspecialchars($settings['about_team_desc'] ?? 'Didukung oleh tenaga ahli berpengalaman') ?></p>
            </div>
          </div>

          <div class="row mb-4">
            <div class="col-2">
              <div class="p-3 bg-title rounded-pill d-flex justify-content-center align-items-center fs-2">
                <i class="fas fa-shipping-fast"></i>
              </div>
            </div>
            <div class="col-10">
              <h5 class="fw-bold mb-1"><?= htmlspecialchars($settings['about_fast_title'] ?? 'Pengerjaan Cepat') ?></h5>
              <p class="small text-muted"><?= htmlspecialchars($settings['about_fast_desc'] ?? 'Proses produksi dan instalasi tepat waktu') ?></p>
            </div>
          </div>

          <a href="about_us.php" class="btn btn-outline-secondary text-dark fw-bold">Lihat Selengkapnya <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
      </div>
    </div>
  </div>
  <!-- ABOUT US SECTION END -->

  <!-- CATEGORY SECTION START -->
  <div class="container-lg mb-5">
    <div class="d-flex align-items-center justify-content-center">
      <h5 class="text-center mb-4 fs-md-5 bg-title py-2 px-3 rounded-pill">
        Kategori Produk
      </h5>
    </div>

    <div class="row g-2" data-aos="fade-up">
      <?php
      $default_cat_images = [
        'assets/img/cat1-ruangtamu.png',
        'assets/img/cat2-ruangmakan.jpg',
        'assets/img/cat3-ruangrapat.png'
      ];
      if (empty($categories)) {
        echo '<div class="col-12"><p class="text-center">Belum ada kategori.</p></div>';
      } else {
        // Tampilkan maksimal 3 kategori
        $display_count = min(count($categories), 3);
        for ($i = 0; $i < $display_count; $i++) {
          $cat = $categories[$i];
          $img = $default_cat_images[$i % count($default_cat_images)];
          $name = htmlspecialchars($cat['nama_kategori']);
          $cid = (int)$cat['id'];
          echo "<div class=\"col-6 col-md-4\">";
          echo "<a href=\"product.php?cat=$cid\" class=\"text-decoration-none\">";
          echo "<div class=\"card card-category shadow bg-card-category\">";
          echo "<img src=\"$img\" class=\"card-img object-fit-cover opacity-50\" alt=\"$name\" />";
          echo "<div class=\"card-img-overlay d-flex justify-content-center align-items-center p-4\">";
          echo "<h5 class=\"card-title m-0 text-center\">$name</h5>";
          echo "</div></div></a></div>";
        }
      }
      ?>
    </div>

    <div class="d-flex justify-content-center align-items-center mt-4">
      <a href="product.php" class="btn btn-outline-secondary text-dark w-auto px-4">Lihat Semua Kategori</a>
    </div>
  </div>
  <!-- CATEGORY SECTION END -->

  <!-- PRODUCT SECTION START -->
  <div class="container-lg mb-5">
    <div class="d-flex align-items-center justify-content-center pt-5">
      <h5 class="text-center mb-4 fs-md-5 bg-title py-2 px-3 rounded-pill">
        Produk Teratas
      </h5>
    </div>

    <div class="row mt-3" data-aos="fade-up">
      <?php if (empty($products)) : ?>
        <div class="col-12">
          <p class="text-center">Belum ada produk.</p>
        </div>
      <?php else: ?>
        <?php foreach ($products as $p):
          $img = public_image_url($p['gambar'] ?? '');
          $img = str_replace('/project/', '', $img);
          $price = isset($p['harga']) ? number_format((float)$p['harga'], 0, ',', '.') : '-';
          $category = isset($p['nama_kategori']) ? htmlspecialchars($p['nama_kategori']) : '';
          $slug = htmlspecialchars($p['slug'] ?? '');
          $pid = (int)$p['id'];
        ?>
          <div class="col-6 col-lg-3 g-3">
            <div class="card shadow h-100">
              <div class="card-body m-0 p-0">
                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>" class="w-100" />
              </div>
              <div class="card-footer py-3">
                <h4><?= htmlspecialchars($p['nama_produk']) ?></h4>
                <span class="badge text-bg-secondary"><?= $category ?></span>
                <p class="my-2">Rp <?= $price ?></p>
                <a href="product_detail.php?slug=<?= urlencode($slug) ?>" class="btn btn-outline-secondary text-dark mt-3 w-100 btn-view-detail" data-product-id="<?= $pid ?>">Lihat Detail</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="d-flex justify-content-center align-items-center">
      <a href="product.php" class="btn btn-outline-secondary text-dark w-auto mt-4 px-4">Lihat Semua Produk</a>
    </div>

    <!-- Product detail moved to separate page -->
  </div>

  </div>
  <!-- PRODUCT SECTION END -->

  <!-- TESTIMONIALS SECTION START -->
  <div class="container-lg mb-5">
    <div class="d-flex align-items-center justify-content-center pt-5">
      <h5 class="text-center mb-4 fs-md-5 bg-title py-2 px-3 rounded-pill">
        Testimoni Pelanggan
      </h5>
    </div>

    <div class="row mt-3">
      <?php
      for ($i = 1; $i <= 3; $i++) {
        $text = isset($settings["testimonial_{$i}_text"]) ? $settings["testimonial_{$i}_text"] : '';
        $name = isset($settings["testimonial_{$i}_name"]) ? $settings["testimonial_{$i}_name"] : '';
        if (empty($text) && empty($name)) continue;
      ?>
        <div class="col-md-4 mb-4" data-aos="fade-up">
          <div class="card shadow bg-title p-3">
            <p class="fst-italic">"<?= htmlspecialchars($text) ?>"</p>
            <p class="fw-bold">— <?= htmlspecialchars($name) ?></p>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
  <!-- TESTIMONIALS SECTION END -->

  <!-- CONTACT SECTION START -->
  <div class="container-lg mb-5">
    <div class="d-flex align-items-center justify-content-center pt-5">
      <h5 class="text-center mb-4 fs-md-5 bg-title py-2 px-3 rounded-pill">
        Hubungi Kami
      </h5>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body m-0 p-0">
            <div class="row g-0">
              <div
                class="col-2 bg-title fs-1 d-flex justify-content-center align-items-center text-primary">
                <i class="fas fa-map-pin"></i>
              </div>
              <div class="col-10 p-3">
                <span class="fw-bold fs-5">Alamat</span>
                <p><?= htmlspecialchars($contact['alamat'] ?? 'Jl. Merdeka No.123, Jakarta, Indonesia') ?></p>
              </div>
            </div>
          </div>
        </div>

        <div class="card mt-4">
          <div class="card-body m-0 p-0">
            <div class="row g-0">
              <div
                class="col-2 bg-title fs-1 d-flex justify-content-center align-items-center text-success">
                <i class="fas fa-phone"></i>
              </div>
              <div class="col-10 p-3">
                <span class="fw-bold fs-5">Telepon</span>
                <p><?= htmlspecialchars($contact['telepon'] ?? '+62 812 3456 7890') ?></p>
              </div>
            </div>
          </div>
        </div>

        <div class="card mt-4">
          <div class="card-body m-0 p-0">
            <div class="row g-0">
              <div
                class="col-2 bg-title fs-1 d-flex justify-content-center align-items-center text-danger">
                <i class="fas fa-envelope"></i>
              </div>
              <div class="col-10 p-3">
                <span class="fw-bold fs-5">Email</span>
                <p><?= htmlspecialchars($contact['email'] ?? 'company@example.com') ?></p>
              </div>
            </div>
          </div>
        </div>

        <iframe
          src="<?= htmlspecialchars($contact['maps_embed'] ?? 'https://www.google.com/maps/embed?...') ?>"
          class="mt-4 w-100 shadow"
          height="250"
          style="border: 0"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>

      <div class="col-md-6">
        <div class="card w-100">
          <div class="card-body">
            <h5 class="fw-bold">Kirim Pesan</h5>
            <p>Isi form di bawah ini dan kami akan segera menghubungi Anda</p>
            <form action="">
              <input
                class="form-control mb-3"
                type="text"
                placeholder="Nama Lengkap"
                required />
              <input
                class="form-control mb-3"
                type="email"
                placeholder="Email"
                required />
              <input
                class="form-control mb-3"
                type="text"
                placeholder="Nomor Telepon"
                required />
              <textarea
                class="form-control mb-4"
                rows="2"
                placeholder="Pesan"></textarea>
              <button type="submit" class="btn btn-success w-100">
                <i class="far fa-paper-plane me-2"></i> Kirim Via WhatsApp
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- CONTACT SECTION END -->

  <!-- FOOTER & WA SECTION -->
  <?php include 'partials/footer.php'; ?>

  <!-- SCRIPT -->
  <?php include 'partials/scripts.php'; ?>

</body>

</html>