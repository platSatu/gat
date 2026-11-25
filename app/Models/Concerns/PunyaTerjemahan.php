<?php

namespace App\Models\Concerns;

use App\Http\Middleware\SetLocale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Schema;

/**
 * Isi dua bahasa untuk model yang punya tabel terjemahan (Article, Highlight).
 *
 * - Kolom di tabel utama = Bahasa Indonesia (bahasa utama, tidak diubah).
 * - $model->tr('title') di halaman publik: versi bahasa yang sedang dipilih kalau sudah diisi,
 *   kalau belum -> versi Indonesia. Admin tetap memakai $model->title (selalu Indonesia).
 *
 * Model yang memakai trait ini harus punya konstanta TERJEMAHAN = nama class model terjemahan.
 *
 * Kalau tabel terjemahan belum dibuat (migration/SQL belum dijalankan), semuanya tetap jalan
 * dalam Bahasa Indonesia; tidak ada error.
 */
trait PunyaTerjemahan
{
    /** @var array<string, bool> cache cek tabel per request */
    private static array $tabelAda = [];

    /** Apakah tabel terjemahan untuk model ini sudah ada di database. */
    public static function terjemahanSiap(): bool
    {
        $tabel = (new (static::TERJEMAHAN))->getTable();

        return self::$tabelAda[$tabel] ??= Schema::hasTable($tabel);
    }

    /** Kolom yang bisa diterjemahkan. */
    public static function kolomTerjemahan(): array
    {
        return ['title', 'description'];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(static::TERJEMAHAN);
    }

    /** Terjemahan untuk bahasa yang sedang dipakai (untuk eager load di halaman publik). */
    public function terjemahanAktif(): HasOne
    {
        return $this->hasOne(static::TERJEMAHAN)->where('locale', app()->getLocale());
    }

    /** Muat terjemahan bahasa aktif sekaligus (hanya kalau bukan Bahasa Indonesia, jadi tidak ada query tambahan). */
    public function scopeDenganTerjemahan(Builder $query): Builder
    {
        return static::bahasaUtama() || ! static::terjemahanSiap() ? $query : $query->with('terjemahanAktif');
    }

    public static function bahasaUtama(): bool
    {
        return app()->getLocale() === SetLocale::DEFAULT;
    }

    /** Nilai kolom dalam bahasa aktif, dengan cadangan Bahasa Indonesia. */
    public function tr(string $kolom): mixed
    {
        $asli = $this->getAttribute($kolom);

        if (static::bahasaUtama() || ! in_array($kolom, static::kolomTerjemahan(), true) || ! static::terjemahanSiap()) {
            return $asli;
        }

        $nilai = $this->terjemahanAktif?->getAttribute($kolom);

        return filled(trim(strip_tags((string) $nilai))) ? $nilai : $asli;
    }

    /** Terjemahan satu bahasa (untuk form admin); model kosong kalau belum ada. */
    public function terjemahan(string $locale): Model
    {
        if (! $this->exists || ! static::terjemahanSiap()) {
            return new (static::TERJEMAHAN)(['locale' => $locale]);
        }

        return $this->translations->firstWhere('locale', $locale)
            ?? new (static::TERJEMAHAN)(['locale' => $locale]);
    }

    /** Simpan terjemahan dari form admin. Semua isian kosong -> terjemahan dihapus. */
    public function simpanTerjemahan(string $locale, array $data): void
    {
        if (! static::terjemahanSiap()) {
            return;
        }

        $data = collect(static::kolomTerjemahan())
            ->mapWithKeys(fn ($k) => [$k => filled(trim(strip_tags((string) ($data[$k] ?? '')))) ? $data[$k] : null])
            ->all();

        if (collect($data)->filter()->isEmpty()) {
            $this->translations()->where('locale', $locale)->delete();

            return;
        }

        $this->translations()->updateOrCreate(['locale' => $locale], $data);
    }
}
