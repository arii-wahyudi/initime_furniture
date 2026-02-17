<?php
require __DIR__ . '/config.php';
require_admin();
require_post_method('admins.php');
require_valid_csrf();

$username = get_post_string('username');
$nama = get_post_string('nama');
$password = $_POST['password'] ?? '';

if (!$username || !$password) {
    die('Username dan password diperlukan');
}

$existing = db_fetch_one($conn, "SELECT id FROM admin WHERE username = ? LIMIT 1", [$username], 's');
if ($existing) {
    die('Username sudah digunakan');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
if (db_insert($conn, "INSERT INTO admin (username, password, nama) VALUES (?, ?, ?)", [$username, $hash, $nama], 'sss')) {
    redirect_with_message('admins.php', 'success', 'Admin berhasil ditambahkan');
} else {
    die('Gagal menambahkan admin');
}
