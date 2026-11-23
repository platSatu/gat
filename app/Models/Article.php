<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Artikel dari database lama (portal_gatsu, tabel `articles`).
 * Gambar disimpan sebagai path relatif dari folder public, mis. "images/judul-artikel.jpg".
 */
class Article extends Model
{
    protected $table = 'articles';

    protected $fillable = [
        'category_id', 'slug', 'title', 'description', 'image', 'status',
        'meta_title', 'meta_description', 'meta_keywords', 'read_count', 'date_post',
    ];

    protected $casts = [
        'status' => 'integer',
        'date_post' => 'date',
        'read_count' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** URL artikel memakai slug (kolom unik), bukan ID. */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** URL lengkap gambar artikel (null kalau tidak ada). */
    public function getImageUrlAttribute(): ?string
    {
        return self::urlPublik($this->image);
    }

    /** Hanya artikel yang sudah terbit (status = 1; status 0 = draf). */
    public function scopeTerbit(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function scopeTerpopuler(Builder $query): Builder
    {
        return $query->orderByDesc('read_count')->orderByDesc('id');
    }

    /** Ringkasan teks polos dari isi artikel (HTML), untuk kartu daftar. */
    public function ringkasan(int $panjang = 160): string
    {
        $teks = html_entity_decode(strip_tags((string) $this->description), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return Str::limit(trim(preg_replace('/\s+/u', ' ', $teks)), $panjang);
    }

    /** Isi artikel (HTML dari admin lama), dibersihkan dari <script>/<style>, atribut on*="..." dan link javascript:. */
    public function getIsiAttribute(): string
    {
        $html = (string) $this->description;
        $html = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', $html);
        $html = preg_replace('#\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $html);
        $html = preg_replace('#(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2#i', '$1="#"', $html);

        return $html;
    }

    public function scopeTerbaru(Builder $query): Builder
    {
        return $query->orderByDesc('date_post')->orderByDesc('id');
    }

    public static function urlPublik(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        // Gambar yang diunggah lewat admin baru ada di server ini sendiri → pakai yang lokal.
        if (is_file(public_path(ltrim($path, '/')))) {
            return asset(ltrim($path, '/'));
        }

        // GAMBAR_URL diisi (mis. https://gbigatsu.id) → gambar diambil dari website lama
        $asal = config('gatsu.gambar_url');

        return $asal ? $asal.'/'.ltrim($path, '/') : asset(ltrim($path, '/'));
    }
}
