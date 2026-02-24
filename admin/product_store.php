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
    $used_first_additional_as_main = false;
    // If main image provided, process it. Otherwise try to use first additional image as main.
       if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        log_debug('Processing uploaded main image', [
            'filename' => $_FILES['gambar']['name'],
            'size' => $_FILES['gambar']['size'],
            'type' => $_FILES['gambar']['type']
        ]);

        $final_filename = handle_file_upload($_FILES['gambar'], $PRODUCTS_UPLOAD_DIR, ['image/jpeg', 'image/png', 'image/webp']);
        if (!$final_filename) {
            $error = 'Gagal menyimpan file utama';
            log_error($error);
            die($error);
        }
        log_debug('Main file uploaded successfully', ['filename' => $final_filename]);
    } elseif (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['tmp_name'][0]) && $_FILES['additional_images']['error'][0] === UPLOAD_ERR_OK) {
        // Use first additional image as main
        $tmp_file = $_FILES['additional_images']['tmp_name'][0];
        $file_name = $_FILES['additional_images']['name'][0];
        $file_type = $_FILES['additional_images']['type'][0];
        $file_size = $_FILES['additional_images']['size'][0];
        $tmp_file_array = [
            'name' => $file_name,
            'type' => $file_type,
            'tmp_name' => $tmp_file,
            'error' => UPLOAD_ERR_OK,
            'size' => $file_size
        ];
        $final_filename = handle_file_upload($tmp_file_array, $PRODUCTS_UPLOAD_DIR, ['image/jpeg', 'image/png', 'image/webp']);
        if ($final_filename) {
            $used_first_additional_as_main = true;
            log_debug('Used first additional image as main', ['filename' => $final_filename]);
        } else {
            log_error('Gagal menyimpan first additional as main');
        }
       } elseif (isset($_FILES['images']) && is_array($_FILES['images']['tmp_name']) && !empty($_FILES['images']['tmp_name'][0]) && $_FILES['images']['error'][0] === UPLOAD_ERR_OK) {
           // Support new form field name 'images[]' (used by admin/product_create.php)
           // Use first images[] as main
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
           $final_filename = handle_file_upload($tmp_file_array, $PRODUCTS_UPLOAD_DIR, ['image/jpeg', 'image/png', 'image/webp']);
           if ($final_filename) {
               $used_first_additional_as_main = true;
               log_debug('Used first images[] as main', ['filename' => $final_filename]);
           } else {
               log_error('Gagal menyimpan first images[] as main');
           }
    } else {
        // No main image provided; final_filename remains null (allowed)
        log_debug('No main image provided, continuing with null main image');
    }
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

// Handle slot uploads: prefer 'images[]' (new admin form) then fallback to 'additional_images' (old compat)
if ($inserted_id) {
    $filesArr = null;
    if (isset($_FILES['images']) && is_array($_FILES['images']['tmp_name'])) {
        $filesArr = &$_FILES['images'];
        log_debug('Using images[] array for multi-upload', []);
    } elseif (isset($_FILES['additional_images']) && is_array($_FILES['additional_images']['tmp_name'])) {
        $filesArr = &$_FILES['additional_images'];
        log_debug('Using additional_images array for multi-upload', []);
    }
    
    if ($filesArr && is_array($filesArr['tmp_name'])) {
        $image_count = count($filesArr['tmp_name']);
        log_debug('Processing all images', ['count' => $image_count, 'used_first_as_main' => !empty($used_first_additional_as_main)]);
        
        for ($i = 0; $i < $image_count; $i++) {
            // Skip index 0 if it was already used as main product image
            if (!empty($used_first_additional_as_main) && $i === 0) {
                log_debug("Skipping slot {$i} - already used as main product image");
                continue;
            }
            
            if ($filesArr['error'][$i] === UPLOAD_ERR_OK) {
                $tmp_file = $filesArr['tmp_name'][$i];
                $file_name = $filesArr['name'][$i];
                $file_type = $filesArr['type'][$i];
                $file_size = $filesArr['size'][$i];
                
                log_debug("Processing image slot {$i}", [
                    'filename' => $file_name,
                    'size' => $file_size,
                    'type' => $file_type
                ]);
                
                $tmp_file_array = [
                    'name' => $file_name,
                    'type' => $file_type,
                    'tmp_name' => $tmp_file,
                    'error' => UPLOAD_ERR_OK,
                    'size' => $file_size
                ];
                
                $filename = handle_file_upload($tmp_file_array, $PRODUCTS_UPLOAD_DIR, ['image/jpeg', 'image/png', 'image/webp']);
                if ($filename) {
                    // Save to produk_gambar with urutan = slot index
                    $result = add_product_image($inserted_id, $filename, $conn, $i);
                    if ($result) {
                        log_debug("Image slot {$i} saved to database", ['filename' => $filename, 'urutan' => $i]);
                    } else {
                        log_error("Failed to add image slot {$i} to produk_gambar", [
                            'filename' => $filename,
                            'product_id' => $inserted_id,
                            'urutan' => $i
                        ]);
                    }
                } else {
                    log_error("Failed to save image slot {$i} to filesystem", [
                        'filename' => $file_name,
                        'size' => $file_size
                    ]);
                }
            } else {
                log_debug("Skipping image slot {$i} - upload error code: " . $filesArr['error'][$i]);
            }
        }
    } else {
        log_debug('No additional images to process', []);
    }
}

redirect('products.php');
