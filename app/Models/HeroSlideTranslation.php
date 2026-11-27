<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Versi bahasa lain dari kartu hero beranda (tabel `hero_slide_translations`). */
class HeroSlideTranslation extends Model
{
    protected $fillable = ['hero_slide_id', 'locale', 'label', 'title', 'sub_label', 'sub_title', 'button_text'];
}
