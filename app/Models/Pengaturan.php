<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Pengaturan website sederhana (tabel `pengaturan`, kunci → nilai), mis. video & gambar latar hero.
 * Pemakaian: Pengaturan::ambil('hero_video')  /  Pengaturan::simpan('hero_video', 'uploads/hero/x.mp4')
 */
class Pengaturan extends Model
{
    private const KUNCI_CACHE = 'pengaturan.semua';

    protected $table = 'pengaturan';

    protected $fillable = ['kunci', 'nilai'];

    private static ?array $semua = null;

    public static function ambil(string $kunci, ?string $bawaan = null): ?string
    {
        static::$semua ??= Cache::rememberForever(self::KUNCI_CACHE, function () {
            try {
                return Schema::hasTable('pengaturan') ? static::query()->pluck('nilai', 'kunci')->all() : [];
            } catch (Throwable $e) {
                report($e);

                return [];
            }
        });

        return filled(static::$semua[$kunci] ?? null) ? static::$semua[$kunci] : $bawaan;
    }

    public static function simpan(string $kunci, ?string $nilai): void
    {
        static::query()->updateOrCreate(['kunci' => $kunci], ['nilai' => $nilai]);
        static::$semua = null;
        Cache::forget(self::KUNCI_CACHE);
    }
}
