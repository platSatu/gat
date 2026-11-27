<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Logo "Pelayanan dan Mitra Kami" di beranda (tabel `partners`).
 * Hanya MEMBUAT tabel baru; tabel lain tidak disentuh. Kalau tabel sudah ada, dilewati (aman dijalankan ulang).
 * Jalankan HANYA file ini:
 *   php artisan migrate --path=database/migrations/2026_11_27_100000_create_partners_table.php
 * Atau manual lewat phpMyAdmin: database/sql/partners.sql (isinya sama).
 *
 * Saat tabel baru dibuat, 8 logo yang sebelumnya tertulis di kode ikut diisi (website dikosongkan).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('partners')) {
            return;
        }

        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // nama mitra (juga teks alt logo)
            $table->string('logo');                    // path gambar di public/, mis. uploads/partners/x.png
            $table->text('description')->nullable();   // keterangan singkat (muncul saat logo disorot)
            $table->string('website')->nullable();     // diisi = logo bisa diklik ke website mitra
            $table->boolean('status')->default(true);  // 1 = active, 0 = inactive
            $table->timestamps();
            $table->index('status');
        });

        $now = now();
        $awal = [
            'gbi' => 'Gereja Bethel Indonesia',
            'e21' => 'Empowered21',
            'everyone' => 'Everyone Asia',
            'sttb' => 'STT Bethel The Way',
            'hmm' => 'Healing Movement Ministry',
            'jdn' => 'Jaringan Doa Nasional',
            'sicc' => 'Sentul International Convention Center',
            'gba' => 'Graha Bina Asuh',
        ];

        foreach ($awal as $file => $name) {
            DB::table('partners')->insert([
                'name' => $name, 'logo' => "images/partners/{$file}.png", 'description' => null, 'website' => null,
                'status' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
