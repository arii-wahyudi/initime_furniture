<?php
require __DIR__ . '/config.php';
require_admin();
require_post_method('products.php');
require_valid_csrf();

$id = get_post_int('id');
if (!$id) die('ID produk tidak valid');

$nama = get_post_string('nama_produk');
if (!$nama) die('Nama kosong');

$id_kategori = get_post_int('id_kategori');
$harga = get_post_float('harga');
$deskripsi = get_post_string('deskripsi');
$removebg = get_post_bool('removebg');
$preview_ai = get_post_string('preview_ai_data');

$row = db_fetch_one($conn, "SELECT gambar FROM produk WHERE id = ? LIMIT 1", [$id], 'i');
$oldfile = $row['gambar'] ?? '';
$final_filename = $oldfile;

if (!empty($preview_ai) && $removebg) {
    $final_filename = handle_base64_image($preview_ai, $PRODUCTS_UPLOAD_DIR, '-nobg');
    if (!$final_filename) die('Preview AI format invalid');
    if ($oldfile) delete_file($PRODUCTS_UPLOAD_DIR . '/' . $oldfile);
} elseif (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $filename = handle_file_upload($_FILES['gambar'], $PRODUCTS_UPLOAD_DIR, ['image/jpeg', 'image/png', 'image/webp']);
    if (!$filename) die('Gagal menyimpan file');
    $final_filename = $filename;
    if ($oldfile) delete_file($PRODUCTS_UPLOAD_DIR . '/' . $oldfile);
}

db_update(
    $conn,
    "UPDATE produk SET id_kategori = ?, nama_produk = ?, deskripsi = ?, harga = ?, gambar = ? WHERE id = ?",
    [$id_kategori, $nama, $deskripsi, $harga, $final_filename, $id],
    'issdsi'
);

redirect('products.php');
