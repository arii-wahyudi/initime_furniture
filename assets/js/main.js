document.addEventListener("DOMContentLoaded", function () {
  const btnBar = document.getElementById("btn-bar");
  const btnTimes = document.getElementById("btn-times");
  const popMenu = document.getElementById("pop-menu");

  // Fungsi saat tombol Bar diklik (cek eksistensi elemen terlebih dahulu)
  if (btnBar && btnTimes && popMenu) {
    btnBar.addEventListener("click", function () {
      btnBar.classList.add("d-none"); // Sembunyikan Bar
      btnTimes.classList.remove("d-none"); // Munculkan Times
      popMenu.classList.remove("d-none"); // Munculkan Menu
    });

    // Fungsi saat tombol Times diklik
    btnTimes.addEventListener("click", function () {
      btnTimes.classList.add("d-none"); // Sembunyikan Times
      btnBar.classList.remove("d-none"); // Munculkan Bar
      popMenu.classList.add("d-none"); // Sembunyikan Menu
    });
  }
});
// Gunakan ScrollReveal hanya jika tersedia (beberapa halaman tidak memuat library ini)
if (typeof ScrollReveal !== 'undefined') {
  try {
    ScrollReveal().reveal('.reveal', {
      distance: '30px',
      duration: 600,
      easing: 'ease-in-out',
      origin: 'left',
      interval: 100,
    });
  } catch (e) {
    console.warn('ScrollReveal error:', e);
  }
}
// (Kategori dropdown feature removed) 
