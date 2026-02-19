-- Migration: add id_kategori to produk_statistik to support category events
ALTER TABLE produk_statistik
  ADD COLUMN id_kategori INT NULL AFTER id_produk;

-- Optionally add index for faster aggregation by category
CREATE INDEX IF NOT EXISTS idx_produk_statistik_id_kategori ON produk_statistik (id_kategori);
