<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Versi bahasa lain dari blok Tentang Kami (tabel `about_section_translations`). */
class AboutSectionTranslation extends Model
{
    protected $fillable = ['about_section_id', 'locale', 'name', 'subtitle', 'excerpt', 'description', 'button_text'];
}
