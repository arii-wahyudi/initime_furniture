<?php
require __DIR__ . '/config.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$admin = null;
if ($id) {
    $stmt = mysqli_prepare($conn, "SELECT id, username, nama FROM admin WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $admin = mysqli_fetch_assoc($res);
}

$title = $id ? 'Edit Admin' : 'Tambah Admin';
include __DIR__ . '/partials/header.php';
?>
<body class="bg-light">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <?php include __DIR__ . '/partials/topbar.php'; ?>

    <main class="admin-main">
        <div class="container-fluid py-4" style="max-width:700px;">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= $title ?></h5>
                    <form method="post" action="<?= $id ? 'admin_update.php' : 'admin_store.php' ?>">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <?php if ($id): ?><input type="hidden" name="id" value="<?= (int)$id ?>"><?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($admin['username'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($admin['nama'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password <?php if ($id): ?><small class="text-muted">(kosongkan jika tidak ingin mengganti)</small><?php endif; ?></label>
                            <div class="input-group">
                                <input id="pw" type="password" name="password" class="form-control">
                                <button id="togglePw" type="button" class="btn btn-outline-secondary" title="Tampilkan kata sandi"><i class="fa fa-eye"></i></button>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary">Simpan</button>
                            <a href="admins.php" class="btn btn-link">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/scripts.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const pw = document.getElementById('pw');
            const btn = document.getElementById('togglePw');
            if (!pw || !btn) return;
            btn.addEventListener('click', function(){
                if (pw.type === 'password') { pw.type = 'text'; btn.innerHTML = '<i class="fa fa-eye-slash"></i>'; }
                else { pw.type = 'password'; btn.innerHTML = '<i class="fa fa-eye"></i>'; }
            });
        });
    </script>
</body>
</html>
