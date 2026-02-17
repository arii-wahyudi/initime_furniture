<?php
// Public endpoint to record product events (view/click/search/wa_click)
// Accepts POST: product_id (int), event (string)
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => 0, 'error' => 'Only POST']);
    exit;
}

// product_id is optional for some events (search)
$id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$event = isset($_POST['event']) ? trim($_POST['event']) : '';
$allowed = ['view','click','search','wa_click'];
if (!in_array($event, $allowed)) {
    echo json_encode(['ok' => 0, 'error' => 'Invalid event']);
    exit;
}

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "INSERT INTO produk_statistik (id_produk, tipe_event) VALUES (?, ?)");
    if (!$stmt) {
        echo json_encode(['ok' => 0, 'error' => 'DB prepare failed']);
        exit;
    }
    mysqli_stmt_bind_param($stmt, 'is', $id, $event);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} else {
    // insert without product reference (for searches or generic events)
    $stmt = mysqli_prepare($conn, "INSERT INTO produk_statistik (tipe_event) VALUES (?)");
    if (!$stmt) {
        echo json_encode(['ok' => 0, 'error' => 'DB prepare failed']);
        exit;
    }
    mysqli_stmt_bind_param($stmt, 's', $event);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

if ($ok) echo json_encode(['ok' => 1]);
else echo json_encode(['ok' => 0, 'error' => 'DB insert failed']);
exit;
