<?php
/**
 * Remove background dari existing image
 * Endpoint: product_remove_bg_existing.php?image_path=uploads/products/filename.jpg
 */
require __DIR__ . '/config.php';

header('Content-Type: application/json');

$image_path = trim((string)($_POST['image_path'] ?? ''));

if (empty($image_path)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Image path is required']);
    exit;
}

// Security: prevent path traversal
$image_path = str_replace(['../', '..\\', "\0"], '', $image_path);

// Get full path
$file_path = dirname(__DIR__) . '/' . $image_path;

// Verify file exists and is in uploads folder
$real_path = realpath($file_path);
$uploads_dir = realpath(dirname(__DIR__) . '/uploads');

if (!$real_path || !$uploads_dir || strpos($real_path, $uploads_dir) !== 0 || !file_exists($real_path)) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'File not found']);
    exit;
}

// If no API key configured, skip AI processing
if (empty($REMOVE_BG_API_KEY)) {
    echo json_encode(['ok' => false, 'error' => 'Remove BG service not configured']);
    exit;
}

// Read file
$file_content = file_get_contents($real_path);
$base64_image = base64_encode($file_content);

// Call remove.bg API
$ch = curl_init('https://api.remove.bg/v1.0/removebg');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => ['image_file_b64' => $base64_image],
    CURLOPT_HTTPHEADER => ['X-API-Key: ' . $REMOVE_BG_API_KEY],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_BINARYTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(['ok' => false, 'error' => 'Remove.bg API error']);
    exit;
}

// Save processed image (overwrite atau with -nobg suffix)
$pathinfo = pathinfo($real_path);
$new_filename = $pathinfo['filename'] . '-nobg.' . $pathinfo['extension'];
$new_path = dirname($real_path) . '/' . $new_filename;

if (file_put_contents($new_path, $response) === false) {
    echo json_encode(['ok' => false, 'error' => 'Failed to save processed image']);
    exit;
}

// Update database if tracking  needed, then return new URL
$new_url = public_image_url(str_replace(dirname(__DIR__) . '/', '', $new_path), 'products');

echo json_encode([
    'ok' => true,
    'url' => $new_url,
    'filename' => $new_filename,
]);
?>
