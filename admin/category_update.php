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

// handle remove image checkbox
if (!empty($_POST['remove_image']) && $_POST['remove_image'] == '1') {
    // fetch current image path and delete file
    $q = mysqli_prepare($conn, "SELECT image FROM kategori_produk WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($q, 'i', $id);
    mysqli_stmt_execute($q);
    $res = mysqli_stmt_get_result($q);
    $row = mysqli_fetch_assoc($res);
    if (!empty($row['image'])) {
        $fs = resolve_image_info($row['image'], 'categories')['fs'] ?? null;
        if ($fs) @unlink($fs);
    }
    db_update($conn, "UPDATE kategori_produk SET image = NULL WHERE id = ?", [$id], 'i');
}

redirect('categories.php');
