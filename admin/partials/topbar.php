<nav class="admin-topbar navbar navbar-expand bg-white border-bottom" style="z-index:1010;">
    <div class="container-fluid">
        <button class="btn btn-sm btn-outline-secondary d-md-none me-2" id="sidebarToggle">☰</button>
        <a class="navbar-brand fw-bold d-none d-md-block" href="index.php">Intime Furniture Admin</a>
        <img src="../assets/img/logo-wtext.png" alt="logo" width="44" height="44" class="ms-2 d-md-none">
        <div class="d-flex align-items-center ms-auto">
            <div class="me-3 small text-muted">Hai, <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></div>
            <div class="avatar bg-secondary rounded-circle text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;"><i class="fas fa-user"></i></div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        var btn = document.getElementById('sidebarToggle');
        var collapseBtn = document.getElementById('sidebarCollapse');
            function toggleMobileSidebar(){
                var aside = document.querySelector('.admin-sidebar');
                var backdrop = document.querySelector('.sidebar-backdrop');
                if (!aside) return;
                aside.classList.toggle('show');
                if (backdrop) backdrop.classList.toggle('d-none');
                if (backdrop) backdrop.classList.toggle('show');
            }

            // on desktop we keep sidebar fixed and do not auto-collapse
            if (btn) btn.addEventListener('click', function(){
                if (window.innerWidth < 768) toggleMobileSidebar();
            });

            // mobile close button inside sidebar
            var closeMobile = document.getElementById('sidebarCloseMobile');
            if (closeMobile) closeMobile.addEventListener('click', function(){ toggleMobileSidebar(); });

            // click on backdrop hides sidebar
            var backdrop = document.querySelector('.sidebar-backdrop');
            if (backdrop) backdrop.addEventListener('click', function(){ toggleMobileSidebar(); });
    });
</script>
