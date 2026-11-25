<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Versi bahasa lain dari sebuah highlight (tabel `highlight_translations`). */
class HighlightTranslation extends Model
{
    protected $fillable = ['highlight_id', 'locale', 'title', 'description'];
}
