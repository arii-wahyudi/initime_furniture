<?php
// Simple handler for contact form using PHPMailer
require __DIR__ . '/admin/config.php';

// load PHPMailer (make sure vendor directory exists, install with composer)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';
require __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $message) {
        $mail = new PHPMailer(true);
        try {
            // SMTP configuration - Hostinger example
            // Replace username/password with mailbox credentials (e.g. sale@intimefurniture.store)
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            // choose encryption: PHPMailer::ENCRYPTION_SMTPS (ssl) or PHPMailer::ENCRYPTION_STARTTLS
            $mail->SMTPSecure = SMTP_SECURE === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;

            // sender/recipient
            $mail->setFrom('no-reply@yourdomain.com', 'INTIME Furniture');
            // use toko contact email if configured
            $contactEmail = '';
            $res = mysqli_query($conn, "SELECT email FROM kontak_toko LIMIT 1");
            if ($res && mysqli_num_rows($res)) {
                $row = mysqli_fetch_assoc($res);
                $contactEmail = $row['email'];
            }
            if (!$contactEmail) {
                $contactEmail = 'furnitureintime@gmail.com';
            }
            $mail->addAddress($contactEmail);
            $mail->addReplyTo($email, $name);

            $mail->isHTML(false);
            $mail->Subject = 'Pesan dari situs INTIME Furniture';
            $body = "Nama: {$name}\nEmail: {$email}\n\nPesan:\n{$message}";
            $mail->Body = $body;

            $mail->send();
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Pesan Anda telah terkirim. Terima kasih!'];
        } catch (Exception $e) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal mengirim pesan: ' . $mail->ErrorInfo];
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Semua kolom wajib diisi.'];
    }
}

header('Location: index.php');
exit;
