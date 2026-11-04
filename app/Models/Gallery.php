<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Foto galeri (tabel `galleries`). Kolom `image` hanya nama file;
 * filenya ada di public/uploads/gallery/.
 */
class Gallery extends Model
{
    protected $table = 'galleries';

    protected $fillable = ['category_id', 'image', 'status'];

    protected $casts = ['status' => 'integer'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryGallery::class, 'category_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (blank($this->image)) {
            return null;
        }

        return preg_match('#^https?://#i', $this->image) ? $this->image : Article::urlPublik('uploads/gallery/'.ltrim($this->image, '/'));
    }
}
