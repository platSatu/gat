<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Bahasa website + terjemahan teks tampilan (tabel BARU milik v2; tabel website lama tidak disentuh).
 *
 *   bahasa      : daftar bahasa (Indonesia = utama, tidak bisa dihapus). Tambah bahasa lewat admin Settings → Bahasa.
 *   terjemahan  : teks tampilan yang di kode ditulis __('Teks Indonesia'). kunci = teks Indonesia itu,
 *                 isi = JSON {"en": "...", "id": "pengganti teks Indonesia (opsional)", ...}.
 *
 * Saat dibuat, isi lang/en.json lama ikut dipindahkan ke tabel terjemahan.
 * Jalankan HANYA file ini:
 *   php artisan migrate --path=database/migrations/2026_11_27_400000_create_bahasa_terjemahan_tables.php
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        if (! Schema::hasTable('bahasa')) {
            Schema::create('bahasa', function (Blueprint $table) {
                $table->id();
                $table->string('kode', 10)->unique();       // id, en, zh, ...
                $table->string('nama');                     // nama yang tampil di tombol & admin, mis. "English"
                $table->boolean('utama')->default(false);   // hanya Indonesia
                $table->boolean('aktif')->default(true);    // tampil di website
                $table->unsignedInteger('urutan')->default(0);
                $table->timestamps();
            });

            DB::table('bahasa')->insert([
                ['kode' => 'id', 'nama' => 'Bahasa Indonesia', 'utama' => true, 'aktif' => true, 'urutan' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['kode' => 'en', 'nama' => 'English', 'utama' => false, 'aktif' => true, 'urutan' => 2, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (! Schema::hasTable('terjemahan')) {
            Schema::create('terjemahan', function (Blueprint $table) {
                $table->id();
                $table->char('hash', 32)->unique();         // md5(kunci), karena kunci bisa panjang
                $table->text('kunci');                      // teks Indonesia di __('...')
                $table->longText('isi')->nullable();        // JSON per bahasa
                $table->string('sumber')->nullable();       // file tempat teks ditemukan (dari tombol "Pindai teks")
                $table->timestamps();
            });

            $file = lang_path('en.json');
            $en = is_file($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
            $baris = [];
            foreach ($en as $kunci => $teks) {
                $baris[] = [
                    'hash' => md5($kunci), 'kunci' => $kunci, 'isi' => json_encode(['en' => $teks], JSON_UNESCAPED_UNICODE),
                    'sumber' => 'lang/en.json', 'created_at' => $now, 'updated_at' => $now,
                ];
            }
            foreach (array_chunk($baris, 200) as $potong) {
                DB::table('terjemahan')->insertOrIgnore($potong);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('terjemahan');
        Schema::dropIfExists('bahasa');
    }
};
