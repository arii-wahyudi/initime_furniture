<?php
require __DIR__ . '/config.php';
require_admin();
require_post_method('categories.php');
require_valid_csrf();

$id = get_post_int('id');
$nama = get_post_string('nama_kategori');

if (!$id || !$nama) {
    die('Input tidak valid');
}

$slug = slugify($nama) . '-' . bin2hex(random_bytes(3));
db_update($conn, "UPDATE kategori_produk SET nama_kategori = ?, slug = ? WHERE id = ?", [$nama, $slug, $id], 'ssi');

redirect('categories.php');
