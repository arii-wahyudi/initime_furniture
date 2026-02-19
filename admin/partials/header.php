<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $title ?></title>
    <link rel="icon" type="image/x-icon" href="../assets/img/logo.ico">

    <!-- Bootstrap CSS -->
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Fontawesome Icon -->
    <link rel="stylesheet" type="text/css" href="../assets/icon/css/all.css">

    <!-- AOS (Animate On Scroll) CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/custom.css" />

    <style>
        /* Small admin tweaks */
        body {
            background-color: #f8f9fa;
        }

        .card.rounded-4 {
            border-radius: 1rem;
        }
        /* Sidebar layout */
        .admin-sidebar { transition: transform .2s ease; width:280px; }
        .admin-sidebar.collapsed { transform: translateX(-300px); }
        .admin-main { margin-left: 280px; transition: margin .2s ease; }
        .admin-main.shifted { margin-left: 0 !important; }
        .admin-topbar { transition: margin .2s ease; margin-left:280px; }
        .admin-topbar.shifted { margin-left: 0 !important; }
        /* Mobile: hide sidebar by default, show when .show is present */
        @media (max-width: 767px) {
            .admin-sidebar { transform: translateX(-300px); position:fixed; left:0; top:0; bottom:0; }
            .admin-sidebar.show { transform: translateX(0); }
            .admin-main { margin-left: 0; }
            .admin-topbar { margin-left: 0; }
        }

        /* Bigger sidebar items */
        .admin-sidebar .nav-link { font-size: 15px; padding: 12px; color: #333; }
        .admin-sidebar .nav-link i { width: 22px; text-align: center; }
        .admin-sidebar .h6 { font-size: 18px; }

        /* Backdrop for mobile sidebar */
        .sidebar-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index:1015; display: none; }
        .sidebar-backdrop.show { display: block; }

        /* Ensure mobile close button looks good */
        .admin-sidebar .btn-close { background: #fff; border-radius: 6px; border: none; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        /* Settings cards: cleaner headers and responsive save */
        .settings-card .card-header { display:flex; align-items:center; justify-content:space-between; background:transparent; border-bottom:0; padding:0.75rem 1rem; }
        .settings-card .card-title { margin:0; font-weight:600; font-size:1rem; }
        .settings-card .card-body { padding:1rem; }
        .settings-save { display:flex; justify-content:flex-end; }
        @media(max-width:767px){ .settings-save .btn { width:100%; } }
        .collapse-toggle { border:none; background:transparent; color:#6c757d; }
        .collapse-toggle .fa { transition: transform .25s ease; }
        .collapse-toggle.collapsed .fa { transform: rotate(0deg); }
        .collapse-toggle:not(.collapsed) .fa { transform: rotate(180deg); }
        /* Image preview styling */
        .img-preview { height:56px; width:56px; object-fit:cover; border-radius:6px; border:1px solid rgba(0,0,0,0.06); }
        .form-help { font-size:0.85rem; color:#6c757d; }
        .input-filename { font-size:0.85rem; color:#495057; margin-top:6px; }
    </style>
</head>