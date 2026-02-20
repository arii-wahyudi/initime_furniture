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

// Handle additional images upload (multiple images support)
if (isset($_FILES['additional_images']) && is_array($_FILES['additional_images']['tmp_name'])) {
    $image_count = count($_FILES['additional_images']['tmp_name']);
    
    for ($i = 0; $i < $image_count; $i++) {
        if ($_FILES['additional_images']['error'][$i] === UPLOAD_ERR_OK) {
            $tmp_file = $_FILES['additional_images']['tmp_name'][$i];
            $file_name = $_FILES['additional_images']['name'][$i];
            $file_type = $_FILES['additional_images']['type'][$i];
            
            // Create a temporary array to use with handle_file_upload
            $tmp_file_array = [
                'name' => $file_name,
                'type' => $file_type,
                'tmp_name' => $tmp_file,
                'error' => UPLOAD_ERR_OK,
                'size' => $_FILES['additional_images']['size'][$i]
            ];
            
            $filename = handle_file_upload($tmp_file_array, $PRODUCTS_UPLOAD_DIR, ['image/jpeg', 'image/png', 'image/webp']);
            if ($filename) {
                add_product_image($id, $filename, $conn);
            }
        }
    }
}

redirect('products.php');
