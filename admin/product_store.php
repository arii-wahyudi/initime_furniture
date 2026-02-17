<?php
require __DIR__ . '/config.php';
require_admin();
require_post_method('product_upload.php');

if (empty($_POST) && !empty($_SERVER['CONTENT_LENGTH'])) {
    die('Request terlalu besar. Periksa konfigurasi PHP: post_max_size dan upload_max_filesize');
}

require_valid_csrf();

$nama = get_post_string('nama_produk');
$id_kategori = get_post_int('id_kategori');
$harga = get_post_float('harga');
$deskripsi = get_post_string('deskripsi');
$removebg = get_post_bool('removebg');
$preview_ai = get_post_string('preview_ai_data');

if (!$nama && !empty($_FILES['gambar']['name'])) {
    $nama = pathinfo($_FILES['gambar']['name'], PATHINFO_FILENAME);
}

if (!$nama) {
    die('Nama produk diperlukan');
}

$final_filename = null;

if (!empty($preview_ai) && $removebg) {
    $final_filename = handle_base64_image($preview_ai, $PRODUCTS_UPLOAD_DIR, '-nobg');
    if (!$final_filename) die('Preview AI format invalid');
} else {
    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
        die('Gambar diperlukan');
    }

    $final_filename = handle_file_upload($_FILES['gambar'], $PRODUCTS_UPLOAD_DIR, ['image/jpeg', 'image/png', 'image/webp']);
    if (!$final_filename) die('Gagal menyimpan file');
}

$slug = slugify($nama) . '-' . bin2hex(random_bytes(4));
db_insert(
    $conn,
    "INSERT INTO produk (id_kategori, nama_produk, slug, deskripsi, harga, gambar, status) VALUES (?, ?, ?, ?, ?, ?, 'aktif')",
    [$id_kategori, $nama, $slug, $deskripsi, $harga, $final_filename],
    'isssds'
);

redirect('products.php');
