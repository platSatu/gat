<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Kategori artikel (tabel `categories`). */
class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = ['name'];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /** Kategori yang punya artikel terbit, beserta jumlahnya (artikel_count), urut nama. */
    public static function untukArtikel()
    {
        return static::query()
            ->withCount(['articles as artikel_count' => fn ($q) => $q->terbit()])
            ->orderBy('name')
            ->get(['id', 'name'])
            ->where('artikel_count', '>', 0)
            ->values();
    }
}
