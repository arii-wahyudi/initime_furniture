<?php
require __DIR__ . '/config.php';
require_admin();

$id = get_id('id');
if (!$id) redirect('products.php');

$row = db_fetch_one($conn, "SELECT gambar FROM produk WHERE id = ? LIMIT 1", [$id], 'i');
if ($row && $row['gambar']) {
    delete_file($PRODUCTS_UPLOAD_DIR . '/' . $row['gambar']);
}

db_delete($conn, "DELETE FROM produk WHERE id = ?", [$id], 'i');
redirect('products.php');
