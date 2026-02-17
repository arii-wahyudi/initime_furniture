<?php

/**
 * UTILITY FUNCTIONS USAGE EXAMPLES
 * 
 * This file demonstrates how to use the refactored utility functions
 * in the admin folder. Copy these patterns to new files.
 */

// ============================================================================
// EXAMPLE 1: DATABASE OPERATIONS
// ============================================================================

// Single fetch
$admin = db_fetch_one($conn, "SELECT id, username FROM admin WHERE id = ?", [$admin_id], 'i');
if ($admin) {
    echo "Found: " . $admin['username'];
} else {
    echo "Not found";
}

// Multiple fetch
$categories = db_fetch_all($conn, "SELECT * FROM kategori_produk ORDER BY nama_kategori", [], '');
foreach ($categories as $cat) {
    echo $cat['nama_kategori'];
}

// Insert (with auto-increment)
$new_id = db_insert(
    $conn,
    "INSERT INTO kategori_produk (nama_kategori, slug) VALUES (?, ?)",
    [$name, $slug],
    'ss'
);
if ($new_id) {
    echo "Created ID: " . $new_id;
}

// Update
$affected = db_update(
    $conn,
    "UPDATE produk SET harga = ? WHERE id = ?",
    [$new_price, $product_id],
    'di'  // d = double, i = int
);
echo "$affected rows updated";

// Delete
$deleted = db_delete(
    $conn,
    "DELETE FROM admin WHERE id = ?",
    [$admin_id],
    'i'
);
echo "$deleted rows deleted";

// Upsert Setting (special case)
db_upsert_setting($conn, 'site_name', 'My Site');
db_upsert_setting($conn, 'footer_text', 'Copyright 2026');

// ============================================================================
// EXAMPLE 2: FILE HANDLING
// ============================================================================

// Upload file with validation
$target_dir = __DIR__ . '/../uploads/products';
$filename = handle_file_upload(
    $_FILES['image'],
    $target_dir,
    ['image/jpeg', 'image/png', 'image/webp'],
    'product'  // optional prefix
);
if ($filename) {
    // Save filename to database
    db_insert($conn, "INSERT INTO produk (gambar) VALUES (?", [$filename], 's');
} else {
    die('File upload failed');
}

// Save base64 image (e.g., from AI preview)
$base64_data = $_POST['preview_ai'];  // "data:image/png;base64,..."
$filename = handle_base64_image(
    $base64_data,
    $target_dir,
    '-background-removed'  // suffix
);
if ($filename) {
    echo "Saved: " . $filename;  // e.g., "a1b2c3d4-background-removed.png"
}

// Delete file safely
delete_file($PRODUCTS_UPLOAD_DIR . '/old-image.jpg');  // No error if doesn't exist

// Generate unique filename
$unique_name = generate_filename('jpg', 'thumbnail');  // "a1b2c3d4jpeg-thumbnail.jpg"

// ============================================================================
// EXAMPLE 3: INPUT VALIDATION
// ============================================================================

// Ensure POST method
require_post_method('product.php');  // Dies if not POST, redirects to product.php

// Validate CSRF token (required after require_post_method)
require_valid_csrf();  // Automatically checks $_POST['csrf']

// Get integer ID
$product_id = get_id('id');           // From $_GET['id'] or $_POST['id']
$category_id = get_id('cat_id', 5);   // With default value

// Get string inputs
$name = get_post_string('nama_produk');      // Trimmed
$description = get_post_string('desc', '');  // With default

// Get numbers
$price = get_post_float('harga');           // Float
$quantity = get_post_int('qty', 0);         // Int with default

// Get boolean
if (get_post_bool('active')) {
    echo "Checkbox was checked";
}

// Require multiple fields
require_post_fields(['nama_produk', 'harga', 'id_kategori']);  // Dies if missing

// ============================================================================
// EXAMPLE 4: REDIRECTS & MESSAGING
// ============================================================================

// Simple redirect
redirect('products.php');  // Equivalent to header + exit

// Redirect with message (shown on next page)
redirect_with_message('products.php', 'success', 'Product added successfully!');
redirect_with_message('products.php', 'error', 'Failed to add product');
redirect_with_message('products.php', 'info', 'Please check your input');

// Display message (in next page)
// <?= render_alert() 
?> // Renders Bootstrap alert, clears session message

// Manual message handling
// $msg = get_session_message(); // Returns ['type' => 'success', 'text' => '...'] or null
// if ($msg) {
// echo "Message: " . $msg['text'];
// }

// ============================================================================
// COMPLETE EXAMPLE: Creating a New Product
// ============================================================================

// require __DIR__ . '/config.php';
// require_admin();
// require_post_method('product_upload.php');
// require_valid_csrf();

// // Get inputs
// $name = get_post_string('nama_produk');
// $price = get_post_float('harga');
// $category_id = get_post_int('id_kategori');
// $description = get_post_string('deskripsi');

// // Validate
// if (!$name || !$price || !$category_id) {
// die('Data tidak lengkap');
// }

// // Handle file upload
// $filename = null;
// if (isset($_FILES['gambar'])) {
// $filename = handle_file_upload(
// $_FILES['gambar'],
// $PRODUCTS_UPLOAD_DIR,
// ['image/jpeg', 'image/png', 'image/webp']
// );
// if (!$filename) {
// die('Gagal upload gambar');
// }
// }

// // Insert to database
// $slug = slugify($name) . '-' . bin2hex(random_bytes(4));
// $product_id = db_insert($conn,
// "INSERT INTO produk (id_kategori, nama_produk, slug, deskripsi, harga, gambar, status)
// VALUES (?, ?, ?, ?, ?, ?, 'aktif')",
// [$category_id, $name, $slug, $description, $price, $filename],
// 'issdds' // i=int, s=string, d=double
// );

// if ($product_id) {
// redirect_with_message('products.php', 'success', 'Produk berhasil ditambahkan!');
// } else {
// redirect_with_message('products.php', 'error', 'Gagal menambahkan produk');
// }

// ============================================================================
// TYPE HINTS (For Future PHP 7.4+)
// ============================================================================

// When you add PHP 7.4+ support, use type hints:
// function db_fetch_one(mysqli $conn, string $query, array $params, string $types): ?array
// function handle_file_upload(array $file, string $target_dir, array $allowed_mimes, string $prefix = ''): ?string
// function redirect(string $url): void
// function get_post_string(string $key, string $default = ''): string

// ============================================================================
// NOTES
// ============================================================================

/*
1. Type strings for db functions:
- 'i' = integer
- 's' = string
- 'd' = double/float
- 'b' = blob

Example: 'idsds' means: int, double, string, double, string

2. MIME types for file upload:
- 'image/jpeg'
- 'image/png'
- 'image/webp'
- 'application/pdf'
- Add more as needed

3. Session messages:
- Use after redirect_with_message()
- Automatically cleared after render_alert()
- Perfect for multi-step forms

4. All validation functions die() on failure:
- require_post_method() redirects on failure
- require_valid_csrf() dies with HTTP 403
- require_post_fields() dies with HTTP 400

5. Safe by default:
- get_post_* functions trim strings
- Files checked for MIME type
- All inputs escaped in db functions
*/

?>
?>