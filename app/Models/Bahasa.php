<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Bahasa website (tabel `bahasa`). Indonesia = bahasa utama: kolom asli di semua tabel berbahasa Indonesia,
 * bahasa lain disimpan di tabel *_translations (konten) dan tabel `terjemahan` (teks tampilan).
 *
 * Daftar bahasa di-cache; kalau tabel belum dibuat, dipakai bawaan Indonesia + English.
 */
class Bahasa extends Model
{
    public const UTAMA = 'id';

    private const KUNCI_CACHE = 'bahasa.daftar';

    protected $table = 'bahasa';

    protected $fillable = ['kode', 'nama', 'utama', 'aktif', 'urutan'];

    protected $casts = [
        'utama' => 'boolean',
        'aktif' => 'boolean',
        'urutan' => 'integer',
    ];

    private static ?Collection $daftarCache = null;

    protected static function booted(): void
    {
        static::saved(fn () => static::lupakan());
        static::deleted(fn () => static::lupakan());
    }

    public static function lupakan(): void
    {
        static::$daftarCache = null;
        Cache::forget(self::KUNCI_CACHE);
    }

    /**
     * Semua bahasa aktif, urut (utama dulu). Isi tiap item: ['kode' => 'en', 'nama' => 'English', 'utama' => false].
     *
     * @return Collection<int, array{kode: string, nama: string, utama: bool}>
     */
    public static function daftar(): Collection
    {
        return static::$daftarCache ??= collect(Cache::rememberForever(self::KUNCI_CACHE, function () {
            try {
                if (Schema::hasTable('bahasa')) {
                    return static::query()->where('aktif', true)->orderByDesc('utama')->orderBy('urutan')->orderBy('id')
                        ->get(['kode', 'nama', 'utama'])
                        ->map(fn ($b) => ['kode' => $b->kode, 'nama' => $b->nama, 'utama' => (bool) $b->utama])->all();
                }
            } catch (Throwable $e) {
                report($e);
            }

            return [
                ['kode' => 'id', 'nama' => 'Bahasa Indonesia', 'utama' => true],
                ['kode' => 'en', 'nama' => 'English', 'utama' => false],
            ];
        }));
    }

    /** Kode bahasa aktif, mis. ['id', 'en']. */
    public static function kodeAktif(): array
    {
        return static::daftar()->pluck('kode')->all();
    }

    /** Bahasa selain Indonesia (untuk tab terjemahan di form admin). */
    public static function lain(): Collection
    {
        return static::daftar()->reject(fn ($b) => $b['kode'] === self::UTAMA)->values();
    }

    public static function nama(string $kode): string
    {
        return static::daftar()->firstWhere('kode', $kode)['nama'] ?? strtoupper($kode);
    }
}
