<?php
$title = "Produk Kami - Intime Furniture";
include 'partials/header.php';
?>

<body>
    <!-- NAVBAR SECTION -->
    <?php include 'partials/navbar.php'; ?>

    <!-- CATEGORY SECTION START -->
    <div class="container-lg mt-4">
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
    </div>
    <!-- PRODUCT SECTION END -->



    <!-- FOOTER & WA SECTION -->
    <?php include 'partials/footer.php'; ?>


    <!-- SCRIPT -->
    <?php include 'partials/scripts.php'; ?>

</body>

</html>