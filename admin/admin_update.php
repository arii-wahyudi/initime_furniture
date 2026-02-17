<?php
require __DIR__ . '/config.php';
require_admin();
require_post_method('admins.php');
require_valid_csrf();

$id = get_post_int('id');
$username = get_post_string('username');
$nama = get_post_string('nama');
$password = $_POST['password'] ?? '';

if (!$id || !$username) {
    die('Data tidak lengkap');
}

$existing = db_fetch_one($conn, "SELECT id FROM admin WHERE username = ? AND id != ? LIMIT 1", [$username, $id], 'si');
if ($existing) {
    die('Username sudah digunakan');
}

if (strlen($password) > 0) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    db_update($conn, "UPDATE admin SET username = ?, password = ?, nama = ? WHERE id = ?", [$username, $hash, $nama, $id], 'sssi');
} else {
    db_update($conn, "UPDATE admin SET username = ?, nama = ? WHERE id = ?", [$username, $nama, $id], 'ssi');
}

redirect_with_message('admins.php', 'success', 'Admin berhasil diperbarui');
