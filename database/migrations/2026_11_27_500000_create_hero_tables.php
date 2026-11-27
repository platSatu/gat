<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Hero beranda dari database (tabel BARU milik v2; tabel website lama tidak disentuh).
 *
 *   hero_slides              : kartu sambutan yang bisa digeser (teks Indonesia)
 *   hero_slide_translations  : versi bahasa lain
 *   pengaturan               : pengaturan website sederhana (kunci → nilai), dipakai untuk video/gambar latar hero
 *
 * Saat dibuat, isi hero yang sebelumnya tertulis di kode ikut diisi, dan menu "Page-Hero" ditambahkan
 * ke tabel menus (untuk Settings → Setting Menu).
 * Jalankan HANYA file ini:
 *   php artisan migrate --path=database/migrations/2026_11_27_500000_create_hero_tables.php
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        if (! Schema::hasTable('hero_slides')) {
            Schema::create('hero_slides', function (Blueprint $table) {
                $table->id();
                $table->string('label')->nullable();        // tulisan kecil di atas judul, mis. "Welcome to"
                $table->string('title');                    // judul besar
                $table->string('sub_label')->nullable();    // tulisan kecil kedua, mis. "Penggembalaan"
                $table->string('sub_title')->nullable();    // teks besar kedua, mis. nama gembala
                $table->string('button_text')->nullable();  // kosong = "Temukan Kisah Kami"
                $table->string('link')->nullable();         // kosong = halaman Tentang
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('status')->default(true);
                $table->timestamps();
            });

            $en = is_file(lang_path('en.json')) ? (json_decode(file_get_contents(lang_path('en.json')), true) ?: []) : [];
            $id = DB::table('hero_slides')->insertGetId([
                'label' => 'Welcome to', 'title' => 'GBI Jl. Jend. Gatot Subroto',
                'sub_label' => 'Penggembalaan', 'sub_title' => 'Pdt. DR. Ir. Niko Njotorahardjo',
                'button_text' => 'Temukan Kisah Kami', 'link' => 'tentang',
                'sort_order' => 1, 'status' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);

            Schema::create('hero_slide_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hero_slide_id')->constrained('hero_slides')->cascadeOnDelete();
                $table->string('locale', 10);
                $table->string('label')->nullable();
                $table->string('title')->nullable();
                $table->string('sub_label')->nullable();
                $table->string('sub_title')->nullable();
                $table->string('button_text')->nullable();
                $table->timestamps();
                $table->unique(['hero_slide_id', 'locale']);
            });

            DB::table('hero_slide_translations')->insert([
                'hero_slide_id' => $id, 'locale' => 'en',
                'label' => $en['Welcome to'] ?? 'Welcome to', 'title' => null,
                'sub_label' => $en['Penggembalaan'] ?? 'Senior Pastor', 'sub_title' => null,
                'button_text' => $en['Temukan Kisah Kami'] ?? 'Discover Our Story',
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        if (! Schema::hasTable('pengaturan')) {
            Schema::create('pengaturan', function (Blueprint $table) {
                $table->id();
                $table->string('kunci', 100)->unique();
                $table->text('nilai')->nullable();
                $table->timestamps();
            });
        }

        // Menu untuk hak akses (Settings → Setting Menu); tabel menus milik website lama, hanya ditambah satu baris.
        if (Schema::hasTable('menus') && ! DB::table('menus')->where('name', 'Page-Hero')->exists()) {
            DB::table('menus')->insert(['name' => 'Page-Hero', 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slide_translations');
        Schema::dropIfExists('hero_slides');
        Schema::dropIfExists('pengaturan');
    }
};
