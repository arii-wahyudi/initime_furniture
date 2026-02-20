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
 * Ambil gambar utama (primary) produk - gambar dengan urutan=0
 */
function get_product_primary_image($id_produk, $conn)
{
    $id_produk = intval($id_produk);
    // Gambar yang urutan=0 adalah gambar utama
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
 * urutan otomatis = urutan terakhir + 1 (jika null)
 */
function add_product_image($id_produk, $gambar_filename, $conn, $urutan = null)
{
    $id_produk = intval($id_produk);
    $gambar_filename = mysqli_real_escape_string($conn, $gambar_filename);
    
    // Auto detect urutan: cari urutan terbesar, +1
    if ($urutan === null) {
        $result = mysqli_query($conn, "SELECT MAX(urutan) as max_urutan FROM produk_gambar WHERE id_produk = $id_produk");
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $urutan = ($row['max_urutan'] !== null) ? ($row['max_urutan'] + 1) : 0;
        } else {
            $urutan = 0;
        }
    } else {
        $urutan = intval($urutan);
    }
    
    $query = "INSERT INTO produk_gambar (id_produk, gambar, urutan) VALUES ($id_produk, '$gambar_filename', $urutan)";
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
 * Set gambar sebagai primary/utama (urutan=0)
 * Gambar utama lama akan digeser ke urutan berikutnya
 */
function set_primary_image($id_gambar, $conn)
{
    $id_gambar = intval($id_gambar);
    
    // Ambil info gambar yang akan dijadikan utama
    $result = mysqli_query($conn, "SELECT id_produk, urutan FROM produk_gambar WHERE id = $id_gambar");
    if (!$result || mysqli_num_rows($result) == 0) {
        return false;
    }
    
    $gambar = mysqli_fetch_assoc($result);
    $id_produk = $gambar['id_produk'];
    $current_urutan = $gambar['urutan'];
    
    // Jika sudah urutan 0, tidak perlu update
    if ($current_urutan == 0) {
        return true;
    }
    
    // Cari gambar utama lama (urutan=0)
    $old_primary_result = mysqli_query($conn, "SELECT id FROM produk_gambar WHERE id_produk = $id_produk AND urutan = 0 LIMIT 1");
    
    if ($old_primary_result && mysqli_num_rows($old_primary_result) > 0) {
        $old = mysqli_fetch_assoc($old_primary_result);
        // Geser gambar utama lama ke urutan yang sebelumnya dipakai
        mysqli_query($conn, "UPDATE produk_gambar SET urutan = $current_urutan WHERE id = {$old['id']}");
    }
    
    // Set gambar baru jadi utama (urutan=0)
    return mysqli_query($conn, "UPDATE produk_gambar SET urutan = 0 WHERE id = $id_gambar");
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
