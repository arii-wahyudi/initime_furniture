<?php
/**
 * Handle product image actions: delete, set_primary
 */
require __DIR__ . '/config.php';
require_admin();
require_once __DIR__ . '/utilities/response.php';

$action = trim((string)($_GET['action'] ?? ''));
$id = isset($_GET['id']) ? (int)($_GET['id'] ?? 0) : null;
$product_id = (int)($_GET['product_id'] ?? 0);

if (!$action || !$product_id) {
    http_response_code(400);
    die('Invalid request');
}

switch ($action) {
    case 'delete':
        // If id > 0 -> delete from produk_gambar table
        if ($id && $id > 0) {
            if (delete_product_image($id, $conn)) {
                $_SESSION['message'] = [ 'type' => 'success', 'text' => 'Gambar berhasil dihapus' ];
            } else {
                $_SESSION['message'] = [ 'type' => 'error', 'text' => 'Gagal menghapus gambar' ];
            }
            redirect("product_edit.php?id=$product_id");
            break;
        }

        // If id is 0 or not provided -> treat as deleting main image stored in produk.gambar
        $prod = db_fetch_one($conn, "SELECT gambar FROM produk WHERE id = ? LIMIT 1", [$product_id], 'i');
        $old = $prod['gambar'] ?? '';
        $deletedMain = false;
        if (!empty($old)) {
            // Attempt to remove file from uploads/products or stored path
            $basename = basename($old);
            $paths = [
                dirname(__DIR__) . '/../' . $old,
                dirname(__DIR__) . '/../uploads/products/' . $basename,
                dirname(__DIR__) . '/../' . $basename
            ];
            foreach ($paths as $p) {
                if (file_exists($p) && is_file($p)) {
                    @unlink($p);
                    $deletedMain = true;
                    break;
                }
            }
        }
        // Clear produk.gambar regardless of file removal result
        mysqli_query($conn, "UPDATE produk SET gambar = '' WHERE id = " . intval($product_id));
        $_SESSION['message'] = [ 'type' => 'success', 'text' => 'Gambar utama berhasil dihapus' ];
        redirect("product_edit.php?id=$product_id");
        break;

    case 'set_primary':
        if (set_primary_image($id, $conn)) {
            $_SESSION['message'] = [
                'type' => 'success',
                'text' => 'Gambar berhasil diset sebagai utama'
            ];
        } else {
            $_SESSION['message'] = [
                'type' => 'error',
                'text' => 'Gagal mengset gambar sebagai utama'
            ];
        }
        redirect("product_edit.php?id=$product_id");
        break;

    default:
        http_response_code(400);
        die('Action not recognized');
}
?>
