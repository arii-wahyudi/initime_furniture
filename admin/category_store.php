<?php
require __DIR__ . '/config.php';
require_admin();
require_post_method('categories.php');
require_valid_csrf();

$nama = get_post_string('nama_kategori');
if (!$nama) die('Nama kategori diperlukan');

$slug = slugify($nama) . '-' . bin2hex(random_bytes(3));

$image_path = null;
$upload_dir = __DIR__ . '/../uploads/categories';
if (!empty($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
	$filename = handle_file_upload($_FILES['image_file'], $upload_dir, ['image/jpeg','image/png','image/webp','image/svg+xml'], 'cat');
	if ($filename) $image_path = 'uploads/categories/' . $filename;
}

if ($image_path) db_insert($conn, "INSERT INTO kategori_produk (nama_kategori, slug, image) VALUES (?, ?, ?)", [$nama, $slug, $image_path], 'sss');
else db_insert($conn, "INSERT INTO kategori_produk (nama_kategori, slug) VALUES (?, ?)", [$nama, $slug], 'ss');

redirect('categories.php');
