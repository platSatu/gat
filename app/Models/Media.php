<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Media / video (tabel `media`): `url` = link video, `gambar` = path gambar di public. */
class Media extends Model
{
    protected $table = 'media';

    protected $fillable = ['category', 'title', 'gambar', 'url', 'date_post'];

    protected $casts = ['date_post' => 'date'];

    public function getGambarUrlAttribute(): ?string
    {
        return Article::urlPublik($this->gambar);
    }
}
