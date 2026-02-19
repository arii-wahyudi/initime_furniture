<?php
require __DIR__ . '/config.php';
require_admin();
require_post_method('settings.php');
require_valid_csrf();

$settings_dir = __DIR__ . '/../uploads/settings';

$fields = [
    'site_name',
    'carousel_1_title', 'carousel_1_desc',
    'carousel_2_title', 'carousel_2_desc',
    'about_title', 'about_desc',
    'about_exp_title', 'about_exp_desc',
    'about_team_title', 'about_team_desc',
    'about_fast_title', 'about_fast_desc',
    'testimonial_1_text', 'testimonial_1_name', 'testimonial_2_text', 'testimonial_2_name', 'testimonial_3_text', 'testimonial_3_name',
    'footer_text', 'footer_credit', 'instagram', 'whatsapp',
    'logo', 'carousel_1_image', 'carousel_2_image', 'about_image'
];

// Human-readable labels for settings keys
$labels = [
    'site_name' => 'Nama Situs',
    'logo' => 'Logo',
    'carousel_1_image' => 'Carousel 1 Image',
    'carousel_2_image' => 'Carousel 2 Image',
    'carousel_1_title' => 'Carousel 1 Title',
    'carousel_1_desc' => 'Carousel 1 Description',
    'carousel_2_title' => 'Carousel 2 Title',
    'carousel_2_desc' => 'Carousel 2 Description',
    'about_title' => 'About Title',
    'about_desc' => 'About Description',
    'about_exp_title' => 'About Experience Title',
    'about_exp_desc' => 'About Experience Description',
    'about_team_title' => 'About Team Title',
    'about_team_desc' => 'About Team Description',
    'about_fast_title' => 'About Fast Title',
    'about_fast_desc' => 'About Fast Description',
    'footer_text' => 'Footer Text',
    'footer_credit' => 'Footer Credit',
    'instagram' => 'Instagram',
    'whatsapp' => 'WhatsApp',
    'about_image' => 'About Image'
];

$changed = [];
foreach ($fields as $f) {
    if (!isset($_POST[$f])) continue;
    $val = trim($_POST[$f]);
    $esc = mysqli_real_escape_string($conn, $f);
    $cur = null;
    $res = mysqli_query($conn, "SELECT isi FROM settings WHERE nama_setting='" . $esc . "' LIMIT 1");
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $cur = $row['isi'];
        mysqli_free_result($res);
    }
    if ($cur !== $val) {
        db_upsert_setting($conn, $f, $val);
        $changed[] = $labels[$f] ?? $f;
    }
}

$img_keys = ['logo', 'carousel_1_image', 'carousel_2_image', 'about_exp_icon', 'about_team_icon', 'about_fast_icon', 'about_image'];
foreach ($img_keys as $k) {
    $file_input = $k . '_file';
    if (!empty($_FILES[$file_input]) && $_FILES[$file_input]['error'] === UPLOAD_ERR_OK) {
        $filename = handle_file_upload($_FILES[$file_input], $settings_dir, ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'], $k);
        if ($filename) db_upsert_setting($conn, $k, 'uploads/settings/' . $filename);
    }
}

// Handle kontak_toko updates (do not migrate kontak_toko into settings; keep table separate)
$contact_keys = ['shop_address' => 'alamat', 'shop_telepon' => 'telepon', 'shop_email' => 'email', 'shop_maps_embed' => 'maps_embed'];
$need_contact = false;
$contact_data = [];
foreach ($contact_keys as $postk => $col) {
    if (isset($_POST[$postk])) {
        $need_contact = true;
        $contact_data[$col] = trim($_POST[$postk]);
    }
}
if ($need_contact) {
    // escape values
    foreach ($contact_data as $col => $val) $contact_data[$col] = mysqli_real_escape_string($conn, $val);
    $res = mysqli_query($conn, "SELECT id FROM kontak_toko LIMIT 1");
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $sets = [];
        foreach ($contact_data as $col => $val) $sets[] = "$col='" . $val . "'";
        mysqli_query($conn, "UPDATE kontak_toko SET " . implode(',', $sets) . " WHERE id=" . intval($row['id']));
    } else {
        // insert new
        $cols = implode(',', array_keys($contact_data));
        $vals = "'" . implode("','", array_values($contact_data)) . "'";
        mysqli_query($conn, "INSERT INTO kontak_toko (" . $cols . ") VALUES (" . $vals . ")");
    }
}

// Determine which contact fields changed and add to $changed
if ($need_contact) {
    // fetch current kontak_toko
    $curContact = [];
    $cres = mysqli_query($conn, "SELECT alamat, telepon, email, maps_embed FROM kontak_toko LIMIT 1");
    if ($cres && mysqli_num_rows($cres) > 0) {
        $curContact = mysqli_fetch_assoc($cres);
        mysqli_free_result($cres);
    }
    foreach ($contact_keys as $postk => $col) {
        $newv = $contact_data[$col] ?? '';
        $oldv = $curContact[$col] ?? null;
        if ($oldv !== $newv) {
            $mapLabel = ['alamat' => 'Alamat', 'telepon' => 'Telepon', 'email' => 'Email', 'maps_embed' => 'Maps Embed'];
            $changed[] = $mapLabel[$col] ?? $postk;
        }
    }
}

// Redirect with message listing changed fields (if any)
if (!empty($changed)) {
    redirect_with_message('settings.php', 'success', 'Berhasil mengubah: ' . implode(', ', array_unique($changed)));
} else {
    redirect_with_message('settings.php', 'info', 'Tidak ada perubahan.');
}
