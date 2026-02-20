<?php
/**
 * Handle product image actions: delete, set_primary
 */
require __DIR__ . '/config.php';
require_admin();

$action = trim((string)($_GET['action'] ?? ''));
$id = (int)($_GET['id'] ?? 0);
$product_id = (int)($_GET['product_id'] ?? 0);

if (!$action || !$id || !$product_id) {
    http_response_code(400);
    die('Invalid request');
}

switch ($action) {
    case 'delete':
        if (delete_product_image($id, $conn)) {
            $_SESSION['msg'] = 'Gambar berhasil dihapus';
            $_SESSION['msg_type'] = 'success';
        } else {
            $_SESSION['msg'] = 'Gagal menghapus gambar';
            $_SESSION['msg_type'] = 'danger';
        }
        header("Location: product_edit.php?id=$product_id");
        break;

    case 'set_primary':
        if (set_primary_image($id, $conn)) {
            $_SESSION['msg'] = 'Gambar berhasil diset sebagai utama';
            $_SESSION['msg_type'] = 'success';
        } else {
            $_SESSION['msg'] = 'Gagal mengset gambar sebagai utama';
            $_SESSION['msg_type'] = 'danger';
        }
        header("Location: product_edit.php?id=$product_id");
        break;

    default:
        http_response_code(400);
        die('Action not recognized');
}
?>
