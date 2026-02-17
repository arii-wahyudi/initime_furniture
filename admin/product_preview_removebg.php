<?php
// Endpoint: accepts a multipart file 'image' and returns JSON { ok:1, data: 'data:image/png;base64,...' }
require __DIR__ . '/config.php';
require_admin();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => 0, 'error' => 'Only POST']);
    exit;
}

if (empty($REMOVE_BG_API_KEY)) {
    echo json_encode(['ok' => 0, 'error' => 'Remove.bg API key not configured']);
    exit;
}

if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok' => 0, 'error' => 'No image uploaded']);
    exit;
}

$tmp = $_FILES['image']['tmp_name'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.remove.bg/v1.0/removebg');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
$cfile = new CURLFile($tmp, mime_content_type($tmp), basename($_FILES['image']['name']));
curl_setopt($ch, CURLOPT_POSTFIELDS, ['image_file' => $cfile, 'size' => 'auto']);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Api-Key: ' . $REMOVE_BG_API_KEY]);
$result = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($http !== 200 || !$result) {
    echo json_encode(['ok' => 0, 'error' => 'Remove.bg error', 'http' => $http, 'curl_error' => $err]);
    exit;
}

$b64 = base64_encode($result);
echo json_encode(['ok' => 1, 'data' => 'data:image/png;base64,' . $b64]);
exit;
