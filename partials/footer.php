<?php
// Dynamic footer: load categories and site settings when possible.
$cats = [];
$settings = [];
if (file_exists(__DIR__ . '/../admin/config.php')) {
  @include_once __DIR__ . '/../admin/config.php';
  if (!empty($conn)) {
    $r = mysqli_query($conn, "SELECT id, nama_kategori FROM kategori_produk ORDER BY nama_kategori LIMIT 8");
    if ($r) {
      while ($row = mysqli_fetch_assoc($r)) $cats[] = $row;
      mysqli_free_result($r);
    }

    $s = mysqli_query($conn, "SELECT nama_setting, isi FROM settings");
    if ($s) {
      while ($ss = mysqli_fetch_assoc($s)) $settings[$ss['nama_setting']] = $ss['isi'];
      mysqli_free_result($s);
    }
  }
}

$site_name = $settings['site_name'] ?? 'INTIME FURNITURE';
$address = $settings['site_address'] ?? 'Jl. Merdeka No.123, Jakarta, Indonesia';
$phone = $settings['contact_phone'] ?? ($settings['whatsapp'] ?? '+62 812 3456 7890');
$email = $settings['contact_email'] ?? 'company@example.com';
$instagram = $settings['instagram'] ?? '';
$whatsapp_raw = $settings['whatsapp'] ?? '';
$wa_number = preg_replace('/[^0-9]/', '', $whatsapp_raw ?: '628123456789');
$wa_text = rawurlencode('Halo, saya ingin bertanya');
$footer_credit = $settings['footer_credit'] ?? 'Developed by Desadroid';
?>

<?php
// Prefer toko contact data from `kontak_toko` table when available
if (!empty($conn)) {
  $kt = [];
  $rk = @mysqli_query($conn, "SELECT alamat, telepon, email, maps_embed FROM kontak_toko ORDER BY id DESC LIMIT 1");
  if ($rk) {
    $kt = mysqli_fetch_assoc($rk) ?: [];
    mysqli_free_result($rk);
  }

  if (!empty($kt['alamat'])) $address = $kt['alamat'];
  if (!empty($kt['telepon'])) $phone = $kt['telepon'];
  if (!empty($kt['email'])) $email = $kt['email'];
  // choose whatsapp number: prefer kontak_toko.telepon, then settings.whatsapp
  $tel_source = $kt['telepon'] ?? ($settings['whatsapp'] ?? $whatsapp_raw);
  if (!empty($tel_source)) {
    $tel = preg_replace('/[^0-9+]/', '', $tel_source);
    if ($tel !== '' && preg_match('/^0/', $tel)) {
      $tel = '62' . preg_replace('/^0+/', '', $tel);
    }
    $wa_number = preg_replace('/[^0-9]/', '', $tel);
  }
}

// display phone fallback
$phone_display = $phone ?? ($settings['whatsapp'] ?? '+62 812 3456 7890');
?>

<div class="bg-dark text-light" data-bs-theme="dark">
  <div class="row g-0 p-4">
    <div class="col-lg-4">
      <h2 class="fw-bold"><?= htmlspecialchars($site_name) ?></h2>
      <p>Solusi Kebutuhan Furniture Anda</p>
      <div class="p-2 bg-dark-subtle shadow text-light d-flex justify-content-center align-items-center rounded" style="width: 50px">
        <?php if (!empty($instagram)): ?>
          <a href="<?= htmlspecialchars($instagram) ?>" class="text-light"><i class="fab fa-instagram fs-1"></i></a>
        <?php else: ?>
          <i class="fab fa-instagram fs-1"></i>
        <?php endif; ?>
      </div>
    </div>
    <div class="col-lg-2 mt-5 mt-lg-0">
      <h4 class="fw-bold mb-3">Navigation</h4>
      <a href="/" class="nav-link mb-2">Dashboard</a>
      <a href="about_us.php" class="nav-link mb-2">Tentang Kami</a>
      <a href="product.php" class="nav-link">Produk</a>
    </div>
    <div class="col-lg-3 mt-5 mt-lg-0">
      <h4 class="fw-bold mb-3">Kategori</h4>
      <?php if (!empty($cats)): foreach ($cats as $c): ?>
          <a href="product.php?cat=<?= (int)$c['id'] ?>" class="nav-link mb-2"><?= htmlspecialchars($c['nama_kategori']) ?></a>
        <?php endforeach;
      else: ?>
        <a href="#" class="nav-link mb-2">Furniture Ruang Keluarga</a>
        <a href="#" class="nav-link mb-2">Furniture Kamar Tidur</a>
        <a href="#" class="nav-link">Furniture Ruang Belajar &amp; Bekerja</a>
      <?php endif; ?>
    </div>
    <div class="col-lg-3 mt-5 mt-lg-0">
      <h4 class="fw-bold mb-3">Kontak</h4>
      <?php if (!empty($settings['maps_embed']) || (!empty($kt['maps_embed'] ?? null))): ?>
        <?php $maps = $kt['maps_embed'] ?? ($settings['maps_embed'] ?? ''); ?>
        <a href="<?= htmlspecialchars($maps ?: '#') ?>" class="nav-link mb-2"><i class="fas fa-map-pin"></i> <?= htmlspecialchars($address) ?></a>
      <?php else: ?>
        <a href="#" class="nav-link mb-2"><i class="fas fa-map-pin"></i> <?= htmlspecialchars($address) ?></a>
      <?php endif; ?>
      <a href="https://wa.me/<?= htmlspecialchars($wa_number) ?>" class="nav-link mb-2"><i class="fas fa-phone"></i> <?= htmlspecialchars($phone_display) ?></a>
      <a href="mailto:<?= htmlspecialchars($email) ?>" class="nav-link"><i class="fas fa-envelope"></i> <?= htmlspecialchars($email) ?></a>
    </div>
  </div>

  <hr class="border-light" />
  <footer class="text-center py-4">
    <small>&copy; <?= date('Y') ?> <?= htmlspecialchars($site_name) ?>. All rights reserved.</small>
    <br>
    <a href="#" class="nav-link"><?= htmlspecialchars($footer_credit) ?></a>
  </footer>
</div>
<a href="https://wa.me/<?= htmlspecialchars($wa_number) ?>?text=<?= $wa_text ?>" class="wa-floating-btn" target="_blank">
  <i class="fab fa-whatsapp"></i>
</a>