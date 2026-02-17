<?php
require __DIR__ . '/config.php';
require_admin();

$id = get_id('id');
if (!$id) redirect('admins.php');

if (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $id) {
    redirect_with_message('admins.php', 'error', 'Tidak dapat menghapus admin yang sedang aktif');
}

if (db_delete($conn, "DELETE FROM admin WHERE id = ?", [$id], 'i') > 0) {
    redirect_with_message('admins.php', 'success', 'Admin berhasil dihapus');
} else {
    redirect_with_message('admins.php', 'error', 'Gagal menghapus admin');
}
