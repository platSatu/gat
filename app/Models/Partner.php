<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Mitra / pelayanan di bagian "Pelayanan dan Mitra Kami" (beranda), tabel `partners`.
 * logo = path di folder public (mis. images/partners/gbi.png atau uploads/partners/x.png).
 * website diisi = logo di beranda bisa diklik dan membuka website mitra.
 */
class Partner extends Model
{
    protected $fillable = ['name', 'logo', 'description', 'website', 'status'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return Article::urlPublik($this->logo);
    }

    /** Mitra aktif untuk beranda; null kalau tabel belum dibuat (beranda memakai daftar lama). */
    public static function untukBeranda(): ?Collection
    {
        return Schema::hasTable('partners') ? static::aktif()->orderBy('id')->get() : null;
    }
}
