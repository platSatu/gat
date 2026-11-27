-- Tabel logo "Pelayanan dan Mitra Kami" (beranda), beserta 8 logo awal.
-- Sama dengan migration database/migrations/2026_11_27_100000_create_partners_table.php.
-- Boleh dijalankan manual di phpMyAdmin (database portal_gatsu). Tabel lain tidak diubah.
-- Jalankan SEKALI saja: bagian INSERT akan menambah baris lagi kalau dijalankan ulang.

CREATE TABLE IF NOT EXISTS `partners` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partners_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `partners` (`name`,`logo`,`description`,`website`,`status`,`created_at`,`updated_at`) VALUES
('Gereja Bethel Indonesia','images/partners/gbi.png',NULL,NULL,1,NOW(),NOW()),
('Empowered21','images/partners/e21.png',NULL,NULL,1,NOW(),NOW()),
('Everyone Asia','images/partners/everyone.png',NULL,NULL,1,NOW(),NOW()),
('STT Bethel The Way','images/partners/sttb.png',NULL,NULL,1,NOW(),NOW()),
('Healing Movement Ministry','images/partners/hmm.png',NULL,NULL,1,NOW(),NOW()),
('Jaringan Doa Nasional','images/partners/jdn.png',NULL,NULL,1,NOW(),NOW()),
('Sentul International Convention Center','images/partners/sicc.png',NULL,NULL,1,NOW(),NOW()),
('Graha Bina Asuh','images/partners/gba.png',NULL,NULL,1,NOW(),NOW());
