<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan nama menu admin v2 yang belum ada ke tabel `menus` (tabel lama, strukturnya TIDAK diubah).
 * Hanya INSERT nama yang belum ada; aman dijalankan ulang. Tabel users/roles/setting_menu/cekrole tidak disentuh.
 * Jalankan HANYA file ini:
 *   php artisan migrate --path=database/migrations/2026_11_27_200000_add_v2_admin_menus.php
 * Atau manual lewat phpMyAdmin: database/sql/admin_menus_v2.sql
 */
return new class extends Migration
{
    private const MENU_BARU = ['Page-Highlights', 'Page-Tentang-Kami', 'Page-Mitra'];

    public function up(): void
    {
        if (! Schema::hasTable('menus')) {
            return;
        }

        $ada = DB::table('menus')->pluck('name')->map(fn ($n) => mb_strtolower(trim($n)))->all();
        $now = now();

        foreach (self::MENU_BARU as $nama) {
            if (! in_array(mb_strtolower($nama), $ada, true)) {
                DB::table('menus')->insert(['name' => $nama, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }

    public function down(): void
    {
        // Sengaja dibiarkan: nama menu mungkin sudah dicentang untuk user.
    }
};
