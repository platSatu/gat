<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Banner (tabel `banners`). `image` berupa URL, atau file di disk "public"
 * (storage/app/public/...) yang tampil lewat /storage/... setelah `php artisan storage:link`.
 */
class Banner extends Model
{
    protected $table = 'banners';

    protected $fillable = ['image', 'title', 'description', 'status', 'posisi'];

    public function getImageUrlAttribute(): ?string
    {
        if (blank($this->image)) {
            return null;
        }

        return preg_match('#^https?://#i', $this->image) ? $this->image : Article::urlPublik('storage/'.ltrim($this->image, '/'));
    }
}
