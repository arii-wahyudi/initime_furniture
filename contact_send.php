<?php
require __DIR__ . '/admin/config.php';

// Simple contact form sender — POST: name, email, phone, message
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $message === '') {
    header('Location: index.php?sent=0');
    exit;
}

// Determine recipient email: prefer kontak_toko.email, then settings.contact_email
$recipient = null;
$kt = @mysqli_query($conn, "SELECT email FROM kontak_toko ORDER BY id DESC LIMIT 1");
if ($kt) {
    $row = mysqli_fetch_assoc($kt);
    if (!empty($row['email'])) $recipient = $row['email'];
}
if (empty($recipient)) {
    $rs = mysqli_query($conn, "SELECT isi FROM settings WHERE nama_setting='contact_email' LIMIT 1");
    if ($rs && mysqli_num_rows($rs) > 0) {
        $r = mysqli_fetch_assoc($rs);
        $recipient = $r['isi'];
    }
}
if (empty($recipient)) $recipient = 'company@example.com';

// Build email
$subject = "Pesan dari website: " . substr($name,0,100);
$body = "Anda menerima pesan melalui form kontak website:\n\n";
$body .= "Nama: " . $name . "\n";
$body .= "Email: " . $email . "\n";
$body .= "Telepon: " . $phone . "\n\n";
$body .= "Pesan:\n" . $message . "\n";

$headers = 'From: ' . $email . "\r\n" .
           'Reply-To: ' . $email . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

$sent = false;
try {
    $sent = mail($recipient, $subject, $body, $headers);
} catch (Exception $e) {
    $sent = false;
}

if ($sent) {
    header('Location: index.php?sent=1');
} else {
    header('Location: index.php?sent=0');
}
exit;
