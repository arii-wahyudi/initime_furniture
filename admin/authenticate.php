<?php
require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$csrf = $_POST['csrf'] ?? '';

if (!check_csrf($csrf)) {
    header('Location: login.php?error=csrf');
    exit;
}
// Attempt DB-backed authentication first
$stmt = mysqli_prepare($conn, "SELECT id, username, password FROM admin WHERE username = ? LIMIT 1");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $hash = $row['password'] ?? '';
        // If stored password looks like a bcrypt/argon hash, verify
        if (!empty($hash) && (password_verify($password, $hash) || $password === $hash)) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $row['username'];
            $_SESSION['admin_id'] = (int)$row['id'];
            header('Location: index.php');
            exit;
        }
    }
}

// Fallback to demo credentials in config.php (only if DB auth fails)
global $ADMIN_USERNAME, $ADMIN_PASSWORD;
if ($username === $ADMIN_USERNAME && $password === $ADMIN_PASSWORD) {
    session_regenerate_id(true);
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $username;
    header('Location: index.php');
    exit;
}

header('Location: login.php?error=invalid');
exit;
