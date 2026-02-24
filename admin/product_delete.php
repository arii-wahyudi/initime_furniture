<?php
require __DIR__ . '/config.php';
require_admin();

$id = get_id('id');
if (!$id) redirect('products.php');

// Ensure utilities are available
if (!function_exists('delete_product_image')) {
    include_once __DIR__ . '/utilities/product-images.php';
}

$row = db_fetch_one($conn, "SELECT gambar FROM produk WHERE id = ? LIMIT 1", [$id], 'i');
if ($row && $row['gambar']) {
    // delete main image file if exists
    delete_file($PRODUCTS_UPLOAD_DIR . '/' . $row['gambar']);
}

// Delete any additional gallery images (produk_gambar)
$res = mysqli_query($conn, "SELECT id FROM produk_gambar WHERE id_produk = " . intval($id));
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        // use utility that deletes DB row and the file
        if (function_exists('delete_product_image')) {
            delete_product_image((int)$r['id'], $conn);
        } else {
            // fallback: delete row only
            mysqli_query($conn, "DELETE FROM produk_gambar WHERE id = " . intval($r['id']));
        }
    }
    mysqli_free_result($res);
}

db_delete($conn, "DELETE FROM produk WHERE id = ?", [$id], 'i');
redirect('products.php');
