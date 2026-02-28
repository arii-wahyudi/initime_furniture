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

// detect whether any basic field changed before performing upload/update
$orig = db_fetch_one($conn, "SELECT id_kategori,nama_produk,deskripsi,harga,gambar FROM produk WHERE id=? LIMIT 1", [$id], 'i');
$changed = false;
if ($orig) {
    if ($orig['id_kategori'] != $id_kategori) $changed = true;
    if ($orig['nama_produk'] !== $nama) $changed = true;
    if ($orig['deskripsi'] !== $deskripsi) $changed = true;
    if ((float)$orig['harga'] != (float)$harga) $changed = true;
} else {
    $changed = true;
}

$row = db_fetch_one($conn, "SELECT gambar FROM produk WHERE id = ? LIMIT 1", [$id], 'i');
$oldfile = $row['gambar'] ?? '';
$final_filename = $oldfile;
$used_images0_as_main = false;

if (!empty($preview_ai) && $removebg) {
    $final_filename = handle_base64_image($preview_ai, $PRODUCTS_UPLOAD_DIR, '-nobg');
    if (!$final_filename) die('Preview AIformat invalid');
    if ($oldfile) delete_file($PRODUCTS_UPLOAD_DIR . '/' . $oldfile);
    if ($final_filename !== $oldfile) $changed = true;
} elseif (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $filename = handle_file_upload($_FILES['gambar'], $PRODUCTS_UPLOAD_DIR, ['image/jpeg', 'image/png', 'image/webp']);
    if (!$filename) die('Gagal menyimpan file');
    $final_filename = $filename;
    if ($oldfile) delete_file($PRODUCTS_UPLOAD_DIR . '/' . $oldfile);
    if ($final_filename !== $oldfile) $changed = true;
} elseif (isset($_FILES['images']) && is_array($_FILES['images']['tmp_name']) && !empty($_FILES['images']['tmp_name'][0]) && $_FILES['images']['error'][0] === UPLOAD_ERR_OK) {
    // Support images[] as first-slot main image when editing from admin UI.
    $tmp_file = $_FILES['images']['tmp_name'][0];
    $file_name = $_FILES['images']['name'][0];
    $file_type = $_FILES['images']['type'][0];
    $file_size = $_FILES['images']['size'][0];
    $tmp_file_array = [
        'name' => $file_name,
        'type' => $file_type,
        'tmp_name' => $tmp_file,
        'error' => UPLOAD_ERR_OK,
        'size' => $file_size
    ];
    $filename = handle_file_upload($tmp_file_array, $PRODUCTS_UPLOAD_DIR, ['image/jpeg', 'image/png', 'image/webp']);
    if (!$filename) die('Gagal menyimpan file utama dari images[]');
    $final_filename = $filename;
    $used_images0_as_main = true;
    if ($oldfile) delete_file($PRODUCTS_UPLOAD_DIR . '/' . $oldfile);
    if ($final_filename !== $oldfile) $changed = true;
}

db_update(
    $conn,
    "UPDATE produk SET id_kategori = ?, nama_produk = ?, deskripsi = ?, harga = ?, gambar = ? WHERE id = ?",
    [$id_kategori, $nama, $deskripsi, $harga, $final_filename, $id],
    'issdsi'
);

// Handle slot uploads: prefer 'images[]' (admin form) then fallback to 'additional_images' older name
$filesArr = null;
if (isset($_FILES['images']) && is_array($_FILES['images']['tmp_name'])) {
    $filesArr = &$_FILES['images'];
    log_debug('Using images[] array for edit multi-upload', []);
} elseif (isset($_FILES['additional_images']) && is_array($_FILES['additional_images']['tmp_name'])) {
    $filesArr = &$_FILES['additional_images'];
    log_debug('Using additional_images array for edit multi-upload', []);
}

$existingIds = isset($_POST['existing_image_ids']) && is_array($_POST['existing_image_ids']) ? $_POST['existing_image_ids'] : [];

if ($filesArr && is_array($filesArr['tmp_name'])) {
    $image_count = count($filesArr['tmp_name']);
    log_debug('Processing images for edit', ['count' => $image_count, 'used_slot_0_as_main' => !empty($used_images0_as_main)]);
    
    for ($i = 0; $i < $image_count; $i++) {
        // skip index 0 if it was already used as main
        if (!empty($used_images0_as_main) && $i === 0) {
            log_debug("Skipping slot {$i} on edit - already used as main product image");
            continue;
        }
        
        if ($filesArr['error'][$i] === UPLOAD_ERR_OK) {
            $tmp_file = $filesArr['tmp_name'][$i];
            $file_name = $filesArr['name'][$i];
            $file_type = $filesArr['type'][$i];
            
            log_debug("Processing edit image slot {$i}", [
                'filename' => $file_name,
                'type' => $file_type,
                'size' => $filesArr['size'][$i]
            ]);

            $tmp_file_array = [
                'name' => $file_name,
                'type' => $file_type,
                'tmp_name' => $tmp_file,
                'error' => UPLOAD_ERR_OK,
                'size' => $filesArr['size'][$i]
            ];

            $filename = handle_file_upload($tmp_file_array, $PRODUCTS_UPLOAD_DIR, ['image/jpeg', 'image/png', 'image/webp']);
            if ($filename) {
                $existingId = isset($existingIds[$i]) ? intval($existingIds[$i]) : 0;
                if ($existingId > 0) {
                    // replace existing db entry and delete old file if present
                    $res = db_fetch_one($conn, "SELECT gambar FROM produk_gambar WHERE id = ? LIMIT 1", [$existingId], 'i');
                    $oldG = $res['gambar'] ?? '';
                    mysqli_query($conn, "UPDATE produk_gambar SET gambar = '" . mysqli_real_escape_string($conn, $filename) . "' WHERE id = $existingId");
                    log_debug("Edit slot {$i} - replaced existing image", ['old_id' => $existingId, 'new_file' => $filename]);
                    if ($oldG) delete_file($PRODUCTS_UPLOAD_DIR . '/' . $oldG);
                    $changed = true;
                } else {
                    // add as new image with urutan equal to slot index when sensible
                    $result = add_product_image($id, $filename, $conn, $i);
                    log_debug("Edit slot {$i} - added new image", ['filename' => $filename, 'urutan' => $i]);
                    $changed = true;
                }
            } else {
                log_error("Failed to save edit image slot {$i}", ['filename' => $file_name]);
            }
        } else {
            log_debug("Skipping edit image slot {$i} - upload error: " . $filesArr['error'][$i]);
        }
    }
} else {
    log_debug('No additional images to process on edit', []);
}

// Redirect with feedback only if something actually changed
require_once __DIR__ . '/utilities/response.php';
if (!empty($changed)) {
    $_SESSION['message'] = [
        'type' => 'success',
        'text' => 'Produk berhasil diupdate.'
    ];
}
redirect('products.php');
