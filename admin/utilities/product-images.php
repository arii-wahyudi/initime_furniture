<?php
/**
 * Utility untuk menangani multiple images produk
 */

/**
 * Ambil semua gambar produk berdasarkan ID produk
 */
function get_product_images($id_produk, $conn)
{
    $id_produk = intval($id_produk);
    $result = mysqli_query($conn, "
        SELECT * FROM produk_gambar 
        WHERE id_produk = $id_produk 
        ORDER BY urutan ASC, is_primary DESC, id ASC
    ");
    
    if (!$result) {
        return [];
    }
    
    $images = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $images[] = $row;
    }
    
    return $images;
}

/**
 * Ambil gambar utama (primary) produk
 */
function get_product_primary_image($id_produk, $conn)
{
    $id_produk = intval($id_produk);
    $result = mysqli_query($conn, "
        SELECT * FROM produk_gambar 
        WHERE id_produk = $id_produk AND is_primary = 1
        LIMIT 1
    ");
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    // Fallback ke gambar pertama jika tidak ada primary
    $result = mysqli_query($conn, "
        SELECT * FROM produk_gambar 
        WHERE id_produk = $id_produk
        ORDER BY urutan ASC
        LIMIT 1
    ");
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Tambah gambar baru untuk produk
 */
function add_product_image($id_produk, $gambar_filename, $urutan = 0, $is_primary = 0, $conn)
{
    $id_produk = intval($id_produk);
    $gambar_filename = mysqli_real_escape_string($conn, $gambar_filename);
    $urutan = intval($urutan);
    $is_primary = intval($is_primary);
    
    // Jika ini primary dan ada primary lain, set yang lain jadi non-primary
    if ($is_primary == 1) {
        mysqli_query($conn, "
            UPDATE produk_gambar 
            SET is_primary = 0 
            WHERE id_produk = $id_produk
        ");
    }
    
    $query = "
        INSERT INTO produk_gambar (id_produk, gambar, urutan, is_primary)
        VALUES ($id_produk, '$gambar_filename', $urutan, $is_primary)
    ";
    
    return mysqli_query($conn, $query);
}

/**
 * Hapus gambar produk berdasarkan ID gambar
 */
function delete_product_image($id_gambar, $conn)
{
    $id_gambar = intval($id_gambar);
    
    // Ambil info gambar sebelum dihapus
    $result = mysqli_query($conn, "SELECT * FROM produk_gambar WHERE id = $id_gambar");
    if (!$result || mysqli_num_rows($result) == 0) {
        return false;
    }
    
    $gambar = mysqli_fetch_assoc($result);
    
    // Hapus dari database
    $deleted = mysqli_query($conn, "DELETE FROM produk_gambar WHERE id = $id_gambar");
    
    // Hapus file jika ada
    if ($deleted && !empty($gambar['gambar'])) {
        $file_path = dirname(__DIR__) . '/../' . $gambar['gambar'];
        if (file_exists($file_path)) {
            @unlink($file_path);
        }
    }
    
    return $deleted;
}

/**
 * Update urutan gambar
 */
function update_image_order($id_gambar, $urutan, $conn)
{
    $id_gambar = intval($id_gambar);
    $urutan = intval($urutan);
    
    return mysqli_query($conn, "
        UPDATE produk_gambar 
        SET urutan = $urutan 
        WHERE id = $id_gambar
    ");
}

/**
 * Set gambar sebagai primary (utama)
 */
function set_primary_image($id_gambar, $conn)
{
    $id_gambar = intval($id_gambar);
    
    // Ambil ID produk dari gambar
    $result = mysqli_query($conn, "SELECT id_produk FROM produk_gambar WHERE id = $id_gambar");
    if (!$result || mysqli_num_rows($result) == 0) {
        return false;
    }
    
    $gambar = mysqli_fetch_assoc($result);
    $id_produk = $gambar['id_produk'];
    
    // Set semua gambar produk jadi non-primary
    mysqli_query($conn, "UPDATE produk_gambar SET is_primary = 0 WHERE id_produk = $id_produk");
    
    // Set gambar ini sebagai primary
    return mysqli_query($conn, "UPDATE produk_gambar SET is_primary = 1 WHERE id = $id_gambar");
}

/**
 * Hitung total gambar produk
 */
function count_product_images($id_produk, $conn)
{
    $id_produk = intval($id_produk);
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM produk_gambar WHERE id_produk = $id_produk");
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    
    return 0;
}
?>
