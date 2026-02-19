<?php
require __DIR__ . '/config.php';
require_admin();

$title = 'Pengaturan Situs - Admin';
include __DIR__ . '/partials/header.php';

// Load all settings into associative array
$settings = [];
$res = mysqli_query($conn, "SELECT nama_setting, isi FROM settings");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) $settings[$r['nama_setting']] = $r['isi'];
    mysqli_free_result($res);
}
// Load toko contact from kontak_toko table so fields show current values
$res2 = mysqli_query($conn, "SELECT alamat, telepon, email, maps_embed FROM kontak_toko LIMIT 1");
if ($res2) {
    if ($ct = mysqli_fetch_assoc($res2)) {
        if (!empty($ct['alamat'])) $settings['shop_address'] = $ct['alamat'];
        if (!empty($ct['telepon'])) $settings['shop_telepon'] = $ct['telepon'];
        if (!empty($ct['email'])) $settings['shop_email'] = $ct['email'];
        if (!empty($ct['maps_embed'])) $settings['shop_maps_embed'] = $ct['maps_embed'];
    }
    mysqli_free_result($res2);
}
?>

<body class="bg-light">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <?php include __DIR__ . '/partials/topbar.php'; ?>

    <main class="admin-main">
        <div class="container-fluid py-4" style="max-width:1200px;">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Umum</h5>
                            <div class="card settings-card">
                                <div class="card-header">
                                    <div class="card-title">Umum</div>
                                    <div>
                                        <button class="btn btn-sm btn-outline-secondary collapse-toggle" data-bs-toggle="collapse" data-bs-target="#section-umum" aria-expanded="true"><i class="fa fa-chevron-down"></i></button>
                                    </div>
                                </div>
                                <div id="section-umum" class="collapse show">
                                    <div class="card-body">
                                        <form action="settings_update.php" method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Situs</label>
                                                <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Logo</label>
                                                <div class="mb-2">
                                                    <?php
                                                    $logoInfo = resolve_image_info($settings['logo'] ?? '', 'settings');
                                                    $logoUrl = htmlspecialchars($logoInfo['url']);
                                                    $logoExists = $logoInfo['exists'] ? 'Yes' : 'No';
                                                    ?>
                                                    <?php if (!empty($logoUrl)): ?><img id="preview_logo" src="<?= $logoUrl ?>" class="img-preview" style="display:block"><?php else: ?><img id="preview_logo" src="" class="img-preview" style="display:none"><?php endif; ?>
                                                </div>
                                                <div class="input-group mb-3">
                                                    <input type="file" id="logo_file" name="logo_file" class="form-control" accept="image/*" onchange="previewFile(this, 'preview_logo'); showFilename(this, 'logo_filename')">
                                                </div>
                                                <input type="text" readonly name="logo" class="form-control mt-2 bg-secondary-subtle" placeholder="Path atau filename logo (opsional)" value="<?= htmlspecialchars($settings['logo'] ?? '') ?>">
                                                <div class="form-help mt-2">Rekomendasi ukuran minimal 200x200px.
                                                    <br>
                                                    Jika upload, field path akan diisi otomatis.
                                                </div>
                                            </div>
                                            <div class="settings-save"><button class="btn btn-primary">Simpan</button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-3 settings-card">
                        <div class="card-header">
                            <div class="card-title">Tentang</div>
                            <div>
                                <button class="btn btn-sm btn-outline-secondary collapse-toggle" data-bs-toggle="collapse" data-bs-target="#section-tentang" aria-expanded="true"><i class="fa fa-chevron-down"></i></button>
                            </div>
                        </div>
                        <div id="section-tentang" class="collapse show">
                            <div class="card-body">
                                <form action="settings_update.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <div class="mb-3">
                                        <label class="form-label">About Title</label>
                                        <input type="text" name="about_title" class="form-control" value="<?= htmlspecialchars($settings['about_title'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">About Description</label>
                                        <textarea name="about_desc" class="form-control" rows="4"><?= htmlspecialchars($settings['about_desc'] ?? '') ?></textarea>
                                    </div>
                                    <hr>
                                    <h6>About Items (3) — Title, Short Desc & Icon</h6>
                                    <?php
                                    $about_items = [
                                        ['key' => 'about_exp', 'label' => 'Experience'],
                                        ['key' => 'about_team', 'label' => 'Team'],
                                        ['key' => 'about_fast', 'label' => 'Fast']
                                    ];
                                    foreach ($about_items as $it):
                                        $t = $it['key'] . '_title';
                                        $d = $it['key'] . '_desc';
                                        $ic = $it['key'] . '_icon';
                                        $iconInfo = resolve_image_info($settings[$ic] ?? '', 'settings');
                                        $iconUrl = htmlspecialchars($iconInfo['url']);
                                    ?>
                                        <div class="mb-3 row">
                                            <div class="col-md-6">
                                                <label class="form-label"><?= $it['label'] ?> Title</label>
                                                <input type="text" name="<?= $t ?>" class="form-control" value="<?= htmlspecialchars($settings[$t] ?? '') ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label"><?= $it['label'] ?> Short Desc</label>
                                                <input type="text" name="<?= $d ?>" class="form-control" value="<?= htmlspecialchars($settings[$d] ?? '') ?>">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label class="form-label">Icon Image (optional)</label>
                                                <div class="mb-2">
                                                    <?php if (!empty($iconUrl)): ?><img id="preview_<?= $ic ?>" src="<?= $iconUrl ?>" style="max-height:48px; display:block"><?php else: ?><img id="preview_<?= $ic ?>" src="" style="max-height:48px; display:none"><?php endif; ?>
                                                </div>
                                                <input type="file" name="<?= $ic ?>_file" class="form-control mb-2" accept="image/*" onchange="previewFile(this, 'preview_<?= $ic ?>')">
                                                <input type="text" name="<?= $ic ?>" class="form-control" value="<?= htmlspecialchars($settings[$ic] ?? '') ?>">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="settings-save"><button class="btn btn-primary">Simpan</button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card settings-card">
                        <div class="card-header">
                            <div class="card-title">Carousel</div>
                            <div>
                                <button class="btn btn-sm btn-outline-secondary collapse-toggle" data-bs-toggle="collapse" data-bs-target="#section-carousel" aria-expanded="true"><i class="fa fa-chevron-down"></i></button>
                            </div>
                        </div>
                        <div id="section-carousel" class="collapse show">
                            <div class="card-body">
                                <form action="settings_update.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <?php for ($i = 1; $i <= 2; $i++):
                                        $k = 'carousel_' . $i . '_image'; ?>
                                        <div class="mb-3">
                                            <label class="form-label">Carousel <?= $i ?> Image</label>
                                            <div class="mb-2">
                                                <?php $ci = resolve_image_info($settings[$k] ?? '', 'settings');
                                                $cui = htmlspecialchars($ci['url']);
                                                $ce = $ci['exists'] ? 'Yes' : 'No'; ?>
                                                <?php if (!empty($cui)): ?><img id="preview_<?= $k ?>" src="<?= $cui ?>" style="max-height:80px; display:block"><?php else: ?><img id="preview_<?= $k ?>" src="" style="max-height:80px; display:none"><?php endif; ?>
                                            </div>
                                            <input type="file" name="<?= $k ?>_file" class="form-control mb-2" accept="image/*" onchange="previewFile(this, 'preview_<?= $k ?>')">
                                            <input type="text" name="<?= $k ?>" class="form-control mb-2" value="<?= htmlspecialchars($settings[$k] ?? '') ?>">
                                            <label class="form-label">Title</label>
                                            <input type="text" name="carousel_<?= $i ?>_title" class="form-control mb-2" value="<?= htmlspecialchars($settings['carousel_' . $i . '_title'] ?? '') ?>">
                                            <label class="form-label">Description</label>
                                            <textarea name="carousel_<?= $i ?>_desc" class="form-control mb-2" rows="2"><?= htmlspecialchars($settings['carousel_' . $i . '_desc'] ?? '') ?></textarea>
                                        </div>
                                    <?php endfor; ?>
                                    <div class="settings-save"><button class="btn btn-primary">Simpan Carousel</button></div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3 settings-card">
                        <div class="card-header">
                            <div class="card-title">Testimonial</div>
                            <div>
                                <button class="btn btn-sm btn-outline-secondary collapse-toggle" data-bs-toggle="collapse" data-bs-target="#section-testimonial" aria-expanded="true"><i class="fa fa-chevron-down"></i></button>
                            </div>
                        </div>
                        <div id="section-testimonial" class="collapse show">
                            <div class="card-body">
                                <form action="settings_update.php" method="post">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <?php for ($i = 1; $i <= 3; $i++): ?>
                                        <div class="mb-3">
                                            <label class="form-label">Text <?= $i ?></label>
                                            <input type="text" name="testimonial_<?= $i ?>_text" class="form-control mb-2" value="<?= htmlspecialchars($settings['testimonial_' . $i . '_text'] ?? '') ?>">
                                            <label class="form-label">Name <?= $i ?></label>
                                            <input type="text" name="testimonial_<?= $i ?>_name" class="form-control" value="<?= htmlspecialchars($settings['testimonial_' . $i . '_name'] ?? '') ?>">
                                        </div>
                                    <?php endfor; ?>
                                    <div class="settings-save"><button class="btn btn-primary">Simpan Testimonial</button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card mt-3 settings-card">
                        <div class="card-header">
                            <div class="card-title">Kontak Toko</div>
                            <div>
                                <button class="btn btn-sm btn-outline-secondary collapse-toggle" data-bs-toggle="collapse" data-bs-target="#section-kontak" aria-expanded="true"><i class="fa fa-chevron-down"></i></button>
                            </div>
                        </div>
                        <div id="section-kontak" class="collapse show">
                            <div class="card-body">
                                <form action="settings_update.php" method="post">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Alamat</label>
                                        <textarea name="shop_address" class="form-control" rows="3"><?= htmlspecialchars($settings['shop_address'] ?? $settings['kontak_address'] ?? '') ?></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Telepon</label>
                                            <input type="text" name="shop_telepon" class="form-control" value="<?= htmlspecialchars($settings['shop_telepon'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="text" name="shop_email" class="form-control" value="<?= htmlspecialchars($settings['shop_email'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Maps Embed / Link</label>
                                            <input type="text" name="shop_maps_embed" class="form-control" value="<?= htmlspecialchars($settings['shop_maps_embed'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="settings-save"><button class="btn btn-primary">Simpan Kontak</button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="card-title">Footer & Sosial</h5>
                            <form action="settings_update.php" method="post">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Footer Text</label>
                                        <input type="text" name="footer_text" class="form-control" value="<?= htmlspecialchars($settings['footer_text'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Footer Credit</label>
                                        <input type="text" name="footer_credit" class="form-control" value="<?= htmlspecialchars($settings['footer_credit'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Instagram</label>
                                        <input type="text" name="instagram" class="form-control" value="<?= htmlspecialchars($settings['instagram'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">WhatsApp</label>
                                        <input type="text" name="whatsapp" class="form-control" value="<?= htmlspecialchars($settings['whatsapp'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="settings-save"><button class="btn btn-primary">Simpan Footer & Sosial</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/partials/scripts.php'; ?>
    <!-- SweetAlert2 for nicer alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php
    // If there's a session message, render it with SweetAlert
    $__msg = get_session_message();
    if ($__msg):
        $s_type = $__msg['type'] ?? 'info';
        $s_text = $__msg['text'] ?? '';
        $s_title = $s_type === 'success' ? 'Berhasil' : ($s_type === 'error' ? 'Gagal' : 'Informasi');
        $s_icon = $s_type === 'success' ? 'success' : ($s_type === 'error' ? 'error' : 'info');
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '<?= htmlspecialchars($s_icon) ?>',
                title: '<?= htmlspecialchars($s_title) ?>',
                text: '<?= htmlspecialchars($s_text) ?>',
                confirmButtonText: 'OK'
            });
        });
    </script>
    <?php endif; ?>
    <script>
        function previewFile(input, imgId) {
            const f = input.files && input.files[0];
            const img = document.getElementById(imgId);
            if (!f) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                img.style.display = 'block';
            };
            reader.readAsDataURL(f);
        }

        function showFilename(input, targetId) {
            const el = document.getElementById(targetId);
            if (!el) return;
            const f = input.files && input.files[0];
            el.textContent = f ? f.name : '';
        }

        // Rotate chevron on collapse toggle (Bootstrap 5 collapse events)
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.collapse').forEach(function(el) {
                el.addEventListener('show.bs.collapse', function(e) {
                    const btn = document.querySelector('[data-bs-target="#' + el.id + '"]');
                    if (btn) btn.classList.remove('collapsed');
                });
                el.addEventListener('hide.bs.collapse', function(e) {
                    const btn = document.querySelector('[data-bs-target="#' + el.id + '"]');
                    if (btn) btn.classList.add('collapsed');
                });
            });
        });
    </script>
</body>

</html>