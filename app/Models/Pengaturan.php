<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Pengaturan website sederhana (tabel `pengaturan`, kunci → nilai): latar hero, SEO, integrasi Google, media sosial.
 * Bacaan untuk halaman publik lewat App\Support\Situs.
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
        static::simpanBanyak([$kunci => $nilai]);
    }

    /** Simpan beberapa pengaturan sekaligus: [kunci => nilai]. Nilai kosong disimpan sebagai null. */
    public static function simpanBanyak(array $data): void
    {
        foreach ($data as $kunci => $nilai) {
            $nilai = is_string($nilai) ? trim($nilai) : $nilai;
            static::query()->updateOrCreate(['kunci' => $kunci], ['nilai' => filled($nilai) ? (string) $nilai : null]);
        }
        static::$semua = null;
        Cache::forget(self::KUNCI_CACHE);
    }
}
