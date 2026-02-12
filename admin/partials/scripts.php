<!-- Bootstrap JS -->
<script src="../assets/bootstrap/js/bootstrap.bundle.min.js" defer></script>
<!-- AOS JS (Animate On Scroll) -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js" defer></script>
<script>
    // small helper: focus first input when page loads, and init AOS
    document.addEventListener('DOMContentLoaded', function() {
        var el = document.querySelector('input[autofocus]');
        if (el) el.focus();
        if (window.AOS) {
            AOS.init({
                once: true,
                duration: 700,
                easing: 'ease-out-cubic'
            });
        }
    });
</script>
<!-- Custom JS -->
<script src="../assets/js/main.js" defer></script>