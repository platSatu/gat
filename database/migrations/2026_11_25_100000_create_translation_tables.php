<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Terjemahan isi (bahasa selain Indonesia) untuk artikel & highlight.
 * Tabel lama `articles` dan `highlights` TIDAK diubah: isi Bahasa Indonesia tetap di sana (bahasa utama),
 * versi bahasa lain (sekarang: en) disimpan di tabel ini. Kolom kosong -> website memakai versi Indonesia.
 *
 * Database ini dipakai bersama website lama dan tabel `migrations`-nya bisa tidak lengkap, jadi:
 * - jalankan HANYA file ini:  php artisan migrate --path=database/migrations/2026_11_25_100000_create_translation_tables.php
 * - tabel yang sudah ada (mis. dibuat manual lewat database/sql/translation_tables.sql) dilewati, tidak diubah.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('article_translations')) {
            Schema::create('article_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
                $table->string('locale', 5);
                $table->string('title')->nullable();
                $table->longText('description')->nullable();
                $table->timestamps();
                $table->unique(['article_id', 'locale']);
            });
        }

        if (! Schema::hasTable('highlight_translations')) {
            Schema::create('highlight_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('highlight_id')->constrained('highlights')->cascadeOnDelete();
                $table->string('locale', 5);
                $table->string('title')->nullable();
                $table->longText('description')->nullable();
                $table->timestamps();
                $table->unique(['highlight_id', 'locale']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('highlight_translations');
        Schema::dropIfExists('article_translations');
    }
};
