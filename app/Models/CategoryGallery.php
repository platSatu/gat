<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Kategori galeri (tabel `category_gallery`). */
class CategoryGallery extends Model
{
    protected $table = 'category_gallery';

    protected $fillable = ['name', 'slug', 'status'];

    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class, 'category_id');
    }
}
