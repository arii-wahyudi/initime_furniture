<?php
require __DIR__ . '/config.php';
$title = 'Admin Login - Intime Furniture';
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

                            <?php if (!empty($_GET['error'])): ?>
                                <div class="alert alert-danger small" role="alert">
                                    <?php if ($_GET['error'] === 'invalid'): ?>
                                        Username atau password salah.
                                    <?php elseif ($_GET['error'] === 'csrf'): ?>
                                        Terjadi kesalahan keamanan (CSRF). Coba lagi.
                                    <?php else: ?>
                                        Terjadi kesalahan. Coba lagi.
                                    <?php endif; ?>
                                </div>
                            <?php elseif (!empty($_GET['msg'])): ?>
                                <div class="alert alert-success small" role="alert">
                                    <?php if ($_GET['msg'] === 'loggedout'): ?>
                                        Anda telah logout.
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <form method="post" action="authenticate.php" novalidate>
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input id="username" name="username" type="text" class="form-control form-control-lg rounded-pill" required autofocus>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group input-group-lg">
                                        <input id="password" name="password" type="password" class="form-control rounded-pill" required>
                                        <button id="togglePassword" type="button" class="btn btn-outline-secondary rounded-pill ms-2" title="Tampilkan kata sandi"><i class="fa fa-eye"></i></button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="remember" name="remember">
                                        <label class="form-check-label small" for="remember">Ingat saya</label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 btn-lg rounded-pill">Masuk</button>
                            </form>
                            <div class="mt-3 text-center small">
                                <a href="../index.php" class="text-decoration-none">Lihat situs publik</a>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center text-muted small py-3">
                            © <?= date('Y') ?> Intime Furniture — Admin Area
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include __DIR__ . '/partials/scripts.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const pwd = document.getElementById('password');
            const btn = document.getElementById('togglePassword');
            if (!pwd || !btn) return;
            btn.addEventListener('click', function(){
                if (pwd.type === 'password') { pwd.type = 'text'; btn.innerHTML = '<i class="fa fa-eye-slash"></i>'; }
                else { pwd.type = 'password'; btn.innerHTML = '<i class="fa fa-eye"></i>'; }
            });
        });
    </script>
</body>

</html>