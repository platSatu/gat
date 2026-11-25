-- Tabel terjemahan (versi bahasa Inggris) untuk artikel & highlight.
-- Sama persis dengan migration database/migrations/2026_11_25_100000_create_translation_tables.php.
-- Boleh dijalankan manual di phpMyAdmin (database portal_gatsu) sebagai ganti `php artisan migrate`.
-- Aman dijalankan ulang: tabel yang sudah ada dilewati. Tabel lama (articles, highlights) tidak diubah.

CREATE TABLE IF NOT EXISTS `article_translations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `article_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `article_translations_article_id_locale_unique` (`article_id`, `locale`),
  CONSTRAINT `article_translations_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `highlight_translations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `highlight_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `highlight_translations_highlight_id_locale_unique` (`highlight_id`, `locale`),
  CONSTRAINT `highlight_translations_highlight_id_foreign` FOREIGN KEY (`highlight_id`) REFERENCES `highlights` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
