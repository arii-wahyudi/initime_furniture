<?php
require __DIR__ . '/config.php';
require_admin();

$id = get_id('id');
if (!$id) redirect('categories.php');

db_delete($conn, "DELETE FROM kategori_produk WHERE id = ?", [$id], 'i');
redirect('categories.php');
