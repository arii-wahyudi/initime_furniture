<?php
$title = 'Admin Login - Intime Furniture';
// Simple admin login form (no authentication logic here).
// Implement server-side validation and authentication as needed.
include __DIR__ . '/partials/header.php';
?>

<body class="bg-light">
    <main class="min-vh-100 d-flex align-items-center justify-content-center">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
                    <div class="card shadow-lg border-0 rounded-4 mx-auto" data-aos="zoom-in" data-aos-duration="700">
                        <div class="card-body p-4">
                            <div class="text-center mb-3">
                                <img src="../assets/img/logo.ico" alt="logo" width="56" height="56" class="mb-2">
                                <h4 class="mb-0">Admin Panel</h4>
                                <small class="text-muted">Masuk untuk mengelola situs</small>
                            </div>

                            <form method="post" action="authenticate.php" novalidate>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input id="username" name="username" type="text" class="form-control form-control-lg rounded-pill" required autofocus>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input id="password" name="password" type="password" class="form-control form-control-lg rounded-pill" required>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="remember" name="remember">
                                        <label class="form-check-label small" for="remember">Ingat saya</label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 btn-lg rounded-pill">Masuk</button>
                            </form>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center text-muted small py-3">
                            © <?= date('Y') ?> Intime Furniture — Admin Area
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include __DIR__ . '/partials/scripts.php'; ?>
</body>

</html>