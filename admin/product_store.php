<?php
require __DIR__ . '/config.php';
require_admin();
require_post_method('product_upload.php');

// Log request untuk debugging
log_request_data();

if (empty($_POST) && !empty($_SERVER['CONTENT_LENGTH'])) {
    $error = 'Request terlalu besar. Periksa konfigurasi PHP: post_max_size dan upload_max_filesize';
    log_error($error, ['content_length' => $_SERVER['CONTENT_LENGTH']]);
    die($error);
}

require_valid_csrf();

$nama = get_post_string('nama_produk');
$id_kategori = get_post_int('id_kategori');
$harga = get_post_float('harga');
$deskripsi = get_post_string('deskripsi');
$removebg = get_post_bool('removebg');
$preview_ai = get_post_string('preview_ai_data');

log_debug('Product store input data', [
    'nama' => $nama,
    'id_kategori' => $id_kategori,
    'harga' => $harga,
    'removebg' => $removebg,
    'has_preview_ai' => !empty($preview_ai),
    'has_gambar_file' => isset($_FILES['gambar']) && !empty($_FILES['gambar']['name'])
]);

if (!$nama && !empty($_FILES['gambar']['name'])) {
    $nama = pathinfo($_FILES['gambar']['name'], PATHINFO_FILENAME);
}

if (!$nama) {
    $error = 'Nama produk diperlukan';
    log_error($error);
    die($error);
}

$final_filename = null;

if (!empty($preview_ai) && $removebg) {
    log_debug('Processing AI preview image');
    $final_filename = handle_base64_image($preview_ai, $PRODUCTS_UPLOAD_DIR, '-nobg');
    if (!$final_filename) {
        $error = 'Preview AI format invalid';
        log_error($error);
        die($error);
    }
    log_debug('AI preview processed', ['filename' => $final_filename]);
} else {
    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Gambar diperlukan atau error upload: ' . ($_FILES['gambar']['error'] ?? 'File not provided');
        log_error($error, ['file_error' => $_FILES['gambar']['error'] ?? null]);
        die($error);
    }

    log_debug('Processing uploaded image', [
        'filename' => $_FILES['gambar']['name'],
        'size' => $_FILES['gambar']['size'],
        'type' => $_FILES['gambar']['type']
    ]);
    
    $final_filename = handle_file_upload($_FILES['gambar'], $PRODUCTS_UPLOAD_DIR, ['image/jpeg', 'image/png', 'image/webp']);
    if (!$final_filename) {
        $error = 'Gagal menyimpan file';
        log_error($error);
        die($error);
    }
    log_debug('File uploaded successfully', ['filename' => $final_filename]);
}

$slug = slugify($nama) . '-' . bin2hex(random_bytes(4));
log_debug('Creating product with slug', ['slug' => $slug, 'nama' => $nama]);

$inserted_id = db_insert(
    $conn,
    "INSERT INTO produk (id_kategori, nama_produk, slug, deskripsi, harga, gambar, status) VALUES (?, ?, ?, ?, ?, ?, 'aktif')",
    [$id_kategori, $nama, $slug, $deskripsi, $harga, $final_filename],
    'isssds'
);

if (!$inserted_id) {
    $error = 'Gagal menyimpan produk ke database';
    log_error($error, [
        'db_error' => mysqli_error($conn),
        'nama' => $nama,
        'slug' => $slug
    ]);
    die($error);
}

log_debug('Product created successfully', ['product_id' => $inserted_id]);

// Handle additional images upload (multiple images support)
if ($inserted_id && isset($_FILES['additional_images']) && is_array($_FILES['additional_images']['tmp_name'])) {
    $image_count = count($_FILES['additional_images']['tmp_name']);
    log_debug('Processing additional images', ['count' => $image_count]);
    
    for ($i = 0; $i < $image_count; $i++) {
        if ($_FILES['additional_images']['error'][$i] === UPLOAD_ERR_OK) {
            $tmp_file = $_FILES['additional_images']['tmp_name'][$i];
            $file_name = $_FILES['additional_images']['name'][$i];
            $file_type = $_FILES['additional_images']['type'][$i];
            
            log_debug("Processing additional image {$i}", [
                'filename' => $file_name,
                'type' => $file_type
            ]);
            
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
                $result = add_product_image($inserted_id, $filename, $conn);
                if ($result) {
                    log_debug("Additional image {$i} saved", ['filename' => $filename]);
                } else {
                    log_error("Failed to add image to database for product {$inserted_id}", [
                        'filename' => $filename,
                        'index' => $i
                    ]);
                }
            } else {
                log_error("Failed to process additional image {$i}", [
                    'filename' => $file_name,
                    'size' => $_FILES['additional_images']['size'][$i]
                ]);
            }
        } else {
            log_debug("Skipping additional image {$i} - upload error: " . $_FILES['additional_images']['error'][$i]);
        }
    }
}

redirect('products.php');
