<?php
require __DIR__ . '/config.php';
require_admin();
require_post_method('categories.php');
require_valid_csrf();

$nama = get_post_string('nama_kategori');
if (!$nama) die('Nama kategori diperlukan');

$slug = slugify($nama) . '-' . bin2hex(random_bytes(3));
db_insert($conn, "INSERT INTO kategori_produk (nama_kategori, slug) VALUES (?, ?)", [$nama, $slug], 'ss');

redirect('categories.php');
