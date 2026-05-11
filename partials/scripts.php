 <script src="assets/bootstrap/js/bootstrap.bundle.min.js" defer></script>
 <!-- AOS Animation Library -->
 <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
 <script>
     AOS.init({
         duration: 1000,
         once: true
     });
 </script>

 <!-- Custom JS -->
 <script src="assets/js/main.js" defer></script>
 <script src="assets/js/analytics.js" defer></script>
 
 <!-- Auto-scroll to product section when arriving from category/search or #product -->
 <script>
 document.addEventListener('DOMContentLoaded', function () {
     try {
         var shouldScroll = false;
         if (window.location.hash === '#product') shouldScroll = true;
         var params = new URLSearchParams(window.location.search);
         if (params.has('cat') || params.has('cat_slug') || params.has('q')) shouldScroll = true;

         if (shouldScroll) {
             var target = document.getElementById('product') || document.querySelector('.card');
             if (target) {
                 // slight delay to allow other scripts/layout to settle
                 setTimeout(function () {
                     target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                 }, 120);
             }
         }
     } catch (e) {
         console && console.error(e);
     }
 });
 </script>