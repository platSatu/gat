<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Versi bahasa lain dari sebuah artikel (tabel `article_translations`). */
class ArticleTranslation extends Model
{
    protected $fillable = ['article_id', 'locale', 'title', 'description'];
}
