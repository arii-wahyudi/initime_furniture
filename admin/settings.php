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
?>

<body class="bg-light">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <?php include __DIR__ . '/partials/topbar.php'; ?>

    <main class="admin-main">
        <div class="container-fluid py-4" style="max-width:1000px;">
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
                                                    <?php if (!empty($logoUrl)): ?><img id="preview_logo" src="<?= $logoUrl ?>" style="max-height:80px; display:block"><?php else: ?><img id="preview_logo" src="" style="max-height:80px; display:none"><?php endif; ?>
                                                </div>
                                                <input type="file" name="logo_file" class="form-control mb-2" accept="image/*" onchange="previewFile(this, 'preview_logo')">
                                                <input type="text" name="logo" class="form-control" value="<?= htmlspecialchars($settings['logo'] ?? '') ?>">
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
                                <form action="settings_update.php" method="post">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <div class="mb-3">
                                        <label class="form-label">About Title</label>
                                        <input type="text" name="about_title" class="form-control" value="<?= htmlspecialchars($settings['about_title'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">About Description</label>
                                        <textarea name="about_desc" class="form-control" rows="4"><?= htmlspecialchars($settings['about_desc'] ?? '') ?></textarea>
                                    </div>
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
                                            <label class="form-label">Short Phrase (3 words)</label>
                                            <input type="text" name="carousel_<?= $i ?>_phrase" class="form-control mb-2" value="<?= htmlspecialchars($settings['carousel_' . $i . '_phrase'] ?? '') ?>">
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
                    <div class="card">
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