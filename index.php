<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>INTIME FURNITURE</title>

  <!-- Bootstrap CSS CDN -->
  <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" />

  <script src="https://unpkg.com/scrollreveal"></script>

  <script
    src="https://kit.fontawesome.com/0722d0c4d5.js"
    crossorigin="anonymous"></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/custom.css" />
    
</head>

<body>
  <!-- NAVBAR SECTION START -->
  <nav
    class="navbar bg-nav sticky-top navbar-expand-lg"
    data-bs-theme="light">
    <div class="container-lg">
      <a class="navbar-brand" href="index.php"><b>INTIME FURNITURE</b></a>
      <div class="navbarMenu d-none d-lg-block ms-auto text-dark">
        <a class="nav-link d-inline" href="index.php">Dashboard</a>
        <a class="nav-link d-inline ps-4" href="about_us.php">Tentang Kami</a>
        <a class="nav-link d-inline ps-4" href="product.php">Produk</a>
      </div>
      <button class="btn btn-outline-dark d-lg-none" id="btn-bar">
        <i class="fa-solid fa-bars"></i>
      </button>
      <button class="btn btn-outline-dark d-lg-none d-none" id="btn-times">
        <i class="fa-solid fa-times"></i>
      </button>
    </div>
    <div
      class="d-lg-none d-none w-50 bg-opacity-100 position-absolute top-100 end-0 p-3 shadow-lg rounded-bottom"
      id="pop-menu">
      <div class="menu ">
        <a class="nav-link fs-6 mb-3" href="index.php">Dashboard</a>
        <a class="nav-link fs-6 mb-3" href="about_us.php">Tentang Kami</a>
        <a class="nav-link fs-6" href="product.php">Produk</a>
      </div>
    </div>
  </nav>
  <!-- NAVBAR SECTION END -->

  <!-- CAROUSEL SECTION -->
  <div
    id="carouselExampleCaptions"
    class="container-lg carousel slide py-md-3 mb-5"
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
      <div class="carousel-item ratio ratio-21x9 active">
        <img
          src="assets/img/cr1.png"
          class="d-block w-100 img-fluid opacity-25 object-fit-cover"
          alt="..." />
        <div
          class="carousel-caption w-md-50 w-75 d-flex align-items-center justify-content-start h-100 top-0 start-0 reveal px-4">
          <div class="d-block text-start px-md-5">
            <p class="mb-1 fw-semibold capt-title mb-md-3 mb-2">
              First slide label
            </p>
            <p class="capt-desc">
              Lorem ipsum, dolor sit amet consectetur adipisicing elit.
              Tempora eveniet blanditiis optio, officiis quaerat ex quas. Esse
              corporis, error tempora minima illo ab tempore aliquam
              doloremque reprehenderit optio omnis natus!
            </p>
          </div>
        </div>
      </div>

      <div class="carousel-item ratio ratio-21x9">
        <img
          src="assets/img/cr2.jpg"
          class="d-block w-100 img-fluid opacity-25 object-fit-cover"
          alt="..." />
        <div
          class="carousel-caption w-md-50 w-75 d-flex align-items-center justify-content-start h-100 top-0 start-0 px-4">
          <div class="d-block text-start px-md-5">
            <p class="mb-1 fw-semibold capt-title mb-md-3 mb-2">
              Second slide label
            </p>
            <p class="capt-desc">
              Lorem ipsum dolor sit amet consectetur adipisicing elit.
              Veritatis quo voluptatum perferendis, nam obcaecati atque
              asperiores quam vero laboriosam corporis quaerat? Beatae quia
              fugiat repellendus voluptates, dolore recusandae aliquid
              deserunt?
            </p>
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

  <!-- CATEGORY SECTION START -->
  <div class="container-lg mb-5">
    <div class="d-flex align-items-center justify-content-center">
      <h5 class="text-center mb-4 fs-md-5 bg-title py-2 px-3 rounded-pill">
        Kategori Produk
      </h5>
    </div>

    <div class="row g-2">
      <div class="col-6 col-md-3">
        <div class="card card-category shadow bg-card-category">
          <img
            src="assets/img/cat1-ruangtamu.png"
            class="card-img object-fit-cover opacity-50"
            alt="" />
          <div
            class="card-img-overlay d-flex justify-content-center align-items-center p-4">
            <h5 class="card-title m-0 text-center">Furniture Ruang Tamu</h5>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="card card-category shadow bg-card-category">
          <img
            src="assets/img/cat2-ruangmakan.jpg"
            class="card-img object-fit-cover opacity-50"
            alt="" />
          <div
            class="card-img-overlay d-flex justify-content-center align-items-center p-4">
            <h5 class="card-title m-0 text-center">Furniture Ruang Makan</h5>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="card card-category shadow bg-card-category">
          <img
            src="assets/img/cat3-ruangrapat.png"
            class="card-img object-fit-cover opacity-50"
            alt="" />
          <div
            class="card-img-overlay d-flex justify-content-center align-items-center p-4">
            <h5 class="card-title m-0 text-center">Furniture Ruang Rapat</h5>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div
          class="card card-category d-flex flex-column align-items-center justify-content-center shadow-sm border-3 border-light p-4">
          <img
            src="assets/img/more-category.svg"
            alt=""
            width="30"
            height="30"
            class="mb-2" />
          <p class="card-title m-0 text-center">Lihat Semua Kategori</p>
        </div>
      </div>
    </div>
  </div>
  <!-- CATEGORY SECTION END -->

  <!-- ABOUT US SECTION START -->
  <div class="container-lg mb-5">
    <div class="d-flex align-items-center justify-content-center pt-5">
      <h5 class="text-center mb-4 fs-md-5 bg-title py-2 px-3 rounded-pill">
        Tentang Kami
      </h5>
    </div>

    <div class="row mt-3">
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
  </div>
  <!-- ABOUT US SECTION END -->

  <!-- PRODUCT SECTION START -->
  <div class="container-lg mb-5">
    <div class="d-flex align-items-center justify-content-center pt-5">
      <h5 class="text-center mb-4 fs-md-5 bg-title py-2 px-3 rounded-pill">
        Produk Teratas
      </h5>
    </div>

    <div class="row mt-3">
      <div class="col-6 col-lg-3 g-3">
        <div class="card shadow">
          <div class="card-body m-0 p-0">
            <img src="assets/img/prod1.jpg" alt="" class="w-100" />
          </div>
          <div class="card-footer py-3">
            <h4>Sofa Bed</h4>
            <span class="badge text-bg-secondary">Ruang Keluarga</span>
            <p class="my-2">Rp 2.500.000</p>
            <a href="#" class="btn btn-outline-secondary text-dark mt-3 w-100">Lihat Detail</a>
          </div>
        </div>
      </div>

      <div class="col-6 col-lg-3 g-3">
        <div class="card shadow">
          <div class="card-body m-0 p-0">
            <img src="assets/img/prod2.jpg" alt="" class="w-100" />
          </div>
          <div class="card-footer py-3">
            <h4>Rak TV</h4>
            <span class="badge text-bg-secondary">Ruang Keluarga</span>
            <p class="my-2">Rp 1.790.000</p>
            <a href="#" class="btn btn-outline-secondary text-dark mt-3 w-100">Lihat Detail</a>
          </div>
        </div>
      </div>

      <div class="col-6 col-lg-3 g-3">
        <div class="card shadow">
          <div class="card-body m-0 p-0">
            <img src="assets/img/prod3.jpg" alt="" class="w-100" />
          </div>
          <div class="card-footer py-3">
            <h4>Lemari Pakaian</h4>
            <span class="badge text-bg-secondary">Kamar Tidur</span>
            <p class="my-2">Rp 8.250.000</p>
            <a href="#" class="btn btn-outline-secondary text-dark mt-3 w-100">Lihat Detail</a>
          </div>
        </div>
      </div>

      <div class="col-6 col-lg-3 g-3">
        <div class="card shadow">
          <div class="card-body m-0 p-0">
            <img src="assets/img/prod4.jpg" alt="" class="w-100" />
          </div>
          <div class="card-footer py-3">
            <h4>Kursi Kantor</h4>
            <span class="badge text-bg-secondary">Ruang Belajar & Bekerja</span>
            <p class="my-2">Rp 2.500.000</p>
            <a href="#" class="btn btn-outline-secondary text-dark mt-3 w-100">Lihat Detail</a>
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex justify-content-center align-items-center">
      <a href="#" class="btn btn-outline-secondary text-dark w-auto mt-4 px-4">Lihat Semua Produk</a>
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
      <div class="col-md-4 mb-4">
        <div class="card shadow bg-title p-3">
          <p class="fst-italic">
            "Produk berkualitas tinggi dan layanan pelanggan yang luar biasa!"
          </p>
          <p class="fw-bold">— Budi Santoso</p>
        </div>
      </div>

      <div class="col-md-4 mb-4">
        <div class="card shadow bg-title p-3">
          <p class="fst-italic">
            "Desain modern dan nyaman. Sangat merekomendasikan!"
          </p>
          <p class="fw-bold">— Dewi Putri</p>
        </div>
      </div>

      <div class="col-md-4 mb-4">
        <div class="card shadow bg-title p-3">
          <p class="fst-italic">
            "Kualitas produk sangat baik dan pengiriman cepat."
          </p>
          <p class="fw-bold">— Andi Prasetyo</p>
        </div>
      </div>
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
                <p>Jl. Merdeka No.123, Jakarta, Indonesia</p>
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
                <p>+62 812 3456 7890</p>
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
                <p>company@example.com</p>
              </div>
            </div>
          </div>
        </div>

        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.1071332340566!2d106.77214907478529!3d-6.380170462407521!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69efaf0ebfd4d9%3A0x795475c2c686a1f3!2sIntime%20furniture%20depok!5e0!3m2!1sid!2sid!4v1769402937982!5m2!1sid!2sid"
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

  <!-- FOOTER SECTION START -->
  <div class="bg-dark text-light" data-bs-theme="dark">
    <div class="row g-0 p-4">
      <div class="col-lg-4">
        <h1 class="fw-bold">INTIME FURNITURE</h1>
        <p>Solusi Kebutuhan Furniture Anda</p>
        <div
          class="p-2 bg-dark-subtle shadow text-light d-flex justify-content-center align-items-center rounded"
          style="width: 50px">
          <i class="fab fa-instagram fs-1"></i>
        </div>
      </div>
      <div class="col-lg-2 mt-5 mt-lg-0">
        <h4 class="fw-bold mb-3">Navigation</h4>
        <a href="#" class="nav-link mb-2">Dashboard</a>
        <a href="#" class="nav-link mb-2">Tentang Kami</a>
        <a href="#" class="nav-link">Produk</a>
      </div>
      <div class="col-lg-3 mt-5 mt-lg-0">
        <h4 class="fw-bold mb-3">Kategori</h4>
        <a href="#" class="nav-link mb-2">Furniture Ruang Keluarga</a>
        <a href="#" class="nav-link mb-2">Furniture Kamar Tidur</a>
        <a href="#" class="nav-link">Furniture Ruang Belajar & Bekerja</a>
      </div>
      <div class="col-lg-3 mt-5 mt-lg-0">
        <h4 class="fw-bold mb-3">Kontak</h4>
        <a href="#" class="nav-link mb-2"><i class="fas fa-map-pin"></i> Jl. Merdeka No.123, Jakarta,
          Indonesia</a>
        <a href="#" class="nav-link mb-2"><i class="fas fa-phone"></i> +62 812 3456 7890</a>
        <a href="#" class="nav-link"><i class="fas fa-envelope"></i> company@example.com</a>
      </div>
    </div>

    <hr class="border-light" />
    <footer class="text-center py-4">
      <small>&copy; <?= date('Y') ?> Intime Furniture. All rights reserved.</small>
      <br>
      <a href="#" class="nav-link">Developed by Desadroid</a>
    </footer>
  </div>

  <!-- FOOTER SECTION END -->

  <a
    href="https://wa.me/628123456789?text=Halo,%20saya%20ingin%20bertanya"
    class="wa-floating-btn"
    target="_blank">
    <i class="fab fa-whatsapp"></i>
  </a>


  <!-- Bootstrap JS (hanya 1 file) -->
  <script src="assets/bootstrap/js/bootstrap.bundle.min.js" defer></script>

  <!-- ScrollReveal -->
  <script src="https://unpkg.com/scrollreveal@4.0.0/dist/scrollreveal.min.js"></script>

  <!-- Custom JS -->
  <script src="assets/js/main.js" defer></script>
</body>

</html>