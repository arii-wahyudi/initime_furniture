-- Hapus semua data produk dan kategori
-- Jalankan ini pada database Anda (backup dulu!)
START TRANSACTION;
TRUNCATE TABLE produk;
TRUNCATE TABLE kategori_produk;
-- Reset AUTO_INCREMENT jika perlu
ALTER TABLE produk AUTO_INCREMENT = 1;
ALTER TABLE kategori_produk AUTO_INCREMENT = 1;
COMMIT;

-- Jika ada tabel relasi lain (mis. produk_statistik) pastikan kaitannya sudah dipertimbangkan.
