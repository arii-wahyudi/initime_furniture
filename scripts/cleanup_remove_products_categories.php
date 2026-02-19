<?php
// Skrip ini menghapus semua file di uploads/products dan uploads/categories
// Gunakan dengan hati-hati. Jalankan: php scripts/cleanup_remove_products_categories.php

$base = __DIR__ . '/../initime_furniture/uploads';
$dirs = [
    $base . '/products',
    $base . '/categories'
];

$removed = [];
foreach ($dirs as $d) {
    if (!is_dir($d)) continue;
    $files = array_diff(scandir($d), ['.', '..']);
    foreach ($files as $f) {
        $path = $d . '/' . $f;
        if (is_file($path)) {
            if (@unlink($path)) $removed[] = $path;
        }
    }
}

if (empty($removed)) {
    echo "No files removed.\n";
} else {
    echo "Removed files:\n" . implode("\n", $removed) . "\n";
}
