<?php $cur = basename($_SERVER['PHP_SELF']); ?>
<aside class="admin-sidebar bg-white border-end position-fixed h-100 d-flex flex-column" style="width:280px; z-index:1020;">
    <button id="sidebarCloseMobile" class="btn-close d-md-none position-absolute" aria-label="Close" style="right:12px;top:12px"></button>
    <div class="p-3 d-flex align-items-center">
        <img src="../assets/img/logo-wtext.png" alt="logo" width="44" height="44" class="me-2">
        <div>
            <div class="h6 mb-0">Intime Admin</div>
            <small class="text-muted">Panel</small>
        </div>
    </div>
    <hr class="my-1">
    <nav class="nav flex-column px-2">
        <a class="nav-link d-flex align-items-center py-3 rounded <?= $cur==='index.php' ? 'bg-light' : '' ?>" href="index.php"><i class="fa fa-tachometer-alt me-3"></i> Dashboard</a>
        <a class="nav-link d-flex align-items-center py-3 rounded <?= $cur==='products.php' ? 'bg-light' : '' ?>" href="products.php"><i class="fa fa-boxes me-3"></i> Produk</a>
        <a class="nav-link d-flex align-items-center py-3 rounded <?= $cur==='categories.php' ? 'bg-light' : '' ?>" href="categories.php"><i class="fa fa-list me-3"></i> Kategori</a>
        <a class="nav-link d-flex align-items-center py-3 rounded <?= $cur==='settings.php' ? 'bg-light' : '' ?>" href="settings.php"><i class="fa fa-cog me-3"></i> Settings</a>
        <a class="nav-link d-flex align-items-center py-3 rounded <?= $cur==='admins.php' ? 'bg-light' : '' ?>" href="admins.php"><i class="fa fa-user-shield me-3"></i> Admins</a>
    </nav>

    <div class="mt-auto p-3">
        <a href="logout.php" class="btn btn-outline-danger w-100 btn-sm">Logout</a>
    </div>
</aside>
<div class="sidebar-backdrop d-none"></div>
