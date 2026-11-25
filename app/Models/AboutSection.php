<?php

namespace App\Models;

use App\Models\Concerns\PunyaTerjemahan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Blok halaman "Tentang Kami" (tabel `about_sections`): tampil berurutan di /tentang, detail di /tentang/{slug}.
 * Teks di tabel ini = Bahasa Indonesia; versi Inggris di `about_section_translations` (pakai $blok->tr('name') dst.).
 * background = path di folder public (mis. images/about/x.jpg atau uploads/about/x.jpg).
 * tint = 1 -> gambar diwarnai biru oleh website; 0 -> gambar dipakai apa adanya (sudah biru dari desain).
 */
class AboutSection extends Model
{
    use PunyaTerjemahan;

    public const TERJEMAHAN = AboutSectionTranslation::class;

    protected $fillable = [
        'name', 'slug', 'subtitle', 'excerpt', 'description', 'background',
        'tint', 'button_text', 'link', 'sort_order', 'status',
    ];

    protected $casts = [
        'tint' => 'boolean',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static function kolomTerjemahan(): array
    {
        return ['name', 'subtitle', 'excerpt', 'description', 'button_text'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeUrut(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function getBackgroundUrlAttribute(): ?string
    {
        return Article::urlPublik($this->background);
    }

    /** Tujuan tombol: kolom link kalau diisi, kalau tidak halaman detail /tentang/{slug}. */
    public function getUrlAttribute(): string
    {
        $link = trim((string) $this->link);

        if ($link === '') {
            return route('about.show', $this);
        }

        return preg_match('#^(https?:)?//|^(mailto|tel):#i', $link) ? $link : url(ltrim($link, '/'));
    }

    public function tombol(): string
    {
        return $this->tr('button_text') ?: __('Lihat Selengkapnya');
    }

    /** Isi halaman detail (HTML) dalam bahasa pengunjung; kalau kosong pakai teks singkat. */
    public function getIsiAttribute(): string
    {
        $isi = Article::bersihkanHtml($this->tr('description'));

        return filled(trim(strip_tags($isi))) ? $isi : '<p>'.e($this->tr('excerpt')).'</p>';
    }
}
