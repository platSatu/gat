-- Tabel blok halaman "Tentang Kami" + terjemahan, beserta 4 blok awal (ID & EN).
-- Sama dengan migration database/migrations/2026_11_25_200000_create_about_sections_table.php.
-- Boleh dijalankan manual di phpMyAdmin (database portal_gatsu). Aman dijalankan ulang:
-- tabel yang sudah ada dilewati dan blok dengan slug yang sama tidak diisi dua kali. Tabel lain tidak diubah.

CREATE TABLE IF NOT EXISTS `about_sections` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `background` varchar(255) DEFAULT NULL,
  `tint` tinyint(1) NOT NULL DEFAULT 1,
  `button_text` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `about_sections_slug_unique` (`slug`),
  KEY `about_sections_status_sort_order_index` (`status`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `about_section_translations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `about_section_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `button_text` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `about_section_translations_about_section_id_locale_unique` (`about_section_id`, `locale`),
  CONSTRAINT `about_section_translations_about_section_id_foreign` FOREIGN KEY (`about_section_id`) REFERENCES `about_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4 blok awal (INSERT IGNORE: dilewati kalau slug sudah ada)
INSERT IGNORE INTO `about_sections` (`name`,`slug`,`subtitle`,`excerpt`,`description`,`background`,`tint`,`button_text`,`link`,`sort_order`,`status`,`created_at`,`updated_at`) VALUES ('Mengenal GBI Gatsu','mengenal-gbi-gatsu','Berawal dari sebuah panggilan','GBI Jl. Jend. Gatot Subroto, atau yang lebih dikenal sebagai GBI Gatsu, adalah gereja lokal di bawah naungan Sinode Gereja Bethel Indonesia. Berdiri sejak 4 September 1988 di Jakarta, GBI Gatsu terus bertumbuh menjadi sebuah keluarga besar gereja apostolik yang terdiri dari rayon, cabang khusus, dan gereja-gereja cabang di bawah pembinaannya.',NULL,'images/about/mengenal-figma.jpg',0,'Ikuti Perjalanan GBI Gatsu','tentang/sejarah',1,1,NOW(),NOW());
INSERT IGNORE INTO `about_sections` (`name`,`slug`,`subtitle`,`excerpt`,`description`,`background`,`tint`,`button_text`,`link`,`sort_order`,`status`,`created_at`,`updated_at`) VALUES ('Dipimpin dalam Ketaatan kepada Tuhan','dipimpin-dalam-ketaatan-kepada-tuhan',NULL,'GBI Gatsu digembalakan oleh Pdt. Dr. Ir. Niko Njotorahardjo sebagai Gembala Jemaat, Gembala Pembina, sekaligus Bapa Rohani. Perjalanan pelayanannya dimulai dari panggilan Tuhan untuk melayani secara penuh waktu dan membawa umat-Nya masuk ke dalam hadirat Tuhan melalui doa, pujian, dan penyembahan.',NULL,'images/about/dipimpin-figma.png',0,'Lihat Selengkapnya','tentang/sejarah',2,1,NOW(),NOW());
INSERT IGNORE INTO `about_sections` (`name`,`slug`,`subtitle`,`excerpt`,`description`,`background`,`tint`,`button_text`,`link`,`sort_order`,`status`,`created_at`,`updated_at`) VALUES ('Menghidupi Panggilan Restorasi Pondok Daud','menghidupi-panggilan-restorasi-pondok-daud',NULL,'Sejak awal, GBI Gatsu menerima panggilan khusus untuk merestorasi Pondok Daud. Panggilan ini diwujudkan dengan membangun prajurit-prajurit Tuhan yang gagah perkasa, yang memiliki gaya hidup doa, pujian, dan penyembahan dalam unity siang dan malam, serta melakukan kehendak Bapa pada zaman ini.',NULL,'images/about/restorasi-figma.png',0,'Pahami Panggilan yang Kami Hidupi','tentang/dna-kami',3,1,NOW(),NOW());
INSERT IGNORE INTO `about_sections` (`name`,`slug`,`subtitle`,`excerpt`,`description`,`background`,`tint`,`button_text`,`link`,`sort_order`,`status`,`created_at`,`updated_at`) VALUES ('Berakar dalam Firman, Melangkah dalam Iman','berakar-dalam-firman-melangkah-dalam-iman',NULL,'Sebagai bagian dari Gereja Bethel Indonesia, GBI Gatsu berpegang pada Alkitab sebagai dasar iman dan kehidupan. Setiap pengajaran, pelayanan, dan langkah gereja diarahkan untuk membawa umat semakin mengenal Kristus, hidup dalam kuasa Roh Kudus, bertumbuh dalam kebenaran firman Tuhan, dan mengerjakan Amanat Agung.',NULL,'images/about/berakar.jpg',1,'Kenali Dasar Iman Kami','tentang/dna-kami',4,1,NOW(),NOW());

-- Terjemahan English untuk 4 blok awal
INSERT IGNORE INTO `about_section_translations` (`about_section_id`,`locale`,`name`,`subtitle`,`excerpt`,`description`,`button_text`,`created_at`,`updated_at`) SELECT `id`,'en','Getting to Know GBI Gatsu','It began with a calling','GBI Jl. Jend. Gatot Subroto, better known as GBI Gatsu, is a local church under the Synod of Gereja Bethel Indonesia. Founded on 4 September 1988 in Jakarta, GBI Gatsu has grown into a large apostolic church family made up of districts, special branches, and branch churches under its oversight.',NULL,'Follow GBI Gatsu''s Journey',NOW(),NOW() FROM `about_sections` WHERE `slug` = 'mengenal-gbi-gatsu';
INSERT IGNORE INTO `about_section_translations` (`about_section_id`,`locale`,`name`,`subtitle`,`excerpt`,`description`,`button_text`,`created_at`,`updated_at`) SELECT `id`,'en','Led in Obedience to God',NULL,'GBI Gatsu is shepherded by Ps. Dr. Ir. Niko Njotorahardjo as Senior Pastor, Overseeing Pastor, and Spiritual Father. His ministry began with God''s call to serve full-time and to lead His people into God''s presence through prayer, praise, and worship.',NULL,'See More',NOW(),NOW() FROM `about_sections` WHERE `slug` = 'dipimpin-dalam-ketaatan-kepada-tuhan';
INSERT IGNORE INTO `about_section_translations` (`about_section_id`,`locale`,`name`,`subtitle`,`excerpt`,`description`,`button_text`,`created_at`,`updated_at`) SELECT `id`,'en','Living Out the Call to Restore the Tabernacle of David',NULL,'From the beginning, GBI Gatsu received a special calling to restore the Tabernacle of David. This calling is lived out by raising mighty warriors of God whose lifestyle is prayer, praise, and worship in unity day and night, doing the will of the Father in this generation.',NULL,'Understand the Calling We Live',NOW(),NOW() FROM `about_sections` WHERE `slug` = 'menghidupi-panggilan-restorasi-pondok-daud';
INSERT IGNORE INTO `about_section_translations` (`about_section_id`,`locale`,`name`,`subtitle`,`excerpt`,`description`,`button_text`,`created_at`,`updated_at`) SELECT `id`,'en','Rooted in the Word, Walking in Faith',NULL,'As part of Gereja Bethel Indonesia, GBI Gatsu holds to the Bible as the foundation of faith and life. Every teaching, ministry, and step of the church is directed toward helping people know Christ more, live in the power of the Holy Spirit, grow in the truth of God''s word, and fulfil the Great Commission.',NULL,'Know Our Statement of Faith',NOW(),NOW() FROM `about_sections` WHERE `slug` = 'berakar-dalam-firman-melangkah-dalam-iman';
