<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>INTIME FURNITURE</title>
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">

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

    <!-- CATEGORY SECTION START -->
    <div class="container-lg mt-4" >
        <div class="row g-2">
            <div class="col-6 col-md-4">
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

            <div class="col-6 col-md-4">
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

            <div class="col-6 col-md-4">
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

            <div class="col-6 col-md-4">
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

            <div class="col-6 col-md-4">
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

            <div class="col-6 col-md-4">
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
            <div class="row justify-content-center">
                <div class="col-lg-8 col-6 g-1">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            class="form-control border-start-0"
                            placeholder="Cari produk..." />
                    </div>
                </div>
                <div class="col-lg-4 col-6 g-1">
                    <div class="dropdown w-100">
                        <a class="btn btn-light shadow w-100 d-flex justify-content-between" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                            <span><i class="fas fa-sliders-h me-2"></i>Kategori</span>
                            <small><i class="fas fa-chevron-down"></i></small>
                        </a>

                        <ul class="dropdown-menu w-100 dropdown-menu-custom mt-2">
                            <li><a class="dropdown-item w-100" href="product.php#product">Ruang Tamu</a></li>
                            <li><a class="dropdown-item w-100" href="product.php#product">Ruang Makan</a></li>
                            <li><a class="dropdown-item w-100" href="product.php#product">Ruang Rapat</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3 px-1 px-lg-0">
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

            <div class="col-6 col-lg-3 g-3" >
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
    </div>
    <!-- PRODUCT SECTION END -->



    <!-- FOOTER SECTION START -->
    <div class="bg-dark text-light mt-5" data-bs-theme="dark">
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
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 1000,
    once: true
  });
</script>

</body>

</html>