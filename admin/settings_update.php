<?php
require __DIR__ . '/config.php';
require_admin();
require_post_method('settings.php');
require_valid_csrf();

$settings_dir = __DIR__ . '/../uploads/settings';
$fields = ['site_name', 'carousel_1_title', 'carousel_1_desc', 'carousel_2_title', 'carousel_2_desc', 'about_title', 'about_desc', 'testimonial_1_text', 'testimonial_1_name', 'testimonial_2_text', 'testimonial_2_name', 'testimonial_3_text', 'testimonial_3_name', 'footer_text', 'footer_credit', 'instagram', 'whatsapp', 'logo', 'carousel_1_image', 'carousel_2_image'];

foreach ($fields as $f) {
    if (isset($_POST[$f])) {
        db_upsert_setting($conn, $f, trim($_POST[$f]));
    }
}

$img_keys = ['logo', 'carousel_1_image', 'carousel_2_image'];
foreach ($img_keys as $k) {
    $file_input = $k . '_file';
    if (!empty($_FILES[$file_input]) && $_FILES[$file_input]['error'] === UPLOAD_ERR_OK) {
        $filename = handle_file_upload($_FILES[$file_input], $settings_dir, ['image/jpeg', 'image/png', 'image/webp'], $k);
        if ($filename) {
            db_upsert_setting($conn, $k, 'uploads/settings/' . $filename);
        }
    }
}

redirect('settings.php');
