<?php

namespace App\Models;

use App\Models\Concerns\PunyaTerjemahan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Kartu sambutan di hero beranda (tabel `hero_slides`), bisa lebih dari satu dan digeser.
 * Teks di tabel ini = Bahasa Indonesia; bahasa lain di `hero_slide_translations` ($slide->tr('title')).
 * Video/gambar latar hero ada di tabel `pengaturan` (lihat App\Models\Pengaturan).
 */
class HeroSlide extends Model
{
    use PunyaTerjemahan;

    public const TERJEMAHAN = HeroSlideTranslation::class;

    protected $fillable = ['label', 'title', 'sub_label', 'sub_title', 'button_text', 'link', 'sort_order', 'status'];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static function kolomTerjemahan(): array
    {
        return ['label', 'title', 'sub_label', 'sub_title', 'button_text'];
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeUrut(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /** Tujuan tombol: kolom link (alamat di website ini atau https://...), kosong = halaman Tentang. */
    public function getUrlAttribute(): string
    {
        $link = trim((string) $this->link);

        if ($link === '') {
            return route('about');
        }

        return preg_match('#^(https?:)?//|^(mailto|tel):#i', $link) ? $link : url(ltrim($link, '/'));
    }

    public function tombol(): string
    {
        return $this->tr('button_text') ?: __('Temukan Kisah Kami');
    }
}
