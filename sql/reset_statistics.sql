-- Reset produk_statistik table
-- Jalankan ini pada database Anda (mis. via phpMyAdmin atau mysql CLI)
START TRANSACTION;
TRUNCATE TABLE produk_statistik;
-- Jika ingin menyimpan struktur tetapi mereset AUTO_INCREMENT:
ALTER TABLE produk_statistik AUTO_INCREMENT = 1;
COMMIT;

-- Catatan: pastikan backup sebelum menjalankan di lingkungan produksi.
