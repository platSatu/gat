<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

        // GAMBAR_URL diisi (mis. https://gbigatsu.id) → gambar diambil dari website lama
        $asal = config('gatsu.gambar_url');

        return $asal ? $asal.'/'.ltrim($path, '/') : asset(ltrim($path, '/'));
    }
}
