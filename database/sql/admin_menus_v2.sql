-- Menambahkan nama menu admin v2 ke tabel `menus` (tabel lama, strukturnya tidak diubah).
-- Sama dengan migration database/migrations/2026_11_27_200000_add_v2_admin_menus.php.
-- Aman dijalankan ulang: nama yang sudah ada dilewati.

INSERT INTO `menus` (`name`, `created_at`, `updated_at`)
SELECT 'Page-Highlights', NOW(), NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `menus` WHERE `name` = 'Page-Highlights');

INSERT INTO `menus` (`name`, `created_at`, `updated_at`)
SELECT 'Page-Tentang-Kami', NOW(), NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `menus` WHERE `name` = 'Page-Tentang-Kami');

INSERT INTO `menus` (`name`, `created_at`, `updated_at`)
SELECT 'Page-Mitra', NOW(), NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `menus` WHERE `name` = 'Page-Mitra');
