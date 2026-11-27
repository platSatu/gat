<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rekap harian kunjungan untuk dashboard admin v2 (tabel BARU, hanya dipakai v2; tabel website lama tidak disentuh).
 * Diisi otomatis dari visitor_histories oleh App\Support\StatistikDashboard: setiap hari yang sudah lewat dihitung
 * sekali saja, jadi dashboard tetap cepat walau visitor_histories berisi jutaan baris.
 * Jalankan HANYA file ini:
 *   php artisan migrate --path=database/migrations/2026_11_27_300000_create_statistik_kunjungan_table.php
 * Aman dihapus kapan saja (isinya dihitung ulang otomatis).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('statistik_kunjungan')) {
            return;
        }

        Schema::create('statistik_kunjungan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('jenis', 20);          // kunjungan | unik | halaman | perangkat | sumber
            $table->string('kunci', 191)->default('');   // path halaman / jenis perangkat / sumber; kosong untuk total
            $table->unsignedInteger('jumlah')->default(0);
            $table->unique(['tanggal', 'jenis', 'kunci']);
            $table->index(['jenis', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistik_kunjungan');
    }
};
