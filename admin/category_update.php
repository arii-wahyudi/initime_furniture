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

// handle optional image upload
$upload_dir = __DIR__ . '/../uploads/categories';
$image_path = null;
if (!empty($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
    $filename = handle_file_upload($_FILES['image_file'], $upload_dir, ['image/jpeg','image/png','image/webp','image/svg+xml'], 'cat');
    if ($filename) $image_path = 'uploads/categories/' . $filename;
}

if ($image_path) {
    db_update($conn, "UPDATE kategori_produk SET nama_kategori = ?, slug = ?, image = ? WHERE id = ?", [$nama, $slug, $image_path, $id], 'sssi');
} else {
    db_update($conn, "UPDATE kategori_produk SET nama_kategori = ?, slug = ? WHERE id = ?", [$nama, $slug, $id], 'ssi');
}

redirect('categories.php');
