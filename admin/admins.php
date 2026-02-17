<?php
require __DIR__ . '/config.php';
require_admin();

$title = 'Manajemen Akun Admin';
include __DIR__ . '/partials/header.php';
?>
<body class="bg-light">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <?php include __DIR__ . '/partials/topbar.php'; ?>

    <main class="admin-main">
        <div class="container-fluid py-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Akun Admin</h4>
                <a href="admin_edit.php" class="btn btn-primary btn-sm">Tambah Admin</a>
            </div>

            <div class="card">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Username</th>
                                    <th>Nama</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $res = mysqli_query($conn, "SELECT id, username, nama FROM admin ORDER BY id DESC");
                                if ($res) {
                                    $i = 1;
                                    while ($row = mysqli_fetch_assoc($res)):
                                ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= htmlspecialchars($row['username']) ?></td>
                                        <td><?= htmlspecialchars($row['nama']) ?></td>
                                        <td>
                                            <a href="admin_edit.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="admin_delete.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus akun admin ini?')">Hapus</a>
                                        </td>
                                    </tr>
                                <?php
                                    endwhile;
                                    mysqli_free_result($res);
                                } else {
                                ?>
                                    <tr><td colspan="4" class="text-muted small">Tidak ada akun.</td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>
